<?php
require_once(dirname(__DIR__) . '/upload/system/library/eracuni/client.php');
require_once(dirname(__DIR__) . '/upload/system/library/eracuni/synchronizer.php');
require_once(dirname(__DIR__) . '/storagedijana/vendor/autoload.php');

function assertSameValue($expected, $actual, $message) {
    if ($expected !== $actual) {
        fwrite(STDERR, "FAIL: {$message}\nExpected: " . var_export($expected, true) . "\nActual: " . var_export($actual, true) . "\n");
        exit(1);
    }
}

$reflection = new ReflectionClass('Eracuni\\Synchronizer');
$sync = $reflection->newInstanceWithoutConstructor();

$stockPayload = array(
    'response' => array(
        'result' => array(
            array('StockQuantityInfo' => array('productCode' => '001', 'quantityOnStock' => 7)),
            array('StockQuantityInfo' => array('productCode' => '007', 'quantityOnStock' => 3.8)),
            array('StockQuantityInfo' => array('productCode' => '013', 'quantityOnStock' => 0))
        )
    )
);

assertSameValue(
    array('001' => 7.0, '007' => 3.8, '013' => 0.0),
    $sync->extractStockRecords($stockPayload, array('001', '007', '013')),
    'StockQuantityInfo records are normalized by product code.'
);

assertSameValue(
    array('001' => 4.0, '007' => 0.0),
    $sync->extractStockRecords(array('data' => array('001' => 4, '007' => 0)), array('001', '007')),
    'Scalar product-code maps are supported as a safe fallback.'
);

$pricePayload = array(
    'items' => array(
        array('productCode' => '001', 'grossPrice' => 15.0, 'vatPercentage' => 25),
        array('productCode' => '007', 'grossPrice' => '16.50', 'vatPercentage' => 25)
    )
);

$prices = $sync->extractProductRecords($pricePayload, 'grossPrice');
assertSameValue('001', $prices[0]['productCode'], 'Leading zeroes are preserved in product codes.');
assertSameValue('16.50', $prices[1]['grossPrice'], 'API decimal strings are preserved until database conversion.');

$retailRecord = array(
    'productCode' => '007',
    'retailPrice' => 29.8,
    'vatPercentage' => 0,
    'PriceCalculation' => array(
        'retailPrice' => 23.84,
        'outgoingVatPercentage' => 25,
        'salesPrice' => 29.8
    )
);
assertSameValue(
    29.8,
    round($sync->resolveOpenCartPrice($retailRecord, 'retailPrice'), 4),
    'retailPrice is imported unchanged without a VAT calculation.'
);
assertSameValue(
    125.0,
    round($sync->resolveOpenCartPrice(array('grossPrice' => 125, 'vatPercentage' => 25), 'grossPrice'), 4),
    'API VAT fields cannot alter the selected catalogue price.'
);

class FakeEracuniApi extends \Agmedia\Api\Api {
    public $calls = array();

    public function __construct() {
    }

    public function post(string $endpoint, $body, string $headers_type = 'form', array $extraHeaders = array()) {
        $this->calls[] = $body;

        if ($body['method'] === 'ProductList') {
            return array(
                array(
                    'productCode' => '007',
                    'name' => 'Existing product',
                    'grossPrice' => 99,
                    'allowChangeOfPriceOnTheInvoice' => true,
                    'allowChangeOfVatRateOnTheInvoice' => true
                ),
                array(
                    'productCode' => 'DOSTAVA',
                    'name' => 'Dostava',
                    'grossPrice' => 0,
                    'allowChangeOfPriceOnTheInvoice' => true,
                    'allowChangeOfVatRateOnTheInvoice' => true
                )
            );
        }

        return array('productCode' => $body['parameters']['product']['productCode']);
    }
}

$api = new FakeEracuniApi();
$connector = new \Agmedia\Api\Connection\Csv\Eracuni(array(
    'products' => array(
        array('model' => '007', 'name' => 'Existing product'),
        array('model' => '008', 'name' => 'Missing product')
    )
));
$catalogueProduct = $connector->buildCatalogueProduct(array(
    'model' => '008',
    'name' => 'Missing product'
));
assertSameValue(
    false,
    array_key_exists('unit', $catalogueProduct),
    'New catalogue products omit the unit so e-Racuni displays JM as "-".'
);
$connector->ensureCatalogueProductsExist($api, array('username' => 'u', 'secretKey' => 's', 'token' => 't'));

assertSameValue('ProductList', $api->calls[0]['method'], 'Order submission checks the catalogue in one ProductList call.');
assertSameValue(2, count($api->calls), 'Only one missing catalogue product is imported.');
assertSameValue('008', $api->calls[1]['parameters']['product']['productCode'], 'Existing catalogue products are not overwritten by order submission.');

class FakeLockedPriceEracuniApi extends \Agmedia\Api\Api {
    public $calls = array();

    public function __construct() {
    }

