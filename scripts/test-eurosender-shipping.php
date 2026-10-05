<?php
// Unit-style checks only. No request in this file reaches Eurosender and no
// shipment/order is created outside the in-memory fakes below.

if (!defined('DB_PREFIX')) {
	define('DB_PREFIX', 'oc_');
}

if (!defined('OC_ENV')) {
	define('OC_ENV', array('eurosender' => array(
		'api_key' => 'legacy-test-api-key',
		'sandbox_api_key' => 'sandbox-test-api-key',
		'production_api_key' => 'production-test-api-key'
	)));
}

require_once __DIR__ . '/../upload/system/library/eurosender/client.php';
require_once __DIR__ . '/../upload/system/library/eurosender/shipment_manager.php';

class EurosenderTestRegistry {
	private $data = array();

	public function __construct($data = array()) {
		$this->data = $data;
	}

	public function has($key) {
		return array_key_exists($key, $this->data);
	}

	public function get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}
}

class EurosenderTestConfig {
	private $values;

	public function __construct($values) {
		$this->values = $values;
	}

	public function get($key) {
		return array_key_exists($key, $this->values) ? $this->values[$key] : null;
	}
}

class EurosenderTestDb {
	public function escape($value) {
		return addslashes((string)$value);
	}

	public function query($sql) {
		throw new RuntimeException('Unexpected database call in isolated test: ' . $sql);
	}
}

class EurosenderTestLog {
	public $entries = array();

	public function write($message) {
		$this->entries[] = $message;
	}
}

class RecordingEurosenderClient extends \Eurosender\Client {
	public $calls = array();

	protected function request($method, $path, $payload = null, $accept = 'application/json', $expect_json = true) {
		$this->calls[] = array(
			'method'      => $method,
			'path'        => $path,
			'payload'     => $payload,
			'accept'      => $accept,
			'expect_json' => $expect_json
		);

		return array('ok' => true);
	}
}

class FakeEurosenderClient {
	public $quote_calls = 0;
	public $validate_calls = 0;
	public $create_calls = 0;
	public $get_order_calls = 0;
	public $tracking_calls = 0;
	public $label_calls = 0;
	public $environment = 'sandbox';
	public $last_quote_payload = array();
	public $last_validation_payload = array();
	public $last_create_payload = array();
	public $quote_exception;
	public $validation_exception;
	public $create_exception;
	public $tracking_exception;
	public $quote_response;
	public $validation_response;
	public $create_response;
	public $order_response;
	public $tracking_response;
	public $label_response;

	public function __construct() {
		$this->quote_response = array(
			'order' => array(
				'totalPrice' => array('original' => array('currencyCode' => 'EUR', 'gross' => 8.75))
			),
			'options' => array(
				'serviceTypes' => array(
					array(
						'name' => 'selection',
						'price' => array('original' => array('currencyCode' => 'EUR', 'gross' => 8.75))
					)
				)
			)
		);
		$this->validation_response = array('valid' => true);
		$this->create_response = array(
			'orderCode'      => 'ES-ORDER-23',
			'status'         => 'order received',
			'trackingNumber' => 'TRACK-23',
			'trackingUrl'    => 'https://tracking.example.test/TRACK-23',
			'totalPrice'     => array('gross' => 9.25, 'currencyCode' => 'EUR')
		);
		$this->order_response = $this->create_response;
		$this->tracking_response = array(
			'status'         => 'intransit',
			'trackingNumber' => 'TRACK-23',
			'trackingUrl'    => 'https://tracking.example.test/TRACK-23'
		);
		$this->label_response = '%PDF-test-label';
	}

	public function isConfigured() {
		return true;
	}

	public function getConnectionInfo() {
		return array('configured' => true, 'environment' => $this->environment);
	}

	public function quote(array $payload) {
		$this->quote_calls++;
		$this->last_quote_payload = $payload;

		if ($this->quote_exception) {
			throw $this->quote_exception;
		}

		return $this->quote_response;
	}

	public function validateOrder(array $payload) {
		$this->validate_calls++;
		$this->last_validation_payload = $payload;

		if ($this->validation_exception) {
			throw $this->validation_exception;
		}

		return $this->validation_response;
	}

	public function createOrder(array $payload) {
		$this->create_calls++;
		$this->last_create_payload = $payload;

		if ($this->create_exception) {
			throw $this->create_exception;
		}

		return $this->create_response;
	}

	public function getOrder($order_code) {
		$this->get_order_calls++;
		return $this->order_response;
	}

	public function getTracking($order_code) {
		$this->tracking_calls++;

		if ($this->tracking_exception) {
			throw $this->tracking_exception;
		}

		return $this->tracking_response;
	}

	public function getLabels($order_code) {
		$this->label_calls++;
		return $this->label_response;
	}
}

class MemoryEurosenderShipmentManager extends \Eurosender\ShipmentManager {
	public $shipment = array();
	public $order = array();
	public $product_rows = array();
	public $delivery_country = 'DE';
	public $delivery_zone_code = 'BE';
	public $parcel_value = 70;
	public $checkout_shipping_price = 12.0;
	public $lock_count = 0;
	public $test_session;

