<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		$this->load->language('common/header');

		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		// Keep analytics inert until the visitor explicitly accepts analytics
		// cookies. OCMOD analytics integrations append their markup to this same
		// array before this point, so their pageview and configuration code is
		// covered as well.
		$data['analytics_encoded'] = base64_encode(implode("\n", $data['analytics']));

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$this->applyDryzenSeoDefaults($server);
		$this->document->addStyle('catalog/view/theme/basel/stylesheet/dryzen-global-footer.css?v=20260916f');

		$data['title'] = $this->document->getTitle();

		// The storefront has native hreflang output below. Using a distinct
		// assignment also prevents the obsolete HuntBee header injection from
		// adding duplicate, parameter-unsafe SQL during an OCMOD refresh.
		$data['base'] = (string)$server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['seo_alternates'] = $this->getDryzenSeoAlternates($data['links']);
		$data['dryzen_robots'] = $this->getDryzenRobotsDirective();
		$this->prepareDryzenThemeStyles($data);
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$data['text_close_menu'] = $this->language->get('text_close_menu');
		$data['text_open_menu'] = $this->language->get('text_open_menu');
		$data['text_back_previous'] = $this->language->get('text_back_previous');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_logout'] = $this->language->get('text_logout');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		// The custom consent manager below replaces the obsolete ModulePoints
		// banner injected by OCMOD. This late assignment intentionally overrides
		// the value injected earlier in this controller.
		$data['mpgdpr_cbstatus'] = false;

		return $this->load->view('common/header', $data);
	}

	private function applyDryzenSeoDefaults($server) {
		$title = trim((string) $this->document->getTitle());
		$description = trim((string) $this->document->getDescription());

		if ($description === '') {
			$description = trim((string) $this->config->get('config_meta_description'));

			if ($description !== '') {
				$this->document->setDescription($description);
			}
		}

		$canonical = '';

		foreach ($this->document->getLinks() as $link) {
			if (isset($link['rel'], $link['href']) && $link['rel'] === 'canonical') {
				$canonical = $link['href'];
				break;
			}
		}

		if ($canonical === '' && (!isset($this->request->get['route']) || $this->request->get['route'] === 'common/home')) {
			$canonical = $server;
			$this->document->addLink($canonical, 'canonical');
		}

		if (!method_exists($this->document, 'setOpengraph')) {
			return;
		}

		$opengraph = method_exists($this->document, 'getOpengraph') ? $this->document->getOpengraph() : array();
		$openGraphMap = array();

		foreach ((array) $opengraph as $meta) {
			if (isset($meta['meta'], $meta['content']) && !isset($openGraphMap[$meta['meta']])) {
				$openGraphMap[$meta['meta']] = $meta['content'];
			}
		}

		$defaultImage = $server . 'image/catalog/seo/dryzen-social-1200x630.webp';
		$languageCode = strtolower(str_replace('_', '-', (string) $this->language->get('code')));
		$openGraphLocale = strpos($languageCode, 'en') === 0 ? 'en_GB' : 'hr_HR';
		$defaults = array(
			'og:title' => $title,
			'og:type' => 'website',
			'og:site_name' => (string) $this->config->get('config_name'),
			'og:url' => $canonical,
			'og:description' => $description,
			'og:locale' => $openGraphLocale,
			'og:image' => $defaultImage,
			'og:image:width' => '1200',
			'og:image:height' => '630',
			'og:image:alt' => $this->language->get('text_social_image_alt'),
		);

		foreach ($defaults as $name => $content) {
			if ($content !== '' && !isset($openGraphMap[$name])) {
				$this->document->setOpengraph($name, $content);
				$openGraphMap[$name] = $content;
			}
		}

		if (!method_exists($this->document, 'setTwittercard')) {
			return;
		}

		$twitterCards = method_exists($this->document, 'getTwittercard') ? $this->document->getTwittercard() : array();
		$twitterMap = array();

		foreach ((array) $twitterCards as $meta) {
			if (isset($meta['name'], $meta['content'])) {
				$twitterMap[$meta['name']] = $meta['content'];
			}
		}

		$twitterDefaults = array(
			'twitter:card' => 'summary_large_image',
			'twitter:title' => $openGraphMap['og:title'],
			'twitter:description' => $openGraphMap['og:description'],
			'twitter:image' => $openGraphMap['og:image'],
			'twitter:image:alt' => isset($openGraphMap['og:image:alt']) ? $openGraphMap['og:image:alt'] : $title,
		);

		foreach ($twitterDefaults as $name => $content) {
			if ($content !== '' && !isset($twitterMap[$name])) {
				$this->document->setTwittercard($name, $content);
			}
		}
	}

	private function getDryzenSeoAlternates($links) {
		$canonical = '';

		foreach ((array) $links as $link) {
			if (isset($link['rel'], $link['href']) && $link['rel'] === 'canonical') {
				$canonical = $link['href'];
				break;
			}
		}

		if ($canonical === '') {
			return array();
		}

		$route = isset($this->request->get['route']) ? (string)$this->request->get['route'] : 'common/home';
		$route_query = $this->getDryzenSeoRouteQuery($route);
		$base_url = rtrim((string)($this->config->get('config_ssl') ?: $this->config->get('config_url')), '/') . '/';
		$default_code = (string)$this->config->get('config_language');
		$default_url = '';
		$alternates = array();

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if (empty($language['status'])) {
				continue;
			}

			$href = $this->getDryzenLanguageUrl(
				$route,
				$route_query,
				$language,
				$base_url,
				$default_code
			);

			if ($href === '') {
				continue;
			}

			if (isset($this->request->get['page']) && (int)$this->request->get['page'] > 1) {
				$href .= (strpos($href, '?') === false ? '?' : '&') . 'page=' . (int)$this->request->get['page'];
			}

			$alternates[] = array(
				'hreflang' => $this->formatDryzenHreflang($language['code']),
				'href' => $href,
			);

			if (strtolower($language['code']) === strtolower($default_code)) {
				$default_url = $href;
			}
		}

		$unique_urls = array_unique(array_column($alternates, 'href'));

		if (count($alternates) < 2 || count($unique_urls) !== count($alternates)) {
			$current_code = $this->formatDryzenHreflang($this->language->get('code'));
			$alternates = array(array('hreflang' => $current_code, 'href' => $canonical));

			if (strtolower((string)$this->language->get('code')) === strtolower($default_code)) {
				$alternates[] = array('hreflang' => 'x-default', 'href' => $canonical);
			}

			return $alternates;
		}

		if ($default_url !== '') {
			$alternates[] = array('hreflang' => 'x-default', 'href' => $default_url);
		}

		return $alternates;
	}

	private function getDryzenSeoRouteQuery($route) {
		if ($route === 'information/information' && isset($this->request->get['information_id'])) {
			return 'information_id=' . (int)$this->request->get['information_id'];
		}

		if ($route === 'product/product' && isset($this->request->get['product_id'])) {
			return 'product_id=' . (int)$this->request->get['product_id'];
		}

		if ($route === 'product/manufacturer/info' && isset($this->request->get['manufacturer_id'])) {
			return 'manufacturer_id=' . (int)$this->request->get['manufacturer_id'];
		}

		if ($route === 'product/category' && isset($this->request->get['path'])) {
			$path = explode('_', (string)$this->request->get['path']);

			return 'category_id=' . (int)end($path);
		}

		return '';
	}

	private function getDryzenLanguageUrl($route, $route_query, $language, $base_url, $default_code) {
		$language_id = (int)$language['language_id'];
		$store_id = (int)$this->config->get('config_store_id');

		if ($route === 'common/home') {
			if (strtolower($language['code']) === strtolower($default_code)) {
				return $base_url;
			}

			$query = $this->db->query(
				"SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'language_id="
				. $language_id
				. "' AND language_id = '" . $language_id
				. "' AND store_id = '" . $store_id
				. "' LIMIT 1"
			);

			return ($query->num_rows && $query->row['keyword'])
				? $base_url . ltrim($query->row['keyword'], '/')
				: '';
		}

		if ($route_query !== '') {
			$query = $this->db->query(
				"SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = '"
				. $this->db->escape($route_query)
				. "' AND language_id = '" . $language_id
				. "' AND store_id = '" . $store_id
				. "' LIMIT 1"
			);
		} else {
			$query = $this->db->query(
				"SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = '"
				. $this->db->escape($route)
				. "' AND language_id = '" . $language_id
				. "' AND store_id = '" . $store_id
				. "' LIMIT 1"
			);

			if (!$query->num_rows) {
				$query = $this->db->query(
					"SELECT keyword FROM " . DB_PREFIX . "hb_url WHERE route = '"
					. $this->db->escape($route)
					. "' AND language_id = '" . $language_id
					. "' AND store_id = '" . $store_id
					. "' LIMIT 1"
				);
			}
		}

		return ($query->num_rows && $query->row['keyword'])
			? $base_url . ltrim($query->row['keyword'], '/')
			: '';
	}

	private function formatDryzenHreflang($code) {
		$parts = explode('-', str_replace('_', '-', (string)$code), 2);
		$locale = strtolower($parts[0]);

		if (isset($parts[1]) && $parts[1] !== '') {
			$locale .= '-' . strtoupper($parts[1]);
		}

		return $locale;
	}

	private function getDryzenRobotsDirective() {
		$route = isset($this->request->get['route']) ? (string) $this->request->get['route'] : 'common/home';
		$noIndexPrefixes = array('account/', 'checkout/', 'affiliate/');

		foreach ($noIndexPrefixes as $prefix) {
			if (strpos($route, $prefix) === 0) {
				return 'noindex, nofollow';
			}
		}

		if (in_array($route, array('product/search', 'product/compare'), true)) {
			return 'noindex, follow';
		}

		return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
	}

	private function prepareDryzenThemeStyles(&$data) {
		$mandatoryCss = isset($data['basel_mandatory_css']) ? trim((string) $data['basel_mandatory_css']) : '';
		$dynamicCss = '';

		if (!empty($data['basel_styles_status']) && !empty($data['basel_styles_cache'])) {
			$dynamicCss .= (string) $data['basel_styles_cache'] . "\n";
		}

		if (!empty($data['basel_typo_status']) && !empty($data['basel_fonts_cache'])) {
			$dynamicCss .= (string) $data['basel_fonts_cache'] . "\n";
		}

		if (!empty($data['basel_custom_css_status']) && !empty($data['basel_custom_css'])) {
			$dynamicCss .= (string) $data['basel_custom_css'] . "\n";
		}

		$data['dryzen_mandatory_stylesheet'] = $this->writeDryzenThemeStylesheet($mandatoryCss, 'mandatory');
		$data['dryzen_dynamic_stylesheet'] = $this->writeDryzenThemeStylesheet($dynamicCss, 'dynamic');
	}

	private function writeDryzenThemeStylesheet($css, $group) {
		$css = trim((string) $css);

		if ($css === '') {
			return '';
		}

		$relativeDirectory = 'view/theme/basel/stylesheet/generated/';
		$absoluteDirectory = DIR_APPLICATION . $relativeDirectory;

		if (!is_dir($absoluteDirectory) && !@mkdir($absoluteDirectory, 0775, true) && !is_dir($absoluteDirectory)) {
			return '';
		}

		$fileName = 'dryzen-theme-' . $group . '-' . substr(sha1($css), 0, 16) . '.css';
		$absolutePath = $absoluteDirectory . $fileName;

		if (!is_file($absolutePath)) {
			file_put_contents($absolutePath, $css . "\n", LOCK_EX);
		}

		return 'catalog/' . $relativeDirectory . $fileName;
	}
}
