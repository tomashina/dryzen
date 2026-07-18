<?php
class ModelExtensionHbseoHbQuick extends Model {
	public function install(){
		
	}
	
	public function uninstall() {
		
	}

	public function getProductColumns() {
		// Base columns
		$columns = array(
			'name'       		=> $this->language->get('col_name'),
			'description'       => $this->language->get('col_description'),
			'meta_title'        => $this->language->get('col_meta_title'),
			'meta_description'  => $this->language->get('col_meta_description'),
			'meta_keyword'      => $this->language->get('col_meta_keyword'),
			'tag'               => $this->language->get('col_tags')
		);
	
		// Additional columns to check
		$additionalColumns = array(
			'h1'                => $this->language->get('col_h1'),
			'h2'                => $this->language->get('col_h2'),
			'image_alt'         => $this->language->get('col_image_alt'),
			'image_title'       => $this->language->get('col_image_title')
		);
	
		// Query to fetch column names from the `product` table
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_description`");
	
		if ($query->num_rows > 0) {
			$existingColumns = array();
	
			// Extract column names
			foreach ($query->rows as $row) {
				$existingColumns[] = $row['Field'];
			}
	
			// Filter additionalColumns to include only those that exist in the `product` table
			$validColumns = array_filter($additionalColumns, function($key) use ($existingColumns) {
				return in_array($key, $existingColumns);
			}, ARRAY_FILTER_USE_KEY);
	
			// Merge valid additional columns into the main columns array
			$columns = array_merge($columns, $validColumns);
		}

		//add seo keyword too
		$attribute_columns = array(
			'keyword'	=> $this->language->get('col_seo_keyword')
		);

		$columns = array_merge($columns, $attribute_columns);
	
		return $columns;
	}

	public function getCategoryColumns() {
		// Base columns
		$columns = array(
			'name'       		=> $this->language->get('col_name'),
			'description'       => $this->language->get('col_description'),
			'meta_title'        => $this->language->get('col_meta_title'),
			'meta_description'  => $this->language->get('col_meta_description'),
			'meta_keyword'      => $this->language->get('col_meta_keyword')
		);
	
		// Additional columns to check
		$additionalColumns = array(
			'h1'                => $this->language->get('col_h1'),
			'h2'                => $this->language->get('col_h2'),
			'image_alt'         => $this->language->get('col_image_alt'),
			'image_title'       => $this->language->get('col_image_title')
		);
	
		// Query to fetch column names from the `category` table
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category_description`");
	
		if ($query->num_rows > 0) {
			$existingColumns = array();
	
			// Extract column names
			foreach ($query->rows as $row) {
				$existingColumns[] = $row['Field'];
			}
	
			// Filter additionalColumns to include only those that exist in the `category` table
			$validColumns = array_filter($additionalColumns, function($key) use ($existingColumns) {
				return in_array($key, $existingColumns);
			}, ARRAY_FILTER_USE_KEY);
	
			// Merge valid additional columns into the main columns array
			$columns = array_merge($columns, $validColumns);
		}
	
		//add seo keyword too
		$attribute_columns = array(
			'keyword'	=> $this->language->get('col_seo_keyword')
		);

		$columns = array_merge($columns, $attribute_columns);

		return $columns;
	}

	public function getInformationColumns(){
		$columns = array(
			'title'       		=> $this->language->get('col_title'),
			'description'       => $this->language->get('col_description'),
			'meta_title'        => $this->language->get('col_meta_title'),
			'meta_description'  => $this->language->get('col_meta_description'),
			'meta_keyword'      => $this->language->get('col_meta_keyword')
		);

		//add seo keyword too
		$attribute_columns = array(
			'keyword'	=> $this->language->get('col_seo_keyword')
		);

		$columns = array_merge($columns, $attribute_columns);

		return $columns;
	}

	public function getManufacturerColumns(){
		$columns = array(
			'name'       		=> $this->language->get('col_name'),			
		);

		if ($this->isManufacturerDescTable()) {
            $additionalColumns = array(
            'description'       => $this->language->get('col_description'),
            'meta_title'        => $this->language->get('col_meta_title'),
            'meta_description'  => $this->language->get('col_meta_description'),
            'meta_keyword'      => $this->language->get('col_meta_keyword'),
            'h1'                => $this->language->get('col_h1'),
            'h2'                => $this->language->get('col_h2'),
            'image_alt'         => $this->language->get('col_image_alt'),
            'image_title'       => $this->language->get('col_image_title')
            );

            $columns = array_merge($columns, $additionalColumns);
        }
		
		//add seo keyword too
		$attribute_columns = array(
			'keyword'	=> $this->language->get('col_seo_keyword')
		);

		$columns = array_merge($columns, $attribute_columns);

		return $columns;
	}

