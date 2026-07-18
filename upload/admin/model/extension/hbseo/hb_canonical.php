<?php
class ModelExtensionHbseoHbCanonical extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "category_canonical` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`category_id` int(11) NOT NULL,
			`path` varchar(100) NOT NULL,
			PRIMARY KEY (`id`)
		)DEFAULT CHARSET=utf8");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "custom_canonical` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
  			`url` varchar(300) NOT NULL,
 			`canonical` varchar(300) NOT NULL,
			PRIMARY KEY (`id`)
		)DEFAULT CHARSET=utf8");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_canonical` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`product_id` int(11) NOT NULL,
			`path` varchar(100) NOT NULL,
			PRIMARY KEY (`id`)
		)DEFAULT CHARSET=utf8");
			
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "category_canonical` ADD INDEX( `category_id`);");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_canonical` ADD INDEX( `product_id`);");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "custom_canonical` ADD INDEX( `url`);");
			
		if ((version_compare(VERSION,'2.0.0.0','>=' )) and (version_compare(VERSION,'2.1.0.0','<' ))) {
			$ocmod_filename = 'ocmod_canonical_20xx.txt';
			$ocmod_name = 'SEO - Canonical URL [20xx]';
		}else if ((version_compare(VERSION,'2.1.0.0','>=' )) and (version_compare(VERSION,'2.2.0.0','<' ))) {
			$ocmod_filename = 'ocmod_canonical_21xx.txt';
			$ocmod_name = 'SEO - Canonical URL [21xx]';
		}else if ((version_compare(VERSION,'2.2.0.0','>=' )) and (version_compare(VERSION,'2.3.0.0','<' ))) {
			$ocmod_filename = 'ocmod_canonical_22xx.txt';
			$ocmod_name = 'SEO - Canonical URL [22xx]';
		}else if ((version_compare(VERSION,'2.3.0.0','>=' )) and (version_compare(VERSION,'3.0.0.0','<' ))) {
			$ocmod_filename = 'ocmod_canonical_23xx.txt';
			$ocmod_name = 'SEO - Canonical URL [23xx]';
		}else if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$ocmod_filename = 'ocmod_canonical_3xxx.txt';
			$ocmod_name = 'SEO - Canonical URL [3xxx]';
		}
		
		$ocmod_version = $this->hb_extension_version;
		$ocmod_code = 'huntbee_seo_canonical_ocmod';	
		$ocmod_author = 'HuntBee OpenCart Services';
		$ocmod_link = 'https://www.huntbee.com/';
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = '".$this->db->escape($ocmod_code)."'");
		
		$file = DIR_APPLICATION . 'view/template/extension/hbseo/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{huntbee_version}',$ocmod_version,$ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}	
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "category_canonical`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "custom_canonical`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_canonical`");
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_canonical_ocmod'");
	}
	
	public function getRecords($data){
		$sql = "SELECT * FROM `".DB_PREFIX."custom_canonical`";
		if ($data['search']){
			$sql .= " WHERE (`url` LIKE '%".$this->db->escape($data['search'])."%' OR `canonical` LIKE '%".$this->db->escape($data['search'])."%')";
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
	
	public function getTotalRecords($data){
		$sql = "SELECT count(*) as count FROM `".DB_PREFIX."custom_canonical`";	
		if ($data['search']){
			$sql .= " WHERE (`url` LIKE '%".$this->db->escape($data['search'])."%' OR `canonical` LIKE '%".$this->db->escape($data['search'])."%')";
		}
		$results = $this->db->query($sql);
		return $results->row['count'];
	}
	
	public function insertCanonical($url, $canonical) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_canonical` WHERE url = '".$this->db->escape($url)."'");
		if ($query->rows){
			return false;
		}else{
			$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_canonical` (`url`,`canonical`) VALUES ('".$this->db->escape($url)."','".$this->db->escape($canonical)."')");
			return true;
		}
	}
	
	public function deleteRecord($id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_canonical` WHERE id = '" . (int)$id . "'");
	}
	
	
}
?>