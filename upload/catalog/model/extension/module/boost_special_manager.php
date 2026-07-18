<?php
//==============================================
//Special Manager
//Author 	: OpenCartBoost
//Email 	: support@opencartboost.com
//Website 	: http://www.opencartboost.com
//==============================================
class ModelExtensionModuleBoostSpecialManager extends Model {
	public function getProductCategories($product_id) {
		$product_category_data = array();
		
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		
		$catids = '';
		
		foreach ($query->rows as $result) {
			$catids .= "'" . $result['category_id'] . "',";
		}
		
		$sql = "SELECT * FROM " . DB_PREFIX . "boost_category_special bcs WHERE bcs.category_id IN (".rtrim($catids,",").") AND bcs.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((bcs.date_start = '0000-00-00' OR bcs.date_start < NOW()) AND (bcs.date_end = '0000-00-00' OR bcs.date_end > NOW())) ORDER BY bcs.priority ASC, bcs.discount_type ASC LIMIT 1";
		
		$query2 = $this->db->query($sql);
		
		return $query2->rows;
	}
}
?>
