<?php

class MarketingTracking {
	const COOKIE_LIFETIME = 2592000;

	private $db;
	private $request;
	private $resolved = array();
	private $processed = false;

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
	}

	public static function isValidCode($code) {
		return is_string($code) && preg_match('/\A[A-Za-z0-9._~-]{1,64}\z/', $code) === 1;
	}

	public function hasMarketingConsent() {
		if (!isset($this->request->cookie['cc_cookie']) || !is_string($this->request->cookie['cc_cookie'])) {
			return false;
		}

		$consent = json_decode(html_entity_decode($this->request->cookie['cc_cookie'], ENT_QUOTES, 'UTF-8'), true);

		return is_array($consent) && isset($consent['categories']) && is_array($consent['categories'])
			&& in_array('marketing', $consent['categories'], true);
	}

	public function processRequest() {
		if ($this->processed) {
			return;
		}

		$this->processed = true;
		$consent = $this->hasMarketingConsent();

		// Checkout controllers all read this cookie, so reject stale attribution
		// here before any checkout or payment extension can use it.
		if (isset($this->request->cookie['tracking'])
			&& (!$consent || !$this->resolveCode($this->request->cookie['tracking']))) {
			$this->clearCookie();
		}

		$method = isset($this->request->server['REQUEST_METHOD'])
			? strtoupper($this->request->server['REQUEST_METHOD']) : 'GET';

		if ($method !== 'GET' || !isset($this->request->get['tracking'])) {
			return;
		}

		$referral = $this->resolveCode($this->request->get['tracking']);

		if (!$referral) {
			return;
		}

		// This is an aggregate link-click counter, including repeated requests.
		// It stores no visitor identifier and also works without cookie consent.
		if ($referral['marketing_id']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "marketing` SET clicks = (clicks + 1) WHERE marketing_id = '" . (int)$referral['marketing_id'] . "'");
		}

		if ($consent) {
			$this->setAttribution($referral['code']);
		}
	}

	// Used after a visitor accepts marketing on the landing page. The initial
	// GET already counted the click, so this request only saves attribution.
	public function capture($code) {
		if (!$this->hasMarketingConsent()) {
			if (isset($this->request->cookie['tracking'])) {
				$this->clearCookie();
			}

			return false;
		}

		$referral = $this->resolveCode($code);

		if (!$referral) {
			return false;
		}

		$this->setAttribution($referral['code']);

		return true;
	}

	private function resolveCode($code) {
		if (!self::isValidCode($code)) {
			return false;
		}

		if (array_key_exists($code, $this->resolved)) {
			return $this->resolved[$code];
		}

		$marketing = $this->db->query("SELECT marketing_id, code FROM `" . DB_PREFIX . "marketing` WHERE code = '" . $this->db->escape($code) . "' LIMIT 1");

		if ($marketing->num_rows && self::isValidCode($marketing->row['code'])) {
			return $this->resolved[$code] = array(
				'code' => $marketing->row['code'],
				'marketing_id' => (int)$marketing->row['marketing_id']
			);
		}

		$affiliate = $this->db->query("SELECT ca.customer_id, ca.tracking FROM `" . DB_PREFIX . "customer_affiliate` ca INNER JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = ca.customer_id WHERE ca.tracking = '" . $this->db->escape($code) . "' AND ca.status = '1' AND c.status = '1' LIMIT 1");

		if ($affiliate->num_rows && self::isValidCode($affiliate->row['tracking'])) {
			return $this->resolved[$code] = array(
				'code' => $affiliate->row['tracking'],
				'marketing_id' => 0
			);
		}

		return $this->resolved[$code] = false;
	}

	private function setAttribution($code) {
		$this->writeCookie($code, time() + self::COOKIE_LIFETIME);
		$this->request->cookie['tracking'] = $code;
	}

	private function clearCookie() {
		$this->writeCookie('', time() - 3600);
		unset($this->request->cookie['tracking']);
	}

	protected function writeCookie($value, $expires) {
		$https = isset($this->request->server['HTTPS']) ? $this->request->server['HTTPS'] : false;
		$secure = $https === true || $https === 1 || $https === '1' || $https === 'on';

		setcookie('tracking', $value, array(
			'expires' => $expires,
			'path' => '/',
			'secure' => $secure,
			'samesite' => 'Lax'
		));
	}
}
