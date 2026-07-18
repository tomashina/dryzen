<?php
class ControllerExtensionHbseoHbBrokenlinks extends Controller {
	protected $registry;
	private $error = array(); 
	
	public function __construct($registry) {
		$this->registry = $registry;
		if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$this->hb_template_folder 		= 'oc3';
			$this->hb_extension_base 		= 'marketplace/extension';
			$this->hb_token_name 			= 'user_token';
			$this->hb_template_extension 	= '';
			$this->hb_extension_route 		= 'extension/hbseo';
		}else if (version_compare(VERSION,'2.2.0.0','<=' )) {
			$this->hb_template_folder 		= 'oc2';
			$this->hb_extension_base 		= 'extension/hbseo';
			$this->hb_token_name 			= 'token';
			$this->hb_template_extension 	= '.tpl';
			$this->hb_extension_route 		= 'hbseo';
		}else{
			$this->hb_template_folder 		= 'oc2';
			$this->hb_extension_base 		= 'extension/extension';
			$this->hb_token_name 			= 'token';
			$this->hb_template_extension 	= '';
			$this->hb_extension_route 		= 'extension/hbseo';
		}
		
		$this->hb_extension_version	= '3.5.3';
		$this->doc_link = 'https://www.huntbee.com/documentation/docs/seo-broken-link-manager/';

		$this->load->model('extension/hbseo/hb_brokenlinks');
		$this->load->language($this->hb_extension_route.'/hb_brokenlinks');
	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_brokenlinks', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_brokenlinks', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$text_strings = array(
				'heading_title','text_extension',
				'tab_broken','tab_redirect', 'tab_setup','tab_templates','tab_tools','tab_keyword','tab_replace',
				'column_enable_page','column_page_designer','column_smart_url','column_keyword_url','column_replacer','column_default_url','column_redirect_type','column_query_exclude','column_error_exclude','column_ignore_ip','column_ignore_agent',
				'column_auto_delete',
				'text_error_url','text_redirect_url','text_redirect_type','text_redirect_author','text_error_url_help',
				'tool_redirect_update','tool_assign_default','tool_reset','tool_type_update',
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
			'href'      => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_brokenlinks', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);

		$data['doc_link']	= $this->doc_link;
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_brokenlinks', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data['delete'] = $this->url->link($this->hb_extension_route.'/hb_brokenlinks/delete', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);
		
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		
		
		$store_info = $this->model_setting_setting->getSetting('hb_brokenlinks', $this->request->get['store_id']);
		
		//Broken links
		$data['hb_brokenlinks_sauthor'] = isset($store_info['hb_brokenlinks_sauthor'])?$store_info['hb_brokenlinks_sauthor']:'0';
		$data['hb_brokenlinks_sredirect'] = isset($store_info['hb_brokenlinks_sredirect'])?$store_info['hb_brokenlinks_sredirect']:'0';
		$data['hb_brokenlinks_ssort'] = isset($store_info['hb_brokenlinks_ssort'])?$store_info['hb_brokenlinks_ssort']:'date_modified';
		$data['hb_brokenlinks_sorder'] = isset($store_info['hb_brokenlinks_sorder'])?$store_info['hb_brokenlinks_sorder']:'DESC';
		
		
		//settings
		$data['hb_brokenlinks_excludequery'] = isset($store_info['hb_brokenlinks_excludequery'])?$store_info['hb_brokenlinks_excludequery']:'sort,order';
		$data['hb_brokenlinks_excludeterms'] = isset($store_info['hb_brokenlinks_excludeterms'])?$store_info['hb_brokenlinks_excludeterms']:'robots.txt,module/,favicon.ico';
		$data['hb_brokenlinks_ignoreip'] = isset($store_info['hb_brokenlinks_ignoreip'])?$store_info['hb_brokenlinks_ignoreip']:'';
		$data['hb_brokenlinks_ignoreagents'] = isset($store_info['hb_brokenlinks_ignoreagents'])?$store_info['hb_brokenlinks_ignoreagents']:'';
		$data['hb_brokenlinks_defaulturl'] = isset($store_info['hb_brokenlinks_defaulturl'])?$store_info['hb_brokenlinks_defaulturl']:'';
		$data['hb_brokenlinks_smarturl'] = isset($store_info['hb_brokenlinks_smarturl'])?$store_info['hb_brokenlinks_smarturl']:'';
		$data['hb_brokenlinks_keywordurl'] = isset($store_info['hb_brokenlinks_keywordurl'])?$store_info['hb_brokenlinks_keywordurl']:'';
		$data['hb_brokenlinks_replacer'] = isset($store_info['hb_brokenlinks_replacer'])?$store_info['hb_brokenlinks_replacer']:'';
		$data['hb_brokenlinks_rtype'] = isset($store_info['hb_brokenlinks_rtype'])?$store_info['hb_brokenlinks_rtype']:'301';
		
		$data['hb_brokenlinks_adel_count'] = isset($store_info['hb_brokenlinks_adel_count'])?$store_info['hb_brokenlinks_adel_count']:'4';
		$data['hb_brokenlinks_adel_days'] = isset($store_info['hb_brokenlinks_adel_days'])?$store_info['hb_brokenlinks_adel_days']:'15';
		
		$data['hb_brokenlinks_enablepage'] = isset($store_info['hb_brokenlinks_enablepage'])?$store_info['hb_brokenlinks_enablepage']:'';
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($data['languages'] as $language){
	 		$language_id = $language['language_id'];	
			$data['hb_brokenlinks_page'][$language_id] =  isset($store_info['hb_brokenlinks_page'.$language_id])?$store_info['hb_brokenlinks_page'.$language_id]:'';
		}
					
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_brokenlinks'.$this->hb_template_extension, $data));

	}
	
