<?php
if (version_compare(VERSION,'3.0.0.0','>=' )) {
	define('TEMPLATE_FOLDER', 'oc3');
	define('EXTENSION_BASE', 'marketplace/extension');
	define('TOKEN_NAME', 'user_token');
	define('TEMPLATE_EXTN', '');
	define('EXTN_ROUTE', 'extension/hbseo');
}else if (version_compare(VERSION,'2.2.0.0','<=' )) {
	define('TEMPLATE_FOLDER', 'oc2');
	define('EXTENSION_BASE', 'extension/hbseo');
	define('TOKEN_NAME', 'token');
	define('TEMPLATE_EXTN', '.tpl');
	define('EXTN_ROUTE', 'hbseo');
}else{
	define('TEMPLATE_FOLDER', 'oc2');
	define('EXTENSION_BASE', 'extension/extension');
	define('TOKEN_NAME', 'token');
	define('TEMPLATE_EXTN', '');
	define('EXTN_ROUTE', 'extension/hbseo');
}
define('EXTN_VERSION', '1.0.0'); 
class ControllerExtensionHbseoHbKeywordHighlight extends Controller {
	
	private $error = array(); 
	
	public function index() {   
		$data['extension_version'] = EXTN_VERSION;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->load->language(EXTN_ROUTE.'/hb_keyword_highlight');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/hbseo/hb_keyword_highlight');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_keyword_highlight', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link(EXTN_ROUTE.'/hb_keyword_highlight', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		
		$text_strings = array(
				'heading_title','text_extension',
				'button_save','button_cancel'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
	
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link(EXTENSION_BASE, TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link(EXTN_ROUTE.'/hb_keyword_highlight', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link(EXTN_ROUTE.'/hb_keyword_highlight', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link(EXTENSION_BASE, TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME] . '&type=hbseo', true);
		$data[TOKEN_NAME] = $this->session->data[TOKEN_NAME];
		$data['base_route'] = EXTN_ROUTE;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.TEMPLATE_FOLDER.'/hb_keyword_highlight'.TEMPLATE_EXTN, $data));
	}
	
	public function keywords() {   		
		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		}else{
			$search = false;
		}
		
		$this->load->language(EXTN_ROUTE.'/hb_keyword_highlight'); 
		$this->load->model('extension/hbseo/hb_keyword_highlight');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}
		
		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'search' 	=> $search
		);
		
		$data[TOKEN_NAME] 	= $this->session->data[TOKEN_NAME];	
		
