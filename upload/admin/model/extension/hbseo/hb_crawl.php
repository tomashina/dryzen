<?php
class ModelExtensionHbseoHbCrawl extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "seo_crawl_routes` (
			`crawl_route_id` int(11) NOT NULL AUTO_INCREMENT,
			`route` VARCHAR(50),
			`meta` VARCHAR(50),
			PRIMARY KEY (`crawl_route_id`)
		)");

		$this->resetCustomRoutes();
		$this->installOcmod();			
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "seo_crawl_routes`");

		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'hb_crawl_ocmod'");
	}

	public function installOcmod(){
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'hb_crawl_ocmod'");
		
		$ocmod_filename = 'ocmod_hb_crawl_3xxx.txt';
		$ocmod_name 	= 'SEO - Index / Crawl Optimizer [3xxx]';
		
		$ocmod_version 	= $this->hb_extension_version;
		$ocmod_code 	= 'hb_crawl_ocmod';	
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

	public function resetCustomRoutes(){
		$routes = array(
			'information/contact'   => 'index, follow',
			'account/login' 		=> 'noindex, nofollow',
			'account/register' 		=> 'noindex, follow',
			'checkout/cart' 		=> 'noindex, nofollow',
			'checkout/checkout'	 	=> 'noindex, nofollow',
			'account/voucher'   	=> 'index, follow',
			'affiliate/login'   	=> 'noindex, nofollow',
			'affiliate/register'   	=> 'noindex, follow',
		);

		foreach ($routes as $key => $value){
			$data['route'] = $key;
			$data['meta'] = $value;

			$this->addRoute($data);
		}
	}

	public function options(){
		return array('No Tag','index, follow','noindex, follow','noindex, nofollow');
	}

	public function getRoutes(){
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."seo_crawl_routes");
		if ($query->rows){
			return $query->rows;
		}else{
			return [];
		}
	}

	public function addRoute($data){
		$this->db->query("INSERT INTO ".DB_PREFIX."seo_crawl_routes (route, meta) VALUES ('".$this->db->escape($data['route'])."', '".$this->db->escape($data['meta'])."')");
	}

	public function updateRoute($data){
		$this->db->query("UPDATE ".DB_PREFIX."seo_crawl_routes SET `meta` = '".$this->db->escape($data['meta'])."' WHERE crawl_route_id = '".(int)$data['route_id']."'");
	}

	public function checkRoute($route){
		$query = $this->db->query("SELECT count(*) AS total FROM ".DB_PREFIX."seo_crawl_routes WHERE trim(route) = '".$this->db->escape($route)."'");
		if ($query->row['total'] == 0){
			return true;
		}else{
			return false;
		}
	}

	public function deleteRoute($route_id){
		$this->db->query("DELETE FROM ".DB_PREFIX."seo_crawl_routes WHERE crawl_route_id = '".(int)$route_id."'");
	}

}
?>