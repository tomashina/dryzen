<?php
class ModelExtensionHbseoHbSeoreport extends Model {
	public function install(){
		return true;
	}
	
	public function uninstall() {
        return true;
	}

    public function tableExists($table) {
        $table = $this->db->escape($table);
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $table . "'");
        return $query->num_rows > 0;
    }

    public function calculateSeoScore($issue_count, $total_items) {
        return round((1 - ($issue_count / $total_items)) * 100, 2);
    }

    public function getTotalEmptyRedirectItems($page_type) {
        $main_table = DB_PREFIX . $this->db->escape($page_type);
        $redirect_table = DB_PREFIX . 'redirect_disabled_' . $this->db->escape($page_type);
        
        $query = "SELECT COUNT(*) AS total FROM `{$main_table}` p LEFT JOIN `{$redirect_table}` rdp ON p.{$page_type}_id = rdp.{$page_type}_id WHERE p.status = 0 AND rdp.redirect IS NULL";
 
        $result = $this->db->query($query);
        
        return (int)$result->row['total'];
    }

    public function getTotalInvalidImageNames($table) {
        $query = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . $table ."` WHERE `image` REGEXP '[^a-zA-Z0-9._/\\-]'";
        $result = $this->db->query($query);
        return (int)$result->row['total'];
    }

    public function getInvalidImageNames($data) {
        $page_type = $this->db->escape($data['page_type']);
        $start = isset($data['start']) && $data['start'] > 0 ? (int)$data['start'] : 0;
        $limit = isset($data['limit']) && $data['limit'] > 0 ? (int)$data['limit'] : 20;
    
        // Check if page_type is manufacturer to handle it differently
        if ($page_type === 'manufacturer') {
            // Check if the manufacturer_description table exists
			if ($this->tableExists('manufacturer_description')) {
				$query = "SELECT a.`manufacturer_id` AS id, b.`name`, a.`image` as value, CHAR_LENGTH(a.image) as characters
					  FROM `" . DB_PREFIX . "manufacturer` a 
					  LEFT JOIN `" . DB_PREFIX . "manufacturer_description` b 
					  ON (a.`manufacturer_id` = b.`manufacturer_id`) 
					  WHERE a.`image` REGEXP '[^a-zA-Z0-9._/\\-]' 
					  AND b.language_id = '" . $this->config->get('config_language_id') . "' 
					  LIMIT " . $start . ", " . $limit;
			} else {
				// If manufacturer_description table doesn't exist, use the default query
				$query = "SELECT a.`manufacturer_id` AS id, a.`name`, a.`image` as value, CHAR_LENGTH(a.image) as characters 
						FROM `" . DB_PREFIX . "manufacturer` a 
						WHERE a.`image` REGEXP '[^a-zA-Z0-9._/\\-]' 
						LIMIT " . $start . ", " . $limit;
			}
        } else {
            $query = "SELECT a.`{$page_type}_id` AS id, b.`name`, a.`image` as value, CHAR_LENGTH(a.image) as characters 
                      FROM `" . DB_PREFIX . $page_type . "` a 
                      LEFT JOIN `" . DB_PREFIX . $page_type . "_description` b 
                      ON (a.`{$page_type}_id` = b.`{$page_type}_id`) 
                      WHERE a.`image` REGEXP '[^a-zA-Z0-9._/\\-]' 
                      AND b.language_id = '" . $this->config->get('config_language_id') . "' 
                      LIMIT " . $start . ", " . $limit;
        }
    
        $result = $this->db->query($query);
        return $result->rows;
    }       
    
    public function getTotalItems($page_type) {
        $tableMapping = [
            'product'      => ['main_table' => 'product_description'],
            'category'     => ['main_table' => 'category_description'],
            'manufacturer' => ['main_table' => 'manufacturer'],
            'information'  => ['main_table' => 'information_description']
        ];
        
        if ($this->tableExists('manufacturer_description')) {
			$tableMapping['manufacturer'] = ['main_table' => 'manufacturer_description'];
		}
        
        if (isset($tableMapping[$page_type])) {
            $main_table = DB_PREFIX . $this->db->escape($tableMapping[$page_type]['main_table']);
            $sql = "SELECT COUNT(*) AS total FROM `{$main_table}`";
            $results = $this->db->query($sql);
            return (int)$results->row['total'];
        }
    
        return 0;
    }

    public function getItemCountByCharacterLimit($total_items, $table, $column, $minLimit, $maxLimit = false) {
        $table = $this->db->escape($table);
        $column = $this->db->escape($column);
        $minLimit = (int)$minLimit;
        $maxLimit = $maxLimit !== false ? $maxLimit : false;
    
        $columnCheckQuery = "SHOW COLUMNS FROM `" . DB_PREFIX . $table . "` LIKE '" . $column . "'";
        $columnCheckResult = $this->db->query($columnCheckQuery);
    
        if ($columnCheckResult->num_rows === 0) {
            return $total_items; // Column does not exist
        }
    
        $query = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . $table . "` WHERE CHAR_LENGTH(`" . $column . "`) < " . $minLimit;
    
        if ($maxLimit !== false) {
            $query .= " OR CHAR_LENGTH(`" . $column . "`) > " . (int)$maxLimit;
        }
    
        $result = $this->db->query($query);
    
        return $result->row['total'];
    }    

    public function getTotalDisabledItems($table) {
        $table = $this->db->escape($table);
        $query = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . $table . "` WHERE status = 0";
        
        $result = $this->db->query($query);
        
        return (int)$result->row['total'];
    }
    
    public function getItemsByCharacterLimit($data) {
        $table = $this->db->escape($data['table']);
        $column = $this->db->escape($data['column']);
        $minLimit = (int)$data['minLimit'];
        $maxLimit = isset($data['maxLimit']) ? $data['maxLimit'] : false;
    
        // Map table to respective ID and name columns
        $columnMapping = [
            'product_description'      => ['id_column' => 'product_id', 'name_column' => 'name'],
            'category_description'     => ['id_column' => 'category_id', 'name_column' => 'name'],
            'manufacturer'             => ['id_column' => 'manufacturer_id', 'name_column' => 'name'],
            'manufacturer_description' => ['id_column' => 'manufacturer_id', 'name_column' => 'name'],
            'information_description'  => ['id_column' => 'information_id', 'name_column' => 'title']
        ];
    
        $idColumn = $columnMapping[$table]['id_column'];
        $nameColumn = $columnMapping[$table]['name_column'];
    
        // Check if the column exists
        $columnCheckQuery = "SHOW COLUMNS FROM `" . DB_PREFIX . $table . "` LIKE '" . $column . "'";
        $columnCheckResult = $this->db->query($columnCheckQuery);
    
        $columnExists = $columnCheckResult->num_rows > 0;
    
        if ($columnExists) {
            // Build SQL with WHERE clause for column checks
            $sql = "SELECT `$idColumn` AS id, `$nameColumn` AS name, `$column` AS value, CHAR_LENGTH(`$column`) as characters FROM `" . DB_PREFIX . $table . "` WHERE CHAR_LENGTH(`$column`) < " . $minLimit;
    
            if ($maxLimit !== false) {
                $sql .= " OR CHAR_LENGTH(`$column`) > " . (int)$maxLimit;
            }
        } else {
            // Column doesn't exist, remove WHERE clause and set characters to 0
            $sql = "SELECT `$idColumn` AS id, `$nameColumn` AS name, '' AS value, 0 AS characters FROM `" . DB_PREFIX . $table . "`";
        }
    
        // Apply LIMIT clause if required
        if (isset($data['start']) || isset($data['limit'])) {
            $start = isset($data['start']) && $data['start'] > 0 ? (int)$data['start'] : 0;
            $limit = isset($data['limit']) && $data['limit'] > 0 ? (int)$data['limit'] : 20;
            $sql .= " LIMIT " . $start . ", " . $limit;
        }
    
        $result = $this->db->query($sql);
    
        return $result->rows;
    }    

    public function getCountBySettingCode($store_id, $code) {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "setting` WHERE store_id = '".(int)$store_id."' AND `code` LIKE '".$this->db->escape($code)."%'";    
        $result = $this->db->query($sql); 
        return (int)$result->row['total'];
    }

    public function getSettingCountByCharacterLimit($store_id, $key, $minLimit, $maxLimit) {    
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "setting` WHERE store_id = '".(int)$store_id."' AND `key` LIKE '".$this->db->escape($key)."%' AND (CHAR_LENGTH(`value`) < " . $minLimit . " OR CHAR_LENGTH(`value`) > " . $maxLimit . ")";
    
        $result = $this->db->query($sql); 

        return (int)$result->row['total'];
    }

    public function getSettingByCharacterLimit($data, $store_id) {
        $minLimit = (int)$data['minLimit'];
        $maxLimit = isset($data['maxLimit']) ? $data['maxLimit'] : false;
    
        $sql = "SELECT `setting_id` AS id, `key`, `value`, CHAR_LENGTH(`value`) as characters FROM `" . DB_PREFIX . "setting` WHERE store_id = '".(int)$store_id."' AND CHAR_LENGTH(`value`) < " . $minLimit;
    
        if ($maxLimit !== false) {
            $sql .= " OR CHAR_LENGTH(`value`) > " . $maxLimit;
        }
    
        if (isset($data['start']) || isset($data['limit'])) {
            $start = isset($data['start']) && $data['start'] > 0 ? (int)$data['start'] : 0;
            $limit = isset($data['limit']) && $data['limit'] > 0 ? (int)$data['limit'] : 20;
            $sql .= " LIMIT " . $start . ", " . $limit;
        }

        $result = $this->db->query($sql); 

        return $result->rows;
    }
    
}
?>