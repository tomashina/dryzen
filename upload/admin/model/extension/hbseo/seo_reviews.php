<?php
class ModelExtensionHbseoSeoReviews extends Model {
	public function install(){
		$this->installOcmod();			
	}
	
	public function uninstall() {
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'seo_reviews_ocmod'");
	}

	public function installOcmod(){
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'seo_reviews_ocmod'");
		
		$ocmod_filename = 'ocmod_seo_reviews_3xxx.txt';
		$ocmod_name 	= 'SEO - Reviews';
		
		$ocmod_version 	= $this->hb_extension_version;
		$ocmod_code 	= 'seo_reviews_ocmod';	
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

}
?>