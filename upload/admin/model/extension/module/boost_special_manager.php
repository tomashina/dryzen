<?php
//==============================================
// Special Manager
// Author 	: OpenCartBoost
// Email 	: support@opencartboost.com
// Website 	: http://www.opencartboost.com
//==============================================
class ModelExtensionModuleBoostSpecialManager extends Model {
	public function install() {
		$this->load->model('setting/setting');
		
		$this->model_setting_setting->editSetting('module_boost_special_manager', array('module_boost_special_manager_include' => '1'));
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "boost_category_special` (
				`cat_special_id` int(11) NOT NULL AUTO_INCREMENT,
				`category_id` int(11) NOT NULL,
				`customer_group_id` int(11) NOT NULL,
				`priority` int(5) NOT NULL DEFAULT 1,
				`discount` decimal(15,4) NOT NULL DEFAULT 0.0000,
				`discount_type` varchar(1) NOT NULL DEFAULT '0',
				`date_start` date DEFAULT NULL,
				`date_end` date DEFAULT NULL,
				PRIMARY KEY (`cat_special_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
		
		$this->vqmod_script_dir = substr_replace(DIR_SYSTEM, '/vqmod/xml/', -8);
		$vqmod_name = 'boost_special_manager';
		
		if (is_file($this->vqmod_script_dir . $vqmod_name . '.xml_')) {
			rename($this->vqmod_script_dir . $vqmod_name . '.xml_', $this->vqmod_script_dir . $vqmod_name . '.xml');
		}
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "boost_category_special`");
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'module_boost_special_manager'");
		
		$this->vqmod_script_dir = substr_replace(DIR_SYSTEM, '/vqmod/xml/', -8);
		$vqmod_name = 'boost_special_manager';
		
		if (is_file($this->vqmod_script_dir . $vqmod_name . '.xml')) {
			rename($this->vqmod_script_dir . $vqmod_name . '.xml', $this->vqmod_script_dir . $vqmod_name . '.xml_');
		}
    }
	
	/* products special */
	public function getSpecialProducts() {
		$sql = "SELECT ps.product_special_id, ps.product_id, pd.name, p.status, p.image, p.quantity, ps.customer_group_id, p.manufacturer_id, ps.priority, ps.price AS special_price, ps.date_start, ps.date_end, p.price
		FROM " . DB_PREFIX . "product_special ps 
		LEFT JOIN " . DB_PREFIX . "product p
         ON (ps.product_id = p.product_id)
		LEFT JOIN " . DB_PREFIX . "product_description pd
         ON (ps.product_id = pd.product_id) 
		WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
				
		$query = $this->db->query($sql);
	
		return $query->rows;
	}
	
	public function addSpecialProducts($data) {
		foreach (explode(',',$data['ProductList']) as $product_id) {
			if ($data['discount_type'] == '0') {
				$new_price 	= $this->getProductPrice($product_id) - (((float)$data['price']/100) * $this->getProductPrice($product_id));
				$price 		= round($new_price,2);
			} else if ($data['discount_type'] == '1') {
				$price = $this->getProductPrice($product_id) - (float)$data['price'];
			} else {
				$price = (float)$data['price'];
			}
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', priority = '" . (int)$data['priority'] . "', price = '" . (float)$price . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "'");
		}
	}
	
	public function editProduct($data) {
		if ($data['action'] === 'delete') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_special_id = '" . (int) $data['product_special_id'] . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "product_special SET customer_group_id = '" . (int)$data['customer_group_id'] . "', priority = '" . (int)$data['priority'] . "', price = '" . (float)$data['special'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "' WHERE product_special_id = '" . (int) $data['product_special_id'] . "'");
		}
	}

	public function deleteProduct($product_id) {
		$ids = implode( ", ", $_POST['value']);
	
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_special_id IN (" . implode( ", ", $_POST['value']). ")");
	}
	
