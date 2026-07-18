<?php
class ControllerExtensionHbseoHbTranslate extends Controller {
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
		
		$this->hb_extension_version	= '3.2.0';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/bulktranslate-pro/';

		$this->load->model('extension/hbseo/hb_translate');		
		$this->load->language($this->hb_extension_route.'/hb_translate');

		$this->master_language_id = ($this->config->get('hb_translate_master_language'))? $this->config->get('hb_translate_master_language') : $this->config->get('config_language_id');

	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_translate', $this->request->post);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_translate', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true));
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
			'href'      => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_translate', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_translate', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;

		$data['clear']	= $this->url->link($this->hb_extension_route.'/hb_translate/clear_logs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);

		$data['doc_link']	= $this->doc_link;
		
		$data['onpage_extension'] = $this->model_extension_hbseo_hb_translate->isExtensionInstalled('hb_onpage');

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['total_languages'] = count($data['languages']);
		
		$store_info = $this->model_setting_setting->getSetting('hb_translate');
		
		//settings
		$data['hb_translate_api']				= isset($store_info['hb_translate_api'])? $store_info['hb_translate_api'] : '';
		$data['hb_translate_master_language']	= isset($store_info['hb_translate_master_language'])? $store_info['hb_translate_master_language'] : $this->config->get('config_language_id');
		$data['hb_translate_batch_count']		= isset($store_info['hb_translate_batch_count'])? $store_info['hb_translate_batch_count'] : '5';

		$data['hb_translate_cron_enable']	= isset($store_info['hb_translate_cron_enable'])? $store_info['hb_translate_cron_enable'] : '';
		$data['hb_translate_cron_key']		= isset($store_info['hb_translate_cron_key'])? $store_info['hb_translate_cron_key'] : md5(rand());

		$data['cron_url'] = HTTPS_CATALOG.'index.php?route=extension/module/hb_translate/cron&key='.$data['hb_translate_cron_key'];
		$data['cron_command'] = 'wget --quiet --delete-after "'.$data['cron_url'].'"';	

		$data['hb_translate_fields_product']		= isset($store_info['hb_translate_fields_product'])? $store_info['hb_translate_fields_product'] : [];
		$data['hb_translate_fields_category']		= isset($store_info['hb_translate_fields_category'])? $store_info['hb_translate_fields_category'] : [];
		$data['hb_translate_fields_information']		= isset($store_info['hb_translate_fields_information'])? $store_info['hb_translate_fields_information'] : [];
		$data['hb_translate_fields_manufacturer']	= isset($store_info['hb_translate_fields_manufacturer'])? $store_info['hb_translate_fields_manufacturer'] : [];
		
		$data['types'][] = array(
			'id'			=> 'product',
			'table_name'	=> $this->language->get('tab_products'),
			'columns' 		=> $this->model_extension_hbseo_hb_translate->getProductColumns()
		);

		$data['types'][] = array(
			'id'			=> 'category',
			'table_name'	=> $this->language->get('tab_categories'),
			'columns' 		=> $this->model_extension_hbseo_hb_translate->getCategoryColumns()
		);

		$data['types'][] = array(
			'id'			=> 'information',
			'table_name'	=> $this->language->get('tab_informations'),
			'columns' 		=> $this->model_extension_hbseo_hb_translate->getInformationColumns()
		);

		$data['types'][] = array(
			'id'			=> 'manufacturer',
			'table_name'	=> $this->language->get('tab_manufacturers'),
			'columns' 		=> $this->model_extension_hbseo_hb_translate->getManufacturerColumns()
		);

		$this->model_extension_hbseo_hb_translate->fix_empty_fields();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate'.$this->hb_template_extension, $data));
	}
	
	public function products() {  
		$data['type'] = 'product';

		$data['total_items'] = $this->model_extension_hbseo_hb_translate->getTotalItems($data['type'], $this->master_language_id);
		$data['total_attribute_items'] = $this->model_extension_hbseo_hb_translate->getTotalAttributeCount($this->master_language_id);

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['columns'] = $this->model_extension_hbseo_hb_translate->getProductColumns();

		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];
		   foreach ($data['columns'] as $key => $value) {
				if ($key == 'attribute') {
					$data['item'][$key][$language_id] = $this->model_extension_hbseo_hb_translate->getAttributeCount($language_id);
				}else{
					$data['item'][$key][$language_id] = $this->model_extension_hbseo_hb_translate->getCount($data['type'], $key, $language_id);
				}			   
		   }
	   	}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_dashboard'.$this->hb_template_extension, $data));
	}

	public function categories() {  
		$data['type'] = 'category';		

		$data['total_items'] = $this->model_extension_hbseo_hb_translate->getTotalItems($data['type'], $this->master_language_id);

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['columns'] = $this->model_extension_hbseo_hb_translate->getCategoryColumns();

		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];
		   foreach ($data['columns'] as $key => $value) {
			   $data['item'][$key][$language_id] = $this->model_extension_hbseo_hb_translate->getCount($data['type'], $key, $language_id);
		   }
	   	}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_dashboard'.$this->hb_template_extension, $data));
	}

	public function informations() {  
		$data['type'] = 'information';	
	
		$data['total_items'] = $this->model_extension_hbseo_hb_translate->getTotalItems($data['type'], $this->master_language_id);

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['columns'] = $this->model_extension_hbseo_hb_translate->getInformationColumns();

		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];
		   foreach ($data['columns'] as $key => $value) {
			   $data['item'][$key][$language_id] = $this->model_extension_hbseo_hb_translate->getCount($data['type'], $key, $language_id);
		   }
	   	}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_dashboard'.$this->hb_template_extension, $data));
	}

	public function manufacturers() {
		$data['type'] = 'manufacturer';	

		$data['total_items'] = $this->model_extension_hbseo_hb_translate->getTotalItems($data['type'], $this->master_language_id);

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['columns'] = $this->model_extension_hbseo_hb_translate->getManufacturerColumns();

		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];
		   foreach ($data['columns'] as $key => $value) {
			   $data['item'][$key][$language_id] = $this->model_extension_hbseo_hb_translate->getCount($data['type'], $key, $language_id);
		   }
	   	}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_dashboard'.$this->hb_template_extension, $data));
	}

	public function show_count(){
		echo $this->model_extension_hbseo_hb_translate->getCount($this->request->get['type'], $this->request->get['column'], $this->request->get['language_id']);
	}

	public function clear_products() {  
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		$reference_language_id = $this->model_extension_hbseo_hb_translate->referenceLanguageID();

		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'search'	=> strtolower($search),
			'reference_language_id'	=> $reference_language_id
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_hb_translate->getTotalProduct($data); 		
		$records = $this->model_extension_hbseo_hb_translate->getProducts($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'item_id' 			=> $record['item_id'],
				'name' 				=> $record['name'],
				'model' 			=> $record['model'],
				'meta_title'		=> $record['meta_title'],
				'reference_meta_title'		=> $record['reference_meta_title'],
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_translate/clear_products', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['type'] = 'product';

		$this->load->model('localisation/language');
		$data['reference_language'] = $this->model_localisation_language->getLanguage($reference_language_id);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_items'.$this->hb_template_extension, $data));
	}

	public function clear_categories() {  		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		$reference_language_id = $this->model_extension_hbseo_hb_translate->referenceLanguageID();

		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'search'	=> strtolower($search),
			'reference_language_id'	=> $reference_language_id
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_hb_translate->getTotalCategory($data); 		
		$records = $this->model_extension_hbseo_hb_translate->getCategories($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'item_id' 			=> $record['item_id'],
				'name' 				=> $record['name'],
				'meta_title'		=> $record['meta_title'],
				'reference_meta_title'		=> $record['reference_meta_title'],
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_translate/clear_categories', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'category';

		$this->load->model('localisation/language');
		$data['reference_language'] = $this->model_localisation_language->getLanguage($reference_language_id);

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_items'.$this->hb_template_extension, $data));
	}

	public function clear_informations() {  		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		$reference_language_id = $this->model_extension_hbseo_hb_translate->referenceLanguageID();

		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'search'	=> strtolower($search),
			'reference_language_id'	=> $reference_language_id
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_hb_translate->getTotalInformation($data); 		
		$records = $this->model_extension_hbseo_hb_translate->getInformations($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'item_id' 			=> $record['item_id'],
				'name' 				=> $record['name'],
				'meta_title'		=> $record['meta_title'],
				'reference_meta_title'		=> $record['reference_meta_title'],
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_translate/clear_informations', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'information';

		$this->load->model('localisation/language');
		$data['reference_language'] = $this->model_localisation_language->getLanguage($reference_language_id);

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_items'.$this->hb_template_extension, $data));
	}

	public function clear_manufacturers() {
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}
		
		$reference_language_id = $this->model_extension_hbseo_hb_translate->referenceLanguageID();

		$data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin'),
			'search'	=> strtolower($search),
			'reference_language_id'	=> $reference_language_id
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total = $this->model_extension_hbseo_hb_translate->getTotalManufacturer($data); 		
		$records = $this->model_extension_hbseo_hb_translate->getManufacturers($data);

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'item_id' 			=> $record['item_id'],
				'name' 				=> $record['name'],
				'meta_title'		=> $record['meta_title'],
				'reference_meta_title'		=> $record['reference_meta_title'],
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_translate/clear_manufacturers', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$data['type'] = 'manufacturer';

		$this->load->model('localisation/language');
		$data['reference_language'] = $this->model_localisation_language->getLanguage($reference_language_id);

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_items'.$this->hb_template_extension, $data));
	}

	public function clear(){
		$json = [];
		$type = (isset($this->request->get['type']))? $this->request->get['type'] : '';
		
		$selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];

		if (empty($type)){
			$json['error'] = 'Invalid Request Type';
		}

		if (empty($selected)){
			$json['error'] = $this->language->get('error_no_record_selected');
		}

		if (!$json){
			switch ($type) {
				case 'product':
					foreach ($selected as $id) {
						$this->model_extension_hbseo_hb_translate->clearProduct($id);
					}
					break;
				
				case 'category':
					foreach ($selected as $id) {
						$this->model_extension_hbseo_hb_translate->clearCategory($id);
					}
					break;

				case 'information':
					foreach ($selected as $id) {
						$this->model_extension_hbseo_hb_translate->clearInformation($id);
					}
					break;
				
				case 'manufacturer':
					foreach ($selected as $id) {
						$this->model_extension_huntbee_seo_translate_module_hb_translate->clearManufacturer($id);
					}
					break;
			}			

			$json['success'] = $this->language->get('success_cleared');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function bulk_clear(){
		$json = [];
		$type = (isset($this->request->post['type']))? $this->request->post['type'] : '';

		$column = (isset($this->request->post['column']))? $this->request->post['column'] : '';

		if (empty($type) || empty($column)){
			$json['error'] = 'Invalid Request Type';
		}		

		if (!$json){
			switch ($type) {
				case 'product':
					$enabled_fields = $this->config->get('hb_translate_fields_product');
					if (in_array($column, $enabled_fields)){
						$this->model_extension_hbseo_hb_translate->bulk_clear_product($column);
						$json['success'] = $this->language->get('success_cleared');
					}else{
						$json['error'] = sprintf($this->language->get('error_column_disabled'), $column);
					}
					break;
				
				case 'category':
					$enabled_fields = $this->config->get('hb_translate_fields_category');
					if (in_array($column, $enabled_fields)){
						$this->model_extension_hbseo_hb_translate->bulk_clear_category($column);
						$json['success'] = $this->language->get('success_cleared');
					}else{
						$json['error'] = sprintf($this->language->get('error_column_disabled'), $column);
					}
					break;

				case 'information':
					$enabled_fields = $this->config->get('hb_translate_fields_information');
					if (in_array($column, $enabled_fields)){
						$this->model_extension_hbseo_hb_translate->bulk_clear_information($column);
						$json['success'] = $this->language->get('success_cleared');
					}else{
						$json['error'] = sprintf($this->language->get('error_column_disabled'), $column);
					}
					break;

				case 'manufacturer':
					$enabled_fields = $this->config->get('hb_translate_fields_manufacturer');
					if (in_array($column, $enabled_fields)){
						$this->model_extension_hbseo_hb_translate->bulk_clear_manufacturer($column);
						$json['success'] = $this->language->get('success_cleared');
					}else{
						$json['error'] = sprintf($this->language->get('error_column_disabled'), $column);
					}
					break;
			}			

			$json['success'] = $this->language->get('success_cleared');
		}	

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function logs(){
		if (!file_exists(DIR_LOGS)) {
			mkdir(DIR_LOGS, 0777, true);
		}

		$file = DIR_LOGS . 'huntbee_translatePro_logs.txt';
		if (file_exists($file)) {
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}else{
			$data['log'] = '';
		}
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_translate_logs'.$this->hb_template_extension, $data));
	}

	public function clear_logs() {
		if (!$this->validate()) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'huntbee_translatePro_logs.txt';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] =  $this->language->get('text_success_logs');
		}

		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_translate', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true));
	}

	public function translate(){
		$json = [];

		$text = (isset($this->request->post['text']))? html_entity_decode($this->request->post['text'], ENT_QUOTES, 'UTF-8') : '';
		$target_language_id = (isset($this->request->post['target_language_id']))? $this->request->post['target_language_id'] : '';

		if (empty($text)){
			$json['error'] = $this->language->get('error_no_text');
		}

		if (!$json){
			$this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();

            $languageMap = [];
            foreach ($languages as $language) {
                $languageMap[$language['language_id']] = $language;
            }

			$master_language_id = $this->config->get('hb_translate_master_language');
			$source_language_code = isset($languageMap[$master_language_id]) ? $languageMap[$master_language_id]['code'] : '';
			$target_language_code = isset($languageMap[$target_language_id]) ? $languageMap[$target_language_id]['code'] : '';

			$data = array(
				'source_text'	=> $text,
				'source_language_code'	=> $source_language_code,
				'target_language_code'	=> $target_language_code
			);

			$translated_text = $this->model_extension_hbseo_hb_translate->translate_content($data);

			$json['success'] = $translated_text;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function install() { 
		$this->model_extension_hbseo_hb_translate->install();
	}
	
	public function uninstall() { 
		$this->model_extension_hbseo_hb_translate->uninstall();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_translate')) {
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