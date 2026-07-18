<?php
define('EXTENSION_VERSION','3.0.0');
class ControllerExtensionModuleBasePlugin extends Controller {
		
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
		$data['extension_version'] = EXTENSION_VERSION;
		
		$this->load->language($this->hb_extension_route.'/base_plugin');
		$this->load->model('extension/module/base_plugin');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$text_strings = array('heading_title','text_edit','button_cancel','text_confirm');
		
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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->hb_extension_route.'/base_plugin', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/base_plugin', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=module', true);
				
		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	

		$data['hbapps'] = $this->url->link('extension/extension/hbapps', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);
		$data['hbapps'] = html_entity_decode($data['hbapps'], ENT_QUOTES, 'UTF-8');

		$data['hbseo'] = $this->url->link('extension/extension/hbseo', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);
		$data['hbseo'] = html_entity_decode($data['hbseo'], ENT_QUOTES, 'UTF-8');

		$data['php_info_link'] = $this->url->link($this->hb_extension_route.'/base_plugin/php_info', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);

		$data['server_date'] = date('d-m-Y h:i:s A');
		$data['opencart_version'] = VERSION;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/'.$this->hb_template_folder.'/base_plugin'.$this->hb_template_extension, $data));
	}

	public function php_info(){
		phpinfo();
	}

	public function install(){
		$this->load->model('extension/module/base_plugin');
		$this->model_extension_module_base_plugin->install();
	}
	
	public function uninstall(){
		$this->load->model('extension/module/base_plugin');
		$this->model_extension_module_base_plugin->uninstall();
	}
	
}