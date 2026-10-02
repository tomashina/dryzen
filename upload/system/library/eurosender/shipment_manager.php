<?php
namespace Eurosender;

class ShipmentManager {
	private $registry;
	protected $db;
	protected $config;
	protected $log;
	protected $client;
	private $schema_installed = false;
	private $schema_exists;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
		$this->log = $registry->has('log') ? $registry->get('log') : null;
		$this->client = $this->resolveClient();

		$registry->set('eurosender_shipment_manager', $this);
	}

	public function installSchema() {
		if ($this->schema_installed) {
			return;
		}

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "eurosender_shipment` (
			`eurosender_shipment_id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`order_number` varchar(64) NOT NULL DEFAULT '',
			`customer_internal_reference` varchar(128) NOT NULL DEFAULT '',
			`service_type` varchar(96) NOT NULL DEFAULT '',
			`environment` varchar(16) NOT NULL DEFAULT 'sandbox',
			`state` varchar(32) NOT NULL DEFAULT '',
			`retryable` tinyint(1) NOT NULL DEFAULT '0',
			`order_code` varchar(128) NULL DEFAULT NULL,
			`status` varchar(96) NOT NULL DEFAULT '',
			`tracking_number` varchar(255) NOT NULL DEFAULT '',
			`tracking_url` varchar(512) NOT NULL DEFAULT '',
			`currency_code` varchar(3) NOT NULL DEFAULT 'EUR',
			`quote_price` decimal(15,4) NULL DEFAULT NULL,
			`booked_price` decimal(15,4) NULL DEFAULT NULL,
			`price_difference` decimal(15,4) NULL DEFAULT NULL,
			`last_http_status` int(11) NOT NULL DEFAULT '0',
			`creation_attempted_at` datetime NULL DEFAULT NULL,
			`created_at` datetime NULL DEFAULT NULL,
			`tracking_checked_at` datetime NULL DEFAULT NULL,
			`label_downloaded_at` datetime NULL DEFAULT NULL,
			`creation_error` text NULL,
			`tracking_error` text NULL,
			`label_error` text NULL,
			`payload` mediumtext NULL,
			`quote_response` mediumtext NULL,
			`validation_response` mediumtext NULL,
			`response` mediumtext NULL,
			`tracking_response` mediumtext NULL,
			`date_added` datetime NOT NULL,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`eurosender_shipment_id`),
			UNIQUE KEY `order_id` (`order_id`),
			UNIQUE KEY `order_code` (`order_code`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		$environment_column = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "eurosender_shipment` LIKE 'environment'");

		if (!$environment_column->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "eurosender_shipment` ADD `environment` varchar(16) NOT NULL DEFAULT 'sandbox' AFTER `service_type`");
		}

		$this->schema_installed = true;
		$this->schema_exists = true;
	}

	public function getShipment($order_id) {
		if (!$this->shipmentTableExists()) {
			return array();
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "eurosender_shipment` WHERE order_id = '" . (int)$order_id . "' LIMIT 1");

		return $query->num_rows ? $query->row : array();
	}

	public function getShipmentByOrderId($order_id) {
		return $this->getShipment($order_id);
	}

	public function previewShipment($order_id) {
		$order_id = (int)$order_id;

		if ($order_id < 1) {
			throw new \InvalidArgumentException('Neispravan ID narudžbe.');
		}

		$this->installSchema();

		return $this->withOrderLock($order_id, function () use ($order_id) {
			return $this->previewShipmentUnlocked($order_id);
		});
	}

	public function createShipment($order_id, $confirmation_token) {
		$order_id = (int)$order_id;

		if ($order_id < 1) {
			throw new \InvalidArgumentException('Neispravan ID narudžbe.');
		}

		$this->installSchema();

		return $this->withOrderLock($order_id, function () use ($order_id, $confirmation_token) {
			return $this->createShipmentUnlocked($order_id, $confirmation_token);
		});
	}

	public function refreshTracking($order_id) {
		$order_id = (int)$order_id;
		$this->installSchema();

		return $this->withOrderLock($order_id, function () use ($order_id) {
			$shipment = $this->getShipment($order_id);
			$this->assertShipmentEnvironment($shipment);

			if (empty($shipment['order_code'])) {
				throw new \RuntimeException('Eurosender pošiljka još nema order code.');
			}

			try {
				$order_response = $this->client->getOrder($shipment['order_code']);
				$tracking_response = array();

				try {
					$tracking_response = $this->client->getTracking($shipment['order_code']);
				} catch (ApiException $exception) {
					// Some Eurosender accounts do not expose a separate tracking
					// resource yet. The order endpoint still contains usable status
					// and parcel tracking data, so a 404 must not discard it.
					if ($exception->getHttpStatus() !== 404) {
						throw $exception;
					}
				}

				$status = $this->extractStatus($tracking_response);

				if ($status === '') {
					$status = $this->extractStatus($order_response);
				}

				$tracking_number = $this->extractTrackingNumber($tracking_response);

				if ($tracking_number === '') {
					$tracking_number = $this->extractTrackingNumber($order_response);
				}

				$tracking_url = $this->extractTrackingUrl($tracking_response);

				if ($tracking_url === '') {
					$tracking_url = $this->extractTrackingUrl($order_response);
				}

				$booked_money = $this->extractBookedMoney($order_response);
				$this->recordTrackingSuccess(
					$shipment,
					$status,
					$tracking_number,
					$tracking_url,
					$order_response,
					$tracking_response,
					$booked_money
				);
			} catch (\Throwable $exception) {
				$this->recordTrackingError($shipment, $exception->getMessage());
				throw $exception;
			}

			return $this->getShipment($order_id);
		});
	}

	public function getLabel($order_id) {
		$order_id = (int)$order_id;
		$this->installSchema();
		$shipment = $this->getShipment($order_id);
		$this->assertShipmentEnvironment($shipment);

		if (empty($shipment['order_code'])) {
			throw new \RuntimeException('Eurosender pošiljka još nema order code.');
		}

		try {
			$response = $this->client->getLabels($shipment['order_code']);
			$pdf = $this->extractPdf($response);

			if ($pdf === '') {
				throw new \RuntimeException('Eurosender još nije vratio ispravnu PDF adresnicu.');
			}

			$this->recordLabelSuccess($shipment);

			return $pdf;
		} catch (\Throwable $exception) {
			$this->recordLabelError($shipment, $exception->getMessage());
			throw $exception;
		}
	}

	public function getStatusLabel($status) {
		$status = strtolower(trim((string)$status));
		$labels = array(
			'validating'   => 'Provjera podataka',
			'creating'     => 'Kreiranje u tijeku',
			'created'      => 'Kreirana',
			'order received' => 'Narudžba zaprimljena',
			'deferred payment' => 'Odobreno odgođeno plaćanje',
			'awaiting payment' => 'Čeka plaćanje',
			'awaiting customs documentation' => 'Čeka carinsku dokumentaciju',
			'awaiting pickup' => 'Čeka preuzimanje',
			'pickup confirmed' => 'Preuzimanje potvrđeno',
			'collected'    => 'Preuzeta',
			'confirmed'    => 'Potvrđena',
			'pending'      => 'Na čekanju',
			'in_transit'   => 'U tranzitu',
			'in-transit'   => 'U tranzitu',
			'in transit'   => 'U tranzitu',
			'inforeceived' => 'Podaci o pošiljci zaprimljeni',
			'intransit'    => 'U tranzitu',
			'out_for_delivery' => 'Na dostavi',
			'outfordelivery' => 'Na dostavi',
			'attemptfail'  => 'Neuspjeli pokušaj dostave',
			'availableforpickup' => 'Spremna za preuzimanje',
			'delivered'    => 'Dostavljena',
			'cancelled'    => 'Otkazana',
			'canceled'     => 'Otkazana',
			'returned'     => 'Vraćena',
			'exception'    => 'Iznimka u dostavi',
			'expired'      => 'Istekla',
			'failed'       => 'Neuspjela',
			'error'        => 'Pogreška – ponovni pokušaj je dopušten',
			'unknown'      => 'Ishod kreiranja nije poznat – potrebna je ručna provjera'
		);

		return isset($labels[$status]) ? $labels[$status] : ($status !== '' ? $status : 'Nije dostupno');
	}

	public function getTrackingUrl($shipment_or_tracking_url) {
		$tracking_number = '';
		$url = '';

		if (is_array($shipment_or_tracking_url)) {
			$url = isset($shipment_or_tracking_url['tracking_url']) ? trim((string)$shipment_or_tracking_url['tracking_url']) : '';
			$tracking_number = isset($shipment_or_tracking_url['tracking_number']) ? trim((string)$shipment_or_tracking_url['tracking_number']) : '';

			if ($url === '' && !empty($shipment_or_tracking_url['tracking_response'])) {
				$decoded = json_decode($shipment_or_tracking_url['tracking_response'], true);
				$url = is_array($decoded) ? $this->extractTrackingUrl($decoded) : '';
			}
		} else {
			$value = trim((string)$shipment_or_tracking_url);

			if (filter_var($value, FILTER_VALIDATE_URL)) {
				$url = $value;
			} else {
				$tracking_number = $value;
			}
		}

		if ($this->isSafeHttpUrl($url)) {
			return $url;
		}

		$template = trim((string)$this->getConfig('tracking_url', ''));

		if ($template !== '' && $tracking_number !== '') {
			if (strpos($template, '{tracking}') !== false) {
				$candidate = str_replace('{tracking}', rawurlencode($tracking_number), $template);
			} else {
				$candidate = rtrim($template, '/') . '/' . rawurlencode($tracking_number);
			}

			return $this->isSafeHttpUrl($candidate) ? $candidate : '';
		}

		return '';
	}

	public function getConnectionInfo() {
		return $this->client->getConnectionInfo();
	}

	public function isConfigured() {
		return $this->client->isConfigured();
	}

	protected function previewShipmentUnlocked($order_id) {
		$existing = $this->getShipment($order_id);

		if (!empty($existing['order_code']) || (isset($existing['state']) && $existing['state'] === 'created')) {
			$this->assertShipmentEnvironment($existing);
			throw new \RuntimeException('Eurosender pošiljka za ovu narudžbu već postoji.');
		}

		$this->assertSafeCreationState($existing);

		if (!$this->client->isConfigured()) {
			throw new \RuntimeException('Eurosender API ključ nije konfiguriran u upload/env.php.');
		}

		$order = $this->getOrder($order_id);
		$payload = $this->buildOrderPayload($order);
		$quote_response = $this->client->quote($this->buildQuotePayload($payload));
		$quote_money = $this->extractSelectedQuoteMoney($quote_response);

		if ($quote_money['amount'] === null || strtoupper((string)$quote_money['currency']) !== 'EUR') {
			throw new \RuntimeException('Eurosender ponuda ne sadrži ispravnu EUR cijenu za odabranu uslugu ' . $payload['serviceType'] . '.');
		}

		$validation_response = $this->client->validateOrder($payload);
		$this->assertValidationPassed($validation_response);
		$checkout_shipping_price = $this->getCheckoutShippingPrice($order_id);
		$token = $this->storePreview($order_id, $payload, $quote_money, $checkout_shipping_price);

		return array(
			'confirmation_token'      => $token,
			'quote_price'             => (float)$quote_money['amount'],
			'currency_code'           => 'EUR',
			'checkout_shipping_price' => $checkout_shipping_price,
			'service_type'            => $payload['serviceType']
		);
	}

	protected function createShipmentUnlocked($order_id, $confirmation_token) {
		$existing = $this->getShipment($order_id);

		if (!empty($existing['order_code']) || (isset($existing['state']) && $existing['state'] === 'created')) {
			$this->assertShipmentEnvironment($existing);
			$existing['existing'] = true;
			return $existing;
		}

		$this->assertSafeCreationState($existing);

		if (!$this->client->isConfigured()) {
			throw new \RuntimeException('Eurosender API ključ nije konfiguriran u upload/env.php.');
		}

		$order = $this->getOrder($order_id);
		$payload = $this->buildOrderPayload($order);
		$preview = $this->consumeAndGetPreview($order_id, $confirmation_token, $payload, $this->getCheckoutShippingPrice($order_id));
		$this->prepareValidationRecord($existing, $order, $payload);
		$shipment = $this->getShipment($order_id);

		try {
			$quote_response = $this->client->quote($this->buildQuotePayload($payload));
			$quote_money = $this->extractSelectedQuoteMoney($quote_response);

			if ($quote_money['amount'] === null) {
				throw new \RuntimeException('Eurosender svježa ponuda ne sadrži cijenu za odabranu uslugu ' . $payload['serviceType'] . '.');
			}

			$this->recordQuote($shipment, $quote_response, $quote_money);
			$shipment = $this->getShipment($order_id);

			if (strtoupper((string)$quote_money['currency']) !== 'EUR') {
				throw new \RuntimeException('Eurosender svježa ponuda nije vraćena u EUR; rezervacija nije poslana.');
			}

			if ($this->moneyToCents($quote_money['amount']) > (int)$preview['quote_price_cents']) {
				throw new \RuntimeException(
					'Eurosender cijena promijenila se s potvrđenih ' . number_format((float)$preview['quote_price'], 2, ',', '.') .
					' EUR na ' . number_format((float)$quote_money['amount'], 2, ',', '.') .
					' EUR. Rezervacija nije poslana; ponovno provjerite i potvrdite novu cijenu.'
				);
			}

			$validation_response = $this->client->validateOrder($payload);
			$this->assertValidationPassed($validation_response);
			$this->recordValidationSuccess($shipment, $validation_response);
			$shipment = $this->getShipment($order_id);
		} catch (\Throwable $exception) {
			$this->recordPreCreateFailure($shipment, $exception);
			throw $exception;
		}

		// There is no documented Eurosender idempotency key. Persist this state
		// before POST /orders and never automatically retry an ambiguous result.
		$this->recordCreating($shipment);
		$shipment = $this->getShipment($order_id);

		try {
			$response = $this->client->createOrder($payload);
			$order_code = $this->extractOrderCode($response);

			if ($order_code === '') {
				throw new ApiException('Eurosender je prihvatio zahtjev, ali odgovor nema orderCode.', 200, $this->encodeJson($response), false, true);
			}

			$booked_money = $this->extractBookedMoney($response);
			$this->recordCreated(
				$shipment,
				$order_code,
				$this->extractStatus($response),
				$this->extractTrackingNumber($response),
				$this->extractTrackingUrl($response),
				$response,
				$booked_money
			);
		} catch (\Throwable $exception) {
			$unknown = $this->isAmbiguousCreateFailure($exception);
			$this->recordCreateFailure($shipment, $exception, $unknown);
			throw $exception;
		}

		return $this->getShipment($order_id);
	}

	private function buildQuotePayload($payload) {
		return array(
			'shipment'      => $payload['shipment'],
			'parcels'       => $payload['parcels'],
			'paymentMethod' => $payload['paymentMethod'],
			'currencyCode'  => 'EUR',
			'serviceType'   => $payload['serviceType']
		);
	}

	protected function buildOrderPayload($order) {
		if (!$order || empty($order['order_id'])) {
			throw new \RuntimeException('Narudžba ne postoji.');
		}

		$shipping_code = isset($order['shipping_code']) ? trim((string)$order['shipping_code']) : '';

		if (strpos($shipping_code, 'eurosender.') !== 0) {
			throw new \RuntimeException('Narudžba nije odabrala Eurosender dostavu.');
		}

		$service_type = substr($shipping_code, strlen('eurosender.'));
		$allowed_service_types = array('selection', 'regular_plus', 'express');

		if (!in_array($service_type, $allowed_service_types, true)) {
			throw new \RuntimeException('Narudžba nema ispravan Eurosender serviceType.');
		}

		$payment_method = strtolower(trim((string)$this->getConfig('payment_method', 'credit')));

		if (!in_array($payment_method, array('credit', 'deferred'), true)) {
			throw new \RuntimeException('Eurosender način plaćanja mora biti credit ili deferred.');
		}

		// OpenCart stores order and order-product amounts in the store base
		// currency; order.currency_code is only the currency selected for display.
		$store_currency = strtoupper(trim((string)$this->config->get('config_currency')));

		if ($store_currency !== '' && $store_currency !== 'EUR') {
			throw new \RuntimeException('Eurosender vrijednost paketa mora biti u EUR, a osnovna valuta trgovine je ' . $store_currency . '.');
		}

		$origin_country = $this->requireCountryCode($this->getConfig('origin_country_code', 'HR'), 'zemlja preuzimanja');
		$delivery_country = $this->getOrderCountryCode($order);
		$origin_name = $this->requiredConfig('origin_name', 'ime kontakta za preuzimanje');
		$origin_email = $this->requireEmail($this->requiredConfig('origin_email', 'email kontakta za preuzimanje'), 'email kontakta za preuzimanje');
		$origin_phone = $this->normalizePhone($this->requiredConfig('origin_phone', 'telefon kontakta za preuzimanje'), $origin_country);
		$delivery_name = trim((isset($order['shipping_firstname']) ? $order['shipping_firstname'] : '') . ' ' . (isset($order['shipping_lastname']) ? $order['shipping_lastname'] : ''));

		if ($delivery_name === '') {
			$delivery_name = trim((isset($order['firstname']) ? $order['firstname'] : '') . ' ' . (isset($order['lastname']) ? $order['lastname'] : ''));
		}

		if ($delivery_name === '') {
			throw new \RuntimeException('Nedostaje ime primatelja.');
		}

		$delivery_email = $this->requireEmail(isset($order['email']) ? $order['email'] : '', 'email primatelja');
		$delivery_phone = $this->normalizePhone(isset($order['telephone']) ? $order['telephone'] : '', $delivery_country);
		$origin_street = $this->joinAddressLines(
			$this->requiredConfig('origin_address_1', 'adresa preuzimanja'),
			$this->getConfig('origin_address_2', '')
		);
		$delivery_street = $this->joinAddressLines(
			isset($order['shipping_address_1']) ? $order['shipping_address_1'] : '',
			isset($order['shipping_address_2']) ? $order['shipping_address_2'] : ''
		);

		if ($delivery_street === '') {
			throw new \RuntimeException('Nedostaje adresa primatelja.');
		}

		$pickup_address = array(
			'country' => $origin_country,
			'zip'     => $this->requiredConfig('origin_postcode', 'poštanski broj preuzimanja'),
			'city'    => $this->requiredConfig('origin_city', 'grad preuzimanja'),
			'street'  => $origin_street
		);
		$delivery_address = array(
			'country' => $delivery_country,
			'zip'     => $this->requiredOrderValue($order, 'shipping_postcode', 'poštanski broj primatelja'),
			'city'    => $this->requiredOrderValue($order, 'shipping_city', 'grad primatelja'),
			'street'  => $delivery_street
		);

		if (!empty($order['shipping_zone'])) {
			$delivery_address['region'] = (string)$order['shipping_zone'];
		}

		$zone_code = $this->getOrderZoneCode($order);

		if ($zone_code !== '' && in_array($delivery_country, array('IT', 'US', 'CA'), true)) {
			$delivery_address['regionCode'] = $zone_code;
		}

		$order_id = (int)$order['order_id'];
		$internal_reference = 'DRYZEN-' . $order_id;
		$parcel = array(
			'parcelId' => $internal_reference . '-1',
			'quantity' => 1,
			'width'    => $this->positiveIntegerDimension('width', 15),
			'height'   => $this->positiveIntegerDimension('height', 10),
			'length'   => $this->positiveIntegerDimension('length', 20),
			'weight'   => $this->calculateParcelWeight($order_id),
			'content'  => trim((string)$this->getConfig('content', 'Cosmetics')),
			'value'    => $this->calculateParcelValue($order)
		);

		if ($parcel['content'] === '') {
			throw new \RuntimeException('Nedostaje opis sadržaja Eurosender paketa.');
		}

		return array(
			'shipment' => array(
				'pickupAddress'   => $pickup_address,
				'deliveryAddress' => $delivery_address,
				'pickupContact'   => array(
					'name'  => $origin_name,
					'email' => $origin_email,
					'phone' => $origin_phone
				),
				'deliveryContact' => array(
					'name'  => $delivery_name,
					'email' => $delivery_email,
					'phone' => $delivery_phone
				)
			),
			'parcels'                   => array('packages' => array($parcel)),
			'serviceType'              => $service_type,
			'paymentMethod'            => $payment_method,
			'orderContact'             => array('email' => $origin_email),
			'labelFormat'              => 'pdf',
			'customerInternalReference'=> $internal_reference
		);
	}

	protected function calculateParcelWeight($order_id) {
		$packaging_weight = $this->nonNegativeDecimalConfig('packaging_weight', 0.20);
		$fallback_item_weight = $this->nonNegativeDecimalConfig('fallback_item_weight', 0.10);
		$minimum_weight = $this->positiveDecimalConfig('minimum_weight', 0.50);
		$weight = $packaging_weight;
		$rows = $this->getOrderProductRows($order_id);
		$kilogram_class = $this->getKilogramWeightClass();

		foreach ($rows as $row) {
			$quantity = max(1, (int)$row['quantity']);
			$product_weight = isset($row['weight']) ? (float)$row['weight'] : 0.0;

			if ($product_weight > 0 && !empty($row['weight_class_value']) && $kilogram_class['value'] > 0) {
				$weight += ($product_weight * $quantity) * ($kilogram_class['value'] / (float)$row['weight_class_value']);
			} else {
				$weight += $fallback_item_weight * $quantity;
			}
		}

		if (!$rows) {
			$weight += $fallback_item_weight;
		}

		return round(max($minimum_weight, $weight), 3);
	}

	protected function calculateParcelValue($order) {
		$query = $this->db->query("SELECT SUM(op.total + (op.tax * op.quantity)) AS total FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = '" . (int)$order['order_id'] . "'");
		$value = isset($query->row['total']) ? (float)$query->row['total'] : 0.0;

		if ($value <= 0 && isset($order['total'])) {
			$value = (float)$order['total'];
		}

		return max(1, (int)ceil($value));
	}

	protected function getOrderProductRows($order_id) {
		$query = $this->db->query("SELECT op.quantity, p.weight, p.weight_class_id, wc.value AS weight_class_value FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "product` p ON (p.product_id = op.product_id) LEFT JOIN `" . DB_PREFIX . "weight_class` wc ON (wc.weight_class_id = p.weight_class_id) WHERE op.order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	protected function getCheckoutShippingPrice($order_id) {
		$query = $this->db->query("SELECT value FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' AND code = 'shipping' ORDER BY sort_order DESC LIMIT 1");

		return $query->num_rows && isset($query->row['value']) && is_numeric($query->row['value'])
			? max(0.0, (float)$query->row['value'])
			: null;
	}

	protected function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' LIMIT 1");

		return $query->num_rows ? $query->row : array();
	}

	protected function prepareValidationRecord($existing, $order, $payload) {
		$order_number = !empty($order['number_order']) ? $order['number_order'] : $order['order_id'];
		$payload_json = $this->encodeJson($payload);
		$connection = $this->client->getConnectionInfo();
		$environment = !empty($connection['environment']) ? strtolower((string)$connection['environment']) : 'sandbox';
		$sql = "order_number = '" . $this->db->escape((string)$order_number) . "', customer_internal_reference = '" . $this->db->escape($payload['customerInternalReference']) . "', service_type = '" . $this->db->escape($payload['serviceType']) . "', environment = '" . $this->db->escape($environment) . "', state = 'validating', retryable = '1', order_code = NULL, status = '', tracking_number = '', tracking_url = '', currency_code = 'EUR', quote_price = NULL, booked_price = NULL, price_difference = NULL, last_http_status = '0', creation_error = NULL, payload = '" . $this->db->escape($payload_json) . "', quote_response = NULL, validation_response = NULL, response = NULL, date_modified = NOW()";

		if (!empty($existing['eurosender_shipment_id'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET " . $sql . " WHERE eurosender_shipment_id = '" . (int)$existing['eurosender_shipment_id'] . "'");
			return;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "eurosender_shipment` SET order_id = '" . (int)$order['order_id'] . "', " . $sql . ", date_added = NOW()");
	}

	protected function recordQuote($shipment, $response, $money) {
		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET quote_price = " . $this->sqlDecimal($money['amount']) . ", currency_code = '" . $this->db->escape($money['currency']) . "', quote_response = '" . $this->db->escape($this->encodeJson($response)) . "', date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function recordValidationSuccess($shipment, $response) {
		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET validation_response = '" . $this->db->escape($this->encodeJson($response)) . "', creation_error = NULL, last_http_status = '0', date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function recordCreating($shipment) {
		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET state = 'creating', retryable = '0', creation_attempted_at = NOW(), creation_error = NULL, last_http_status = '0', date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function recordPreCreateFailure($shipment, $exception) {
		$this->recordFailure($shipment, $exception, 'error', true);
	}

	protected function recordCreateFailure($shipment, $exception, $unknown) {
		$this->recordFailure($shipment, $exception, $unknown ? 'unknown' : 'error', !$unknown);
	}

	protected function recordFailure($shipment, $exception, $state, $retryable) {
		$http_status = $exception instanceof ApiException ? $exception->getHttpStatus() : 0;
		$response = $exception instanceof ApiException ? $exception->getResponseBody() : '';
		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET state = '" . $this->db->escape($state) . "', retryable = '" . ($retryable ? '1' : '0') . "', last_http_status = '" . (int)$http_status . "', creation_error = '" . $this->db->escape($exception->getMessage()) . "', response = " . $this->sqlNullableText($response) . ", date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function recordCreated($shipment, $order_code, $status, $tracking_number, $tracking_url, $response, $booked_money) {
		$quote_price = isset($shipment['quote_price']) && $shipment['quote_price'] !== null && $shipment['quote_price'] !== '' ? (float)$shipment['quote_price'] : null;
		$booked_price = $booked_money['amount'];
		$difference = ($quote_price !== null && $booked_price !== null) ? $booked_price - $quote_price : null;
		$currency = $booked_money['currency'] !== '' ? $booked_money['currency'] : (!empty($shipment['currency_code']) ? $shipment['currency_code'] : 'EUR');

		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET state = 'created', retryable = '0', order_code = '" . $this->db->escape($order_code) . "', status = '" . $this->db->escape($status !== '' ? $status : 'created') . "', tracking_number = '" . $this->db->escape($tracking_number) . "', tracking_url = '" . $this->db->escape($tracking_url) . "', currency_code = '" . $this->db->escape($currency) . "', booked_price = " . $this->sqlDecimal($booked_price) . ", price_difference = " . $this->sqlDecimal($difference) . ", last_http_status = '0', created_at = NOW(), creation_error = NULL, response = '" . $this->db->escape($this->encodeJson($response)) . "', date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function recordTrackingSuccess($shipment, $status, $tracking_number, $tracking_url, $order_response, $tracking_response, $booked_money) {
		$quote_price = isset($shipment['quote_price']) && $shipment['quote_price'] !== null && $shipment['quote_price'] !== '' ? (float)$shipment['quote_price'] : null;
		$booked_price = $booked_money['amount'] !== null ? $booked_money['amount'] : (isset($shipment['booked_price']) && $shipment['booked_price'] !== '' ? (float)$shipment['booked_price'] : null);
		$difference = ($quote_price !== null && $booked_price !== null) ? $booked_price - $quote_price : null;
		$currency = $booked_money['currency'] !== '' ? $booked_money['currency'] : (!empty($shipment['currency_code']) ? $shipment['currency_code'] : 'EUR');
		$combined = array('order' => $order_response, 'tracking' => $tracking_response);
		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET status = '" . $this->db->escape($status !== '' ? $status : $shipment['status']) . "', tracking_number = '" . $this->db->escape($tracking_number !== '' ? $tracking_number : $shipment['tracking_number']) . "', tracking_url = '" . $this->db->escape($tracking_url !== '' ? $tracking_url : $shipment['tracking_url']) . "', currency_code = '" . $this->db->escape($currency) . "', booked_price = " . $this->sqlDecimal($booked_price) . ", price_difference = " . $this->sqlDecimal($difference) . ", tracking_checked_at = NOW(), tracking_error = NULL, tracking_response = '" . $this->db->escape($this->encodeJson($combined)) . "', date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function recordTrackingError($shipment, $error) {
		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET tracking_checked_at = NOW(), tracking_error = '" . $this->db->escape((string)$error) . "', date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function recordLabelSuccess($shipment) {
		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET label_downloaded_at = NOW(), label_error = NULL, date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function recordLabelError($shipment, $error) {
		$this->db->query("UPDATE `" . DB_PREFIX . "eurosender_shipment` SET label_error = '" . $this->db->escape((string)$error) . "', date_modified = NOW() WHERE eurosender_shipment_id = '" . (int)$shipment['eurosender_shipment_id'] . "'");
	}

	protected function withOrderLock($order_id, $callback) {
		$lock_name = 'dryzen_eurosender_order_' . (int)$order_id;
		$query = $this->db->query("SELECT GET_LOCK('" . $this->db->escape($lock_name) . "', 10) AS acquired");

		if (!$query->num_rows || (int)$query->row['acquired'] !== 1) {
			throw new \RuntimeException('Eurosender obrada narudžbe je već u tijeku.');
		}

		try {
			return call_user_func($callback);
		} finally {
			$this->db->query("SELECT RELEASE_LOCK('" . $this->db->escape($lock_name) . "')");
		}
	}

	protected function extractSelectedQuoteMoney($response) {
		$amount = $this->arrayPath($response, array('order', 'totalPrice', 'original', 'gross'));
		$currency = $this->arrayPath($response, array('order', 'totalPrice', 'original', 'currencyCode'));

		return array(
			'amount'   => is_numeric($amount) ? (float)$amount : null,
			'currency' => $currency !== null && $currency !== '' ? strtoupper((string)$currency) : ''
		);
	}

	protected function extractBookedMoney($response) {
		$candidates = array(
			$this->arrayPath($response, array('totalPrice')),
			$this->arrayPath($response, array('order', 'totalPrice')),
			$this->arrayPath($response, array('price')),
			$this->arrayPath($response, array('order', 'price'))
		);

		foreach ($candidates as $candidate) {
			$money = $this->normalizeMoney($candidate);

			if ($money['amount'] !== null) {
				return $money;
			}
		}

		return array('amount' => null, 'currency' => '');
	}

	protected function extractOrderCode($response) {
		foreach (array(
			array('orderCode'),
			array('order', 'orderCode'),
			array('data', 'orderCode')
		) as $path) {
			$value = $this->arrayPath($response, $path);

			if (is_scalar($value) && trim((string)$value) !== '') {
				return trim((string)$value);
			}
		}

		return '';
	}

	protected function extractStatus($response) {
		foreach (array('currentStatus', 'status', 'orderStatus', 'shipmentStatus', 'deliveryStatus', 'state') as $key) {
			$value = $this->findScalarByKey($response, $key);

			if ($value !== '') {
				return $value;
			}
		}

		return '';
	}

	protected function extractTrackingNumber($response) {
		foreach (array(
			array('parcels', 'packages', 0, 'tracking', 'number'),
			array('packages', 0, 'tracking', 'number'),
			array('parcels', 0, 'tracking', 'number')
		) as $path) {
			$value = $this->arrayPath($response, $path);

			if (is_scalar($value) && trim((string)$value) !== '') {
				return trim((string)$value);
			}
		}

		foreach (array('trackingNumber', 'trackingCode', 'trackingId', 'awb', 'parcelNumber') as $key) {
			$value = $this->findScalarByKey($response, $key);

			if ($value !== '') {
				return $value;
			}
		}

		return '';
	}

	protected function extractTrackingUrl($response) {
		foreach (array(
			array('parcels', 'packages', 0, 'tracking', 'url'),
			array('packages', 0, 'tracking', 'url'),
			array('parcels', 0, 'tracking', 'url')
		) as $path) {
			$value = $this->arrayPath($response, $path);

			if (is_scalar($value) && $this->isSafeHttpUrl((string)$value)) {
				return trim((string)$value);
			}
		}

		foreach (array('trackingUrl', 'trackingURL', 'trackingLink') as $key) {
			$value = $this->findScalarByKey($response, $key);

			if ($this->isSafeHttpUrl($value)) {
				return $value;
			}
		}

		return '';
	}

	protected function extractPdf($response) {
		if (is_string($response)) {
			if (strncmp($response, '%PDF-', 5) === 0) {
				return $response;
			}

			return $this->decodePdfCandidate($response);
		}

		if (!is_array($response)) {
			return '';
		}

		foreach (array('content', 'data', 'label', 'base64', 'file', 'document') as $key) {
			if (isset($response[$key]) && is_string($response[$key])) {
				$pdf = $this->decodePdfCandidate($response[$key]);

				if ($pdf !== '') {
					return $pdf;
				}
			}
		}

		foreach ($response as $value) {
			if (is_array($value)) {
				$pdf = $this->extractPdf($value);

				if ($pdf !== '') {
					return $pdf;
				}
			}
		}

		return '';
	}

	protected function normalizePhone($phone, $country_code) {
		$country_code = strtoupper(trim((string)$country_code));
		$phone = trim((string)$phone);

		if ($phone === '') {
			throw new \RuntimeException('Nedostaje kontakt telefon.');
		}

		$phone = preg_replace('/[^0-9+]/', '', $phone);

		if (strpos($phone, '00') === 0) {
			$phone = '+' . substr($phone, 2);
		}

		if (strpos($phone, '+') !== 0) {
			$dialing_codes = $this->countryDialingCodes();

			if (!isset($dialing_codes[$country_code])) {
				throw new \RuntimeException('Telefon mora biti u E.164 formatu jer pozivni broj zemlje nije poznat.');
			}

			$digits = preg_replace('/\D/', '', $phone);
			$dialing_code = $dialing_codes[$country_code];

			if (strpos($digits, $dialing_code) === 0) {
				$phone = '+' . $digits;
			} elseif ($country_code === 'IT' && strpos($digits, '0') === 0) {
				$phone = '+' . $dialing_code . $digits;
			} else {
				$phone = '+' . $dialing_code . ltrim($digits, '0');
			}
		}

		if (!preg_match('/^\+[1-9][0-9]{7,14}$/', $phone)) {
			throw new \RuntimeException('Kontakt telefon nije ispravan E.164 broj: ' . $phone);
		}

		return $phone;
	}

	private function resolveClient() {
		if ($this->registry->has('eurosender_client')) {
			return $this->registry->get('eurosender_client');
		}

		if (!class_exists(__NAMESPACE__ . '\\Client', false)) {
			require_once(__DIR__ . '/client.php');
		}

		return new Client($this->registry);
	}

	protected function getKilogramWeightClass() {
		$language_id = (int)$this->config->get('config_language_id');
		$query = $this->db->query("SELECT wc.weight_class_id, wc.value FROM `" . DB_PREFIX . "weight_class` wc LEFT JOIN `" . DB_PREFIX . "weight_class_description` wcd ON (wcd.weight_class_id = wc.weight_class_id) WHERE LOWER(REPLACE(wcd.unit, ' ', '')) IN ('kg', 'kgs') OR LOWER(wcd.title) LIKE 'kilogram%' ORDER BY (wcd.language_id = '" . $language_id . "') DESC LIMIT 1");

		if ($query->num_rows && (float)$query->row['value'] > 0) {
			return array('id' => (int)$query->row['weight_class_id'], 'value' => (float)$query->row['value']);
		}

		$weight_class_id = (int)$this->config->get('config_weight_class_id');
		$fallback = $this->db->query("SELECT weight_class_id, value FROM `" . DB_PREFIX . "weight_class` WHERE weight_class_id = '" . $weight_class_id . "' LIMIT 1");

		return array(
			'id'    => $weight_class_id,
			'value' => $fallback->num_rows && (float)$fallback->row['value'] > 0 ? (float)$fallback->row['value'] : 1.0
		);
	}

	protected function getOrderCountryCode($order) {
		$country_id = !empty($order['shipping_country_id']) ? (int)$order['shipping_country_id'] : 0;

		if ($country_id > 0) {
			$query = $this->db->query("SELECT iso_code_2 FROM `" . DB_PREFIX . "country` WHERE country_id = '" . $country_id . "' LIMIT 1");

			if ($query->num_rows && !empty($query->row['iso_code_2'])) {
				return $this->requireCountryCode($query->row['iso_code_2'], 'zemlja primatelja');
			}
		}

		throw new \RuntimeException('Nedostaje ISO oznaka zemlje primatelja.');
	}

	protected function getOrderZoneCode($order) {
		$zone_id = !empty($order['shipping_zone_id']) ? (int)$order['shipping_zone_id'] : 0;

		if ($zone_id < 1) {
			return '';
		}

		$query = $this->db->query("SELECT code FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . $zone_id . "' LIMIT 1");

		return $query->num_rows && !empty($query->row['code']) ? trim((string)$query->row['code']) : '';
	}

	private function assertValidationPassed($response) {
		if (isset($response['valid']) && !$response['valid']) {
			throw new \RuntimeException('Eurosender validacija narudžbe nije prošla: ' . $this->validationErrors($response));
		}

		if (isset($response['isValid']) && !$response['isValid']) {
			throw new \RuntimeException('Eurosender validacija narudžbe nije prošla: ' . $this->validationErrors($response));
		}

		if (!empty($response['errors']) || !empty($response['validationErrors'])) {
			throw new \RuntimeException('Eurosender validacija narudžbe nije prošla: ' . $this->validationErrors($response));
		}
	}

	private function validationErrors($response) {
		$errors = !empty($response['errors']) ? $response['errors'] : (isset($response['validationErrors']) ? $response['validationErrors'] : $response);
		$encoded = json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		return $encoded !== false ? $encoded : 'nepoznata pogreška';
	}

	private function assertSafeCreationState($shipment) {
		if (!empty($shipment['state']) && in_array($shipment['state'], array('creating', 'unknown'), true)) {
			throw new \RuntimeException('Prethodni pokušaj kreiranja Eurosender pošiljke nema siguran ishod. Provjerite Eurosender račun prije bilo kakvog ponavljanja kako biste izbjegli dvostruku naplatu.');
		}
	}

	private function storePreview($order_id, $payload, $quote_money, $checkout_shipping_price) {
		$session = $this->getSession();
		$previews = isset($session->data['eurosender_booking_previews']) && is_array($session->data['eurosender_booking_previews'])
			? $session->data['eurosender_booking_previews']
			: array();
		$now = time();

		foreach ($previews as $key => $preview) {
			if (!is_array($preview) || empty($preview['expires_at']) || (int)$preview['expires_at'] <= $now) {
				unset($previews[$key]);
			}
		}

		$connection = $this->client->getConnectionInfo();
		$token = bin2hex(random_bytes(32));
		$previews[(int)$order_id] = array(
			'token'                   => $token,
			'payload_hash'            => hash('sha256', $this->encodeJson($payload)),
			'quote_price'             => (float)$quote_money['amount'],
			'quote_price_cents'       => $this->moneyToCents($quote_money['amount']),
			'currency'                => strtoupper((string)$quote_money['currency']),
			'checkout_shipping_cents' => $checkout_shipping_price === null ? null : $this->moneyToCents($checkout_shipping_price),
			'environment'             => !empty($connection['environment']) ? strtolower((string)$connection['environment']) : 'sandbox',
			'admin_user_id'           => $this->getAdminUserId($session),
			'expires_at'              => $now + 600
		);
		$session->data['eurosender_booking_previews'] = $previews;

		return $token;
	}

	private function consumeAndGetPreview($order_id, $confirmation_token, $payload, $checkout_shipping_price) {
		$session = $this->getSession();
		$previews = isset($session->data['eurosender_booking_previews']) && is_array($session->data['eurosender_booking_previews'])
			? $session->data['eurosender_booking_previews']
			: array();
		$preview = isset($previews[(int)$order_id]) && is_array($previews[(int)$order_id]) ? $previews[(int)$order_id] : array();
		$token = trim((string)$confirmation_token);

		if (!$preview || empty($preview['token']) || $token === '' || !hash_equals((string)$preview['token'], $token)) {
			throw new \RuntimeException('Eurosender potvrda cijene nedostaje ili nije valjana. Ponovno provjerite cijenu prije rezervacije.');
		}

		// A valid token is single-use. Consume it before any further quote,
		// validation or paid request so retries always require a new explicit
		// price confirmation.
		unset($previews[(int)$order_id]);
		$session->data['eurosender_booking_previews'] = $previews;

		if (empty($preview['expires_at']) || (int)$preview['expires_at'] <= time()) {
			throw new \RuntimeException('Eurosender potvrda cijene je istekla. Ponovno provjerite cijenu prije rezervacije.');
		}

		$payload_hash = hash('sha256', $this->encodeJson($payload));
		$connection = $this->client->getConnectionInfo();
		$environment = !empty($connection['environment']) ? strtolower((string)$connection['environment']) : 'sandbox';

		if (empty($preview['payload_hash']) || !hash_equals((string)$preview['payload_hash'], $payload_hash) || (string)$preview['environment'] !== $environment) {
			throw new \RuntimeException('Podaci narudžbe ili Eurosender okruženje promijenili su se nakon provjere cijene. Ponovite provjeru.');
		}

		$current_checkout_cents = $checkout_shipping_price === null ? null : $this->moneyToCents($checkout_shipping_price);

		if (!array_key_exists('checkout_shipping_cents', $preview) || $preview['checkout_shipping_cents'] !== $current_checkout_cents) {
			throw new \RuntimeException('Iznos dostave na narudžbi promijenio se nakon provjere cijene. Ponovite provjeru.');
		}

		if (empty($preview['admin_user_id']) || (int)$preview['admin_user_id'] !== $this->getAdminUserId($session)) {
			throw new \RuntimeException('Eurosender potvrdu cijene mora dovršiti isti administrator koji ju je zatražio. Ponovite provjeru.');
		}

		if (!isset($preview['quote_price']) || !is_numeric($preview['quote_price']) || !isset($preview['quote_price_cents']) || !is_int($preview['quote_price_cents']) || strtoupper((string)$preview['currency']) !== 'EUR') {
			throw new \RuntimeException('Spremljena Eurosender potvrda cijene nije ispravna. Ponovite provjeru.');
		}

		return $preview;
	}

	private function getSession() {
		if (!$this->registry->has('session')) {
			throw new \RuntimeException('Eurosender provjera cijene zahtijeva aktivnu administratorsku sesiju.');
		}

		$session = $this->registry->get('session');

		if (!is_object($session) || !isset($session->data) || !is_array($session->data)) {
			throw new \RuntimeException('Eurosender administratorska sesija nije dostupna.');
		}

		return $session;
	}

	private function getAdminUserId($session) {
		$user_id = 0;

		if ($this->registry->has('user')) {
			$user = $this->registry->get('user');

			if (is_object($user) && method_exists($user, 'getId')) {
				$user_id = (int)$user->getId();
			}
		}

		if ($user_id < 1 && isset($session->data['user_id'])) {
			$user_id = (int)$session->data['user_id'];
		}

		if ($user_id < 1) {
			throw new \RuntimeException('Eurosender potvrda cijene zahtijeva prijavljenog administratora.');
		}

		return $user_id;
	}

	private function moneyToCents($amount) {
		return (int)round((float)$amount * 100, 0, PHP_ROUND_HALF_UP);
	}

	private function isAmbiguousCreateFailure($exception) {
		if (!$exception instanceof ApiException) {
			return true;
		}

		if ($exception->isTransportError() || $exception->isAmbiguousResponse()) {
			return true;
		}

		$status = $exception->getHttpStatus();

		return !in_array($status, array(400, 422), true);
	}

	private function normalizeMoney($candidate) {
		if (is_numeric($candidate)) {
			return array('amount' => (float)$candidate, 'currency' => '');
		}

		if (!is_array($candidate)) {
			return array('amount' => null, 'currency' => '');
		}

		foreach (array('gross', 'amount', 'value', 'total', 'original') as $key) {
			if (!array_key_exists($key, $candidate)) {
				continue;
			}

			if (is_numeric($candidate[$key])) {
				$currency = isset($candidate['currencyCode']) ? $candidate['currencyCode'] : (isset($candidate['currency']) ? $candidate['currency'] : '');
				return array('amount' => (float)$candidate[$key], 'currency' => strtoupper((string)$currency));
			}

			if (is_array($candidate[$key])) {
				$nested = $this->normalizeMoney($candidate[$key]);

				if ($nested['amount'] !== null) {
					if ($nested['currency'] === '' && !empty($candidate['currencyCode'])) {
						$nested['currency'] = strtoupper((string)$candidate['currencyCode']);
					}

					return $nested;
				}
			}
		}

		return array('amount' => null, 'currency' => '');
	}

	private function decodePdfCandidate($candidate) {
		$candidate = trim((string)$candidate);

		if (stripos($candidate, 'data:application/pdf;base64,') === 0) {
			$candidate = substr($candidate, strpos($candidate, ',') + 1);
		}

		$decoded = base64_decode($candidate, true);

		return is_string($decoded) && strncmp($decoded, '%PDF-', 5) === 0 ? $decoded : '';
	}

	private function findScalarByKey($value, $wanted_key) {
		if (!is_array($value)) {
			return '';
		}

		foreach ($value as $key => $child) {
			if (strcasecmp((string)$key, (string)$wanted_key) === 0 && is_scalar($child) && trim((string)$child) !== '') {
				return trim((string)$child);
			}
		}

		foreach ($value as $child) {
			if (is_array($child)) {
				$found = $this->findScalarByKey($child, $wanted_key);

				if ($found !== '') {
					return $found;
				}
			}
		}

		return '';
	}

	private function arrayPath($array, $path) {
		$value = $array;

		foreach ($path as $key) {
			if (!is_array($value) || !array_key_exists($key, $value)) {
				return null;
			}

			$value = $value[$key];
		}

		return $value;
	}

	private function encodeJson($value) {
		$json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		if ($json === false) {
			throw new \RuntimeException('Nije moguće serijalizirati Eurosender podatke.');
		}

		return $json;
	}

	private function sqlDecimal($value) {
		return $value === null || $value === '' ? 'NULL' : "'" . number_format((float)$value, 4, '.', '') . "'";
	}

	private function sqlNullableText($value) {
		return $value === '' ? 'NULL' : "'" . $this->db->escape((string)$value) . "'";
	}

	private function getConfig($key, $default = '') {
		$value = $this->config->get('shipping_eurosender_' . $key);

		return ($value !== null && $value !== '') ? $value : $default;
	}

	private function requiredConfig($key, $label) {
		$value = trim((string)$this->getConfig($key, ''));

		if ($value === '') {
			throw new \RuntimeException('Nedostaje Eurosender postavka: ' . $label . '.');
		}

		return $value;
	}

	private function requiredOrderValue($order, $key, $label) {
		$value = isset($order[$key]) ? trim((string)$order[$key]) : '';

		if ($value === '') {
			throw new \RuntimeException('Nedostaje ' . $label . '.');
		}

		return $value;
	}

	private function requireCountryCode($country_code, $label) {
		$country_code = strtoupper(trim((string)$country_code));

		if (!preg_match('/^[A-Z]{2}$/', $country_code)) {
			throw new \RuntimeException('Nedostaje ispravna ISO-2 oznaka za: ' . $label . '.');
		}

		return $country_code;
	}

	private function requireEmail($email, $label) {
		$email = trim((string)$email);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new \RuntimeException('Nedostaje ispravan ' . $label . '.');
		}

		return $email;
	}

	private function joinAddressLines($address_1, $address_2) {
		$parts = array_filter(array(trim((string)$address_1), trim((string)$address_2)), 'strlen');

		return implode(', ', $parts);
	}

	private function positiveDecimalConfig($key, $default) {
		$value = (float)$this->getConfig($key, $default);

		return $value > 0 ? $value : (float)$default;
	}

	private function positiveIntegerDimension($key, $default) {
		$value = (float)$this->getConfig($key, $default);

		return $value > 0 ? max(1, (int)ceil($value)) : (int)$default;
	}

	private function nonNegativeDecimalConfig($key, $default) {
		$value = (float)$this->getConfig($key, $default);

		return $value >= 0 ? $value : (float)$default;
	}

	private function countryDialingCodes() {
		return array(
			'AT' => '43', 'BE' => '32', 'BG' => '359', 'CH' => '41', 'CY' => '357',
			'CZ' => '420', 'DE' => '49', 'DK' => '45', 'EE' => '372', 'ES' => '34',
			'FI' => '358', 'FR' => '33', 'GB' => '44', 'GR' => '30', 'HR' => '385',
			'HU' => '36', 'IE' => '353', 'IS' => '354', 'IT' => '39', 'LT' => '370',
			'LU' => '352', 'LV' => '371', 'MT' => '356', 'NL' => '31', 'NO' => '47',
			'PL' => '48', 'PT' => '351', 'RO' => '40', 'SE' => '46', 'SI' => '386',
			'SK' => '421', 'US' => '1', 'CA' => '1'
		);
	}

	private function isSafeHttpUrl($url) {
		$url = trim((string)$url);
		$scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));

		return filter_var($url, FILTER_VALIDATE_URL) !== false && in_array($scheme, array('http', 'https'), true);
	}

	private function shipmentTableExists() {
		if ($this->schema_exists !== null) {
			return $this->schema_exists;
		}

		$query = $this->db->query("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '" . $this->db->escape(DB_PREFIX . "eurosender_shipment") . "' LIMIT 1");
		$this->schema_exists = (bool)$query->num_rows;

		return $this->schema_exists;
	}

	private function assertShipmentEnvironment($shipment) {
		if (!$shipment || empty($shipment['environment'])) {
			return;
		}

		$connection = $this->client->getConnectionInfo();
		$current = !empty($connection['environment']) ? strtolower((string)$connection['environment']) : 'sandbox';
		$stored = strtolower((string)$shipment['environment']);

		if ($stored !== $current) {
			throw new \RuntimeException('Eurosender pošiljka pripada okruženju ' . $stored . ', a modul je trenutačno postavljen na ' . $current . '. Promijenite okruženje prije ove radnje.');
		}
	}
}

// OpenCart 3 constructs a class name directly from the library route.
if (!class_exists(__NAMESPACE__ . '\\shipment_manager', false)) {
	class_alias(__NAMESPACE__ . '\\ShipmentManager', __NAMESPACE__ . '\\shipment_manager');
}
