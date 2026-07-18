<?php
class ModelExtensionHbseoHbAigen extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "aigen` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			   `type` VARCHAR(20) NOT NULL,
                `item_id` INT NOT NULL,
                `element` VARCHAR(50) NOT NULL,
                `language_id` INT NOT NULL,
                `value` TEXT NOT NULL,
                `previous_value` TEXT,
                `date_added` DATETIME DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8");
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "aigen`");
	}         
    
    public function isExtensionInstalled($code) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE `code` = '" . $this->db->escape($code) . "'");

        return $query->num_rows;
    }

    public function getProduct($product_id, $langauge_id = 1) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$langauge_id . "' LIMIT 1");

        return $query->row;
    }

    public function getCategory($category_id, $langauge_id = 1) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$langauge_id . "' LIMIT 1");

        return $query->row;
    }

    public function getManufacturer($manufacturer_id, $langauge_id = 1) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND md.language_id = '" . (int)$langauge_id . "' LIMIT 1");

        return $query->row;
    }

    public function getInformation($information_id, $langauge_id = 1) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$langauge_id . "' LIMIT 1");

        return $query->row;
    }

    public function getProducts($data) {
		$sql = "SELECT pd.*, p.product_id AS item_id, p.model FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."'";
		
		if (!empty($data['search'])) {
			$sql .= " AND (p.product_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.model) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.tag) LIKE '%".$this->db->escape($data['search'])."%')";
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

	public function getTotalProducts($data){
		$sql = "SELECT count(p.product_id) AS total FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$this->config->get('config_language_id')."'";
		if (!empty($data['search'])) {
			$sql .= " AND (p.product_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.model) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.tag) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}

    public function getCategories($data) {
        $sql = "SELECT cd.*, c.category_id AS item_id FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '".(int)$this->config->get('config_language_id')."'";
        if (!empty($data['search'])) {
            $sql .= " AND (c.category_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.description) LIKE '%".$this->db->escape($data['search'])."%')";
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

    public function getTotalCategories($data){
        $sql = "SELECT count(c.category_id) AS total FROM `".DB_PREFIX."category` c LEFT JOIN `".DB_PREFIX."category_description` cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '".(int)$this->config->get('config_language_id')."'";
        if (!empty($data['search'])) {
            $sql .= " AND (c.category_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(cd.description) LIKE '%".$this->db->escape($data['search'])."%')";
        }

        $results = $this->db->query($sql);
        return $results->row['total'];
    }

    public function getManufacturers($data) {
        $sql = "SELECT m.*, md.* FROM `" . DB_PREFIX . "manufacturer` m LEFT JOIN `" . DB_PREFIX . "manufacturer_description` md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        if (!empty($data['search'])) {
            $sql .= " AND (m.manufacturer_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(md.name) LIKE '%".$this->db->escape($data['search'])."%')";
        }

        $sql .=  " ORDER BY md.name DESC";

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

    public function getTotalManufacturers($data){
        $sql = "SELECT count(m.manufacturer_id) AS total FROM `" . DB_PREFIX . "manufacturer` m LEFT JOIN `" . DB_PREFIX . "manufacturer_description` md ON (m.manufacturer_id = md.manufacturer_id) WHERE md.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        if (!empty($data['search'])) {
            $sql .= " AND (m.manufacturer_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(md.name) LIKE '%".$this->db->escape($data['search'])."%')";
        }

        $results = $this->db->query($sql);
        return $results->row['total'];
    }

    public function getInformations($data) {
        $sql = "SELECT id.*, i.information_id AS item_id FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$this->config->get('config_language_id')."'";
        if (!empty($data['search'])) {
            $sql .= " AND (i.information_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.title) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.description) LIKE '%".$this->db->escape($data['search'])."%')";
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

    public function getTotalInformations($data){
        $sql = "SELECT count(i.information_id) AS total FROM `".DB_PREFIX."information` i LEFT JOIN `".DB_PREFIX."information_description` id ON (i.information_id = id.information_id) WHERE id.language_id = '".(int)$this->config->get('config_language_id')."'";
        if (!empty($data['search'])) {
            $sql .= " AND (i.information_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.title) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(id.description) LIKE '%".$this->db->escape($data['search'])."%')";
        }

        $results = $this->db->query($sql);
        return $results->row['total'];
    }

    public function getItems($data){
        $sql = "SELECT * FROM `".DB_PREFIX."aigen`";
        if (!empty($data['search'])) {
            $sql .= " WHERE (item_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(element) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(value) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(previous_value) LIKE '%".$this->db->escape($data['search'])."%')";
        }

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

    public function getTotalItems($data){
        $sql = "SELECT count(id) AS total FROM `".DB_PREFIX."aigen`";
        if (!empty($data['search'])) {
            $sql .= " WHERE (item_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(element) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(value) LIKE '%".$this->db->escape($data['search'])."%')";
        }

        $results = $this->db->query($sql);
        return $results->row['total'];
    }

    public function addlog($text = ''){
        if ($this->config->get('hb_aigen_logs')) {
            if (!file_exists(DIR_LOGS)) {
                mkdir(DIR_LOGS, 0777, true);
            }
    
            $file = DIR_LOGS . 'hb_aigen.txt';
    
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

    public function saveItem($data){
        $overwrite = ($this->config->get('hb_aigen_overwrite')) ? true : false;

        switch($data['type']){
            case 'product':
                if ($overwrite || empty($data['previous_value'])) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET " . $data['element'] . " = '" . $this->db->escape($data['value']) . "' WHERE product_id = '" . (int)$data['item_id'] . "' AND language_id = '" . (int)$data['language_id'] . "'");
                }
                break;

            case 'category':
                if ($overwrite || empty($data['previous_value'])) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "category_description` SET " . $data['element'] . " = '" . $this->db->escape($data['value']) . "' WHERE category_id = '" . (int)$data['item_id'] . "' AND language_id = '" . (int)$data['language_id'] . "'");
                }
                break;
            
            case 'manufacturer':
                if ($overwrite || empty($data['previous_value'])) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "manufacturer_description` SET " . $data['element'] . " = '" . $this->db->escape($data['value']) . "' WHERE manufacturer_id = '" . (int)$data['item_id'] . "' AND language_id = '" . (int)$data['language_id'] . "'");
                }
                break;

            case 'information':
                if ($overwrite || empty($data['previous_value'])) {
                    $this->db->query("UPDATE `" . DB_PREFIX . "information_description` SET " . $data['element'] . " = '" . $this->db->escape($data['value']) . "' WHERE information_id = '" . (int)$data['item_id'] . "' AND language_id = '" . (int)$data['language_id'] . "'");
                }
                break;
        }

        $this->addlog('Item '. $data['type'] . ' with ID ' . $data['item_id'] . ' and element ' . $data['element'] . ' saved');
    }

    public function acceptItem($id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aigen` WHERE id = '" . (int)$id . "'");

        if ($query->num_rows) {
            $this->saveItem($query->row);
            return true;
        }else{
            $this->addlog('Item not found');
            return false;
        }
    }

    public function acceptAllItems() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aigen`");

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $this->saveItem($row);
            }
            return true;
        }else{
            $this->addlog('No items found');
            return false;
        }
    }

    public function restoreItem($id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aigen` WHERE id = '" . (int)$id . "'");

        if ($query->num_rows) {            
            $previous_value = $query->row['previous_value'];

            //$this->db->query("DELETE FROM `" . DB_PREFIX . "aigen` WHERE id = '" . (int)$id . "'");
            switch ($query->row['type']) {
                case 'product':
                    $this->db->query("UPDATE `" . DB_PREFIX . "product_description` SET " . $query->row['element'] . " = '" . $this->db->escape($previous_value) . "' WHERE product_id = '" . (int)$query->row['item_id'] . "' AND language_id = '" . (int)$query->row['language_id'] . "'");
                    break;
                
                case 'category':
                    $this->db->query("UPDATE `" . DB_PREFIX . "category_description` SET " . $query->row['element'] . " = '" . $this->db->escape($previous_value) . "' WHERE category_id = '" . (int)$query->row['item_id'] . "' AND language_id = '" . (int)$query->row['language_id'] . "'");
                    break;

                case 'manufacturer':
                    $this->db->query("UPDATE `" . DB_PREFIX . "manufacturer_description` SET " . $query->row['element'] . " = '" . $this->db->escape($previous_value) . "' WHERE manufacturer_id = '" . (int)$query->row['item_id'] . "' AND language_id = '" . (int)$query->row['language_id'] . "'");
                    break;

                case 'information':
                    $this->db->query("UPDATE `" . DB_PREFIX . "information_description` SET " . $query->row['element'] . " = '" . $this->db->escape($previous_value) . "' WHERE information_id = '" . (int)$query->row['item_id'] . "' AND language_id = '" . (int)$query->row['language_id'] . "'");
                    break;

            }

            $this->addlog('Item '. $query->row['type'] . ' with ID ' . $query->row['item_id'] . ' and element ' . $query->row['element'] . ' restored');
            return true;
        }else{
            $this->addlog('Item not found');
            return false;
        }
    }

    public function restoreAllItems() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aigen`");

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $this->restoreItem($row['id']);
            }
            return true;
        }else{
            $this->addlog('No items found');
            return false;
        }
    }

    public function deleteItem($id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "aigen` WHERE id = '" . (int)$id . "'");
        $this->addlog('Item '. $id .' deleted');
    }

    public function deleteAllItems() {
        $this->db->query("TRUNCATE TABLE `" . DB_PREFIX . "aigen`");
        $this->addlog('All items deleted');
    }
	
}
?>