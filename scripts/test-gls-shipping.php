<?php

// Focused, offline checks for GLS quote defaults, checkout persistence and
// safe pickup-point formatting. No database row or external GLS request is made.

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit('This script can only be run from the command line.');
}

class Controller
{
}

class Model
{
	public $config;
	public $load;
	public $db;
	public $session;
	public $tax;
	public $currency;
	public $language;
	public $cart;
}

if (!defined('DB_PREFIX')) {
	define('DB_PREFIX', 'oc_');
}

class DryzenGlsTestLanguage
{
	private $values = array(
		'text_title' => 'GLS',
		'text_description' => 'GLS paket veličine %s',
		'text_gls_parcel_shop' => 'GLS paket shop',
		'text_gls_parcel_locker' => 'GLS paketomat',
		'text_gls_pickup_location' => 'Mjesto preuzimanja',
		'text_gls_pickup_id' => 'ID lokacije',
		'text_free' => 'Besplatno',
	);

	public function get($key)
	{
		return isset($this->values[$key]) ? $this->values[$key] : $key;
	}
}

class DryzenGlsTestConfig
{
	private $values;

	public function __construct(array $values)
	{
		$this->values = $values;
	}

	public function get($key)
	{
		return array_key_exists($key, $this->values) ? $this->values[$key] : null;
	}
}

class DryzenGlsTestLoad
{
	public function language($route)
	{
		return array();
	}
}

class DryzenGlsTestDb
{
	public $num_rows;
	public $last_sql = '';

	public function __construct($num_rows)
	{
		$this->num_rows = (int)$num_rows;
	}

	public function query($sql)
	{
		$this->last_sql = $sql;

		return (object)array('num_rows' => $this->num_rows);
	}
}

class DryzenGlsTestSession
{
	public $data = array('currency' => 'EUR');
}

class DryzenGlsTestTax
{
	public $last_tax_class_id = null;

	public function calculate($cost, $tax_class_id, $calculate)
	{
		$this->last_tax_class_id = $tax_class_id;

		return (float)$cost;
	}
}

class DryzenGlsTestCurrency
{
	public function format($value, $currency)
	{
		return number_format((float)$value, 2, '.', '') . ' ' . $currency;
	}
}

class DryzenGlsTestCart
{
	private $sub_total;

	public function __construct($sub_total)
	{
		$this->sub_total = (float)$sub_total;
	}

	public function getSubTotal()
	{
		return $this->sub_total;
	}
}

function dryzenGlsFail($message)
{
	fwrite(STDERR, 'FAIL: ' . $message . PHP_EOL);
	exit(1);
}

function dryzenGlsAssertSame($expected, $actual, $message)
{
	if ($expected !== $actual) {
		dryzenGlsFail(
			$message . PHP_EOL
			. 'Expected: ' . var_export($expected, true) . PHP_EOL
			. 'Actual: ' . var_export($actual, true)
		);
	}
}

function dryzenGlsAssertContains($needle, $haystack, $message)
{
	if (strpos($haystack, $needle) === false) {
		dryzenGlsFail($message . PHP_EOL . 'Missing: ' . $needle);
	}
}

function dryzenGlsAssertMatches($pattern, $value, $message)
{
	if (!preg_match($pattern, $value)) {
		dryzenGlsFail($message . PHP_EOL . 'Pattern: ' . $pattern);
	}
}

function dryzenGlsRead($root, $path)
{
	$contents = file_get_contents($root . '/' . $path);

	if ($contents === false) {
		dryzenGlsFail('Unable to read ' . $path);
	}

	return $contents;
}

$root = dirname(__DIR__);

require_once $root . '/upload/catalog/controller/mail/order.php';
require_once $root . '/upload/catalog/model/extension/shipping/glsshop.php';
require_once $root . '/upload/catalog/model/extension/shipping/glspaketomat.php';

$reflection = new ReflectionClass('ControllerMailOrder');
$controller = $reflection->newInstanceWithoutConstructor();
$parse_point = $reflection->getMethod('parseGlsPickupPoint');
$parse_point->setAccessible(true);
$format_shipping = $reflection->getMethod('formatShippingMethodForEmail');
$format_shipping->setAccessible(true);

dryzenGlsAssertSame(
	array(
		'location' => 'GLS Centar, Ilica 1 & 3, Zagreb',
		'point_id' => 'HR-GLS-100',
	),
	$parse_point->invoke($controller, 'GLS <b>Centar</b>, Ilica 1 &amp; 3, Zagreb;HR-GLS-100'),
	'Pickup location and GLS ID should be normalized separately.'
);

