<?php
class ControllerExtensionHbseoHbCanonical extends Controller {
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
		
		$this->hb_extension_version	= '4.0.2';
	}
	
	public function index() {   
		$data['extension_version'] =  $this->hb_extension_version;
		
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->load->language($this->hb_extension_route.'/hb_canonical');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_canonical', $this->request->post, $this->request->get['store_id']);	
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_canonical', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		
		$text_strings = array(
				'heading_title',
				'text_type_long','text_type_short','text_type',
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
			'href'      => $this->url->link($this->hb_extension_route.'/hb_canonical', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true)
   		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_canonical', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;
		
		$store_info = $this->model_setting_setting->getSetting('hb_canonical', $this->request->get['store_id']);
		
		$data['hb_canonical_type'] 		= isset($store_info['hb_canonical_type'])?$store_info['hb_canonical_type']:'1';
		$data['hb_canonical_type_c'] 	= isset($store_info['hb_canonical_type_c'])?$store_info['hb_canonical_type_c']:'1';
		$data['hb_canonical_level'] 	= isset($store_info['hb_canonical_level'])?$store_info['hb_canonical_level']:'1';	
		$data['hb_canonical_status'] 	= isset($store_info['hb_canonical_status'])?$store_info['hb_canonical_status']:'';	
					
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_canonical'.$this->hb_template_extension, $data));

	}
	
	public function custom() {  
		//$store_id = (int)$this->request->get['store_id'];		
		$this->load->model('extension/hbseo/hb_canonical');
		
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
			//'store_id'	=> $store_id,
			'search'	=> $search
		);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	
		
		$reports_total 	= $this->model_extension_hbseo_hb_canonical->getTotalRecords($data); 		
		$records 		= $this->model_extension_hbseo_hb_canonical->getRecords($data);
		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' 			=> $record['id'],
				'url' 			=> $record['url'],
				'canonical' 	=> $record['canonical'],
				'selected'      => isset($this->request->post['selected']) && in_array($result['id'], $this->request->post['selected'])
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link($this->hb_extension_route.'/hb_canonical/custom', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_canonical_custom'.$this->hb_template_extension, $data));
	}
	
	public function add_canonical(){
		$json = array();
		$this->load->model('extension/hbseo/hb_canonical');
		
		$url 		= trim($this->request->post['browser_url']);
		$canonical 	= trim($this->request->post['canonical']);
		
		if (!empty($url) and !empty($canonical)) {
			if ($this->model_extension_hbseo_hb_canonical->insertCanonical($url,$canonical)) {
				$json['success'] = 'Canonical Link Added';
			} else {
				$json['warning'] = 'URL already Exists!';
			}
		}else{
			$json['warning'] = 'Improper Data. Please check all fields!';
		}

		$this->response->setOutput(json_encode($json));
	}
	
	public function delete(){
		$this->load->model('extension/hbseo/hb_canonical');
		
		if (!isset($this->request->post['selected'])){
			$json['warning'] = 'No Record Selected!';
		}else{
			$count = 0;
			$json['success'] = '';
			if (isset($this->request->post['selected'])){
				foreach ($this->request->post['selected'] as $id) {
					$this->model_extension_hbseo_hb_canonical->deleteRecord($id);
					$count = $count + 1;
				}
				$json['success'] .= $count.' record(s) deleted';
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function install() { 
		$this->load->model('extension/hbseo/hb_canonical');
		$this->model_extension_hbseo_hb_canonical->install();
	}
	
	public function uninstall() { 
		$this->load->model('extension/hbseo/hb_canonical');
		$this->model_extension_hbseo_hb_canonical->uninstall();
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_canonical')) {
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