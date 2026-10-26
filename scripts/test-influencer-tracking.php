<?php

// Standalone regression tests. All referral and order data stays in memory.
if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit('This script can only be run from the command line.');
}

define('DB_PREFIX', 'oc_');

require_once dirname(__DIR__) . '/upload/system/engine/registry.php';
require_once dirname(__DIR__) . '/upload/system/engine/model.php';
require_once dirname(__DIR__) . '/upload/system/library/config.php';
require_once dirname(__DIR__) . '/upload/system/library/marketingtracking.php';
require_once dirname(__DIR__) . '/upload/admin/model/marketing/marketing.php';
require_once dirname(__DIR__) . '/upload/admin/model/extension/report/marketing.php';

function influencerAssert($condition, $message) {
	if (!$condition) {
		throw new RuntimeException($message);
	}
}

function influencerSame($expected, $actual, $message) {
	if ($expected !== $actual) {
		throw new RuntimeException($message . '\nExpected: ' . var_export($expected, true) . '\nActual: ' . var_export($actual, true));
	}
}

class InfluencerTestResult {
	public $row;
	public $rows;
	public $num_rows;

	public function __construct($rows = array()) {
		$this->rows = $rows;
		$this->row = $rows ? $rows[0] : array();
		$this->num_rows = count($rows);
	}
}

class InfluencerTrackingTestDb {
	public $queries = array();
	public $clicks = array('ana' => 0, 'ivan' => 0);

	public function escape($value) {
		return addslashes((string)$value);
	}

	public function query($sql) {
		$this->queries[] = $sql;

		if (stripos($sql, 'marketing') !== false) {
			preg_match("/\\bcode\\s*=\\s*'([^']*)'/i", $sql, $matches);
			$code = isset($matches[1]) ? stripslashes($matches[1]) : '';

			if (stripos(ltrim($sql), 'UPDATE') === 0) {
				if (preg_match("/\\bmarketing_id\\s*=\\s*'([12])'/i", $sql, $id_match)) {
					$code = $id_match[1] === '1' ? 'ana' : 'ivan';
				}

				if (array_key_exists($code, $this->clicks)) {
					$this->clicks[$code]++;
				}

				return new InfluencerTestResult();
			}

			if (array_key_exists($code, $this->clicks)) {
				return new InfluencerTestResult(array(array('marketing_id' => $code === 'ana' ? 1 : 2, 'code' => $code)));
			}

			return new InfluencerTestResult();
		}

		if (stripos($sql, 'customer_affiliate') !== false) {
			influencerAssert(stripos($sql, 'customer`') !== false || preg_match('/\\bcustomer\\s+c\\b/i', $sql), 'Affiliate validation also checks the customer record.');
			influencerAssert(preg_match('/(?:ca|a|customer_affiliate)\\.status\\s*=\\s*\'?1\'?/i', $sql), 'Only approved affiliates can set attribution.');
			influencerAssert(preg_match('/(?:c|customer)\\.status\\s*=\\s*\'?1\'?/i', $sql), 'Only active affiliate customers can set attribution.');

			if (strpos($sql, "'affiliate-approved'") !== false) {
				return new InfluencerTestResult(array(array('customer_id' => 7, 'tracking' => 'affiliate-approved')));
			}

			return new InfluencerTestResult();
		}

		throw new RuntimeException('Unexpected tracking query: ' . $sql);
	}
}

class InfluencerTestTracking extends MarketingTracking {
	public $cookie_writes = array();

	protected function writeCookie($value, $expires) {
		$this->cookie_writes[] = array('value' => $value, 'expires' => $expires);
	}
}

function influencerFixture($get = array(), $cookies = array()) {
	$registry = new Registry();
	$request = new stdClass();
	$request->get = $get;
	$request->cookie = $cookies;
	$request->server = array('HTTPS' => true, 'HTTP_HOST' => 'dryzen.test');
	$db = new InfluencerTrackingTestDb();
	$registry->set('request', $request);
	$registry->set('db', $db);

	return array(new InfluencerTestTracking($registry), $request, $db);
}

function influencerConsent($categories = array('necessary', 'marketing')) {
	return json_encode(array('categories' => $categories));
}

