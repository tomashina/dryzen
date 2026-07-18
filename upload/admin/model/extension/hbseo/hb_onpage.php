<?php
class ModelExtensionHbseoHbOnpage extends Model {
	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hb_onpage_templates` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `template` VARCHAR(200) NOT NULL,
			  `page_type` VARCHAR(100) NOT NULL,
			  `element_type` VARCHAR(100) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL,
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)");
		
		$tables = [
			'product_description' => [
				'columns' => [
					'h1'          => 'VARCHAR(300) AFTER `meta_keyword`',
					'h2'          => 'VARCHAR(300) AFTER `h1`',
					'image_alt'   => 'VARCHAR(300) AFTER `h2`',
					'image_title' => 'VARCHAR(300) AFTER `image_alt`'
				]
			],
			'category_description' => [
				'columns' => [
					'h1'          => 'VARCHAR(300) AFTER `meta_keyword`',
					'h2'          => 'VARCHAR(300) AFTER `h1`',
					'image_alt'   => 'VARCHAR(300) AFTER `h2`',
					'image_title' => 'VARCHAR(300) AFTER `image_alt`'
				]
			]
		];
		
		foreach ($tables as $table => $data) {
			foreach ($data['columns'] as $column => $definition) {
				$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "$table` LIKE '$column'");
				if (!$check->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "$table` ADD `$column` $definition");
				}
			}
		}		

		//MANUFACTURER MULTI-LANGUAGE
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "manufacturer_description` (
			`manufacturer_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` VARCHAR(100) NOT NULL,
			`description` TEXT NOT NULL,
			`meta_title` VARCHAR(300) NOT NULL,
			`meta_description` VARCHAR(300) NOT NULL,
			`meta_keyword` VARCHAR(300) NOT NULL,
			`h1` VARCHAR(300) NOT NULL,
			`h2` VARCHAR(300) NOT NULL,
			`image_alt` VARCHAR(300) NOT NULL,
			`image_title` VARCHAR(300) NOT NULL,
			PRIMARY KEY (`manufacturer_id`, `language_id`)
		)");

		// Insert data into manufacturer_description table based on manufacturer table and language ID
		$languages = $this->db->query("SELECT language_id FROM `" . DB_PREFIX . "language`")->rows;
		$manufacturers = $this->db->query("SELECT manufacturer_id, name FROM `" . DB_PREFIX . "manufacturer`")->rows;

		foreach ($manufacturers as $manufacturer) {
			foreach ($languages as $language) {
				$check = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manufacturer_description` WHERE `manufacturer_id` = '" . (int)$manufacturer['manufacturer_id'] . "' AND `language_id` = '" . (int)$language['language_id'] . "'");
				if (!$check->num_rows) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "manufacturer_description` 
						(`manufacturer_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `h1`, `h2`, `image_alt`, `image_title`) 
						VALUES ('" . (int)$manufacturer['manufacturer_id'] . "', '" . (int)$language['language_id'] . "', '" . $this->db->escape($manufacturer['name']) . "', '', '', '', '', '', '', '', '')");
				}
			}
		}
		
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'huntbee_onpage_tags_ocmod'");

		$this->installOcmod();
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_onpage_templates`");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "modification` WHERE `code` = 'huntbee_onpage_tags_ocmod'");
		$tables = [
			'product_description' => ['h1', 'h2', 'image_alt', 'image_title'],
			'category_description'=> ['h1', 'h2', 'image_alt', 'image_title'],
			'manufacturer'        => [
				'language_id',
				'brand_description',
				'meta_title',
				'meta_description',
				'meta_keyword',
				'h1',
				'h2',
				'image_alt',
				'image_title'
			]//for versions old than 3.9.0, we need to remove these columns
		];
		
		foreach ($tables as $table => $columns) {
			foreach ($columns as $column) {
				$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "$table` LIKE '$column'");
				if ($check->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "$table` DROP COLUMN `$column`");
				}
			}
		}	
		
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "manufacturer_description`");
	}

	public function update() {
		$tables = [
			'manufacturer'        => [
				'language_id',
				'brand_description',
				'meta_title',
				'meta_description',
				'meta_keyword',
				'h1',
				'h2',
				'image_alt',
				'image_title'
			]//for versions old than 3.9.0, we need to remove these columns
		];
		
		foreach ($tables as $table => $columns) {
			foreach ($columns as $column) {
				$check = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "$table` LIKE '$column'");
				if ($check->num_rows) {
					$this->db->query("ALTER TABLE `" . DB_PREFIX . "$table` DROP COLUMN `$column`");
				}
			}
		}	
		
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "manufacturer_description`");

		$this->install();
	}
	
	public function installOcmod() {
		$theme = str_replace('theme_', '', $this->config->get('config_theme'));
		
		if ($theme == 'journal3') {
			$template_name = 'journal3';
		} else {
			$template_name = 'default';
		}
		
		$ocmod_filename = 'ocmod_seo_onpage_3xxx_' . $template_name . '.txt';
		$ocmod_name = 'SEO - On-Page Tags Elements [' . $template_name . '][3xxx]';		
		
		$ocmod_version = $this->hb_extension_version;
		$ocmod_code    = 'huntbee_onpage_tags_ocmod';	
		$ocmod_author  = 'HuntBee OpenCart Services';
		$ocmod_link    = 'https://www.huntbee.com/';
		
		$file = DIR_APPLICATION . 'view/template/extension/hbseo/ocmod/' . $ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{version}', $ocmod_version, $ocmod_xml);
			$ocmod_xml = str_replace('{name}', $ocmod_name, $ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}
	}
	
	public function sampletemplates($language_id, $store_id, $store_name){
		$templates = [
			// Product templates
			['{name}', 'product', 'meta_title'],
			['{name} | ' . $store_name, 'product', 'meta_title'],
			['{name} | {manufacturer} | ' . $store_name, 'product', 'meta_title'],
			['{name} - {description}', 'product', 'meta_description'],
			['{description}', 'product', 'meta_description'],
			['Buy {name}, {manufacturer} from ' . $store_name . '. Fast & Free Home Delivery. High Quality Service', 'product', 'meta_description'],
			['{name} {category} {model} {manufacturer} {tag}', 'product', 'meta_keyword'],
			['buy {xname}, buy {xname} online, online shopping {xname}, {xcategory}, {xcategory} {xname}, {xb} {xname} {xm}, quality {xname} {xcategory}, best price {xname}, less price {xname}', 'product', 'meta_keyword'],
			['{name} {manufacturer} {model} {upc}', 'product', 'h1'],
			['{name}', 'product', 'h1'],
			['{name}', 'product', 'h2'],
			['{name} | {manufacturer} | {category}', 'product', 'h2'],
			['{name} image', 'product', 'image_alt'],
			['{name} {category} image', 'product', 'image_alt'],
			['Showing image for {name}', 'product', 'image_title'],
	
			// Category templates
			['{name} - ' . $store_name, 'category', 'meta_title'],
			['Buy Best {name} Products from ' . $store_name, 'category', 'meta_title'],
			['{description}', 'category', 'meta_description'],
			['{category} - {description}', 'category', 'meta_description'],
			['Buy best and quality {name} products at less price only from ' . $store_name . '. Fast and free home delivery', 'category', 'meta_description'],
			['buy {xname}, buy {xname} products, best {xname} products, low price {xname}, high quality {xname} products, online {xname} products, buy {xname} online', 'category', 'meta_keyword'],
			['{name}', 'category', 'h1'],
			['Best {name} products', 'category', 'h1'],
			['Buy best and quality {name} products', 'category', 'h2'],
			['Quality {name} products from ' . $store_name, 'category', 'h2'],
			['{name} image', 'category', 'image_alt'],
			['Showing image for {name}', 'category', 'image_title'],
	
			// Brand templates
			['{name} - ' . $store_name, 'manufacturer', 'meta_title'],
			['{name} Products from ' . $store_name, 'manufacturer', 'meta_title'],
			['Buy Best {name} Products from ' . $store_name, 'manufacturer', 'meta_title'],
			['{name} - {description}', 'manufacturer', 'meta_description'],
			['{name}', 'manufacturer', 'meta_description'],
			['Buy best and quality {name} products at less price only from ' . $store_name . '. Fast and free home delivery', 'manufacturer', 'meta_description'],
			['buy {xname}, buy {xname} products, best {xname} products, low price {xname}, high quality {xname} products, online {xname} products, buy {xname} online', 'manufacturer', 'meta_keyword'],
			['Best {name} products', 'manufacturer', 'h1'],
			['Buy best and quality {name} products', 'manufacturer', 'h2'],
			['{name} image', 'manufacturer', 'image_alt'],
			['Showing image for {name}', 'manufacturer', 'image_title'],
	
			// Information templates
			['{name} | ' . $store_name, 'information', 'meta_title'],
			['{name} {description}', 'information', 'meta_description'],
			['{description}', 'information', 'meta_description'],
			['{name} - ' . $store_name . ' . Best Products, Best Price, Best Quality, Free Home Delivery', 'information', 'meta_description'],
			['{name} - ' . $store_name, 'information', 'meta_keyword'],
		];
	
		foreach ($templates as $template) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_onpage_templates` (`template`, `page_type`, `element_type`, `language_id`, `store_id`) VALUES ('" . $this->db->escape($template[0]) . "', '" . $this->db->escape($template[1]) . "', '" . $this->db->escape($template[2]) . "', '" . (int)$language_id . "', '" . (int)$store_id . "')");
		}
	}
	
	public function defaultLanguage() {
		$query = $this->db->query("SELECT language_id FROM `" . DB_PREFIX . "language` WHERE `code` = '".$this->config->get('config_language')."'");
		return $query->row['language_id'];
	}
	
	public function addTemplate($data) {
		foreach ($data['template'] as $language_id => $template) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_onpage_templates` (`template`,`page_type`,`element_type`,`language_id`,`store_id`) 
			VALUES ('".$this->db->escape($template)."','".$this->db->escape($data['page_type'])."','".$this->db->escape($data['element_type'])."','".(int)$language_id."','".(int)$data['store_id']."')");
		}
	}
	
	public function getTemplates($data) {
		$results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "hb_onpage_templates` WHERE `page_type` = '".$this->db->escape($data['page_type'])."' AND `element_type` = '".$this->db->escape($data['element_type'])."' AND `language_id` = '".(int)$data['language_id']."' AND `store_id` = '".(int)$data['store_id']."'");
		if ($results->rows) {
			return $results->rows;
		} else {
			return [];
		}
	}
	
	public function deleteTemplate($id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_onpage_templates` WHERE `id` = '".(int)$id."'");
	}

	public function clearAllTemplates() {
		$this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "hb_onpage_templates`");
	}
	
	public function clearTemplatesByElement($element_type, $store_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_onpage_templates` WHERE `element_type` = '" . $this->db->escape($element_type) . "' AND `store_id` = '" . (int)$store_id . "'");
	}

	public function getTotalItems($page_type) {
		$table = DB_PREFIX . $page_type;
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `{$table}`");
	
		return (int)($query->row['total'] ?? 0);
	}
		
	
	public function getCount($page_type, $element_type, $language_id) {
		$table = DB_PREFIX . $page_type . '_description';
	
		$query = $this->db->query("SELECT COUNT(*) as total FROM `{$table}` WHERE `language_id` = '" . (int)$language_id . "' AND `{$element_type}` <> ''");
	
		return (int)($query->row['total'] ?? 0);
	}		
	
	public function clearTags($page_type, $element_type, $store_id) {
		$table = DB_PREFIX . $page_type . '_description';
		$storeTable = DB_PREFIX . $page_type . '_to_store';
		$idField = $page_type . '_id';
	
		$this->db->query("UPDATE {$table} a, {$storeTable} b SET a.{$element_type} = '' WHERE a.{$idField} = b.{$idField} AND b.store_id = '" . (int)$store_id . "'");
	}
	
	public function invalidLanguageEntries($page_type) {
		$table = DB_PREFIX . $page_type . '_description';
	
		$query = $this->db->query("SELECT COUNT(*) as total FROM `{$table}` WHERE language_id NOT IN (SELECT language_id FROM " . DB_PREFIX . "language)");
	
		return (int)($query->row['total'] ?? 0) ?: false;
	}	
	
	public function fixLanguageEntries() {
		$tables = [
			'product_description',
			'category_description',
			'manufacturer_description',
			'information_description'
		];
	
		foreach ($tables as $table) {
			$this->db->query("DELETE FROM " . DB_PREFIX . $table . " WHERE language_id NOT IN (SELECT language_id FROM " . DB_PREFIX . "language)");
		}
	}	
	
	public function titleLengthIssues($page_type) {
		$table = DB_PREFIX . $page_type . '_description';
	
		$query = $this->db->query("SELECT COUNT(*) as total FROM `{$table}` WHERE CHAR_LENGTH(meta_title) NOT BETWEEN 50 AND 60 AND meta_title <> ''");
	
		return (int)($query->row['total'] ?? 0);
	}
	
	
	public function deleteLengthIssues($page_type) {
		$table = DB_PREFIX . $page_type . '_description';
	
		$this->db->query("UPDATE `{$table}` SET meta_title = '' WHERE CHAR_LENGTH(meta_title) NOT BETWEEN 50 AND 60");
	}
	
	
	public function mdLengthIssues($page_type) {
		$table = DB_PREFIX . $page_type . '_description';
	
		$query = $this->db->query("SELECT COUNT(*) as total FROM `{$table}` WHERE CHAR_LENGTH(meta_description) < 100 AND meta_description <> ''");
	
		return (int)($query->row['total'] ?? 0);
	}
		
	
	public function deletemdLengthIssues($page_type) {
		$table = DB_PREFIX . $page_type . '_description';
	
		$this->db->query("UPDATE `{$table}` SET meta_description = '' WHERE CHAR_LENGTH(meta_description) < 100");
	}
	
}
?>