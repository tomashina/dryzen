<?php
class ModelExtensionHbseoRedirectDisabled extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "redirect_disabled_product` (
			`rdp_id` int(11) NOT NULL AUTO_INCREMENT,
			`product_id` int(11) NOT NULL DEFAULT '0',
			`pagetype` VARCHAR(50) DEFAULT NULL,
			`redirect` text,
			`redirect_type` int(11) NOT NULL DEFAULT '302',
			`redirect_hits` int(11) NOT NULL DEFAULT '0',
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`rdp_id`)
		)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "redirect_disabled_category` (
			`rdc_id` int(11) NOT NULL AUTO_INCREMENT,
			`category_id` int(11) NOT NULL DEFAULT '0',
			`pagetype` VARCHAR(50) DEFAULT NULL,
			`redirect` text,
			`redirect_type` int(11) NOT NULL DEFAULT '302',
			`redirect_hits` int(11) NOT NULL DEFAULT '0',
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`rdc_id`)
		)");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "redirect_disabled_information` (
			`rdi_id` int(11) NOT NULL AUTO_INCREMENT,
			`information_id` int(11) NOT NULL DEFAULT '0',
			`pagetype` VARCHAR(50) DEFAULT NULL,
			`redirect` text,
			`redirect_type` int(11) NOT NULL DEFAULT '302',
			`redirect_hits` int(11) NOT NULL DEFAULT '0',
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`rdi_id`)
		)");
			
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "redirect_disabled_logs` (
			`rdl_id` int(11) NOT NULL AUTO_INCREMENT,
			`type` VARCHAR(50) NOT NULL ,
			`item_id` int(11) NOT NULL DEFAULT '0',
			`referrer` text,
			`user_agent` text,
			`ip` varchar(20) DEFAULT NULL,
			`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`rdl_id`)
		)");

		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'redirect_disabled_ocmod'");
		
		$ocmod_filename = 'ocmod_redirect_disabled_3xxx.txt';
		$ocmod_name 	= 'SEO - Redirect Disabled [3xxx]';
		
		$ocmod_version 	= $this->hb_extension_version;
		$ocmod_code 	= 'redirect_disabled_ocmod';	
		$ocmod_author 	= 'HuntBee OpenCart Services';
		$ocmod_link 	= 'https://www.huntbee.com';
		
		$file = DIR_APPLICATION . 'view/template/extension/hbseo/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{huntbee_version}',$ocmod_version,$ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}		
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "redirect_disabled_product`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "redirect_disabled_category`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "redirect_disabled_information`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "redirect_disabled_logs`");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'redirect_disabled_ocmod'");
	}

	//GET DISABLED COUNTS
	public function getTotalDisabledProductCount(){
		$query =  $this->db->query("SELECT count(p.product_id) as total FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE p.status = 0 AND pd.language_id = '".(int)$this->config->get('config_language_id')."'");
		return $query->row['total'];
	}

	public function getTotalDisabledCategoryCount(){
		$query =  $this->db->query("SELECT count(c.category_id) as total FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) WHERE c.status = 0 AND cd.language_id = '".(int)$this->config->get('config_language_id')."'");
		return $query->row['total'];
	}

	public function getTotalDisabledInformationCount(){
		$query =  $this->db->query("SELECT count(i.information_id) as total FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) WHERE i.status = 0 AND id.language_id = '".(int)$this->config->get('config_language_id')."'");
		return $query->row['total'];
	}

	//GET DISABLED RECORDS
	public function getDisabledProducts($data) {
		$sql = "SELECT p.product_id as item_id, pd.name, p.model, rdp.* FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) LEFT JOIN `".DB_PREFIX."redirect_disabled_product` rdp ON (rdp.product_id = p.product_id) WHERE p.status = 0 AND pd.language_id = '".(int)$this->config->get('config_language_id')."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (p.product_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.model) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.sku) LIKE '%".$this->db->escape($data['search'])."%')";
		}
		
		$sql .=  " ORDER BY p.date_modified DESC";

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

	public function getDisabledCategories($data) {
		$sql = "SELECT c.category_id as item_id, cd.name, rdc.* FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN `".DB_PREFIX."redirect_disabled_category` rdc ON (rdc.category_id = c.category_id) WHERE c.status = 0 AND cd.language_id = '".(int)$this->config->get('config_language_id')."'";	
		
		if (!empty($data['search'])) {
			$sql .= " AND (c.category_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.name) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$sql .=  " ORDER BY c.date_modified DESC";

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

	public function getDisabledInformations($data) {
		$sql = "SELECT i.information_id as item_id, id.title as `name`, rdi.* FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) LEFT JOIN `".DB_PREFIX."redirect_disabled_information` rdi ON (rdi.information_id = i.information_id) WHERE i.status = 0 AND id.language_id = '".(int)$this->config->get('config_language_id')."'";	
		
		if (!empty($data['search'])) {
			$sql .= " AND (i.information_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.title) LIKE '%".$this->db->escape($data['search'])."%')";
		}
		
		$sql .=  " ORDER BY id.title";

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

	public function getTotalDisabledProduct($data){
		$sql = "SELECT count(p.product_id) as total FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE p.status = 0 AND pd.language_id = '".(int)$this->config->get('config_language_id')."'";
		if (!empty($data['search'])) {
			$sql .= " AND (p.product_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.model) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.sku) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalDisabledCategory($data){
		$sql = "SELECT count(c.category_id) as total FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) WHERE c.status = 0 AND cd.language_id = '".(int)$this->config->get('config_language_id')."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (c.category_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.name) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalDisabledInformation($data){
		$sql = "SELECT count(i.information_id) as total FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) WHERE i.status = 0 AND id.language_id = '".(int)$this->config->get('config_language_id')."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (i.information_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.title) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getProductName($product_id){
		$query =  $this->db->query("SELECT name FROM `".DB_PREFIX."product_description` WHERE product_id = '".(int)$product_id."' AND language_id = '".(int)$this->config->get('config_language_id')."' LIMIT 1");
		if (isset($query->row['name'])){
			return $query->row['name'];
		}else{
			return $product_id;
		}
	}

	public function getCategoryName($category_id) {
		$query = $this->db->query("SELECT name FROM `".DB_PREFIX."category_description` WHERE category_id = '".(int)$category_id."' AND language_id = '".(int)$this->config->get('config_language_id')."'");
		if (isset($query->row['name'])){
			return $query->row['name'];
		}else{
			return $category_id;
		}
	}

	public function getInformationName($information_id) {
		$query = $this->db->query("SELECT title FROM `".DB_PREFIX."information_description` WHERE information_id = '".(int)$information_id."' AND language_id = '".(int)$this->config->get('config_language_id')."'");
		if (isset($query->row['title'])){
			return $query->row['title'];
		}else{
			return $information_id;
		}
	}

	public function setRedirect($data){
		switch ($data['type']) {
			case 'product':
				$query = $this->db->query("SELECT count(*) as total FROM `" . DB_PREFIX . "redirect_disabled_product` WHERE product_id = '".(int)$data['item_id']."'");
				if ($query->row['total'] > 0){
					$this->db->query("UPDATE `" . DB_PREFIX . "redirect_disabled_product` SET pagetype = '".$this->db->escape($data['pagetype'])."', redirect = '".$this->db->escape($data['redirect'])."', redirect_type = '".(int)$data['redirect_type']."', date_modified = now() WHERE product_id = '".(int)$data['item_id']."'");
				}else{
					$this->db->query("INSERT INTO `" . DB_PREFIX . "redirect_disabled_product` (product_id, pagetype, redirect, redirect_type, date_modified) VALUES ('".(int)$data['item_id']."', '".$this->db->escape($data['pagetype'])."', '".$this->db->escape($data['redirect'])."', '".(int)$data['redirect_type']."', now())");
				}
				break;

			case 'category':
				$query = $this->db->query("SELECT count(*) as total FROM `" . DB_PREFIX . "redirect_disabled_category` WHERE category_id = '".(int)$data['item_id']."'");
				if ($query->row['total'] > 0){
					$this->db->query("UPDATE `" . DB_PREFIX . "redirect_disabled_category` SET pagetype = '".$this->db->escape($data['pagetype'])."', redirect = '".$this->db->escape($data['redirect'])."', redirect_type = '".(int)$data['redirect_type']."', date_modified = now() WHERE category_id = '".(int)$data['item_id']."'");
				}else{
					$this->db->query("INSERT INTO `" . DB_PREFIX . "redirect_disabled_category` (category_id, pagetype, redirect, redirect_type, date_modified) VALUES ('".(int)$data['item_id']."', '".$this->db->escape($data['pagetype'])."', '".$this->db->escape($data['redirect'])."', '".(int)$data['redirect_type']."', now())");
				}
				break;

			case 'information':
				$query = $this->db->query("SELECT count(*) as total FROM `" . DB_PREFIX . "redirect_disabled_information` WHERE information_id = '".(int)$data['item_id']."'");
				if ($query->row['total'] > 0){
					$this->db->query("UPDATE `" . DB_PREFIX . "redirect_disabled_information` SET pagetype = '".$this->db->escape($data['pagetype'])."', redirect = '".$this->db->escape($data['redirect'])."', redirect_type = '".(int)$data['redirect_type']."', date_modified = now() WHERE information_id = '".(int)$data['item_id']."'");
				}else{
					$this->db->query("INSERT INTO `" . DB_PREFIX . "redirect_disabled_information` (information_id, pagetype, redirect, redirect_type, date_modified) VALUES ('".(int)$data['item_id']."', '".$this->db->escape($data['pagetype'])."', '".$this->db->escape($data['redirect'])."', '".(int)$data['redirect_type']."', now())");
				}
				break;
		}		
	}

	public function getLogs($data){
		$sql = "SELECT * FROM `".DB_PREFIX."redirect_disabled_logs` WHERE type = '".$this->db->escape($data['type'])."' AND item_id = '".$this->db->escape($data['item_id'])."'";
		$sql .=  " ORDER BY date_added DESC";

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

	public function getTotalLogs($data){
		$sql = "SELECT count(*) as total FROM `".DB_PREFIX."redirect_disabled_logs` WHERE type = '".$this->db->escape($data['type'])."' AND item_id = '".$this->db->escape($data['item_id'])."'";

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	//not using opencart model because no option for status filter, and we want only active items.
	public function getCategories($data = array()) {
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND cd2.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND c1.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY cp.category_id";

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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

	public function getInformations($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = 1";

		if (!empty($data['filter_name'])) {
			$sql .= " AND id.title LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'id.title',
			'i.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id.title";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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


}
?>