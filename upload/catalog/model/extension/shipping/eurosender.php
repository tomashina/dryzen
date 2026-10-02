<?php
class ModelExtensionShippingEurosender extends Model {
	private $kilogram_class_id;
	private $shipment_table_exists;

	public function getQuote($address) {
		$this->load->language('extension/shipping/eurosender');

		if (!$this->isAvailableForAddress($address)) {
			return array();
		}

		$quotes = array();
		$quote_response_received = false;

		try {
			$client = $this->getClient();

			if ($client && $client->isConfigured()) {
				$payload = $this->buildQuotePayload($address);
				$cache_key = $this->buildQuoteCacheKey($payload);
				$response = $this->getCachedQuoteResponse($cache_key);

				if ($response === null) {
					$response = $client->quote($payload);
					$quote_response_received = true;
					$quotes = $this->parseApiQuotes($response);

					if ($quotes) {
						$this->cacheQuoteResponse($cache_key, $response);
					}
				} else {
					$quote_response_received = true;
					$quotes = $this->parseApiQuotes($response);
				}
			}
		} catch (\Throwable $exception) {
			if ($this->isDefiniteQuoteRejection($exception)) {
				return array();
			}

			if (!$this->isTransientQuoteFailure($exception)) {
				if ($this->registry->has('log')) {
					$this->log->write('Eurosender quote disabled after local failure: ' . $exception->getMessage());
				}

				return array();
			}

			// A carrier outage must not interrupt checkout. A configured fallback
			// rate is used below; otherwise this method simply stays unavailable.
			$quotes = array();
		}

		if (!$quotes) {
			if ($quote_response_received) {
				return array();
			}

			$quotes = $this->getFallbackQuote();
		}

		if (!$quotes) {
			return array();
		}

		return array(
			'code'       => 'eurosender',
			'title'      => $this->language->get('text_title'),
			'quote'      => $quotes,
			'sort_order' => (int)$this->config->get('shipping_eurosender_sort_order'),
			'error'      => false
		);
	}

	public function getShipmentByOrderId($order_id) {
		if (!$this->shipmentTableExists()) {
			return array();
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "eurosender_shipment` WHERE order_id = '" . (int)$order_id . "' LIMIT 1");

		return $query->num_rows ? $query->row : array();
	}

	public function getTrackingUrl($shipment) {
		if (!is_array($shipment)) {
			return '';
		}

		$url = !empty($shipment['tracking_url']) ? trim((string)$shipment['tracking_url']) : '';

		return $this->isSafeHttpUrl($url) ? $url : '';
	}

	public function getStatusLabel($status) {
		$this->load->language('extension/shipping/eurosender', 'eurosender_tracking');
		$language = $this->language->get('eurosender_tracking');
		$key = strtolower(trim((string)$status));
		$key = preg_replace('/[^a-z0-9]+/', '_', $key);
		$key = trim($key, '_');
		$map = array(
			'inforeceived'                  => 'status_info_received',
			'info_received'                 => 'status_info_received',
			'intransit'                     => 'status_in_transit',
			'in_transit'                    => 'status_in_transit',
			'outfordelivery'                => 'status_out_for_delivery',
			'out_for_delivery'              => 'status_out_for_delivery',
			'attemptfail'                   => 'status_attempt_failed',
			'attempt_fail'                  => 'status_attempt_failed',
			'delivered'                     => 'status_delivered',
			'availableforpickup'            => 'status_available_for_pickup',
			'available_for_pickup'          => 'status_available_for_pickup',
			'exception'                     => 'status_exception',
			'expired'                       => 'status_expired',
			'pending'                       => 'status_pending',
			'order_received'                => 'status_order_received',
			'deferred_payment'              => 'status_pending',
			'confirmed'                     => 'status_confirmed',
			'tracking'                      => 'status_in_transit',
			'awaiting_payment'              => 'status_awaiting_payment',
			'awaiting_customs_documentation'=> 'status_awaiting_customs',
			'awaiting_additional_payment'   => 'status_awaiting_payment',
			'label_error'                   => 'status_label_error',
			'canceled'                      => 'status_cancelled',
			'cancelled'                     => 'status_cancelled'
		);

		if (isset($map[$key])) {
			return $language->get($map[$key]);
		}

		return $status !== '' ? (string)$status : $language->get('status_unavailable');
	}

