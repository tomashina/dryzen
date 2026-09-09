<?php
define('DB_PREFIX', 'oc_');

class TestRegistry {
	private $data = array();

	public function __construct($data = array()) {
		$this->data = $data;
	}

	public function get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function has($key) {
		return array_key_exists($key, $this->data);
	}
}

class TestConfig {
	private $data;

	public function __construct($data) {
		$this->data = $data;
	}

	public function get($key) {
		return array_key_exists($key, $this->data) ? $this->data[$key] : null;
	}
}

class TestLog {
	public $messages = array();

	public function write($message) {
		$this->messages[] = $message;
	}
}

class Language {
	private $code;

	public function __construct($code) {
		$this->code = $code;
	}

	public function load($route) {
		return array();
	}

	public function get($key) {
		$values = array(
			'text_tracking_email_history' => 'Tracking email sent:',
			'mail_subject' => 'BOX NOW shipment - %s',
			'mail_heading' => 'Tracking information',
			'mail_greeting' => 'Hello %s,',
			'mail_intro' => 'Shipment for order #%s was created.',
			'mail_tracking_code' => 'Tracking code',
			'mail_tracking_status' => 'Status',
			'mail_shipping_method' => 'Shipping',
			'mail_track_button' => 'Track',
			'mail_order_button' => 'Order',
			'mail_note' => 'Tracking can update later.',
			'mail_footer' => 'Regards, %s',
			'status_new' => 'New',
			'status_unknown' => 'Status: %s',
			'status_unavailable' => 'Unavailable'
		);

		return isset($values[$key]) ? $values[$key] : $key;
	}
}

class TestMail {
	public $to;
	public $bcc = array();
	public $from;
	public $sender;
	public $subject;
	public $text;
	public $html;
	public $attachments = array();
	public $parameter;
	public $smtp_hostname;
	public $smtp_username;
	public $smtp_password;
	public $smtp_port;
	public $smtp_timeout;
	private $manager;
	private $fail;

	public function __construct($manager, $fail) {
		$this->manager = $manager;
		$this->fail = $fail;
	}

	public function setTo($value) { $this->to = $value; }
	public function setBcc($value) { $this->bcc = $value; }
	public function setFrom($value) { $this->from = $value; }
	public function setSender($value) { $this->sender = $value; }
	public function setSubject($value) { $this->subject = $value; }
	public function setText($value) { $this->text = $value; }
	public function setHtml($value) { $this->html = $value; }
	public function addAttachment($value) { $this->attachments[] = $value; }

	public function send() {
		if ($this->fail) {
			throw new RuntimeException('Simulated mail failure');
		}

		$this->manager->recordSentMail($this);
	}
}

require_once __DIR__ . '/../upload/system/library/boxnow/shipment_manager.php';

class TestShipmentManager extends \Boxnow\ShipmentManager {
	public $order;
	public $shipment = array();
	public $apiCalls = array();
	public $sentMails = array();
	public $histories = array();
	public $settings = array();
	public $mailShouldFail = false;
	public $apiShouldFail = false;
	public $invalidPdf = false;
	public $lockCount = 0;
	public $temporaryPaths = array();

	public function installSchema() {
		// The test uses in-memory storage.
	}

	public function getShipmentByOrderId($order_id) {
		return $this->shipment;
	}

	public function recordSentMail($mail) {
		$attachments = array();

		foreach ($mail->attachments as $path) {
			if (!is_file($path)) {
				throw new RuntimeException('Attachment did not exist while the mail was sent.');
			}

			$attachments[] = array(
				'path' => $path,
				'name' => basename($path),
				'content' => file_get_contents($path)
			);
		}

		$this->sentMails[] = array(
			'to' => $mail->to,
			'bcc' => $mail->bcc,
			'from' => $mail->from,
			'subject' => $mail->subject,
			'text' => $mail->text,
			'html' => $mail->html,
			'attachments' => $attachments
		);
	}

	protected function withOrderLock($order_id, $callback) {
		$this->lockCount++;
		return call_user_func($callback);
	}

	protected function getOrder($order_id) {
		return $this->order;
	}

