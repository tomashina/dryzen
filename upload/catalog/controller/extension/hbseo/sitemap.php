<?php
class ControllerExtensionHbseoSitemap extends Controller {
	public function index() {
		if (!$this->config->get('hb_sitemap_enable')) {
			echo 'HuntBee SEO XML Sitemap Generator PRO is disabled.';
			return;
		}
		
		$search = 'extension/hbseo/sitemap';
		$file = '.htaccess';
		$htaccess_enabled = $this->isHtaccessEnabled($file, $search);
		
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		$limit = $this->config->get('hb_sitemap_limit') ?: 3000;
		$product_total = $this->getTotalProducts();
		$number_of_pages = ceil($product_total / $limit);
		
		$store_id = (int)$this->config->get('config_store_id');
		$store_url = $this->getStoreUrl($store_id);
		
		$languages = $this->getActiveLanguages();
		
		// Loop through each language
		foreach ($languages as $language) {
			$language_code = $language['code'];  
	
			// Process different sitemap types
			$output .= $this->generateSitemapSection($language_code, 'product', $number_of_pages, $htaccess_enabled, $store_url, $limit);
			$output .= $this->generateSitemapSection($language_code, 'product_tags', $number_of_pages, $htaccess_enabled, $store_url, $limit);
			$output .= $this->generateSitemapSection($language_code, 'category', 1, $htaccess_enabled, $store_url);
			$output .= $this->generateSitemapSection($language_code, 'brand', 1, $htaccess_enabled, $store_url);
			$output .= $this->generateSitemapSection($language_code, 'information', 1, $htaccess_enabled, $store_url);
			$output .= $this->generateSitemapSection($language_code, 'category_to_product', 1, $htaccess_enabled, $store_url);
			$output .= $this->generateSitemapSection($language_code, 'brand_to_product', 1, $htaccess_enabled, $store_url);
			$output .= $this->generateSitemapSection($language_code, 'journal2blog', 1, $htaccess_enabled, $store_url);
			$output .= $this->generateSitemapSection($language_code, 'journal3blog', 1, $htaccess_enabled, $store_url);
		}
	
		// Miscellaneous sitemap
		if ($this->config->get('hb_sitemap_misc')) {
			$output .= $this->generateSitemapSection('misc', 'misc', 1, $htaccess_enabled, $store_url);
		}
		
		$output .= '</sitemapindex>';
	
		$this->response->addHeader('Content-Type: application/xml');
		$this->response->setOutput($output);
	}
	
	private function isHtaccessEnabled($file, $search) {
		if (file_exists($file)) {
			$lines = file($file);
			foreach ($lines as $line) {
				if (strpos($line, $search) !== false) {
					return true;
				}
			}
		}
		return false;
	}
	
	private function getStoreUrl($store_id) {
		if ($store_id == 0) {
			return HTTPS_SERVER;
		}
		
		$result = $this->db->query("SELECT `url` FROM `" . DB_PREFIX . "store` WHERE store_id = ".(int)$store_id);
		return $result->row['url'];
	}
	
	private function getActiveLanguages() {
		return $this->db->query("SELECT * FROM ".DB_PREFIX."language WHERE status = 1")->rows;
	}
	
	private function generateSitemapSection($language_code, $type, $number_of_pages, $htaccess_enabled, $store_url, $limit = 3000) {
		$output = '';
		
		// Skip if the configuration for this sitemap type is not enabled
		if (!$this->config->get("hb_sitemap_{$type}")) {
			return $output;
		}
	
		// Generate paginated sitemaps for types that have multiple pages
		for ($x = 1; $x <= $number_of_pages; $x++) {
			$output .= '<sitemap>';
			
			$url = $this->getSitemapUrl($type, $language_code, $x, $htaccess_enabled, $store_url);
			$output .= "<loc>{$url}</loc>";
			
			$lastmod = $this->getLastModifiedDate($type, $x, $limit);
			$output .= "<lastmod>{$lastmod}</lastmod>";
			
			$output .= '</sitemap>';
		}
		
		return $output;
	}
	
