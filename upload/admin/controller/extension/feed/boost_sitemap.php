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
		$this->directory = str_replace('system', 'sitemaps', DIR_SYSTEM);
		
		$this->load->language('extension/feed/boost_sitemap');
		
		$this->load->model('setting/store');
		$this->load->model('setting/setting');
		
		$stores = $this->model_setting_store->getStores();
		
		$this->stores[] = [
			'store_id' => 0,
			'name' => 'Default',
			'url' => ($this->config->get('config_secure') ? HTTPS_CATALOG : HTTPS_CATALOG)
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
		umask(0);
		mkdir($this->directory, 0777);
		
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
		umask(0);
		
		$dir = $this->directory;
		$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
					
		foreach($files as $file) {
			if ($file->isDir()){
				rmdir($file->getRealPath());
			} else {
				unlink($file->getRealPath());
			}
		}
					
		rmdir($dir);
		
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
			'category_product' => 'Category To Product Sitemaps',
			'manufacturer' => 'Manufacturer Sitemaps',
			'manufacturer_product' => 'Manufacturer To Product Sitemaps',
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
		$files = scandir($dir);
		
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
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['selected'])) {
				$selected = $this->request->post['selected'];
				
				foreach ($selected as $path) {
					if (file_exists($this->directory . $path)) {
						unlink($this->directory . $path);
					}
				}
			}
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
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');
			
			if (isset($this->request->post['selected'])) {
				unset($this->request->post['selected']);
			}
			
			$this->model_setting_setting->editSetting('feed_boost_sitemap', $this->request->post);
			
			$items = isset($this->request->post['feed_boost_sitemap_item']) ? $this->request->post['feed_boost_sitemap_item'] : [];
			
			/* Journal3 Blog */
			if(defined('JOURNAL3_INSTALLED')){
				$this->load->model('extension/feed/boost_sitemap');
					
                if (in_array('journal3blogpost', $items)) {
					$this->generateJournal3BlogPostSitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
                }			

                if (in_array('journal3blogcategory', $items)) {
                    $this->model_extension_feed_boost_sitemap->alterTableBlogCategory();
					
					$this->generateJournal3BlogCategorySitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
                }				
			}
			
			if (in_array('product', $items)) {
				$this->generateProductSitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
			}
			
			if (in_array('category', $items)) {
				$this->generateCategorySitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
			}
			
			if (in_array('category_product', $items)) {
				$this->generateCategoryToProductSitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
			}
			
			if (in_array('information', $items)) {
				$this->generateInformationSitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
			}
			
			if (in_array('manufacturer', $items)) {
				$this->generateManufacturerSitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
			}
			
			if (in_array('manufacturer_product', $items)) {
				$this->generateManufacturerToProductSitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
			}
			
			if (in_array('custom_link', $items)) {
				$this->generateCustomLinkSitemap((int)$this->request->post['feed_boost_sitemap_item_limit']);
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
	public function getCategories($parent_id, $current_path = '', $language_id, $store_id) {
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
			$width = $conf_height;
		}
				
		foreach ($this->stores as $store) {
			foreach ($this->languages as $language) {
				$categories = $this->getCategories(0, '', $language['language_id'], $store['store_id']);
				$results = array_chunk($categories, $limit);
				$count = 1;
				
				foreach ($results as $key => $result) {
					$output  = '<?xml version="1.0" encoding="UTF-8"?>';
					$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
							
					foreach ($result as $product) {
						$output .= '<url>';
						$output .= '  <loc>' . $this->link($store['url'], 'product/product', 'path=' . $product['path'] . '&product_id=' . $product['product_id'], $store['store_id'], $language['language_id']) . '</loc>';
						$output .= '  <changefreq>weekly</changefreq>';
						$output .= '  <priority>1.0</priority>';
						
						if ($product['image']) {
							$output .= '  <image:image>';
							$output .= '  <image:loc>' . $this->model_extension_feed_boost_sitemap->resizeImage($product['image'], $width, $height, $store['url']) . '</image:loc>';
							$output .= '  <image:caption>' . $product['name'] . '</image:caption>';
							$output .= '  <image:title>' . $product['name'] . '</image:title>';
							$output .= '  </image:image>';
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
								
					$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
								
					fwrite($xml_file, $output);
					fclose($xml_file);
						
					$this->files[] = 'sitemaps/' . $file_name;
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
			$width = $conf_height;
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
						$output  = '<?xml version="1.0" encoding="UTF-8"?>';
						$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
								
						foreach ($result as $product) {
							$output .= '<url>';
							$output .= '  <loc>' . $this->link($store['url'], 'product/product', 'manufacturer_id=' . $product['manufacturer_id'] . '&product_id=' . $product['product_id'], $store['store_id'], $language['language_id']) . '</loc>';
							$output .= '  <changefreq>weekly</changefreq>';
							$output .= '  <priority>1.0</priority>';
							
							if ($product['image']) {
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . $this->model_extension_feed_boost_sitemap->resizeImage($product['image'], $width, $height, $store['url']) . '</image:loc>';
								$output .= '  <image:caption>' . $product['name'] . '</image:caption>';
								$output .= '  <image:title>' . $product['name'] . '</image:title>';
								$output .= '  </image:image>';
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
									
						$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
									
						fwrite($xml_file, $output);
						fclose($xml_file);
							
						$this->files[] = 'sitemaps/' . $file_name;
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
						$output  = '<?xml version="1.0" encoding="UTF-8"?>';
						$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
							
						$params = [
							'store_id' => $store['store_id'], 
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$informations = $this->model_extension_feed_boost_sitemap->getInformations($params);
					
						foreach ($informations as $information) {
							$output .= '<url>';
							$output .= '  <loc>' . $this->link($store['url'], 'information/information', 'information_id=' . $information['information_id'], $store['store_id'], $language['language_id']) . '</loc>';
							$output .= '  <changefreq>monthly</changefreq>';
							$output .= '  <priority>0.5</priority>';
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_information.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_information_' . $i . '.xml';
						}
							
						$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
							
						fwrite($xml_file, $output);
						fclose($xml_file);
							
						$this->files[] = 'sitemaps/' . $file_name;	
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
			$width = $conf_height;
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
						$output  = '<?xml version="1.0" encoding="UTF-8"?>';
						$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
							
						$params = [
							'store_id' => $store['store_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$manufacturers = $this->model_extension_feed_boost_sitemap->getManufacturers($params);
					
						foreach ($manufacturers as $manufacturer) {
							$output .= '<url>';
							$output .= '  <loc>' . $this->link($store['url'], 'product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'], $store['store_id'], $language['language_id']) . '</loc>';
							$output .= '  <changefreq>monthly</changefreq>';
							$output .= '  <priority>0.5</priority>';
								
							if ($manufacturer['image']) {
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . $this->model_extension_feed_boost_sitemap->resizeImage($manufacturer['image'], $width, $height, $store['url']) . '</image:loc>';
								$output .= '  <image:caption>' . $manufacturer['name'] . '</image:caption>';
								$output .= '  <image:title>' . $manufacturer['name'] . '</image:title>';
								$output .= '  </image:image>';
							}
								
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_manufacturer.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_manufacturer_' . $i . '.xml';
						}
							
						$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
							
						fwrite($xml_file, $output);
						fclose($xml_file);
							
						$this->files[] = 'sitemaps/' . $file_name;	
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
						$output  = '<?xml version="1.0" encoding="UTF-8"?>';
						$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
							
						$params = [
							'store_id' => $store['store_id'],
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$categories = $this->model_extension_feed_boost_sitemap->getBlogCategories($params);
					
						foreach ($categories as $category) {
							$output .= '<url>';
                            $output .= '<loc>' . $this->link($store['url'], 'journal3/blog', 'journal_blog_category_id=' . $category['category_id'],$store['store_id'], $language['language_id']) . '</loc>';
							
							$output .= '  <changefreq>weekly</changefreq>';
							$output .= '  <lastmod>' . date('c', strtotime($category['date_updated'])) . '</lastmod>';
							$output .= '  <priority>0.5</priority>';
							
							if ($category['image']) {
								$output .= '  <image:image>';								
                                $output .= '  <image:loc>' . $this->model_extension_feed_boost_sitemap->resizeImage($category['image'], $width, $height, $store['url']) . '</image:loc>';
								$output .= '  <image:caption>' . $category['name'] . '</image:caption>';
								$output .= '  <image:title>' . $category['name'] . '</image:title>';
								$output .= '  </image:image>';
							}
							
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_blog_category.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_blog_category_' . $i . '.xml';
						}
							
						$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
							
						fwrite($xml_file, $output);
						fclose($xml_file);
							
						$this->files[] = 'sitemaps/' . $file_name;	
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
				$width = $conf_height;
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
						$output  = '<?xml version="1.0" encoding="UTF-8"?>';
						$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
							
						$params = [
							'store_id' => $store['store_id'],
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$posts = $this->model_extension_feed_boost_sitemap->getBlogPosts($params);
					
						foreach ($posts as $post) {
							if ($post['image']) {
								$output .= '<url>';
								$output .= '  <loc>' . $this->link($store['url'], 'journal3/blog/post', 'journal_blog_post_id=' . $post['post_id'], $store['store_id'], $language['language_id']) . '</loc>';
								$output .= '  <changefreq>weekly</changefreq>';
								$output .= '  <lastmod>' . date('c', strtotime($post['date_updated'])) . '</lastmod>';
								$output .= '  <priority>1.0</priority>';
								$output .= '  <image:image>';
                                $output .= '  <image:loc>' . $this->model_extension_feed_boost_sitemap->resizeImage($post['image'], $width, $height, $store['url']) . '</image:loc>';
                                $output .= '  <image:caption>' . $post['name'] . '</image:caption>';
								$output .= '  <image:title>' . $post['name'] . '</image:title>';
								$output .= '  </image:image>';
								$output .= '</url>';
							}
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_blog_post.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_blog_post_' . $i . '.xml';
						}
							
						$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
							
						fwrite($xml_file, $output);
						fclose($xml_file);
							
						$this->files[] = 'sitemaps/' . $file_name;	
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
			$width = $conf_height;
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
						$output  = '<?xml version="1.0" encoding="UTF-8"?>';
						$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
							
						$params = [
							'store_id' => $store['store_id'],
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$products = $this->model_extension_feed_boost_sitemap->getProducts($params);
					
						foreach ($products as $product) {
							if ($product['image']) {
								$output .= '<url>';
								$output .= '  <loc>' . $this->link($store['url'], 'product/product', 'product_id=' . $product['product_id'], $store['store_id'], $language['language_id']) . '</loc>';
								$output .= '  <changefreq>weekly</changefreq>';
								$output .= '  <lastmod>' . date('c', strtotime($product['date_modified'])) . '</lastmod>';
								$output .= '  <priority>1.0</priority>';
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . $this->model_extension_feed_boost_sitemap->resizeImage($product['image'], $width, $height, $store['url']) . '</image:loc>';
								$output .= '  <image:caption>' . $product['name'] . '</image:caption>';
								$output .= '  <image:title>' . $product['name'] . '</image:title>';
								$output .= '  </image:image>';
								$output .= '</url>';
							}
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_product.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_product_' . $i . '.xml';
						}
							
						$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
							
						fwrite($xml_file, $output);
						fclose($xml_file);
							
						$this->files[] = 'sitemaps/' . $file_name;	
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
			$width = $conf_height;
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
						$output  = '<?xml version="1.0" encoding="UTF-8"?>';
						$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
							
						$params = [
							'store_id' => $store['store_id'],
							'language_id' => $language['language_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$categories = $this->model_extension_feed_boost_sitemap->getCategories($params);
					
						foreach ($categories as $category) {
							$output .= '<url>';
							$output .= '  <loc>' . $this->link($store['url'], 'product/category', 'path=' . $category['path'], $store['store_id'], $language['language_id']) . '</loc>';
							$output .= '  <changefreq>weekly</changefreq>';
							$output .= '  <lastmod>' . date('c', strtotime($category['date_modified'])) . '</lastmod>';
							$output .= '  <priority>0.5</priority>';
							
							if ($category['image']) {
								$output .= '  <image:image>';
								$output .= '  <image:loc>' . $this->model_extension_feed_boost_sitemap->resizeImage($category['image'], $width, $height, $store['url']) . '</image:loc>';
								$output .= '  <image:caption>' . $category['name'] . '</image:caption>';
								$output .= '  <image:title>' . $category['name'] . '</image:title>';
								$output .= '  </image:image>';
							}
							
							$output .= '</url>';
						}
					
						$output .= '</urlset>';
						
						if ($total_pages == 1) {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_category.xml';
						} else {
							$file_name = 'sitemap_' . $store['store_id'] . '_' . $language['language_id'] . '_category_' . $i . '.xml';
						}
							
						$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
							
						fwrite($xml_file, $output);
						fclose($xml_file);
							
						$this->files[] = 'sitemaps/' . $file_name;	
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
						$output  = '<?xml version="1.0" encoding="UTF-8"?>';
						$output .= '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
							
						$params = [
							'store_id' => $store['store_id'],
							'start' => ($i - 1) * $limit,
							'limit' => $limit
						];
							
						$custom_links = $this->model_extension_feed_boost_sitemap->getCustomLinks($params);
					
						foreach ($custom_links as $custom_link) {
							$output .= '<url>';
							$output .= '  <loc>' . $custom_link['url'] . '</loc>';
							$output .= '  <changefreq>' . $custom_link['frequency'] . '</changefreq>';
							$output .= '  <lastmod>' . date('c', strtotime($custom_link['date_added'])) . '</lastmod>';
							$output .= '  <priority>' . $custom_link['frequency'] . '</priority>';
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
							
						$xml_file = fopen($this->directory . $file_name, 'w') or die('Unable to open file!');
							
						fwrite($xml_file, $output);
						fclose($xml_file);
							
						$this->files[] = 'sitemaps/' . $file_name;	
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
                        if ($journal_blog_keyword = $this->model_extension_feed_boost_sitemap->rewritePost($value)) {
                            $url .= '/' . $journal_blog_keyword;
                            unset($data[$key]);
                        }
                    } elseif ($key == 'journal_blog_category_id') {
                        $is_journal3_blog = true;
                        if ($journal_blog_keyword = $this->model_extension_feed_boost_sitemap->rewriteCategory($value)) {
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
		
		$this->load->model('extension/feed/boost_sitemap');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['custom_link_ids'])) {
				foreach ($this->request->post['custom_link_ids'] as $custom_link_id) {
					$this->model_extension_feed_boost_sitemap->deleteCustomLink($custom_link_id);	
				}
			}
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
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['custom_link_url']) && isset($this->request->post['custom_link_frequency']) && isset($this->request->post['custom_link_priority']) && isset($this->request->post['custom_link_store_id'])) {
				$data['url'] = $this->request->post['custom_link_url'];
				$data['frequency'] = $this->request->post['custom_link_frequency'];
				$data['priority'] = $this->request->post['custom_link_priority'];
				$data['store_id'] = (int)$this->request->post['custom_link_store_id'];
				
				$this->model_extension_feed_boost_sitemap->addCustomLink($data);	
			}
		} else {
			$custom_links = $this->model_extension_feed_boost_sitemap->getCustomLinks();
			
			$html = '';
			
			if ($custom_links) {
				foreach ($custom_links as $custom_link) {
					$html .= '<tr>';
					$html .= '<td><input type="checkbox" name="custom_link_ids[]" value="' . (int)$custom_link['boost_sitemap_custom_link_id'] . '" /></td>';
					$html .= '<td>' . ($custom_link['store_name'] ? $custom_link['store_name'] : 'Default') . '</td>';
					$html .= '<td>' . $custom_link['url'] . '</td>';
					$html .= '<td>' . $custom_link['frequency'] . '</td>';
					$html .= '<td>' . $custom_link['priority'] . '</td>';
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