	public function brokenlinks() {		
		$store_id = (int)$this->request->get['store_id'];
		
		if (isset($this->request->get['search'])) {
			$search_link = $this->request->get['search'];
		}else{
			$search_link = false;
		}
		
		 
		$this->load->model('setting/setting');
		
		$store_info = $this->model_setting_setting->getSetting('hb_brokenlinks', $this->request->get['store_id']);
		
		$data['hb_brokenlinks_sauthor'] 	= isset($store_info['hb_brokenlinks_sauthor'])?$store_info['hb_brokenlinks_sauthor']:'0';
		$data['hb_brokenlinks_sredirect'] 	= isset($store_info['hb_brokenlinks_sredirect'])?$store_info['hb_brokenlinks_sredirect']:'0';
		$data['hb_brokenlinks_ssort'] 		= isset($store_info['hb_brokenlinks_ssort'])?$store_info['hb_brokenlinks_ssort']:'date_modified';
		$data['hb_brokenlinks_sorder'] 		= isset($store_info['hb_brokenlinks_sorder'])?$store_info['hb_brokenlinks_sorder']:'DESC';
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		
		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}
		
		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'sauthor' 	=> $data['hb_brokenlinks_sauthor'],
			'sredirect' => $data['hb_brokenlinks_sredirect'],
			'ssort' 	=> $data['hb_brokenlinks_ssort'],
			'sorder' 	=> $data['hb_brokenlinks_sorder'],
			'search_link' => $search_link,
			'store_id'	=> $store_id
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];	
		
		$reports_total = $this->model_extension_hbseo_hb_brokenlinks->getTotalrecords($data); 		
		$records = $this->model_extension_hbseo_hb_brokenlinks->getrecords($data);

		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' 			=> $record['id'],
				'error' 		=> urldecode($record['error']),
				'redirect' 		=> urldecode($record['redirect']),
				'type' 			=> $record['type'],
				'author' 		=> $this->model_extension_hbseo_hb_brokenlinks->authorReference($record['author']),
				'hits' 			=> $record['hits'],
				'redirect_hits' => $record['redirect_hits'],
				'date_added' 	=> date($this->language->get('date_format_short'), strtotime($record['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($record['date_modified'])),
				'selected404'   => isset($this->request->post['selected404']) && in_array($record['id'], $this->request->post['selected404'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_brokenlinks/brokenlinks', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id='.$store_id.'&page={page}'.$url, true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_brokenlinks_records'.$this->hb_template_extension, $data));
	}
	
	public function redirects() {  
		$store_id = (int)$this->request->get['store_id'];
		
		if (isset($this->request->get['search'])) {
			$search_link = $this->request->get['search'];
		}else{
			$search_link = false;
		}
		 
		$this->load->model('setting/setting');
		
		$store_info = $this->model_setting_setting->getSetting('hb_brokenlinks', $this->request->get['store_id']);
		
		$data['hb_brokenlinks_sauthor'] = isset($store_info['hb_brokenlinks_sauthor'])?$store_info['hb_brokenlinks_sauthor']:'0';
		$data['hb_brokenlinks_sredirect'] = isset($store_info['hb_brokenlinks_sredirect'])?$store_info['hb_brokenlinks_sredirect']:'0';
		$data['hb_brokenlinks_ssort'] = isset($store_info['hb_brokenlinks_ssort'])?$store_info['hb_brokenlinks_ssort']:'date_modified';
		$data['hb_brokenlinks_sorder'] = isset($store_info['hb_brokenlinks_sorder'])?$store_info['hb_brokenlinks_sorder']:'DESC';
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		
		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}
		
		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'sauthor' => 1,
			'sredirect' => 0,
			'ssort' => $data['hb_brokenlinks_ssort'],
			'sorder' => $data['hb_brokenlinks_sorder'],
			'search_link' => $search_link,
			'store_id'=> $store_id
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];	
		
		$reports_total = $this->model_extension_hbseo_hb_brokenlinks->getTotalrecords($data); 		
		$records = $this->model_extension_hbseo_hb_brokenlinks->getrecords($data);

		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' => $record['id'],
				'error' => urldecode($record['error']),
				'redirect' => urldecode($record['redirect']),
				'type' => $record['type'],
				'author' => $record['author'],
				'hits' => $record['hits'],
				'redirect_hits' => $record['redirect_hits'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($record['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($record['date_modified'])),
				'selected200'      => isset($this->request->post['selected200']) && in_array($record['id'], $this->request->post['selected200'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_brokenlinks/pageredirects', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id='.$store_id.'&page={page}'.$url, true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_brokenlinks_pageredirects'.$this->hb_template_extension, $data));
	}
	
	public function addlinks(){		
		$links = $this->request->post['links'];
		$redirect_url = $this->request->post['redirect'];
		$response = $this->request->post['response'];
		$type = $this->request->post['type'];
		
		if ($type == 0){
			$json['error'] = $this->language->get('error_redirect_type');
		}else{
			$links = explode(',',$links);
		
			foreach ($links as $link){
				$link = trim(html_entity_decode($link));
				$redirect_url = trim($redirect_url);
				
				if (!empty($link) and !empty($redirect_url)){
					$this->model_extension_hbseo_hb_brokenlinks->insertRecord(urlencode($link),urlencode($redirect_url),$response,$type,$this->request->get['store_id']);
					$json['success'] = $this->language->get('text_insert_success');
				}else{
					$json['error'] = $this->language->get('error_improper_data');
				}
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function keywords(){
		$store_id = (int)$this->request->get['store_id'];
		
		$records = $this->model_extension_hbseo_hb_brokenlinks->getkeywords($store_id);

		$data['records'] = array();
		if ($records) {
			foreach ($records as $record) {
				$data['records'][] = array(
					'id' 			=> $record['id'],
					'keyword' 		=> urldecode($record['keyword']),
					'redirect_url' 	=> urldecode($record['redirect_url']),
					'date_added' 	=> date($this->language->get('date_format_short'), strtotime($record['date_added']))
				);
			}
		}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_brokenlinks_keywords'.$this->hb_template_extension, $data));
	}
	
	public function replacers(){
		$store_id = (int)$this->request->get['store_id'];
		
		$records = $this->model_extension_hbseo_hb_brokenlinks->getUrlReplacers($store_id);

		$data['records'] = array();
		if ($records) {
			foreach ($records as $record) {
				$data['records'][] = array(
					'id' 			=> $record['id'],
					'match' 		=> urldecode($record['match']),
					'replace' 		=> urldecode($record['replace']),
					'date_added' 	=> date($this->language->get('date_format_short'), strtotime($record['date_added']))
				);
			}
		}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_brokenlinks_replacer'.$this->hb_template_extension, $data));

	}
	
	public function add_keyword(){		
		$keyword = trim($this->request->post['keyword']);
		$redirect_url = trim($this->request->post['redirect']);
		
		if (!empty($keyword) and !empty($redirect_url)){
			$this->model_extension_hbseo_hb_brokenlinks->insertKeyword(urlencode($keyword),urlencode($redirect_url),$this->request->get['store_id']);
			$json['success'] = $this->language->get('text_entry_added');
		}else{
			$json['error'] = $this->language->get('error_improper_data');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function add_replacer(){
		$matchsting = trim($this->request->post['matchsting']);
		$replacestring = trim($this->request->post['replacestring']);
		
		if (!empty($matchsting) and !empty($replacestring)){
			if (strpos($replacestring,$matchsting) !== false){
				$json['error'] = $this->language->get('error_same_string');
			}else{
				$this->model_extension_hbseo_hb_brokenlinks->insertReplacer(urlencode($matchsting),urlencode($replacestring),$this->request->get['store_id']);
				$json['success'] = $this->language->get('text_entry_added');
			}
		}else{
			$json['error'] = $this->language->get('error_improper_data');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete_keyword(){
		$id = $this->request->post['id'];
		$this->db->query("DELETE FROM `" . DB_PREFIX . "error_keyword` WHERE `id` = '".(int)$id."'");
		$json['success'] = $this->language->get('text_entry_delete');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete_replacer(){
		$id = $this->request->post['id'];
		$this->db->query("DELETE FROM `" . DB_PREFIX . "error_replacer` WHERE `id` = '".(int)$id."'");
		$json['success'] = $this->language->get('text_entry_delete');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete(){
		if (!isset($this->request->post['selected404']) and !isset($this->request->post['selected200'])){
			$json['error'] = $this->language->get('text_no_record_selected');
		}else{
			$count404 = 0;
			$count200 = 0;
			$json['success'] = '';
			if (isset($this->request->post['selected404'])){
				foreach ($this->request->post['selected404'] as $id) {
					$this->model_extension_hbseo_hb_brokenlinks->deleteRecord($id);
					$count404 = $count404 + 1;
				}
				$json['success'] .= sprintf($this->language->get('text_delete_broken_link'), $count404);
			}
			
			if (isset($this->request->post['selected200'])){
				foreach ($this->request->post['selected200'] as $id) {
					$this->model_extension_hbseo_hb_brokenlinks->deleteRecord($id);
					$count200 = $count200 + 1;
				}
				$json['success'] .= '<br>'.sprintf($this->language->get('text_delete_broken_link'), $count200);;
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function update_redirect(){
		$id = $this->request->post['id'];
		$redirect = urlencode($this->request->post['redirect']);
		 
		$this->load->model('setting/setting');
		
		$store_info = $this->model_setting_setting->getSetting('hb_brokenlinks', $this->request->get['store_id']);
		
		$hb_brokenlinks_rtype = isset($store_info['hb_brokenlinks_rtype'])?$store_info['hb_brokenlinks_rtype']:'301';
		
		if (!$this->model_extension_hbseo_hb_brokenlinks->isSameRedirect($redirect,$id)){
			if ($this->model_extension_hbseo_hb_brokenlinks->checkRedirect($redirect) == true){
				$this->model_extension_hbseo_hb_brokenlinks->updateRecord($id,$redirect,$hb_brokenlinks_rtype);	
				$json['success'] = sprintf($this->language->get('text_redirect_updated'),urldecode($redirect));
			}else {
				$json['error'] = 'Redirect URL cannot be a Broken URL';
			}
		}else{
			$json['sameurl'] = 'No change in Redirect URL';
		}
				
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function referrers() {	 	
		if (isset($this->request->get['id'])) {
			$id = $this->request->get['id'];
		}
		
		$data['records'] = $this->model_extension_hbseo_hb_brokenlinks->getReferrers($id);
		$data['referrers'] = array();

		if ($data['records']) {
			foreach ($data['records'] as $record) {
				$data['referrers'][] = array(
					'referrer' 		=> (!empty($record['referrer']))? urldecode($record['referrer']) : ' - ',
					'user_agent' 	=> $record['user_agent'],
					'ip' 			=> $record['ip'],
					'datetime' 		=> $record['date_added']
				);
			}
		}

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_brokenlinks_referrers'.$this->hb_template_extension, $data));
	}
	
	public function tool_resetall() {
		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : 0;

		$this->model_extension_hbseo_hb_brokenlinks->deleteAllRecords($store_id);

		$this->session->data['success'] = $this->language->get('text_truncated');
		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_brokenlinks', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$this->request->get['store_id'], true));
	}
	
	public function tool_bulkredirectupdate() {
		$old = $this->request->get['old'];
		$new = $this->request->get['new'];
		
		if (isset($old) && isset($new)){
			$query = $this->db->query("UPDATE `" . DB_PREFIX . "error` SET `redirect` = '" . $this->db->escape(urlencode($new)) . "' WHERE `redirect` LIKE '%" . $this->db->escape(urlencode($old)) . "%'");
			$this->session->data['success'] = $this->language->get('text_updated');
		}
		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_brokenlinks', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$this->request->get['store_id'], true));
	}
	
	public function tool_bulktype() {
		$old = $this->request->get['old'];
		$new = $this->request->get['new'];
		
		if (isset($old) && isset($new)){
			$query = $this->db->query("UPDATE `" . DB_PREFIX . "error` SET `type` = '".(int)$new."' WHERE `type` = '".(int)$old."'");
			$this->session->data['success'] = 'UPDATED SUCCESSFULLY';
		}
		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_brokenlinks', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$this->request->get['store_id'], true));
	}
	
	public function tool_bulkdefault() {
		$this->load->model('setting/setting');
		$store_info = $this->model_setting_setting->getSetting('hb_brokenlinks', $this->request->get['store_id']);
		
		$value = isset($store_info['hb_brokenlinks_defaulturl'])?$store_info['hb_brokenlinks_defaulturl']:'';
		$type = isset($store_info['hb_brokenlinks_rtype'])?$store_info['hb_brokenlinks_rtype']:'301';
		
		$query = $this->db->query("UPDATE `" . DB_PREFIX . "error` SET `redirect` = '".$this->db->escape(urlencode($value))."', `type` = '".(int)$type."' WHERE `redirect` IS NULL or trim(redirect) = '' AND store_id = '".(int)$this->request->get['store_id']."'");
		$this->session->data['success'] = 'UPDATED SUCCESSFULLY';
		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_brokenlinks', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$this->request->get['store_id'], true));
	}
	
	public function install() { 
		$this->model_extension_hbseo_hb_brokenlinks->install();
	}
	
	public function uninstall() { 
		$this->model_extension_hbseo_hb_brokenlinks->uninstall();
	}

	public function update(){
		$this->model_extension_hbseo_hb_brokenlinks->update();
		return true;
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_brokenlinks')) {
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