	protected function getOrderWeight($order_id) {
		return 1.25;
	}

	protected function ensureShipmentRecord($existing, $order_id, $order_number, $locker_id, $payload) {
		if (!$this->shipment) {
			$this->shipment = array(
				'boxnow_shipment_id' => 1,
				'order_id' => $order_id,
				'order_number' => $order_number,
				'reference_number' => '',
				'parcel_id' => '',
				'locker_id' => $locker_id,
				'status' => 'creating',
				'email_sent_at' => null,
				'email_error' => null,
				'label_email_sent_at' => null,
				'label_email_error' => null,
				'payload' => json_encode($payload)
			);
		}

		return $this->shipment;
	}

	protected function recordShipmentCreated($shipment, $reference_number, $parcel_id, $response) {
		$this->shipment['reference_number'] = $reference_number;
		$this->shipment['parcel_id'] = $parcel_id;
		$this->shipment['status'] = 'new';
		$this->shipment['creation_error'] = null;
		$this->shipment['response'] = json_encode($response);
	}

	protected function recordCreationError($shipment, $error) {
		$this->shipment['status'] = 'error';
		$this->shipment['creation_error'] = $error;
	}

	protected function apiRequest($method, $endpoint, $payload = null, $binary = false, $authenticated = true) {
		$this->apiCalls[] = array('method' => $method, 'endpoint' => $endpoint, 'payload' => $payload, 'binary' => $binary);

		if ($this->apiShouldFail) {
			throw new RuntimeException('Simulated BOX NOW API failure');
		}

		if ($binary) {
			return $this->invalidPdf ? '<html>not a pdf</html>' : "%PDF-1.4\nFake BOX NOW label";
		}

		return array('referenceNumber' => 'REF-23', 'parcels' => array(array('id' => 'PARCEL-23')));
	}

	protected function getStoreSettingValue($key, $store_id, $fallback = null) {
		return array_key_exists($key, $this->settings) ? $this->settings[$key] : $fallback;
	}

	protected function createMail() {
		return new TestMail($this, $this->mailShouldFail);
	}

	protected function markLabelEmailSent($shipment, $recipients) {
		$this->shipment['label_email_sent_at'] = '2026-07-31 15:00:00';
		$this->shipment['label_email_error'] = null;
		$this->shipment['label_email_recipients'] = json_encode(array_values($recipients));
	}

	protected function markLabelEmailError($shipment, $error) {
		$this->shipment['label_email_error'] = $error;
	}

	protected function markTrackingEmailSent($shipment) {
		$this->shipment['email_sent_at'] = '2026-07-31 15:00:00';
		$this->shipment['email_error'] = null;
	}

	protected function markTrackingEmailError($shipment, $error) {
		$this->shipment['email_error'] = $error;
	}

	protected function addOrderHistory($order, $comment) {
		$this->histories[] = $comment;
	}

	protected function getOrderLanguageCode($order) {
		return 'en-gb';
	}

	protected function cleanupTemporaryLabel($path) {
		$this->temporaryPaths[] = $path;
		parent::cleanupTemporaryLabel($path);
	}
}

class TestLoad {
	public function language($route) { return array(); }
	public function library($route) { return null; }
}

class TestQuoteDb {
	public function query($sql) {
		return (object)array('num_rows' => 0, 'row' => array());
	}
}

class TestQuoteCart {
	public $quantity;
	public $subtotal;

	public function countProducts() { return $this->quantity; }
	public function getSubTotal() { return $this->subtotal; }
}

class TestQuoteTax {
	public function calculate($cost, $tax_class_id, $calculate) { return $cost; }
}

class TestQuoteCurrency {
	public function format($amount, $currency) { return (string)$amount; }
}

function assertTrue($condition, $message) {
	if (!$condition) {
		fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
		exit(1);
	}
}

function assertSameValue($expected, $actual, $message) {
	if ($expected !== $actual) {
		fwrite(STDERR, "FAIL: " . $message . PHP_EOL);
		fwrite(STDERR, 'Expected: ' . var_export($expected, true) . PHP_EOL);
		fwrite(STDERR, 'Actual:   ' . var_export($actual, true) . PHP_EOL);
		exit(1);
	}
}