dryzenGlsAssertSame(
	array('location' => '', 'point_id' => ''),
	$parse_point->invoke($controller, 'undefined;undefined'),
	'Undefined widget values must not reach an order email.'
);

dryzenGlsAssertSame(
	'GLS Paketomat<br /><br /><strong>GLS paketomat</strong><br /><strong>Mjesto preuzimanja:</strong> GLS Centar, Ilica 1 &amp; 3, Zagreb<br /><strong>ID lokacije:</strong> HR-GLS-100',
	$format_shipping->invoke(
		$controller,
		array(
			'shipping_method' => 'GLS Paketomat',
			'shipping_code' => 'glspaketomat.glspaketomat',
			'gls_ps' => 'GLS Centar, Ilica 1 & 3, Zagreb;HR-GLS-100',
		),
		new DryzenGlsTestLanguage()
	),
	'GLS pickup data in email should be escaped and retain its readable value.'
);

$quote_models = array(
	array('class' => 'ModelExtensionShippingGlsshop', 'prefix' => 'shipping_glsshop', 'quote' => 'glsshop', 'code' => 'glsshop.glsshop', 'sort_order' => 3),
	array('class' => 'ModelExtensionShippingGlspaketomat', 'prefix' => 'shipping_glspaketomat', 'quote' => 'glspaketomat', 'code' => 'glspaketomat.glspaketomat', 'sort_order' => 2),
);

foreach ($quote_models as $definition) {
	$config_values = array(
		'config_currency' => 'EUR',
		'config_tax' => false,
		$definition['prefix'] . '_geo_zone_id' => 6,
		$definition['prefix'] . '_default_size' => 'S',
		$definition['prefix'] . '_cost_xs' => '4.50',
		$definition['prefix'] . '_cost_s' => '5.50',
		$definition['prefix'] . '_cost_m' => '6.50',
		$definition['prefix'] . '_cost_l' => '8.50',
		$definition['prefix'] . '_cost_xl' => '10.50',
		$definition['prefix'] . '_free_total' => '50.00',
		$definition['prefix'] . '_tax_class_id' => 0,
		$definition['prefix'] . '_sort_order' => $definition['sort_order'],
	);

	$model_class = $definition['class'];
	$model = new $model_class();
	$model->config = new DryzenGlsTestConfig($config_values);
	$model->load = new DryzenGlsTestLoad();
	$model->db = new DryzenGlsTestDb(1);
	$model->session = new DryzenGlsTestSession();
	$model->tax = new DryzenGlsTestTax();
	$model->currency = new DryzenGlsTestCurrency();
	$model->language = new DryzenGlsTestLanguage();
	$model->cart = new DryzenGlsTestCart(49.99);

	$quote = $model->getQuote(array('country_id' => 53, 'zone_id' => 0));
	$line = $quote['quote'][$definition['quote']];

	dryzenGlsAssertSame($definition['code'], $line['code'], 'Each GLS module must expose its stable checkout code.');
	dryzenGlsAssertSame(5.5, $line['cost'], 'Default package size S must use the confirmed final 5.50 EUR cost.');
	dryzenGlsAssertSame(0, $line['tax_class_id'], 'The quote must follow DryZen final-price mode without an added tax class.');
	dryzenGlsAssertSame('5.50 EUR', $line['text'], 'The S quote must display the final 5.50 EUR price.');
	dryzenGlsAssertSame('GLS paket veličine S', $line['title'], 'The selected package tier must be visible in the quote.');
	dryzenGlsAssertSame($definition['sort_order'], $quote['sort_order'], 'The configured shipping sort order must be retained.');
	dryzenGlsAssertContains("geo_zone_id = '6'", $model->db->last_sql, 'The quote must enforce the Croatia geo zone.');

	foreach (array('XS' => 4.50, 'S' => 5.50, 'M' => 6.50, 'L' => 8.50, 'XL' => 10.50) as $size => $expected_cost) {
		$tier_config = $config_values;
		$tier_config[$definition['prefix'] . '_default_size'] = $size;
		$model->config = new DryzenGlsTestConfig($tier_config);
		$tier_quote = $model->getQuote(array('country_id' => 53, 'zone_id' => 0));
		$tier_line = $tier_quote['quote'][$definition['quote']];

		dryzenGlsAssertSame($expected_cost, $tier_line['cost'], 'The ' . $size . ' tier must use its confirmed final price.');
		dryzenGlsAssertSame(number_format($expected_cost, 2, '.', '') . ' EUR', $tier_line['text'], 'The ' . $size . ' tier must display its final price.');
	}

	$model->config = new DryzenGlsTestConfig($config_values);
	$model->cart = new DryzenGlsTestCart(50.00);
	$free_quote = $model->getQuote(array('country_id' => 53, 'zone_id' => 0));
	$free_line = $free_quote['quote'][$definition['quote']];
	dryzenGlsAssertSame(0.0, $free_line['cost'], 'GLS shipping must become free exactly at the 50.00 EUR threshold.');
	dryzenGlsAssertSame('Besplatno', $free_line['text'], 'A free GLS quote must be clearly labelled as free.');

	$model->cart = new DryzenGlsTestCart(50.01);
	$above_threshold_quote = $model->getQuote(array('country_id' => 53, 'zone_id' => 0));
	dryzenGlsAssertSame(0.0, $above_threshold_quote['quote'][$definition['quote']]['cost'], 'GLS shipping must remain free above 50.00 EUR.');

	$disabled_free_shipping_config = $config_values;
	$disabled_free_shipping_config[$definition['prefix'] . '_free_total'] = '0';
	$model->config = new DryzenGlsTestConfig($disabled_free_shipping_config);
	$model->cart = new DryzenGlsTestCart(100.00);
	$paid_quote = $model->getQuote(array('country_id' => 53, 'zone_id' => 0));
	dryzenGlsAssertSame(5.5, $paid_quote['quote'][$definition['quote']]['cost'], 'A zero threshold must disable free GLS shipping.');

	$model->db = new DryzenGlsTestDb(0);
	dryzenGlsAssertSame(array(), $model->getQuote(array('country_id' => 14, 'zone_id' => 0)), 'The GLS pickup quote must be unavailable outside the configured geo zone.');
}

