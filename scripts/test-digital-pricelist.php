<?php

function digitalPricelistAssert($condition, $message) {
	if (!$condition) {
		fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
		exit(1);
	}
}

function digitalPricelistSame($expected, $actual, $message) {
	if ($expected !== $actual) {
		fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
		fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
		fwrite(STDERR, 'Actual:   ' . var_export($actual, true) . PHP_EOL);
		exit(1);
	}
}

function digitalPricelistRemoveTree($directory) {
	if (!is_dir($directory)) {
		return;
	}

	$items = scandir($directory);

	foreach ($items as $item) {
		if ($item === '.' || $item === '..') {
			continue;
		}

		$path = $directory . '/' . $item;

		if (is_dir($path)) {
			digitalPricelistRemoveTree($path);
		} else {
			unlink($path);
		}
	}

	rmdir($directory);
}

class DigitalPricelistTestResult {
	public $row = array();
	public $rows = array();
	public $num_rows = 0;

	public function __construct(array $rows) {
		$this->rows = $rows;
		$this->row = $rows ? $rows[0] : array();
		$this->num_rows = count($rows);
	}
}

class DigitalPricelistTestDb {
	public $has_anchor_columns = true;
	public $last_product_query = '';
	public $products = array();

	public function escape($value) {
		return addslashes((string)$value);
	}

	public function query($sql) {
		if (strpos($sql, 'SHOW COLUMNS') === 0) {
			return new DigitalPricelistTestResult($this->has_anchor_columns ? array(array('Field' => 'present')) : array());
		}

		if (strpos($sql, 'FROM `oc_language`') !== false) {
			return new DigitalPricelistTestResult(array(array('language_id' => 2)));
		}

		if (strpos($sql, 'FROM `oc_setting`') !== false) {
			return new DigitalPricelistTestResult(array(array('value' => 1)));
		}

		if (strpos($sql, 'FROM `oc_product` p') !== false) {
			$this->last_product_query = $sql;
			return new DigitalPricelistTestResult($this->products);
		}

		throw new RuntimeException('Unexpected SQL in test: ' . $sql);
	}
}

class DigitalPricelistTestConfig {
	private $data;

	public function __construct(array $data) {
		$this->data = $data;
	}

	public function get($key) {
		return array_key_exists($key, $this->data) ? $this->data[$key] : null;
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}
}

class DigitalPricelistTestRegistry {
	private $data;

	public function __construct(array $data) {
		$this->data = $data;
	}

	public function get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	public function has($key) {
		return isset($this->data[$key]);
	}
}

class DigitalPricelistTestTax {
	public function calculate($value, $tax_class_id, $calculate = true) {
		return $calculate && $tax_class_id ? (float)$value * 1.25 : (float)$value;
	}
}

class DigitalPricelistTestLog {
	public function write($message) {
	}
}

$test_root = sys_get_temp_dir() . '/dryzen-digital-pricelist-' . bin2hex(random_bytes(6));
mkdir($test_root, 0750, true);
define('DIR_STORAGE', $test_root . '/');
define('DB_PREFIX', 'oc_');

require_once dirname(__DIR__) . '/upload/system/library/digital_pricelist/generator.php';
digitalPricelistAssert(class_exists('digital_pricelist\\generator'), 'The library namespace matches OpenCart Loader route resolution.');

$db = new DigitalPricelistTestDb();
$db->products = array(
	array(
		'product_id' => 11,
		'model' => '001',
		'sku' => 'DRY-001',
		'upc' => '',
		'ean' => '3851234567890',
		'jan' => '',
		'isbn' => '',
		'mpn' => '',
		'quantity' => 7,
		'price' => '10.0000',
		'tax_class_id' => 9,
		'anchor_price' => '12.0000',
		'anchor_price_date' => '2026-09-01',
		'name' => 'DryZen &amp; Calm <Sensitive>',
		'brand' => '',
		'discount' => '9.0000',
		'special' => '8.0000'
	),
	array(
		'product_id' => 12,
		'model' => '002',
		'sku' => '',
		'upc' => '012345678905',
		'ean' => '',
		'jan' => '',
		'isbn' => '',
		'mpn' => '',
		'quantity' => 0,
		'price' => '4.0000',
		'tax_class_id' => 9,
		'anchor_price' => '6.0000',
		'anchor_price_date' => '0000-00-00',
		'name' => '=HYPERLINK("https://example.test")',
		'brand' => 'DryZen',
		'discount' => null,
		'special' => null
	)
);

