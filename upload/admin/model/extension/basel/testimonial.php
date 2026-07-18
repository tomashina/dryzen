<?php
class ModelExtensionBaselTestimonial extends Model {

	public function addTestimonial($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "testimonial SET 
		name='".$this->db->escape($data['name'])."', 
		image='".$this->db->escape($data['image'])."', 
		org = '".$this->db->escape($data['org'])."', 
		status = '" . (int)$data['status'] . "'
		");

		$testimonial_id = $this->db->getLastId();
		 
		foreach ($data['testimonial_description'] as $language_id => $value) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "testimonial_description SET 
		testimonial_id = '" . (int)$testimonial_id . "', 
		language_id = '" . (int)$language_id . "', 
		description = '" . $this->db->escape($value['description']) . "'
		");
		}
		
		if (isset($data['testimonial_store'])) {
			foreach ($data['testimonial_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "testimonial_to_store SET testimonial_id = '" . (int)$testimonial_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

	}
	
	public function editTestimonial($testimonial_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "testimonial SET 
		name='".$this->db->escape($data['name'])."', 
		image='".$this->db->escape($data['image'])."', 
		org = '".$this->db->escape($data['org'])."', 
		status = '" . (int)$data['status'] . "'
		WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");
					
		foreach ($data['testimonial_description'] as $language_id => $value) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "testimonial_description SET 
		testimonial_id = '" . (int)$testimonial_id . "', 
		language_id = '" . (int)$language_id . "', 
		description = '" . $this->db->escape($value['description']) . "'
		");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "testimonial_to_store WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		
		if (isset($data['testimonial_store'])) {
			foreach ($data['testimonial_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "testimonial_to_store SET testimonial_id = '" . (int)$testimonial_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		return $testimonial_id;
		
	}
	
	public function getTestimonialStores($testimonial_id) {
		$testimonial_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonial_to_store WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		foreach ($query->rows as $result) {
			$testimonial_store_data[] = $result['store_id'];
		}

		return $testimonial_store_data;
	}
	
	public function deleteTestimonial($testimonial_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "testimonial WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");
	}	

	public function getTestimonial($testimonial_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "testimonial WHERE testimonial_id = '" . (int)$testimonial_id . "'");
		
		return $query->row;
	}
		
	public function getTestimonials($data = array()) {
	
		if ($data) {
			if (!isset($data['language_id']))  $data['language_id']=$this->config->get('config_language_id');
			$sql = "SELECT * FROM " . DB_PREFIX . "testimonial t LEFT JOIN " . DB_PREFIX . "testimonial_description td ON (t.testimonial_id = td.testimonial_id) where language_id = " . $data['language_id'];
		
			$sort_data = array(
				'td.description',				
				'td.title',
				't.name',
				't.status'
			);		
			
			$query = $this->db->query($sql);

			return $query->rows;
		} else {
		
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonial t LEFT JOIN " . DB_PREFIX . "testimonial_description td ON (t.testimonial_id = td.testimonial_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY td.title");

			$testimonial_data = $query->rows;
			
			return $testimonial_data;			
		}
	}
	
	public function getTestimonialDescriptions($testimonial_id) {
		
		$testimonial_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");

		foreach ($query->rows as $result) {
			$testimonial_description_data[$result['language_id']] = array(
				'description' => $result['description']
			);
		}
		
		return $testimonial_description_data;		
	}

	
	public function getTotalTestimonials() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "testimonial");
		
		return $query->row['total'];
	}	

	
	public function dropDatabaseTables() {
		$sql = "DROP TABLE IF EXISTS `".DB_PREFIX."testimonial`;";
		$this->db->query($sql);
		$sql = "DROP TABLE IF EXISTS `".DB_PREFIX."testimonial_description`;";
		$this->db->query($sql);
	}

}