$quick_confirm = dryzenGlsRead($root, 'upload/catalog/controller/extension/quickcheckout/confirm.php');
$standard_confirm = dryzenGlsRead($root, 'upload/catalog/controller/checkout/confirm.php');
$shipping_controller = dryzenGlsRead($root, 'upload/catalog/controller/extension/quickcheckout/shipping_method.php');
$shipping_template = dryzenGlsRead($root, 'upload/catalog/view/theme/basel/template/extension/quickcheckout/shipping_method.twig');
$order_model = dryzenGlsRead($root, 'upload/catalog/model/checkout/order.php');
$success_controller = dryzenGlsRead($root, 'upload/catalog/controller/checkout/success.php');
$admin_order_model = dryzenGlsRead($root, 'upload/admin/model/sale/order.php');
$admin_order_controller = dryzenGlsRead($root, 'upload/admin/controller/sale/order.php');
$cod_model = dryzenGlsRead($root, 'upload/catalog/model/extension/payment/cod.php');
$migration = dryzenGlsRead($root, 'database/migrations/20261007_gls_pickup_shipping.sql');
$free_shipping_migration = dryzenGlsRead($root, 'database/migrations/20261007_gls_free_shipping.sql');
$gls_admin_controllers = array(
	dryzenGlsRead($root, 'upload/admin/controller/extension/shipping/glsshop.php'),
	dryzenGlsRead($root, 'upload/admin/controller/extension/shipping/glspaketomat.php')
);

foreach (array($quick_confirm, $standard_confirm) as $confirm_source) {
	dryzenGlsAssertContains('glsshop.glsshop', $confirm_source, 'Both checkout confirms must recognize GLS ParcelShop.');
	dryzenGlsAssertContains('glspaketomat.glspaketomat', $confirm_source, 'Both checkout confirms must recognize GLS parcel lockers.');
	dryzenGlsAssertContains('gls_ps_shipping_code', $confirm_source, 'A GLS point must remain tied to the selected shipping method.');
	dryzenGlsAssertContains("order_data['gls_ps']", $confirm_source, 'The validated GLS point must be passed to the order model.');
}

dryzenGlsAssertContains('saveGlsPoint', $shipping_controller, 'QuickCheckout must provide the dedicated GLS point endpoint.');
dryzenGlsAssertContains("request->post['gls_ps']", $shipping_controller, 'Validation must accept the sanitized posted point if it overtakes the asynchronous save.');
dryzenGlsAssertContains("array_key_exists('gls_ps'", $shipping_controller, 'An explicitly empty point must not fall back to an older hidden session value.');
dryzenGlsAssertContains("'parcel-shop'", $shipping_controller, 'GLS ParcelShop must use the parcel-shop widget filter.');
dryzenGlsAssertContains("'parcel-locker'", $shipping_controller, 'GLS parcel lockers must use the parcel-locker widget filter.');
dryzenGlsAssertContains('https://map.gls-hungary.com/widget/gls-dpm.js', $shipping_template, 'QuickCheckout must load the official GLS DPM widget.');
dryzenGlsAssertContains('clearGlsPoint(shippingCode, true)', $shipping_template, 'Only an invalid widget result should send an explicit asynchronous clear.');
dryzenGlsAssertContains("removeAttr('name')", $shipping_template, 'Inactive GLS point inputs must not submit a duplicate gls_ps value.');
dryzenGlsAssertContains("attr('name', 'gls_ps')", $shipping_template, 'Only the active GLS point input may submit gls_ps.');

