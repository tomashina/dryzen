<?php
if (!defined('DB_PREFIX')) {
    define('DB_PREFIX', 'oc_');
}

if (!defined('OC_ENV')) {
    define('OC_ENV', array('test' => true));
}

function agconf($key) {
    $values = array(
        'import.api.username' => 'test-user',
        'import.api.password' => 'test-secret',
        'import.api.token' => 'test-token',
    );

    return isset($values[$key]) ? $values[$key] : null;
}

class Log {
    public $messages = array();

    public function __construct($file) {
    }

    public function write($message) {
        $this->messages[] = $message;
    }
}

class FakeInvoiceExportResult {
    public $rows = array();
    public $row = array();
    public $num_rows = 0;

    public function __construct(array $rows = array()) {
        $this->rows = $rows;
        $this->row = $rows ? $rows[0] : array();
        $this->num_rows = count($rows);
    }
}

class FakeInvoiceExportDb {
    public $order = array(
        'order_id' => 42,
        'order_status_id' => 1,
        'number_order' => '',
    );

    public function escape($value) {
        return addslashes($value);
    }

    public function query($sql) {
        if (strpos($sql, 'GET_LOCK') !== false) {
            return new FakeInvoiceExportResult(array(array('acquired' => 1)));
        }

        if (strpos($sql, 'RELEASE_LOCK') !== false) {
            return new FakeInvoiceExportResult(array(array('released' => 1)));
        }

        if (strpos($sql, 'order_product') !== false) {
            return new FakeInvoiceExportResult(array(
                array('model' => '007', 'quantity' => 1, 'price' => 23.84, 'tax' => 5.96),
            ));
        }

        if (strpos($sql, 'order_total') !== false) {
            return new FakeInvoiceExportResult(array(
                array('code' => 'total', 'value' => 29.80),
            ));
        }

        if (strpos($sql, 'SELECT * FROM `' . DB_PREFIX . 'order`') !== false) {
            return new FakeInvoiceExportResult(array($this->order));
        }

        if (preg_match("/SET number_order = '([^']+)'/", $sql, $matches)) {
            $this->order['number_order'] = stripslashes($matches[1]);
        }

        return new FakeInvoiceExportResult();
    }
}

class FakeInvoiceExportRegistry {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function get($key) {
        return $key === 'db' ? $this->db : null;
    }
}

class FakeInvoiceExportApi {
    public $calls = array();

    public function post($endpoint, $body, $format) {
        $this->calls[] = array(
            'endpoint' => $endpoint,
            'body' => $body,
            'format' => $format,
        );

        return array('number' => 'IR-2026-42');
    }
}

class FakeInvoiceExportConnector {
    public $saleType;
    public $catalogueChecked = false;

    public function ensureCatalogueProductsExist($api, array $auth) {
        $this->catalogueChecked = true;
    }

    public function createSale($type, $mode) {
        $this->saleType = $type;

        return array(
            'SalesInvoice' => array(
                'dateOfSupplyFrom' => '2026-07-28',
                'Items' => array(),
            ),
        );
    }
}

require_once(dirname(__DIR__) . '/upload/system/library/eracuni/order_exporter.php');

class TestableInvoiceExporter extends \Eracuni\OrderExporter {
    private $api;
    private $connector;

    public function __construct($registry, $api, $connector) {
        $this->api = $api;
        $this->connector = $connector;
        parent::__construct($registry);
    }

    protected function createApi() {
        return $this->api;
    }

    protected function createConnector(array $order) {
        return $this->connector;
    }
}

function assertInvoiceExportSame($expected, $actual, $message) {
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            "FAIL: {$message}\nExpected: " . var_export($expected, true) .
            "\nActual: " . var_export($actual, true) . "\n"
        );
        exit(1);
    }
}

$db = new FakeInvoiceExportDb();
$api = new FakeInvoiceExportApi();
$connector = new FakeInvoiceExportConnector();
$exporter = new TestableInvoiceExporter(new FakeInvoiceExportRegistry($db), $api, $connector);

$result = $exporter->export(42);
$body = $api->calls[0]['body'];

assertInvoiceExportSame(true, $connector->catalogueChecked, 'Catalogue products are checked before invoice creation.');
assertInvoiceExportSame('invoice', $connector->saleType, 'Success export requests an invoice payload.');
assertInvoiceExportSame('SalesInvoiceCreate', $body['method'], 'Success export calls SalesInvoiceCreate.');
assertInvoiceExportSame(
    'dryzen-invoice-42',
    $body['parameters']['apiTransactionId'],
    'Invoice creation uses a stable transaction ID.'
);
assertInvoiceExportSame('IR-2026-42', $db->order['number_order'], 'The returned invoice number is stored on the order.');
assertInvoiceExportSame(false, $result['skipped'], 'The first invoice export is not skipped.');

$secondResult = $exporter->export(42);
assertInvoiceExportSame(true, $secondResult['skipped'], 'A repeated success callback does not create another invoice.');
assertInvoiceExportSame(1, count($api->calls), 'The API is called only once for an already exported order.');

echo "e-Racuni invoice export fixture tests passed.\n";
