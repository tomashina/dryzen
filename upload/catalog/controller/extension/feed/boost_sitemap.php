<?php
class ControllerExtensionFeedBoostSitemap extends Controller {
	public function index() {
		if (!$this->config->get('feed_boost_sitemap_status')) {
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
			$this->response->addHeader('Content-Type: text/plain; charset=UTF-8');
			$this->response->setOutput('404 Not Found');
			return;
		}

		$directory = dirname(rtrim(DIR_APPLICATION, '/\\')) . DIRECTORY_SEPARATOR . 'sitemaps' . DIRECTORY_SEPARATOR;
		$files = is_dir($directory) ? glob($directory . 'sitemap_*.xml') : [];

		if (!$files) {
			$files = [];
		}

		sort($files, SORT_NATURAL);

		$store_id = (int)$this->config->get('config_store_id');
		$base_url = $this->config->get('config_ssl') ?: $this->config->get('config_url');
		$base_url = rtrim($base_url, '/') . '/';
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<?xml-stylesheet type="text/xsl" href="/sitemaps/sitemap-style.xml"?>';
		$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

		foreach ($files as $path) {
			$file = basename($path);

			if (!is_file($path) || !is_readable($path) || !preg_match('/^sitemap_(\d+)_/', $file, $matches)) {
				continue;
			}

			if ((int)$matches[1] !== $store_id) {
				continue;
			}

			if (strpos($file, '_category_product') !== false || strpos($file, '_manufacturer_product') !== false) {
				continue;
			}

			$time = filemtime($path);
			$output .= '<sitemap>';
			$output .= '<loc>' . $this->escapeXml($base_url . 'sitemaps/' . rawurlencode($file)) . '</loc>';

			if ($time !== false) {
				$output .= '<lastmod>' . date('c', $time) . '</lastmod>';
			}

			$output .= '</sitemap>';
		}

		$output .= '</sitemapindex>';

		$this->response->addHeader('Content-Type: application/xml; charset=UTF-8');
		$this->response->addHeader('Cache-Control: public, max-age=300');
		$this->response->addHeader('X-Content-Type-Options: nosniff');
		$this->response->setOutput($output);
	}

	/**
	 * @param string $value
	 * @return string
	 */
	protected function escapeXml($value) {
		return htmlspecialchars((string)$value, ENT_QUOTES | ENT_XML1, 'UTF-8');
	}
}