dryzenGlsAssertContains('persistGlsPickupPoint', $order_model, 'The catalog order model must persist GLS pickup data.');
dryzenGlsAssertContains("'gls_ps'", $order_model, 'The catalog order model must return GLS pickup data.');
dryzenGlsAssertContains('throw new \\RuntimeException', $order_model, 'Missing GLS order storage must fail closed instead of silently losing the selected point.');
dryzenGlsAssertContains('$order_id = $this->db->getLastId();', $order_model, 'The BOX NOW OCMOD add-order anchor must remain intact.');
dryzenGlsAssertContains('// Validate minimum quantity requirements.', $quick_confirm, 'The BOX NOW quick-confirm validation anchor must remain intact.');
dryzenGlsAssertContains("order_data['products'] = array();", $quick_confirm, 'The BOX NOW order-data OCMOD anchor must remain intact.');
dryzenGlsAssertContains("data['gls_ps']", $success_controller, 'Checkout success must clear the GLS point.');
dryzenGlsAssertContains("data['gls_ps_shipping_code']", $success_controller, 'Checkout success must clear the GLS shipping-code binding.');
dryzenGlsAssertContains("'gls_ps'", $admin_order_model, 'The admin order model must return GLS pickup data.');
dryzenGlsAssertContains('formatGlsPickupMethod', $admin_order_controller, 'The admin order view must format GLS pickup data safely.');
dryzenGlsAssertContains('text_gls_shop_note', $cod_model, 'Cash on delivery must explain ParcelShop payment.');
dryzenGlsAssertContains('text_gls_locker_note', $cod_model, 'Cash on delivery must explain parcel-locker payment.');

foreach ($gls_admin_controllers as $admin_controller) {
	dryzenGlsAssertContains('public function install()', $admin_controller, 'Each GLS module must install its database support and defaults through OpenCart.');
	dryzenGlsAssertContains('ensureGlsOrderColumn', $admin_controller, 'Each GLS module install hook must create the pickup-point order column.');
}

foreach (array('glsshop', 'glspaketomat') as $extension) {
	dryzenGlsAssertContains("'shipping', '" . $extension . "'", $migration, 'The migration must register ' . $extension . '.');
	dryzenGlsAssertContains('extension/shipping/' . $extension, $migration, 'The migration must grant access to ' . $extension . ' settings.');
	$prefix = 'shipping_' . $extension;
	$expected_tiers = array('xs' => '4.50', 's' => '5.50', 'm' => '6.50', 'l' => '8.50', 'xl' => '10.50');

	foreach ($expected_tiers as $size => $final_cost) {
		$key = preg_quote($prefix . '_cost_' . $size, '/');
		$cost = preg_quote($final_cost, '/');
		dryzenGlsAssertMatches("/'" . $key . "'(?: AS `key`)?\\s*,\\s*'" . $cost . "'/", $migration, 'The migration must contain the confirmed final ' . strtoupper($size) . ' price.');
	}

	dryzenGlsAssertContains($prefix . "_default_size', 'S'", $migration, 'Zero-dimension products must default to package size S.');
	dryzenGlsAssertContains($prefix . "_tax_class_id', '0'", $migration, 'GLS prices must use DryZen final-price mode without added tax.');
	dryzenGlsAssertContains($prefix . "_geo_zone_id', '6'", $migration, 'GLS pickup must use the Croatia geo zone.');
	dryzenGlsAssertContains($prefix . "_status', '1'", $migration, 'Both GLS pickup methods must be enabled by default.');
	$free_total_key = preg_quote($prefix . '_free_total', '/');
	dryzenGlsAssertMatches("/'" . $free_total_key . "'(?: AS `key`)?\\s*,\\s*'50\\.00'/", $free_shipping_migration, 'Both GLS pickup methods must become free from 50.00 EUR.');
}

dryzenGlsAssertContains('`gls_ps` text NULL', $migration, 'The migration must add oc_order.gls_ps.');

fwrite(STDOUT, "GLS shipping checks passed.\n");
