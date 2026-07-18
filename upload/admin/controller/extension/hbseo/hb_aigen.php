<?php
class ControllerExtensionHbseoHbAigen extends Controller {
	protected $registry;
	private $error = array(); 
	
	public function __construct($registry) {
		$this->registry = $registry;
		$this->hb_extension_version	= '3.1.4';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/ai-generator';

		$this->load->model('extension/hbseo/hb_aigen');		
		$this->load->language('extension/hbseo/hb_aigen');
	}
	
	public function index() {
		$data['extension_version'] =  $this->hb_extension_version;
		
		$data['store_id'] = 0;
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_aigen', $this->request->post, $data['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/hbseo/hb_aigen', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
	
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/hbseo/hb_aigen', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link('extension/hbseo/hb_aigen', 'user_token=' . $this->session->data['user_token'].'&store_id='.$data['store_id'], true);		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=hbseo', true);
		$data['clear']	= $this->url->link('extension/hbseo/hb_aigen/clear_logs', 'user_token=' . $this->session->data['user_token'], true);

		$data['user_token'] = $this->session->data['user_token'];
		$data['doc_link']	= $this->doc_link;		
		
		$data['onpage_extension'] = $this->model_extension_hbseo_hb_aigen->isExtensionInstalled('hb_onpage');

		$store_info = $this->model_setting_setting->getSetting('hb_aigen', $data['store_id']);

		//settings
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];	
			$data['hb_aigen_product_template'][$language_id] 	= isset($store_info['hb_aigen_product_template'.$language_id]) ? $store_info['hb_aigen_product_template'.$language_id] : 'Create content for an eCommerce product using the following details:

- Product Name: {name}

The language of the content should be in '.$language['name'].'.';
			$data['hb_aigen_category_template'][$language_id] 	= isset($store_info['hb_aigen_category_template'.$language_id]) ? $store_info['hb_aigen_category_template'.$language_id] : 'Create content for an eCommerce category page using the following details:

- Category Name: {name}

The language of the content should be in '.$language['name'].'.';
			$data['hb_aigen_manufacturer_template'][$language_id] 	= isset($store_info['hb_aigen_manufacturer_template'.$language_id]) ? $store_info['hb_aigen_manufacturer_template'.$language_id] : 'Create content for an eCommerce manufacturer page where Manufacturer Name is {name}. The language of the content should be in '.$language['name'].'.';
			$data['hb_aigen_information_template'][$language_id] 	= isset($store_info['hb_aigen_information_template'.$language_id]) ? $store_info['hb_aigen_information_template'.$language_id] : 'Create content for an eCommerce informational page using the following details:

