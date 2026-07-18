<?php
class ModelExtensionModuleBrokenlinkManager extends Model {
	
	public function insertErrorLog($data) {
		$current_url 		= $data['current_url'];
		$referrer_url 		= $data['referrer_url'];
		$user_agent 		= $data['user_agent'];
		$ip 				= $data['ip'];

		$this->db->query("INSERT INTO `" . DB_PREFIX . "error_logs` (error,referrer,user_agent,ip) VALUES ('".$this->db->escape($current_url)."','".$this->db->escape($referrer_url)."','".$this->db->escape($user_agent)."','".$this->db->escape($ip)."')");
		
		$redirect_default = !empty($this->config->get('hb_brokenlinks_defaulturl')) ? urlencode($this->config->get('hb_brokenlinks_defaulturl')) : '';
		$redirect_path = $redirect_default;
		
		//CHECK IF USER HAS SELECTED SMART REDIRECT
		$smart_url = $this->config->get('hb_brokenlinks_smarturl');
		if ($smart_url == 1){
			 $keywords = strtok($_SERVER['REQUEST_URI'], '?');
			 $keywords = explode('/',$keywords);
			 $keywords = array_reverse($keywords);
			 foreach ($keywords as $keyword){
			 	if (version_compare(VERSION, '3.0.0.0', '>=')){ 
			 		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword sounds like '" . $this->db->escape($keyword) . "' LIMIT 1");
				} else {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword sounds like '" . $this->db->escape($keyword) . "' LIMIT 1");
				}
				if ($query->row) {
					if (strlen($query->row['keyword']) > 2) { 
						$auto_redirect = urlencode($query->row['keyword']);
					}
					break;
				}
			 }
			// $construct_host = $http_protocol.$_SERVER['HTTP_HOST'].'/';
			$store_url = $this->config->get('config_url'); // Default store URL

			if ($this->config->get('config_secure') == 1) {
				$store_url = $this->config->get('config_ssl') ?: str_replace('http://', 'https://', $store_url);
			}

			$redirect_path = (isset($auto_redirect))? urlencode($store_url).$auto_redirect : $redirect_default;
		}
		
		//CHECK IF USER HAS SELECTED KEYWORD BASED REDIRECT
		if ($this->config->get('hb_brokenlinks_keywordurl')){
			$allkeywordurls = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error_keyword` WHERE `store_id`='".(int)$this->config->get('config_store_id')."'");
			if ($allkeywordurls->num_rows > 0){
				$keywordurls = $allkeywordurls->rows;
				foreach ($keywordurls as $keywordurl) {
					$keyword_term = strtolower($keywordurl['keyword']);
					$keyword_redirect_url = $keywordurl['redirect_url'];
					$keyword_current_url = strtolower($current_url);
					if (strpos($keyword_current_url,$keyword_term) !== false){
						$redirect_path = $keyword_redirect_url;
					}
				}
			}
		}

		$type = (strlen(trim($redirect_path)) === '') ? 404 : $this->config->get('hb_brokenlinks_rtype');

		$query = $this->db->query("SELECT id FROM `" . DB_PREFIX . "error` WHERE BINARY error = '".$this->db->escape($current_url)."' LIMIT 1");
		if ($query->num_rows > 0){
			$id = $query->row['id'];
			$redirect_url = $this->getRedirect($current_url);
			
			if (trim($redirect_url) == ''){
				$this->db->query("UPDATE `" . DB_PREFIX . "error` SET hits = hits+1, `redirect` = '".$this->db->escape($redirect_path)."', `type` = '".(int)$type."' WHERE id = '" . (int)$id . "'");
			}else{
				$verify_redirect_url = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error` WHERE BINARY error = '".$this->db->escape($redirect_url)."'");
				if ($verify_redirect_url->num_rows > 0){
					$redirect_path = $redirect_default;
					$this->db->query("UPDATE `" . DB_PREFIX . "error` SET `redirect` = '".$this->db->escape($redirect_path)."' WHERE redirect = '" . $this->db->escape($redirect_url) . "'");
				}
			
				$this->db->query("UPDATE `" . DB_PREFIX . "error` SET hits = hits+1 WHERE id = '" . (int)$id . "'");
			}
		}else{
			$this->db->query("INSERT INTO `" . DB_PREFIX . "error` (error, redirect, type, author, store_id, date_modified) VALUES ('".$this->db->escape($current_url)."','".$this->db->escape($redirect_path)."','".(int)$type."', 3, '".(int)$this->config->get('config_store_id')."', now())");
		}
	}
	
	public function validateCommonRedirect() {
		$http_protocol = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1')) ? "https://" : "http://";
    	$current_url = urlencode($http_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

		$query = $this->db->query("SELECT redirect FROM `" . DB_PREFIX . "error` WHERE BINARY error = '".$this->db->escape($current_url)."' and author = 1 LIMIT 1");
		if ($query->num_rows > 0){
			$redirect_url = urldecode($query->row['redirect']);
			$this->updateRedirecthits($current_url);
			$response_code = $this->getResponse($current_url);
			$this->response->redirect($redirect_url, $response_code);
			return;
		}
		
		if ($this->config->get('hb_brokenlinks_replacer')) {
			$replacer = $this->db->query("SELECT * FROM `" . DB_PREFIX . "error_replacer`");
			if ($replacer->rows) {
				foreach ($replacer->rows as $row) {
					$match = $row['match'];
					if (strpos($current_url,$match) !== false){
						$replaced_url = str_replace($match, $row['replace'], $current_url);
						$this->response->redirect(urldecode($replaced_url));
						return;
					}
				}
			}
		}

		//remove trailing slash code - needs admin improvement
		$current_url = rawurldecode($current_url);
		if (isset($this->request->get['route']) && $this->request->get['route'] != 'common/home' && substr($current_url, -1) == '/') {
			//$this->log->write($current_url);
			$redirect_url = rtrim($current_url, '/');
			//$this->log->write($redirect_url);
			$this->response->redirect($redirect_url, 301);
		}
	}
	
	public function getRedirect($current_url){
		$query = $this->db->query("SELECT redirect FROM `" . DB_PREFIX . "error` WHERE BINARY error = '".$this->db->escape($current_url)."' LIMIT 1");
		if ($query->num_rows > 0){
			return $query->row['redirect'];
		}else{
			return false;
		}
	}
	
	public function getResponse($current_url){
		$query = $this->db->query("SELECT type FROM `" . DB_PREFIX . "error` WHERE BINARY error = '".$this->db->escape($current_url)."' LIMIT 1");
		return $query->row['type'];
	}
	
	public function updateRedirecthits($current_url) {
		$this->db->query("UPDATE `" . DB_PREFIX . "error` SET redirect_hits = redirect_hits + 1 WHERE BINARY error = '".$this->db->escape($current_url)."'");
	}
	
	public function removeQueryStringParameter($url, $varname) {
		$parsedUrl = parse_url($url);
		$query = array();
	
		if (isset($parsedUrl['query'])) {
			parse_str($parsedUrl['query'], $query);
			unset($query[$varname]);
		}
	
		$path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
		$query = !empty($query) ? '?'. http_build_query($query) : '';
	
		if (isset($parsedUrl['scheme']) && $parsedUrl['host']){
            $outputurl = $parsedUrl['scheme']. '://'. $parsedUrl['host']. $path. $query;
	    }else{
	        $outputurl = ''; //may need improvement
	    }
		return urldecode($outputurl);
	}
	
	public function checkandredirect(){
		//adding delete query to auto delete unwanted records	
		$this->db->query("DELETE FROM `" . DB_PREFIX . "error` WHERE `hits` < '".(int)$this->config->get('hb_brokenlinks_adel_count')."' AND `author` = 3 AND store_id = '".(int)$this->config->get('config_store_id')."' AND date_added < DATE_SUB(NOW(), INTERVAL ".(int)$this->config->get('hb_brokenlinks_adel_days')." DAY)");

		$http_protocol = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1')) ? "https://" : "http://";
    	$current_url = $http_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
		// Get referrer URL if available
		$referrer_url = isset($_SERVER['HTTP_REFERER']) ? urlencode($_SERVER['HTTP_REFERER']) : NULL;
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	
		$queryparameters = $this->config->get('hb_brokenlinks_excludequery');
		
		if (isset($queryparameters) && strlen(trim($queryparameters)) > 3){
			$queryparameters = explode(',', $queryparameters);
			foreach ($queryparameters as $queryparameter) {
				$current_url = $this->removeQueryStringParameter($current_url, trim($queryparameter));
			}
		}
		
		$current_url = urlencode($current_url);
		
		$skip = false;
		
		// Check if IP is excluded
		$exclude_ip = $this->config->get('hb_brokenlinks_ignoreip');

		if (!empty($exclude_ip) && strpos($exclude_ip, $ip) !== false) {
			$skip = true;
		}
		
		//CHECK IF TO SKIP REDIRECT BASED ON USER AGENT
		$bots = $this->config->get('hb_brokenlinks_ignoreagents'); 
		if (!$skip && !empty($bots)){ 
			$bots = explode(',', $bots);
			foreach ($bots as $bot) {
				if (strpos($user_agent, trim($bot)) !== false) {
					$skip = true;
					break;
				}
			}
		}
				
		//CHECK IF TO SKIP REDIRECT BASED ON TERMS IN THE URL
		$excludes = $this->config->get('hb_brokenlinks_excludeterms'); 
		if (!empty($excludes) and $skip === false){ 
			$excludes = explode(',', $excludes);
			foreach ($excludes as $exclude) {
				$term = urlencode(trim($exclude));
				if (!empty($term) && strpos($current_url, $term) !== false) {
					$skip = true;
					break;
				}
			} 
		}

		if (!$skip) {
			$data['current_url'] 	= $current_url;
			$data['referrer_url'] 	= $referrer_url;
			$data['user_agent'] 	= $user_agent;
			$data['ip'] 			= $ip;

			$this->insertErrorLog($data);
			$redirect_url = $this->getRedirect($current_url);
			if ($redirect_url != false){
				$redirect_url = urldecode($redirect_url);
				$this->updateRedirecthits($current_url);
				$response_code = $this->getResponse($current_url);
				$this->response->redirect($redirect_url, $response_code);
			}
	   }
	}

}