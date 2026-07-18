<?php
class ModelExtensionModuleRedirectDisabled extends Model {	
	public function redirect(){
		if(isset($_SERVER['HTTP_REFERER'])) {
			$data['referrer'] = urlencode($_SERVER['HTTP_REFERER']);
		} else{
			$data['referrer'] = NULL;
		}

		$data['ip'] = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '';
		$data['user_agent'] = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
		
		$data['type'] = '';
		$data['item_id'] = '0'; 

		if (($this->request->get['route'] == 'product/product') && isset($this->request->get['product_id'])){
			$data['type'] = 'product';
			$data['item_id'] = $this->request->get['product_id'];			
		}

		if (($this->request->get['route'] == 'product/category') && isset($this->request->get['path'])){
			$data['type'] = 'category';
			$parts = explode('_', (string)$this->request->get['path']);
			$data['item_id'] = end($parts);			
		}

		if (($this->request->get['route'] == 'information/information') && isset($this->request->get['information_id'])){
			$data['type'] = 'information';
			$data['item_id'] = $this->request->get['information_id'];		
		}

		$redirect = $this->getRedirect($data['type'], $data['item_id']);	

		if (!empty($redirect)){			
			switch ($redirect['pagetype']) {
				case 'product':
					$redirect_url = $this->url->link('product/product', 'product_id='. $redirect['redirect']);
					break;
				
				case 'category':
					$redirect_url = $this->url->link('product/category', 'path='. $redirect['redirect']);
					break;
				
				case 'information':
					$redirect_url = $this->url->link('information/information', 'information_id='. $redirect['redirect']);
					break;
				
				case 'custom':
					$redirect_url = $this->url->link($redirect['redirect']);
					break;
				
				default:
					$redirect_url = '';
					break;
			}

			$this->addLog($data);
			if (!empty($redirect_url)){
				$this->response->redirect($redirect_url, $redirect['redirect_type']);
			}			
		}
		
	}

	public function getRedirect($type, $item_id){
		switch ($type) {
			case 'product':
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "redirect_disabled_product` WHERE product_id = '".(int)$item_id."' LIMIT 1");
				break;

			case 'category':
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "redirect_disabled_category` WHERE category_id = '".(int)$item_id."' LIMIT 1");
				break;

			case 'information':
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "redirect_disabled_information` WHERE information_id = '".(int)$item_id."' LIMIT 1");
				break;
		}

		if (!empty($query->row)){
			return $query->row;
		}
	}

	public function addLog($data){
		$this->db->query("INSERT INTO `" . DB_PREFIX . "redirect_disabled_logs` (`type`, `item_id`, `referrer`, `user_agent`, `ip`) VALUES ('".$this->db->escape($data['type'])."', '".(int)$data['item_id']."', '".$this->db->escape($data['referrer'])."', '".$this->db->escape($data['user_agent'])."', '".$this->db->escape($data['ip'])."')");

		switch ($data['type']) {
			case 'product':
				$this->db->query("UPDATE `" . DB_PREFIX . "redirect_disabled_product` SET redirect_hits =  redirect_hits + 1 WHERE product_id = '".(int)$data['item_id']."'");
				break;
			
			case 'category':
				$this->db->query("UPDATE `" . DB_PREFIX . "redirect_disabled_category` SET redirect_hits =  redirect_hits + 1 WHERE category_id = '".(int)$data['item_id']."'");
				break;

			case 'information':
				$this->db->query("UPDATE `" . DB_PREFIX . "redirect_disabled_information` SET redirect_hits =  redirect_hits + 1 WHERE information_id = '".(int)$data['item_id']."'");
				break;
		}
		
	}

}