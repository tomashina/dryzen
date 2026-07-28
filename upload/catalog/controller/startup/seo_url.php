<?php
class ControllerStartupSeoUrl extends Controller {
	private function hydrateRouteFromRequestUri() {
		if ((isset($this->request->get['_route_']) && $this->request->get['_route_'] !== '') || empty($this->request->server['REQUEST_URI'])) {
			return;
		}

		$request_path = parse_url(html_entity_decode($this->request->server['REQUEST_URI'], ENT_QUOTES, 'UTF-8'), PHP_URL_PATH);
		$store_path = parse_url($this->config->get('config_url') ?: HTTP_SERVER, PHP_URL_PATH);

		if ($store_path) {
			$store_path = rtrim($store_path, '/');

			if ($store_path !== '' && strpos($request_path, $store_path) === 0) {
				$request_path = substr($request_path, strlen($store_path));
			}
		}

		$request_path = trim((string)$request_path, '/');

		if ($request_path !== '' && $request_path !== 'index.php') {
			$this->request->get['_route_'] = $request_path;
		}
	}

	public function index() {
		$this->hydrateRouteFromRequestUri();

		// Boost Sitemap advertises this stable root URL in robots.txt. Resolve it
		// without requiring a fragile, manually-created seo_url database row.
		if (isset($this->request->get['_route_']) && trim($this->request->get['_route_'], '/') === 'sitemap-index.xml') {
			$this->request->get['route'] = 'extension/feed/boost_sitemap';
			unset($this->request->get['_route_']);
		}

		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		$this->redirectLegacyQueryUrl();

		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			foreach ($parts as $part) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

					if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id') {
						$this->request->get['route'] = $query->row['query'];
					}
				} else {
					$this->request->get['route'] = 'error/not_found';

					break;
				}
			}

			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
				}
			}
		}
	}

	/**
	 * Permanently consolidate clean, parameter-only OpenCart content URLs.
	 *
	 * This intentionally redirects only a small allowlist and only when no
	 * functional/filter parameters are present. SEO rewrites and AJAX routes
	 * therefore continue to work normally.
	 */
	private function redirectLegacyQueryUrl() {
		$method = isset($this->request->server['REQUEST_METHOD']) ? strtoupper($this->request->server['REQUEST_METHOD']) : 'GET';

		if (!in_array($method, array('GET', 'HEAD'), true) || empty($this->request->server['REQUEST_URI'])) {
			return;
		}

		$request_path = parse_url($this->request->server['REQUEST_URI'], PHP_URL_PATH);

		if (basename((string)$request_path) !== 'index.php' || empty($this->request->get['route'])) {
			return;
		}

		$route = (string)$this->request->get['route'];
		$route_keys = array(
			'common/home' => array(),
			'account/return/add' => array(),
			'extension/blog/home' => array(),
			'information/contact' => array(),
			'information/information' => array('information_id'),
			'product/product' => array('product_id'),
			'product/category' => array('path'),
			'product/manufacturer/info' => array('manufacturer_id'),
		);

		if (!isset($route_keys[$route])) {
			return;
		}

		$allowed_keys = array_merge(array('route'), $route_keys[$route]);
		$request_keys = array_keys($this->request->get);

		if (array_diff($request_keys, $allowed_keys)) {
			return;
		}

		$args = array();

		foreach ($route_keys[$route] as $key) {
			if (!isset($this->request->get[$key]) || $this->request->get[$key] === '') {
				return;
			}

			$args[$key] = $this->request->get[$key];
		}

		if ($route === 'common/home') {
			$canonical = $this->getCanonicalHomeUrl();
		} else {
			$canonical = html_entity_decode(
				$this->url->link($route, http_build_query($args, '', '&'), true),
				ENT_QUOTES,
				'UTF-8'
			);
		}
		$canonical_parts = parse_url($canonical);

		if (empty($canonical_parts['host']) || strpos((string)$canonical_parts['path'], '/index.php') !== false) {
			return;
		}

		$current_scheme = $this->request->server['HTTPS'] ? 'https' : 'http';
		$current_host = isset($this->request->server['HTTP_HOST']) ? $this->request->server['HTTP_HOST'] : '';
		$current_url = $current_scheme . '://' . $current_host . $this->request->server['REQUEST_URI'];

		if (rtrim($canonical, '/') !== rtrim($current_url, '/')) {
			$this->response->redirect($canonical, 301);
		}
	}

	private function getCanonicalHomeUrl() {
		$base_url = rtrim((string)($this->config->get('config_ssl') ?: $this->config->get('config_url')), '/') . '/';
		$current_language_id = (int)$this->config->get('config_language_id');
		$default_language = (string)$this->config->get('config_language');

		$query = $this->db->query(
			"SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '"
			. $this->db->escape($default_language)
			. "' LIMIT 1"
		);
		$default_language_id = $query->num_rows ? (int)$query->row['language_id'] : $current_language_id;

		if ($current_language_id === $default_language_id) {
			return $base_url;
		}

		$query = $this->db->query(
			"SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'language_id="
			. $current_language_id
			. "' AND language_id = '" . $current_language_id
			. "' AND store_id = '" . (int)$this->config->get('config_store_id')
			. "' LIMIT 1"
		);

		return ($query->num_rows && $query->row['keyword'])
			? $base_url . ltrim($query->row['keyword'], '/')
			: $base_url;
	}

	public function rewrite($link) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {
				if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {


                                $url .= '/' . $query->row['keyword'];
                   

						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		// Route aliases (for example /contact and /kontakt) live in seo_url on
		// this store. HuntBee historically checked only its separate hb_url
		// table, which left one language on an index.php?route=... canonical.
		if (isset($data['route']) && $data['route'] === 'extension/blog/home') {
			$route_keyword = $this->getRouteRewriteKeyword($data['route']);

			if ($route_keyword !== '') {
				$url = '/' . ltrim($route_keyword, '/');
			}
		} elseif ($url === '' && isset($data['route'])) {
			if ($data['route'] === 'common/home') {
				$home_keyword = $this->getHomeRewriteKeyword();
				$url = '/' . ltrim($home_keyword, '/');
			} else {
				$route_keyword = $this->getRouteRewriteKeyword($data['route']);

				if ($route_keyword !== '') {
					$url = '/' . ltrim($route_keyword, '/');
				}
			}
		}

		// The explicit comparison keeps the obsolete HuntBee route injection
		// from appending a second copy of aliases already handled above.
		if ($url !== '') {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}

	private function getRouteRewriteKeyword($route) {
		$store_id = (int)$this->config->get('config_store_id');
		$language_id = (int)$this->config->get('config_language_id');
		$route = (string)$route;
		$query = $this->db->query(
			"SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = '"
			. $this->db->escape($route)
			. "' AND language_id = '" . $language_id
			. "' AND store_id = '" . $store_id
			. "' LIMIT 1"
		);

		if ($query->num_rows && $query->row['keyword']) {
			return $query->row['keyword'];
		}

		$query = $this->db->query(
			"SELECT keyword FROM " . DB_PREFIX . "hb_url WHERE route = '"
			. $this->db->escape($route)
			. "' AND language_id = '" . $language_id
			. "' AND store_id = '" . $store_id
			. "' LIMIT 1"
		);

		return ($query->num_rows && $query->row['keyword']) ? $query->row['keyword'] : '';
	}

	private function getHomeRewriteKeyword() {
		$current_language_id = (int)$this->config->get('config_language_id');
		$default_language = (string)$this->config->get('config_language');
		$query = $this->db->query(
			"SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '"
			. $this->db->escape($default_language)
			. "' LIMIT 1"
		);
		$default_language_id = $query->num_rows ? (int)$query->row['language_id'] : $current_language_id;

		if ($current_language_id === $default_language_id) {
			return '';
		}

		$query = $this->db->query(
			"SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'language_id="
			. $current_language_id
			. "' AND language_id = '" . $current_language_id
			. "' AND store_id = '" . (int)$this->config->get('config_store_id')
			. "' LIMIT 1"
		);

		return ($query->num_rows && $query->row['keyword']) ? $query->row['keyword'] : '';
	}
}
