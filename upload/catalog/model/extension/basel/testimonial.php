<?php
class ModelExtensionBaselTestimonial extends Model {
	
	public function getTestimonials($limit = 3) {
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonial t LEFT JOIN " . DB_PREFIX . "testimonial_description td ON (t.testimonial_id = td.testimonial_id) LEFT JOIN " . DB_PREFIX . "testimonial_to_store i2s ON (t.testimonial_id = i2s.testimonial_id) WHERE td.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND t.status = '1' ORDER BY RAND() LIMIT " . (int)$limit);

		return $query->rows;
	}
	
}