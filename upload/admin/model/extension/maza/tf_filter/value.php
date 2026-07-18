<?php
class ModelExtensionMazaTfFilterValue extends Model {
	public function addValue($filter_id, $data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "tf_filter_value SET filter_id = '" . (int)$filter_id . "', `regex` = '" . (int)$data['regex'] . "', `value` = '" . $this->db->escape($data['value']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");

		$value_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "tf_filter_value SET image = '" . $this->db->escape($data['image']) . "' WHERE value_id = '" . (int)$value_id . "'");
		}

		foreach ($data['value_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "tf_filter_value_description SET value_id = '" . (int)$value_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		return $value_id;
	}

	public function editValue($value_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "tf_filter_value SET sort_order = '" . (int)$data['sort_order'] . "', `regex` = '" . (int)$data['regex'] . "', `value` = '" . $this->db->escape($data['value']) . "', status = '" . (int)$data['status'] . "' WHERE value_id = '" . (int)$value_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "tf_filter_value SET image = '" . $this->db->escape($data['image']) . "' WHERE value_id = '" . (int)$value_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "tf_filter_value_description WHERE value_id = '" . (int)$value_id . "'");

		foreach ($data['value_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "tf_filter_value_description SET value_id = '" . (int)$value_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
                
                if(!$data['status']){
                    $this->db->query("DELETE FROM " . DB_PREFIX . "tf_filter_value_to_product WHERE value_id = '" . (int)$value_id . "'");
                }
	}
        
        public function copyValues($from_filter_id, $to_filter_id) {
                $start = 0;
                
                do{
                    $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tf_filter_value WHERE filter_id = '" . (int)$from_filter_id . "' LIMIT $start,20");
                
                    foreach($query->rows as $value){
                        $value['filter_id'] = $to_filter_id;
                        $value['value_description'] = $this->getValueDescriptions($value['value_id']);

                        $this->addValue($to_filter_id, $value);
                    }
                    
                    $start += 20;
                } while($query->num_rows);
		
	}
        
        public function enableValue($value_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "tf_filter_value SET status = 1 WHERE value_id = '" . (int)$value_id . "'");
	}
        
        public function disableValue($value_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "tf_filter_value SET status = 0 WHERE value_id = '" . (int)$value_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "tf_filter_value_to_product WHERE value_id = '" . (int)$value_id . "'");
	}

	public function deleteValue($value_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "tf_filter_value WHERE value_id = '" . (int)$value_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "tf_filter_value_description WHERE value_id = '" . (int)$value_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "tf_filter_value_to_product WHERE value_id = '" . (int)$value_id . "'");
	}

	public function getValue($value_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "tf_filter_value v LEFT JOIN " . DB_PREFIX . "tf_filter_value_description vd ON (v.value_id = vd.value_id) WHERE v.value_id = '" . (int)$value_id . "' AND vd.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
                
		return $query->row;
	}

	public function getValues($filter_id, $data = array()) {
		$sql = "SELECT DISTINCT *, (SELECT COUNT(*) FROM " . DB_PREFIX . "tf_filter_value_to_product WHERE value_id = v.value_id) total_product FROM " . DB_PREFIX . "tf_filter_value v LEFT JOIN " . DB_PREFIX . "tf_filter_value_description vd ON (v.value_id = vd.value_id) WHERE v.filter_id = '" . (int)$filter_id . "' AND vd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND vd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
                if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND v.status = '" . (int)$data['filter_status'] . "'";
		}
                
                if (isset($data['filter_regex']) && !is_null($data['filter_regex'])) {
			$sql .= " AND v.regex = '" . (int)$data['filter_regex'] . "'";
		}

		$sql .= " GROUP BY v.value_id";

		$sort_data = array(
			'name',
			'sort_order',
                        'date_added',
                        'status',
                        'regex',
                        'total_product'
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

	public function getValueDescriptions($value_id) {
		$value_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tf_filter_value_description WHERE value_id = '" . (int)$value_id . "'");

		foreach ($query->rows as $result) {
			$value_description_data[$result['language_id']] = array(
				'name'             => $result['name']
			);
		}

		return $value_description_data;
	}

	public function getTotalValues($filter_id, $data = array()) {
		$sql = "SELECT COUNT(DISTINCT v.value_id) AS total FROM " . DB_PREFIX . "tf_filter_value v LEFT JOIN " . DB_PREFIX . "tf_filter_value_description vd ON (v.value_id = vd.value_id) WHERE v.filter_id = '" . (int)$filter_id . "' AND vd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
                
                if (!empty($data['filter_name'])) {
			$sql .= " AND vd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
                
                if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND v.status = '" . (int)$data['filter_status'] . "'";
		}
                
                if (isset($data['filter_regex']) && !is_null($data['filter_regex'])) {
			$sql .= " AND v.regex = '" . (int)$data['filter_regex'] . "'";
		}
                
                $query = $this->db->query($sql);

		return $query->row['total'];
	}
        
        private function getCSVdata($file){
                if(is_file($file)){
                    $file = fopen($file,"r");
                    
                    if($column = fgetcsv($file)){
                        while(($data = fgetcsv($file)) !== FALSE){
                            $data =  array_combine($column, $data);
                            
                            if(!empty($data['name']) && !empty($data['value'])){
                                yield array_merge(array('regex' => 0, 'sort_order' => 0, 
                                    'status' => 1, 'image' => ''), $data);
                            }
                        }
                    }
                    
                    fclose($file);
                }
        }
        
        public function import($filter_id, $file, $merge = true){
                if(!$merge){
                    $this->db->query("DELETE fvp, fvd, fv FROM " . DB_PREFIX . "tf_filter_value fv LEFT JOIN " . DB_PREFIX . "tf_filter_value_description fvd ON (fv.value_id = fvd.value_id) LEFT JOIN " . DB_PREFIX . "tf_filter_value_to_product fvp ON (fv.value_id = fvp.value_id) WHERE fv.filter_id = '" . (int)$filter_id . "'");
                }
                
                $this->load->model('localisation/language');
                
                $languages = $this->model_localisation_language->getLanguages();
                
                foreach($this->getCSVdata($file) as $data){
                    foreach($languages as $language){
                        $data['value_description'][$language['language_id']]['name'] = $data['name'];
                    }
                    
                    if($merge){
                        $query = $this->db->query("SELECT value_id FROM " . DB_PREFIX . "tf_filter_value WHERE `regex` = '" . (int)$data['regex'] . "' AND `value` = '" . $this->db->escape($data['value']) . "'");
                        
                        if($query->row){
                            $this->editValue($query->row['value_id'], $data);
                            continue;
                        }
                    }
                    
                    $this->addValue($filter_id, $data);
                }
        }
}