	private function getSitemapUrl($type, $language_code, $page, $htaccess_enabled, $store_url) {
		// Check if the page parameter should be included based on the sitemap type
		if ($htaccess_enabled) {
			if ($type === 'product' || $type === 'product_tags') {
				return "{$store_url}sitemaps/{$language_code}/{$type}_sitemap_{$page}.xml";
			}elseif ($type === 'misc'){
				return "{$store_url}sitemaps/{$type}_sitemap.xml";
			} else {
				return "{$store_url}sitemaps/{$language_code}/{$type}_sitemap.xml";
			}
		} else {
			// If page is for product or product_tags, add the page to the link
			if ($type === 'product' || $type === 'product_tags') {
				return $this->url->link("extension/hbseo/sitemap/{$type}", "&hbxmllang={$language_code}&page={$page}");
			} elseif ($type === 'misc') {
				return $this->url->link("extension/hbseo/sitemap/{$type}");
			}else {
				return $this->url->link("extension/hbseo/sitemap/{$type}", "&hbxmllang={$language_code}");
			}
		}
	}	
	
	private function getLastModifiedDate($type, $page, $limit) {
		switch ($type) {
			case 'product':
			case 'product_tags':
				return date('c', strtotime($this->lastupdatedProduct(($page - 1) * $limit, $limit)));
			case 'category':
				return date('c', strtotime($this->lastupdatedCategory()));
			case 'brand':
			case 'information':
			case 'category_to_product':
			case 'brand_to_product':
			case 'journal2blog':
			case 'journal3blog':
				return date('c', strtotime($this->lastupdatedProduct('x', 1)));
			case 'misc':
				return date('c', strtotime($this->lastupdatedMisc()));
			default:
				return date('c');
		}
	}

