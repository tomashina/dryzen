<?php
namespace Digital_pricelist;

/**
 * Generates immutable XML and CSV snapshots of the public product price list.
 *
 * Snapshots live below DIR_STORAGE (outside the public web root). A small
 * manifest is replaced only after both formats have been written successfully,
 * so public readers never see a partially generated pair of files.
 */
class Generator {
	const FORMAT_VERSION = '1.1';

	private $registry;
	private $db;
	private $config;
	private $log;
	private $directory;
	private $archive_directory;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
		$this->log = $registry->get('log');
		$this->directory = rtrim(DIR_STORAGE, '/\\') . '/digital_pricelist';
		$this->archive_directory = $this->directory . '/archive';
	}

	/**
	 * Generate at most once per local calendar day.
	 */
	public function ensureDaily() {
		return $this->generate(false);
	}

	/**
	 * @param bool $force Create a new immutable snapshot even if today's exists.
	 * @return array Generation summary / active manifest.
	 */
	public function generate($force = false) {
		$this->ensureDirectories();

		$lock_path = $this->directory . '/generation.lock';
		$lock = fopen($lock_path, 'c');

		if (!$lock) {
			throw new \RuntimeException('Nije moguće otvoriti zaključavanje digitalnog cjenika.');
		}

		try {
			if (!flock($lock, LOCK_EX)) {
				throw new \RuntimeException('Nije moguće zaključati generiranje digitalnog cjenika.');
			}

			$manifest = $this->readManifest();

			if (!$force && $this->isFreshToday($manifest)) {
				$manifest['generated'] = false;
				return $manifest;
			}

			$generated_at = date('c');
			$products = $this->getProducts($generated_at);
			$sequence = $this->nextStorageSequence();
			$base_name = $this->newSnapshotBaseName($sequence);
			$xml_name = $base_name . '.xml';
			$csv_name = $base_name . '.csv';
			$xml_path = $this->archive_directory . '/' . $xml_name;
			$csv_path = $this->archive_directory . '/' . $csv_name;
			$xml_temp = tempnam($this->archive_directory, '.xml-build-');
			$csv_temp = tempnam($this->archive_directory, '.csv-build-');

			if ($xml_temp === false || $csv_temp === false) {
				$this->removeFile($xml_temp);
				$this->removeFile($csv_temp);
				throw new \RuntimeException('Nije moguće pripremiti datoteke digitalnog cjenika.');
			}

			try {
				$this->writeXml($xml_temp, $products, $generated_at);
				$this->writeCsv($csv_temp, $products, $generated_at);

				if (!rename($xml_temp, $xml_path)) {
					throw new \RuntimeException('Nije moguće spremiti XML digitalnog cjenika.');
				}

				$xml_temp = null;

				if (!rename($csv_temp, $csv_path)) {
					$this->removeFile($xml_path);
					throw new \RuntimeException('Nije moguće spremiti CSV digitalnog cjenika.');
				}

				$csv_temp = null;
				@chmod($xml_path, 0640);
				@chmod($csv_path, 0640);
			} catch (\Throwable $exception) {
				$this->removeFile($xml_temp);
				$this->removeFile($csv_temp);
				throw $exception;
			}

			$manifest = array(
				'format_version' => self::FORMAT_VERSION,
				'generated_at'   => $generated_at,
				'generated_date' => date('Y-m-d'),
				'product_count'  => count($products),
				'currency'       => (string)$this->config->get('config_currency'),
				'storage_sequence' => $sequence,
				'xml_file'       => $xml_name,
				'csv_file'       => $csv_name,
				'xml_sha256'     => hash_file('sha256', $xml_path),
				'csv_sha256'     => hash_file('sha256', $csv_path)
			);

			$this->writeManifest($manifest);
			$this->removeExpiredSnapshots($manifest);
			$manifest['generated'] = true;

			return $manifest;
		} finally {
			flock($lock, LOCK_UN);
			fclose($lock);
		}
	}

	/**
	 * Return public metadata without exposing the private storage path.
	 */
	public function getStatus() {
		$manifest = $this->readManifest();

		if (!$manifest || !$this->manifestFilesExist($manifest)) {
			return array(
				'available'     => false,
				'generated_at'  => '',
				'product_count' => 0,
				'currency'      => (string)$this->config->get('config_currency')
			);
		}

		return array(
			'available'      => true,
			'generated_at'   => isset($manifest['generated_at']) ? $manifest['generated_at'] : '',
			'product_count'  => isset($manifest['product_count']) ? (int)$manifest['product_count'] : 0,
			'currency'       => isset($manifest['currency']) ? $manifest['currency'] : (string)$this->config->get('config_currency'),
			'xml_sha256'     => isset($manifest['xml_sha256']) ? $manifest['xml_sha256'] : '',
			'csv_sha256'     => isset($manifest['csv_sha256']) ? $manifest['csv_sha256'] : ''
		);
	}

	/**
	 * Resolve only the active manifest entry; request input never reaches a path.
	 */
	public function getCurrentFile($format) {
		$format = strtolower((string)$format);

		if ($format !== 'xml' && $format !== 'csv') {
			throw new \InvalidArgumentException('Nepoznat format digitalnog cjenika.');
		}

		$manifest = $this->readManifest();
		$key = $format . '_file';

		if (!$manifest || empty($manifest[$key]) || !$this->isSnapshotFileName($manifest[$key], $format)) {
			throw new \RuntimeException('Digitalni cjenik još nije generiran.');
		}

		$path = $this->archive_directory . '/' . $manifest[$key];

		if (!is_file($path) || !is_readable($path)) {
			throw new \RuntimeException('Aktualna datoteka digitalnog cjenika nije dostupna.');
		}

		return array(
			'path'          => $path,
			'name'          => $manifest[$key],
			'generated_at'  => isset($manifest['generated_at']) ? $manifest['generated_at'] : '',
			'product_count' => isset($manifest['product_count']) ? (int)$manifest['product_count'] : 0,
			'sha256'        => isset($manifest[$format . '_sha256']) ? $manifest[$format . '_sha256'] : hash_file('sha256', $path)
		);
	}

	/**
	 * List retained immutable snapshots without exposing their private paths.
	 */
	public function getArchive() {
		$this->ensureDirectories();
		$items = array();

		foreach (glob($this->archive_directory . '/*.{xml,csv}', GLOB_BRACE) ?: array() as $path) {
			$name = basename($path);
			$format = strtolower((string)pathinfo($name, PATHINFO_EXTENSION));

			if (!$this->isSnapshotFileName($name, $format) || !is_file($path) || !is_readable($path)) {
				continue;
			}

			$stem = substr($name, 0, -(strlen($format) + 1));

			if (!isset($items[$stem])) {
				$items[$stem] = array(
					'generated_at' => date('c', (int)filemtime($path)),
					'xml_file' => '',
					'csv_file' => ''
				);
			}

			$items[$stem][$format . '_file'] = $name;
		}

		usort($items, function ($left, $right) {
			return strcmp($right['generated_at'], $left['generated_at']);
		});

		return array_values($items);
	}

	/**
	 * Resolve a retained public snapshot using a strict allow-list filename.
	 */
	public function getArchivedFile($name) {
		$name = (string)$name;
		$format = strtolower((string)pathinfo($name, PATHINFO_EXTENSION));

		if (basename($name) !== $name || !$this->isSnapshotFileName($name, $format)) {
			throw new \InvalidArgumentException('Neispravan naziv arhivskog cjenika.');
		}

		$path = $this->archive_directory . '/' . $name;

		if (!is_file($path) || !is_readable($path)) {
			throw new \RuntimeException('Arhivski cjenik nije dostupan.');
		}

		return array(
			'path' => $path,
			'name' => $name,
			'format' => $format,
			'generated_at' => date('c', (int)filemtime($path)),
			'sha256' => hash_file('sha256', $path)
		);
	}

	private function getProducts($generated_at) {
		$store_id = (int)$this->config->get('config_store_id');
		$language_id = $this->getStoreLanguageId();
		$customer_group_id = $this->getStoreCustomerGroupId();
		$has_anchor_price = $this->hasProductColumn('anchor_price');
		$has_anchor_date = $this->hasProductColumn('anchor_price_date');
		$anchor_price_sql = $has_anchor_price ? 'p.anchor_price' : '0.0000';
		$anchor_date_sql = $has_anchor_date ? 'p.anchor_price_date' : 'NULL';

		$sql = "SELECT p.product_id, p.model, p.sku, p.upc, p.ean, p.jan, p.isbn, p.mpn, p.quantity, p.price, p.tax_class_id, "
			. $anchor_price_sql . " AS anchor_price, " . $anchor_date_sql . " AS anchor_price_date, pd.name, COALESCE(m.name, '') AS brand, "
			. "(SELECT pd2.price FROM `" . DB_PREFIX . "product_discount` pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . $customer_group_id . "' AND pd2.quantity = '1' AND ((CAST(pd2.date_start AS CHAR) = '0000-00-00') OR pd2.date_start <= NOW()) AND ((CAST(pd2.date_end AS CHAR) = '0000-00-00') OR pd2.date_end >= NOW()) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, "
			. "(SELECT ps.price FROM `" . DB_PREFIX . "product_special` ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . $customer_group_id . "' AND ((CAST(ps.date_start AS CHAR) = '0000-00-00') OR ps.date_start <= NOW()) AND ((CAST(ps.date_end AS CHAR) = '0000-00-00') OR ps.date_end >= NOW()) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special "
			. "FROM `" . DB_PREFIX . "product` p "
			. "INNER JOIN `" . DB_PREFIX . "product_description` pd ON (pd.product_id = p.product_id AND pd.language_id = '" . $language_id . "') "
			. "INNER JOIN `" . DB_PREFIX . "product_to_store` p2s ON (p2s.product_id = p.product_id AND p2s.store_id = '" . $store_id . "') "
			. "LEFT JOIN `" . DB_PREFIX . "manufacturer` m ON (m.manufacturer_id = p.manufacturer_id) "
			. "WHERE p.status = '1' AND p.date_available <= NOW() ORDER BY pd.name ASC, p.product_id ASC";

		$query = $this->db->query($sql);
		$tax = $this->createTaxCalculator($customer_group_id);
		$currency = (string)$this->config->get('config_currency');
		$products = array();

		foreach ($query->rows as $row) {
			$base_price = (isset($row['discount']) && (float)$row['discount'] > 0) ? (float)$row['discount'] : (float)$row['price'];
			$has_special = isset($row['special']) && (float)$row['special'] > 0;
			$has_discount = !$has_special && isset($row['discount']) && (float)$row['discount'] > 0;
			$current_price = $has_special ? (float)$row['special'] : $base_price;
			$current_price = $tax->calculate($current_price, (int)$row['tax_class_id'], true);
			$anchor_price = '';
			$anchor_date = '';

			if ((float)$row['anchor_price'] > 0 && $this->isValidDate(isset($row['anchor_price_date']) ? $row['anchor_price_date'] : '')) {
				$anchor_price = $this->formatPrice($tax->calculate((float)$row['anchor_price'], (int)$row['tax_class_id'], true));
				$anchor_date = (string)$row['anchor_price_date'];
			}

			$quantity = (int)$row['quantity'];

			$code = trim((string)$row['model']) !== '' ? (string)$row['model'] : (trim((string)$row['sku']) !== '' ? (string)$row['sku'] : (string)$row['product_id']);
			$brand = isset($row['brand']) && trim((string)$row['brand']) !== '' ? (string)$row['brand'] : (string)$this->config->get('config_name');
			$unit = trim((string)$this->config->get('feed_digital_pricelist_unit'));

			if ($unit === '') {
				$unit = 'kom';
			}

			$products[] = array(
				'product_id'        => (int)$row['product_id'],
				'code'              => $this->cleanText($code),
				'model'             => $this->cleanText($row['model']),
				'sku'               => $this->cleanText($row['sku']),
				'name'              => $this->cleanText($row['name']),
				'brand'             => $this->cleanText($brand),
				'unit'              => $this->cleanText($unit),
				'barcode'           => $this->cleanText($this->selectBarcode($row)),
				'current_price'     => $this->formatPrice($current_price),
				'retail_price'      => $this->formatPrice($current_price),
				'unit_price'        => $this->formatPrice($current_price),
				'promotion_applied' => ($has_special || $has_discount) ? 'da' : 'ne',
				'promotion_name'    => $has_special ? 'Akcija' : ($has_discount ? 'Popust' : ''),
				'anchor_price'      => $anchor_price,
				'anchor_price_date' => $anchor_date,
				'currency'          => $currency,
				'quantity'          => $quantity,
				'available'         => $quantity > 0 ? '1' : '0',
				'availability'      => $quantity > 0 ? 'dostupno' : 'nedostupno',
				'product_url'       => $this->productUrl((int)$row['product_id']),
				'generated_at'      => $generated_at
			);
		}

		return $products;
	}

	private function writeXml($path, array $products, $generated_at) {
		$writer = new \XMLWriter();

		if (!$writer->openURI($path)) {
			throw new \RuntimeException('Nije moguće otvoriti XML digitalnog cjenika.');
		}

		$writer->setIndent(true);
		$writer->setIndentString('  ');
		$writer->startDocument('1.0', 'UTF-8');
		$writer->startElement('digital_price_list');
		$writer->writeAttribute('version', self::FORMAT_VERSION);
		$writer->writeElement('store', $this->cleanText((string)$this->config->get('config_name')));
		$writer->writeElement('generated_at', $generated_at);
		$writer->writeElement('currency', (string)$this->config->get('config_currency'));
		$writer->startElement('products');
		$writer->writeAttribute('count', (string)count($products));

		foreach ($products as $product) {
			$writer->startElement('product');

			foreach ($product as $key => $value) {
				if ($key !== 'generated_at') {
					$writer->writeElement($key, (string)$value);
				}
			}

			$writer->endElement();
		}

		$writer->endElement();
		$writer->endElement();
		$writer->endDocument();

		if ($writer->flush() === false) {
			throw new \RuntimeException('Nije moguće zapisati XML digitalnog cjenika.');
		}
	}

	private function writeCsv($path, array $products, $generated_at) {
		$handle = fopen($path, 'wb');

		if (!$handle) {
			throw new \RuntimeException('Nije moguće otvoriti CSV digitalnog cjenika.');
		}

		try {
			// UTF-8 BOM keeps Croatian characters intact in common spreadsheet apps.
			fwrite($handle, "\xEF\xBB\xBF");
				$columns = array(
					'product_id', 'code', 'model', 'sku', 'name', 'brand', 'unit', 'barcode',
					'current_price', 'retail_price', 'unit_price', 'promotion_applied', 'promotion_name',
					'anchor_price', 'anchor_price_date', 'currency', 'quantity',
				'available', 'availability', 'product_url', 'generated_at'
			);
			$this->writeCsvRow($handle, $columns);

			foreach ($products as $product) {
				$row = array();

				foreach ($columns as $column) {
					$row[] = isset($product[$column]) ? $product[$column] : ($column === 'generated_at' ? $generated_at : '');
				}

				$this->writeCsvRow($handle, $row);
			}
		} finally {
			fclose($handle);
		}
	}

	private function writeCsvRow($handle, array $row) {
		foreach ($row as &$value) {
			if (is_string($value) && preg_match('/^[=+\-@\x09\x0D]/', $value)) {
				$value = "'" . $value;
			}
		}
		unset($value);

		if (fputcsv($handle, $row, ',', '"', '') === false) {
			throw new \RuntimeException('Nije moguće zapisati CSV digitalnog cjenika.');
		}
	}

	private function createTaxCalculator($customer_group_id) {
		if ($this->registry->has('digital_pricelist_tax')) {
			return $this->registry->get('digital_pricelist_tax');
		}

		$original_customer_group_id = $this->config->get('config_customer_group_id');
		$this->config->set('config_customer_group_id', (int)$customer_group_id);

		try {
			$tax = new \Cart\Tax($this->registry);

			if ($this->config->get('config_tax_default') === 'shipping') {
				$tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			}

			if ($this->config->get('config_tax_default') === 'payment') {
				$tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			}

			$tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			return $tax;
		} finally {
			$this->config->set('config_customer_group_id', $original_customer_group_id);
		}
	}

	private function getStoreLanguageId() {
		$language_code = (string)$this->config->get('config_language');
		$query = $this->db->query("SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = '" . $this->db->escape($language_code) . "' AND status = '1' LIMIT 1");

		if ($query->num_rows) {
			return (int)$query->row['language_id'];
		}

		return (int)$this->config->get('config_language_id');
	}

	private function getStoreCustomerGroupId() {
		$store_id = (int)$this->config->get('config_store_id');
		$query = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_customer_group_id' AND (`store_id` = '0' OR `store_id` = '" . $store_id . "') ORDER BY `store_id` DESC LIMIT 1");

		if ($query->num_rows) {
			return (int)$query->row['value'];
		}

		return (int)$this->config->get('config_customer_group_id');
	}

	private function hasProductColumn($column) {
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product` LIKE '" . $this->db->escape($column) . "'");
		return (bool)$query->num_rows;
	}

	private function selectBarcode(array $row) {
		foreach (array('ean', 'upc', 'jan', 'isbn') as $field) {
			if (!empty($row[$field])) {
				return (string)$row[$field];
			}
		}

		return '';
	}

	private function productUrl($product_id) {
		$base = (string)($this->config->get('config_ssl') ?: $this->config->get('config_url'));
		return rtrim($base, '/') . '/index.php?route=product/product&product_id=' . (int)$product_id;
	}

	private function formatPrice($price) {
		return number_format(round((float)$price + 0.0000001, 2), 2, '.', '');
	}

	private function isValidDate($date) {
		$date = (string)$date;

		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
			return false;
		}

		$parsed = \DateTime::createFromFormat('!Y-m-d', $date);
		return $parsed && $parsed->format('Y-m-d') === $date;
	}

	private function cleanText($value) {
		$value = html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');

		if (function_exists('mb_check_encoding') && !mb_check_encoding($value, 'UTF-8')) {
			$value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
		}

		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
	}

	private function ensureDirectories() {
		foreach (array($this->directory, $this->archive_directory) as $directory) {
			if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
				throw new \RuntimeException('Nije moguće izraditi direktorij digitalnog cjenika.');
			}
		}
	}

	private function manifestPath() {
		return $this->directory . '/current.json';
	}

	private function readManifest() {
		$path = $this->manifestPath();

		if (!is_file($path) || !is_readable($path)) {
			return array();
		}

		$data = json_decode((string)file_get_contents($path), true);
		return is_array($data) ? $data : array();
	}

	private function writeManifest(array $manifest) {
		$json = json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

		if ($json === false) {
			throw new \RuntimeException('Nije moguće pripremiti manifest digitalnog cjenika.');
		}

		$temp = tempnam($this->directory, '.manifest-build-');

		if ($temp === false || file_put_contents($temp, $json . "\n", LOCK_EX) === false) {
			$this->removeFile($temp);
			throw new \RuntimeException('Nije moguće zapisati manifest digitalnog cjenika.');
		}

		@chmod($temp, 0640);

		if (!rename($temp, $this->manifestPath())) {
			$this->removeFile($temp);
			throw new \RuntimeException('Nije moguće aktivirati digitalni cjenik.');
		}
	}

	private function isFreshToday(array $manifest) {
		return isset($manifest['format_version']) && $manifest['format_version'] === self::FORMAT_VERSION
			&& !empty($manifest['generated_date'])
			&& $manifest['generated_date'] === date('Y-m-d')
			&& $this->manifestFilesExist($manifest);
	}

	private function manifestFilesExist(array $manifest) {
		foreach (array('xml', 'csv') as $format) {
			$key = $format . '_file';

			if (empty($manifest[$key]) || !$this->isSnapshotFileName($manifest[$key], $format) || !is_file($this->archive_directory . '/' . $manifest[$key])) {
				return false;
			}
		}

		return true;
	}

	private function isSnapshotFileName($name, $format) {
		if ($format !== 'xml' && $format !== 'csv') {
			return false;
		}

		$extension = preg_quote($format, '/');
		return (bool)preg_match('/^(?:cjenik_[a-z0-9-]{1,40}_[a-z0-9-]{1,60}_[a-z0-9-]{1,40}_\d{6,12}_\d{8}_\d{6}|digital-pricelist_\d{8}_\d{6}_[a-f0-9]{12})\.' . $extension . '$/', (string)$name);
	}

	private function newSnapshotBaseName($sequence) {
		$type = $this->slug($this->settingOrDefault('feed_digital_pricelist_object_type', 'webshop'), 40);
		$address = $this->settingOrDefault('feed_digital_pricelist_object_address', (string)$this->config->get('config_address'));

		if (trim($address) === '') {
			$address = (string)$this->config->get('config_name');
		}

		$designation = $this->slug($this->settingOrDefault('feed_digital_pricelist_object_designation', 'WEB-01'), 40);
		return 'cjenik_' . $type . '_' . $this->slug($address, 60) . '_' . $designation . '_' . str_pad((string)$sequence, 6, '0', STR_PAD_LEFT) . '_' . date('Ymd_His');
	}

	private function nextStorageSequence() {
		$path = $this->directory . '/sequence.txt';
		$current = 0;

		if (is_file($path) && is_readable($path)) {
			$current = max(0, (int)trim((string)file_get_contents($path)));
		} else {
			$manifest = $this->readManifest();
			$current = isset($manifest['storage_sequence']) ? max(0, (int)$manifest['storage_sequence']) : 0;
		}

		$next = $current + 1;
		$temp = tempnam($this->directory, '.sequence-build-');

		if ($temp === false || file_put_contents($temp, (string)$next . "\n", LOCK_EX) === false) {
			$this->removeFile($temp);
			throw new \RuntimeException('Nije moguće spremiti redni broj digitalnog cjenika.');
		}

		@chmod($temp, 0640);

		if (!rename($temp, $path)) {
			$this->removeFile($temp);
			throw new \RuntimeException('Nije moguće aktivirati redni broj digitalnog cjenika.');
		}

		return $next;
	}

	private function settingOrDefault($key, $default) {
		$value = trim((string)$this->config->get($key));
		return $value !== '' ? $value : (string)$default;
	}

	private function slug($value, $limit) {
		$value = $this->cleanText($value);

		if (function_exists('iconv')) {
			$converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

			if ($converted !== false) {
				$value = $converted;
			}
		}

		$value = strtolower($value);
		$value = preg_replace('/[^a-z0-9]+/', '-', $value);
		$value = trim((string)$value, '-');

		if ($value === '') {
			$value = 'nije-navedeno';
		}

		return substr($value, 0, (int)$limit);
	}

	private function removeExpiredSnapshots(array $active_manifest) {
		$retention_days = max(30, (int)$this->config->get('feed_digital_pricelist_archive_days'));
		$cutoff = time() - ($retention_days * 86400);
		$active = array_filter(array(
			isset($active_manifest['xml_file']) ? $active_manifest['xml_file'] : '',
			isset($active_manifest['csv_file']) ? $active_manifest['csv_file'] : ''
		));

		foreach (glob($this->archive_directory . '/*.*') ?: array() as $path) {
			$name = basename($path);

			if (in_array($name, $active, true)) {
				continue;
			}

			$format = strtolower((string)pathinfo($name, PATHINFO_EXTENSION));

			if (!$this->isSnapshotFileName($name, $format)) {
				continue;
			}

			if (is_file($path) && filemtime($path) < $cutoff) {
				@unlink($path);
			}
		}
	}

	private function removeFile($path) {
		if (is_string($path) && $path !== '' && is_file($path)) {
			@unlink($path);
		}
	}
}
