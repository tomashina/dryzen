<?php
class ModelExtensionHbseoHbKeywordHighlight extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hb_seo_keywords` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `keyword` VARCHAR(300) NOT NULL,
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			) DEFAULT CHARSET=utf8");
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_seo_keywords`");
	}
	
	public function getrecords($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_seo_keywords`";
		
		if ($data['search']){
			$sql .= " WHERE `keyword` LIKE '%".$this->db->escape($data['search'])."%'";
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
	
	public function getTotalrecords($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_seo_keywords`";
		if ($data['search']){
			$sql .= " WHERE `keyword` LIKE '%".$this->db->escape($data['search'])."%'";
		}
		$results = $this->db->query($sql);
		return $results->num_rows;
	}
	
	public function deleteRecord($id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_seo_keywords` WHERE id = '" . (int)$id . "'");
	}
	
}
?>