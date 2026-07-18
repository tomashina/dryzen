<?php
class ControllerExtensionHbseoHbSeourl extends Controller {
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
		
		$this->hb_extension_version	= '3.6.2';		
		$this->doc_link = 'https://www.huntbee.com/resources/docs/seo-url-generator/';

		$this->load->model('extension/hbseo/hb_seourl');		
		$this->load->language($this->hb_extension_route.'/hb_seourl');
	} 
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/hbseo/hb_seourl');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_seourl', $this->request->post, $data['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_seourl', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
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
			'href' => $this->url->link('marketplace/extension', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link($this->hb_extension_route.'/hb_seourl', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_seourl', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);		
		$data['cancel'] = $this->url->link('marketplace/extension', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data['clear']	= $this->url->link($this->hb_extension_route.'/hb_seourl/clear_logs', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);

		$data['doc_link']	= $this->doc_link;

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	

		$extn_info = $this->model_setting_setting->getSetting('hb_seourl', $data['store_id']);

		//dashboard
		if ($data['store_id'] == 0){ 
			$data['store_url'] = HTTPS_CATALOG;
		}else{
			$results = $this->model_setting_store->getStore($data['store_id']);
			$data['store_url'] = $results['url'];
		}
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		//settings
		$data['hb_seourl_product_template'] 	= isset($extn_info['hb_seourl_product_template']) ? $extn_info['hb_seourl_product_template']:'{product_name}-{model}';
		$data['hb_seourl_use_pattern'] 			= isset($extn_info['hb_seourl_use_pattern']) ? $extn_info['hb_seourl_use_pattern']:'';
		
		$data['hb_seourl_trans'] 				= isset($extn_info['hb_seourl_trans']) ? $extn_info['hb_seourl_trans']:'';
		$data['hb_seourl_auto'] 				= isset($extn_info['hb_seourl_auto']) ? $extn_info['hb_seourl_auto']:'';
		$data['hb_seourl_dynamic'] 				= isset($extn_info['hb_seourl_dynamic']) ? $extn_info['hb_seourl_dynamic']:'';
		$data['hb_seourl_preserve'] 			= isset($extn_info['hb_seourl_preserve']) ? $extn_info['hb_seourl_preserve']:'';
		$data['hb_seourl_hreflang'] 			= isset($extn_info['hb_seourl_hreflang']) ? $extn_info['hb_seourl_hreflang']:'';
		$data['hb_seourl_simplified'] 			= isset($extn_info['hb_seourl_simplified']) ? $extn_info['hb_seourl_simplified']:'';
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seourl'.$this->hb_template_extension, $data));
	}

	public function dashboard(): void{
		$store_id 	= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		foreach ($data['languages'] as $language){
			$language_id = (int)$language['language_id'];	

			$data['total_product_count'][$language_id] 		= $this->model_extension_hbseo_hb_seourl->getProductCount($language_id, $store_id);
			$data['total_category_count'][$language_id] 	= $this->model_extension_hbseo_hb_seourl->getCategoryCount($language_id, $store_id);
			$data['total_brand_count'][$language_id] 		= $this->model_extension_hbseo_hb_seourl->getBrandCount($language_id, $store_id);
			$data['total_information_count'][$language_id] 	= $this->model_extension_hbseo_hb_seourl->getInformationCount($language_id, $store_id);
		
			$data['available_product_count'][$language_id] 	= $this->model_extension_hbseo_hb_seourl->getKeywordCountbyType('product_id=', $language_id, $store_id);
			$data['available_category_count'][$language_id] = $this->model_extension_hbseo_hb_seourl->getKeywordCountbyType('category_id=', $language_id, $store_id);
			$data['available_brand_count'][$language_id] 	= $this->model_extension_hbseo_hb_seourl->getKeywordCountbyType('manufacturer_id=', $language_id, $store_id);
			$data['available_information_count'][$language_id] = $this->model_extension_hbseo_hb_seourl->getKeywordCountbyType('information_id=', $language_id, $store_id);

			if ($data['total_product_count'][$language_id] == 0){
				$data['percent_product_count'][$language_id] = 0;
			}else{
				$data['percent_product_count'][$language_id] = ceil(($data['available_product_count'][$language_id]/$data['total_product_count'][$language_id]) * 100);
			}

			if ($data['total_category_count'][$language_id] == 0){
				$data['percent_category_count'][$language_id] = 0;
			}else{
				$data['percent_category_count'][$language_id] = ceil(($data['available_category_count'][$language_id]/$data['total_category_count'][$language_id]) * 100);
			}

			if ($data['total_brand_count'][$language_id] == 0){
				$data['percent_brand_count'][$language_id] = 0;
			}else{
				$data['percent_brand_count'][$language_id] = ceil(($data['available_brand_count'][$language_id]/$data['total_brand_count'][$language_id]) * 100);
			}

			if ($data['total_information_count'][$language_id] == 0){
				$data['percent_information_count'][$language_id] = 0;
			}else{
				$data['percent_information_count'][$language_id] = ceil(($data['available_information_count'][$language_id]/$data['total_information_count'][$language_id]) * 100);
			}

			if ($data['percent_product_count'][$language_id] > 100) {
				$data['product_bar_color_class'][$language_id] = 'warning';
			}elseif ($data['percent_product_count'][$language_id] == 100) {
				$data['product_bar_color_class'][$language_id] = 'success';
			}else{
				$data['product_bar_color_class'][$language_id] = 'info';
			}

			if ($data['percent_category_count'][$language_id] > 100) {
				$data['category_bar_color_class'][$language_id] = 'warning';
			}elseif ($data['percent_category_count'][$language_id] == 100) {
				$data['category_bar_color_class'][$language_id] = 'success';
			}else{
				$data['category_bar_color_class'][$language_id] = 'info';
			}

			if ($data['percent_brand_count'][$language_id] > 100) {
				$data['brand_bar_color_class'][$language_id] = 'warning';
			}elseif ($data['percent_brand_count'][$language_id] == 100) {
				$data['brand_bar_color_class'][$language_id] = 'success';
			}else{
				$data['brand_bar_color_class'][$language_id] = 'info';
			}

			if ($data['percent_information_count'][$language_id] > 100) {
				$data['information_bar_color_class'][$language_id] = 'warning';
			}elseif ($data['percent_information_count'][$language_id] == 100) {
				$data['information_bar_color_class'][$language_id] = 'success';
			}else{
				$data['information_bar_color_class'][$language_id] = 'info';
			}
			
		}

		$this->model_extension_hbseo_hb_seourl->clearEmptyKeywords();

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seourl_dashboard'.$this->hb_template_extension, $data));
	}

	public function languages(){
		$store_id 	= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';

		$data['store_id'] = $store_id;

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['base_route'] = $this->hb_extension_route;

		$this->load->model('setting/setting');
		$config_data = $this->model_setting_setting->getSetting('config', $store_id);

		$data['config_language'] = (isset($config_data['config_language'])) ? $config_data['config_language'] : $this->config->get('config_language');		

		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];
			if ($language['code'] != $data['config_language']) {
				$stored_keyword = $this->model_extension_hbseo_hb_seourl->getKeyword($store_id, $language_id, 'language_id='.$language_id); 
				$row_status = false;
				if ($stored_keyword) {
					if (isset($stored_keyword) && !empty($stored_keyword)) {
						$row_status = true;
					}
				}	
				$data['set_languages'][] = array(
					'language_id' 		=>	$language_id,
					'keyword' 			=> substr($language['code'],0,2),
					'code' 				=> $language['code'],
					'row_status' 		=> $row_status,
				);
			}		
		}

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seourl_languages'.$this->hb_template_extension, $data));
	}

	public function update_language_keyword(): void{
		$json = [];

		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';

		$language_id = (isset($this->request->post['language_id'])) ? (int)$this->request->post['language_id'] : '0';
		$keyword = (isset($this->request->post['keyword'])) ? $this->request->post['keyword'] : '';
		$keyword = trim($keyword);

		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json){
			if (empty($keyword)){
				$this->model_extension_hbseo_hb_seourl->deleteKeywordbyKeyValue($store_id, $language_id, 'language_id='.$language_id);
				$json['success'] = $this->language->get('success_language_keyword_removed');
			}else{
				$this->model_extension_hbseo_hb_seourl->addRoutes($store_id, $language_id, 'language_id='.$language_id, $keyword);
				$json['success'] = sprintf($this->language->get('success_language_keyword_updated'), $keyword);
			}
			
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function routes(): void{
		$store_id 	= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';

		$data['store_id'] = $store_id;

		$data['user_token'] = $this->session->data['user_token'];
		$data['base_route'] = $this->hb_extension_route;

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['routes'] = $this->model_extension_hbseo_hb_seourl->getDistinctRoutes($store_id); 
		
		foreach ($data['routes'] as $route){
			foreach ($data['languages'] as $language){
				$language_id = $language['language_id'];
				$data['route_array'][$route['query']][$language_id] = $this->model_extension_hbseo_hb_seourl->getKeyword($store_id, $language_id, $route['query']);
			}
		}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seourl_routes'.$this->hb_template_extension, $data));
	}

	public function edit_route(): void{
		$store_id 	= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';

		$data['store_id'] = $store_id;

		$data['routekey'] 	= (isset($this->request->get['routekey'])) ? $this->request->get['routekey'] : '';

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (empty($data['routekey'])){
			foreach ($data['languages'] as $language){
				$language_id = $language['language_id'];
				$data['route_value'][$language_id] = '';
			}
		}else{
			foreach ($data['languages'] as $language){
				$language_id = $language['language_id'];
				$data['route_value'][$language_id] = $this->model_extension_hbseo_hb_seourl->getKeyword($store_id, $language_id, $data['routekey']);
			}
		}		

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seourl_edit_route', $data));
	}

	public function update_route(): void {
		$json = [];
		
		$this->load->model('localisation/language');

		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';
		
		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json){
			$routekey = (isset($this->request->post['routekey'])) ? $this->request->post['routekey'] :''; 
			$route_value = (isset($this->request->post['route_value'])) ? $this->request->post['route_value'] : array(); 
			$sameas = (isset($this->request->post['sameas'])) ? true : false; 
			$same_keyword = (isset($this->request->post['route_keyword'])) ? $this->request->post['route_keyword'] :''; 

			
			if (!empty($route_value)) {
				foreach ($route_value as $key => $value){
					$language_id 	= $key;
					$keyword 		= trim($value);
					
					$language = $this->model_localisation_language->getLanguage($language_id);
					if (!empty($keyword)){
						if (!$this->model_extension_hbseo_hb_seourl->is_duplicate_keyword($store_id, 0, $keyword)){
							$this->model_extension_hbseo_hb_seourl->addRoutes($store_id, $language_id, $routekey, $keyword);
							$json['success'] = sprintf($this->language->get('success_route_updated'), $keyword, $language['name']);
						}else{
							$json['error'] = sprintf($this->language->get('error_route_exists'),$keyword);
						}						
					}
				}
			}else{
				$json['error'] = $this->language->get('error_route_invalid');
			}						
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}	

	public function delete_route(): void {
		$json = [];

		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';
		
		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!isset($this->request->post['selected']) || empty($this->request->post['selected'])){
			$json['error'] = $this->language->get('error_no_record_selected');
		}

		$count = 0;
		
		if (!$json){			
			foreach ($this->request->post['selected'] as $value) {
				$this->model_extension_hbseo_hb_seourl->deleteRoutes($value, $store_id);
				$count = $count + 1;
			}
			$json['success'] = sprintf($this->language->get('success_routes_deleted'), $count);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function reset_routes(): void{
		$json = [];
		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';
		
		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json){	
			$default_routes = $this->model_extension_hbseo_hb_seourl->default_routes();
			
			foreach ($default_routes as $key => $value){
				$this->model_extension_hbseo_hb_seourl->addRoutes($store_id, $this->config->get('config_language_id'), $key, $value);
			}				
	
			$json['success'] = $this->language->get('success_route_reset');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function preserved(): void{
		$store_id 	= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';
		$page 		= (isset($this->request->get['page'])) ? (int)$this->request->get['page'] : '1';
		$search 	= (isset($this->request->get['search'])) ? $this->request->get['search'] : '';

		$limit = ($this->config->get('config_limit_admin')) ? $this->config->get('config_limit_admin') : 50;
		$data = array(
			'start' 		=> ($page - 1) * $limit,
			'limit' 		=> $limit,
			'search'		=> strtolower($search),
			'store_id'		=> $store_id
		);

		$data['user_token'] = $this->session->data['user_token'];	
		
		$records_total = $this->model_extension_hbseo_hb_seourl->getTotalPreserved($data); 		
		$records = $this->model_extension_hbseo_hb_seourl->getPreserved($data);

		$data['records'] = array();

		foreach ($records as $record) {

			$data['records'][] = array(
				'hb_url_preserve_id'	=> $record['id'],
				'store_id'				=> $record['store_id'],
				'language_id'			=> $record['language_id'],
				'query'					=> $record['query'],
				'old_keyword' 			=> $record['old_keyword'],
				'new_keyword' 			=> $record['new_keyword'],
				'date_added' 			=> date('d-M-Y H:i:s', strtotime($record['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $records_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_seourl/preserved', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] .'&store_id=' . $store_id . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seourl_preserved'.$this->hb_template_extension, $data));
	}

	public function preserve_keywords(){
		$json = [];

		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';

		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		$last_preserved_date = $this->model_extension_hbseo_hb_seourl->getPreserveDate($store_id);
		if (!empty($last_preserved_date)) {
			$json['error'] = sprintf($this->language->get('error_preserve_table_not_empty'), date('d-m-Y',strtotime($last_preserved_date)));
		}

		if (!$json){
			$this->model_extension_hbseo_hb_seourl->preserveKeywords($store_id);
			$this->model_extension_hbseo_hb_seourl->addlog('Old keywords preserved for Store ID = '.$store_id);
			$json['success'] = $this->language->get('success_keywords_preserved');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function clear_preserve(){
		$json = [];

		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';

		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json){
			$this->model_extension_hbseo_hb_seourl->clearPreserve($store_id);
			$this->model_extension_hbseo_hb_seourl->addlog('Preserved Keywords deleted for Store ID = '.$store_id);
			$json['success'] = $this->language->get('success_preserve_keywords_deleted');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function seourl_to_brokenlinks(){
		$json = [];

		$store_id = (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';

		$this->load->model('setting/store');
		if ($store_id == 0){ 
			$store_url = HTTPS_CATALOG;
		}else{
			$results = $this->model_setting_store->getStore($store_id);
			$store_url = $results['url'];
		}

		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			if ($this->model_extension_hbseo_hb_seourl->isExtensionInstalled('hb_brokenlinks')) {
				$this->load->model('extension/hbseo/hb_brokenlinks');
				$records = $this->model_extension_hbseo_hb_seourl->getPreserveRecords($store_id);
				if ($records){
					foreach ($records as $record) {
						$old_path = urlencode($store_url.$record['old_keyword']);
						$new_path = urlencode($store_url.$record['new_keyword']);
						$this->model_extension_hbseo_hb_brokenlinks->insertRecord($old_path, $new_path, $type = '301', $author = 3, $store_id);
						$this->model_extension_hbseo_hb_seourl->addlog('Inserting 301 Redirect Record for old keyword ['.$record['old_keyword'].'] :: Redirect URL - '. urldecode($new_path));
					}
					
					$json['success'] = $this->language->get('success_preserve_keywords_redirect');
				}else{
					$json['error'] = $this->language->get('error_no_valid_preserve_records');
				}
			}else{
				$json['error'] = $this->language->get('error_brokenlinks_not_installed');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function generate_keywords(): void {
		$json = [];

		$store_id 		= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';
		$language_id 	= (isset($this->request->get['language_id'])) ? (int)$this->request->get['language_id'] : '0';

		$type 			= (isset($this->request->get['key'])) ? $this->request->get['key'] : '';
		
		$this->load->model('setting/setting');
		$extn_info = $this->model_setting_setting->getSetting('hb_seourl', $store_id);

		$hb_seourl_product_template 	= isset($extn_info['hb_seourl_product_template']) ? $extn_info['hb_seourl_product_template']:'{name}';
		$hb_seourl_use_pattern 			= isset($extn_info['hb_seourl_use_pattern']) ? $extn_info['hb_seourl_use_pattern']:false;
		$hb_seourl_trans 				= isset($extn_info['hb_seourl_trans']) ? $extn_info['hb_seourl_trans']:true;
		$hb_seourl_preserve				= isset($extn_info['hb_seourl_preserve']) ? $extn_info['hb_seourl_preserve']:false;

		if (!$hb_seourl_use_pattern){
			$hb_seourl_product_template = '{name}';
		}
		$extension_settings = array(
			'template'		=> 	$hb_seourl_product_template,
			'transliterate'	=>	$hb_seourl_trans,
			'preserve'		=>	$hb_seourl_preserve
		);

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$limit = 5;

			switch ($type) {
				case 'product':
					$query = 'product_id=';
					$record_total = $this->model_extension_hbseo_hb_seourl->getProductCount($language_id, $store_id);
					$results = $this->model_extension_hbseo_hb_seourl->getProducts($language_id, $store_id, ($page - 1) * $limit, $limit);	
				break;
	
				case 'category':
					$query = 'category_id=';
					$record_total = $this->model_extension_hbseo_hb_seourl->getCategoryCount($language_id, $store_id);
					$results = $this->model_extension_hbseo_hb_seourl->getCategories($language_id, $store_id, ($page - 1) * $limit, $limit);
				break;
				
				case 'manufacturer':
					$query = 'manufacturer_id=';
					$record_total = $this->model_extension_hbseo_hb_seourl->getBrandCount($language_id, $store_id);
					$results = $this->model_extension_hbseo_hb_seourl->getBrands($language_id, $store_id, ($page - 1) * $limit, $limit);
				break;
	
				case 'information':
					$query = 'information_id=';
					$record_total = $this->model_extension_hbseo_hb_seourl->getInformationCount($language_id, $store_id);
					$results = $this->model_extension_hbseo_hb_seourl->getInformations($language_id, $store_id, ($page - 1) * $limit, $limit);
				break;
			}
			
			$available_keywords = $this->model_extension_hbseo_hb_seourl->getKeywordCountbyType($query, $language_id, $store_id);
			$json['available_keywords'] = $available_keywords;
			$json['progress'] 			= ceil(($available_keywords/$record_total) * 100);

			if ($results) {
				$start = ($page - 1) * $limit;
				$end = $start + $limit;
				
				$json['success'] = sprintf($this->language->get('success_keyword_generated'), $start ? $start : 1, $record_total);

				if ($end < $record_total) {
					$json['next'] = $this->url->link($this->hb_extension_route.'/hb_seourl/generate_keywords', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&key='. $type . '&language_id=' . $language_id . '&store_id='. $store_id . '&page=' . ($page + 1), true);
					$json['next'] =  html_entity_decode($json['next']);
				} else {
					$json['next'] = '';
					$json['success'] = $this->language->get('success_all_keyword_generated');
					$json['progress'] = '100';
					$json['progress_success'] = true;
					$json['available_keywords'] = $record_total;
				}

				foreach ($results as $result) {
					$this->model_extension_hbseo_hb_seourl->generateKeyword($store_id, $type, $result, $extension_settings);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function clear_seo_url(): void {
		$json = [];

		$store_id 		= (isset($this->request->get['store_id'])) ? (int)$this->request->get['store_id'] : '0';
		$language_id 	= (isset($this->request->get['language_id'])) ? (int)$this->request->get['language_id'] : '0';

		$key 			= (isset($this->request->post['key'])) ? $this->request->post['key'] : '';
		
		if (!$this->validate()) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json){
			$this->model_extension_hbseo_hb_seourl->clear_keyword_by_type($key, $store_id, $language_id);
			$this->model_extension_hbseo_hb_seourl->addlog('SEO URL Cleared for query = '.$key.' :: Store ID = '.$store_id. ' :: Language ID = '. $language_id);
			$json['success'] = $this->language->get('success_seo_url_cleared');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function logs(){
		if (!file_exists(DIR_LOGS)) {
			mkdir(DIR_LOGS, 0777, true);
		}

		$file = DIR_LOGS . 'huntbee_seo_url_logs.txt';
		if (file_exists($file)) {
			$data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}else{
			$data['log'] = '';
		}

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_seourl_logs', $data));
	}

	public function clear_logs() {
		if (!$this->validate()) {
			$this->session->data['error'] = $this->language->get('error_permission');
		} else {
			$file = DIR_LOGS . 'huntbee_seo_url_logs.txt';

			$handle = fopen($file, 'w+');

			fclose($handle);

			$this->session->data['success'] =  $this->language->get('text_success_logs');
		}

		$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_seourl', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true));
	}

	//EVENTS
	public function event_add_product(&$route, &$args){
		if (!$this->config->get('hb_seourl_auto')){
			return false;
		}
		//$this->log->write($args);

		$product_id = $args[0];
		$this->load->model('setting/store');

		$data['stores'] = [];

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		];

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			];
		}

		foreach ($data['stores'] as $store){
			$store_id = $store['store_id'];

			$this->load->model('setting/setting');
			$extn_info = $this->model_setting_setting->getSetting('hb_seourl', $store_id);

			$hb_seourl_product_template 	= isset($extn_info['hb_seourl_product_template']) ? $extn_info['hb_seourl_product_template']:'{product_name}';
			$hb_seourl_use_pattern 			= isset($extn_info['hb_seourl_use_pattern']) ? $extn_info['hb_seourl_use_pattern']:false;
			$hb_seourl_trans 				= isset($extn_info['hb_seourl_trans']) ? $extn_info['hb_seourl_trans']:true;
			$hb_seourl_preserve				= isset($extn_info['hb_seourl_preserve']) ? $extn_info['hb_seourl_preserve']:false;

			if (!$hb_seourl_use_pattern){
				$hb_seourl_product_template = '{product_name}';
			}
			$extension_settings = array(
				'template'		=> 	$hb_seourl_product_template,
				'transliterate'	=>	$hb_seourl_trans,
				'preserve'		=>	$hb_seourl_preserve
			);

			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();

			foreach ($languages as $language) {
				$info = $this->model_extension_hbseo_hb_seourl->getProduct($product_id, $language['language_id']);

				$this->model_extension_hbseo_hb_seourl->generateKeyword($store_id, 'product', $info, $extension_settings);
			}
			
		}
	}

	public function event_add_category(&$route, &$args){
		if (!$this->config->get('hb_seourl_auto')){
			return false;
		}
		//$this->log->write($args);

		$category_id = $args[0];
		$this->load->model('setting/store');

		$data['stores'] = [];

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		];

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			];
		}

		foreach ($data['stores'] as $store){
			$store_id = $store['store_id'];

			$this->load->model('setting/setting');
			$extn_info = $this->model_setting_setting->getSetting('hb_seourl', $store_id);

			$hb_seourl_trans 				= isset($extn_info['hb_seourl_trans']) ? $extn_info['hb_seourl_trans']:true;
			$hb_seourl_preserve				= isset($extn_info['hb_seourl_preserve']) ? $extn_info['hb_seourl_preserve']:false;

			$extension_settings = array(
				'transliterate'	=>	$hb_seourl_trans,
				'preserve'		=>	$hb_seourl_preserve
			);

			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();

			foreach ($languages as $language) {
				$info = $this->model_extension_hbseo_hb_seourl->getCategory($category_id, $language['language_id']);

				$this->model_extension_hbseo_hb_seourl->generateKeyword($store_id, 'category', $info, $extension_settings);
			}
			
		}
	}

	public function event_add_brand(&$route, &$args){
		if (!$this->config->get('hb_seourl_auto')){
			return false;
		}
		//$this->log->write($args);

		$manufacturer_id = $args[0];
		$this->load->model('setting/store');

		$data['stores'] = [];

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		];

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			];
		}

		foreach ($data['stores'] as $store){
			$store_id = $store['store_id'];

			$this->load->model('setting/setting');
			$extn_info = $this->model_setting_setting->getSetting('hb_seourl', $store_id);

			$hb_seourl_trans 				= isset($extn_info['hb_seourl_trans']) ? $extn_info['hb_seourl_trans']:true;
			$hb_seourl_preserve				= isset($extn_info['hb_seourl_preserve']) ? $extn_info['hb_seourl_preserve']:false;

			$extension_settings = array(
				'transliterate'	=>	$hb_seourl_trans,
				'preserve'		=>	$hb_seourl_preserve
			);

			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();

			foreach ($languages as $language) {
				$info = $this->model_extension_hbseo_hb_seourl->getBrand($manufacturer_id, $language['language_id']);

				$this->model_extension_hbseo_hb_seourl->generateKeyword($store_id, 'manufacturer', $info, $extension_settings);
			}
			
		}
	}

	public function event_add_information(&$route, &$args){
		if (!$this->config->get('hb_seourl_auto')){
			return false;
		}
		//$this->log->write($args);

		$information_id = $args[0];
		$this->load->model('setting/store');

		$data['stores'] = [];

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		];

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			];
		}

		foreach ($data['stores'] as $store){
			$store_id = $store['store_id'];

			$this->load->model('setting/setting');
			$extn_info = $this->model_setting_setting->getSetting('hb_seourl', $store_id);

			$hb_seourl_trans 				= isset($extn_info['hb_seourl_trans']) ? $extn_info['hb_seourl_trans']:true;
			$hb_seourl_preserve				= isset($extn_info['hb_seourl_preserve']) ? $extn_info['hb_seourl_preserve']:false;

			$extension_settings = array(
				'transliterate'	=>	$hb_seourl_trans,
				'preserve'		=>	$hb_seourl_preserve
			);

			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();

			foreach ($languages as $language) {
				$info = $this->model_extension_hbseo_hb_seourl->getInformation($information_id, $language['language_id']);

				$this->model_extension_hbseo_hb_seourl->generateKeyword($store_id, 'information', $info, $extension_settings);
			}
			
		}
	}
	
	public function install(){
			$this->model_extension_hbseo_hb_seourl->install();
	}
	
	public function uninstall(){
			$this->model_extension_hbseo_hb_seourl->uninstall();
	}
		
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_seourl')) {
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