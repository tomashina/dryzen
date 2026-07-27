<?php
namespace Eracuni;

class OrderExporter {
	private $registry;
	private $db;
	private $log;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->db = $registry->get('db');
		$this->log = new \Log('eracuni_orders.log');
	}

	public function export($order_id) {
		$order_id = (int)$order_id;

		if ($order_id < 1) {
			throw new \InvalidArgumentException('Nedostaje ispravan ID narudžbe.');
		}

		return $this->withOrderLock($order_id, function () use ($order_id) {
			$order = $this->getOrder($order_id);

			if (!$order) {
				throw new \RuntimeException('Narudžba #' . $order_id . ' ne postoji.');
			}

			if (!empty($order['number_order'])) {
				return array(
					'success'      => true,
					'skipped'      => true,
					'order_id'     => $order_id,
					'number_order' => $order['number_order'],
					'message'      => 'Narudžba je već izrađena u e-Računi.'
				);
			}

			if ((int)$order['order_status_id'] < 1) {
				return array(
					'success'  => true,
					'skipped'  => true,
					'order_id' => $order_id,
					'message'  => 'Nepotvrđena narudžba nije poslana u e-Računi.'
				);
			}

			$this->loadEnvironment();

			$auth = array(
				'username'  => agconf('import.api.username'),
				'secretKey' => agconf('import.api.password'),
				'token'     => agconf('import.api.token')
			);

			foreach ($auth as $value) {
				if (!$value || $value === 'Env key not found!') {
					throw new \RuntimeException('Nedostaju e-Računi API pristupni podaci.');
				}
			}

			$api = new \Agmedia\Api\Api();
			$eracuni = new \Agmedia\Api\Connection\Csv\Eracuni($order);

			$eracuni->ensureCatalogueProductsExist($api, $auth);

			$parameters = $eracuni->createSale('order', 'json');
			$parameters['apiTransactionId'] = $this->getTransactionId($order_id);

			$body = array(
				'username'   => $auth['username'],
				'secretKey'  => $auth['secretKey'],
				'token'      => $auth['token'],
				'method'     => 'SalesOrderCreate',
				'parameters' => $parameters
			);

			$response = $this->postWithRetry($api, $body);

			if (!is_array($response) || empty($response['number'])) {
				throw new \RuntimeException('e-Računi nije vratio broj izrađene narudžbe.');
			}

			$number = trim((string)$response['number']);

			$this->db->query(
				"UPDATE `" . DB_PREFIX . "order` SET number_order = '" . $this->db->escape($number) .
				"', date_modified = NOW() WHERE order_id = '" . $order_id . "' AND (number_order IS NULL OR number_order = '')"
			);

			$this->log->write('Automatski izrađena e-Računi narudžba #' . $order_id . ' (' . $number . ').');

			return array(
				'success'      => true,
				'skipped'      => false,
				'order_id'     => $order_id,
				'number_order' => $number,
				'message'      => 'Narudžba je izrađena u e-Računi.'
			);
		});
	}

	private function getOrder($order_id) {
		$query = $this->db->query(
			"SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' LIMIT 1"
		);

		if (!$query->num_rows) {
			return array();
		}

		$order = $query->row;

		$products = $this->db->query(
			"SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id .
			"' ORDER BY order_product_id ASC"
		);
		$order['products'] = $products->rows;

		$totals = $this->db->query(
			"SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id .
			"' ORDER BY sort_order ASC, order_total_id ASC"
		);
		$order['totals'] = $totals->rows;

		return $order;
	}

	private function loadEnvironment() {
		if (defined('OC_ENV')) {
			return;
		}

		$environment_file = dirname(rtrim(DIR_APPLICATION, '/\\')) . '/env.php';

		if (!is_file($environment_file)) {
			throw new \RuntimeException('Nedostaje e-Računi konfiguracijska datoteka.');
		}

		require_once($environment_file);

		if (!defined('OC_ENV')) {
			throw new \RuntimeException('e-Računi konfiguracija nije učitana.');
		}
	}

	private function getTransactionId($order_id) {
		return 'dryzen-order-' . (int)$order_id;
	}

	private function postWithRetry($api, array $body) {
		$response = null;

		for ($attempt = 1; $attempt <= 3; $attempt++) {
			$response = $api->post('WebServices/API', $body, 'json');

			if (is_array($response) && !empty($response['number'])) {
				return $response;
			}

			$description = $this->getApiError($response);

			if ($description !== '' && stripos($description, 'Too Many Requests') === false) {
				throw new \RuntimeException('e-Računi API: ' . $description);
			}

			if ($attempt < 3) {
				usleep($attempt * 500000);
			}
		}

		$description = $this->getApiError($response);

		if ($description !== '') {
			throw new \RuntimeException('e-Računi API: ' . $description);
		}

		throw new \RuntimeException('Greška pri komunikaciji s e-Računi API-jem.');
	}

	private function getApiError($response) {
		if (!is_array($response)) {
			return '';
		}

		if (isset($response['response']['status']) && $response['response']['status'] === 'error') {
			return isset($response['response']['description']) ? (string)$response['response']['description'] : 'Nepoznata greška';
		}

		if (isset($response['status']) && $response['status'] === 'error') {
			return isset($response['description']) ? (string)$response['description'] : 'Nepoznata greška';
		}

		return '';
	}

	private function withOrderLock($order_id, callable $callback) {
		$lock_name = DB_PREFIX . 'eracuni_order_' . (int)$order_id;
		$lock = $this->db->query(
			"SELECT GET_LOCK('" . $this->db->escape($lock_name) . "', 0) AS acquired"
		);

		if (empty($lock->row['acquired'])) {
			return array(
				'success'  => true,
				'skipped'  => true,
				'order_id' => (int)$order_id,
				'message'  => 'Izrada ove narudžbe u e-Računi već je pokrenuta.'
			);
		}

		try {
			return $callback();
		} finally {
			$this->db->query(
				"SELECT RELEASE_LOCK('" . $this->db->escape($lock_name) . "')"
			);
		}
	}
}
