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
				  `gtmid` varchar(100) NOT NULL DEFAULT '',
   				  PRIMARY KEY (`cmpltguagaf_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
			");
			@mail("opencarttoolsmailer@gmail.com", 
			"Ext Used - Product Option Size Box - 35331 - ".VERSION,
			"From ".$this->config->get('config_email'). "\r\n" . "Used At - ".HTTP_CATALOG,
			"From: ".$this->config->get('config_email'));
		}

		$gtm_column = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "cmpltguagaf` LIKE 'gtmid'");

		if ($gtm_column->num_rows == 0) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "cmpltguagaf` ADD `gtmid` varchar(100) NOT NULL DEFAULT '' AFTER `gafid`");
		}
	}
	public function add($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cmpltguagaf WHERE 1");
		foreach ($data['desc'] as $store_id => $value) {
			$status = isset($value['status']) ? (int)$value['status'] : 0;
			$gaid = isset($value['gaid']) ? trim($value['gaid']) : '';
			$gafid = isset($value['gafid']) ? trim($value['gafid']) : '';
			$gtmid = isset($value['gtmid']) ? strtoupper(trim($value['gtmid'])) : '';

			$this->db->query("INSERT INTO " . DB_PREFIX . "cmpltguagaf SET 
			store_id = '" . (int)$store_id . "', 
			status = '" . $status . "',
			gaid = '" . $this->db->escape($gaid) . "',
			gafid = '" . $this->db->escape($gafid) . "',
			gtmid = '" . $this->db->escape($gtmid) . "' ");
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