function influencerRunTrackingTests() {
	foreach (array('ana', 'ANA_01.test~link', str_repeat('x', 64)) as $code) {
		influencerAssert(MarketingTracking::isValidCode($code), 'URL-safe referral codes up to 64 characters are accepted.');
	}

	foreach (array('', ' ana', 'ana ', 'ana link', 'ana%20link', "ana\n", "' OR 1=1", str_repeat('x', 65), array('ana'), null, true, 123) as $code) {
		influencerAssert(!MarketingTracking::isValidCode($code), 'Malformed or non-string referral codes are rejected.');
	}

	foreach (array(null, '', '{bad', array('categories' => array('marketing')), influencerConsent(array('necessary', 'analytics'))) as $consent) {
		list($tracking) = influencerFixture(array(), array('cc_cookie' => $consent));
		influencerAssert(!$tracking->hasMarketingConsent(), 'Missing, malformed, and analytics-only consent cannot enable referral cookies.');
	}

	list($tracking) = influencerFixture(array(), array('cc_cookie' => htmlspecialchars(influencerConsent(), ENT_QUOTES, 'UTF-8')));
	influencerAssert($tracking->hasMarketingConsent(), 'The HTML-escaped consent cookie produced by OpenCart is understood.');

	list($tracking, $request, $db) = influencerFixture(array('tracking' => 'ana'));
	$tracking->processRequest();
	influencerSame(1, $db->clicks['ana'], 'A valid link counts its landing request before consent.');
	influencerAssert(empty($request->cookie['tracking']), 'A landing request without consent leaves no referral cookie for checkout.');
	foreach ($tracking->cookie_writes as $cookie) {
		influencerSame('', $cookie['value'], 'No positive referral cookie is written without consent.');
	}

	$before = time();
	list($tracking, $request, $db) = influencerFixture(array('tracking' => 'ana'), array('cc_cookie' => influencerConsent()));
	$tracking->processRequest();
	influencerSame('ana', $request->cookie['tracking'], 'The current landing request can already attribute checkout after consent.');
	influencerSame(1, $db->clicks['ana'], 'A consented landing increments the influencer once.');
	influencerSame(1, count($tracking->cookie_writes), 'A consented valid landing writes one referral cookie.');
	$expires = $tracking->cookie_writes[0]['expires'];
	influencerAssert($expires >= $before + 30 * 86400 && $expires <= time() + 30 * 86400, 'Attribution expires 30 days after the valid referral.');
	$tracking->processRequest();
	influencerSame(1, $db->clicks['ana'], 'Repeated startup execution within one request cannot double-count a click.');

	list($tracking, $request, $db) = influencerFixture(array('tracking' => 'ana'), array('cc_cookie' => influencerConsent()));
	$request->server['REQUEST_METHOD'] = 'POST';
	$tracking->processRequest();
	influencerSame(array('ana' => 0, 'ivan' => 0), $db->clicks, 'A POST carrying a referral query does not count as another link landing.');
	influencerAssert(empty($request->cookie['tracking']), 'A POST query cannot silently change the selected influencer.');

	list($tracking, $request, $db) = influencerFixture(array('tracking' => 'ivan'), array('cc_cookie' => influencerConsent(), 'tracking' => 'ana'));
	$tracking->processRequest();
	influencerSame('ivan', $request->cookie['tracking'], 'The most recent valid referral replaces a previous influencer.');
	influencerSame(array('ana' => 0, 'ivan' => 1), $db->clicks, 'Only the newly visited influencer receives the click.');

	foreach (array('unknown-influencer', array('ivan'), "ana\r\nSet-Cookie: bad", str_repeat('x', 65)) as $invalid_code) {
		list($tracking, $request, $db) = influencerFixture(array('tracking' => $invalid_code), array('cc_cookie' => influencerConsent(), 'tracking' => 'ana'));
		$tracking->processRequest();
		influencerSame('ana', $request->cookie['tracking'], 'An unknown or malformed referral cannot erase an existing valid influencer.');
		influencerSame(array('ana' => 0, 'ivan' => 0), $db->clicks, 'An invalid referral contributes no influencer clicks.');
	}

	list($tracking, $request, $db) = influencerFixture(array(), array('cc_cookie' => influencerConsent(), 'tracking' => 'ana'));
	$tracking->processRequest();
	influencerSame('ana', $request->cookie['tracking'], 'A valid existing referral remains usable on following pages.');
	influencerSame(array(), $tracking->cookie_writes, 'Following pages do not extend the original attribution expiry.');
	influencerSame(array('ana' => 0, 'ivan' => 0), $db->clicks, 'Following pages are not counted as new referral landings.');

	list($tracking, $request) = influencerFixture(array(), array('cc_cookie' => influencerConsent()));
	$tracking->processRequest();
	influencerAssert(empty($request->cookie['tracking']), 'An expired or absent browser cookie cannot attribute a later visit.');

	foreach (array('unknown-influencer', array('ana')) as $invalid_cookie) {
		list($tracking, $request) = influencerFixture(array(), array('cc_cookie' => influencerConsent(), 'tracking' => $invalid_cookie));
		$tracking->processRequest();
		influencerAssert(empty($request->cookie['tracking']), 'Unknown and malformed stored referrals are cleared before checkout.');
	}

	list($tracking, $request) = influencerFixture(array(), array('cc_cookie' => influencerConsent(array('necessary')), 'tracking' => 'ana'));
	$tracking->processRequest();
	influencerAssert(empty($request->cookie['tracking']), 'Revoking marketing consent removes attribution immediately.');
	influencerSame('', $tracking->cookie_writes[0]['value'], 'Revoking consent clears the browser referral cookie.');
	influencerAssert($tracking->cookie_writes[0]['expires'] < time(), 'A revoked referral cookie expires in the past.');

	list($tracking, $request, $db) = influencerFixture(array('tracking' => 'affiliate-approved'), array('cc_cookie' => influencerConsent()));
	$tracking->processRequest();
	influencerSame('affiliate-approved', $request->cookie['tracking'], 'Existing approved customer affiliate links remain supported.');
	influencerSame(array('ana' => 0, 'ivan' => 0), $db->clicks, 'A customer affiliate referral does not create influencer clicks.');

	list($tracking, $request) = influencerFixture(array('tracking' => 'affiliate-disabled'), array('cc_cookie' => influencerConsent()));
	$tracking->processRequest();
	influencerAssert(empty($request->cookie['tracking']), 'Inactive or unapproved affiliate links do not establish attribution.');

	list($tracking, $request, $db) = influencerFixture(array(), array('cc_cookie' => influencerConsent()));
	influencerAssert($tracking->capture('ana'), 'The consent callback accepts a valid influencer.');
	influencerSame('ana', $request->cookie['tracking'], 'The consent callback makes attribution available immediately.');
	influencerSame(array('ana' => 0, 'ivan' => 0), $db->clicks, 'Accepting consent cannot count a landing twice.');
	influencerAssert(!$tracking->capture('unknown-influencer'), 'The consent callback rejects unknown codes.');
	influencerSame('ana', $request->cookie['tracking'], 'A rejected consent callback leaves existing attribution intact.');
	influencerAssert(!$tracking->capture(array('ana')), 'The consent callback safely rejects non-scalar input.');

	list($tracking, $request) = influencerFixture();
	influencerAssert(!$tracking->capture('ana'), 'The consent callback cannot create attribution without marketing consent.');
	influencerAssert(empty($request->cookie['tracking']), 'A denied consent callback leaves checkout unattributed.');
}

