<?php
class ModelExtensionHbseoHbTags extends Model {
	public function install(){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = 'hb_tags'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "event` (`code`, `trigger`, `action`, `status`, `sort_order`) VALUES ('hb_tags', 'admin/view/catalog/product_list/after', 'extension/hbseo/hb_tags/event_add_product_tag', '1', '0')");
	}

	public function uninstall(){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = 'hb_tags'");
	}

	public function getTotalProducts(){
		$sql = "SELECT count(*) as total FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id)";		
		$results = $this->db->query($sql);
		return $results->row['total'];
	}
	
	public function getTotalEmptyTagsProducts(){
		$sql = "SELECT count(*) as total FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON (p.product_id = pd.product_id) WHERE trim(pd.tag) = ''";		
		$results = $this->db->query($sql);
		return $results->row['total'];
	}
	
	public function clearTags(){
		$this->db->query("UPDATE ".DB_PREFIX."product_description SET tag = ''");
	}

	public function deleteTags($product_id){
		$this->db->query("UPDATE ".DB_PREFIX."product_description SET tag = '' WHERE product_id = '".(int)$product_id."'");
	}

	public function getRecords($data) {
		$sql = "SELECT pd.*, p.product_id AS item_id, p.model FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$data['language_id']."'";
		
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

	public function getTotalRecords($data){
		$sql = "SELECT count(p.product_id) AS total FROM `".DB_PREFIX."product` p LEFT JOIN `".DB_PREFIX."product_description` pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '".(int)$data['language_id']."'";
		if (!empty($data['search'])) {
			$sql .= " AND (p.product_id LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.name) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(p.model) LIKE '%".$this->db->escape($data['search'])."%' OR LOWER(pd.tag) LIKE '%".$this->db->escape($data['search'])."%')";
		}

		$results = $this->db->query($sql);
		return $results->row['total'];
	}
}
?>