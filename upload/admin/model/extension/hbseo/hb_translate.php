<?php
class ModelExtensionHbseoHbTranslate extends Model {
	public function install(){
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'hb_translate_ocmod'");
		
		$ocmod_filename = 'ocmod_hb_translate_3xxx.txt';
		$ocmod_name 	= 'SEO - BulkTranslate PRO [3xxx]';
		
		$ocmod_version 	= $this->hb_extension_version;
		$ocmod_code 	= 'hb_translate_ocmod';	
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
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'hb_translate_ocmod'");
	}

	public function isExtensionInstalled($code) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `code` = '" . $this->db->escape($code) . "'");

        return $query->num_rows;
    }
	
	public function translate_content($data) {
        $apiKey = $this->config->get('hb_translate_api');
        $url = "https://translation.googleapis.com/language/translate/v2";

        // Build request data with necessary parameters
        $request_data = [
            'q' => $data['source_text'],
			'target' => $data['target_language_code'],
            'format' => 'html',  // This preserves HTML formatting in the translation
            'source' => $data['source_language_code'],
            'key' => $apiKey
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode response
        $result = json_decode($response, true);

        // Check for errors
        if (isset($result['error'])) {
            $this->addlog("Error: " . $result['error']['message']);
            return null;
        }

        // Return translated text
        return $result['data']['translations'][0]['translatedText'] ?? null;

		return $data['source_text'].' '.$data['target_language_code'];//TESTING OUTPUT
	}
	
	public function fix_empty_fields(){
        $this->db->query("UPDATE `".DB_PREFIX."product_description` SET description = '' WHERE description = '&lt;p&gt;&lt;br&gt;&lt;/p&gt;'");
        $this->db->query("UPDATE `".DB_PREFIX."category_description` SET description = '' WHERE description = '&lt;p&gt;&lt;br&gt;&lt;/p&gt;'");
        $this->db->query("UPDATE `".DB_PREFIX."information_description` SET description = '' WHERE description = '&lt;p&gt;&lt;br&gt;&lt;/p&gt;'");
    }

	public function getProductColumns() {
		// Base columns
		$columns = array(
			'name'       		=> 'Name',
			'description'       => 'Description',
			'meta_title'        => 'Meta Title',
			'meta_description'  => 'Meta Description',
			'meta_keyword'      => 'Meta Keyword',
			'tag'               => 'Product Tags'
		);
	
		// Additional columns to check
		$additionalColumns = array(
			'h1'                => 'H1 Tag',
			'h2'                => 'H2 Tag',
			'image_alt'         => 'Image Alt Tag',
			'image_title'       => 'Image Title Tag'
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

		//add attribute translate too
		$attribute_columns = array(
			'attribute'	=> 'Product Attributes'
		);

		$columns = array_merge($columns, $attribute_columns);
	
		return $columns;
	}
	

	public function getCategoryColumns() {
		// Base columns
		$columns = array(
			'name'       		=> 'Name',
			'description'       => 'Description',
			'meta_title'        => 'Meta Title',
			'meta_description'  => 'Meta Description',
			'meta_keyword'      => 'Meta Keyword'
		);
	
		// Additional columns to check
		$additionalColumns = array(
			'h1'                => 'H1 Tag',
			'h2'                => 'H2 Tag',
			'image_alt'         => 'Image Alt Tag',
			'image_title'       => 'Image Title Tag'
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
	
		return $columns;
	}
	

	public function getInformationColumns(){
		$columns = array(
			'title'       		=> 'Title',
			'description'		=> 'Description',
			'meta_title' 		=> 'Meta Title',
			'meta_description'	=> 'Meta Description',
			'meta_keyword'		=> 'Meta Keyword',
		);

		return $columns;
	}

	public function getManufacturerColumns() {
        $columns = array(
            'name'       		=> 'Name',
			'description'       => 'Description',
			'meta_title'        => 'Meta Title',
			'meta_description'  => 'Meta Description',
			'meta_keyword'      => 'Meta Keyword',
            'h1'                => 'H1 Tag',
			'h2'                => 'H2 Tag',
			'image_alt'         => 'Image Alt Tag',
			'image_title'       => 'Image Title Tag'
        );

        return $columns;
    }

	public function getTotalItems($type, $master_language_id) {
		switch ($type) {
			case 'product':
				$sql = "SELECT count(*) as total FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$master_language_id."'";
				break;

			case 'category':
				$sql = "SELECT count(*) as total FROM ".DB_PREFIX."category c LEFT JOIN ".DB_PREFIX."category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '".(int)$master_language_id."'";
				break;

			case 'information':
				$sql = "SELECT count(*) as total FROM ".DB_PREFIX."information i LEFT JOIN ".DB_PREFIX."information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$master_language_id."'";
				break;

			case 'manufacturer':
				$sql = "SELECT count(*) as total FROM ".DB_PREFIX."manufacturer m LEFT JOIN ".DB_PREFIX."manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '".(int)$master_language_id."'";
				break;
		}

		$results = $this->db->query($sql);
		return (int)$results->row['total'];
	}


	public function getCount($type, $column, $language_id) {
		switch ($type) {
			case 'product':
				$sql = "SELECT count(*) as total FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$language_id."' AND (trim(pd.`".$column."`) <> '')";
				break;

			case 'category':
				$sql = "SELECT count(*) as total FROM ".DB_PREFIX."category c LEFT JOIN ".DB_PREFIX."category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '".(int)$language_id."' AND (trim(cd.`".$column."`) <> '')";
				break;

			case 'information':
				$sql = "SELECT count(*) as total FROM ".DB_PREFIX."information i LEFT JOIN ".DB_PREFIX."information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$language_id."' AND (trim(id.`".$column."`) <> '')";
				break;

			case 'manufacturer':
				$sql = "SELECT count(*) as total FROM ".DB_PREFIX."manufacturer m LEFT JOIN ".DB_PREFIX."manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '".(int)$language_id."' AND (trim(md.`".$column."`) <> '')";
				break;
		}

		$results = $this->db->query($sql);

		return (int)$results->row['total'];
	}

	public function getTotalAttributeCount($master_language_id) {
		$results = $this->db->query("SELECT count(*) as total FROM ".DB_PREFIX."product_attribute WHERE language_id = '".$master_language_id."'");
		return (int)$results->row['total'];
	}

	public function getAttributeCount($language_id) {
		$results = $this->db->query("SELECT count(*) as total FROM ".DB_PREFIX."product_attribute WHERE language_id = '".$language_id."' AND trim(`text`) <> ''");
		return (int)$results->row['total'];
	}

	public function referenceLanguageID(){
		$query = $this->db->query("SELECT language_id FROM ".DB_PREFIX."language WHERE language_id != '".(int)$this->master_language_id."' ORDER BY language_id DESC LIMIT 1");
		if ($query->row){
			return $query->row['language_id'];
		}else{
			return '0';
		}
	}

	public function getProducts($data) {
		$sql = "SELECT p.product_id as item_id, pd.name, p.model, pd.meta_title, (SELECT meta_title FROM `".DB_PREFIX."product_description` WHERE product_id = p.product_id AND language_id = '".$data['reference_language_id']."' LIMIT 1) as reference_meta_title FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."'";
		
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

	public function getCategories($data) {
		$sql = "SELECT c.category_id as item_id, cd.name, cd.meta_title, (SELECT meta_title FROM `".DB_PREFIX."category_description` WHERE category_id = c.category_id AND language_id = '".$data['reference_language_id']."' LIMIT 1) as reference_meta_title FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '".(int)$this->config->get('config_language_id')."'";	
		
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

	public function getInformations($data) {
		$sql = "SELECT i.information_id as item_id, id.title as `name`, id.meta_title, (SELECT meta_title FROM `".DB_PREFIX."information_description` WHERE information_id = i.information_id AND language_id = '".$data['reference_language_id']."' LIMIT 1) as reference_meta_title FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$this->config->get('config_language_id')."'";	
		
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

	public function getManufacturers($data) {
        $sql = "SELECT m.manufacturer_id as item_id, md.name, md.meta_title, (SELECT meta_title FROM `".DB_PREFIX."manufacturer_description` WHERE manufacturer_id = m.manufacturer_id AND language_id = '".$data['reference_language_id']."' LIMIT 1) as reference_meta_title FROM `".DB_PREFIX."manufacturer` m LEFT JOIN `".DB_PREFIX."manufacturer_description` md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '".(int)$this->config->get('config_language_id')."'";	
        
        if (!empty($data['search'])) {
            $sql .= " AND (m.manufacturer_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(md.name) LIKE '%".$this->db->escape($data['search'])."%')";
        }
        
        $sql .=  " ORDER BY md.name";

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

	public function getTotalProduct($data){
		$sql = "SELECT count(p.product_id) as total FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."'";
		if (!empty($data['search'])) {
			$sql .= " AND (p.product_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.model) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.sku) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalCategory($data){
		$sql = "SELECT count(c.category_id) as total FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '".(int)$this->config->get('config_language_id')."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (c.category_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.name) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalInformation($data){
		$sql = "SELECT count(i.information_id) as total FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$this->config->get('config_language_id')."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (i.information_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.title) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

	public function getTotalManufacturer($data){
        $sql = "SELECT count(m.manufacturer_id) as total FROM `".DB_PREFIX."manufacturer` m LEFT JOIN `".DB_PREFIX."manufacturer_description` md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '".(int)$this->config->get('config_language_id')."'";
        
        if (!empty($data['search'])) {
            $sql .= " AND (m.manufacturer_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(md.name) LIKE '%".$this->db->escape($data['search'])."%')";
        }

        $results = $this->db->query($sql);
        return $results->row['total'];
    }

	public function clearProduct($product_id){
		$master_language_id = $this->master_language_id;

		$columns = $this->getProductColumns();

		$enabled_fields = $this->config->get('hb_translate_fields_product');

		foreach ($columns as $key => $value){
			if (is_array($enabled_fields) && in_array($key, $enabled_fields)){
				if ($key == 'attribute') {
					$this->db->query("UPDATE `".DB_PREFIX."product_attribute` SET `text` = '' WHERE product_id = '".(int)$product_id."' AND language_id != '".(int)$master_language_id."'");
				}else{
					$this->db->query("UPDATE `".DB_PREFIX."product_description` SET `".$key."` = '' WHERE product_id = '".(int)$product_id."' AND language_id != '".(int)$master_language_id."'");
				}
				$this->addlog("Cleared ".$key." data for Product ID ". $product_id. ". Ignored Master Language ID ".$master_language_id);
			}			
		}		
	}

	public function clearCategory($category_id){
		$master_language_id = $this->master_language_id;

		$columns = $this->getCategoryColumns();

		$enabled_fields = $this->config->get('hb_translate_fields_category');

		foreach ($columns as $key => $value){
			if (is_array($enabled_fields) && in_array($key, $enabled_fields)){
				$this->db->query("UPDATE `".DB_PREFIX."category_description` SET `".$key."` = '' WHERE category_id = '".(int)$category_id."' AND language_id != '".(int)$master_language_id."'");
				$this->addlog("Cleared ".$key." data for Category ID ". $category_id. ". Ignored Master Language ID ".$master_language_id);
			}			
		}		
	}

	public function clearInformation($information_id){
		$master_language_id = $this->master_language_id;

		$columns = $this->getInformationColumns();

		$enabled_fields = $this->config->get('hb_translate_fields_information');

		foreach ($columns as $key => $value){
			if (is_array($enabled_fields) && in_array($key, $enabled_fields)){
				$this->db->query("UPDATE `".DB_PREFIX."information_description` SET `".$key."` = '' WHERE information_id = '".(int)$information_id."' AND language_id != '".(int)$master_language_id."'");
				$this->addlog("Cleared ".$key." data for information ID ". $information_id. ". Ignored Master Language ID ".$master_language_id);
			}			
		}		
	}

	public function clearManufacturer($manufacturer_id){
        $master_language_id = $this->master_language_id;

        $columns = $this->getManufacturerColumns();

        $enabled_fields = $this->config->get('hb_translate_fields_manufacturer');

        foreach ($columns as $key => $value){
            if (is_array($enabled_fields) && in_array($key, $enabled_fields)){
                $this->db->query("UPDATE `".DB_PREFIX."manufacturer_description` SET `".$key."` = '' WHERE manufacturer_id = '".(int)$manufacturer_id."' AND language_id != '".(int)$master_language_id."'");
                $this->addlog("Cleared ".$key." data for Manufacturer ID ". $manufacturer_id. ". Ignored Master Language ID ".$master_language_id);
            }			
        }		
    }

	public function bulk_clear_product($column){
		$master_language_id = $this->master_language_id;

		if ($column == 'attribute') {
			$this->db->query("UPDATE `".DB_PREFIX."product_attribute` SET `text` = '' WHERE language_id != '".(int)$master_language_id."'");
		}else{
			$this->db->query("UPDATE `".DB_PREFIX."product_description` SET `".$column."` = '' WHERE language_id != '".(int)$master_language_id."'");
		}

		$this->addlog("Cleared all ".$column." data for Products. Ignored Master Language ID ".$master_language_id);			
	}

	public function bulk_clear_category($column){
		$master_language_id = $this->master_language_id;

		$this->db->query("UPDATE `".DB_PREFIX."category_description` SET `".$column."` = '' WHERE language_id != '".(int)$master_language_id."'");

		$this->addlog("Cleared all ".$column." data for Categories. Ignored Master Language ID ".$master_language_id);			
	}

	public function bulk_clear_information($column){
		$master_language_id = $this->master_language_id;

		$this->db->query("UPDATE `".DB_PREFIX."information_description` SET `".$column."` = '' WHERE language_id != '".(int)$master_language_id."'");

		$this->addlog("Cleared all ".$column." data for Informations. Ignored Master Language ID ".$master_language_id);			
	}

	public function bulk_clear_manufacturer($column){
        $master_language_id = $this->master_language_id;

        $this->db->query("UPDATE `".DB_PREFIX."manufacturer_description` SET `".$column."` = '' WHERE language_id != '".(int)$master_language_id."'");

        $this->addlog("Cleared all ".$column." data for Manufacturers. Ignored Master Language ID ".$master_language_id);			
    }

	public function addlog($text = ''){
        if (!file_exists(DIR_LOGS)) {
            mkdir(DIR_LOGS, 0777, true);
        }

        $file = DIR_LOGS . 'huntbee_translatePro_logs.txt';

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

}
?>