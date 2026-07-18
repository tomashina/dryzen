<?php
class ControllerExtensioncmpltguagaf extends Controller {
	private $error = array();  
	private $modpath = 'extension/cmpltguagaf';
	private $modtpl = 'extension/cmpltguagaf.tpl';
	private $modssl = 'SSL';
	private $token_str = '';
	public function __construct($registry) {
		parent::__construct($registry);
 		
		if(substr(VERSION,0,3)>='3.0' || substr(VERSION,0,3)=='2.3') { 
 			$this->modpath = 'extension/cmpltguagaf';
 			$this->modtpl = 'extension/cmpltguagaf';
  		} else if(substr(VERSION,0,3)=='2.2') {
 			$this->modtpl = 'extension/cmpltguagaf';
		} 
		 
		if(substr(VERSION,0,3)>='3.0') { 
 			$this->token_str = 'user_token=' . $this->session->data['user_token'];
		} else {
			$this->token_str = 'token=' . $this->session->data['token'];
		}
		
		if(substr(VERSION,0,3)>='3.0' || substr(VERSION,0,3)=='2.3' || substr(VERSION,0,3)=='2.2') { 
			$this->modssl = true;
		} 
 	} 

	public function index() {
		$data = $this->load->language($this->modpath);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model($this->modpath);
		
		$this->model_extension_cmpltguagaf->checkdb();
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_str, $this->modssl)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->modpath, $this->token_str, $this->modssl)
		);
		
		if(substr(VERSION,0,3)>='3.0') { 
 			$data['user_token'] = $this->session->data['user_token']; 
		} else {
			$data['token'] = $this->session->data['token'];
		}
		
		$data['action'] = $this->url->link($this->modpath, $this->token_str, $this->modssl);
		$data['cancel'] = $this->url->link('common/dashboard', $this->token_str, $this->modssl);
		
		$data['stores'] = $this->model_extension_cmpltguagaf->getStores();
		$data['languages'] = $this->model_extension_cmpltguagaf->getLang();
 		 
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->session->data['success'] = $this->language->get('text_success');
			$this->model_extension_cmpltguagaf->add($this->request->post);
		}
 		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
 			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
 		$rs = $this->model_extension_cmpltguagaf->getrsdata();
    		 
		if (isset($this->request->post['desc'])) {
			$data['desc'] = $this->request->post['desc'];
		} elseif ($rs) {
			$data['desc'] = $rs;
 		} else {
			$data['desc'] = array();
			
			foreach($data['stores'] as $store_id => $storedata) {
				$lang = array();
				foreach($data['languages'] as $language) {					
					$lang[$language['language_id']] = 'Reset';
				}
				
				$data['desc'][$store_id] = array(
					'status' => 0,
 					'gaid' => 'UA-XXXXXXXX-X',
  					'gafid' => 'G-XXXXXXXXX',
   				);
			}
			//print_r($data['desc']);exit;
 		} 
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($this->modtpl, $data));
	}
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', $this->modpath)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}		
		return !$this->error;
	}
}