	public function product() {
		if ($this->config->get('hb_sitemap_product')) {
			
			// Language ID handling
			$language_id = isset($this->request->get['hbxmllang']) ? $this->getset_language_id($this->request->get['hbxmllang']) : (int)$this->config->get('config_language_id');
			
			// Pagination
			$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
			$limit = $this->config->get('hb_sitemap_limit') ?: 500;
			$start = max(0, ($page - 1) * $limit);
			
			// Configuration values
			$caption = $this->config->get('hb_sitemap_caption' . $language_id);
			$title = $this->config->get('hb_sitemap_title' . $language_id);
			$width = $this->config->get('hb_sitemap_width') ?: 500;
			$height = $this->config->get('hb_sitemap_height') ?: 500;
			
			// Load required models
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			
			// Fetch products
			$products = $this->getProducts($start, $limit);
	
			if (empty($products)) {
				$this->response->addHeader('Content-Type: application/xml');
				$this->response->setOutput('<?xml version="1.0" encoding="UTF-8"?>' . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
				return;
			}
	
			// Begin XML output
			$output = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
	
			// Generate product sitemap URLs
			foreach ($products as $product) {
				$product_url = $this->url->link('product/product', 'product_id=' . $product['product_id']);
				$lastmod = date('Y-m-d\TH:i:sP', strtotime($product['date_modified']));
				
				$resized_image = $this->model_tool_image->resize($product['image'], $width, $height);
				$image_name = htmlspecialchars($product['name']);
				$image_caption = str_replace('{p}', $image_name, $caption);
				$image_title = str_replace('{p}', $image_name, $title);
				
				$output .= '<url>';
				$output .= "<loc>{$product_url}</loc>";
				$output .= "<changefreq>weekly</changefreq>";
				$output .= "<lastmod>{$lastmod}</lastmod>";
				$output .= "<priority>1.0</priority>";
	
				// Add product image if available
				if (!empty($resized_image)) {
					$output .= '<image:image>';
					$output .= "<image:loc>{$resized_image}</image:loc>";
					$output .= "<image:caption>{$image_caption}</image:caption>";
					$output .= "<image:title>{$image_title}</image:title>";
					$output .= '</image:image>';
				}
	
				// Add additional images if available
				if ($this->getadditionalimages($product['product_id'])) {
					$output .= $this->getadditionalimages($product['product_id']);
				}
				$output .= '</url>';
			}
	
			$output .= '</urlset>';
	
			// Beautify XML if needed
			$this->response->addHeader('Content-Type: application/xml');
			$output = $this->config->get('hb_sitemap_beautify') ? $this->xmlbeautify($output) : $output;
	
			// Output the final XML
			$this->response->setOutput($output);
		}
	}
	
	/* PRODUCT TAGS */	
	public function product_tags() {
		if ($this->config->get('hb_sitemap_product_tags')) {
			$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
			$limit = $this->config->get('hb_sitemap_limit');
			$start = max(0, ($page - 1) * $limit);
			
			if (isset($this->request->get['hbxmllang'])) {
				$language_id = $this->getset_language_id($this->request->get['hbxmllang']);
			}
			
			$this->load->model('catalog/product');
			
			$products = $this->getProducts($start, $limit);
			
			if ($products) {
				$output = '<?xml version="1.0" encoding="UTF-8"?>';
				$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
				
				foreach ($products as $product) {
					$tags = $this->getProductTags($product['tag']);
					
					foreach ($tags as $tag) {
						$output .= $this->generateTagUrl($tag, $product['date_modified']);
					}
				}
				
				$output .= '</urlset>';
				
				$this->response->addHeader('Content-Type: application/xml');
				$output = $this->config->get('hb_sitemap_beautify') ? $this->xmlbeautify($output) : $output;
				$this->response->setOutput($output);
			} else {
				$this->response->setOutput('<?xml version="1.0" encoding="UTF-8"?><urlset/>');
			}
		}
	}
	
	protected function getProducts($start=0, $limit=100){
		$records = $this->db->query("SELECT p.product_id, pd.name, p.image, pd.tag, p.date_modified FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY p.product_id ORDER BY p.date_modified LIMIT ".(int)$start.",".(int)$limit);
		if ($records->rows) {
			return $records->rows;
		}else{
			return false;
		}
	}
	
	protected function getadditionalimages($product_id){
		$a_caption = $this->config->get('hb_sitemap_a_caption'.(int)$this->config->get('config_language_id'));
		$a_title = $this->config->get('hb_sitemap_a_title'.(int)$this->config->get('config_language_id'));
		$width = ($this->config->get('hb_sitemap_width'))?$this->config->get('hb_sitemap_width'):500;
		$height = ($this->config->get('hb_sitemap_height'))?$this->config->get('hb_sitemap_height'):500;
			
		$results = $this->db->query("SELECT a.image, b.name FROM  `" . DB_PREFIX . "product_image` a LEFT JOIN`" . DB_PREFIX . "product_description` b ON a.product_id = b.product_id WHERE a.product_id = '".(int)$product_id."' AND b.language_id = '".(int)$this->config->get('config_language_id')."'");
		if ($results->rows){
			$images = $results->rows;
			$output = '';
			
			foreach ($images as $image){
				if (trim($image['image']) <> ''){
					$resized_image = $this->model_tool_image->resize($image['image'], $width, $height);
					$image_name = htmlspecialchars($image['name']);
					if ($resized_image) {
						$output .= '<image:image>';
						$output .= '<image:loc>' . $resized_image . '</image:loc>';
						$output .= '<image:caption>' . str_replace('{p}', $image_name, $a_caption) . '</image:caption>';
						$output .= '<image:title>' . str_replace('{p}', $image_name, $a_title) . '</image:title>';
						$output .= '</image:image>';
					}
				}
			}
			return $output;
		}else{
			return false;
		}
	}

	// Helper function to get tags as an array
	private function getProductTags($tags) {
		$tags = explode(',', $tags);
		return array_map('trim', $tags); // Trim each tag
	}
	
	// Helper function to generate XML for each tag
	private function generateTagUrl($tag, $last_modified) {
		if (trim($tag) !== '') {
			$tag_url = $this->url->link('product/search', 'tag=' . urlencode($tag));
			$lastmod = date('Y-m-d\TH:i:sP', strtotime($last_modified));
			
			return '<url>' .
					   '<loc>' . $tag_url . '</loc>' .
					   '<changefreq>weekly</changefreq>' .
					   '<lastmod>' . $lastmod . '</lastmod>' .
					   '<priority>0.2</priority>' .
				   '</url>';
		}
		return ''; 
	}

	public function information() {
		if ($this->config->get('hb_sitemap_information')) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			
			$this->load->model('catalog/information');
			
			$informations = $this->model_catalog_information->getInformations();
	
			foreach ($informations as $information) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('information/information', 'information_id=' . $information['information_id']) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.5</priority>';
				$output .= '</url>';
			}
	
			$output .= '</urlset>';
	
			$this->response->addHeader('Content-Type: application/xml');
	
			if ($this->config->get('hb_sitemap_beautify')) {
				$output = $this->xmlbeautify($output);
			}
	
			$this->response->setOutput($output);
		}
	}
		
	
	public function brand() {
		if ($this->config->get('hb_sitemap_brand')) {
			if (isset($this->request->get['hbxmllang'])) {
				$language_id = $this->getset_language_id($this->request->get['hbxmllang']);
			}
	
			$width = $this->config->get('hb_sitemap_width') ?: 500;
			$height = $this->config->get('hb_sitemap_height') ?: 500;
			
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
	
			$this->load->model('catalog/manufacturer');
			$this->load->model('tool/image');
	
			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();
	
			foreach ($manufacturers as $manufacturer) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.7</priority>';
	
				if ($manufacturer['image']) {
					$resized_image = $this->model_tool_image->resize($manufacturer['image'], $width, $height);
					if ($resized_image) {
						$output .= '<image:image>';
						$output .= '<image:loc>' . $resized_image . '</image:loc>';
						$output .= '<image:caption>' . htmlspecialchars($manufacturer['name']) . '</image:caption>';
						$output .= '<image:title>' . htmlspecialchars($manufacturer['name']) . '</image:title>';
						$output .= '</image:image>';
					}
				}
	
				$output .= '</url>';
			}
	
			$output .= '</urlset>';
	
			$this->response->addHeader('Content-Type: application/xml');
	
			if ($this->config->get('hb_sitemap_beautify')) {
				$output = $this->xmlbeautify($output);
			}
	
			$this->response->setOutput($output);
		}
	}
		
	
	public function brand_to_product() {
		if ($this->config->get('hb_sitemap_brand_to_product')) {
			if (isset($this->request->get['hbxmllang'])) {
				$language_id = $this->getset_language_id($this->request->get['hbxmllang']);
			}
			
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			
			$this->load->model('catalog/product');
			$this->load->model('catalog/manufacturer');
	
			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();
	
			foreach ($manufacturers as $manufacturer) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id']) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.7</priority>';
				$output .= '</url>';
	
				$products = $this->model_catalog_product->getProducts(['filter_manufacturer_id' => $manufacturer['manufacturer_id']]);
				foreach ($products as $product) {
					$output .= '<url>';
					$output .= '<loc>' . $this->url->link('product/product', 'manufacturer_id=' . $manufacturer['manufacturer_id'] . '&product_id=' . $product['product_id']) . '</loc>';
					$output .= '<changefreq>weekly</changefreq>';
					$output .= '<priority>1.0</priority>';
					$output .= '</url>';
				}
			}
	
			$output .= '</urlset>';
	
			$this->response->addHeader('Content-Type: application/xml');
	
			if ($this->config->get('hb_sitemap_beautify')) {
				$output = $this->xmlbeautify($output);
			}
	
			$this->response->setOutput($output);
		}
	}
	
	public function misc() {
		if ($this->config->get('hb_sitemap_misc')) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	
			$links = $this->db->query("SELECT * FROM `" . DB_PREFIX . "sitemap_links` WHERE store_id = '" . (int)$this->config->get('config_store_id') . "'")->rows;
			
			foreach ($links as $link) {
				$output .= '<url>';
				$output .= '<loc>' . htmlspecialchars(urldecode($link['link'])) . '</loc>';
				$output .= '<changefreq>' . $link['freq'] . '</changefreq>';
				$output .= '<priority>' . $link['priority'] . '</priority>';
				$output .= '<lastmod>' . date('Y-m-d\TH:i:sP', strtotime($link['date_added'])) . '</lastmod>';
				$output .= '</url>';
			}
	
			$output .= '</urlset>';
	
			$this->response->addHeader('Content-Type: application/xml');
	
			// Beautify the XML output if the config is set
			if ($this->config->get('hb_sitemap_beautify')) {
				$output = $this->xmlbeautify($output);
			}
	
			$this->response->setOutput($output);
		}
	}	
	
	public function journal3blog() {
		if ($this->config->get('hb_sitemap_journal3blog')) {
			if (isset($this->request->get['hbxmllang'])) {
				$language_id = $this->getset_language_id($this->request->get['hbxmllang']);
			}
			
			$width = ($this->config->get('hb_sitemap_width'))?$this->config->get('hb_sitemap_width'):500;
			$height = ($this->config->get('hb_sitemap_height'))?$this->config->get('hb_sitemap_height'):500;
			
			$output  = '<?xml version="1.0" encoding="UTF-8"?>';
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';

			$this->load->model('catalog/manufacturer');
			$this->load->model('tool/image');

			$this->load->model('journal3/blog');
			
			$posts = $this->model_journal3_blog->getPosts();

			foreach ($posts as $post) {
                $output .= '<url>';
                $output .= '<loc>' . $this->url->link('journal3/blog/post', 'journal_blog_post_id=' . $post['post_id']) . '</loc>';
                $output .= '<changefreq>weekly</changefreq>';
                $output .= '<priority>0.8</priority>';
				if (trim($post['image']) <> '') {
					$resized_image = $this->model_tool_image->resize($post['image'], $width, $height);
					$image_name = htmlspecialchars($post['name']);
					if ($resized_image) {
						$output .= '<image:image>';
						$output .= '<image:loc>' . $resized_image . '</image:loc>';
						$output .= '<image:caption>' . $image_name . '</image:caption>';
						$output .= '<image:title>' . $image_name . '</image:title>';
						$output .= '</image:image>';
					}
				}
                $output .= '</url>';
            }

			$categories = $this->model_journal3_blog->getCategories();
			
			foreach ($categories as $category) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('journal3/blog', 'journal_blog_category_id=' . $category['category_id']) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>0.8</priority>';
				$output .= '</url>';
			}

			$output .= '</urlset>';

			$this->response->addHeader('Content-Type: application/xml');
			
			if ($this->config->get('hb_sitemap_beautify')) {
				$output = $this->xmlbeautify($output);
			}
			$this->response->setOutput($output);
		}
	}
	
	//FOR CATEGORY
	public function category() {
		if (!$this->config->get('hb_sitemap_category')) {
			return;
		}
	
		$language_id = isset($this->request->get['hbxmllang']) 
			? $this->getset_language_id($this->request->get['hbxmllang']) 
			: $this->config->get('config_language_id');
	
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
	
		$output  = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
	
		$output .= $this->getCategories(0);
	
		$output .= '</urlset>';
	
		$this->response->addHeader('Content-Type: application/xml');
	
		if ($this->config->get('hb_sitemap_beautify')) {
			$output = $this->xmlbeautify($output);
		}
	
		$this->response->setOutput($output);
	}
	
	protected function getCategories($parent_id, $current_path = '') {
		$output = '';
		$results = $this->model_catalog_category->getCategories($parent_id);
	
		$width = $this->config->get('hb_sitemap_width') ?: 500;
		$height = $this->config->get('hb_sitemap_height') ?: 500;
	
		foreach ($results as $result) {
			$new_path = $current_path ? $current_path . '_' . $result['category_id'] : $result['category_id'];
	
			$output .= '<url>';
			$output .= '<loc>' . $this->url->link('product/category', 'path=' . $new_path) . '</loc>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.7</priority>';
	
			if (!empty($result['image'])) {
				$resized_image = $this->model_tool_image->resize($result['image'], $width, $height);
				$image_name = htmlspecialchars($result['name']);
				if ($resized_image) {
					$output .= '<image:image>';
					$output .= '<image:loc>' . $resized_image . '</image:loc>';
					$output .= '<image:caption>' . $image_name . '</image:caption>';
					$output .= '<image:title>' . $image_name . '</image:title>';
					$output .= '</image:image>';
				}
			}
	
			$output .= '</url>';
	
			$output .= $this->getCategories($result['category_id'], $new_path);
		}
	
		return $output;
	}
	
	//FOR CATEGORY TO PRODUCTS
	public function category_to_product() {
		if (!$this->config->get('hb_sitemap_category_to_product')) {
			return;
		}
	
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/category');
		
		$output = '<?xml version="1.0" encoding="UTF-8"?>';
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		
		$output .= $this->getCategoriesProduct(0);
	
		$output .= '</urlset>';
		
		$this->response->addHeader('Content-Type: application/xml');
		if ($this->config->get('hb_sitemap_beautify')) {
			$output = $this->xmlbeautify($output);
		}
	
		$this->response->setOutput($output);
	}
	
	protected function getCategoriesProduct($parent_id, $current_path = '') {
		$output = '';
		$results = $this->model_catalog_category->getCategories($parent_id);
	
		foreach ($results as $result) {
			$new_path = $current_path ? $current_path . '_' . $result['category_id'] : $result['category_id'];
	
			$output .= '<url>';
			$output .= '<loc>' . $this->url->link('product/category', 'path=' . $new_path) . '</loc>';
			$output .= '<changefreq>weekly</changefreq>';
			$output .= '<priority>0.7</priority>';
			$output .= '</url>';
	
			$products = $this->model_catalog_product->getProducts(array('filter_category_id' => $result['category_id']));
	
			foreach ($products as $product) {
				$output .= '<url>';
				$output .= '<loc>' . $this->url->link('product/product', 'path=' . $new_path . '&product_id=' . $product['product_id']) . '</loc>';
				$output .= '<changefreq>weekly</changefreq>';
				$output .= '<priority>1.0</priority>';
				$output .= '</url>';
			}
	
			$output .= $this->getCategoriesProduct($result['category_id'], $new_path);
		}
	
		return $output;
	}
	
	
	//FOR INDEX
	private function getTotalProducts(){		
		$records = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		//$records = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		//$records = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "product WHERE status = '1' AND p.date_available <= NOW()");
		return $records->row['total'];
	}
	
	private function lastupdatedCategory(){
		$records = $this->db->query("SELECT `date_modified` FROM " . DB_PREFIX . "category ORDER BY date_modified DESC LIMIT 1");
		if ($records->row) {
			return $records->row['date_modified'];
		}else{
			return date('Y-m-d H:i:s');
		}
	}

	private function lastupdatedProduct($start=0, $limit=1){
		if ($start == 'x') {
			$records = $this->db->query("SELECT `date_modified` FROM " . DB_PREFIX . "product WHERE status = 1 ORDER BY date_modified DESC LIMIT 1");
			return $records->row['date_modified'];
		}else{
			$records = $this->db->query("SELECT `date_modified` FROM " . DB_PREFIX . "product WHERE status = 1 ORDER BY date_modified LIMIT ".(int)$start.",".(int)$limit);
			if ($records->rows) {
				$array = $records->rows;
				$last_item = $array[count($array)-1];
				return $last_item['date_modified'];
				//return $records->row['date_modified'];
			}else{
				return date('Y-m-d H:i:s');
			}
		}
		
	}
	
	private function lastupdatedMisc(){
		$records = $this->db->query("SELECT `date_added` FROM " . DB_PREFIX . "sitemap_links ORDER BY date_added DESC LIMIT 1");
		if ($records->row) {
			return $records->row['date_added'];
		}else{
			return date('Y-m-d H:i:s');
		}
	}
	
	private function xmlbeautify($string){
			$string = str_replace("<url>","\r\n\t<url>",$string);
			$string = str_replace("<loc>","\r\n\t\t<loc>",$string);
			$string = str_replace("<changefreq>","\r\n\t\t<changefreq>",$string);
			$string = str_replace("<priority>","\r\n\t\t<priority>",$string);
			$string = str_replace("</url>","\r\n\t</url>",$string);
			$string = str_replace("<image:image>","\r\n\t\t<image:image>",$string);
			$string = str_replace("<image:loc>","\r\n\t\t\t<image:loc>",$string);
			$string = str_replace("<image:caption>","\r\n\t\t\t<image:caption>",$string);
			$string = str_replace("<image:title>","\r\n\t\t\t<image:title>",$string);
			$string = str_replace("</image:image>","\r\n\t\t</image:image>",$string);
			$string = str_replace("</urlset>","\r\n</urlset>",$string);
			return $string;
	}
	
	private function getset_language_id($language_code){
		$language_id = $this->db->query("SELECT `language_id` FROM `".DB_PREFIX."language` WHERE `code` = '".$this->db->escape($language_code)."'");
		if (isset($language_id->row['language_id'])) {
			$language_id = (int)$language_id->row['language_id'];
		}else{
			$language_id = 1;
		}
		
		$this->session->data['language_id'] = $language_id;
		if (isset($this->session->data['language_id'])){
			$this->config->set('config_language_id',$this->session->data['language_id']);
			$this->session->data['language'] = $language_code;
			$this->language = new Language($this->session->data['language']);
			$this->language->load($this->session->data['language']);
			$this->registry->set('language', $this->language);
		}
		
		return $language_id;
	}

}