	private function isAvailableForAddress($address) {
		if (!$this->config->get('shipping_eurosender_status')) {
			return false;
		}

		$geo_zone_id = (int)$this->config->get('shipping_eurosender_geo_zone_id');

		if (!$geo_zone_id) {
			return true;
		}

		$country_id = isset($address['country_id']) ? (int)$address['country_id'] : 0;
		$zone_id = isset($address['zone_id']) ? (int)$address['zone_id'] : 0;
		$query = $this->db->query("SELECT zone_to_geo_zone_id FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . $geo_zone_id . "' AND country_id = '" . $country_id . "' AND (zone_id = '" . $zone_id . "' OR zone_id = '0') LIMIT 1");

		return (bool)$query->num_rows;
	}

	private function buildQuotePayload($address) {
		$weight = $this->calculateWeight();
		$value = $this->calculateParcelValue();
		$store_currency = (string)$this->config->get('config_currency');

		if ($store_currency !== '' && $store_currency !== 'EUR') {
			$value = $this->currency->convert($value, $store_currency, 'EUR');
		}

		$package = array(
			'parcelId' => 'CART-1',
			'quantity' => 1,
			'width'    => max(1, (int)ceil($this->positiveConfigNumber('width', 15))),
			'height'   => max(1, (int)ceil($this->positiveConfigNumber('height', 10))),
			'length'   => max(1, (int)ceil($this->positiveConfigNumber('length', 20))),
			'weight'   => $weight,
			'content'  => $this->configValue('content', 'cosmetics'),
			'value'    => max(1, (int)ceil($value))
		);

		return array(
			'shipment' => array(
				'pickupAddress'  => $this->buildPickupAddress(),
				'deliveryAddress' => $this->buildDeliveryAddress($address)
			),
			'parcels' => array(
				'packages' => array($package)
			),
			'paymentMethod' => $this->paymentMethod(),
			'currencyCode'  => 'EUR'
		);
	}

	private function calculateParcelValue() {
		$value = 0.0;

		foreach ($this->cart->getProducts() as $product) {
			$net_total = isset($product['total'])
				? (float)$product['total']
				: ((isset($product['price']) ? (float)$product['price'] : 0.0) * (isset($product['quantity']) ? max(1, (int)$product['quantity']) : 1));
			$tax_class_id = isset($product['tax_class_id']) ? (int)$product['tax_class_id'] : 0;
			$value += $tax_class_id ? (float)$this->tax->calculate($net_total, $tax_class_id, true) : $net_total;
		}

		return $value > 0 ? $value : (float)$this->cart->getSubTotal();
	}

	private function buildPickupAddress() {
		$street = $this->configValue('origin_address_1');
		$address_2 = $this->configValue('origin_address_2');

		if ($address_2 !== '') {
			$street .= ($street !== '' ? ', ' : '') . $address_2;
		}

		return array(
			'country' => strtoupper($this->configValue('origin_country_code')),
			'zip'     => $this->configValue('origin_postcode'),
			'city'    => $this->configValue('origin_city'),
			'street'  => $street
		);
	}

	private function buildDeliveryAddress($address) {
		$country_code = isset($address['iso_code_2']) ? strtoupper(trim((string)$address['iso_code_2'])) : '';

		if ($country_code === '' && !empty($address['country_id'])) {
			$query = $this->db->query("SELECT iso_code_2 FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$address['country_id'] . "' LIMIT 1");

			if ($query->num_rows) {
				$country_code = strtoupper((string)$query->row['iso_code_2']);
			}
		}

		$street = isset($address['address_1']) ? trim((string)$address['address_1']) : '';
		$address_2 = isset($address['address_2']) ? trim((string)$address['address_2']) : '';

		if ($address_2 !== '') {
			$street .= ($street !== '' ? ', ' : '') . $address_2;
		}

		$delivery = array(
			'country' => $country_code,
			'zip'     => isset($address['postcode']) ? trim((string)$address['postcode']) : '',
			'city'    => isset($address['city']) ? trim((string)$address['city']) : '',
			'street'  => $street
		);

		if (!empty($address['zone'])) {
			$delivery['region'] = trim((string)$address['zone']);
		}

		if (in_array($country_code, array('IT', 'US', 'CA'), true)) {
			$region_code = isset($address['zone_code']) ? trim((string)$address['zone_code']) : '';

			if ($region_code === '' && !empty($address['zone_id'])) {
				$query = $this->db->query("SELECT code FROM " . DB_PREFIX . "zone WHERE zone_id = '" . (int)$address['zone_id'] . "' LIMIT 1");
				$region_code = $query->num_rows && !empty($query->row['code']) ? trim((string)$query->row['code']) : '';
			}

			if ($region_code !== '') {
				$delivery['regionCode'] = $region_code;
			}
		}

		return $delivery;
	}

	private function calculateWeight() {
		$fallback_item_weight = $this->nonNegativeConfigNumber('fallback_item_weight', 0.10);
		$packaging_weight = $this->nonNegativeConfigNumber('packaging_weight', 0.20);
		$minimum_weight = $this->positiveConfigNumber('minimum_weight', 0.50);
		$weight = 0.0;

		foreach ($this->cart->getProducts() as $product) {
			$product_weight = isset($product['weight']) ? (float)$product['weight'] : 0.0;

			if ($product_weight > 0) {
				$weight += $this->convertWeightToKilograms($product_weight, isset($product['weight_class_id']) ? (int)$product['weight_class_id'] : 0);
			} else {
				$quantity = isset($product['quantity']) ? max(1, (int)$product['quantity']) : 1;
				$weight += $fallback_item_weight * $quantity;
			}
		}

		$weight += $packaging_weight;
		$weight = max($minimum_weight, $weight);

		return round($weight, 3);
	}

	private function convertWeightToKilograms($weight, $weight_class_id) {
		$weight = (float)$weight;

		if ($weight <= 0) {
			return 0.0;
		}

		$kilogram_class_id = $this->getKilogramClassId();

		if ($kilogram_class_id && $weight_class_id) {
			return (float)$this->weight->convert($weight, $weight_class_id, $kilogram_class_id);
		}

		$unit = strtolower(trim((string)$this->weight->getUnit($weight_class_id)));

		switch ($unit) {
			case 'g':
			case 'gram':
			case 'grams':
				return $weight / 1000;
			case 'lb':
			case 'lbs':
				return $weight * 0.45359237;
			case 'oz':
				return $weight * 0.0283495231;
			default:
				return $weight;
		}
	}

	private function getKilogramClassId() {
		if ($this->kilogram_class_id !== null) {
			return $this->kilogram_class_id;
		}

		$language_id = (int)$this->config->get('config_language_id');
		$query = $this->db->query("SELECT wc.weight_class_id FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . $language_id . "' AND LOWER(wcd.unit) IN ('kg', 'kgs') ORDER BY ABS(wc.value - 1) ASC LIMIT 1");
		$this->kilogram_class_id = $query->num_rows ? (int)$query->row['weight_class_id'] : 0;

		return $this->kilogram_class_id;
	}

	private function parseApiQuotes($response) {
		$quotes = array();
		$allowed = $this->allowedServiceTypes();
		$services = isset($response['options']['serviceTypes']) && is_array($response['options']['serviceTypes']) ? $response['options']['serviceTypes'] : array();

		foreach ($services as $service) {
			$name = isset($service['name']) ? strtolower(trim((string)$service['name'])) : '';

			if (!in_array($name, $allowed, true)) {
				continue;
			}

			$gross_eur = $this->extractGrossEuroPrice($service);

			if ($gross_eur === null) {
				continue;
			}

			$gross_eur = $this->applyMarkup($gross_eur);
			$quote = $this->createQuote($name, $this->serviceTitle($name, $service), $gross_eur);

			if (!isset($quotes[$name]) || $quote['cost'] < $quotes[$name]['cost']) {
				$quotes[$name] = $quote;
			}
		}

		return $quotes;
	}

	private function extractGrossEuroPrice($service) {
		$price = isset($service['price']) && is_array($service['price']) ? $service['price'] : array();

		foreach (array('original', 'converted') as $price_type) {
			$candidate = isset($price[$price_type]) && is_array($price[$price_type]) ? $price[$price_type] : array();
			$currency = isset($candidate['currencyCode']) ? strtoupper((string)$candidate['currencyCode']) : '';

			if ($currency === 'EUR' && isset($candidate['gross']) && is_numeric($candidate['gross'])) {
				$gross = (float)$candidate['gross'];
				$fees = isset($service['pickupDateFees']) && is_array($service['pickupDateFees']) ? $service['pickupDateFees'] : array();

				foreach ($fees as $fee) {
					$fee_money = isset($fee['price'][$price_type]) && is_array($fee['price'][$price_type]) ? $fee['price'][$price_type] : array();
					$fee_currency = isset($fee_money['currencyCode']) ? strtoupper((string)$fee_money['currencyCode']) : '';

					if ($fee_currency !== $currency || !isset($fee_money['gross']) || !is_numeric($fee_money['gross'])) {
						return null;
					}

					$gross += (float)$fee_money['gross'];
				}

				return max(0, $gross);
			}
		}

		return null;
	}

	private function createQuote($code, $title, $gross_eur) {
		$store_currency = (string)$this->config->get('config_currency');
		$store_currency = $store_currency !== '' ? $store_currency : 'EUR';
		$cost = $store_currency === 'EUR' ? (float)$gross_eur : (float)$this->currency->convert($gross_eur, 'EUR', $store_currency);
		$session_currency = isset($this->session->data['currency']) ? (string)$this->session->data['currency'] : $store_currency;

		return array(
			'code'         => 'eurosender.' . $code,
			'title'        => $title,
			'cost'         => $cost,
			// Eurosender prices are gross. A zero tax class prevents OpenCart from
			// adding tax to the carrier's already-taxed amount a second time.
			'tax_class_id' => 0,
			'text'         => $this->currency->format($cost, $session_currency)
		);
	}

	private function getFallbackQuote() {
		$value = $this->config->get('shipping_eurosender_fallback_rate');

		if ($value === null || trim((string)$value) === '' || !is_numeric($value) || (float)$value < 0) {
			return array();
		}

		$service_type = $this->fallbackServiceType();

		return array(
			$service_type => $this->createQuote($service_type, $this->language->get('text_fallback'), (float)$value)
		);
	}

	private function fallbackServiceType() {
		$allowed = $this->allowedServiceTypes();

		if (in_array('selection', $allowed, true)) {
			return 'selection';
		}

		return $allowed ? reset($allowed) : 'selection';
	}

	private function serviceTitle($name, $service) {
		$key = 'text_service_' . $name;
		$label = $this->language->get($key);

		if ($label === $key) {
			$label = ucwords(str_replace('_', ' ', $name));
		}

		$title = sprintf($this->language->get('text_service_title'), $label);
		$estimate = isset($service['edt']) ? trim((string)$service['edt']) : '';
		$estimate = preg_replace('/[^0-9.,\-–— ]/u', '', $estimate);

		if ($estimate !== '') {
			$title .= ' (' . sprintf($this->language->get('text_estimated_delivery'), $estimate) . ')';
		}

		return $title;
	}

	private function applyMarkup($price) {
		$price = max(0, (float)$price);
		$value = $this->nonNegativeConfigNumber('markup_value', 0);

		if ($this->configValue('markup_type', 'fixed') === 'percent') {
			$price += $price * ($value / 100);
		} else {
			$price += $value;
		}

		return round(max(0, $price), 2);
	}

	private function allowedServiceTypes() {
		$allowed = $this->config->get('shipping_eurosender_service_types');

		if (!is_array($allowed)) {
			$allowed = array('selection', 'regular_plus', 'express');
		}

		return array_values(array_intersect(array('selection', 'regular_plus', 'express'), $allowed));
	}

	private function paymentMethod() {
		$method = $this->configValue('payment_method', 'credit');

		return in_array($method, array('credit', 'deferred'), true) ? $method : 'credit';
	}

	private function configValue($key, $default = '') {
		$value = $this->config->get('shipping_eurosender_' . $key);

		return ($value !== null && $value !== '') ? trim((string)$value) : $default;
	}

	private function positiveConfigNumber($key, $default) {
		$value = $this->config->get('shipping_eurosender_' . $key);

		return is_numeric($value) && (float)$value > 0 ? (float)$value : (float)$default;
	}

	private function nonNegativeConfigNumber($key, $default) {
		$value = $this->config->get('shipping_eurosender_' . $key);

		return is_numeric($value) && (float)$value >= 0 ? (float)$value : (float)$default;
	}

	private function buildQuoteCacheKey($payload) {
		$context = array(
			'payload'       => $payload,
			'environment'   => $this->configValue('environment', 'sandbox'),
			'api_url'       => $this->selectedApiUrl(),
			'service_types' => $this->allowedServiceTypes(),
			'markup_type'   => $this->configValue('markup_type', 'fixed'),
			'markup_value'  => $this->nonNegativeConfigNumber('markup_value', 0)
		);

		return hash('sha256', json_encode($context));
	}

	private function selectedApiUrl() {
		$environment = $this->configValue('environment', 'sandbox');

		return $environment === 'production'
			? \Eurosender\Client::PRODUCTION_API_URL
			: \Eurosender\Client::SANDBOX_API_URL;
	}

	private function getCachedQuoteResponse($cache_key) {
		$cache = isset($this->session->data['eurosender_quote_cache']) && is_array($this->session->data['eurosender_quote_cache'])
			? $this->session->data['eurosender_quote_cache']
			: array();
		$now = time();

		foreach ($cache as $key => $entry) {
			if (!is_array($entry) || empty($entry['expires']) || (int)$entry['expires'] <= $now) {
				unset($cache[$key]);
			}
		}

		$this->session->data['eurosender_quote_cache'] = $cache;

		if (!isset($cache[$cache_key]['response']) || !is_array($cache[$cache_key]['response'])) {
			return null;
		}

		return $cache[$cache_key]['response'];
	}

	private function cacheQuoteResponse($cache_key, $response) {
		if (!is_array($response)) {
			return;
		}

		$cache = isset($this->session->data['eurosender_quote_cache']) && is_array($this->session->data['eurosender_quote_cache'])
			? $this->session->data['eurosender_quote_cache']
			: array();

		$cache[$cache_key] = array(
			'expires'  => time() + 600,
			'response' => $response
		);

		if (count($cache) > 10) {
			uasort($cache, function($left, $right) {
				return (int)$left['expires'] <=> (int)$right['expires'];
			});
			$cache = array_slice($cache, -10, null, true);
		}

		$this->session->data['eurosender_quote_cache'] = $cache;
	}

	private function getClient() {
		if ($this->registry->has('eurosender_client')) {
			return $this->registry->get('eurosender_client');
		}

		$file = DIR_SYSTEM . 'library/eurosender/client.php';

		if (!is_file($file)) {
			return null;
		}

		require_once($file);

		if (!class_exists('\\Eurosender\\Client')) {
			return null;
		}

		$client = new \Eurosender\Client($this->registry);
		$this->registry->set('eurosender_client', $client);

		return $client;
	}

	private function shipmentTableExists() {
		if ($this->shipment_table_exists !== null) {
			return $this->shipment_table_exists;
		}

		$query = $this->db->query("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '" . $this->db->escape(DB_PREFIX . "eurosender_shipment") . "' LIMIT 1");
		$this->shipment_table_exists = (bool)$query->num_rows;

		return $this->shipment_table_exists;
	}

	private function isSafeHttpUrl($url) {
		$url = trim((string)$url);
		$scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));

		return filter_var($url, FILTER_VALIDATE_URL) !== false && in_array($scheme, array('http', 'https'), true);
	}

	private function isDefiniteQuoteRejection($exception) {
		if (!$exception instanceof \Eurosender\ApiException) {
			return false;
		}

		$status = $exception->getHttpStatus();

		return $status >= 400 && $status < 500 && !in_array($status, array(408, 429), true);
	}

	private function isTransientQuoteFailure($exception) {
		if (!$exception instanceof \Eurosender\ApiException) {
			return false;
		}

		$status = $exception->getHttpStatus();

		return $exception->isTransportError()
			|| ($exception->isAmbiguousResponse() && $status >= 200 && $status < 300)
			|| in_array($status, array(408, 429), true)
			|| $status >= 500;
	}
}
