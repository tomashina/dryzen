<?php
class ModelExtensionBaselBasel extends Model {
	public function getSpecialEndDate($product_id) {
	  $query = $this->db->query("SELECT date_end FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . $this->config->get('config_customer_group_id') . "' AND ((date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
        
		if ($query->num_rows) {
            return array(
                'date_end'   => $query->row['date_end'],
            );
        } else {
            return false;
        }
	}
	

	
}