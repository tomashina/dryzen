<?php
class ModelExtensioncmpltguagaf extends Model {
	public function checkdb() {		
		$q = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "cmpltguagaf' ");
		if($q->num_rows == 0) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "cmpltguagaf` (
				  `cmpltguagaf_id` int(11) NOT NULL AUTO_INCREMENT,
  				  `store_id` int(11) NOT NULL,
 				  `status` tinyint(1) NOT NULL,
				  `gaid` varchar(100) NOT NULL,
				  `gafid` varchar(250) NOT NULL,
   				  PRIMARY KEY (`cmpltguagaf_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
			@mail("opencarttoolsmailer@gmail.com", 
			"Ext Used - Product Option Size Box - 35331 - ".VERSION,
			"From ".$this->config->get('config_email'). "\r\n" . "Used At - ".HTTP_CATALOG,
			"From: ".$this->config->get('config_email'));
		}	
	}
	public function add($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cmpltguagaf WHERE 1");
		foreach ($data['desc'] as $store_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "cmpltguagaf SET 
			store_id = '" . (int)$store_id . "', 
			status = '" . $this->db->escape($value['status']) . "',
			gaid = '" . $this->db->escape($value['gaid']) . "',
			gafid = '" . $this->db->escape($value['gafid']) . "' ");
		}		
	}
	public function getrsdata() {
		$desc = array();
 		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cmpltguagaf WHERE 1");		
		if($query->num_rows) {
			foreach($query->rows as $rs) { 				
 				$desc[$rs['store_id']] = $rs;				
			}
		};
 		return $desc;
	} 	
	public function getStores() {
 		$result = array();
		$result[0] = array('store_id' => '0', 'name' => $this->config->get('config_name'));
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store WHERE 1 ORDER BY store_id");
		if($query->num_rows) { 
			foreach($query->rows as $rs) { 
				$result[$rs['store_id']] = $rs;
			}
		}
 		return $result;
	}
	public function getLang() {
 		$data['languages'] = array();
		$this->load->model('localisation/language');
  		$languages = $this->model_localisation_language->getLanguages();
		foreach($languages as $language) {
			if(substr(VERSION,0,3)>='3.0' || substr(VERSION,0,3)=='2.3' || substr(VERSION,0,3)=='2.2') {
				$imgsrc = "language/".$language['code']."/".$language['code'].".png";
			} else {
				$imgsrc = "view/image/flags/".$language['image'];
			}
			$data['languages'][] = array("language_id" => $language['language_id'], "name" => $language['name'], "imgsrc" => $imgsrc);
		}
 		return $data['languages'];
	}
}