$config = new DigitalPricelistTestConfig(array(
	'config_store_id' => 0,
	'config_language' => 'hr-hr',
	'config_language_id' => 2,
	'config_customer_group_id' => 9,
	'config_currency' => 'EUR',
	'config_name' => 'DryZen',
	'config_address' => 'Bila 8, 10000 Zagreb',
	'config_ssl' => 'https://dryzen.test/',
	'config_url' => 'http://dryzen.test/',
	'feed_digital_pricelist_archive_days' => 30,
	'feed_digital_pricelist_object_type' => 'webshop',
	'feed_digital_pricelist_object_address' => 'Bila 8, 10000 Zagreb',
	'feed_digital_pricelist_object_designation' => 'WEB-01',
	'feed_digital_pricelist_unit' => 'kom'
));

$registry = new DigitalPricelistTestRegistry(array(
	'db' => $db,
	'config' => $config,
	'log' => new DigitalPricelistTestLog(),
	'digital_pricelist_tax' => new DigitalPricelistTestTax()
));

$generator = new \Digital_pricelist\Generator($registry);

try {
	$first = $generator->generate(true);
	digitalPricelistAssert(!empty($first['generated']), 'The first run creates a snapshot.');
	digitalPricelistSame(2, $first['product_count'], 'Both active products are exported.');
	digitalPricelistSame('EUR', $first['currency'], 'The base shop currency is exported.');
	digitalPricelistAssert(preg_match('/^[a-f0-9]{64}$/', $first['xml_sha256']) === 1, 'The manifest contains an XML checksum.');

	$xml_file = $generator->getCurrentFile('xml');
	$csv_file = $generator->getCurrentFile('csv');
	digitalPricelistAssert(is_file($xml_file['path']) && is_file($csv_file['path']), 'Both public formats point to complete immutable files.');

	$xml = simplexml_load_file($xml_file['path']);
	digitalPricelistAssert($xml !== false, 'Generated XML is well formed.');
	digitalPricelistSame('2', (string)$xml->products['count'], 'XML includes a product count.');
	digitalPricelistSame('DryZen & Calm <Sensitive>', (string)$xml->products->product[0]->name, 'XMLWriter safely round-trips special product-name characters.');
	digitalPricelistSame('10.00', (string)$xml->products->product[0]->current_price, 'The active special price includes 25% VAT.');
	digitalPricelistSame('001', (string)$xml->products->product[0]->code, 'The product code is exported.');
	digitalPricelistSame('DryZen', (string)$xml->products->product[0]->brand, 'The brand is exported.');
	digitalPricelistSame('kom', (string)$xml->products->product[0]->unit, 'The unit of measure is exported.');
	digitalPricelistSame('10.00', (string)$xml->products->product[0]->unit_price, 'The unit price is exported.');
	digitalPricelistSame('10.00', (string)$xml->products->product[0]->retail_price, 'The retail price is exported.');
	digitalPricelistSame('da', (string)$xml->products->product[0]->promotion_applied, 'The promotion flag is exported.');
	digitalPricelistSame('Akcija', (string)$xml->products->product[0]->promotion_name, 'The promotion name is exported.');
	digitalPricelistSame('15.00', (string)$xml->products->product[0]->anchor_price, 'The anchor price includes the same VAT calculation.');
	digitalPricelistSame('2026-09-01', (string)$xml->products->product[0]->anchor_price_date, 'The reference date is exported.');
	digitalPricelistSame('3851234567890', (string)$xml->products->product[0]->barcode, 'EAN is preferred as the barcode.');
	digitalPricelistSame('nedostupno', (string)$xml->products->product[1]->availability, 'Zero quantity is marked unavailable.');
	digitalPricelistSame('DryZen', (string)$xml->products->product[1]->brand, 'The store name is used when no manufacturer is assigned.');
	digitalPricelistSame('', (string)$xml->products->product[1]->anchor_price, 'An invalid anchor date suppresses the anchor price.');

	$csv_handle = fopen($csv_file['path'], 'rb');
	$header = fgetcsv($csv_handle, 0, ',', '"', '');
	$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
	$csv_product = array_combine($header, fgetcsv($csv_handle, 0, ',', '"', ''));
	$csv_product_two = array_combine($header, fgetcsv($csv_handle, 0, ',', '"', ''));
	fclose($csv_handle);
	digitalPricelistSame('10.00', $csv_product['current_price'], 'CSV and XML contain the same current price.');
	digitalPricelistSame('da', $csv_product['promotion_applied'], 'CSV contains the promotion indicator.');
	digitalPricelistSame("'=HYPERLINK(\"https://example.test\")", $csv_product_two['name'], 'Spreadsheet formulas are neutralized in CSV text fields.');
	digitalPricelistSame('https://dryzen.test/index.php?route=product/product&product_id=11', $csv_product['product_url'], 'Product URLs use the secure catalog origin.');

	digitalPricelistAssert(preg_match('/^cjenik_webshop_bila-8-10000-zagreb_web-01_000001_\d{8}_\d{6}\.xml$/', $xml_file['name']) === 1, 'The public filename contains object type, address, designation, storage sequence and timestamp.');
	$snapshots_before = glob($test_root . '/digital_pricelist/archive/cjenik_*.*');
	$daily = $generator->ensureDaily();
	$snapshots_after = glob($test_root . '/digital_pricelist/archive/cjenik_*.*');
	digitalPricelistAssert(empty($daily['generated']), 'A second daily call reuses the current snapshot.');
	digitalPricelistSame(count($snapshots_before), count($snapshots_after), 'Daily reuse does not create duplicate archive files.');

	$second = $generator->generate(true);
	digitalPricelistAssert(!empty($second['generated']), 'Manual generation creates a new snapshot.');
	digitalPricelistSame(4, count(glob($test_root . '/digital_pricelist/archive/cjenik_*.*')), 'The previous XML and CSV remain archived.');
	digitalPricelistSame(2, count($generator->getArchive()), 'Both retained snapshot pairs are listed in the public archive.');
	$archived = $generator->getArchivedFile($xml_file['name']);
	digitalPricelistSame($xml_file['name'], $archived['name'], 'A retained snapshot can be resolved through the strict public archive API.');
	$traversal_blocked = false;

	try {
		$generator->getArchivedFile('../config.php');
	} catch (InvalidArgumentException $exception) {
		$traversal_blocked = true;
	}

	digitalPricelistAssert($traversal_blocked, 'Archive path traversal is rejected.');

	$old_base = $test_root . '/digital_pricelist/archive/digital-pricelist_20200101_010101_abcdef123456';
	file_put_contents($old_base . '.xml', '<old/>');
	file_put_contents($old_base . '.csv', 'old');
	touch($old_base . '.xml', time() - (31 * 86400));
	touch($old_base . '.csv', time() - (31 * 86400));
	$generator->generate(true);
	digitalPricelistAssert(!is_file($old_base . '.xml') && !is_file($old_base . '.csv'), 'Snapshots older than the configured 30-day minimum are pruned.');
	digitalPricelistAssert(is_file($generator->getCurrentFile('xml')['path']), 'Cleanup never removes the active snapshot.');

	$db->has_anchor_columns = false;
	$generator->generate(true);
	digitalPricelistAssert(strpos($db->last_product_query, '0.0000 AS anchor_price') !== false, 'The feed remains deployable before the anchor-price migration is applied.');

	$manifest_path = $test_root . '/digital_pricelist/current.json';
	$valid_manifest = file_get_contents($manifest_path);
	file_put_contents($manifest_path, json_encode(array('xml_file' => '../config.php', 'csv_file' => '../config.php')));
	$blocked = false;

	try {
		$generator->getCurrentFile('xml');
	} catch (RuntimeException $exception) {
		$blocked = true;
	}

	digitalPricelistAssert($blocked, 'Manifest filenames are validated before a private path is resolved.');
	file_put_contents($manifest_path, $valid_manifest);

	echo "Digital pricelist tests passed." . PHP_EOL;
} finally {
	digitalPricelistRemoveTree($test_root);
}
