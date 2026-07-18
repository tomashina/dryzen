<?php
class ModelExtensionBlogBlogSetting extends Model { 
	
	
	// OPENCART 2.3 FUNCTIONS
	public function getHomeKeyword() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'extension/blog/home'");
		return $query->row;
	}
	public function saveHomeKeyword($code, $data, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'extension/blog/home'");
		if ($data['blog_home_url']) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'extension/blog/home', keyword = '" . $this->db->escape($data['blog_home_url']) . "'");
		}
	}
	
	// OPENCART 3.X FUNCTIONS
	public function getBlogHomeSeoUrls() {
		$blog_home_seo_url_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'extension/blog/home'");
		foreach ($query->rows as $result) {
			$blog_home_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}
		return $blog_home_seo_url_data;
	}
	
	public function saveBlogHomeKeyword($code, $data, $store_id = 0) {
	$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'extension/blog/home'");	
		if (isset($data['blog_home_seo_url'])) {
			foreach ($data['blog_home_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (trim($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'extension/blog/home', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
	}
	
	

}