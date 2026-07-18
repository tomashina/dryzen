<?php

class ControllerExtensionExtensionHbapps extends Controller {

	protected $registry;
	private $error = array(); 
	
	public function __construct($registry) {
		$this->registry = $registry;
		if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$this->hb_template_folder 		= 'oc3';
			$this->hb_extension_base 		= 'marketplace/extension';
			$this->hb_token_name 			= 'user_token';
			$this->hb_template_extension 	= '';
			$this->hb_extension_route 		= 'extension/module';
		}else if (version_compare(VERSION,'2.2.0.0','<=' )) {
			$this->hb_template_folder 		= 'oc2';
			$this->hb_extension_base 		= 'extension/module';
			$this->hb_token_name 			= 'token';
			$this->hb_template_extension 	= '.tpl';
			$this->hb_extension_route 		= 'module';
		}else{
			$this->hb_template_folder 		= 'oc2';
			$this->hb_extension_base 		= 'extension/extension';
			$this->hb_token_name 			= 'token';
			$this->hb_template_extension 	= '';
			$this->hb_extension_route 		= 'extension/module';
		}
	}

	public function index() {
		$this->load->language('extension/extension/hbapps');

		if (version_compare(VERSION,'3.0.0.0','<' )) {
			$this->load->model('extension/extension');
		}else{
			$this->load->model('setting/extension');
		}

		$this->getList();
	}

	public function menu(){
		$data['extensions'] = array();

		$this->load->language('extension/extension/hbapps');
		$data['extensions'][] = array(
			'name'      => $this->language->get('all_apps').'&nbsp;&nbsp;<i class="fa fa-home"></i>',
			'href'   	=> $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbapps', true)
		);

		$files = glob(DIR_APPLICATION . 'controller/extension/hbapps/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				$this->load->language('extension/hbapps/' . $extension);
				
				if ($this->language->get('menu_title')){
					$menu_title = $this->language->get('menu_title');
				}else{
					$menu_title = $this->language->get('heading_title');
				}

				if ($menu_title == 'menu_title') {
					$menu_title = $this->language->get('heading_title');
				}

				if (strpos($menu_title, ':') !== false) {
					$menu_title = explode(':',$menu_title);
					$menu_title = $menu_title[1];
				}
				$menu_title = (utf8_strlen($menu_title) > 24 ? utf8_substr($menu_title, 0, 22) . '..' : $menu_title);
				
				$data['extensions'][] = array(
					'name'      => $menu_title,
					'href'   	=> $this->url->link('extension/hbapps/' . $extension, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id=0', true)
				);
				
				$this->language->set('menu_title', false);
			}

			
		}

		$menu = array();
			
		if ($this->user->hasPermission('access', 'extension/extension/hbapps')) {

			foreach ($data['extensions'] as $extn) {
				$menu[] = array(
					'name'     => $extn['name'],
					'href'     => $extn['href'],
					'children' => array()	
				);
			}
			
		}

		return $menu;
	}

	public function install() {
		$this->load->language('extension/extension/hbapps');

		if (version_compare(VERSION,'3.0.0.0','<' )) {
			$this->load->model('extension/extension');
		}else{
			$this->load->model('setting/extension');
		}

		if ($this->validate()) {
			if (version_compare(VERSION,'3.0.0.0','<' )) {
				$this->model_extension_extension->install('hbapps', $this->request->get['extension']);
			}else{
				$this->model_setting_extension->install('hbapps', $this->request->get['extension']);
			}

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/hbapps/' . $this->request->get['extension']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/hbapps/' . $this->request->get['extension']);

			// Call install method if it exsits
			$this->load->controller('extension/hbapps/' . $this->request->get['extension'] . '/install');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->getList();
	}

	public function uninstall() {
		$this->load->language('extension/extension/hbapps');

		if (version_compare(VERSION,'3.0.0.0','<' )) {
			$this->load->model('extension/extension');
		}else{
			$this->load->model('setting/extension');
		}

		if ($this->validate()) {
			if (version_compare(VERSION,'3.0.0.0','<' )) {
				$this->model_extension_extension->uninstall('hbapps', $this->request->get['extension']);
			}else{
				$this->model_setting_extension->uninstall('hbapps', $this->request->get['extension']);
			}

			// Call uninstall method if it exsits
			$this->load->controller('extension/hbapps/' . $this->request->get['extension'] . '/uninstall');

			$this->session->data['success'] = $this->language->get('text_success');
		}
		
		$this->getList();
	}

	public function update() {
		$this->load->language('extension/extension/hbapps');

		if (version_compare(VERSION,'3.0.0.0','<' )) {
			$this->load->model('extension/extension');
		}else{
			$this->load->model('setting/extension');
		}

		if ($this->validate()) {
			// Call update method if it exsits
			if (!$this->load->controller('extension/hbapps/' . $this->request->get['extension'] . '/update')){

				$extension_version = 'x.x.x';
				if (defined('EXTN_VERSION')){
					$extension_version = EXTN_VERSION;
				}

				if (defined('EXTENSION_VERSION')){
					$extension_version = EXTENSION_VERSION;
				}

				if ($this->hb_extension_version){
					$extension_version = $this->hb_extension_version;
				}

				$this->session->data['warning'] = sprintf($this->language->get('text_update_not_supported'), $extension_version);
			}else{
				$this->session->data['success'] = $this->language->get('text_updated');
			}
		}

		$this->getList();
	}

	protected function getList() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_install'] = $this->language->get('button_install');
		$data['button_uninstall'] = $this->language->get('button_uninstall');
		$data['button_update'] = $this->language->get('button_update');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];

			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}		

		
		if (version_compare(VERSION,'3.0.0.0','<' )) {
			$extensions = $this->model_extension_extension->getInstalled('hbapps');
		}else{
			$extensions = $this->model_setting_extension->getInstalled('hbapps');
		}

		foreach ($extensions as $key => $value) {
			if (!is_file(DIR_APPLICATION . 'controller/extension/hbapps/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/hbapps/' . $value . '.php')) {
				if (version_compare(VERSION,'3.0.0.0','<' )) {
					$this->model_extension_extension->uninstall('hbapps', $value);
				}else{
					$this->model_setting_extension->uninstall('hbapps', $value);
				}

				unset($extensions[$key]);
			}
		}

		$this->load->model('setting/store');
		$this->load->model('setting/setting');

		$stores = $this->model_setting_store->getStores();

		$data['extensions'] = array();
		
		// Compatibility code for old extension folders
		$files = glob(DIR_APPLICATION . 'controller/extension/hbapps/*.php', GLOB_BRACE);

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				$this->load->language('extension/hbapps/' . $extension);
					
				$store_data = array();
				
				$store_data[] = array(
					'name'   => $this->config->get('config_name'),
					'edit'   => $this->url->link('extension/hbapps/' . $extension, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id=0', true)
				);
									
				foreach ($stores as $store) {
					$store_data[] = array(
						'name'   => $store['name'],
						'edit'   => $this->url->link('extension/hbapps/' . $extension, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&store_id=' . $store['store_id'], true)
					);
				}
				
				$data['extensions'][] = array(
					'name'      => $this->language->get('heading_title'),
					'install'   => $this->url->link('extension/extension/hbapps/install', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&extension=' . $extension, true),
					'uninstall' => $this->url->link('extension/extension/hbapps/uninstall', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&extension=' . $extension, true),
					'update' 	=> $this->url->link('extension/extension/hbapps/update', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&extension=' . $extension, true),
					'installed' => in_array($extension, $extensions),
					'store'     => $store_data
				);
			}
			$data['store_count'] = count($store_data);
		}

		$this->response->setOutput($this->load->view('extension/extension/'.$this->hb_template_folder.'/hb'.$this->hb_template_extension, $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/extension/hbapps')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}