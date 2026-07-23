<?php
namespace Eracuni;

require_once(__DIR__ . '/client.php');

class Synchronizer {
	private $registry;
	private $db;
	private $config;
	private $client;
	private $log;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
		$this->client = new Client($registry);
		$this->log = new \Log('eracuni_sync.log');
	}

	public function getConnectionInfo() {
		return $this->client->getConnectionInfo();
	}

	public function testConnection() {
		$products = $this->getOpenCartProducts();
		$parameters = array('status' => 'active');

		if ($products) {
			$parameters['productCode'] = implode(',', array_slice(array_keys($products), 0, 3));
		}

		$result = $this->client->call('ProductList', $parameters);

		return array(
			'success' => true,
			'message' => 'Veza s e-Racuni API-jem je uspjesna.',
			'records' => count($this->extractProductRecords($result, 'grossPrice'))
		);
	}

	public function syncStock() {
		return $this->withLock('stock', function () {
			$started = microtime(true);
			$products = $this->getOpenCartProducts();

			if (!$products) {
				throw new \RuntimeException('U OpenCartu nema proizvoda sa sifrom u odabranom polju.');
			}

			$warehouse = trim((string)$this->config->get('module_eracuni_sync_warehouse_code'));
			$stock_mode = (string)$this->config->get('module_eracuni_sync_stock_mode');
			$query_type = $stock_mode === 'physical' ? 'cumulativeForAllWarehouses' : 'availableForAllWarehouses';

			if ($warehouse !== '') {
				$query_type = $stock_mode === 'physical' ? 'perWarehouse' : 'perWarehouseAvailableStock';
			}

			$parameters = array(
				'queryType'                => $query_type,
				'productCode'              => array_keys($products),
				'productStatus'            => 'active',
				'allStockTrackingProducts' => true,
				'isForSale'                => true
			);

			if ($warehouse !== '') {
				$parameters['warehouseCode'] = $warehouse;
			}

			$result = $this->client->call('WarehouseGetArticleStockQuantity', $parameters);
			$stock_records = $this->extractStockRecords($result, array_keys($products));

			if (!$stock_records) {
				throw new \RuntimeException('e-Racuni nije vratio prepoznatljive zapise zalihe; proizvodi nisu promijenjeni.');
			}

			$summary = $this->baseSummary('stock', count($stock_records));
			$this->db->query('START TRANSACTION');

			try {
				foreach ($stock_records as $code => $quantity) {
					if (!isset($products[$code])) {
						$summary['skipped']++;
						continue;
					}

					$quantity = max(0, (int)floor((float)$quantity));
					$product = $products[$code];

					if ((int)$product['quantity'] === $quantity) {
						$summary['unchanged']++;
						continue;
					}

					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = '" . (int)$quantity . "', date_modified = NOW() WHERE product_id = '" . (int)$product['product_id'] . "'");
					$summary['updated']++;
				}

				$this->db->query('COMMIT');
			} catch (\Throwable $exception) {
				$this->db->query('ROLLBACK');
				throw $exception;
			}

			$summary['duration_ms'] = (int)round((microtime(true) - $started) * 1000);
			$summary['message'] = 'Zaliha je sinkronizirana.';
			$this->saveLastRun('stock', $summary);

			return $summary;
		});
	}

	public function syncPrices() {
		return $this->withLock('prices', function () {
			$started = microtime(true);
			$products = $this->getOpenCartProducts();

			if (!$products) {
				throw new \RuntimeException('U OpenCartu nema proizvoda sa sifrom u odabranom polju.');
			}

			$price_field = (string)$this->config->get('module_eracuni_sync_price_field');

			if (!in_array($price_field, array('grossPrice', 'retailPrice', 'purchasePrice'), true)) {
				$price_field = 'grossPrice';
			}

			$result = $this->client->call('ProductList', array(
				'productCode' => implode(',', array_keys($products)),
				'status'      => 'active'
			));
			$price_records = $this->extractProductRecords($result, $price_field);

			if (!$price_records) {
				throw new \RuntimeException('e-Racuni nije vratio prepoznatljive cijene; proizvodi nisu promijenjeni.');
			}

			$summary = $this->baseSummary('prices', count($price_records));
			// e-Racuni's top-level retailPrice is the final sales price with
			// outgoing VAT included. OpenCart stores the net price and applies
			// the configured tax class later, so retailPrice must always be
			// converted to net to avoid charging VAT twice.
			$price_includes_tax = (bool)$this->config->get('module_eracuni_sync_price_includes_tax');
			$this->db->query('START TRANSACTION');

			try {
				foreach ($price_records as $record) {
					$code = trim((string)$record['productCode']);

					if ($code === '' || !isset($products[$code]) || !isset($record[$price_field]) || !is_numeric($record[$price_field])) {
						$summary['skipped']++;
						continue;
					}

					$price = $this->resolveOpenCartPrice($record, $price_field, $price_includes_tax);

					// A zero catalogue price is commonly used by the legacy order
					// connector as a placeholder. Never let that erase a live shop
					// price during synchronization.
					if ($price <= 0) {
						$summary['skipped']++;
						continue;
					}

					$price = round($price, 4);
					$product = $products[$code];

					if (abs((float)$product['price'] - $price) < 0.00005) {
						$summary['unchanged']++;
						continue;
					}

					$this->db->query("UPDATE `" . DB_PREFIX . "product` SET price = '" . (float)$price . "', date_modified = NOW() WHERE product_id = '" . (int)$product['product_id'] . "'");
					$summary['updated']++;
				}

				$this->db->query('COMMIT');
			} catch (\Throwable $exception) {
				$this->db->query('ROLLBACK');
				throw $exception;
			}

			$summary['duration_ms'] = (int)round((microtime(true) - $started) * 1000);
			$summary['message'] = 'Cijene su sinkronizirane.';
			$this->saveLastRun('prices', $summary);

			return $summary;
		});
	}

	/** Public for deterministic fixture tests. */
	public function extractProductRecords($node, $price_field) {
		$records = array();
		$this->collectProductRecords($node, $price_field, $records);
		$unique = array();

		foreach ($records as $record) {
			$code = trim((string)$record['productCode']);

			if ($code !== '') {
				$unique[$code] = $record;
			}
		}

		return array_values($unique);
	}

	/** Public for deterministic fixture tests. */
	public function resolveOpenCartPrice(array $record, $price_field, $price_includes_tax) {
		$price_includes_tax = $price_field === 'retailPrice' || (bool)$price_includes_tax;
		$price = isset($record[$price_field]) && is_numeric($record[$price_field])
			? max(0, (float)$record[$price_field])
			: 0.0;

		if (!$price_includes_tax || $price <= 0) {
			return $price;
		}

		$vat = isset($record['vatPercentage']) && is_numeric($record['vatPercentage'])
			? (float)$record['vatPercentage']
			: 0.0;

		// ProductList can return vatPercentage=0 on the product itself even
		// though retailPrice contains VAT. The actual outgoing VAT rate is then
		// available in the nested price calculation.
		if ($vat <= 0
			&& isset($record['PriceCalculation'])
			&& is_array($record['PriceCalculation'])
			&& isset($record['PriceCalculation']['outgoingVatPercentage'])
			&& is_numeric($record['PriceCalculation']['outgoingVatPercentage'])
		) {
			$vat = (float)$record['PriceCalculation']['outgoingVatPercentage'];
		}

		if ($vat > 0) {
			$price = $price / (1 + ($vat / 100));
		}

		return $price;
	}

	/** Public for deterministic fixture tests. */
	public function extractStockRecords($node, array $known_codes = array()) {
		$records = array();
		$this->collectStockRecords($node, $records);

		if (!$records && is_array($node)) {
			$known = array_fill_keys($known_codes, true);
			$this->collectScalarStockMap($node, $known, $records);
		}

		return $records;
	}

	private function collectProductRecords($node, $price_field, array &$records) {
		if (!is_array($node)) {
			return;
		}

		if (isset($node['productCode']) && array_key_exists($price_field, $node)) {
			$records[] = $node;
			return;
		}

		foreach ($node as $value) {
			$this->collectProductRecords($value, $price_field, $records);
		}
	}

	private function collectStockRecords($node, array &$records) {
		if (!is_array($node)) {
			return;
		}

		if (isset($node['StockQuantityInfo']) && is_array($node['StockQuantityInfo'])) {
			$this->collectStockRecords($node['StockQuantityInfo'], $records);
			return;
		}

		$code = '';
		foreach (array('productCode', 'articleCode', 'code') as $key) {
			if (isset($node[$key]) && is_scalar($node[$key])) {
				$code = trim((string)$node[$key]);
				break;
			}
		}

		if ($code !== '') {
			foreach (array('quantityOnStock', 'availableQuantity', 'stockQuantity', 'quantity', 'stock', 'qty') as $key) {
				if (isset($node[$key]) && is_numeric($node[$key])) {
					$records[$code] = (float)$node[$key];
					return;
				}
			}
		}

		foreach ($node as $value) {
			$this->collectStockRecords($value, $records);
		}
	}

	private function collectScalarStockMap($node, array $known, array &$records) {
		foreach ($node as $key => $value) {
			if (is_array($value)) {
				$this->collectScalarStockMap($value, $known, $records);
			} elseif (isset($known[(string)$key]) && is_numeric($value)) {
				$records[(string)$key] = (float)$value;
			}
		}
	}

	private function getOpenCartProducts() {
		$code_field = (string)$this->config->get('module_eracuni_sync_code_field');

		if (!in_array($code_field, array('model', 'sku', 'ean'), true)) {
			$code_field = 'model';
		}

		$query = $this->db->query("SELECT product_id, `" . $code_field . "` AS code, quantity, price FROM `" . DB_PREFIX . "product` WHERE TRIM(`" . $code_field . "`) <> ''");
		$products = array();

		foreach ($query->rows as $row) {
			$code = trim((string)$row['code']);

			if ($code !== '') {
				$products[$code] = $row;
			}
		}

		return $products;
	}

	private function baseSummary($type, $processed) {
		return array(
			'success'     => true,
			'type'        => $type,
			'processed'   => (int)$processed,
			'updated'     => 0,
			'unchanged'   => 0,
			'skipped'     => 0,
			'duration_ms' => 0,
			'finished_at' => date('Y-m-d H:i:s')
		);
	}

	private function withLock($type, $callback) {
		$lock_name = DB_PREFIX . 'eracuni_sync_' . preg_replace('/[^a-z_]/', '', $type);
		$query = $this->db->query("SELECT GET_LOCK('" . $this->db->escape($lock_name) . "', 0) AS acquired");

		if (empty($query->row['acquired'])) {
			throw new \RuntimeException('Sinkronizacija je vec pokrenuta. Pokusajte ponovno nakon zavrsetka.');
		}

		try {
			return call_user_func($callback);
		} catch (\Throwable $exception) {
			$this->log->write(strtoupper($type) . ' ERROR: ' . $exception->getMessage());
			$this->saveLastRun($type, array(
				'success'     => false,
				'type'        => $type,
				'message'     => $exception->getMessage(),
				'finished_at' => date('Y-m-d H:i:s')
			));
			throw $exception;
		} finally {
			$this->db->query("SELECT RELEASE_LOCK('" . $this->db->escape($lock_name) . "')");
		}
	}

	private function saveLastRun($type, array $summary) {
		$key = 'module_eracuni_sync_last_' . ($type === 'prices' ? 'prices' : 'stock');
		$value = json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$existing = $this->db->query("SELECT setting_id FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `code` = 'module_eracuni_sync' AND `key` = '" . $this->db->escape($key) . "' LIMIT 1");

		if ($existing->num_rows) {
			$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '" . $this->db->escape($value) . "', serialized = '0' WHERE setting_id = '" . (int)$existing->row['setting_id'] . "'");
		} else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET store_id = '0', `code` = 'module_eracuni_sync', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "', serialized = '0'");
		}

		$this->log->write(strtoupper($type) . ': ' . $value);
	}
}
