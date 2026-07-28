<?php 
//==============================================
// XML Sitemap OC 3.0_v2.1x
// Author 	: OpenCartBoost
// Email 	: support@opencartboost.com
// Website 	: http://www.opencartboost.com
//==============================================

class ControllerExtensionFeedBoostSitemap extends Controller {
	private $error = [];
	private $languages = [];
	private $stores = [];
	private $files = [];
	private $directory;
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * @param mixed $registry
	 * @return void
	 */
	public function __construct($registry) {
		$this->registry = $registry;
		$this->directory = dirname(rtrim(DIR_CATALOG, '/\\')) . DIRECTORY_SEPARATOR . 'sitemaps' . DIRECTORY_SEPARATOR;
		
		$this->load->language('extension/feed/boost_sitemap');
		
		$this->load->model('setting/store');
		$this->load->model('setting/setting');
		
		$stores = $this->model_setting_store->getStores();
		
		$this->stores[] = [
			'store_id' => 0,
			'name' => 'Default',
			'url' => HTTPS_CATALOG
		];
		
		foreach ($stores as $store) {
			$ssl = $this->model_setting_setting->getSettingValue('config_secure', $store['store_id']);
			
			$this->stores[] = [
				'store_id' => $store['store_id'],
				'name' => $store['name'],
				'url' => ($ssl ? $store['ssl'] : $store['url'])
			];
		}
		
		$this->load->model('localisation/language');
		
		$languages = $this->model_localisation_language->getLanguages();
		
		foreach ($languages as $language) {
			$this->languages[] = $language;
		}
	}
	
	/**
	 * Install
	 * 
	 * @access public
	 * @return void
	 */
	public function install() {
		$this->ensureSitemapDirectory();
		
		$this->load->model('extension/feed/boost_sitemap');
		
		$this->model_extension_feed_boost_sitemap->install();
	}
	
	/**
	 * Uninstall
	 * 
	 * @access public
	 * @return void
	 */
	public function uninstall() {
		$dir = $this->directory;

		if (is_dir($dir)) {
			$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

			foreach ($files as $file) {
				if ($file->isDir()) {
					@rmdir($file->getRealPath());
				} else {
					@unlink($file->getRealPath());
				}
			}

			@rmdir($dir);
		}

		$this->load->model('extension/feed/boost_sitemap');

		$this->model_extension_feed_boost_sitemap->uninstall();
	}