$config = new TestConfig(array(
	'shipping_boxnow_client_id' => 'client-id',
	'shipping_boxnow_client_secret' => 'client-secret',
	'shipping_boxnow_partner_id' => '15871',
	'shipping_boxnow_warehouse_id' => '2',
	'shipping_boxnow_origin_phone' => '098 111 222',
	'shipping_boxnow_origin_email' => 'warehouse@example.test',
	'shipping_boxnow_origin_name' => 'DryZen',
	'shipping_boxnow_order_prefix' => 'DRYZEN-',
	'shipping_boxnow_compartment_size' => '2',
	'shipping_boxnow_tracking_url' => 'https://track.boxnow.hr/?track={parcel}',
	'config_name' => 'DryZen',
	'config_email' => 'store@example.test',
	'config_telephone' => '098 111 222',
	'config_logo' => 'logo.png',
	'config_language' => 'en-gb',
	'config_mail_engine' => 'smtp',
	'config_mail_parameter' => '',
	'config_mail_smtp_hostname' => 'smtp.example.test',
	'config_mail_smtp_username' => 'store@example.test',
	'config_mail_smtp_password' => 'password',
	'config_mail_smtp_port' => 587,
	'config_mail_smtp_timeout' => 5,
	'shipping_boxnow_status' => 1,
	'config_processing_status' => array(2),
	'config_complete_status' => array(5),
	'payment_revolut_completed_status_id' => 15
));
$log = new TestLog();
$registry = new TestRegistry(array('db' => new stdClass(), 'config' => $config, 'log' => $log));
$manager = new TestShipmentManager($registry);
$manager->order = array(
	'order_id' => 23,
	'number_order' => '',
	'store_id' => 0,
	'store_name' => 'DryZen',
	'store_url' => 'https://dryzen.test/',
	'customer_id' => 7,
	'firstname' => 'Lucia',
	'lastname' => 'Radošević',
	'email' => 'customer@example.test',
	'telephone' => '091 901 5102',
	'shipping_code' => 'boxnow.boxnow',
	'shipping_method' => 'BOX NOW paketomat',
	'payment_code' => 'revolut',
	'payment_method' => 'Pay with Card',
	'total' => '29.80',
	'boxnow' => '21000, Split;HR-SPLIT-01',
	'order_status_id' => 2,
	'language_id' => 2
);
$manager->settings = array(
	'config_email' => 'order@example.test',
	'config_mail_alert' => array('account', 'order'),
	'config_mail_alert_email' => 'nabava@example.test, order@example.test;invalid, tomislav@example.test'
);

$shipment = $manager->createShipment(23);
assertSameValue('PARCEL-23', $shipment['parcel_id'], 'The parcel ID is stored after creation.');
assertSameValue('REF-23', $shipment['reference_number'], 'The BOX NOW reference is stored after creation.');
assertSameValue(1, count($manager->apiCalls), 'One delivery request is made.');
$payload = $manager->apiCalls[0]['payload'];
assertSameValue('DRYZEN-23', $payload['orderNumber'], 'The stable prefixed order number is sent.');
assertSameValue('HR-SPLIT-01', $payload['destination']['locationId'], 'The locker ID is parsed from the checkout value.');
assertSameValue('+385919015102', $payload['destination']['contactNumber'], 'The Croatian customer phone is normalized.');
assertSameValue('prepaid', $payload['paymentMode'], 'Card orders are sent as prepaid.');
assertSameValue(2, $payload['items'][0]['compartmentSize'], 'The configured compartment size is sent.');

$duplicate = $manager->createShipment(23);
assertTrue(!empty($duplicate['existing']), 'A repeated order event returns the existing shipment.');
assertSameValue(1, count($manager->apiCalls), 'A repeated order event does not create another BOX NOW parcel.');