    public function post(string $endpoint, $body, string $headers_type = 'form', array $extraHeaders = array()) {
        $this->calls[] = $body;

        if ($body['method'] === 'ProductList') {
            return array(
                array(
                    'productCode' => '007',
                    'name' => 'Hand wipes women',
                    'allowChangeOfPriceOnTheInvoice' => false,
                    'allowChangeOfVatRateOnTheInvoice' => false
                ),
                array(
                    'productCode' => 'DOSTAVA',
                    'name' => 'Dostava',
                    'allowChangeOfPriceOnTheInvoice' => true,
                    'allowChangeOfVatRateOnTheInvoice' => true
                )
            );
        }

        return array('status' => 'ok');
    }
}

$lockedApi = new FakeLockedPriceEracuniApi();
$lockedConnector = new \Agmedia\Api\Connection\Csv\Eracuni(array(
    'order_id' => 42,
    'date_added' => '2026-07-28 14:15:16',
    'products' => array(
        array('model' => '007', 'name' => 'Hand wipes women', 'quantity' => 1, 'price' => 23.84, 'tax' => 5.96)
    )
));
$lockedConnector->ensureCatalogueProductsExist($lockedApi, array('username' => 'u', 'secretKey' => 's', 'token' => 't'));
$lockedSale = $lockedConnector->createSale('order', 'json');

assertSameValue(2, count($lockedApi->calls), 'A locked catalogue price triggers one ProductUpdate request.');
assertSameValue('ProductUpdate', $lockedApi->calls[1]['method'], 'ProductUpdate enables line-price changes before order submission.');
assertSameValue(
    array(
        'productCode' => '007',
        'name' => 'Hand wipes women',
        'allowChangeOfPriceOnTheInvoice' => true,
        'allowChangeOfVatRateOnTheInvoice' => true
    ),
    $lockedApi->calls[1]['parameters']['product'],
    'Only the product identity and document override permissions are updated.'
);
assertSameValue(
    false,
    $lockedSale['sendIssuedInvoiceByEmail'],
    'e-Racuni buyer email delivery is disabled.'
);
assertSameValue(
    'Retail',
    $lockedSale['SalesOrder']['type'],
    'Orders without company tax data are sent as B2C Retail documents.'
);
assertSameValue(
    '29.800000',
    $lockedSale['SalesOrder']['Items'][0]['price'],
    'B2C items carry the VAT-inclusive OpenCart price.'
);
assertSameValue(
    25.0,
    $lockedSale['SalesOrder']['Items'][0]['vatPercentage'],
    'B2C items carry the documented vatPercentage API field.'
);
assertSameValue(
    false,
    array_key_exists('netPrice', $lockedSale['SalesOrder']['Items'][0]),
    'B2C items do not send the B2B-only netPrice field.'
);

$invoiceSale = $lockedConnector->createSale('invoice', 'json');
assertSameValue(true, isset($invoiceSale['SalesInvoice']), 'Invoice payload uses the documented SalesInvoice root key.');
assertSameValue(false, isset($invoiceSale['SalesOrder']), 'Invoice payload does not contain the SalesOrder root key.');
assertSameValue('2026-07-28', $invoiceSale['SalesInvoice']['date'], 'Invoice date comes from the OpenCart order.');
assertSameValue(
    '2026-07-28',
    $invoiceSale['SalesInvoice']['dateOfSupplyFrom'],
    'Invoice date of supply comes from the OpenCart order.'
);
assertSameValue(
    '2026-08-04',
    $invoiceSale['SalesInvoice']['paymentDueDate'],
    'Invoice due date is seven days after the invoice date.'
);
assertSameValue(
    false,
    array_key_exists('validUntil', $invoiceSale['SalesInvoice']),
    'Invoice payload does not send the order/quote validUntil field.'
);
assertSameValue(
    false,
    array_key_exists('expirationDate', $invoiceSale['SalesInvoice']),
    'Invoice payload uses the documented paymentDueDate field.'
);

$businessConnector = new \Agmedia\Api\Connection\Csv\Eracuni(array(
    'order_id' => 43,
    'custom_field' => json_encode(array(1 => 'Test d.o.o.', 2 => '12345678901')),
    'products' => array(
        array('model' => '007', 'name' => 'Hand wipes women', 'quantity' => 1, 'price' => 23.84, 'tax' => 5.96)
    )
));
$businessSale = $businessConnector->createSale('order', 'json');
assertSameValue('Gross', $businessSale['SalesOrder']['type'], 'Orders with company tax data are sent as B2B Gross documents.');
assertSameValue('23.840000', $businessSale['SalesOrder']['Items'][0]['netPrice'], 'B2B items carry the net OpenCart price.');
assertSameValue(25.0, $businessSale['SalesOrder']['Items'][0]['vatPercentage'], 'B2B items also carry VAT percentage.');
assertSameValue(
    false,
    strpos($businessConnector->createSale('order', 'form'), 'sendIssuedInvoiceByEmail=true') !== false,
    'Form payloads never request buyer email delivery.'
);