		$records_total 		= $this->model_extension_hbseo_hb_keyword_highlight->getTotalrecords($data); 		
		$records 			= $this->model_extension_hbseo_hb_keyword_highlight->getrecords($data);
		
		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' 			=> $record['id'],
				'keyword' 		=> $record['keyword'],
				'date_added' 	=> date($this->language->get('date_format_short'), strtotime($record['date_added']))
			);
		}
		
		$pagination 			= new Pagination();
		$pagination->total 		= $records_total;
		$pagination->page 		= $page;
		$pagination->limit 		= $this->config->get('config_limit_admin');
		$pagination->url 		= $this->url->link(EXTN_ROUTE.'/hb_keyword_highlight/keywords', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME] . '&page={page}', true);

		$data['pagination'] 	= $pagination->render();
		$limit 					= $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.TEMPLATE_FOLDER.'/hb_keyword_highlight_keywords'.TEMPLATE_EXTN, $data));
	}
	
	public function addkeyword(){
		$keyword = trim($this->request->post['keyword']);
		
		if (empty($keyword)){
			$json['error'] = 'Please enter keyword or phrase';
		}else {
			$count = $this->db->query("SELECT count(*) as count FROM  `" . DB_PREFIX . "hb_seo_keywords` WHERE BINARY(keyword) = '".$this->db->escape($keyword)."'");
			if ($count->row['count'] == 0){
				$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_seo_keywords` (`keyword`) VALUES ('".$this->db->escape($keyword)."')");
				$json['success'] = 'Keyword Added to database';
			}else{
				$json['error'] = 'Keyword '.$keyword.' Already Exists';
			}
		}
		$this->response->setOutput(json_encode($json));	
	}
	
	public function deletekeywords(){
		$count = 0;
		if (!isset($this->request->post['selected'])){
			$json['warning'] = 'No Record Selected!';
		}else{
			foreach ($this->request->post['selected'] as $id) {
				$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_seo_keywords` WHERE `id` = '".(int)$id."'");
				$count = $count + 1;
			}
			$json['success'] = $count.' selected keywords deleted.';
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function generate(){
		if (!isset($this->request->post['tables'])){
			$json['error'] = 'No Tables Selected!';
		}else if (empty($this->request->post['tag'])){
			$json['error'] = 'You must define a tag like b or strong or mark. Defaut value : b';
		}else{
			$tag = $this->request->post['tag'];
			
			$keywords = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_seo_keywords` ORDER BY date_added DESC");
			if ($keywords->rows) {
				$count = 0;
				foreach ($keywords->rows as $row) {
					$replace_text = '<'.$tag.'>'.$row['keyword'].'</'.$tag.'>';
					foreach ($this->request->post['tables'] as $table) {
						if ($table == 'product') {
							$this->db->query("UPDATE `".DB_PREFIX."product_description` SET description = REPLACE(description, '<".$tag."><".$tag.">', '<".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."product_description` SET description = REPLACE(description, '</".$tag."></".$tag.">', '</".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."product_description` SET description = REPLACE(description, '".$this->db->escape($row['keyword'])."', '".$this->db->escape($replace_text)."')");
							$this->db->query("UPDATE `".DB_PREFIX."product_description` SET description = REPLACE(description, '<".$tag."><".$tag.">', '<".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."product_description` SET description = REPLACE(description, '</".$tag."></".$tag.">', '</".$tag.">')");
						}
						
						if ($table == 'category') {
							$this->db->query("UPDATE `".DB_PREFIX."category_description` SET description = REPLACE(description, '<".$tag."><".$tag.">', '<".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."category_description` SET description = REPLACE(description, '</".$tag."></".$tag.">', '</".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."category_description` SET description = REPLACE(description, '".$this->db->escape($row['keyword'])."', '".$this->db->escape($replace_text)."')");
							$this->db->query("UPDATE `".DB_PREFIX."category_description` SET description = REPLACE(description, '<".$tag."><".$tag.">', '<".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."category_description` SET description = REPLACE(description, '</".$tag."></".$tag.">', '</".$tag.">')");
						}
						
						if ($table == 'manufacturer') {
							$brand_description = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "manufacturer` LIKE 'brand_description'");
							if ($brand_description->num_rows){
								$this->db->query("UPDATE `".DB_PREFIX."manufacturer` SET brand_description = REPLACE(brand_description, '<".$tag."><".$tag.">', '<".$tag.">')");
								$this->db->query("UPDATE `".DB_PREFIX."manufacturer` SET brand_description = REPLACE(brand_description, '</".$tag."></".$tag.">', '</".$tag.">')");
								$this->db->query("UPDATE `".DB_PREFIX."manufacturer` SET brand_description = REPLACE(brand_description, '".$this->db->escape($row['keyword'])."', '".$this->db->escape($replace_text)."')");
								$this->db->query("UPDATE `".DB_PREFIX."manufacturer` SET brand_description = REPLACE(brand_description, '<".$tag."><".$tag.">', '<".$tag.">')");
								$this->db->query("UPDATE `".DB_PREFIX."manufacturer` SET brand_description = REPLACE(brand_description, '</".$tag."></".$tag.">', '</".$tag.">')");
							}
						}
						
						if ($table == 'information') {
							$this->db->query("UPDATE `".DB_PREFIX."information_description` SET description = REPLACE(description, '<".$tag."><".$tag.">', '<".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."information_description` SET description = REPLACE(description, '</".$tag."></".$tag.">', '</".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."information_description` SET description = REPLACE(description, '".$this->db->escape($row['keyword'])."', '".$this->db->escape($replace_text)."')");
							$this->db->query("UPDATE `".DB_PREFIX."information_description` SET description = REPLACE(description, '<".$tag."><".$tag.">', '<".$tag.">')");
							$this->db->query("UPDATE `".DB_PREFIX."information_description` SET description = REPLACE(description, '</".$tag."></".$tag.">', '</".$tag.">')");
						}
					}
				}
				$json['success'] = 'Keywords Highlight Process Completed.';
			}else{
				$json['error'] = 'No Keywords added to database list!';
			}
		}
		$this->response->setOutput(json_encode($json));
	}
	
	public function install(){
			$this->load->model('extension/hbseo/hb_keyword_highlight');
			$this->model_extension_hbseo_hb_keyword_highlight->install();
	}
	
	public function uninstall(){
			$this->load->model('extension/hbseo/hb_keyword_highlight');
			$this->model_extension_hbseo_hb_keyword_highlight->uninstall();
	}
		
	private function validate() {
		if (!$this->user->hasPermission('modify', EXTN_ROUTE.'/hb_keyword_highlight')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}	
	
}
?>