	public function installSchema() {
	}

	public function getShipment($order_id) {
		return $this->shipment;
	}

	protected function withOrderLock($order_id, $callback) {
		$this->lock_count++;
		return call_user_func($callback);
	}

	protected function getOrder($order_id) {
		return $this->order;
	}

	protected function getOrderCountryCode($order) {
		return $this->delivery_country;
	}

	protected function getOrderZoneCode($order) {
		return $this->delivery_zone_code;
	}

	protected function getOrderProductRows($order_id) {
		return $this->product_rows;
	}

	protected function getKilogramWeightClass() {
		return array('id' => 1, 'value' => 1.0);
	}

	protected function calculateParcelValue($order) {
		return $this->parcel_value;
	}

	protected function getCheckoutShippingPrice($order_id) {
		return $this->checkout_shipping_price;
	}

	protected function prepareValidationRecord($existing, $order, $payload) {
		$this->shipment = array(
			'eurosender_shipment_id' => isset($existing['eurosender_shipment_id']) ? $existing['eurosender_shipment_id'] : 1,
			'order_id'                => (int)$order['order_id'],
			'order_number'            => !empty($order['number_order']) ? $order['number_order'] : $order['order_id'],
			'customer_internal_reference' => $payload['customerInternalReference'],
			'service_type'            => $payload['serviceType'],
			'environment'             => $this->client->getConnectionInfo()['environment'],
			'state'                   => 'validating',
			'retryable'               => 1,
			'order_code'              => null,
			'status'                  => '',
			'tracking_number'         => '',
			'tracking_url'            => '',
			'currency_code'           => 'EUR',
			'quote_price'             => null,
			'booked_price'            => null,
			'price_difference'        => null,
			'payload'                 => json_encode($payload),
			'creation_error'          => '',
			'tracking_error'          => '',
			'label_error'             => ''
		);
	}

	protected function recordQuote($shipment, $response, $money) {
		$this->shipment['quote_price'] = $money['amount'];
		$this->shipment['currency_code'] = $money['currency'];
		$this->shipment['quote_response'] = json_encode($response);
	}

	protected function recordValidationSuccess($shipment, $response) {
		$this->shipment['validation_response'] = json_encode($response);
		$this->shipment['creation_error'] = '';
	}

	protected function recordCreating($shipment) {
		$this->shipment['state'] = 'creating';
		$this->shipment['retryable'] = 0;
		$this->shipment['creation_error'] = '';
	}

	protected function recordPreCreateFailure($shipment, $exception) {
		$this->shipment['state'] = 'error';
		$this->shipment['retryable'] = 1;
		$this->shipment['creation_error'] = $exception->getMessage();
	}

	protected function recordCreateFailure($shipment, $exception, $unknown) {
		$this->shipment['state'] = $unknown ? 'unknown' : 'error';
		$this->shipment['retryable'] = $unknown ? 0 : 1;
		$this->shipment['creation_error'] = $exception->getMessage();
	}

	protected function recordCreated($shipment, $order_code, $status, $tracking_number, $tracking_url, $response, $booked_money) {
		$this->shipment['state'] = 'created';
		$this->shipment['retryable'] = 0;
		$this->shipment['order_code'] = $order_code;
		$this->shipment['status'] = $status !== '' ? $status : 'created';
		$this->shipment['tracking_number'] = $tracking_number;
		$this->shipment['tracking_url'] = $tracking_url;
		$this->shipment['booked_price'] = $booked_money['amount'];
		$this->shipment['price_difference'] = $booked_money['amount'] - $this->shipment['quote_price'];
		$this->shipment['response'] = json_encode($response);
		$this->shipment['creation_error'] = '';
	}

	protected function recordTrackingSuccess($shipment, $status, $tracking_number, $tracking_url, $order_response, $tracking_response, $booked_money) {
		$this->shipment['status'] = $status;
		$this->shipment['tracking_number'] = $tracking_number;
		$this->shipment['tracking_url'] = $tracking_url;
		$this->shipment['tracking_response'] = json_encode(array('order' => $order_response, 'tracking' => $tracking_response));
		$this->shipment['tracking_error'] = '';
		if ($booked_money['amount'] !== null) {
			$this->shipment['booked_price'] = $booked_money['amount'];
			$this->shipment['price_difference'] = $booked_money['amount'] - $this->shipment['quote_price'];
		}
	}

	protected function recordTrackingError($shipment, $error) {
		$this->shipment['tracking_error'] = $error;
	}

	protected function recordLabelSuccess($shipment) {
		$this->shipment['label_downloaded_at'] = '2026-10-02 12:00:00';
		$this->shipment['label_error'] = '';
	}

	protected function recordLabelError($shipment, $error) {
		$this->shipment['label_error'] = $error;
	}
}

