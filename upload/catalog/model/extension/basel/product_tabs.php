<?php
class ModelExtensionBaselProductTabs extends Model {
	
	public function getExtraTabsProduct($product_id){
		$product_tab = array();
		$categories = $this->getCategories($product_id);
		$products = $this->getProducts($product_id);
		
		// Grab all tab_ids
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tabs WHERE status = '1' ORDER BY sort_order, tab_id ASC");
		
			if($query->rows){
			
			foreach ($query->rows as $tab) {

				// Dont show tab by default
				$show_tab = false;
				
				// Show tab if set to show on all products
				if($tab['global']) {
					$show_tab = true;
				}
				
				// Show tab if linked via category
				if($categories){
					foreach ($categories as $category) {
					$categoriestwo = $this->getCategoriesTwo($category['category_id'], $tab['tab_id']);
						if ($categoriestwo){
							$show_tab = true;
						}
					}
				}
				
				// Show tab if linked via product
				if($products){
					foreach ($products as $product) {
						if ($product['tab_id'] == $tab['tab_id']) {
							$show_tab = true;
						}
					}
				}
				
			if($show_tab){
				
				$result = $this->getSingleTab($tab['tab_id']);
				
				$product_tab[] = array(
					'tab_id' 			=> $result['tab_id'],
					'name'				=> $result['name'],
					'description'		=> html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')
				);
			}
		}
		}
		
		$product_tab = array_map("unserialize", array_unique(array_map("serialize", $product_tab)));
		return $product_tab;
	}
	
	
	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		return $query->rows;
	}
	
	public function getCategoriesTwo($category_id, $tab_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tabs_to_category WHERE category_id = '" . (int)$category_id . "' AND tab_id = '" . (int)$tab_id . "'");
		return $query->rows;
	}
	
	public function getProducts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tabs_to_product WHERE product_id = '" . (int)$product_id . "'");
		return $query->rows;
	}
	
	public function getSingleTab($tab_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tabs p LEFT JOIN " . DB_PREFIX . "product_tabs_description pt ON (p.tab_id = pt.tab_id) WHERE pt.tab_id = '" . (int)$tab_id . "' AND pt.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->row;
	}
	
}