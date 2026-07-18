<?php
class ModelExtensionHbseoHbSnippets extends Model {
	public function install(){
		$this->installOcmod();
	}
	
	public function uninstall() {
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_snippet_ocmod'");
	}

	public function update(){
		$this->installOcmod();
	}

	public function installOcmod(){
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_seo_snippet_ocmod'");

		if ((version_compare(VERSION,'2.0.0.0','>=' )) and (version_compare(VERSION,'2.3.0.0','<' ))) {
			$ocmod_filename = 'ocmod_hb_seo_snippets_2000_2200.txt';
			$ocmod_name = 'SEO - Structured Data Markup [2000-2200]';
		}else if ((version_compare(VERSION,'2.3.0.0','>=' )) and (version_compare(VERSION,'3.0.0.0','<' ))) {
			$ocmod_filename = 'ocmod_hb_seo_snippets_23xx.txt';
			$ocmod_name = 'SEO - Structured Data Markup [23xx]';
		}else if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$ocmod_filename = 'ocmod_hb_seo_snippets_3xxx.txt';
			$ocmod_name = 'SEO - Structured Data Markup [3xxx]';
		}
		
		$ocmod_version 	= $this->hb_extension_version;
		$ocmod_code 	= 'huntbee_seo_snippet_ocmod';	
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