<?php
class ModelExtensionHbseoHbSeourl extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hb_url_preserve` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `store_id` int(11) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `query` VARCHAR(255) NOT NULL,
			  `old_keyword` VARCHAR(255) NOT NULL,
			  `new_keyword` VARCHAR(255) NOT NULL,
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8");
		
		if (version_compare(VERSION,'3.0.0.0','<')){	
			$language_id = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "url_alias` LIKE 'language_id'");
			if (!$language_id->num_rows){
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "url_alias` ADD `language_id` INT NOT NULL DEFAULT 1 AFTER `query`");
			}
		}
		
		$this->installOcmod();

		$this->installEvents();
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_url`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_url_preserve`");
		if (version_compare(VERSION,'3.0.0.0','<')){	
			$language_id = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "url_alias` LIKE 'language_id'");
			if ($language_id->num_rows){
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "url_alias` DROP COLUMN `language_id`");
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_friendly_url_ocmod'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_multi_language_url_ocmod'");

		//delete events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('seo_url_alert_menu');
		$this->model_setting_event->deleteEventByCode('seo_url_product_add');
		$this->model_setting_event->deleteEventByCode('seo_url_product_edit');
		$this->model_setting_event->deleteEventByCode('seo_url_category_add');
		$this->model_setting_event->deleteEventByCode('seo_url_category_edit');
		$this->model_setting_event->deleteEventByCode('seo_url_brand_add');
		$this->model_setting_event->deleteEventByCode('seo_url_brand_edit');
		$this->model_setting_event->deleteEventByCode('seo_url_information_add');
		$this->model_setting_event->deleteEventByCode('seo_url_information_edit');
	}

	public function installOcmod(){
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_friendly_url_ocmod'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_multi_language_url_ocmod'");

		if ((version_compare(VERSION,'2.0.0.0','>=' )) and (version_compare(VERSION,'2.2.0.0','<' ))) {
			$ocmod_filename = 'ocmod_hb_seourl_2000_21xx.txt';
			$ocmod_name = 'SEO - URL BASIC [2000 - 21xx]';
		}else if ((version_compare(VERSION,'2.2.0.0','>=' )) and (version_compare(VERSION,'3.0.0.0','<' ))) {
			$ocmod_filename = 'ocmod_hb_seourl_2200_23xx.txt';
			$ocmod_name = 'SEO - URL BASIC [2200 - 23xx]';
		}else if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$ocmod_filename = 'ocmod_hb_seourl_3xxx.txt';
			$ocmod_name = 'SEO - URL BASIC [3xxx]';
		}
		
		$ocmod_version 	= $this->hb_extension_version;
		$ocmod_code = 'huntbee_seo_friendly_url_ocmod';	
		$ocmod_author = 'HuntBee OpenCart Services';
		$ocmod_link = 'https://www.huntbee.com';
		
		$file = DIR_APPLICATION . 'view/template/extension/hbseo/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{version}',$ocmod_version,$ocmod_xml);
			$ocmod_xml = str_replace('{name}',$ocmod_name,$ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}
		
		$languages_count = $this->db->query("SELECT count(*) as total FROM `" . DB_PREFIX . "language` WHERE status = 1");
		if ($languages_count->row['total'] > 1) {
			if ((version_compare(VERSION,'2.0.0.0','>=' )) and (version_compare(VERSION,'2.2.0.0','<' ))) {
				$ocmod_filename = 'ocmod_hb_seourl_ML_2000_21xx.txt';
				$ocmod_name = 'SEO - URL Multi-Language [2000 - 21xx]';
			}else if ((version_compare(VERSION,'2.1.0.2','>=' )) and (version_compare(VERSION,'2.3.0.0','<' ))) {
				$ocmod_filename = 'ocmod_hb_seourl_ML_22xx.txt';
				$ocmod_name = 'SEO - URL Multi-Language [22xx]';
			}else if ((version_compare(VERSION,'2.3.0.0','>=' )) and (version_compare(VERSION,'3.0.0.0','<' ))) {
				$ocmod_filename = 'ocmod_hb_seourl_ML_23xx.txt';
				$ocmod_name = 'SEO - URL Multi-Language [23xx]';
			}else if (version_compare(VERSION,'3.0.0.0','>=' )) {
				$ocmod_filename = 'ocmod_hb_seourl_ML_3xxx.txt';
				$ocmod_name = 'SEO - URL Multi-Language [3xxx]';
			}
			
			$file = DIR_APPLICATION . 'view/template/extension/hbseo/ocmod/'.$ocmod_filename;
			if (file_exists($file)) {
				$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
				$ocmod_xml = str_replace('{version}',$ocmod_version,$ocmod_xml);
				$ocmod_xml = str_replace('{name}',$ocmod_name,$ocmod_xml);
				$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
			}
		}
	}

	public function installEvents(){
		//events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('seo_url_product_add');
		$this->model_setting_event->deleteEventByCode('seo_url_product_edit');
		$this->model_setting_event->deleteEventByCode('seo_url_category_add');
		$this->model_setting_event->deleteEventByCode('seo_url_category_edit');
		$this->model_setting_event->deleteEventByCode('seo_url_brand_add');
		$this->model_setting_event->deleteEventByCode('seo_url_brand_edit');
		$this->model_setting_event->deleteEventByCode('seo_url_information_add');
		$this->model_setting_event->deleteEventByCode('seo_url_information_edit');

		$event_data = [];

		$event_data[] = array(
			'code' 			=> 'seo_url_product_add',
			'trigger'		=> 'admin/model/catalog/product/addProduct/after',
			'action'		=> 'extension/hbseo/hb_seourl/event_add_product',
			'status' 		=> TRUE,
			'sort_order'	=>	'1'
		);

		$event_data[] = array(
			'code' 			=> 'seo_url_product_edit',
			'trigger'		=> 'admin/model/catalog/product/editProduct/after',
			'action'		=> 'extension/hbseo/hb_seourl/event_add_product',
			'status' 		=> TRUE,
			'sort_order'	=>	'1'
		);

		$event_data[] = array(
			'code' 			=> 'seo_url_category_add',
			'trigger'		=> 'admin/model/catalog/category/addCategory/after',
			'action'		=> 'extension/hbseo/hb_seourl/event_add_category',
			'status' 		=> TRUE,
			'sort_order'	=>	'1'
		);

		$event_data[] = array(
			'code' 			=> 'seo_url_category_edit',
			'trigger'		=> 'admin/model/catalog/category/editCategory/after',
			'action'		=> 'extension/hbseo/hb_seourl/event_add_category',
			'status' 		=> TRUE,
			'sort_order'	=>	'1'
		);

		$event_data[] = array(
			'code' 			=> 'seo_url_brand_add',
			'trigger'		=> 'admin/model/catalog/manufacturer/addManufacturer/after',
			'action'		=> 'extension/hbseo/hb_seourl/event_add_brand',
			'status' 		=> TRUE,
			'sort_order'	=>	'1'
		);

		$event_data[] = array(
			'code' 			=> 'seo_url_brand_edit',
			'trigger'		=> 'admin/model/catalog/manufacturer/editManufacturer/after',
			'action'		=> 'extension/hbseo/hb_seourl/event_add_brand',
			'status' 		=> TRUE,
			'sort_order'	=>	'1'
		);

		$event_data[] = array(
			'code' 			=> 'seo_url_information_add',
			'trigger'		=> 'admin/model/catalog/information/addInformation/after',
			'action'		=> 'extension/hbseo/hb_seourl/event_add_information',
			'status' 		=> TRUE,
			'sort_order'	=>	'1'
		);

		$event_data[] = array(
			'code' 			=> 'seo_url_information_edit',
			'trigger'		=> 'admin/model/catalog/information/editInformation/after',
			'action'		=> 'extension/hbseo/hb_seourl/event_add_information',
			'status' 		=> TRUE,
			'sort_order'	=>	'1'
		);

		foreach ($event_data as $data){
			$this->addEvent($data);
		}	
	}

	public function addEvent(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET `code` = '" . $this->db->escape($data['code']) . "', `trigger` = '" . $this->db->escape($data['trigger']) . "', `action` = '" . $this->db->escape($data['action']) . "', `status` = '" . (bool)$data['status'] . "', `sort_order` = '" . (int)$data['sort_order'] . "'");

		return $this->db->getLastId();
	}

	public function isExtensionInstalled($code){
		$query = $this->db->query("SELECT count(*) as total FROM `".DB_PREFIX."extension` WHERE `code` = '".$this->db->escape($code)."'");	
		if ($query->row['total'] > 0){
			return true;
		}else{
			return false;
		}
	}

	public function getKeywordCountbyType($query, $language_id, $store_id) {
		$result = $this->db->query("SELECT count(*) as total FROM `".DB_PREFIX."seo_url` WHERE `query` LIKE '".$this->db->escape($query)."%' AND `language_id` = '".(int)$language_id."' AND `store_id` = '".(int)$store_id."'");
		return $result->row['total'];
	}
	
	public function getProductCount(int $language_id, int $store_id): int{
		$query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "product_description pd LEFT JOIN " . DB_PREFIX . "product p ON (pd.product_id = p.product_id) WHERE pd.language_id = '".(int)$language_id."'");
		return $query->row['total'];
	}

	public function getCategoryCount(int $language_id, $store_id): int{
		$query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "category_description cd LEFT JOIN " . DB_PREFIX . "category c ON (cd.category_id = c.category_id) WHERE cd.language_id = '".(int)$language_id."'");
		return $query->row['total'];
	}
	
	public function getBrandCount(int $language_id, $store_id): int{
		$query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "manufacturer");
		return $query->row['total'];
	}
	
	public function getInformationCount(int $language_id, $store_id): int{
		$query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$language_id."'");
		return $query->row['total'];
	}

	public function getProducts(int $language_id, int $store_id, int $start, int $limit): array{
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT p.*, pd.*, m.name as brand FROM " . DB_PREFIX . "product_description pd LEFT JOIN " . DB_PREFIX . "product p ON (pd.product_id = p.product_id) LEFT JOIN `" . DB_PREFIX . "manufacturer` m ON (p.`manufacturer_id` = m.`manufacturer_id`) WHERE pd.language_id = '".(int)$language_id."' LIMIT " . (int)$start . "," . (int)$limit);
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}

	public function getProduct(int $product_id, int $language_id): array {
		$query = $this->db->query("SELECT p.*, pd.*, m.name as brand FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`) LEFT JOIN `" . DB_PREFIX . "manufacturer` m ON (p.`manufacturer_id` = m.`manufacturer_id`) WHERE p.`product_id` = '" . (int)$product_id . "' AND pd.`language_id` = '" . (int)$language_id . "' LIMIT 1");

		return $query->row;
	}

	public function getCategories(int $language_id, int $store_id = 0, int $start = 0, int $limit = 20): array{
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT cd.* FROM " . DB_PREFIX . "category_description cd LEFT JOIN " . DB_PREFIX . "category c ON (cd.category_id = c.category_id) WHERE cd.language_id = '".(int)$language_id."' LIMIT " . (int)$start . "," . (int)$limit);
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}

	public function getCategory(int $category_id, int $language_id): array {
		$query = $this->db->query("SELECT cd.* FROM `" . DB_PREFIX . "category_description` cd LEFT JOIN `" . DB_PREFIX . "category` c ON (cd.category_id = c.category_id) WHERE cd.category_id = '".(int)$category_id."' AND cd.language_id = '".(int)$language_id."' LIMIT 1");
		return $query->row;
	}

	public function getBrands(int $language_id, $store_id = 0, int $start = 0, int $limit = 20): array{
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT *, '".(int)$language_id."' as language_id  FROM " . DB_PREFIX . "manufacturer LIMIT " . (int)$start . "," . (int)$limit);
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}

	public function getBrand(int $manufacturer_id, int $language_id): array{
		$query = $this->db->query("SELECT *, '".(int)$language_id."' as language_id  FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '".(int)$manufacturer_id."' LIMIT 1 ");
		return $query->row;
	}
	
	public function getInformations(int $language_id, $store_id = 0, int $start = 0, int $limit = 20): array{
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$language_id."' LIMIT " . (int)$start . "," . (int)$limit);
		if ($query->num_rows) {
			return $query->rows;
		} else {
			return [];
		}
	}

	public function getInformation($information_id, $language_id): array{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE i.information_id = '".(int)$information_id."' AND id.language_id = '".(int)$language_id."' LIMIT 1");
		return $query->row;
	}

	public function generateKeyword(int $store_id, string $type, array $data, array $extension_settings): void{
		$this->clearEmptyKeywords();
		
		$preserve		= $extension_settings['preserve'];
		$transliterate	= $extension_settings['transliterate'];

		switch ($type) {
			case 'product':
				$this->addlog('Initializing Keyword Generation for Product ID '.$data['product_id']);

				$template		= $extension_settings['template'];
				$seo_query 		= 'product_id='.$data['product_id'];

				if ($this->isKeywordNotAvailable($store_id, $data['language_id'], $seo_query)) {
					foreach ($data as $key => $value) {
						if (!is_array($value)) {
							$template 	= str_replace('{'.$key.'}',$value ?? '', $template);
						}
					}

					$slug = $this->slugify($template, $transliterate);

					//check for duplicates
					if ($this->is_duplicate_keyword($store_id, 0, $slug)) {
						$slug = $slug.'-'.$data['product_id'].'-'.$data['language_id'];
						$this->addlog('Product Keyword Already exists for Store ID - '.$store_id.' :: Lang ID - '.$data['language_id'].' :: [keyword: '.$slug.']');
					}

					//insert record to seo url table
					$this->addKeyword($store_id, $data['language_id'], $seo_query, $slug, $preserve);
					$this->addlog('Generated Keyword for Product ID '.$data['product_id'].' :: Store ID - '.$store_id.' :: Lang ID - '.$data['language_id'].' :: [keyword: '.$slug.']');
				}else{
					$this->addlog('Keyword exists for Product ID '.$data['product_id'].' :: Store ID - '.$store_id.' :: Lang ID - '.$data['language_id']);
				}

			break;

			case 'category':
				$this->addlog('Initializing Keyword Generation for Category ID '.$data['category_id']);

				$seo_query 		= 'category_id='.$data['category_id'];

				if ($this->isKeywordNotAvailable($store_id, $data['language_id'], $seo_query)) {
					$template = $data['name'];					

					$slug = $this->slugify($template, $transliterate);

					//check for duplicates
					if ($this->is_duplicate_keyword($store_id, 0, $slug)) {
						$slug = $slug.'-'.$data['category_id'].'-'.$data['language_id'];
						$this->addlog('Category Keyword Already exists for Store ID - '.$store_id.' :: Lang ID - '.$data['language_id'].' :: [keyword: '.$slug.']');
					}

					//insert record to seo url table
					$this->addKeyword($store_id, $data['language_id'], $seo_query, $slug, $preserve);
					$this->addlog('Generated Keyword for Category ID '.$data['category_id'].' :: Store ID - '.$store_id.' :: Lang ID - '.$data['language_id'].' :: [keyword: '.$slug.']');
				}
			break;
			
			case 'manufacturer':
				$this->addlog('Initializing Keyword Generation for Manufacturer ID '.$data['manufacturer_id']);
				$seo_query 		= 'manufacturer_id='.$data['manufacturer_id'];

				if ($this->isKeywordNotAvailable($store_id, $data['language_id'], $seo_query)) {
					$template = $data['name'];					

					$slug = $this->slugify($template, $transliterate);

					//check for duplicates
					if ($this->is_duplicate_keyword($store_id, 0, $slug)) {
						$slug = $slug.'-'.$data['manufacturer_id'].'-'.$data['language_id'];
						$this->addlog('Manufacturer Keyword Already exists for Store ID - '.$store_id.' :: Lang ID - '.$data['language_id'].' :: [keyword: '.$slug.']');
					}

					//insert record to seo url table
					$this->addKeyword($store_id, $data['language_id'], $seo_query, $slug, $preserve);
					$this->addlog('Generated Keyword for Manufacturer ID '.$data['manufacturer_id'].' :: Store ID - '.$store_id.' :: Lang ID - '.$data['language_id'].' :: [keyword: '.$slug.']');
				}
			break;

			case 'information':
				$this->addlog('Initializing Keyword Generation for Information ID '.$data['information_id']);				
				$seo_query 		= 'information_id='.$data['information_id'];

				if ($this->isKeywordNotAvailable($store_id, $data['language_id'], $seo_query)) {
					$template = $data['title'];

					$slug = $this->slugify($template, $transliterate);

					//check for duplicates
					if ($this->is_duplicate_keyword($store_id, 0, $slug)) {
						$slug = $slug.'-'.$data['information_id'].'-'.$data['language_id'];
						$this->addlog('Information Keyword Already exists for Store ID - '.$store_id.' :: Lang ID - '.$data['language_id'].' :: [keyword: '.$slug.']');
					}

					//insert record to seo url table
					$this->addKeyword($store_id, $data['language_id'], $seo_query, $slug, $preserve);
					$this->addlog('Generated Keyword for Information ID '.$data['information_id'].' :: Store ID - '.$store_id.' :: Lang ID - '.$data['language_id'].' :: [keyword: '.$slug.']');
				}
			break;
		}
	}

	public function isKeywordNotAvailable(int $store_id, int $language_id, string $query): bool{
		$query = $this->db->query("SELECT count(*) as count FROM `" . DB_PREFIX . "seo_url` WHERE `store_id` = '".(int)$store_id."' AND `language_id` = '".(int)$language_id."' AND `query` = '".$this->db->escape($query)."'");
	
		if ($query->row['count'] == 0) {
			return true;
		}else{
			return false;
		}
	}

	public function is_duplicate_keyword(int $store_id, int $language_id, string $keyword): bool{
		if ($language_id != 0) {
			$query = $this->db->query("SELECT count(*) as count FROM `" . DB_PREFIX . "seo_url` WHERE `keyword` = '".$this->db->escape($keyword)."' AND `store_id` = '".(int)$store_id."' AND `language_id` = '".(int)$language_id."'");
		}else{
			$query = $this->db->query("SELECT count(*) as count FROM `" . DB_PREFIX . "seo_url` WHERE `keyword` = '".$this->db->escape($keyword)."' AND `store_id` = '".(int)$store_id."'");
		}
		if ($query->row['count'] > 0) {
			$this->addlog('Keyword '.$keyword.' already exists :: Store ID: '.$store_id.' :: Lang: '.$language_id);
			return true;
		}else{
			return false;
		}
	}

	public function addKeyword(int $store_id, int $language_id, string $query, string $keyword, $preserve = false): void{
		$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` (`store_id`,`language_id`,`query`,`keyword`) VALUES ('".(int)$store_id."', '".(int)$language_id."', '".$this->db->escape($query)."', '".$this->db->escape($keyword)."')");

		if ($preserve) {
			$this->db->query("UPDATE `".DB_PREFIX."hb_url_preserve` SET `new_keyword` = '".$this->db->escape($keyword)."' WHERE `query` = '".$this->db->escape($query)."' AND `language_id` = '".(int)$language_id."' AND `store_id` = '".(int)$store_id."'");
		}
	}

	public function getDistinctRoutes(int $store_id): array {
		$query = $this->db->query("SELECT DISTINCT(query) as query FROM `".DB_PREFIX."seo_url` WHERE`query` NOT LIKE 'product_id=%' AND `query` NOT LIKE 'category_id=%' AND `query` NOT LIKE 'manufacturer_id=%' AND `query` NOT LIKE 'information_id=%' AND `query` NOT LIKE 'language_id=%' AND store_id = '".(int)$store_id."' ORDER BY `query` ASC");
		if ($query->rows){
			return $query->rows;
		}else{
			return [];
		}
	}

	public function getKeywordRows(int $store_id, int $language_id, string $query): array {		
		$query = $this->db->query("SELECT * FROM `".DB_PREFIX."seo_url` WHERE `query` = '".$this->db->escape($query)."' AND `store_id` = '".(int)$store_id."' AND `language_id` = '".(int)$language_id."' LIMIT 1");
		
		if ($query->row) {
			return $query->row;
		} else {
			return [];
		}
	}

	public function getKeyword(int $store_id, int $language_id, string $query): string {
		$query = $this->db->query("SELECT * FROM `".DB_PREFIX."seo_url` WHERE `query` = '".$this->db->escape($query)."' AND `store_id` = '".(int)$store_id."' AND `language_id` = '".(int)$language_id."' LIMIT 1");
		
		if ($query->row) {
			return $query->row['keyword'];
		} else {
			return '';
		}
	}

	public function addRoutes(int $store_id, int $language_id, string $query, string $keyword): void{
		$record = $this->getKeywordRows($store_id, $language_id, $query);

		if ($record){
			$this->db->query("UPDATE `".DB_PREFIX."seo_url` SET `keyword` = '".$this->db->escape($keyword)."' WHERE `query` = '".$this->db->escape($query)."' AND `language_id` = '".(int)$language_id."'  AND `store_id` = '".(int)$store_id."'");
			$this->addlog("UPDATE `".DB_PREFIX."seo_url` SET `keyword` = '".$this->db->escape($keyword)."' WHERE `query` = '".$this->db->escape($query)."' AND `language_id` = '".(int)$language_id."'  AND `store_id` = '".(int)$store_id."'");
		}else{
			$this->db->query("INSERT INTO `".DB_PREFIX."seo_url` (`store_id`, `language_id`, `query`, `keyword`) VALUES ('".(int)$store_id."', '".(int)$language_id."', '".$this->db->escape($query)."', '".$this->db->escape($keyword)."') ");
			$this->addlog("INSERT INTO `".DB_PREFIX."seo_url` (`store_id`, `language_id`, `query`, `keyword`) VALUES ('".(int)$store_id."', '".(int)$language_id."', '".$this->db->escape($query)."', '".$this->db->escape($keyword)."') ");
		}
	}

	public function deleteRoutes(string $value, int $store_id): void{
		$this->db->query("DELETE FROM `".DB_PREFIX."seo_url` WHERE `query` = '".$this->db->escape($value)."' AND `store_id` = '".(int)$store_id."'");
		$this->addlog("DELETE FROM `".DB_PREFIX."seo_url` AND `query` = '".$this->db->escape($value)."' AND `store_id` = '".(int)$store_id."'");
	}

	public function deleteAllRoutes(int $store_id): void{
		$this->db->query("DELETE FROM `".DB_PREFIX."seo_url` WHERE `key` = 'route' AND `store_id` = '".(int)$store_id."'");
		$this->addlog("DELETE FROM `".DB_PREFIX."seo_url` WHERE `key` = 'route' AND `store_id` = '".(int)$store_id."'");
	}

	public function default_routes(): array{
		$default_routes =  array(
			'product/special' 				=> 'offers',
			'information/contact' 			=> 'contact',
			'information/sitemap' 			=> 'sitemap',
			'information/product' 			=> 'product',
			'checkout/cart' 				=> 'cart',
			'checkout/checkout' 			=> 'checkout',
			'account/login' 				=> 'login',
			'account/register' 				=> 'register',
			'account/account' 				=> 'account',
			'account/logout' 				=> 'logout',
			'account/edit' 					=> 'edit-account',
			'account/password' 				=> 'change-password',
			'account/wishlist' 				=> 'wishlist',
			'account/order' 				=> 'orders',
			'account/download' 				=> 'downloads',
			'account/newsletter' 			=> 'newsletter-preference',
			'account/voucher' 				=> 'gift-voucher',
			'account/forgotten' 			=> 'forgot-password',
			'account/address' 				=> 'address-book',
			'account/recurring' 			=> 'recurring-payments',
			'account/reward' 				=> 'rewards',
			'account/transaction' 			=> 'wallet',
			'account/returns' 				=> 'returns',
			'account/returns/add' 			=> 'returns-form',
			'affiliate/login' 				=> 'affiliate-login',
		);

		return $default_routes;
	}

	public function getPreserved(array $data = []): array {
		$sql = "SELECT * FROM `".DB_PREFIX."hb_url_preserve`";
		if (!empty($data['search'])) {
			$sql .= " WHERE (`old_keyword` LIKE '%".$this->db->escape($data['search'])."%' OR `new_keyword` LIKE '%".$this->db->escape($data['search'])."%')";
		}
		$sql .= " ORDER BY date_added DESC";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getTotalPreserved(array $data = []): int{
		$sql = "SELECT COUNT(*) AS `total` FROM `".DB_PREFIX."hb_url_preserve`";
		if (!empty($data['search'])) {
			$sql .= " WHERE (`old_keyword` LIKE '%".$this->db->escape($data['search'])."%' OR `new_keyword` LIKE '%".$this->db->escape($data['search'])."%')";
		}
		$results = $this->db->query($sql);
		return (int)$results->row['total'];
	}

	public function getPreserveDate(int $store_id) : string {
		$result = $this->db->query("SELECT date_added FROM `".DB_PREFIX."hb_url_preserve` WHERE `store_id` = '".(int)$store_id."' ORDER BY `date_added` DESC LIMIT 1");	
		if ($result->row) {
			return $result->row['date_added'];
		}else{
			return '';
		}		
	}

	public function preserveKeywords(int $store_id): void {
		$sql = "INSERT INTO `".DB_PREFIX."hb_url_preserve` (`store_id`, `language_id`, `query`, `old_keyword`)";
		
		$sql .= " SELECT `store_id`, `language_id`, `query`, `keyword` FROM `" . DB_PREFIX . "seo_url` WHERE `store_id` = '".(int)$store_id."'";
		
		$this->db->query($sql);
	}

	public function clearPreserve(int $store_id): void {
		$this->db->query("DELETE FROM `".DB_PREFIX."hb_url_preserve` WHERE `store_id` = '".(int)$store_id."'");
	}

	public function getPreserveRecords(int $store_id): array {
		$query = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_url_preserve` WHERE `store_id` = '".(int)$store_id."' AND new_keyword <> '' AND old_keyword <> new_keyword");
		if ($query->rows) {
			return $query->rows;
		} else {
			return [];
		}
	}	
	
	public function deleteKeywordbyKeyValue(int $store_id, int $language_id, string $query): void{
		$this->db->query("DELETE FROM `".DB_PREFIX."seo_url` WHERE `query` = '".$this->db->escape($query)."' AND `language_id` = '".(int)$language_id."'  AND `store_id` = '".(int)$store_id."'");
		$this->addlog("DELETE FROM `".DB_PREFIX."seo_url` WHERE `query` = '".$this->db->escape($query)."' AND `language_id` = '".(int)$language_id."'  AND `store_id` = '".(int)$store_id."'");
	}

	public function slugify(string $text = '', bool $transliterate = true): string {
		$text = htmlspecialchars_decode($text);
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		//$text = mb_convert_encoding((string)$text, 'UTF-8', mb_list_encodings());
		if ($transliterate) {
			//$text = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $text);
			$text = $this->transliterate($text);
			$text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
		}

		$text = preg_replace('/[^a-zA-Z0-9]+/', '-', $text);
		$text = trim($text, '-');
		$text = strtolower($text);
	
		return $text;
	}

	public function clearEmptyKeywords(): void{
		$this->db->query("DELETE FROM `".DB_PREFIX."seo_url` WHERE `keyword` = '' ");
	}

	public function clear_keyword_by_type($query, $store_id, $language_id) {
		if ($language_id != 0) {
			$this->db->query("DELETE FROM `".DB_PREFIX."seo_url` WHERE `query` LIKE '".$this->db->escape($query)."%' AND `store_id` = '".(int)$store_id."' AND language_id = '".(int)$language_id."'");
		}else{
			$this->db->query("DELETE FROM `".DB_PREFIX."seo_url` WHERE `query` LIKE '".$this->db->escape($query)."%' AND `store_id` = '".(int)$store_id."'");
		}
	}

	public function generate_dynamic_keyword(string $title, int $store_id, int $language_id, string $key, int $count): string{
		$slug = '';
		
		$slug = $this->slugify($title, true);

		if ($this->is_duplicate_keyword($store_id, $language_id, $key, $slug)) {
			$count = $count + 1;
			$slug = $slug.'-'.$count;
			if ($this->is_duplicate_keyword($store_id, $language_id, $key, $slug)) {
				$slug = $this->generate_dynamic_keyword($title, $store_id, $language_id, $key, $count);
			}
		}

		return $slug;
	}
	
	public function addlog($text = ''){
		if (!file_exists(DIR_LOGS)) {
			mkdir(DIR_LOGS, 0777, true);
		}

		$file = DIR_LOGS . 'huntbee_seo_url_logs.txt';

		if (file_exists($file)) {
			$size = filesize($file);
			if ($size > 5242880){
				$handle = fopen($file, 'w+');
				fclose($handle);
			}
		}

		$fp = fopen($file, 'a');
		fwrite($fp, "\r\n".date('d-M-Y G:i:s A') . ' - ' .$text);
		fclose($fp);
	}

	public function transliterate(string $str): string{
		$char_map = array(
			// Latin
			'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
			'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
			'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
			'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
			'ß' => 'ss',
			'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
			'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
			'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
			'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
			'ÿ' => 'y',
			 
			// Latin symbols
			'©' => '(c)',
			 
			// Greek
			'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
			'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
			'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
			'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
			'Ϋ' => 'Y', 
			'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
			'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
			'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
			'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
			'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
			 
			// Turkish
			'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
			'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
			 
			// Russian
			'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
			'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
			'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
			'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
			'Я' => 'Ya',
			'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
			'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
			'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
			'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
			'я' => 'ya',
			 
			// Ukrainian
			'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
			'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
			 
			// Czech
			'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
			'Ž' => 'Z',
			'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
			'ž' => 'z',
			 
			// Polish
			'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
			'Ż' => 'Z',
			'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
			'ż' => 'z',
			
			//Arabic
			"ا"=>"a", "أ"=>"a", "آ"=>"a", "إ"=>"e", "ب"=>"b", "ت"=>"t", "ث"=>"th", "ج"=>"j",
			"ح"=>"h", "خ"=>"kh", "د"=>"d", "ذ"=>"d", "ر"=>"r", "ز"=>"z", "س"=>"s", "ش"=>"sh",
			"ص"=>"s", "ض"=>"d", "ط"=>"t", "ظ"=>"z", "ع"=>"'e", "غ"=>"gh", "ف"=>"f", "ق"=>"q",
			"ك"=>"k", "ل"=>"l", "م"=>"m", "ن"=>"n", "ه"=>"h", "و"=>"w", "ي"=>"y", "ى"=>"a",
			"ئ"=>"'e", "ء"=>"'",   
			"ؤ"=>"'e", "لا"=>"la", "ة"=>"h", "؟"=>"?", "!"=>"!", 
			"ـ"=>"", 
			"،"=>",", 
			"َ‎"=>"a", "ُ"=>"u", "ِ‎"=>"e", "ٌ"=>"un", "ً"=>"an", "ٍ"=>"en", "ّ"=>"",
			
			//persian
			"ا" => "a", "أ" => "a", "آ" => "a", "إ" => "e", "ب" => "b", "ت" => "t", "ث" => "th",
			"ج" => "j", "ح" => "h", "خ" => "kh", "د" => "d", "ذ" => "d", "ر" => "r", "ز" => "z",
			"س" => "s", "ش" => "sh", "ص" => "s", "ض" => "d", "ط" => "t", "ظ" => "z", "ع" => "'e",
			"غ" => "gh", "ف" => "f", "ق" => "q", "ك" => "k", "ل" => "l", "م" => "m", "ن" => "n",
			"ه" => "h", "و" => "w", "ي" => "y", "ى" => "a", "ئ" => "'e", "ء" => "'", 
			"ؤ" => "'e", "لا" => "la", "ک" => "ke", "پ" => "pe", "چ" => "che", "ژ" => "je", "گ" => "gu",
			"ی" => "a", "ٔ" => "", "ة" => "h", "؟" => "?", "!" => "!", 
			"ـ" => "", 
			"،" => ",", 
			"َ‎" => "a", "ُ" => "u", "ِ‎" => "e", "ٌ" => "un", "ً" => "an", "ٍ" => "en", "ّ" => "",
			 
			// Latvian
			'Ā'  =>  'A', 'Č'  =>  'C', 'Ē'  =>  'E', 'Ģ'  =>  'G', 'Ī'  =>  'i', 'Ķ'  =>  'k', 'Ļ'  =>  'L', 'Ņ'  =>  'N',
			'Š'  =>  'S', 'Ū'  =>  'u', 'Ž'  =>  'Z',
			'ā'  =>  'a', 'č'  =>  'c', 'ē'  =>  'e', 'ģ'  =>  'g', 'ī'  =>  'i', 'ķ'  =>  'k', 'ļ'  =>  'l', 'ņ'  =>  'n',
			'š'  =>  's', 'ū'  =>  'u', 'ž'  =>  'z'
			);

			return $str = str_replace(array_keys($char_map), $char_map, $str);
	}
	
}
?>