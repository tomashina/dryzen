<?php
class ControllerExtensionHbseoHbRelated extends Controller {		
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
		$this->doc_link = 'https://www.huntbee.com/resources/docs/related-products-generator/';

		$this->load->model('extension/hbseo/hb_related');
		$this->load->language($this->hb_extension_route.'/hb_related');

		$this->hb_related_limit 	    = ($this->config->get('hb_related_limit')) ? $this->config->get('hb_related_limit') : 10;
	}

	public function index() {
		$data['extension_version'] = $this->hb_extension_version;

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		$extn_info = $this->model_setting_setting->getSetting('hb_related', 0);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_related', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link($this->hb_extension_route.'/hb_related', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true));
		}
		
		$text_strings = array(
				'heading_title',
				'text_category','text_brand','text_random','text_total_limit', 'text_confirm',
				'button_clear','button_generate','button_save','button_cancel','button_docs','button_create_template','button_clear_logs'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($this->hb_extension_route.'/hb_related', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true)
		);
		
		$data['action'] = $this->url->link($this->hb_extension_route.'/hb_related', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name], true);
		$data['cancel'] = $this->url->link($this->hb_extension_base, $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&type=hbseo', true);

		$data[$this->hb_token_name] = $this->session->data[$this->hb_token_name];
		$data['base_route'] = $this->hb_extension_route;	

		$data['hb_related_category']	= isset($extn_info['hb_related_category'])?$extn_info['hb_related_category'] : '';
		$data['hb_related_parent']		= isset($extn_info['hb_related_parent'])?$extn_info['hb_related_parent'] : '';
		$data['hb_related_brand']		= isset($extn_info['hb_related_brand'])?$extn_info['hb_related_brand'] : '';
		$data['hb_related_random']		= isset($extn_info['hb_related_random'])?$extn_info['hb_related_random'] : '';
		$data['hb_related_limit']		= isset($extn_info['hb_related_limit'])?$extn_info['hb_related_limit'] : '10';

		$data['doc_link']	= $this->doc_link;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbseo/'.$this->hb_template_folder.'/hb_related'.$this->hb_template_extension, $data));
	}

	public function batch_generate() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		if (!$json) {
			$page = (isset($this->request->get['page'])) ? (int)$this->request->get['page'] : 1;

			$product_total = 0;

			$products = array();

			$product_data = array(
				'start' => ($page - 1) * 10,
				'limit' => 10
			);

			$product_total = $this->model_extension_hbseo_hb_related->get_total_products($product_data);

			$results = $this->model_extension_hbseo_hb_related->getProducts($product_data);

			foreach ($results as $result) {
				$products[] = $result['product_id'];
			}
			
			if ($products) {
				$json['success'] = $this->language->get('text_generation_completion');

				$start = ($page - 1) * 10;
				$end = $start + 10;

				if ($end < $product_total) {
					$json['success'] = sprintf($this->language->get('text_generation_progress'), $start, $product_total);
				}

				if ($end < $product_total) {
					$json['next'] = str_replace('&amp;', '&', $this->url->link($this->hb_extension_route.'/hb_related/batch_generate', $this->hb_token_name.'=' . $this->session->data[$this->hb_token_name] . '&page=' . ($page + 1), true));
				} else {
					$json['next'] = '';
				}

				foreach ($products as $product_id) {
					$this->model_extension_hbseo_hb_related->generate_related_product($product_id, $this->hb_related_limit);
				}

			}else {
				$json['error'] = $this->language->get('error_products');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function clear_related_products() {
		$json = [];

		if (!$this->validate()) {
			$json['error'] = $this->error['warning'];
		}

		if (!$json) {
			$this->model_extension_hbseo_hb_related->truncate_related_products();
			$json['success'] = $this->language->get('text_related_products_cleared');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->hb_extension_route.'/hb_related')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install(){
		$this->model_extension_hbseo_hb_related->install();
	}
	
	public function uninstall(){
		$this->model_extension_hbseo_hb_related->uninstall();
	}
	
	public function update(){
		$this->model_extension_hbseo_hb_related->update();
		return true;
	}
}