- Page Title: {name}
The language of the content should be in '.$language['name'].'.';
		}
	
		$data['hb_aigen_status']	= isset($store_info['hb_aigen_status']) ? $store_info['hb_aigen_status'] : '';
		$data['hb_aigen_api']		= isset($store_info['hb_aigen_api']) ? $store_info['hb_aigen_api'] : '';
		$data['hb_aigen_gpt_model']		= isset($store_info['hb_aigen_gpt_model']) ? $store_info['hb_aigen_gpt_model'] : 'gpt-3.5-turbo';

		$data['sections'] = array(
			'description' 		=> $this->language->get('text_description'),
			'meta_title' 		=> $this->language->get('text_meta_title'),
			'meta_description' 	=> $this->language->get('text_meta_description'),
			'meta_keyword' 		=> $this->language->get('text_meta_keyword'),
			'tag' 				=> $this->language->get('text_product_tags'),
		);

		if ($data['onpage_extension']) {
			$data['sections']['h1'] = $this->language->get('text_h1');
			$data['sections']['h2'] = $this->language->get('text_h2');
		}

		$data['hb_aigen_sections']   	= isset($store_info['hb_aigen_sections']) ? $store_info['hb_aigen_sections'] : [];
		$data['hb_aigen_cron_limit'] 	= isset($store_info['hb_aigen_cron_limit']) ? $store_info['hb_aigen_cron_limit'] : 10;
		$data['hb_aigen_cron_key'] 		= isset($store_info['hb_aigen_cron_key']) ? $store_info['hb_aigen_cron_key'] : md5(rand());
		$data['hb_aigen_logs'] 			= isset($store_info['hb_aigen_logs']) ? $store_info['hb_aigen_logs'] : '';	
		$data['hb_aigen_one_language'] 	= isset($store_info['hb_aigen_one_language']) ? $store_info['hb_aigen_one_language'] : '';
		$data['hb_aigen_language_id'] 	= isset($store_info['hb_aigen_language_id']) ? $store_info['hb_aigen_language_id'] : $this->config->get('config_language_id');
		$data['hb_aigen_overwrite'] 	= isset($store_info['hb_aigen_overwrite']) ? $store_info['hb_aigen_overwrite'] : '';
		$data['hb_aigen_simulate'] 		= isset($store_info['hb_aigen_simulate']) ? $store_info['hb_aigen_simulate'] : '';

		$data['hb_aigen_description_max_length'] = isset($store_info['hb_aigen_description_max_length']) ? $store_info['hb_aigen_description_max_length'] : 500;

		$data['hb_aigen_cron'] = 'wget --quiet --delete-after "'.HTTPS_CATALOG.'index.php?route=extension/module/hb_aigen/cron&authkey='.$data['hb_aigen_cron_key'].'"';

		$data['header'] 		= $this->load->controller('common/header');
		$data['column_left'] 	= $this->load->controller('common/column_left');
		$data['footer'] 		= $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_aigen', $data));
	}

	public function product() {  
		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$limit = ($this->config->get('config_limit_admin')) ? $this->config->get('config_limit_admin') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'search'		=> strtolower($search),
		);

		$data['user_token'] = $this->session->data['user_token'];	
		
		$records_total = $this->model_extension_hbseo_hb_aigen->getTotalProducts($data); 		
		$records = $this->model_extension_hbseo_hb_aigen->getProducts($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'item_id'			=> $record['product_id'],
				'name'				=> !empty($record['name']) ? html_entity_decode($record['name'], ENT_QUOTES, 'UTF-8') : '',
				'model'				=> !empty($record['model']) ? html_entity_decode($record['model'], ENT_QUOTES, 'UTF-8') : '',
				'meta_title'		=> !empty($record['meta_title']) ? html_entity_decode($record['meta_title'], ENT_QUOTES, 'UTF-8') : '',
				'meta_description'	=> !empty($record['meta_description']) ? html_entity_decode($record['meta_description'], ENT_QUOTES, 'UTF-8') : '',
				'meta_keyword'		=> !empty($record['meta_keyword']) ? html_entity_decode($record['meta_keyword'], ENT_QUOTES, 'UTF-8') : '',
				'language_id'		=> $record['language_id'],
				'edit'				=> $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token']. '&product_id='.$record['product_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/hbseo/hb_aigen/product', 'user_token=' . $this->session->data['user_token'] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'product';

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_aigen_items', $data));
	}

	public function category() {  
		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$limit = ($this->config->get('config_limit_admin')) ? $this->config->get('config_limit_admin') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'search'		=> strtolower($search),
		);

		$data['user_token'] = $this->session->data['user_token'];	
		
		$records_total = $this->model_extension_hbseo_hb_aigen->getTotalCategories($data); 		
		$records = $this->model_extension_hbseo_hb_aigen->getCategories($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'item_id'			=> $record['category_id'],
				'name'				=> !empty($record['name']) ? html_entity_decode($record['name'], ENT_QUOTES, 'UTF-8') : '',
				'meta_title'		=> !empty($record['meta_title']) ? html_entity_decode($record['meta_title'], ENT_QUOTES, 'UTF-8') : '',
				'meta_description'	=> !empty($record['meta_description']) ? html_entity_decode($record['meta_description'], ENT_QUOTES, 'UTF-8') : '',
				'meta_keyword'		=> !empty($record['meta_keyword']) ? html_entity_decode($record['meta_keyword'], ENT_QUOTES, 'UTF-8') : '',
				'language_id'		=> $record['language_id'],
				'edit'				=> $this->url->link('catalog/category/edit', 'user_token=' . $this->session->data['user_token']. '&category_id='.$record['category_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/hbseo/hb_aigen/category', 'user_token=' . $this->session->data['user_token'] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'category';

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_aigen_items', $data));
	}

	public function manufacturer() {  
		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$limit = ($this->config->get('config_limit_admin')) ? $this->config->get('config_limit_admin') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'search'		=> strtolower($search),
		);

		$data['user_token'] = $this->session->data['user_token'];	
		
		$records_total = $this->model_extension_hbseo_hb_aigen->getTotalManufacturers($data); 		
		$records = $this->model_extension_hbseo_hb_aigen->getManufacturers($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'item_id'			=> $record['manufacturer_id'],
				'name'				=> !empty($record['name']) ? html_entity_decode($record['name'], ENT_QUOTES, 'UTF-8') : '',
				'meta_title'		=> !empty($record['meta_title']) ? html_entity_decode($record['meta_title'], ENT_QUOTES, 'UTF-8') : '',
				'meta_description'	=> !empty($record['meta_description']) ? html_entity_decode($record['meta_description'], ENT_QUOTES, 'UTF-8') : '',
				'meta_keyword'		=> !empty($record['meta_keyword']) ? html_entity_decode($record['meta_keyword'], ENT_QUOTES, 'UTF-8') : '',
				'language_id'		=> $record['language_id'],
				'edit'				=> $this->url->link('catalog/manufacturer/edit', 'user_token=' . $this->session->data['user_token']. '&manufacturer_id='.$record['manufacturer_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/hbseo/hb_aigen/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'manufacturer';
		
		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_aigen_items', $data));
	}

	public function information() {
		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$limit = ($this->config->get('config_limit_admin')) ? $this->config->get('config_limit_admin') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'search'		=> strtolower($search),
		);

		$data['user_token'] = $this->session->data['user_token'];	
		
		$records_total = $this->model_extension_hbseo_hb_aigen->getTotalInformations($data); 		
		$records = $this->model_extension_hbseo_hb_aigen->getInformations($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'item_id'			=> $record['information_id'],
				'name'				=> !empty($record['title']) ? html_entity_decode($record['title'], ENT_QUOTES, 'UTF-8') : '',
				'meta_title'		=> !empty($record['meta_title']) ? html_entity_decode($record['meta_title'], ENT_QUOTES, 'UTF-8') : '',
				'meta_description'	=> !empty($record['meta_description']) ? html_entity_decode($record['meta_description'], ENT_QUOTES, 'UTF-8') : '',
				'meta_keyword'		=> !empty($record['meta_keyword']) ? html_entity_decode($record['meta_keyword'], ENT_QUOTES, 'UTF-8') : '',
				'language_id'		=> $record['language_id'],
				'edit'				=> $this->url->link('catalog/information/edit', 'user_token=' . $this->session->data['user_token']. '&information_id='.$record['information_id'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/hbseo/hb_aigen/information', 'user_token=' . $this->session->data['user_token'] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'information';

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_aigen_items', $data));
	}

	public function items() {
		$page 	= (isset($this->request->get['page']))? $this->request->get['page'] : 1;
		$search = (isset($this->request->get['search']))? $this->request->get['search'] : '';
		
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$language_map = array_column($languages, 'name', 'language_id');

		$limit = ($this->config->get('config_limit_admin')) ? $this->config->get('config_limit_admin') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'search'		=> strtolower($search),
		);

		$data['user_token'] = $this->session->data['user_token'];	
		
		$records_total = $this->model_extension_hbseo_hb_aigen->getTotalItems($data); 		
		$records = $this->model_extension_hbseo_hb_aigen->getItems($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'id'			=> $record['id'],
				'type'			=> $record['type'],
				'item_id'		=> $record['item_id'],
				'element'		=> $record['element'],
				'language_id'	=> $record['language_id'],
				'language'		=> $language_map[$record['language_id']],
				'value'			=> !empty($record['value']) ? html_entity_decode($record['value'], ENT_QUOTES, 'UTF-8') : '',
				'previous_value'=> !empty($record['previous_value']) ? html_entity_decode($record['previous_value'], ENT_QUOTES, 'UTF-8') : '',
				'date_added'	=> date($this->language->get('date_format_short'), strtotime($record['date_added']))
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/hbseo/hb_aigen/items', 'user_token=' . $this->session->data['user_token'] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_aigen_items_generated', $data));
	}

	public function accept_items(){
		$json = [];

		$mode = (isset($this->request->get['mode'])) ? $this->request->get['mode'] : 'all';
		$selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];

		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if ($mode == 'selected' && empty($selected)){
			$json['error'] = $this->language->get('error_no_record_selected');
		}

		if (!$json){
			if ($mode == 'selected') {
				foreach ($selected as $item) {				
					if ($this->model_extension_hbseo_hb_aigen->acceptItem($item)) {
						$json['success'] = $this->language->get('success_accept');
					} else {
						$json['error'] = $this->language->get('error_accept_failed');
					}
				}
			}else{
				if ($this->model_extension_hbseo_hb_aigen->acceptAllItems()) {
					$json['success'] = $this->language->get('success_accept_all');
				} else {
					$json['error'] = $this->language->get('error_accept_failed');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function restore_items(){
		$json = [];

		$mode = (isset($this->request->get['mode'])) ? $this->request->get['mode'] : 'all';
		$selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];

		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if ($mode == 'selected' && empty($selected)){
			$json['error'] = $this->language->get('error_no_record_selected');
		}

		if (!$json){
			if ($mode == 'selected') {
				foreach ($selected as $item) {				
					if ($this->model_extension_hbseo_hb_aigen->restoreItem($item)) {
						$json['success'] = $this->language->get('success_restore');
					} else {
						$json['error'] = $this->language->get('error_restore_failed');
					}
				}
			}else{
				if ($this->model_extension_hbseo_hb_aigen->restoreAllItems()) {
					$json['success'] = $this->language->get('success_restore_all');
				} else {
					$json['error'] = $this->language->get('error_restore_failed');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete_items(){
		$json = [];

		$mode = (isset($this->request->get['mode'])) ? $this->request->get['mode'] : 'all';
		$selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];

		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if ($mode == 'selected' && empty($selected)){
			$json['error'] = $this->language->get('error_no_record_selected');
		}

		if (!$json){
			if ($mode == 'selected') {
				foreach ($selected as $item) {				
					$this->model_extension_hbseo_hb_aigen->deleteItem($item);
					$json['success'] = $this->language->get('success_delete');				
				}
			} else {
				$this->model_extension_hbseo_hb_aigen->deleteAllItems();
				$json['success'] = $this->language->get('success_delete_all');
			}			
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function logs(){
		if (!file_exists(DIR_LOGS)) {
			mkdir(DIR_LOGS, 0777, true);
		}

		$file = DIR_LOGS . 'hb_aigen.txt';
		if (file_exists($file)) {
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}else{
			$data['log'] = '';
		}

		$this->response->setOutput($this->load->view('extension/hbseo/oc3/hb_aigen_logs', $data));
	}

	public function clear_logs() {
		if (!$this->validate()) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'hb_aigen.txt';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] =  $this->language->get('text_success_logs');
		}

		$this->response->redirect($this->url->link('extension/hbseo/hb_aigen', 'user_token=' . $this->session->data['user_token'], true));
	}
			
	public function install(){
		$this->model_extension_hbseo_hb_aigen->install();
	}
	
	public function uninstall(){
		$this->model_extension_hbseo_hb_aigen->uninstall();
	}
	
	public function update(){
		$this->model_extension_hbseo_hb_aigen->update();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/hbseo/hb_aigen')) {
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