$redact = new ReflectionMethod('Agmedia\\Api\\Api', 'redactSecrets');
$redact->setAccessible(true);
$redacted = json_decode($redact->invoke($api, json_encode(array(
    'username' => 'private-user',
    'secretKey' => 'private-key',
    'token' => 'private-token',
    'method' => 'ProductList'
))), true);
assertSameValue('[REDACTED]', $redacted['username'], 'API usernames are redacted from request logs.');
assertSameValue('[REDACTED]', $redacted['secretKey'], 'API secret keys are redacted from request logs.');
assertSameValue('[REDACTED]', $redacted['token'], 'API tokens are redacted from request logs.');

if (!defined('DB_PREFIX')) {
    define('DB_PREFIX', 'oc_');
}

class FakeResult {
    public $rows = array();
    public $row = array();
    public $num_rows = 0;

    public function __construct(array $rows = array()) {
        $this->rows = $rows;
        $this->row = $rows ? $rows[0] : array();
        $this->num_rows = count($rows);
    }
}

class FakeSyncConfig {
    private $values;

    public function __construct(array $values) {
        $this->values = $values;
    }

    public function get($key) {
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }
}

class FakeSyncClient {
    public function call($method, array $parameters = array()) {
        if ($method === 'WarehouseGetArticleStockQuantity') {
            return array(
                array('StockQuantityInfo' => array('productCode' => '001', 'quantityOnStock' => 6)),
                array('StockQuantityInfo' => array('productCode' => '007', 'quantityOnStock' => 0))
            );
        }

        return array(
            array('productCode' => '001', 'retailPrice' => 29.8, 'vatPercentage' => 25),
            array('productCode' => '007', 'retailPrice' => 0, 'vatPercentage' => 25)
        );
    }
}

class FakeSyncLog {
    public function write($message) {
    }
}

class FakeSyncDb {
    public $products = array(
        '001' => array('product_id' => 1, 'code' => '001', 'quantity' => 10, 'price' => 15.0),
        '007' => array('product_id' => 7, 'code' => '007', 'quantity' => 5, 'price' => 15.0)
    );

    public function escape($value) {
        return addslashes($value);
    }

    public function query($sql) {
        if (strpos($sql, 'GET_LOCK') !== false) return new FakeResult(array(array('acquired' => 1)));
        if (strpos($sql, 'RELEASE_LOCK') !== false) return new FakeResult(array(array('released' => 1)));
        if (strpos($sql, 'SELECT product_id') === 0) return new FakeResult(array_values($this->products));
        if (strpos($sql, 'SELECT setting_id') === 0) return new FakeResult();

        if (preg_match("/SET quantity = '([0-9]+)'.*product_id = '([0-9]+)'/", $sql, $match)) {
            foreach ($this->products as &$product) {
                if ((int)$product['product_id'] === (int)$match[2]) $product['quantity'] = (int)$match[1];
            }
            unset($product);
        }

        if (preg_match("/SET price = '([0-9.]+)'.*product_id = '([0-9]+)'/", $sql, $match)) {
            foreach ($this->products as &$product) {
                if ((int)$product['product_id'] === (int)$match[2]) $product['price'] = (float)$match[1];
            }
            unset($product);
        }

        return new FakeResult();
    }
}

function setPrivateProperty($object, $property, $value) {
    $reflection = new ReflectionProperty('Eracuni\\Synchronizer', $property);
    $reflection->setAccessible(true);
    $reflection->setValue($object, $value);
}

$fullSync = $reflection->newInstanceWithoutConstructor();
$fakeDb = new FakeSyncDb();
setPrivateProperty($fullSync, 'db', $fakeDb);
setPrivateProperty($fullSync, 'config', new FakeSyncConfig(array(
    'module_eracuni_sync_code_field' => 'model',
    'module_eracuni_sync_warehouse_code' => '',
    'module_eracuni_sync_stock_mode' => 'available',
    'module_eracuni_sync_price_field' => 'retailPrice'
)));
setPrivateProperty($fullSync, 'client', new FakeSyncClient());
setPrivateProperty($fullSync, 'log', new FakeSyncLog());

$stockSummary = $fullSync->syncStock();
assertSameValue(2, $stockSummary['updated'], 'Stock synchronization updates changed known products.');
assertSameValue(6, $fakeDb->products['001']['quantity'], 'Stock quantity is written by product code.');
assertSameValue(0, $fakeDb->products['007']['quantity'], 'A valid zero stock value is applied.');

$priceSummary = $fullSync->syncPrices();
assertSameValue(1, $priceSummary['updated'], 'Only a positive changed price is updated.');
assertSameValue(1, $priceSummary['skipped'], 'A zero API price is safely skipped.');
assertSameValue(29.8, $fakeDb->products['001']['price'], 'retailPrice is applied unchanged.');
assertSameValue(15.0, $fakeDb->products['007']['price'], 'Zero API prices never erase live shop prices.');

echo "e-Racuni sync fixture tests passed.\n";