class InfluencerModelTestDb {
	public $queries = array();
	private $connection;

	public function __construct() {
		$this->connection = new PDO('sqlite::memory:');
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->connection->sqliteCreateFunction('CONCAT', function() { return implode('', func_get_args()); });
	}

	public function escape($value) {
		return str_replace("'", "''", (string)$value);
	}

	public function query($sql) {
		$this->queries[] = $sql;
		$statement = $this->connection->query($sql);
		return new InfluencerTestResult($statement->columnCount() ? $statement->fetchAll(PDO::FETCH_ASSOC) : array());
	}

	public function lastSql() {
		return end($this->queries);
	}
}

function influencerIndexById($rows) {
	$result = array();
	foreach ($rows as $row) {
		$result[(int)$row['marketing_id']] = $row;
	}
	return $result;
}

function influencerRunModelTests() {
	$db = new InfluencerModelTestDb();
	$db->query('CREATE TABLE oc_marketing (marketing_id INTEGER PRIMARY KEY, name TEXT, description TEXT, code TEXT, clicks INTEGER, date_added TEXT)');
	$db->query('CREATE TABLE `oc_order` (order_id INTEGER PRIMARY KEY, marketing_id INTEGER, order_status_id INTEGER, total DECIMAL(15,4), currency_code TEXT, currency_value DECIMAL(15,8), date_added TEXT, firstname TEXT, lastname TEXT)');
	$db->query('CREATE TABLE oc_customer_affiliate (customer_id INTEGER PRIMARY KEY, tracking TEXT)');
	$db->query('CREATE TABLE oc_order_status (order_status_id INTEGER, language_id INTEGER, name TEXT)');
	$db->query("INSERT INTO oc_marketing VALUES (1, 'Ana', '', 'ana', 10, '2026-10-01 10:00:00'), (2, 'Anita', '', 'anita', 0, '2026-10-02 10:00:00'), (3, 'Ivan', '', 'ivan', 5, '2026-10-03 10:00:00'), (4, 'Empty', '', 'empty', 0, '2026-10-04 10:00:00'), (5, 'Draft only', '', 'draft', 0, '2026-10-05 10:00:00')");
	$db->query("INSERT INTO `oc_order` VALUES (101, 1, 5, 100, 'EUR', 1, '2026-10-10 12:00:00', 'Ana', 'Buyer'), (102, 1, 5, 40, 'USD', 1.25, '2026-10-11 12:00:00', 'Second', 'Buyer'), (103, 1, 7, 900, 'EUR', 1, '2026-10-12 12:00:00', 'Cancelled', 'Buyer'), (104, 1, 0, 20, 'EUR', 1, '2026-10-13 12:00:00', 'Draft', 'Buyer'), (105, 1, 1, 10, 'EUR', 1, '2026-10-14 12:00:00', 'Pending', 'Buyer'), (201, 3, 5, 75, 'EUR', 1, '2026-10-15 12:00:00', 'Ivan', 'Buyer'), (501, 5, 0, 0, 'EUR', 1, '2026-10-16 12:00:00', 'Draft', 'Only')");
	$db->query("INSERT INTO oc_customer_affiliate VALUES (9, 'affiliate-approved')");
	$db->query("INSERT INTO oc_order_status VALUES (1, 1, 'Pending'), (5, 1, 'Complete'), (7, 1, 'Cancelled'), (5, 2, 'Dovršeno')");
	$config = new Config();
	$config->set('config_complete_status', array(5));
	$config->set('config_language_id', 2);
	$registry = new Registry();
	$registry->set('db', $db);
	$registry->set('config', $config);
	$model = new ModelMarketingMarketing($registry);
	$report = new ModelExtensionReportMarketing($registry);

	$rows = influencerIndexById($model->getMarketings());
	influencerSame(2, (int)$rows[1]['orders'], 'Only configured successful orders count as influencer conversions.');
	influencerSame(140.0, (float)$rows[1]['revenue'], 'Revenue sums stored base-currency totals even when order display currencies differ.');
	influencerSame(0, (int)$rows[4]['orders'], 'Influencers with no orders have zero conversions.');
	influencerSame(0.0, (float)$rows[4]['revenue'], 'Influencers with no orders have zero revenue.');

	$config->set('config_complete_status', array(5, '5', 1, 0, -3));
	$rows = influencerIndexById($model->getMarketings());
	influencerSame(3, (int)$rows[1]['orders'], 'Configured positive statuses count once while zero and negative statuses are excluded.');
	influencerSame(150.0, (float)$rows[1]['revenue'], 'Conversion revenue uses the same status selection as the conversion count.');
	influencerAssert(strpos($db->lastSql(), 'IN (5,1)') !== false, 'Status IDs are normalised and deduplicated before the query.');

	$config->set('config_complete_status', array());
	$rows = influencerIndexById($model->getMarketings());
	influencerSame(0, (int)$rows[1]['orders'], 'An empty successful-status configuration counts no conversions.');
	influencerSame(0.0, (float)$rows[1]['revenue'], 'An empty successful-status configuration produces zero revenue.');
	$config->set('config_complete_status', array(5));

	$filter = array('filter_name' => 'An');
	influencerSame(2, count($model->getMarketings($filter)), 'The name filter matches an influencer prefix.');
	influencerSame(2, (int)$model->getTotalMarketings($filter), 'Pagination totals use the same name-prefix filter as the list.');
	$filter['filter_code'] = 'ana';
	influencerSame(1, count($model->getMarketings($filter)), 'Name and code filters combine instead of overriding one another.');
	influencerSame(1, (int)$model->getTotalMarketings($filter), 'Combined filter pagination totals match displayed influencers.');
	$rows = $model->getMarketings(array('filter_date_added' => '2026-10-01'));
	influencerSame(1, count($rows), 'The creation-date filter selects the influencer record.');
	influencerSame(2, (int)$rows[0]['orders'], 'An influencer creation-date filter does not accidentally limit later conversions.');
	influencerSame(0, count($model->getMarketings(array('filter_name' => "' OR 1=1 --"))), 'An injected filter is safely treated as literal text.');
	$model->getMarketings(array('sort' => 'm.name; DROP TABLE oc_marketing', 'order' => 'DESC; SELECT 1', 'start' => -20, 'limit' => 0));
	influencerAssert(strpos($db->lastSql(), 'ORDER BY m.name ASC LIMIT 0,20') !== false, 'Unsafe sort parameters and invalid pagination use safe defaults.');
	influencerSame(2, count($model->getMarketings(array('start' => 1, 'limit' => 2))), 'Influencer pagination limits actual returned records.');

	$rows = $model->getMarketingOrders(1);
	influencerSame(array(105, 103, 102, 101), array_map('intval', array_column($rows, 'order_id')), 'Influencer order details include placed orders by newest ID and exclude checkout drafts.');
	influencerSame(4, $model->getTotalMarketingOrders(1), 'Order-detail pagination uses the same placed-order scope.');
	influencerSame('Pending', $rows[0]['order_status'], 'Missing translated statuses fall back to the default language.');
	influencerSame('Dovršeno', $rows[2]['order_status'], 'Available translated statuses are used in order details.');
	influencerSame(1.25, (float)$rows[2]['currency_value'], 'Order details retain the original exchange rate for correct display.');
	influencerSame(0, count($model->getMarketingOrders('0 OR 1=1')), 'An injected influencer ID is cast to an integer.');
	influencerAssert($model->hasMarketingHistory(1), 'An influencer with link clicks is protected as historical data.');
	influencerAssert($model->hasMarketingHistory(5), 'An influencer used by a draft order is also protected as historical data.');
	influencerAssert(!$model->hasMarketingHistory(4), 'An unused influencer has no history.');

	influencerAssert($model->isTrackingCodeInUse('ana'), 'Another influencer cannot reuse an existing link code.');
	influencerAssert(!$model->isTrackingCodeInUse('ana', 1), 'Editing an influencer can keep its own code.');
	influencerAssert($model->isTrackingCodeInUse('affiliate-approved'), 'Influencer codes cannot collide with existing customer affiliate codes.');
	influencerAssert(!$model->isTrackingCodeInUse('available-new-code'), 'An unused referral code remains available.');
	$model->editMarketing(1, array('name' => "Ana's profile", 'description' => 'Updated contact', 'code' => 'changed-code'));
	$influencer = $model->getMarketing(1);
	influencerSame('ana', $influencer['code'], 'Editing influencer information cannot invalidate previously issued links.');
	influencerSame("Ana's profile", $influencer['name'], 'Influencer display information remains editable.');

	$rows = influencerIndexById($report->getMarketing());
	influencerSame(2, (int)$rows[1]['orders'], 'The existing marketing report defaults to the same configured conversion statuses.');
	influencerSame(140.0, (float)$rows[1]['total'], 'The existing marketing report uses the same conversion revenue.');
	$rows = influencerIndexById($report->getMarketing(array('filter_date_start' => '2026-10-11', 'filter_date_end' => '2026-10-11')));
	influencerSame(1, (int)$rows[1]['orders'], 'The marketing report date range filters order conversion dates.');
	influencerSame(40.0, (float)$rows[1]['total'], 'Report revenue is scoped to the same order date range.');
	$rows = influencerIndexById($report->getMarketing(array('filter_order_status_id' => 7)));
	influencerSame(1, (int)$rows[1]['orders'], 'An explicitly selected report status can inspect cancelled orders.');
	influencerSame(900.0, (float)$rows[1]['total'], 'An explicit report status scopes its revenue consistently.');
	$config->set('config_complete_status', array());
	$rows = influencerIndexById($report->getMarketing());
	influencerSame(0, (int)$rows[1]['orders'], 'The report also counts no conversions when successful statuses are unconfigured.');
	influencerSame(0.0, (float)$rows[1]['total'], 'The report has no conversion revenue when successful statuses are unconfigured.');
}

try {
	influencerRunTrackingTests();
	influencerRunModelTests();
	echo "Influencer tracking tests passed.\n";
} catch (Throwable $error) {
	fwrite(STDERR, 'FAIL: ' . $error->getMessage() . PHP_EOL);
	exit(1);
}
