<?php
class ModelExtensionHbseoHbMetatags extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hb_route_meta` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `route` VARCHAR(200) NOT NULL,
			  `meta_title` VARCHAR(300) NOT NULL,
			  `meta_description` VARCHAR(300) NOT NULL,
			  `meta_keyword` VARCHAR(300) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL,
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)");

		$this->installOcmod();
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_route_meta`");

		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_metatags_ocmod'");
	}

	public function installOcmod(){
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_metatags_ocmod'");
		
		$ocmod_filename = 'ocmod_hb_meta_3xxx.txt';
		$ocmod_name 	= 'SEO - Meta Manager [3xxx]';
		
		$ocmod_version 	= $this->hb_extension_version;
		$ocmod_code 	= 'huntbee_seo_metatags_ocmod';	
		$ocmod_author 	= 'HuntBee OpenCart Services';
		$ocmod_link 	= 'https://www.huntbee.com';
		
		$file = DIR_APPLICATION . 'view/template/extension/hbseo/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{version}',$ocmod_version,$ocmod_xml);
			$ocmod_xml = str_replace('{name}',$ocmod_name,$ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}	
	}
	
	public function getRoutes($store_id = 0, $language_id = 1){
		$results = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_route_meta` WHERE store_id = '".(int)$store_id."' AND language_id = '".(int)$language_id."' ORDER BY date_added DESC");
		if ($results->rows) {
			return $results->rows;
		}else{
			return false;
		}
	}

	public function isMetaExists($data){
		$query = $this->db->query("SELECT count(*) as total FROM  `" . DB_PREFIX . "hb_route_meta` WHERE `route` = '".$data['route']."' AND store_id = '".(int)$data['store_id']."' AND language_id = '".(int)$data['language_id']."'");
		if ($query->row['total'] > 0){
			return true;
		}else{
			return false;
		}
	}

	public function addMeta($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_route_meta` (`route`, `meta_title`, `meta_description`, `meta_keyword`, `language_id`, `store_id`) VALUES ('".$this->db->escape($data['route'])."','".$this->db->escape($data['meta_title'])."','".$this->db->escape($data['meta_description'])."','".$this->db->escape($data['meta_keyword'])."','".(int)$data['language_id']."','".(int)$data['store_id']."')");
	}
	
	public function deleteMeta($id) {
		$this->db->query("DELETE FROM `".DB_PREFIX."hb_route_meta` WHERE `id` = '".(int)$id."'");
	}	
}
?>