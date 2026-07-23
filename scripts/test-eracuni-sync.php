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

class FakeEracuniApi extends \Agmedia\Api\Api {
    public $calls = array();

    public function __construct() {
    }

    public function post(string $endpoint, $body, string $headers_type = 'form', array $extraHeaders = array()) {
        $this->calls[] = $body;

        if ($body['method'] === 'ProductList') {
            return array(
                array('productCode' => '007', 'grossPrice' => 99),
                array('productCode' => 'DOSTAVA', 'grossPrice' => 0)
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
$connector->ensureCatalogueProductsExist($api, array('username' => 'u', 'secretKey' => 's', 'token' => 't'));

assertSameValue('ProductList', $api->calls[0]['method'], 'Order submission checks the catalogue in one ProductList call.');
assertSameValue(2, count($api->calls), 'Only one missing catalogue product is imported.');
assertSameValue('008', $api->calls[1]['parameters']['product']['productCode'], 'Existing catalogue products are not overwritten by order submission.');

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
            array('productCode' => '001', 'grossPrice' => 20, 'vatPercentage' => 25),
            array('productCode' => '007', 'grossPrice' => 0, 'vatPercentage' => 25)
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
    'module_eracuni_sync_price_field' => 'grossPrice',
    'module_eracuni_sync_price_includes_tax' => 0
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
assertSameValue(20.0, $fakeDb->products['001']['price'], 'Positive API prices are applied.');
assertSameValue(15.0, $fakeDb->products['007']['price'], 'Zero API prices never erase live shop prices.');

echo "e-Racuni sync fixture tests passed.\n";