$tracking = $manager->sendTrackingEmail(23);
assertTrue(!empty($tracking['email_sent']), 'The customer tracking email is sent.');
assertSameValue('customer@example.test', $manager->sentMails[0]['to'], 'The tracking email goes to the customer.');
assertTrue(strpos($manager->sentMails[0]['subject'], 'BOX NOW shipment') !== false, 'The tracking subject identifies BOX NOW.');
assertTrue(strpos($manager->sentMails[0]['html'], 'PARCEL-23') !== false, 'The tracking code is present in the HTML email.');
assertTrue(strpos($manager->sentMails[0]['text'], 'https://track.boxnow.hr/?track=PARCEL-23') !== false, 'The tracking URL is present in the text email.');
$trackingDuplicate = $manager->sendTrackingEmail(23);
assertTrue(!empty($trackingDuplicate['email_already_sent']), 'The customer tracking email is idempotent.');
assertSameValue(1, count($manager->sentMails), 'The customer does not receive a duplicate tracking email.');

$label = $manager->sendLabelEmail(23);
assertTrue(!empty($label['label_email_sent']), 'The internal label email is sent.');
$labelMail = $manager->sentMails[1];
assertSameValue('order@example.test', $labelMail['to'], 'The store order address is the primary label recipient.');
assertSameValue(array('nabava@example.test', 'tomislav@example.test'), $labelMail['bcc'], 'Additional order-alert recipients are deduplicated and invalid addresses are ignored.');
assertTrue(strpos($labelMail['subject'], 'DRYZEN-23') !== false && strpos($labelMail['subject'], 'REF-23') !== false, 'The label subject contains the order and BOX NOW reference.');
assertSameValue('BOX-NOW-adresnica-DRYZEN-23.pdf', $labelMail['attachments'][0]['name'], 'The PDF attachment has an operational filename.');
assertTrue(strpos($labelMail['attachments'][0]['content'], '%PDF-') === 0, 'The attachment contains the BOX NOW PDF.');
assertTrue(!is_file($manager->temporaryPaths[0]), 'The temporary PDF is removed after sending.');
assertSameValue(array('order@example.test', 'nabava@example.test', 'tomislav@example.test'), json_decode($manager->shipment['label_email_recipients'], true), 'The actual internal recipients are recorded.');

$labelDuplicate = $manager->sendLabelEmail(23);
assertTrue(!empty($labelDuplicate['label_email_already_sent']), 'The automatic label email is idempotent.');
assertSameValue(2, count($manager->sentMails), 'The automatic flow does not resend an already sent label.');
$manager->sendLabelEmail(23, true);
assertSameValue(3, count($manager->sentMails), 'An explicit admin resend sends the label again.');

$manager->shipment['label_email_sent_at'] = null;
$manager->mailShouldFail = true;
$mailFailureCaught = false;
try {
	$manager->sendLabelEmail(23);
} catch (RuntimeException $exception) {
	$mailFailureCaught = true;
}
assertTrue($mailFailureCaught, 'A label mail transport failure is reported.');
assertSameValue('Simulated mail failure', $manager->shipment['label_email_error'], 'A label mail transport failure is stored separately.');
$manager->mailShouldFail = false;
$manager->sendLabelEmail(23);
assertTrue(!empty($manager->shipment['label_email_sent_at']), 'A failed label email can be retried without recreating the parcel.');
assertSameValue(1, count(array_filter($manager->apiCalls, function ($call) { return $call['endpoint'] === '/api/v1/delivery-requests'; })), 'Email retries never create another delivery request.');

$failedRegistry = new TestRegistry(array('db' => new stdClass(), 'config' => $config, 'log' => new TestLog()));
$failedManager = new TestShipmentManager($failedRegistry);
$failedManager->order = $manager->order;
$failedManager->settings = $manager->settings;
$failedManager->apiShouldFail = true;
$apiFailureCaught = false;
try {
	$failedManager->createShipment(23);
} catch (RuntimeException $exception) {
	$apiFailureCaught = true;
}
assertTrue($apiFailureCaught, 'A BOX NOW API failure is reported.');
assertSameValue('Simulated BOX NOW API failure', $failedManager->shipment['creation_error'], 'A BOX NOW API failure is stored for the admin panel.');
$failedManager->apiShouldFail = false;
$retriedShipment = $failedManager->createShipment(23);
assertSameValue('PARCEL-23', $retriedShipment['parcel_id'], 'A failed delivery request can be retried.');

