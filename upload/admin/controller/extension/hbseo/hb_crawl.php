<?php
class ControllerExtensionHbseoHbCrawl extends Controller {
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
		
		$this->hb_extension_version	= '3.0.2';
		$this->doc_link = 'https://www.huntbee.com/resources/docs/index-crawl-optimizer/';

		$this->load->model('extension/hbseo/hb_crawl');		
		$this->load->language($this->hb_extension_route.'/hb_crawl');
	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_crawl', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_crawl', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
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
			'href'      => $this->url->link($this->hb_extension_route.'/hb_crawl', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_crawl', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;

		$data['doc_link']	= $this->doc_link;
		
		$data['options'] = $this->model_extension_hbseo_hb_crawl->options();

		$data['categories'] = array(
			'product' 		=> $this->language->get('text_crawl_product'), 
			'category'		=> $this->language->get('text_crawl_category'), 
			'information'	=> $this->language->get('text_crawl_information'),
			'manufacturer'	=> $this->language->get('text_crawl_manufacturer'),
			'special'		=> $this->language->get('text_crawl_special'),
			'search'		=> $this->language->get('text_crawl_search'),
			'tag'			=> $this->language->get('text_crawl_tag'),
			'notfound'		=> $this->language->get('text_crawl_notfound'),
			'jblog'			=> $this->language->get('text_crawl_jblog'),  
		);
		
		$store_info = $this->model_setting_setting->getSetting('hb_crawl', $this->request->get['store_id']);
		
		//settings
		$data['hb_crawl_status'] 			= isset($store_info['hb_crawl_status']) ? $store_info['hb_crawl_status'] : '';

		$data['hb_crawl']['product'] 			= isset($store_info['hb_crawl_product']) ? $store_info['hb_crawl_product'] : 'index, follow';
		$data['hb_crawl']['category'] 			= isset($store_info['hb_crawl_category']) ? $store_info['hb_crawl_category'] : 'index, follow';
		$data['hb_crawl']['information']		= isset($store_info['hb_crawl_information']) ? $store_info['hb_crawl_information'] : 'index, follow';
		$data['hb_crawl']['manufacturer']		= isset($store_info['hb_crawl_manufacturer']) ? $store_info['hb_crawl_manufacturer'] : 'index, follow';
		$data['hb_crawl']['special']			= isset($store_info['hb_crawl_special']) ? $store_info['hb_crawl_special'] : 'index, follow';
		$data['hb_crawl']['search']				= isset($store_info['hb_crawl_search']) ? $store_info['hb_crawl_search'] : 'noindex, follow';
		$data['hb_crawl']['tag']			    = isset($store_info['hb_crawl_tag']) ? $store_info['hb_crawl_tag'] : 'index, follow';
		$data['hb_crawl']['notfound']			= isset($store_info['hb_crawl_notfound']) ? $store_info['hb_crawl_notfound'] : 'noindex, nofollow';
		$data['hb_crawl']['jblog']				= isset($store_info['hb_crawl_jblog']) ? $store_info['hb_crawl_jblog'] : 'index, follow';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_crawl'.$this->hb_template_extension, $data));
	}

	public function routes(){
		$data['options'] = $this->model_extension_hbseo_hb_crawl->options();

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
			
		$records = $this->model_extension_hbseo_hb_crawl->getRoutes();

		$data['records'] = [];

		foreach ($records as $record) {
			$data['records'][] = array(
				'crawl_route_id'	=> $record['crawl_route_id'],
				'route' 			=> $record['route'],
				'meta' 				=> $record['meta']
			);
		}
		
		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_crawl_routes'.$this->hb_template_extension, $data));
	}

	public function add_route(){
		$json = [];
		
		$data['route'] = (isset($this->request->post['route']))? $this->request->post['route'] : '';
		$data['meta'] = (isset($this->request->post['meta']))? $this->request->post['meta'] : '';

		if (empty($data['route']) || $data['meta'] == 'No Tag'){
			$json['error'] = $this->language->get('error_invalid_data');
		}

		if (!$json){
			if ($this->model_extension_hbseo_hb_crawl->checkRoute($data['route'])){
				$this->model_extension_hbseo_hb_crawl->addRoute($data);
				$json['success'] = $this->language->get('success_route_added');
			}else{
				$json['error'] = $this->language->get('error_route_exists');
			}
			
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function update_route(){
		$json = [];
		
		$data['route_id'] = (isset($this->request->post['id']))? $this->request->post['id'] : '';
		$data['meta'] = (isset($this->request->post['meta']))? $this->request->post['meta'] : '';

		if (empty($data['route_id']) || $data['meta'] == 'No Tag'){
			$json['error'] = $this->language->get('error_invalid_data');
		}

		if (!$json){
			$this->model_extension_hbseo_hb_crawl->updateRoute($data);
			$json['success'] = $this->language->get('success_route_updated');			
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete_routes(){
		$json = [];
		
		$selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];

		if (empty($selected)){
			$json['error'] = $this->language->get('error_no_record_selected');
		}

		if (!$json){
			foreach ($selected as $id) {
				$this->model_extension_hbseo_hb_crawl->deleteRoute($id);
			}

			$json['success'] = $this->language->get('success_deleted');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function install() { 
		$this->model_extension_hbseo_hb_crawl->install();
	}
	
	public function uninstall() { 
		$this->model_extension_hbseo_hb_crawl->uninstall();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_crawl')) {
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