	/* categories special */
	public function getSpecialCategories($data = array()) {
		//$sql = "SELECT * FROM " . DB_PREFIX . "product_special s LEFT JOIN " . DB_PREFIX . "product_description pd ON (s.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (s.product_id = p.product_id)";
		$sql = "SELECT bcs.*, cd.language_id, cd.name FROM " . DB_PREFIX . "boost_category_special bcs INNER JOIN " . DB_PREFIX . "category_description cd ON (bcs.category_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$query = $this->db->query($sql);
	
		return $query->rows;
	}
	
	public function addSpecialCategories($data) {
		foreach (explode(',',$data['ProductList']) as $category_id) {
			$price = (float)$data['price'];
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "boost_category_special SET category_id = '" . (int)$category_id . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', priority = '" . (int)$data['priority'] . "', discount = '" . (float)$price . "', discount_type = '" . (int)$data['discount_type'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "'");
		}
	}
	
	public function editCategory($data) {
		if($data['action'] === 'delete'){
			$this->db->query("DELETE FROM " . DB_PREFIX . "boost_category_special WHERE cat_special_id = '" . (int) $data['cat_special_id'] . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "boost_category_special SET customer_group_id = '" . (int)$data['customer_group_id'] . "', priority = '" . (int)$data['priority'] . "', discount = '" . (float)$data['discount'] . "', discount_type = '" . (int)$data['discount_type'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "' WHERE cat_special_id = '" . (int) $data['cat_special_id'] . "'");
		}
	}
	
	public function deleteCategory($category_id) {
		$ids = implode( ", ", $_POST['value']);

		$this->db->query("DELETE FROM " . DB_PREFIX . "boost_category_special WHERE cat_special_id IN (" . implode( ", ", $_POST['value']). ")");
	}
	
	/* general */
	public function getProductPrice($product_id) {
		$query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "' ORDER BY product_id");
				
		return $query->row['price'];
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
		
		return $query->rows;
	}

	public function getCustomerGroups() {
		$query = $this->db->query("SELECT customer_group_id, name FROM " . DB_PREFIX . "customer_group_description  WHERE " . DB_PREFIX . "customer_group_description.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->rows;
	}
	
	public function getCustomerGroup($customer_group_id) {
		$query = $this->db->query("SELECT customer_group_id, name FROM " . DB_PREFIX . "customer_group_description cgd WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cgd.customer_group_id = '" . (int)$customer_group_id . "'");
		
		return $query->row;
	}
	
	public function getProductCategories($product_id) {
		$product_category_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		return $product_category_data;
	}
	
	public function getProductsByCategoryId() {
		$query = $this->db->query("SELECT p2c.product_id, pd.name, p.model FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$_GET['catid'] . "' ORDER BY pd.name ASC");
		
		if ($query->rows) {							
			foreach ($query->rows as $rec) {
				$arr[] = $rec;
			}
			
			$jsonresult = json_encode($arr);
			
			echo $jsonresult;
		} else {
			echo '';
		}
		//return $product_category_data;
	} 
	
	public function getProductsByManufacturerId() {
		$query = $this->db->query("SELECT p2c.product_id, pd.name, p.model FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.manufacturer_id = '" . (int)$_GET['manid'] . "' GROUP BY p2c.product_id ORDER BY pd.name ASC");
								  
		if ($query->rows) {							
			foreach ($query->rows as $rec) {
				$arr[] = $rec;
			}
			
			$jsonresult = json_encode($arr);
		
			echo $jsonresult;
		} else {
			echo '';
		}
	} 

	public function getCategories() {
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id ORDER BY sort_order ASC"; // AND c1.category_id = 35";
		//$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name, c1.parent_id, c1.sort_order FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c1.category_id = '" . (int)$result['category_id'] . "'";
		$query = $this->db->query($sql);

		return $query->rows;
	}
		
	public function getManufacturers() {
		$sql = "SELECT manufacturer_id, name FROM " . DB_PREFIX . "manufacturer ORDER BY name;";

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function getManufacturer($manufacturer_id) {
		$sql = "SELECT name FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'";
		
		$query = $this->db->query($sql);
		
		return $query->row;
	}
}
?>
