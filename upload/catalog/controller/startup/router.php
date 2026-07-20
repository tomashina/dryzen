<?php
class ControllerStartupRouter extends Controller {
	public function index() {
		// Route
		if (isset($this->request->get['route']) && $this->request->get['route'] != 'startup/router') {
			$route = $this->request->get['route'];
		} else {
			$route = $this->config->get('action_default');
		}
		
		// Sanitize the call
		$route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$route);
		$page_cache_key = $this->getDryzenPageCacheKey($route);

		if ($page_cache_key) {
			$page_cache = $this->cache->get($page_cache_key);

			if (is_array($page_cache) && isset($page_cache['html'], $page_cache['expires']) && $page_cache['expires'] >= time()) {
				$this->response->addHeader('X-DryZen-Page-Cache: HIT');
				$this->response->setOutput($page_cache['html']);

				return null;
			}

			if ($page_cache !== false) {
				$this->cache->delete($page_cache_key);
			}

			$this->response->addHeader('X-DryZen-Page-Cache: MISS');
		}
		
		// Trigger the pre events
		$result = $this->event->trigger('controller/' . $route . '/before', array(&$route, &$data));
		
		if (!is_null($result)) {
			return $result;
		}
		
		// We dont want to use the loader class as it would make an controller callable.
		$action = new Action($route);
		
		// Any output needs to be another Action object.
		$output = $action->execute($this->registry); 
		
		// Trigger the post events
		$result = $this->event->trigger('controller/' . $route . '/after', array(&$route, &$data, &$output));
		
		if (!is_null($result)) {
			return $result;
		}

		if ($page_cache_key) {
			$html = $this->response->getOutput();

			if (is_string($html) && $html !== '' && $this->isDryzenCacheableResponse()) {
				$this->cache->set($page_cache_key, array(
					'expires' => time() + 300,
					'html' => $html,
				));
			}
		}
		
		return $output;
	}

	private function getDryzenPageCacheKey($route) {
		$cacheable_routes = array(
			'common/home',
			'information/contact',
			'information/information',
			'information/sitemap',
			'product/category',
			'product/manufacturer',
			'product/manufacturer/info',
			'product/product',
			'product/special',
		);

		if (!in_array($route, $cacheable_routes, true)) {
			return '';
		}

		$request_method = isset($this->request->server['REQUEST_METHOD'])
			? strtoupper($this->request->server['REQUEST_METHOD'])
			: 'GET';

		if ($request_method !== 'GET' || $this->request->post || $this->customer->isLogged()) {
			return '';
		}

		if (!empty($this->request->server['HTTP_AUTHORIZATION']) || !empty($this->request->server['PHP_AUTH_USER'])) {
			return '';
		}

		$cache_control = isset($this->request->server['HTTP_CACHE_CONTROL']) ? $this->request->server['HTTP_CACHE_CONTROL'] : '';
		$pragma = isset($this->request->server['HTTP_PRAGMA']) ? $this->request->server['HTTP_PRAGMA'] : '';

		if (stripos($cache_control, 'no-cache') !== false || stripos($pragma, 'no-cache') !== false) {
			return '';
		}

		if ($this->config->get('config_customer_online')) {
			return '';
		}

		$session_keys = array(
			'api_id',
			'cart',
			'comment',
			'compare',
			'coupon',
			'customer',
			'error',
			'guest',
			'order_id',
			'payment_address',
			'payment_method',
			'reward',
			'shipping_address',
			'shipping_method',
			'success',
			'vouchers',
			'warning',
			'wishlist',
		);

		foreach ($session_keys as $session_key) {
			if (!empty($this->session->data[$session_key])) {
				return '';
			}
		}

		$cart_query = $this->db->query("SELECT cart_id FROM " . DB_PREFIX . "cart WHERE api_id = '0' AND customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "' LIMIT 1");

		if ($cart_query->num_rows) {
			return '';
		}

		$query_data = $this->request->get;
		unset($query_data['route'], $query_data['_route_']);

		foreach (array_keys($query_data) as $key) {
			if (in_array($key, array('fbclid', 'gclid', 'msclkid', 'tracking'), true) || strpos($key, 'utm_') === 0) {
				unset($query_data[$key]);
			}
		}

		$query_data = $this->normalizeDryzenCacheData($query_data);
		$query_json = json_encode($query_data);

		if ($query_json === false || strlen($query_json) > 1024) {
			return '';
		}

		$page_version = $this->cache->get('dryzen.page.version');

		if ($page_version === false) {
			$page_version = '1';
		}

		$cookie_variation = array(
			'cookieconsent_status' => hash('sha256', isset($this->request->cookie['cookieconsent_status']) ? (string)$this->request->cookie['cookieconsent_status'] : ''),
			'mpcookie_preferencesdisable' => hash('sha256', isset($this->request->cookie['mpcookie_preferencesdisable']) ? (string)$this->request->cookie['mpcookie_preferencesdisable'] : ''),
		);
		$variation = array(
			'route' => $route,
			'query' => $query_data,
			'store_id' => (int)$this->config->get('config_store_id'),
			'language_id' => (int)$this->config->get('config_language_id'),
			'customer_group_id' => (int)$this->config->get('config_customer_group_id'),
			'currency' => isset($this->session->data['currency']) ? $this->session->data['currency'] : '',
			'https' => !empty($this->request->server['HTTPS']),
			'cookies' => $cookie_variation,
			'version' => (string)$page_version,
		);

		return 'dryzen.page.' . md5(json_encode($variation));
	}

	private function isDryzenCacheableResponse() {
		if (!method_exists($this->response, 'getHeaders')) {
			return false;
		}

		foreach ($this->response->getHeaders() as $header) {
			if (preg_match('/^HTTP\/\S+\s+([0-9]{3})/i', $header, $matches) && (int)$matches[1] !== 200) {
				return false;
			}

			if (stripos($header, 'Location:') === 0 || stripos($header, 'Status:') === 0) {
				return false;
			}
		}

		return true;
	}

	private function normalizeDryzenCacheData($data) {
		if (!is_array($data)) {
			return $data;
		}

		ksort($data);

		foreach ($data as $key => $value) {
			$data[$key] = $this->normalizeDryzenCacheData($value);
		}

		return $data;
	}
}