function assertEurosenderSame($expected, $actual, $message) {
	if ($expected !== $actual) {
		throw new RuntimeException($message . ' Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . '.');
	}
}

function assertEurosenderTrue($condition, $message) {
	if (!$condition) {
		throw new RuntimeException($message);
	}
}

function eurosenderSelectedApiKey($client) {
	$property = new \ReflectionProperty('\Eurosender\Client', 'api_key');
	$property->setAccessible(true);

	return $property->getValue($client);
}

function eurosenderSelectApiKey($client, $configuration, $environment) {
	$method = new \ReflectionMethod('\Eurosender\Client', 'selectApiKey');
	$method->setAccessible(true);

	return $method->invoke($client, $configuration, $environment);
}

function expectEurosenderException($callback, $message_part) {
	try {
		call_user_func($callback);
	} catch (Throwable $exception) {
		if ($message_part !== '' && strpos($exception->getMessage(), $message_part) === false) {
			throw new RuntimeException('Unexpected exception: ' . $exception->getMessage());
		}

		return $exception;
	}

	throw new RuntimeException('Expected exception was not thrown.');
}

function eurosenderTestConfigValues() {
	return array(
		'shipping_eurosender_environment' => 'sandbox',
		'shipping_eurosender_origin_name' => 'DryZen Logistics',
		'shipping_eurosender_origin_email' => 'warehouse@example.test',
		'shipping_eurosender_origin_phone' => '099 111 2222',
		'shipping_eurosender_origin_address_1' => 'Ilica 1',
		'shipping_eurosender_origin_address_2' => 'Skladište',
		'shipping_eurosender_origin_city' => 'Zagreb',
		'shipping_eurosender_origin_postcode' => '10000',
		'shipping_eurosender_origin_country_code' => 'HR',
		'shipping_eurosender_length' => '20',
		'shipping_eurosender_width' => '15.2',
		'shipping_eurosender_height' => '10',
		'shipping_eurosender_packaging_weight' => '0.20',
		'shipping_eurosender_fallback_item_weight' => '0.10',
		'shipping_eurosender_minimum_weight' => '0.50',
		'shipping_eurosender_content' => 'Cosmetics',
		'shipping_eurosender_payment_method' => 'credit',
		'config_currency' => 'EUR',
		'config_language_id' => 3,
		'config_weight_class_id' => 1
	);
}

function eurosenderTestOrder() {
	return array(
		'order_id' => 23,
		'number_order' => '2026-23',
		'shipping_code' => 'eurosender.selection',
		'currency_code' => 'EUR',
		'firstname' => 'Ana',
		'lastname' => 'Kupac',
		'shipping_firstname' => 'Ana',
		'shipping_lastname' => 'Kupac',
		'shipping_address_1' => 'Hauptstrasse 2',
		'shipping_address_2' => '2. kat',
		'shipping_city' => 'Berlin',
		'shipping_postcode' => '10115',
		'shipping_country_id' => 81,
		'shipping_zone' => 'Berlin',
		'shipping_zone_id' => 1,
		'email' => 'ana@example.test',
		'telephone' => '0151 23456789',
		'total' => 78.75
	);
}

function makeEurosenderManager($client) {
	$config = new EurosenderTestConfig(eurosenderTestConfigValues());
	$session = new EurosenderTestSession();
	$registry = new EurosenderTestRegistry(array(
		'config' => $config,
		'db' => new EurosenderTestDb(),
		'log' => new EurosenderTestLog(),
		'session' => $session,
		'eurosender_client' => $client
	));
	$manager = new MemoryEurosenderShipmentManager($registry);
	$manager->test_session = $session;
	$manager->order = eurosenderTestOrder();
	$manager->product_rows = array(array(
		'quantity' => 5,
		'weight' => 0,
		'weight_class_id' => 1,
		'weight_class_value' => 1
	));

	return $manager;
}

function previewEurosenderShipment($manager, $order_id = 23) {
	return $manager->previewShipment($order_id);
}

function bookEurosenderShipment($manager, $order_id = 23) {
	$preview = previewEurosenderShipment($manager, $order_id);

	return $manager->createShipment($order_id, $preview['confirmation_token']);
}

// Client contract and routing; overridden transport guarantees no network use.
$client_registry = new EurosenderTestRegistry(array('config' => new EurosenderTestConfig(eurosenderTestConfigValues())));
$recording_client = new RecordingEurosenderClient($client_registry);
assertEurosenderTrue($recording_client->isConfigured(), 'Client should read the API key from OC_ENV.');
assertEurosenderSame('sandbox', $recording_client->getConnectionInfo()['environment'], 'Sandbox should be the safe default.');
assertEurosenderSame('sandbox-test-api-key', eurosenderSelectedApiKey($recording_client), 'Sandbox must use its environment-specific API key.');
assertEurosenderSame('legacy-key', eurosenderSelectApiKey($recording_client, array('api_key' => 'legacy-key'), 'sandbox'), 'Legacy single-key configuration must remain supported.');
assertEurosenderSame('', eurosenderSelectApiKey($recording_client, array(), 'sandbox'), 'Missing API keys must remain unconfigured.');
assertEurosenderTrue(!array_key_exists('api_key', $recording_client->getConnectionInfo()), 'Connection info must never expose the API key.');

$production_config = eurosenderTestConfigValues();
$production_config['shipping_eurosender_environment'] = 'production';
$production_client = new RecordingEurosenderClient(new EurosenderTestRegistry(array('config' => new EurosenderTestConfig($production_config))));
assertEurosenderSame('production', $production_client->getConnectionInfo()['environment'], 'Production environment should be selected explicitly.');
assertEurosenderSame('production-test-api-key', eurosenderSelectedApiKey($production_client), 'Production must use its environment-specific API key.');
assertEurosenderSame('sandbox-key', eurosenderSelectApiKey($recording_client, array('sandbox_api_key' => 'sandbox-key', 'api_key' => 'legacy-key'), 'sandbox'), 'Environment-specific keys must take precedence over the legacy key.');
$recording_client->quote(array('shipment' => array()));
$recording_client->validateOrder(array('shipment' => array()));
$recording_client->createOrder(array('shipment' => array()));
$recording_client->getOrder('ORDER 1');
$recording_client->getLabels('ORDER 1');
$recording_client->getTracking('ORDER 1');
assertEurosenderSame('/v1/quotes', $recording_client->calls[0]['path'], 'Quote endpoint mismatch.');
assertEurosenderSame('/v1/orders/validate_creation', $recording_client->calls[1]['path'], 'Validation endpoint mismatch.');
assertEurosenderSame('/v1/orders', $recording_client->calls[2]['path'], 'Create endpoint mismatch.');
assertEurosenderSame('/v1/orders/ORDER%201', $recording_client->calls[3]['path'], 'Order code must be URL encoded.');
assertEurosenderSame('application/pdf', $recording_client->calls[4]['accept'], 'Label request must ask for PDF.');
assertEurosenderSame(false, $recording_client->calls[4]['expect_json'], 'Label response must not be forced through JSON decoding.');

// Successful manual quote -> validation -> booking.
$fake_client = new FakeEurosenderClient();
$manager = makeEurosenderManager($fake_client);
$preview = previewEurosenderShipment($manager);
assertEurosenderSame(8.75, $preview['quote_price'], 'Preview should expose the fresh carrier quote.');
assertEurosenderSame(12.0, $preview['checkout_shipping_price'], 'Preview should expose the delivery amount charged to the customer.');
assertEurosenderTrue(strlen($preview['confirmation_token']) === 64, 'Preview must issue a strong one-time confirmation token.');
$shipment = $manager->createShipment(23, $preview['confirmation_token']);
assertEurosenderSame(2, $fake_client->quote_calls, 'Preview and booking must each obtain a fresh quote.');
assertEurosenderSame(2, $fake_client->validate_calls, 'Preview and booking must each validate the order.');
assertEurosenderSame(1, $fake_client->create_calls, 'Order should be created once.');
assertEurosenderSame('EUR', $fake_client->last_quote_payload['currencyCode'], 'Fresh quote must explicitly request EUR.');
assertEurosenderSame('selection', $fake_client->last_quote_payload['serviceType'], 'Fresh booking quote must select the same service as the order.');
assertEurosenderSame('selection', $fake_client->last_create_payload['serviceType'], 'serviceType must come from the OpenCart shipping code.');
assertEurosenderSame('DRYZEN-23', $fake_client->last_create_payload['customerInternalReference'], 'Internal reference mismatch.');
assertEurosenderSame('+385991112222', $fake_client->last_create_payload['shipment']['pickupContact']['phone'], 'Origin phone should be normalized to E.164.');
assertEurosenderSame('+4915123456789', $fake_client->last_create_payload['shipment']['deliveryContact']['phone'], 'Delivery phone should be normalized to E.164.');
assertEurosenderTrue(!isset($fake_client->last_create_payload['shipment']['deliveryAddress']['regionCode']), 'regionCode must be omitted outside IT/US/CA.');
assertEurosenderSame(16, $fake_client->last_create_payload['parcels']['packages'][0]['width'], 'Package dimensions must be positive integers.');
assertEurosenderSame(0.7, $fake_client->last_create_payload['parcels']['packages'][0]['weight'], 'Fallback item weight plus packaging is incorrect.');
assertEurosenderSame(70, $fake_client->last_create_payload['parcels']['packages'][0]['value'], 'Parcel value must be an integer EUR amount.');
assertEurosenderSame(array('email' => 'warehouse@example.test'), $fake_client->last_create_payload['orderContact'], 'Order contact should contain the documented email field only.');
assertEurosenderTrue(!isset($fake_client->last_create_payload['courierId']), 'Internal-only courierId must never be sent.');
assertEurosenderTrue(!isset($fake_client->last_create_payload['pickupDate']), 'Pickup date must be omitted unless explicitly supported/configured.');
assertEurosenderSame(8.75, $shipment['quote_price'], 'Fresh quote price was not stored.');
assertEurosenderSame(9.25, $shipment['booked_price'], 'Authoritative booked price was not stored.');
assertEurosenderSame(0.5, $shipment['price_difference'], 'Booked-versus-quoted price difference is incorrect.');
assertEurosenderSame('created', $shipment['state'], 'Successful booking state mismatch.');
$duplicate = $manager->createShipment(23, $preview['confirmation_token']);
assertEurosenderTrue(!empty($duplicate['existing']), 'Repeated explicit action should return the existing booking.');
assertEurosenderSame(1, $fake_client->create_calls, 'Existing booking must not call POST /orders again.');

// Booking confirmation must use the selected order total, which includes any
// applicable fees. A service-option base price is not a safe substitute.
$incomplete_quote_client = new FakeEurosenderClient();
unset($incomplete_quote_client->quote_response['order']['totalPrice']);
$incomplete_quote_manager = makeEurosenderManager($incomplete_quote_client);
expectEurosenderException(function () use ($incomplete_quote_manager) {
	previewEurosenderShipment($incomplete_quote_manager);
}, 'ne sadrži ispravnu EUR cijenu');
assertEurosenderSame(0, $incomplete_quote_client->validate_calls, 'An incomplete selected-order total must stop before validation.');
assertEurosenderSame(0, $incomplete_quote_client->create_calls, 'An incomplete selected-order total must never reach booking.');

$missing_currency_client = new FakeEurosenderClient();
unset($missing_currency_client->quote_response['order']['totalPrice']['original']['currencyCode']);
$missing_currency_manager = makeEurosenderManager($missing_currency_client);
expectEurosenderException(function () use ($missing_currency_manager) {
	previewEurosenderShipment($missing_currency_manager);
}, 'ne sadrži ispravnu EUR cijenu');
assertEurosenderSame(0, $missing_currency_client->validate_calls, 'A quote without an explicit EUR currency must stop before validation.');
assertEurosenderSame(0, $missing_currency_client->create_calls, 'A quote without an explicit EUR currency must never reach booking.');

// Invalid tokens do not consume a valid preview, while a successful token is
// single-use and bound to the current admin, payload, checkout price and API environment.
$token_client = new FakeEurosenderClient();
$token_manager = makeEurosenderManager($token_client);
$token_preview = previewEurosenderShipment($token_manager);
expectEurosenderException(function () use ($token_manager) {
	$token_manager->createShipment(23, 'invalid-token');
}, 'nije valjana');
assertEurosenderSame(0, $token_client->create_calls, 'An invalid token must not create an order.');
$token_manager->createShipment(23, $token_preview['confirmation_token']);
assertEurosenderSame(1, $token_client->create_calls, 'The still-valid preview should book exactly once.');

$expired_client = new FakeEurosenderClient();
$expired_manager = makeEurosenderManager($expired_client);
$expired_preview = previewEurosenderShipment($expired_manager);
$expired_manager->test_session->data['eurosender_booking_previews'][23]['expires_at'] = time() - 1;
expectEurosenderException(function () use ($expired_manager, $expired_preview) {
	$expired_manager->createShipment(23, $expired_preview['confirmation_token']);
}, 'istekla');
assertEurosenderSame(0, $expired_client->create_calls, 'An expired preview must not create an order.');

$admin_client = new FakeEurosenderClient();
$admin_manager = makeEurosenderManager($admin_client);
$admin_preview = previewEurosenderShipment($admin_manager);
$admin_manager->test_session->data['user_id'] = 99;
expectEurosenderException(function () use ($admin_manager, $admin_preview) {
	$admin_manager->createShipment(23, $admin_preview['confirmation_token']);
}, 'isti administrator');
assertEurosenderSame(0, $admin_client->create_calls, 'A different administrator must not confirm another admin preview.');

$payload_client = new FakeEurosenderClient();
$payload_manager = makeEurosenderManager($payload_client);
$payload_preview = previewEurosenderShipment($payload_manager);
$payload_manager->order['shipping_address_1'] = 'Changed after preview 9';
expectEurosenderException(function () use ($payload_manager, $payload_preview) {
	$payload_manager->createShipment(23, $payload_preview['confirmation_token']);
}, 'Podaci narudžbe');
assertEurosenderSame(0, $payload_client->create_calls, 'Changed order data must require another preview.');

$checkout_client = new FakeEurosenderClient();
$checkout_manager = makeEurosenderManager($checkout_client);
$checkout_preview = previewEurosenderShipment($checkout_manager);
$checkout_manager->checkout_shipping_price = 11.50;
expectEurosenderException(function () use ($checkout_manager, $checkout_preview) {
	$checkout_manager->createShipment(23, $checkout_preview['confirmation_token']);
}, 'Iznos dostave');
assertEurosenderSame(0, $checkout_client->create_calls, 'Changed checkout shipping price must require another preview.');

$preview_environment_client = new FakeEurosenderClient();
$preview_environment_manager = makeEurosenderManager($preview_environment_client);
$environment_preview = previewEurosenderShipment($preview_environment_manager);
$preview_environment_client->environment = 'production';
expectEurosenderException(function () use ($preview_environment_manager, $environment_preview) {
	$preview_environment_manager->createShipment(23, $environment_preview['confirmation_token']);
}, 'okruženje promijenili');
assertEurosenderSame(0, $preview_environment_client->create_calls, 'Environment changes must invalidate the preview.');

// Official order/tracking response paths must populate the tracking panel.
$official_client = new FakeEurosenderClient();
$official_client->create_response = array(
	'orderCode' => 'ES-OFFICIAL-23',
	'parcels' => array(
		'packages' => array(
			array('tracking' => array(
				'number' => 'OFFICIAL-TRACK-23',
				'url' => 'https://tracking.example.test/OFFICIAL-TRACK-23',
				'deliveryStatus' => 'confirmed'
			))
		)
	),
	'price' => array('original' => array('gross' => 9.25, 'currencyCode' => 'EUR'))
);
$official_manager = makeEurosenderManager($official_client);
$official_shipment = bookEurosenderShipment($official_manager);
assertEurosenderSame('OFFICIAL-TRACK-23', $official_shipment['tracking_number'], 'Official parcels.packages[].tracking.number path was not read.');
assertEurosenderSame('https://tracking.example.test/OFFICIAL-TRACK-23', $official_shipment['tracking_url'], 'Official parcels.packages[].tracking.url path was not read.');
assertEurosenderSame('confirmed', $official_shipment['status'], 'Official tracking deliveryStatus was not read.');
$official_client->order_response = $official_client->create_response;
$official_client->tracking_response = array('parcels' => array(array(
	'trackingNumber' => 'OFFICIAL-TRACK-23',
	'currentStatus' => 'delivered'
)));
$official_refreshed = $official_manager->refreshTracking(23);
assertEurosenderSame('delivered', $official_refreshed['status'], 'Official parcels[].currentStatus path was not read.');
$official_client->order_response = array(
	'orderCode' => 'ES-OFFICIAL-23',
	'status' => 'delivered',
	'trackingNumber' => 'OFFICIAL-TRACK-23'
);
$official_client->tracking_exception = new \Eurosender\ApiException('tracking not available', 404, '{}');
$official_order_only = $official_manager->refreshTracking(23);
assertEurosenderSame('delivered', $official_order_only['status'], 'Order status should still refresh when the optional tracking endpoint returns 404.');
assertEurosenderSame('', $official_order_only['tracking_error'], 'A tracking 404 must not discard a successful order refresh.');

// A definite 4xx is retryable, but still only after another explicit action.
$fourxx_client = new FakeEurosenderClient();
$fourxx_client->create_exception = new \Eurosender\ApiException('Invalid order', 422, '{"error":"invalid"}');
$fourxx_manager = makeEurosenderManager($fourxx_client);
$fourxx_preview = previewEurosenderShipment($fourxx_manager);
expectEurosenderException(function () use ($fourxx_manager, $fourxx_preview) {
	$fourxx_manager->createShipment(23, $fourxx_preview['confirmation_token']);
}, 'Invalid order');
assertEurosenderSame('error', $fourxx_manager->shipment['state'], 'A definite 4xx should use error state.');
assertEurosenderSame(1, $fourxx_manager->shipment['retryable'], 'A definite 4xx should be marked retryable.');
$fourxx_client->create_exception = null;
$fourxx_retry_preview = previewEurosenderShipment($fourxx_manager);
$fourxx_manager->createShipment(23, $fourxx_retry_preview['confirmation_token']);
assertEurosenderSame(2, $fourxx_client->create_calls, 'A new explicit action may retry a definite 4xx.');

// Transport timeout and 5xx are ambiguous: never blind-retry a potentially paid order.
$unknown_client = new FakeEurosenderClient();
$unknown_client->create_exception = new \Eurosender\ApiException('timeout', 0, '', true, true);
$unknown_manager = makeEurosenderManager($unknown_client);
$unknown_preview = previewEurosenderShipment($unknown_manager);
expectEurosenderException(function () use ($unknown_manager, $unknown_preview) {
	$unknown_manager->createShipment(23, $unknown_preview['confirmation_token']);
}, 'timeout');
assertEurosenderSame('unknown', $unknown_manager->shipment['state'], 'Transport error should use unknown state.');
assertEurosenderSame(0, $unknown_manager->shipment['retryable'], 'Unknown creation result must not be retryable.');
expectEurosenderException(function () use ($unknown_manager, $unknown_preview) {
	$unknown_manager->createShipment(23, $unknown_preview['confirmation_token']);
}, 'dvostruku naplatu');
assertEurosenderSame(1, $unknown_client->create_calls, 'Unknown creation result must block a second POST /orders.');

$server_client = new FakeEurosenderClient();
$server_client->create_exception = new \Eurosender\ApiException('server error', 503, '{}', false, true);
$server_manager = makeEurosenderManager($server_client);
$server_preview = previewEurosenderShipment($server_manager);
expectEurosenderException(function () use ($server_manager, $server_preview) {
	$server_manager->createShipment(23, $server_preview['confirmation_token']);
}, 'server error');
assertEurosenderSame('unknown', $server_manager->shipment['state'], 'HTTP 5xx should use unknown state.');

$rate_limit_client = new FakeEurosenderClient();
$rate_limit_client->create_exception = new \Eurosender\ApiException('rate limited', 429, '{}');
$rate_limit_manager = makeEurosenderManager($rate_limit_client);
$rate_limit_preview = previewEurosenderShipment($rate_limit_manager);
expectEurosenderException(function () use ($rate_limit_manager, $rate_limit_preview) {
	$rate_limit_manager->createShipment(23, $rate_limit_preview['confirmation_token']);
}, 'rate limited');
assertEurosenderSame('unknown', $rate_limit_manager->shipment['state'], 'An undocumented create 4xx must be treated as ambiguous.');
assertEurosenderSame(0, $rate_limit_manager->shipment['retryable'], 'An ambiguous create 4xx must block blind retries.');

// Validation failure cannot have charged the account and remains retryable.
$validation_client = new FakeEurosenderClient();
$validation_manager = makeEurosenderManager($validation_client);
$validation_preview = previewEurosenderShipment($validation_manager);
$validation_client->validation_exception = new \Eurosender\ApiException('validation failed', 422, '{}');
expectEurosenderException(function () use ($validation_manager, $validation_preview) {
	$validation_manager->createShipment(23, $validation_preview['confirmation_token']);
}, 'validation failed');
assertEurosenderSame('error', $validation_manager->shipment['state'], 'Validation failure should use error state.');
assertEurosenderSame(0, $validation_client->create_calls, 'Validation failure must not call POST /orders.');

// Even a one-cent increase after confirmation must stop before a paid request.
$price_guard_client = new FakeEurosenderClient();
$price_guard_manager = makeEurosenderManager($price_guard_client);
$price_guard_preview = previewEurosenderShipment($price_guard_manager);
$price_guard_client->quote_response['order']['totalPrice']['original']['gross'] = 8.76;
expectEurosenderException(function () use ($price_guard_manager, $price_guard_preview) {
	$price_guard_manager->createShipment(23, $price_guard_preview['confirmation_token']);
}, 'cijena promijenila');
assertEurosenderSame(1, $price_guard_client->validate_calls, 'Repricing must stop before the second validation.');
assertEurosenderSame(0, $price_guard_client->create_calls, 'Price guard must stop before a chargeable create call.');
expectEurosenderException(function () use ($price_guard_manager, $price_guard_preview) {
	$price_guard_manager->createShipment(23, $price_guard_preview['confirmation_token']);
}, 'nije valjana');
assertEurosenderSame(2, $price_guard_client->quote_calls, 'A consumed confirmation token must not trigger another quote.');

// A carrier price above the checkout charge is allowed only after the admin
// has explicitly seen and confirmed that difference.
$subsidy_client = new FakeEurosenderClient();
$subsidy_client->quote_response['order']['totalPrice']['original']['gross'] = 12.50;
$subsidy_manager = makeEurosenderManager($subsidy_client);
$subsidy_manager->checkout_shipping_price = 12.00;
$subsidy_shipment = bookEurosenderShipment($subsidy_manager);
assertEurosenderSame('created', $subsidy_shipment['state'], 'An explicitly confirmed carrier subsidy should be bookable.');
assertEurosenderSame(1, $subsidy_client->create_calls, 'Confirmed carrier subsidy should create exactly one order.');

// Tracking refresh and conservative label decoding.
$refreshed = $manager->refreshTracking(23);
assertEurosenderSame('intransit', $refreshed['status'], 'Tracking status refresh mismatch.');
assertEurosenderSame('TRACK-23', $refreshed['tracking_number'], 'Tracking number refresh mismatch.');
assertEurosenderSame('%PDF-test-label', $manager->getLabel(23), 'Raw PDF label should be returned unchanged.');
$tracking_calls_before_environment_switch = $fake_client->tracking_calls;
$fake_client->environment = 'production';
expectEurosenderException(function () use ($manager) {
	$manager->refreshTracking(23);
}, 'pripada okruženju sandbox');
assertEurosenderSame($tracking_calls_before_environment_switch, $fake_client->tracking_calls, 'Environment mismatch must be rejected before an API request.');
$fake_client->environment = 'sandbox';
$fake_client->label_response = array('label' => base64_encode('%PDF-json-label'));
assertEurosenderSame('%PDF-json-label', $manager->getLabel(23), 'Detected JSON/base64 PDF should be decoded.');
$fake_client->label_response = array('url' => 'https://example.test/undocumented-label');
expectEurosenderException(function () use ($manager) {
	$manager->getLabel(23);
}, 'PDF adresnicu');
assertEurosenderTrue($manager->shipment['label_error'] !== '', 'Invalid label response should be recorded.');

// Checkout quote parsing: pickup-date fees are not included in the base price
// and the parcel value must match the gross value used when booking.
if (!class_exists('Model', false)) {
	class Model {
		protected $registry;

		public function __construct($registry) {
			$this->registry = $registry;
		}

		public function __get($key) {
			return $this->registry->get($key);
		}
	}
}

class EurosenderTestCart {
	public function getProducts() {
		return array(
			array('total' => 20.0, 'tax_class_id' => 1, 'quantity' => 2, 'price' => 10.0),
			array('total' => 10.0, 'tax_class_id' => 0, 'quantity' => 1, 'price' => 10.0)
		);
	}

	public function getSubTotal() {
		return 30.0;
	}
}

class EurosenderTestTax {
	public function calculate($value, $tax_class_id, $calculate = true) {
		return $calculate && $tax_class_id ? (float)$value * 1.25 : (float)$value;
	}
}

class EurosenderTestLanguage {
	public function get($key) {
		return $key;
	}
}

class EurosenderTestLoader {
	public function language($route, $key = '') {
		return array();
	}
}

class EurosenderTestCurrency {
	public function convert($value, $from, $to) {
		return (float)$value;
	}

	public function format($value, $currency) {
		return $currency . ' ' . number_format((float)$value, 2, '.', '');
	}
}

class EurosenderTestSession {
	public $data = array('currency' => 'EUR', 'user_id' => 7);
}

function makeEurosenderQuoteModel($client) {
	$values = array_merge(eurosenderTestConfigValues(), array(
		'shipping_eurosender_status' => 1,
		'shipping_eurosender_geo_zone_id' => 0,
		'shipping_eurosender_service_types' => array('selection'),
		'shipping_eurosender_markup_type' => 'fixed',
		'shipping_eurosender_markup_value' => '0',
		'shipping_eurosender_fallback_rate' => '7.50',
		'shipping_eurosender_sort_order' => 2
	));
	$registry = new EurosenderTestRegistry(array(
		'cart' => new EurosenderTestCart(),
		'tax' => new EurosenderTestTax(),
		'config' => new EurosenderTestConfig($values),
		'load' => new EurosenderTestLoader(),
		'language' => new EurosenderTestLanguage(),
		'currency' => new EurosenderTestCurrency(),
		'session' => new EurosenderTestSession(),
		'eurosender_client' => $client
	));

	return new ModelExtensionShippingEurosender($registry);
}

require_once __DIR__ . '/../upload/catalog/model/extension/shipping/eurosender.php';
$quote_model = new ModelExtensionShippingEurosender(new EurosenderTestRegistry(array(
	'cart' => new EurosenderTestCart(),
	'tax' => new EurosenderTestTax()
)));
$quote_reflection = new ReflectionClass($quote_model);
$gross_price_method = $quote_reflection->getMethod('extractGrossEuroPrice');
$gross_price_method->setAccessible(true);
$service_quote = array(
	'price' => array('original' => array('currencyCode' => 'EUR', 'gross' => 10.0)),
	'pickupDateFees' => array(
		array('code' => 'same_day', 'price' => array('original' => array('currencyCode' => 'EUR', 'gross' => 1.5))),
		array('code' => 'late_ordering', 'price' => array('original' => array('currencyCode' => 'EUR', 'gross' => 0.75)))
	)
);
assertEurosenderSame(12.25, $gross_price_method->invoke($quote_model, $service_quote), 'Pickup-date fees must be added to the checkout quote.');
$parcel_value_method = $quote_reflection->getMethod('calculateParcelValue');
$parcel_value_method->setAccessible(true);
assertEurosenderSame(35.0, $parcel_value_method->invoke($quote_model), 'Checkout parcel value must include recorded product tax.');
$rejection_method = $quote_reflection->getMethod('isDefiniteQuoteRejection');
$rejection_method->setAccessible(true);
assertEurosenderSame(true, $rejection_method->invoke($quote_model, new \Eurosender\ApiException('invalid', 422)), 'Invalid quote payloads must not use a fallback rate.');
assertEurosenderSame(false, $rejection_method->invoke($quote_model, new \Eurosender\ApiException('temporary', 429)), 'Temporary quote failures may use the configured fallback rate.');

$delivery_address = array(
	'iso_code_2' => 'HR',
	'postcode' => '21000',
	'city' => 'Split',
	'address_1' => 'Riva 1',
	'address_2' => '',
	'zone' => 'Splitsko-dalmatinska'
);
$empty_quote_client = new FakeEurosenderClient();
$empty_quote_client->quote_response = array('options' => array('serviceTypes' => array()));
assertEurosenderSame(array(), makeEurosenderQuoteModel($empty_quote_client)->getQuote($delivery_address), 'A successful quote with no supported service must not expose a fallback rate.');
$invalid_quote_client = new FakeEurosenderClient();
$invalid_quote_client->quote_exception = new \Eurosender\ApiException('invalid route', 422, '{}');
assertEurosenderSame(array(), makeEurosenderQuoteModel($invalid_quote_client)->getQuote($delivery_address), 'A rejected shipment must not expose a fallback rate.');
$temporary_quote_client = new FakeEurosenderClient();
$temporary_quote_client->quote_exception = new \Eurosender\ApiException('temporary outage', 503, '{}', false, true);
$temporary_quote = makeEurosenderQuoteModel($temporary_quote_client)->getQuote($delivery_address);
assertEurosenderTrue(isset($temporary_quote['quote']['selection']), 'A transient API outage should use the configured fallback rate.');
$local_failure_client = new FakeEurosenderClient();
$local_failure_client->quote_exception = new RuntimeException('local payload failure');
assertEurosenderSame(array(), makeEurosenderQuoteModel($local_failure_client)->getQuote($delivery_address), 'A local programming/configuration failure must not expose a fallback rate.');

echo "Eurosender shipping tests passed.\n";
