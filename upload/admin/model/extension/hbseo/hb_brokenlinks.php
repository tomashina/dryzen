<?php
class ModelExtensionHbseoHbBrokenlinks extends Model {
	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "error` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `error` text NOT NULL,
			  `redirect` text,
			  `type` int(11) NOT NULL DEFAULT '301',
			  `author` int(11) NOT NULL DEFAULT '3',
			  `hits` int(11) NOT NULL DEFAULT '1',
			  `redirect_hits` int(11) NOT NULL DEFAULT '0',
			  `store_id` int(11) NOT NULL DEFAULT '0',
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `date_modified` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			)");
			
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "error_logs` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `error` text NOT NULL,
			  `referrer` text,
			  `user_agent` text,
			  `ip` varchar(20) DEFAULT NULL,
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)");
			
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "error_keyword` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `keyword` VARCHAR(500),
			  `redirect_url` text,
			  `store_id` int(11) NOT NULL DEFAULT '0',
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)");
			
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "error_replacer` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `match` VARCHAR(500),
			  `replace` VARCHAR(500),
			  `store_id` int(11) NOT NULL DEFAULT '0',
			  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`)
			)");
			
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'broken_link_manager_ocmod'");

		if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$ocmod_filename = 'ocmod_hb_redirectmanager_3xxx.txt';
			$ocmod_name = 'SEO - Broken Link Redirect Manager [3xxx]';
		} else {
			$ocmod_filename = 'ocmod_hb_redirectmanager_2xxx.txt';
			$ocmod_name = 'SEO - Broken Link Redirect Manager [2xxx]';
		}
		
		$ocmod_version = $this->hb_extension_version;
		$ocmod_code = 'broken_link_manager_ocmod';	
		$ocmod_author = 'HuntBee OpenCart Services';
		$ocmod_link = 'https://www.huntbee.com';
		
		$file = DIR_APPLICATION . 'view/template/extension/hbseo/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{huntbee_version}', $ocmod_version, $ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}
		
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "error`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "error_logs`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "error_keyword`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "error_replacer`");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'broken_link_manager_ocmod'");
	}

	public function update() {
		$this->install();
	}
	
	public function getrecords($data){		
		$sql = "SELECT * FROM `".DB_PREFIX."error` WHERE store_id = '".(int)$data['store_id']."'";
		
		if ($data['search_link']){
			$sql .= " AND (`error` LIKE '%".urlencode($data['search_link'])."%' OR `redirect` LIKE '%".urlencode($data['search_link'])."%')";
		}
		
		if ($data['sauthor'] <> '0'){
			$sql .= " AND `author`= ".$data['sauthor'];
		}
		
		if ($data['sredirect'] == '1'){
			$sql .= " AND (`redirect` IS NULL OR trim(redirect) = '')";
		}
		
		if ($data['sredirect'] == '2'){
			$sql .= " AND (`redirect` IS NOT NULL OR trim(redirect) <> '')";
		}
		
		$sql .=  " ORDER BY ".$data['ssort']." ".$data['sorder'];
		
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
		$sql = "SELECT * FROM `".DB_PREFIX."error` WHERE store_id = '".(int)$data['store_id']."'";
		if ($data['search_link']){
			$sql .= " AND (`error` LIKE '%".urlencode($data['search_link'])."%' OR `redirect` LIKE '%".urlencode($data['search_link'])."%')";
		}
		
		if ($data['sauthor'] <> '0'){
			$sql .= " AND `author`= ".$data['sauthor'];
		}
		
		if ($data['sredirect'] == '1'){
			$sql .= " AND (`redirect` IS NULL OR trim(redirect) = '')";
		}
		
		if ($data['sredirect'] == '2'){
			$sql .= " AND (`redirect` IS NOT NULL OR trim(redirect) <> '')";
		}
		
		$sql .=  " ORDER BY ".$data['ssort']." ".$data['sorder'];
		$results = $this->db->query($sql);
		return $results->num_rows;
	}
	
	public function deleteRecord($id) {
		$query = $this->db->query("SELECT `error` FROM `" . DB_PREFIX . "error` WHERE id = '" . (int)$id . "'");
		$error = $query->row['error'];
		$this->db->query("DELETE FROM `" . DB_PREFIX . "error` WHERE id = '" . (int)$id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "error_logs` WHERE `error` = '".$this->db->escape($error)."'");
	}
	
	public function checkRedirect($redirect) {
		$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error` WHERE BINARY error = '" . $this->db->escape($redirect) . "'");
		if ($result->num_rows > 0){
			return false;
		}else{
			return true;
		}
	}
	
	public function isSameRedirect($redirect,$id) {
		$result = $this->db->query("SELECT `redirect` FROM `" . DB_PREFIX . "error` WHERE id = '" . (int)$id . "'");
		if ($result->row['redirect'] == $redirect){
			return true;
		}else{
			return false;
		}
	}
	
	public function updateRecord($id, $redirect, $type = '301') {
		$this->db->query("UPDATE `" . DB_PREFIX . "error` SET redirect = '".$this->db->escape($redirect)."',`type` = '".$type."' WHERE id = '" . (int)$id . "'");
	}
	
	
	public function insertRecord($error, $redirect, $type = '301', $author = 3, $store_id = 0) {
		$query = $this->db->query("SELECT id FROM `" . DB_PREFIX . "error` WHERE BINARY error = '".$this->db->escape($error)."' AND `store_id` = '".(int)$store_id."' LIMIT 1");
		
		if ($query->num_rows > 0){
			$id = $query->row['id'];
			$this->db->query("UPDATE `" . DB_PREFIX . "error` SET redirect = '".$this->db->escape($redirect)."', hits = hits+1, `type` = '".$type."', `author` = '".$author."' WHERE id = '" . (int)$id . "' AND `store_id` = '".(int)$store_id."'");
		}else{
			$this->db->query("INSERT INTO `" . DB_PREFIX . "error` (error,redirect,type,author,store_id,date_modified) VALUES ('".$this->db->escape($error)."','".$this->db->escape($redirect)."','".$type."','".$author."', '".(int)$store_id."', now())");
		}
	}
	
	public function insertKeyword($keyword, $redirect, $store_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error_keyword` WHERE keyword = '".$this->db->escape($keyword)."' AND `store_id` = '".(int)$store_id."' LIMIT 1");
		if ($query->num_rows > 0){
			$id = $query->row['id'];
			$this->db->query("UPDATE `" . DB_PREFIX . "error_keyword` SET redirect_url = '".$this->db->escape($redirect)."' WHERE id = '" . (int)$id . "' AND `store_id` = '".(int)$store_id."'");
		}else{
			$this->db->query("INSERT INTO `" . DB_PREFIX . "error_keyword` (`keyword`,`redirect_url`,`store_id`) VALUES ('".$this->db->escape($keyword)."','".$this->db->escape($redirect)."','".(int)$store_id."')");
		}
	}
	
	public function insertReplacer($match, $replace, $store_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error_replacer` WHERE `match` = '".$this->db->escape($match)."' AND `store_id` = '".(int)$store_id."' LIMIT 1");
		if ($query->num_rows > 0){
			$id = $query->row['id'];
			$this->db->query("UPDATE `" . DB_PREFIX . "error_replacer` SET `replace` = '".$this->db->escape($replace)."' WHERE id = '" . (int)$id . "' AND `store_id` = '".(int)$store_id."'");
		}else{
			$this->db->query("INSERT INTO `" . DB_PREFIX . "error_replacer` (`match`,`replace`,`store_id`) VALUES ('".$this->db->escape($match)."','".$this->db->escape($replace)."','".(int)$store_id."')");
		}
	}
	
	public function getkeywords($store_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error_keyword` WHERE `store_id` = '".(int)$store_id."'");
		if ($query->num_rows > 0) {
			return $query->rows;
		}else{
			return false;
		}
	}
	
	public function getUrlReplacers($store_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error_replacer` WHERE `store_id` = '".(int)$store_id."'");
		if ($query->num_rows > 0) {
			return $query->rows;
		}else{
			return false;
		}
	}
	
	public function getReferrers($id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error_logs` WHERE `error` = (SELECT error from `" . DB_PREFIX . "error` WHERE id = '".(int)$id."')");
		if ($query->num_rows > 0) {
			return $query->rows;
		}else{
			return false;
		}
	}

	public function deleteAllRecords($store_id){
		$query = $this->db->query("DELETE FROM `" . DB_PREFIX . "error` WHERE store_id = '".(int)$store_id."'");
		$query = $this->db->query("TRUNCATE `" . DB_PREFIX . "error_logs`");
	}

	public function authorReference($author_id){
		switch ($author_id) {
			case '1':
				return '<span class="text text-primary">Admin (200)</span>';
				break;
			
			case '2':
				return '<span class="text text-primary">Admin (404)</span>';
				break;
			
			default:
				return '<span class="text text-danger">System</span>';
				break;
		}
	}
}
?>