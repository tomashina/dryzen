<?php
class ModelExtensionHbseoHbRelated extends Model {
	public function install(){
		$this->db->query("INSERT INTO `".DB_PREFIX."setting` (`code`, `key`, `value`, `serialized`) VALUES ('hbseo_hb_related','hbseo_hb_related_status', '1','0')");
	}
	
	public function uninstall(){
		$this->db->query("DELETE FROM `".DB_PREFIX."setting` WHERE `key` = 'hbseo_hb_related_status'");
	}

	public function update(){
		return false;
	}

	public function isExtensionInstalled($code){
		$query = $this->db->query("SELECT count(*) as total FROM `".DB_PREFIX."extension` WHERE `code` = '".$this->db->escape($code)."'");	
		if ($query->row['total'] > 0){
			return true;
		}else{
			return false;
		}
	}

	public function get_total_products(){
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProducts($data = array()){
		$sql = "SELECT DISTINCT(p.product_id) AS product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
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

	public function get_total_related_products($product_id) {
		$query =  $this->db->query("SELECT COUNT(DISTINCT related_id) AS total FROM " . DB_PREFIX . "product_related WHERE product_id = '".(int)$product_id."'");
		return $query->row['total'];
	}

	public function get_related_products($product_id) {
		$related_id = array();
		$query =  $this->db->query("SELECT DISTINCT(related_id) AS product_id FROM " . DB_PREFIX . "product_related WHERE product_id = '".(int)$product_id."'");
		if ($query->rows) {
			$related_id = $query->rows;
		}
		return $related_id;
	}

	public function get_store_id($product_id) {
		$query =  $this->db->query("SELECT store_id FROM " . DB_PREFIX . "product_to_store WHERE product_id = '".(int)$product_id."' ORDER BY store_id LIMIT 1");
		return $query->row['store_id'];
	}

	public function generate_related_product($product_id, $related_limit){
		$bycategory 		= array();
		$bymanufacturer 	= array();
		$byrandom 			= array();

		$related_product_ids = array();
		$available = $this->get_total_related_products($product_id);
		
		if ($available < $related_limit){
			//$this->log->write('check point 2');
			$needed_count = $related_limit - $available;

			$store_id = $this->get_store_id($product_id);
			$existing_related_product = $this->get_related_products($product_id);
			$existing_related_product = $this->single_array($existing_related_product);

			if ($this->config->get('hb_related_category')) {
				$bycategory 	= $this->getProductRelatedByCategory($product_id, $store_id, $related_limit);
			}

			if ($this->config->get('hb_related_brand')) {
				$bymanufacturer = $this->getProductRelatedByManufacturer($product_id, $store_id, $related_limit);
			}

			if ($this->config->get('hb_related_random')) {
				$byrandom 		= $this->getRandomProductRelated($product_id, $store_id, $related_limit);
			}			
			
			$related_product_ids = array_merge($bycategory, $bymanufacturer, $byrandom);
			$related_product_ids = $this->single_array($related_product_ids);
			$related_product_ids = array_unique($related_product_ids);

			$related_product_ids = array_diff($related_product_ids,$existing_related_product);
			
			$related_product_ids = array_slice($related_product_ids, 0, $needed_count, true);
			//$this->log->write($related_product_ids);
			foreach ($related_product_ids as $related_id) {
				$this->add_related_product($product_id, $related_id);
			}	
		}
	}

	public function single_array($product_ids){
		$id = array();
		foreach ($product_ids as $product_id) {
			foreach ($product_id as $key => $value){
				array_push($id, $value);
			}			
		}
		return $id;
	}

	public function truncate_related_products(){
		$this->db->query("TRUNCATE " . DB_PREFIX . "product_related");
	}

	public function add_related_product($product_id, $related_id){
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_related (product_id, related_id) VALUES ('".(int)$product_id."', '".(int)$related_id."')");
	}

	public function getProductRelatedByCategory($product_id, $store_id, $limit) {
		$product_data = array(); 

		if ($this->config->get('hb_related_parent')) {
			$parent = true;
		}else{
			$parent = false;		
		}

		if ($parent) {
			$sql = "SELECT DISTINCT(p.product_id) FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p2c.category_id IN (SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "') AND p.product_id != '" . (int)$product_id . "' AND p.status = '1' AND p.quantity > 0 AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$store_id. "' ORDER BY RAND() LIMIT ".(int)$limit;                       
		}else{
			$sql = "SELECT DISTINCT(p.product_id) FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id) WHERE p2c.category_id IN (SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "') AND p.product_id != '" . (int)$product_id . "' AND p.status = '1' AND p.quantity > 0 AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$store_id. "' AND c.parent_id != 0 ORDER BY RAND() LIMIT ".(int)$limit;                       
		}
		//$this->log->write($sql);
		$query = $this->db->query($sql);
		if (isset($query->rows)) {
				return $query->rows;
		}else{
			return false;
		}
	}
	
	public function getProductRelatedByManufacturer($product_id, $store_id, $limit) {
	  $product_data = array();
	  $query = $this->db->query("SELECT DISTINCT(p.product_id) FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.manufacturer_id = (SELECT manufacturer_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "' LIMIT 1) AND p.product_id != '" . (int)$product_id . "' AND p.status = '1' AND p.quantity > 0 AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$store_id. "' LIMIT ".(int)$limit);
	  if (isset($query->rows)) {
			return $query->rows;
	  }else{
		  return false;
	  }					
	}  
		
	public function getRandomProductRelated($product_id, $store_id, $limit) {
		$product_data = array();
		$query = $this->db->query("SELECT DISTINCT(p.product_id) FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id != '" . (int)$product_id . "' AND p.status = '1' AND p.quantity > 0 AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$store_id. "'  ORDER BY RAND() LIMIT ".(int)$limit);					   
		if (isset($query->rows)) {
			return $query->rows;
		}else{
			return false;
		}
	} 
	
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR '&nbsp;&#47;&nbsp;') FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id) AS path FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}


}
?>