$failedManager->invalidPdf = true;
$invalidPdfCaught = false;
try {
	$failedManager->sendLabelEmail(23);
} catch (RuntimeException $exception) {
	$invalidPdfCaught = true;
}
assertTrue($invalidPdfCaught, 'A non-PDF BOX NOW response is rejected before email sending.');
assertTrue(strpos($failedManager->shipment['label_email_error'], 'PDF') !== false, 'An invalid label response is recorded as a label email error.');

require_once __DIR__ . '/../upload/system/engine/controller.php';
require_once __DIR__ . '/../upload/catalog/controller/event/boxnow.php';
$registry->set('load', new TestLoad());
$registry->set('shipment_manager', $manager);
$eventController = new ControllerEventBoxnow($registry);
$route = 'checkout/order/addOrderHistory';
$output = null;
$beforeLocks = $manager->lockCount;
$failedStatusArgs = array(23, 10);
$eventController->afterOrderHistory($route, $failedStatusArgs, $output);
assertSameValue($beforeLocks, $manager->lockCount, 'Failed payment statuses do not start BOX NOW processing.');
$eligibleStatusArgs = array(23, 2);
$eventController->afterOrderHistory($route, $eligibleStatusArgs, $output);
assertSameValue($beforeLocks + 3, $manager->lockCount, 'A processing status locks shipment creation and both email deliveries.');
$revolutCompletedStatusArgs = array(23, 15);
$eventController->afterOrderHistory($route, $revolutCompletedStatusArgs, $output);
assertSameValue($beforeLocks + 6, $manager->lockCount, 'The configured Revolut completed status invokes the complete automatic BOX NOW flow.');
assertSameValue(1, count(array_filter($manager->apiCalls, function ($call) { return $call['endpoint'] === '/api/v1/delivery-requests'; })), 'The event remains idempotent for an existing parcel.');

require_once __DIR__ . '/../upload/system/engine/model.php';
require_once __DIR__ . '/../upload/catalog/model/extension/shipping/boxnow.php';
$quoteConfig = new TestConfig(array(
	'shipping_boxnow_geo_zone_id' => 0,
	'shipping_boxnow_cost' => '5.00',
	'shipping_boxnow_three_plus_cost' => '2.50',
	'shipping_boxnow_free_total' => '50.00',
	'shipping_boxnow_tax_class_id' => 0,
	'shipping_boxnow_sort_order' => 0,
	'config_tax' => false
));
$quoteCart = new TestQuoteCart();
$quoteRegistry = new TestRegistry(array(
	'load' => new TestLoad(),
	'language' => new Language('en-gb'),
	'db' => new TestQuoteDb(),
	'config' => $quoteConfig,
	'cart' => $quoteCart,
	'tax' => new TestQuoteTax(),
	'currency' => new TestQuoteCurrency(),
	'session' => (object)array('data' => array('currency' => 'EUR'))
));
$quoteModel = new ModelExtensionShippingBoxnow($quoteRegistry);
$address = array('country_id' => 53, 'zone_id' => 0);
$quoteCart->quantity = 2;
$quoteCart->subtotal = 20.00;
$quote = $quoteModel->getQuote($address);
assertSameValue(5.0, $quote['quote']['boxnow']['cost'], 'The regular BOX NOW cost applies below three items.');
$quoteCart->quantity = 3;
$quote = $quoteModel->getQuote($address);
assertSameValue(2.5, $quote['quote']['boxnow']['cost'], 'The configured BOX NOW cost applies at three items.');
$quoteCart->quantity = 5;
$quote = $quoteModel->getQuote($address);
assertSameValue(2.5, $quote['quote']['boxnow']['cost'], 'The configured BOX NOW cost applies above three items.');
$quoteCart->subtotal = 50.00;
$quote = $quoteModel->getQuote($address);
assertSameValue(0, $quote['quote']['boxnow']['cost'], 'Free shipping still takes priority over the three-item cost.');

echo "BOX NOW automation tests passed.\n";