	/**
	 * Index
	 * 
	 * @access public
	 * @return void
	 */
	public function index() {
		$this->document->setTitle($this->language->get('heading_title_etitle'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('feed_boost_sitemap', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->get['continue'])) {
				$this->response->redirect($this->url->link('extension/feed/boost_sitemap', 'user_token=' . $this->session->data['user_token'], true));
			} else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true));
			}
			
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/feed/boost_sitemap', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['action'] = $this->url->link('extension/feed/boost_sitemap', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true);
		$data['continue'] = $this->url->link('extension/feed/boost_sitemap', 'user_token=' . $this->session->data['user_token'] . '&continue=1', true);
		$data['delete'] = $this->url->link('extension/feed/boost_sitemap/delete', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['generate'] = $this->url->link('extension/feed/boost_sitemap/generate', 'user_token=' . $this->session->data['user_token'], true);
		//$data['keyword'] = $this->url->link('extension/feed/boost_sitemap/keyword', 'user_token=' . $this->session->data['user_token'], true);
		$data['custom_link'] = str_replace('&amp;', '&', $this->url->link('extension/feed/boost_sitemap/custom_link', 'user_token=' . $this->session->data['user_token'], true));
		$data['delete_custom_link'] = str_replace('&amp;', '&', $this->url->link('extension/feed/boost_sitemap/delete_custom_link', 'user_token=' . $this->session->data['user_token'], true));

		$data['data_feed'] = [];
		
		foreach ($this->stores as $store) {
			$data['data_feed'][] = [
				'store_name' => $store['name'],
				'feed' => $this->link($store['url'], 'extension/feed/boost_sitemap', '')
			];
		}
		
		$boostsitemap_config = [
			'feed_boost_sitemap_status',
			'feed_boost_sitemap_item_limit'
		];
		
		foreach ($boostsitemap_config as $conf_sitemap1) {
			if (isset($this->request->post[$conf_sitemap1])) {
				$data[$conf_sitemap1] = $this->request->post[$conf_sitemap1];
			} else {
				$data[$conf_sitemap1] = $this->config->get($conf_sitemap1);
			}
		}
		
		if (isset($this->request->post['feed_boost_sitemap_item'])) {
			$data['feed_boost_sitemap_item'] = $this->request->post['feed_boost_sitemap_item'];
		} elseif ($this->config->get('feed_boost_sitemap_item')) {
			$data['feed_boost_sitemap_item'] = $this->config->get('feed_boost_sitemap_item');
		} else {
			$data['feed_boost_sitemap_item'] = [];
		}
		
		$data['items'] = [
			'product' => 'Product Sitemaps',
			'category' => 'Category Sitemaps',
			'manufacturer' => 'Manufacturer Sitemaps',
			'information' => 'Information Sitemaps',
			'custom_link' => 'Custom Link Sitemaps'
		];
		
		/* New Journal3 Blog*/
		if(defined('JOURNAL3_INSTALLED')){
            $data['items']['journal3blogpost'] = 'Journal3 Blog Post Sitemaps';
            $data['items']['journal3blogcategory'] = 'Journal3 Blog Category Sitemaps';
        }
        		
		$data['xml_files'] = [];
		
		foreach ($this->getRecursiveFiles($this->directory) as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) == 'xml') {
				foreach ($this->stores as $store) {
					$path = basename($file);
					$explode = explode('_', $path);
					
					if (isset($explode[1])) {
						$store_id = $explode[1];
						
						if ($store_id == $store['store_id']) {
							$data['xml_files'][] = [
								'url' => $store['url'] . 'sitemaps/' . $path,
								'path' => $path,
								'size' => $this->getFileSize(filesize($file)),
								'datetime' => date($this->language->get('datetime_format'), filemtime($file))
							];
						}
					}
				}
			}
		}
		
		$data['text_overwrite'] = $this->language->get('text_overwrite');
		$data['stores'] = $this->stores;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/feed/boost_sitemap', $data));
	}
	
	/**
	 * Validate
	 * 
	 * @access protected
	 * @return void
	 */
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/feed/boost_sitemap')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['feed_boost_sitemap_item_limit'])) {
			$limit = (int)$this->request->post['feed_boost_sitemap_item_limit'];

			if ($limit < 1 || $limit > 50000) {
				$this->error['warning'] = $this->language->get('error_item_limit');
			}
		}

		return !$this->error;
	}
	
	/**
	 * Get files recursively
	 * 
	 * @access protected
	 * @param string $dir
	 * @param array &$results
	 * @return array
	 */
	protected function getRecursiveFiles($dir, &$results = []) {
		if (!is_dir($dir) || !is_readable($dir)) {
			return $results;
		}

		$files = scandir($dir);
		
		if ($files === false) {
			return $results;
		}

		foreach ($files as $key => $value) {
			$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
			if (!is_dir($path)) {
				$results[] = $path;
			} else if ($value != "." && $value != "..") {
				$this->getRecursiveFiles($path, $results);
				$results[] = $path;
			}
		}
		
		return $results;
	}
	
	/**
	 * Ensure that the public sitemap directory exists and is writable.
	 *
	 * @throws RuntimeException
	 */
	protected function ensureSitemapDirectory() {
		if (!is_dir($this->directory) && !@mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
			throw new RuntimeException(sprintf($this->language->get('error_directory_create'), $this->directory));
		}

		if (!is_writable($this->directory)) {
			@chmod($this->directory, 0775);
			clearstatcache(true, $this->directory);
		}

		if (!is_writable($this->directory)) {
			throw new RuntimeException(sprintf($this->language->get('error_directory_write'), $this->directory));
		}
	}

	/**
	 * Write through a temporary file so existing read-only files can be replaced
	 * safely and crawlers never receive a partially-written sitemap.
	 *
	 * @param string $file_name
	 * @param string $output
	 * @throws RuntimeException
	 */
	protected function writeSitemap($file_name, $output) {
		$this->ensureSitemapDirectory();

		$file_name = basename($file_name);
		$target = $this->directory . $file_name;

		if (strlen($output) > 50 * 1024 * 1024) {
			throw new RuntimeException(sprintf($this->language->get('error_file_size'), $target));
		}

		$temporary = tempnam($this->directory, '.boost-sitemap-');

		if ($temporary === false) {
			throw new RuntimeException(sprintf($this->language->get('error_file_write'), $target));
		}

		$bytes = @file_put_contents($temporary, $output, LOCK_EX);

		if ($bytes === false || $bytes !== strlen($output)) {
			@unlink($temporary);
			throw new RuntimeException(sprintf($this->language->get('error_file_write'), $target));
		}

		@chmod($temporary, 0644);

		if (!@rename($temporary, $target)) {
			// Windows cannot rename over an existing target. Keep this fallback
			// after the complete temporary file has already been written.
			if (is_file($target)) {
				@unlink($target);
			}

			if (!@rename($temporary, $target)) {
				@unlink($temporary);
				throw new RuntimeException(sprintf($this->language->get('error_file_replace'), $target));
			}
		}

		$this->files[] = 'sitemaps/' . $file_name;
	}

	/**
	 * Remove old split files only after their replacements were generated.
	 *
	 * @param array $items
	 * @return void
	 */
	protected function removeStaleSitemapFiles($items) {
		$type_map = [
			'product' => 'product',
			'category' => 'category',
			'category_product' => 'category_product',
			'manufacturer' => 'manufacturer',
			'manufacturer_product' => 'manufacturer_product',
			'information' => 'information',
			'custom_link' => 'custom_link',
			'journal3blogpost' => 'blog_post',
			'journal3blogcategory' => 'blog_category'
		];
		$generated = array_map('basename', $this->files);

		foreach (glob($this->directory . 'sitemap_*.xml') ?: [] as $file) {
			$name = basename($file);

			if (in_array($name, $generated, true)) {
				continue;
			}

			foreach ($items as $item) {
				if (!isset($type_map[$item])) {
					continue;
				}

				$type = preg_quote($type_map[$item], '/');
				$language_pattern = $item === 'custom_link' ? '' : '_\d+';

				if (preg_match('/^sitemap_\d+' . $language_pattern . '_' . $type . '(?:_\d+)?\.xml$/', $name)) {
					@unlink($file);
					break;
				}
			}
		}
	}

	/**
	 * Category/manufacturer product paths duplicate the canonical product URL and
	 * dilute sitemap quality. Keep only the canonical product sitemap.
	 *
	 * @return void
	 */
	protected function removeLegacyDuplicateSitemaps() {
		$patterns = [
			$this->directory . 'sitemap_*_*_category_product*.xml',
			$this->directory . 'sitemap_*_*_manufacturer_product*.xml'
		];

		foreach ($patterns as $pattern) {
			foreach (glob($pattern) ?: [] as $file) {
				@unlink($file);
			}
		}
	}

	/**
	 * XML-escape dynamic content while normalising already escaped OpenCart URLs.
	 *
	 * @param string $value
	 * @return string
	 */
	protected function escapeXml($value) {
		$value = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');

		return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
	}

	/**
	 * Return a trustworthy W3C timestamp or an empty string for invalid dates.
	 *
	 * @param string $value
	 * @return string
	 */
	protected function formatLastmod($value) {
		if (!$value || substr((string)$value, 0, 10) === '0000-00-00') {
			return '';
		}

		$timestamp = strtotime($value);

		return $timestamp === false ? '' : date('c', $timestamp);
	}

	/**
	 * Attach a human-readable stylesheet without changing sitemap semantics.
	 * This also avoids Firefox rendering multilingual xhtml:link entries as a
	 * single unformatted block of text.
	 *
	 * @return string
	 */
	protected function getXmlHeader() {
		return '<?xml version="1.0" encoding="UTF-8"?>'
			. '<?xml-stylesheet type="text/xsl" href="/sitemaps/sitemap-style.xml"?>';
	}

	/**
	 * @param bool $with_images
	 * @return string
	 */
	protected function getUrlsetOpen($with_images = false) {
		$output = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml"';

		if ($with_images) {
			$output .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
		}

		return $output . '>';
	}

	/**
	 * @param string $value
	 * @return string
	 */
	protected function getLastmodXml($value) {
		$lastmod = $this->formatLastmod($value);

		return $lastmod ? '<lastmod>' . $this->escapeXml($lastmod) . '</lastmod>' : '';
	}

	/**
	 * Google currently supports only image:loc in image sitemap metadata.
	 *
	 * @param string $url
	 * @return string
	 */
	protected function getImageXml($url) {
		return $url ? '<image:image><image:loc>' . $this->escapeXml($url) . '</image:loc></image:image>' : '';
	}

	/**
	 * Sitemaps may only contain canonical URLs from the current store host.
	 *
	 * @param string $url
	 * @param string $store_url
	 * @return bool
	 */
	protected function isStoreUrl($url, $store_url) {
		$url_parts = parse_url($url);
		$store_parts = parse_url($store_url);

		return isset($url_parts['scheme'], $url_parts['host'], $store_parts['host'])
			&& in_array(strtolower($url_parts['scheme']), ['http', 'https'], true)
			&& strtolower($url_parts['host']) === strtolower($store_parts['host']);
	}

	/**
	 * Resolve a URL that is safe to advertise as canonical in a sitemap.
	 * Parameter fallbacks indicate a missing SEO alias and must be omitted.
	 *
	 * @param array $store
	 * @param string $route
	 * @param string $args
	 * @param int $language_id
	 * @return string
	 */
	protected function getCanonicalSitemapUrl($store, $route, $args, $language_id) {
		$url = $this->link($store['url'], $route, $args, $store['store_id'], $language_id);
		$parts = parse_url(html_entity_decode($url, ENT_QUOTES, 'UTF-8'));

		if (
			!$this->isStoreUrl($url, $store['url'])
			|| empty($parts['path'])
			|| strpos($parts['path'], '/index.php') !== false
			|| !empty($parts['query'])
		) {
			return '';
		}

		return $url;
	}

	/**
	 * Add all reciprocal language variants for multilingual search and AI discovery.
	 *
	 * @param array $store
	 * @param string $route
	 * @param string $args
	 * @return string
	 */
	protected function getAlternateLinks($store, $route, $args = '') {
		if (count($this->languages) < 2) {
			return '';
		}

		$links = [];
		$default_url = '';
		$default_code = strtolower(str_replace('_', '-', (string)$this->config->get('config_language')));

		foreach ($this->languages as $language) {
			$code = strtolower(str_replace('_', '-', $language['code']));
			$href = $this->getCanonicalSitemapUrl($store, $route, $args, $language['language_id']);

			if ($href === '') {
				return '';
			}

			$links[] = ['code' => $code, 'href' => $href];

			if ($code === $default_code || strpos($code, $default_code . '-') === 0) {
				$default_url = $href;
			}
		}

		if (count(array_unique(array_column($links, 'href'))) !== count($links)) {
			return '';
		}

		$output = '';

		foreach ($links as $link) {
			$output .= '<xhtml:link rel="alternate" hreflang="' . $this->escapeXml($link['code']) . '" href="' . $this->escapeXml($link['href']) . '"/>';
		}

		if ($default_url) {
			$output .= '<xhtml:link rel="alternate" hreflang="x-default" href="' . $this->escapeXml($default_url) . '"/>';
		}

		return $output;
	}

	/**
	 * Get file size
	 * 
	 * @access protected
	 * @param float $size
	 * @return string
	 */
 	protected function getFileSize($size) {
		$suffix = [
			'B',
			'KB',
			'MB',
			'GB',
			'TB',
			'PB',
			'EB',
			'ZB',
			'YB'
		];

		$i = 0;

		while (($size / 1024) > 1) {
			$size = $size / 1024;
			$i++;
		}
		
		return number_format($size, 2, '.', ',') . ' ' . $suffix[$i];
	}
	
	/**
	 * Delete xml files
	 * 
	 * @access public
	 * @return void
	 */
	public function delete() {
		$json = [];
		$this->response->addHeader('Content-Type: application/json; charset=UTF-8');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['selected'])) {
				$selected = $this->request->post['selected'];
				
				foreach ($selected as $path) {
					$path = basename($path);

					if (preg_match('/^sitemap_\d+(?:_\d+)?_[a-z0-9_]+(?:_\d+)?\.xml$/', $path) && is_file($this->directory . $path)) {
						@unlink($this->directory . $path);
					}
				}
			}
		} elseif (isset($this->error['warning'])) {
			$json['error'] = $this->error['warning'];
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	/**
	 * Generate xml files
	 * 
	 * @access public
	 * @return void
	 */
	public function generate() {
		$json = [];

		$this->response->addHeader('Content-Type: application/json; charset=UTF-8');

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$json['error'] = $this->language->get('error_permission');
		} elseif (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		} else {
			$limit = isset($this->request->post['feed_boost_sitemap_item_limit']) ? (int)$this->request->post['feed_boost_sitemap_item_limit'] : 0;

			if ($limit < 1 || $limit > 50000) {
				$json['error'] = $this->language->get('error_item_limit');
				$this->response->setOutput(json_encode($json));
				return;
			}

			try {
				$this->ensureSitemapDirectory();
				$this->load->model('setting/setting');

				if (isset($this->request->post['selected'])) {
					unset($this->request->post['selected']);
				}

				$this->request->post['feed_boost_sitemap_item_limit'] = $limit;
				$this->model_setting_setting->editSetting('feed_boost_sitemap', $this->request->post);

				$items = isset($this->request->post['feed_boost_sitemap_item']) ? $this->request->post['feed_boost_sitemap_item'] : [];

				if (defined('JOURNAL3_INSTALLED')) {
					$this->load->model('extension/feed/boost_sitemap');

					if (in_array('journal3blogpost', $items)) {
						$this->generateJournal3BlogPostSitemap($limit);
					}

					if (in_array('journal3blogcategory', $items)) {
						$this->model_extension_feed_boost_sitemap->alterTableBlogCategory();
						$this->generateJournal3BlogCategorySitemap($limit);
					}
				}

				if (in_array('product', $items)) {
					$this->generateProductSitemap($limit);
				}

				if (in_array('category', $items)) {
					$this->generateCategorySitemap($limit);
				}

				if (in_array('information', $items)) {
					$this->generateInformationSitemap($limit);
				}

				if (in_array('manufacturer', $items)) {
					$this->generateManufacturerSitemap($limit);
				}

				if (in_array('custom_link', $items)) {
					$this->generateCustomLinkSitemap($limit);
				}

				$this->removeStaleSitemapFiles($items);
				$this->removeLegacyDuplicateSitemaps();
				$json['success'] = sprintf($this->language->get('text_generate_success'), count($this->files));
			} catch (Throwable $e) {
				$this->log->write('Boost Sitemap: ' . $e->getMessage());
				$json['error'] = sprintf($this->language->get('error_generate'), $e->getMessage());
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Get products of category recursively
	 * 
	 * @access public
	 * @param int $parent_id
	 * @param string $current_path
	 * @param int $language_id
	 * @param int $store_id
	 * @return array
	 */
	public function getCategories($parent_id, $current_path, $language_id, $store_id) {
		$this->load->model('extension/feed/boost_sitemap');
		
		$output = [];
		$results = $this->model_extension_feed_boost_sitemap->getCategories([
			'store_id' => $store_id,
			'parent_id' => $parent_id, 
			'language_id' => $language_id
		]);

		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
				
			$products = $this->model_extension_feed_boost_sitemap->getProducts([
				'store_id' => $store_id,
				'filter_category_id' => $result['category_id'], 
				'language_id' => $language_id
			]);

						foreach ($products as $product) {
				$output[] = [
					'product_id' => $product['product_id'],
					'name' => $product['name'],
					'image' => $product['image'],
					'date_modified' => $product['date_modified'],
					'path' => $new_path
				];
			}
			
			foreach ($this->getCategories($result['category_id'], $new_path, $language_id, $store_id) as $child) {
				$output[] = $child;
			}
		}

		return $output;
	}
	
	/**
	 * Generate category to product sitemap
	 * 
	 * @access protected
	 * @param int $limit
	 * @return void
	 */
	protected function generateCategoryToProductSitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		$width = 500;
		$height = 500;
		$conf_width = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width');
		$conf_height = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height');
        
		if ($conf_width) {
			$width = $conf_width;
		}
		
        if ($conf_height) { 
			$height = $conf_height;
		}
				
		foreach ($this->stores as $store) {
			foreach ($this->languages as $language) {
				$categories = $this->getCategories(0, '', $language['language_id'], $store['store_id']);
				$results = array_chunk($categories, $limit);
				$count = 1;
				
				foreach ($results as $key => $result) {
					$output  = $this->getXmlHeader();
					$output .= $this->getUrlsetOpen(true);
							
					foreach ($result as $product) {
						$output .= '<url>';
						$args = 'path=' . $product['path'] . '&product_id=' . $product['product_id'];
						$output .= '<loc>' . $this->escapeXml($this->link($store['url'], 'product/product', $args, $store['store_id'], $language['language_id'])) . '</loc>';
						$output .= $this->getAlternateLinks($store, 'product/product', $args);
						$output .= $this->getLastmodXml($product['date_modified']);
						
						if ($product['image']) {
							$output .= $this->getImageXml($this->model_extension_feed_boost_sitemap->resizeImage($product['image'], $width, $height, $store['url']));
						}
						
						$output .= '</url>';
					}
							
					$output .= '</urlset>';
					
					if (count($categories) <= $limit) {
						$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_category_product.xml';
					} else {
						$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_category_product_' . $count . '.xml';
					}
					
					$count++;
								
					$this->writeSitemap($file_name, $output);
				}
			}
		}
	}
	
	/**
	 * Generate manufacturer to product sitemap
	 * 
	 * @access protected
	 * @param int $limit
	 * @return void
	 */
	protected function generateManufacturerToProductSitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		$width = 500;
		$height = 500;
		$conf_width = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width');
		$conf_height = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height');
        
		if ($conf_width) {
			$width = $conf_width;
		}
		
        if ($conf_height) { 
			$height = $conf_height;
		}
		
		foreach ($this->stores as $store) {
			$products = [];
			$manufacturers = $this->model_extension_feed_boost_sitemap->getManufacturers(['store_id' => $store['store_id']]);
			
			foreach ($this->languages as $language) {
				foreach ($manufacturers as $manufacturer) {
					$params = [
						'store_id' => $store['store_id'],
						'language_id' => $language['language_id'],
						'filter_manufacturer_id' => $manufacturer['manufacturer_id']
					];
					
					foreach ($this->model_extension_feed_boost_sitemap->getProducts($params) as $product) {
						$products[$language['language_id']][] = $product;
					}
				}
			}
			
			foreach ($this->languages as $language) {
				if (isset($products[$language['language_id']])) {
					$results = array_chunk($products[$language['language_id']], $limit);
					$count = 1;
					
					foreach ($results as $result) {
						$output  = $this->getXmlHeader();
						$output .= $this->getUrlsetOpen(true);
								
						foreach ($result as $product) {
							$output .= '<url>';
								$args = 'product_id=' . $product['product_id'];
								$output .= '<loc>' . $this->escapeXml($this->link($store['url'], 'product/product', $args, $store['store_id'], $language['language_id'])) . '</loc>';
								$output .= $this->getAlternateLinks($store, 'product/product', $args);
								$output .= $this->getLastmodXml($product['date_modified']);
							
							if ($product['image']) {
									$output .= $this->getImageXml($this->model_extension_feed_boost_sitemap->resizeImage($product['image'], $width, $height, $store['url']));
							}
							
							$output .= '</url>';
						}
								
						$output .= '</urlset>';
						
						if (count($products[$language['language_id']]) <= $limit) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_manufacturer_product.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_manufacturer_product_' . $count . '.xml';
						}
						
						$count++;
									
						$this->writeSitemap($file_name, $output);
					}
				}
			}
		}
	}
	
	/**
	 * Generate information sitemap
	 * 
	 * @access protected
	 * @param int $limit
	 * @return void
	 */
	protected function generateInformationSitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		foreach ($this->stores as $store) {
			foreach ($this->languages as $language) {
				$total = $this->model_extension_feed_boost_sitemap->getTotalInformations([
					'store_id' => $store['store_id'], 
					'language_id' => $language['language_id']
				]);
					
				if ($total && $limit) {
					if ($total > $limit) {
						$total_pages = ceil($total / $limit);
					} else {
						$total_pages = 1;
					}
						
					for ($i = 1; $i <= $total_pages; $i++) {
						$output  = $this->getXmlHeader();
						$output .= $this->getUrlsetOpen(false);
							
						$params = [
							'store_id' => $store['store_id'], 
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$informations = $this->model_extension_feed_boost_sitemap->getInformations($params);
					
						foreach ($informations as $information) {
							$args = 'information_id=' . $information['information_id'];
							$canonical_url = $this->getCanonicalSitemapUrl($store, 'information/information', $args, $language['language_id']);

							if ($canonical_url === '') {
								continue;
							}

							$output .= '<url>';
							$output .= '<loc>' . $this->escapeXml($canonical_url) . '</loc>';
							$output .= $this->getAlternateLinks($store, 'information/information', $args);
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_information.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_information_' . $i . '.xml';
						}
							
						$this->writeSitemap($file_name, $output);
					}	
				}
			}
		}
	}
	
	/**
	 * Generate manufacturer sitemap
	 * 
	 * @access protected
	 * @param int $limit
	 * @return void
	 */
	protected function generateManufacturerSitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		$width = 200;
		$height = 200;
		$conf_width = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
		$conf_height = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
        
		if ($conf_width) {
			$width = $conf_width;
		}
		
        if ($conf_height) { 
			$height = $conf_height;
		}
		
		foreach ($this->stores as $store) {
			foreach ($this->languages as $language) {
				$total = $this->model_extension_feed_boost_sitemap->getTotalManufacturers(['store_id' => $store['store_id']]);
					
				if ($total && $limit) {
					if ($total > $limit) {
						$total_pages = ceil($total / $limit);
					} else {
						$total_pages = 1;
					}
						
					for ($i = 1; $i <= $total_pages; $i++) {
						$output  = $this->getXmlHeader();
						$output .= $this->getUrlsetOpen(true);
							
						$params = [
							'store_id' => $store['store_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$manufacturers = $this->model_extension_feed_boost_sitemap->getManufacturers($params);
					
						foreach ($manufacturers as $manufacturer) {
							$output .= '<url>';
							$args = 'manufacturer_id=' . $manufacturer['manufacturer_id'];
							$output .= '<loc>' . $this->escapeXml($this->link($store['url'], 'product/manufacturer/info', $args, $store['store_id'], $language['language_id'])) . '</loc>';
							$output .= $this->getAlternateLinks($store, 'product/manufacturer/info', $args);
								
							if ($manufacturer['image']) {
								$output .= $this->getImageXml($this->model_extension_feed_boost_sitemap->resizeImage($manufacturer['image'], $width, $height, $store['url']));
							}
								
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_manufacturer.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_manufacturer_' . $i . '.xml';
						}
							
						$this->writeSitemap($file_name, $output);
					}	
				}
			}
		}
	}
	
	protected function generateJournal3BlogCategorySitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		foreach ($this->stores as $store) {
			$width = 200;
			$height = 200;
        	$conf_width = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
			$conf_height = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
                
			if ($conf_width) {
				$width = $conf_width;
			}
				
            if ($conf_height) {
				$height = $conf_height;
			}
		
			foreach ($this->languages as $language) {
				$total =  $this->model_extension_feed_boost_sitemap->getTotalBlogCategories([
					'store_id' => $store['store_id'],
					'language_id' => $language['language_id']
				]);
					
				if ($total && $limit) {
					if ($total > $limit) {
						$total_pages = ceil($total / $limit);
					} else {
						$total_pages = 1;
					}
						
					for ($i = 1; $i <= $total_pages; $i++) {
						$output  = $this->getXmlHeader();
						$output .= $this->getUrlsetOpen(true);
							
						$params = [
							'store_id' => $store['store_id'],
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$categories = $this->model_extension_feed_boost_sitemap->getBlogCategories($params);
					
						foreach ($categories as $category) {
							$output .= '<url>';
							$args = 'journal_blog_category_id=' . $category['category_id'];
							$output .= '<loc>' . $this->escapeXml($this->link($store['url'], 'journal3/blog', $args, $store['store_id'], $language['language_id'])) . '</loc>';
							$output .= $this->getAlternateLinks($store, 'journal3/blog', $args);
							$output .= $this->getLastmodXml($category['date_updated']);
							
							if ($category['image']) {
								$output .= $this->getImageXml($this->model_extension_feed_boost_sitemap->resizeImage($category['image'], $width, $height, $store['url']));
							}
							
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_blog_category.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_blog_category_' . $i . '.xml';
						}
							
						$this->writeSitemap($file_name, $output);
					}
				}
			}
		}
	}	
	
	
	/**
	 * Generate generateJournal3BlogPostSitemap by Intanweb.com
	 * 
	 * @access protected
	 * @param int $limit
	 * @return void
	 */
	protected function generateJournal3BlogPostSitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		foreach ($this->stores as $store) {
			$width = 200;
			$height = 200;
			$conf_width = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
			$conf_height = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');

            if ($conf_width) {
				$width = $conf_width;
			}
			
            if ($conf_height) {
				$height = $conf_height;
			}
        
			foreach ($this->languages as $language) {
			
				$total = $this->model_extension_feed_boost_sitemap->getTotalBlogPosts([
					'store_id' => $store['store_id'],
					'language_id' => $language['language_id']
				]);
				
				
				if ($total && $limit) {
					if ($total > $limit) {
						$total_pages = ceil($total / $limit);
					} else {
						$total_pages = 1;
					}
						
					for ($i = 1; $i <= $total_pages; $i++) {
						$output  = $this->getXmlHeader();
						$output .= $this->getUrlsetOpen(true);
							
						$params = [
							'store_id' => $store['store_id'],
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$posts = $this->model_extension_feed_boost_sitemap->getBlogPosts($params);
					
						foreach ($posts as $post) {
							$args = 'journal_blog_post_id=' . $post['post_id'];
								$output .= '<url>';
							$output .= '<loc>' . $this->escapeXml($this->link($store['url'], 'journal3/blog/post', $args, $store['store_id'], $language['language_id'])) . '</loc>';
							$output .= $this->getAlternateLinks($store, 'journal3/blog/post', $args);
							$output .= $this->getLastmodXml($post['date_updated']);

							if ($post['image']) {
								$output .= $this->getImageXml($this->model_extension_feed_boost_sitemap->resizeImage($post['image'], $width, $height, $store['url']));
							}

							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_blog_post.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_blog_post_' . $i . '.xml';
						}
							
						$this->writeSitemap($file_name, $output);
					}	
				}
			}
		}
	}	
	
	
	/**
	 * Generate product sitemap
	 * 
	 * @access protected
	 * @param int $limit
	 * @return void
	 */
	protected function generateProductSitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		$width = 500;
		$height = 500;
		$conf_width = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width');
		$conf_height = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height');
        
		if ($conf_width) {
			$width = $conf_width;
		}
		
        if ($conf_height) { 
			$height = $conf_height;
		}
		
		foreach ($this->stores as $store) {
			foreach ($this->languages as $language) {
				$total = $this->model_extension_feed_boost_sitemap->getTotalProducts([
					'store_id' => $store['store_id'],
					'language_id' => $language['language_id']
				]);
					
				if ($total && $limit) {
					if ($total > $limit) {
						$total_pages = ceil($total / $limit);
					} else {
						$total_pages = 1;
					}
						
					for ($i = 1; $i <= $total_pages; $i++) {
						$output  = $this->getXmlHeader();
						$output .= $this->getUrlsetOpen(true);
							
						$params = [
							'store_id' => $store['store_id'],
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];

						$products = $this->model_extension_feed_boost_sitemap->getProducts($params);

						foreach ($products as $product) {
							$args = 'product_id=' . $product['product_id'];
							$canonical_url = $this->getCanonicalSitemapUrl($store, 'product/product', $args, $language['language_id']);

							if ($canonical_url === '') {
								continue;
							}

							$output .= '<url>';
							$output .= '<loc>' . $this->escapeXml($canonical_url) . '</loc>';
							$output .= $this->getAlternateLinks($store, 'product/product', $args);
							$output .= $this->getLastmodXml($product['date_modified']);

							if ($product['image']) {
								$output .= $this->getImageXml($this->model_extension_feed_boost_sitemap->resizeImage($product['image'], $width, $height, $store['url']));
							}

							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_product.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_product_' . $i . '.xml';
						}
							
						$this->writeSitemap($file_name, $output);
					}	
				}
			}
		}
	}
	
	/**
	 * Generate category sitemap
	 * 
	 * @access protected
	 * @param int $limit
	 * @return void
	 */
	protected function generateCategorySitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		$width = 200;
		$height = 200;
		$conf_width = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
		$conf_height = (int)$this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');
		
		if ($conf_width) {
			$width = $conf_width;
		}
		
        if ($conf_height) { 
			$height = $conf_height;
		}
		
		foreach ($this->stores as $store) {
			foreach ($this->languages as $language) {
				$total = $this->model_extension_feed_boost_sitemap->getTotalCategories([
					'store_id' => $store['store_id'],
					'language_id' => $language['language_id']
				]);
					
				if ($total && $limit) {
					if ($total > $limit) {
						$total_pages = ceil($total / $limit);
					} else {
						$total_pages = 1;
					}
						
					for ($i = 1; $i <= $total_pages; $i++) {
						$output  = $this->getXmlHeader();
						$output .= $this->getUrlsetOpen(true);
							
						$params = [
							'store_id' => $store['store_id'],
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$categories = $this->model_extension_feed_boost_sitemap->getCategories($params);
					
						foreach ($categories as $category) {
							// Category pages canonicalise to the leaf alias, not
							// to every possible parent-path combination.
							$args = 'path=' . $category['category_id'];
							$canonical_url = $this->getCanonicalSitemapUrl($store, 'product/category', $args, $language['language_id']);

							if ($canonical_url === '') {
								continue;
							}

							$output .= '<url>';
							$output .= '<loc>' . $this->escapeXml($canonical_url) . '</loc>';
							$output .= $this->getAlternateLinks($store, 'product/category', $args);
							$output .= $this->getLastmodXml($category['date_modified']);
							
							if ($category['image']) {
								$output .= $this->getImageXml($this->model_extension_feed_boost_sitemap->resizeImage($category['image'], $width, $height, $store['url']));
							}
							
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_category.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_category_' . $i . '.xml';
						}
							
						$this->writeSitemap($file_name, $output);
					}
				}
			}
		}
	}
	
	/**
	 * Generate custom link sitemap
	 * 
	 * @access protected
	 * @param int $limit
	 * @return void
	 */
	protected function generateCustomLinkSitemap($limit = null) {
		$this->load->model('extension/feed/boost_sitemap');
		
		foreach ($this->stores as $store) {
			//foreach ($this->languages as $language) {
				$total = $this->model_extension_feed_boost_sitemap->getTotalCustomLinks([
					'store_id' => $store['store_id']
				]);
				
				if ($total && $limit) {
					if ($total > $limit) {
						$total_pages = ceil($total / $limit);
					} else {
						$total_pages = 1;
					}
						
						for ($i = 1; $i <= $total_pages; $i++) {
							$output  = $this->getXmlHeader();
							$output .= $this->getUrlsetOpen(false);
							
						$params = [
							'store_id' => $store['store_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$custom_links = $this->model_extension_feed_boost_sitemap->getCustomLinks($params);
					
						foreach ($custom_links as $custom_link) {
							if (!$this->isStoreUrl($custom_link['url'], $store['url'])) {
								continue;
							}

							$frequencies = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
							$frequency = in_array($custom_link['frequency'], $frequencies, true) ? $custom_link['frequency'] : 'weekly';
							$priority = (float)$custom_link['priority'];
							$priority = number_format(max(0, min(1, $priority)), 1, '.', '');
							$output .= '<url>';
							$output .= '<loc>' . $this->escapeXml($custom_link['url']) . '</loc>';
							$output .= '<changefreq>' . $frequency . '</changefreq>';
							$output .= $this->getLastmodXml($custom_link['date_added']);
							$output .= '<priority>' . $priority . '</priority>';
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							//$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_custom_link.xml';
							$file_name = 'sitemap_' . $store['store_id'] . '_custom_link.xml';
						} else {
							//$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_custom_link_' . $i . '.xml';
							$file_name = 'sitemap_' . $store['store_id'] . '_custom_link_' . $i . '.xml';
						}
							
						$this->writeSitemap($file_name, $output);
					}
				}
			//}
		}
	}
	
	/**
	 * Link
	 * 
	 * @access protected
	 * @param string $url
	 * @param string $route
	 * @param string $args
	 * @param int $store_id
	 * @param int $language_id
	 * @return string
	 */
	protected function link($url, $route, $args = '', $store_id = 0, $language_id = 0) {
		$url = $url . 'index.php?route=' . $route;
		
		if ($args) {
			if (is_array($args)) {
				$url .= '&amp;' . http_build_query($args);
			} else {
				$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&'));
			}
		}
		
		if ($this->config->get('config_seo_url')) {
			$url = $this->rewrite($url, $store_id, $language_id);
		}
		
		return $url;
	}
	
	
	/**
	 * Rewrite
	 * 
	 * @access public
	 * @param mixed $link
	 * @param int $store_id
	 * @param int $language_id
	 * @return void
	 */
	public function rewrite($link, $store_id, $language_id) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = [];

		parse_str($url_info['query'], $data);

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {
				if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$store_id . "' AND language_id = '" . (int)$language_id . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
					
					// Journal Theme Modification
                    } elseif ($key == 'journal_blog_post_id') {
                        $is_journal3_blog = true;
						if ($journal_blog_keyword = $this->model_extension_feed_boost_sitemap->rewritePost($value, $language_id)) {
                            $url .= '/' . $journal_blog_keyword;
                            unset($data[$key]);
                        }
                    } elseif ($key == 'journal_blog_category_id') {
                        $is_journal3_blog = true;
						if ($journal_blog_keyword = $this->model_extension_feed_boost_sitemap->rewriteCategory($value, $language_id)) {
                            $url .= '/' . $journal_blog_keyword;
                            unset($data[$key]);
                        }
                    } elseif (isset($data['route']) && $data['route'] == 'journal3/blog') {
                        if (!isset($data['journal_blog_post_id']) && !isset($data['journal_blog_category_id'])) {
                            $is_journal3_blog = true;
                        }
                    // End Journal Theme Modification                        
					
					
				} elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$store_id . "' AND language_id = '" . (int)$language_id . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				} elseif ($data['route'] == 'extension/feed/boost_sitemap') {
					$url = '/sitemap-index.xml';
					
					unset($data[$key]);
				}
			}
		}

		if ($url) {
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
	
	/**
	 * Delete custom link
	 * 
	 * @access public
	 * @return void
	 */
	public function delete_custom_link() {
		$json = [];
		$this->response->addHeader('Content-Type: application/json; charset=UTF-8');
		
		$this->load->model('extension/feed/boost_sitemap');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['custom_link_ids'])) {
				foreach ($this->request->post['custom_link_ids'] as $custom_link_id) {
					$this->model_extension_feed_boost_sitemap->deleteCustomLink($custom_link_id);	
				}
			}
		} elseif (isset($this->error['warning'])) {
			$json['error'] = $this->error['warning'];
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	/**
	 * Custom link
	 * 
	 * @access public
	 * @return void
	 */
	public function custom_link() {
		$this->load->model('extension/feed/boost_sitemap');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$json = [];
			$this->response->addHeader('Content-Type: application/json; charset=UTF-8');

			if (!$this->validate()) {
				$json['error'] = $this->error['warning'];
				$this->response->setOutput(json_encode($json));
				return;
			}

			if (isset($this->request->post['custom_link_url']) && isset($this->request->post['custom_link_frequency']) && isset($this->request->post['custom_link_priority']) && isset($this->request->post['custom_link_store_id'])) {
				$data['url'] = trim($this->request->post['custom_link_url']);
				$data['frequency'] = $this->request->post['custom_link_frequency'];
				$data['priority'] = $this->request->post['custom_link_priority'];
				$data['store_id'] = (int)$this->request->post['custom_link_store_id'];
				$frequencies = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];
				
				if (!in_array($data['frequency'], $frequencies, true)) {
					$data['frequency'] = 'weekly';
				}

				$data['priority'] = number_format(max(0, min(1, (float)$data['priority'])), 1, '.', '');

				$store_url = '';

				foreach ($this->stores as $store) {
					if ((int)$store['store_id'] === $data['store_id']) {
						$store_url = $store['url'];
						break;
					}
				}

				if (!$store_url || !$this->isStoreUrl($data['url'], $store_url)) {
					$json['error'] = $this->language->get('error_custom_link');
				} else {
				$this->model_extension_feed_boost_sitemap->addCustomLink($data);	
					$json['success'] = true;
			}
			}

			$this->response->setOutput(json_encode($json));
		} else {
			$custom_links = $this->model_extension_feed_boost_sitemap->getCustomLinks();
			
			$html = '';
			
			if ($custom_links) {
				foreach ($custom_links as $custom_link) {
					$html .= '<tr>';
					$html .= '<td><input type="checkbox" name="custom_link_ids[]" value="' . (int)$custom_link['boost_sitemap_custom_link_id'] . '" /></td>';
					$html .= '<td>' . htmlspecialchars(($custom_link['store_name'] ? $custom_link['store_name'] : 'Default'), ENT_QUOTES, 'UTF-8') . '</td>';
					$html .= '<td>' . htmlspecialchars($custom_link['url'], ENT_QUOTES, 'UTF-8') . '</td>';
					$html .= '<td>' . htmlspecialchars($custom_link['frequency'], ENT_QUOTES, 'UTF-8') . '</td>';
					$html .= '<td>' . htmlspecialchars($custom_link['priority'], ENT_QUOTES, 'UTF-8') . '</td>';
					$html .= '</tr>';
				}
			} else {
				$html .= '<tr><td colspan="4" class="text-center">' . $this->language->get('text_empty') . '</td></tr>';
			}
			
			$this->response->addHeader('Content-Type: text/html; charset=UTF-8');
			$this->response->setOutput($html);
		}
	}
}