	public function isManufacturerDescTable() {
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "manufacturer_description'");
        return $query->num_rows > 0;
    }

	public function getProducts($data) {
		$sql = "SELECT pd.*, p.product_id AS item_id, p.model, su.keyword AS seo_keyword FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) LEFT JOIN `".DB_PREFIX."seo_url` su ON (su.query = CONCAT('product_id=', p.product_id) AND su.language_id = '".(int)$data['language_id']."') WHERE pd.language_id = '".(int)$data['language_id']."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (p.product_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.model) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.sku) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.tag) LIKE '%".$this->db->escape($data['search'])."%')";
		}
		
		if ($data['sort_field'] == 'date_modified') {
			$sql .=  " ORDER BY p.date_modified ".$data['sort_order'];
		}elseif($data['sort_field'] == 'item_id') {
			$sql .=  " ORDER BY item_id ".$data['sort_order'];
		}else{
			$sql .=  " ORDER BY pd.".$data['sort_field']." ".$data['sort_order'];
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

	public function getCategories($data) {
		$sql = "SELECT cd.*, c.category_id AS item_id, su.keyword AS seo_keyword FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN `".DB_PREFIX."seo_url` su ON (su.query = CONCAT('category_id=', c.category_id) AND su.language_id = '".(int)$data['language_id']."') WHERE cd.language_id = '".(int)$data['language_id']."'";	
		
		if (!empty($data['search'])) {
			$sql .= " AND (c.category_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.name) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		if ($data['sort_field'] == 'date_modified') {
			$sql .=  " ORDER BY c.date_modified ".$data['sort_order'];
		}elseif($data['sort_field'] == 'item_id') {
			$sql .=  " ORDER BY item_id ".$data['sort_order'];
		}else{
			$sql .=  " ORDER BY cd.".$data['sort_field']." ".$data['sort_order'];
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

	public function getInformations($data) {
		$sql = "SELECT id.*, i.information_id AS item_id, id.title AS `name`, su.keyword AS seo_keyword FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) LEFT JOIN `".DB_PREFIX."seo_url` su ON (su.query = CONCAT('information_id=', i.information_id) AND su.language_id = '".(int)$data['language_id']."') WHERE id.language_id = '".(int)$data['language_id']."'";	
		
		if (!empty($data['search'])) {
			$sql .= " AND (i.information_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.title) LIKE '%".$this->db->escape($data['search'])."%')";
		}
		
		if($data['sort_field'] == 'item_id') {
			$sql .=  " ORDER BY item_id ".$data['sort_order'];
		}else{
			$sql .=  " ORDER BY id.title ".$data['sort_order'];
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

	public function getMLManufacturers($data) {
        $sql = "SELECT md.*, m.manufacturer_id AS item_id, su.keyword AS seo_keyword FROM `".DB_PREFIX."manufacturer` m LEFT JOIN `".DB_PREFIX."manufacturer_description` md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN `".DB_PREFIX."seo_url` su ON (su.query = CONCAT('manufacturer_id=', m.manufacturer_id) AND su.language_id = '".(int)$data['language_id']."') WHERE md.language_id = '".(int)$data['language_id']."'";

        if (!empty($data['search'])) {
			$sql .= " AND (m.manufacturer_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(md.name) LIKE '%".$this->db->escape($data['search'])."%')";
		}

        if($data['sort_field'] == 'item_id') {
            $sql .=  " ORDER BY item_id ".$data['sort_order'];
        }else{
            $sql .=  " ORDER BY md.name ".$data['sort_order'];
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

	public function getManufacturers($data) {
		$sql = "SELECT m.*, su.keyword AS seo_keyword FROM `".DB_PREFIX."manufacturer` m LEFT JOIN `".DB_PREFIX."seo_url` su ON (su.query = CONCAT('manufacturer_id=', m.manufacturer_id) AND su.language_id = '".(int)$data['language_id']."')";
		
		if (!empty($data['search'])) {
			$search = $this->db->escape($data['search']);
			$sql .= empty($data['is_language']) ? " WHERE" : " AND"; 
			$sql .= " (m.manufacturer_id LIKE '%" . $search . "%' OR LOWER(m.name) LIKE '%" . $search . "%')";
		}
		
		if($data['sort_field'] == 'item_id') {
			$sql .=  " ORDER BY m.manufacturer_id ".$data['sort_order'];
		}else{
			$sql .=  " ORDER BY m.name ".$data['sort_order'];
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

	public function getTotalProducts($data){
		$sql = "SELECT count(p.product_id) AS total FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$data['language_id']."'";
		if (!empty($data['search'])) {
			$sql .= " AND (p.product_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.model) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.sku) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.tag) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalCategories($data){
		$sql = "SELECT count(c.category_id) AS total FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '".(int)$data['language_id']."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (c.category_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.name) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalInformations($data){
		$sql = "SELECT count(i.information_id) AS total FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$data['language_id']."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (i.information_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.title) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalManufacturers($data){
		$sql = "SELECT count(manufacturer_id) AS total FROM `".DB_PREFIX."manufacturer`";
		
		if (!empty($data['search'])) {
			$search = $this->db->escape($data['search']);
			$sql .= empty($data['is_language']) ? " WHERE" : " AND"; 
			$sql .= " (manufacturer_id LIKE '%" . $search . "%' OR LOWER(name) LIKE '%" . $search . "%')";
		}
		
		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalMLManufacturers($data){
		$sql = "SELECT count(m.manufacturer_id) AS total FROM `".DB_PREFIX."manufacturer` m LEFT JOIN `".DB_PREFIX."manufacturer_description` md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '".(int)$data['language_id']."'";

        if (!empty($data['search'])) {
            $sql .= " AND (m.manufacturer_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(md.name) LIKE '%".$this->db->escape($data['search'])."%')";
        }
		
		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function update_value($data) {
		// Get all stores (including default store 0) once
		$stores = array_merge([['store_id' => 0]], $this->db->query("SELECT store_id FROM " . DB_PREFIX . "store")->rows);
	
		switch ($data['type']) {
			case 'product':
				if ($data['column'] == 'keyword') {
					foreach ($stores as $store) {
						$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "seo_url` WHERE query = CONCAT('product_id=', " . (int)$data['id'] . ") AND language_id = '" . (int)$data['language_id'] . "' AND store_id = '" . (int)$store['store_id'] . "'");
						
						if ($query->row['total'] == 0) {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` (query, keyword, language_id, store_id) VALUES ('product_id=" . (int)$data['id'] . "', '" . $this->db->escape($data['updated_value']) . "', '" . (int)$data['language_id'] . "', '" . (int)$store['store_id'] . "')");
						} else {
							$this->db->query("UPDATE `" . DB_PREFIX . "seo_url` SET keyword = '" . $this->db->escape($data['updated_value']) . "' WHERE query = CONCAT('product_id=', " . (int)$data['id'] . ") AND language_id = '" . (int)$data['language_id'] . "' AND store_id = '" . (int)$store['store_id'] . "'");
						}
					}
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET `" . $this->db->escape($data['column']) . "` = '" . $this->db->escape($data['updated_value']) . "' WHERE product_id = '" . (int)$data['id'] . "' AND language_id = '" . (int)$data['language_id'] . "'");
				}
				break;
	
			case 'category':
				if ($data['column'] == 'keyword') {
					foreach ($stores as $store) {
						$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "seo_url` WHERE query = CONCAT('category_id=', " . (int)$data['id'] . ") AND language_id = '" . (int)$data['language_id'] . "' AND store_id = '" . (int)$store['store_id'] . "'");
						
						if ($query->row['total'] == 0) {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` (query, keyword, language_id, store_id) VALUES ('category_id=" . (int)$data['id'] . "', '" . $this->db->escape($data['updated_value']) . "', '" . (int)$data['language_id'] . "', '" . (int)$store['store_id'] . "')");
						} else {
							$this->db->query("UPDATE `" . DB_PREFIX . "seo_url` SET keyword = '" . $this->db->escape($data['updated_value']) . "' WHERE query = CONCAT('category_id=', " . (int)$data['id'] . ") AND language_id = '" . (int)$data['language_id'] . "' AND store_id = '" . (int)$store['store_id'] . "'");
						}
					}
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "category_description` SET `" . $this->db->escape($data['column']) . "` = '" . $this->db->escape($data['updated_value']) . "' WHERE category_id = '" . (int)$data['id'] . "' AND language_id = '" . (int)$data['language_id'] . "'");
				}
				break;
	
			case 'information':
				if ($data['column'] == 'keyword') {
					foreach ($stores as $store) {
						$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "seo_url` WHERE query = CONCAT('information_id=', " . (int)$data['id'] . ") AND language_id = '" . (int)$data['language_id'] . "' AND store_id = '" . (int)$store['store_id'] . "'");
						
						if ($query->row['total'] == 0) {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` (query, keyword, language_id, store_id) VALUES ('information_id=" . (int)$data['id'] . "', '" . $this->db->escape($data['updated_value']) . "', '" . (int)$data['language_id'] . "', '" . (int)$store['store_id'] . "')");
						} else {
							$this->db->query("UPDATE `" . DB_PREFIX . "seo_url` SET keyword = '" . $this->db->escape($data['updated_value']) . "' WHERE query = CONCAT('information_id=', " . (int)$data['id'] . ") AND language_id = '" . (int)$data['language_id'] . "' AND store_id = '" . (int)$store['store_id'] . "'");
						}
					}
				} else {
					$this->db->query("UPDATE `" . DB_PREFIX . "information_description` SET `" . $this->db->escape($data['column']) . "` = '" . $this->db->escape($data['updated_value']) . "' WHERE information_id = '" . (int)$data['id'] . "' AND language_id = '" . (int)$data['language_id'] . "'");
				}
				break;
	
			case 'manufacturer':
				if ($data['column'] == 'keyword') {
					foreach ($stores as $store) {
						$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "seo_url` WHERE query = CONCAT('manufacturer_id=', " . (int)$data['id'] . ") AND language_id = '" . (int)$data['language_id'] . "' AND store_id = '" . (int)$store['store_id'] . "'");
						
						if ($query->row['total'] == 0) {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "seo_url` (query, keyword, language_id, store_id) VALUES ('manufacturer_id=" . (int)$data['id'] . "', '" . $this->db->escape($data['updated_value']) . "', '" . (int)$data['language_id'] . "', '" . (int)$store['store_id'] . "')");
						} else {
							$this->db->query("UPDATE `" . DB_PREFIX . "seo_url` SET keyword = '" . $this->db->escape($data['updated_value']) . "' WHERE query = CONCAT('manufacturer_id=', " . (int)$data['id'] . ") AND language_id = '" . (int)$data['language_id'] . "' AND store_id = '" . (int)$store['store_id'] . "'");
						}
					}
				} else {
					if ($data['column'] == 'name') {
						$this->db->query("UPDATE `" . DB_PREFIX . "manufacturer` SET `name` = '" . $this->db->escape($data['updated_value']) . "' WHERE manufacturer_id = '" . (int)$data['id'] . "'");
					}

					if ($this->isManufacturerDescTable()) {
						$this->db->query("UPDATE `" . DB_PREFIX . "manufacturer_description` SET `" . $this->db->escape($data['column']) . "` = '" . $this->db->escape($data['updated_value']) . "' WHERE manufacturer_id = '" . (int)$data['id'] . "' AND language_id = '" . (int)$data['language_id'] . "'");
					}
				}
				break;
	
			default:
				throw new Exception('Invalid type specified: ' . $this->db->escape($data['type']));
		}
	}

	public function is_keyword_exists($keyword){
		$query = $this->db->query("SELECT count(*) as total FROM `" . DB_PREFIX . "seo_url` WHERE `keyword` = '" . $this->db->escape($keyword) . "'");
		if ($query->row['total'] > 0) {
			return true;
		}else{
			return false;
		}
	}

	public function getDescription($data){
		switch ($data['type']) {
			case 'product':
				$sql = "SELECT description FROM `".DB_PREFIX."product_description` WHERE product_id = '".(int)$data['item_id']."' AND language_id = '".(int)$data['language_id']."' LIMIT 1";
				break;
			
			case 'category':
				$sql = "SELECT description FROM `".DB_PREFIX."category_description` WHERE category_id = '".(int)$data['item_id']."' AND language_id = '".(int)$data['language_id']."' LIMIT 1";
				break;
			
			case 'information':
				$sql = "SELECT description FROM `".DB_PREFIX."information_description` WHERE information_id = '".(int)$data['item_id']."' AND language_id = '".(int)$data['language_id']."' LIMIT 1";
				break;

			case 'manufacturer':
				$sql = "SELECT description FROM `".DB_PREFIX."manufacturer_description` WHERE manufacturer_id = '".(int)$data['item_id']."' AND language_id = '".(int)$data['language_id']."' LIMIT 1";
				break;
		}

		$query = $this->db->query($sql);
		if (isset($query->row['description'])){
			return $query->row['description'];
		}else{
			return '';
		}
	}

	public function getName($data){
		switch ($data['type']) {
			case 'product':
				$sql = "SELECT name FROM `".DB_PREFIX."product_description` WHERE product_id = '".(int)$data['item_id']."' AND language_id = '".(int)$data['language_id']."' LIMIT 1";
				break;
			
			case 'category':
				$sql = "SELECT name FROM `".DB_PREFIX."category_description` WHERE category_id = '".(int)$data['item_id']."' AND language_id = '".(int)$data['language_id']."' LIMIT 1";
				break;
			
			case 'information':
				$sql = "SELECT title AS name FROM `".DB_PREFIX."information_description` WHERE information_id = '".(int)$data['item_id']."' AND language_id = '".(int)$data['language_id']."' LIMIT 1";
				break;

			case 'manufacturer':
				$sql = "SELECT name FROM `".DB_PREFIX."manufacturer_description` WHERE manufacturer_id = '".(int)$data['item_id']."' AND language_id = '".(int)$data['language_id']."' LIMIT 1";
				break;
		}

		$query = $this->db->query($sql);
		if (isset($query->row['name'])){
			return $query->row['name'];
		}else{
			return '';
		}
	}
	
}
?>