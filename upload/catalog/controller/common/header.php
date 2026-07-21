<?php
class ControllerCommonHeader extends Controller {
	public function index() {
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

		$this->load->language('common/header');

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
		$defaults = array(
			'og:title' => $title,
			'og:type' => 'website',
			'og:site_name' => (string) $this->config->get('config_name'),
			'og:url' => $canonical,
			'og:description' => $description,
			'og:locale' => 'hr_HR',
			'og:image' => $defaultImage,
			'og:image:width' => '1200',
			'og:image:height' => '630',
			'og:image:alt' => 'DryZen proizvodi i tim',
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

		$code = str_replace('_', '-', (string) $this->config->get('config_language'));
		$parts = explode('-', $code, 2);
		$locale = strtolower($parts[0]);

		if (isset($parts[1]) && $parts[1] !== '') {
			$locale .= '-' . strtoupper($parts[1]);
		}

		return array(
			array('hreflang' => $locale, 'href' => $canonical),
			array('hreflang' => 'x-default', 'href' => $canonical),
		);
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
