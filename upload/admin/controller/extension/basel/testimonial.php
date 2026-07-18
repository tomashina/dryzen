<?php
class ControllerExtensionBaselTestimonial extends Controller { 
	private $error = array();
	
	public function index() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/testimonial');

		$this->document->SetTitle($this->language->get('heading_title'));
		 
		$this->load->model('extension/basel/testimonial');
		
		$this->getList();
	}
	
	

	public function insert() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/testimonial');

		$this->document->SetTitle($this->language->get('heading_title'));
				
		$this->load->model('extension/basel/testimonial');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_basel_testimonial->addTestimonial($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/basel/testimonial', $token_prefix . '=' . $this->session->data[$token_prefix], true));
		}

		$this->getForm(false);
	}

	public function update() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/testimonial');

		$this->document->SetTitle( $this->language->get('heading_title') );
		
		$this->load->model('extension/basel/testimonial');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_basel_testimonial->editTestimonial($this->request->get['testimonial_id'], $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/basel/testimonial', $token_prefix . '=' . $this->session->data[$token_prefix], true));
		}

		$this->getForm(false);
	}
 
	public function delete() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/testimonial');

		$this->document->SetTitle( $this->language->get('heading_title'));
		
		$this->load->model('extension/basel/testimonial');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $testimonial_id) {
				$this->model_extension_basel_testimonial->deleteTestimonial($testimonial_id);
			}
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/basel/testimonial', $token_prefix . '=' . $this->session->data[$token_prefix], true));
		}

		$this->getList();
	}

	protected function getList() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$data['button_edit'] = $this->language->get('button_edit');
		$data['text_list'] = $this->language->get('text_list');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'	=> $this->language->get('text_home'),
			'href'	=> $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], true)
   		);

   		$data['breadcrumbs'][] = array(
       		'text'	=> $this->language->get('heading_title'),
			'href'	=> $this->url->link('extension/basel/testimonial', $token_prefix . '=' . $this->session->data[$token_prefix], true)
   		);
							
		$data['insert'] = $this->url->link('extension/basel/testimonial/insert', $token_prefix . '=' . $this->session->data[$token_prefix], true);
		$data['delete'] = $this->url->link('extension/basel/testimonial/delete', $token_prefix . '=' . $this->session->data[$token_prefix], true);	

		$data['testimonials'] = array();

		$filter_data = array(
			'limit' => 999
		);
		
		$testimonial_total = $this->model_extension_basel_testimonial->getTotalTestimonials();
		
		$data['testimonial_total'] = $testimonial_total;
		
		$results = $this->model_extension_basel_testimonial->getTestimonials($filter_data);
 		
		foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('extension/basel/testimonial/update', $token_prefix . '=' . $this->session->data[$token_prefix]. '&testimonial_id=' . $result['testimonial_id'], true)
			);
	
			$data['testimonials'][] = array(
				'testimonial_id' => $result['testimonial_id'],
				'name'		=> $result['name'],
				'status' 		=> ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'selected'   	=> isset($this->request->post['selected']) && in_array($result['testimonial_id'], $this->request->post['selected']),
				'action'     	=> $action
			);
		}	
	
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_no_results'] = $this->language->get('text_no_results');

		$data['column_title'] = $this->language->get('column_title');
		
		$data['insert'] = $this->url->link('extension/basel/testimonial/insert', $token_prefix . '=' . $this->session->data[$token_prefix], true);
		$data['delete'] = $this->url->link('extension/basel/testimonial/delete', $token_prefix . '=' . $this->session->data[$token_prefix], true);	

		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');		
		$data['column_name'] = $this->language->get('column_name');		
		$data['button_add'] = $this->language->get('button_add');
		$data['button_delete'] = $this->language->get('button_delete');

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

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/basel/testimonial_list', $data));
	}

	private function getForm($is_edit) {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$data['is_edit'] = $is_edit;
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_default'] = $this->language->get('text_default');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_org'] = $this->language->get('entry_org');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
	 	if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = '';
		}

		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', $token_prefix . '=' . $this->session->data[$token_prefix], true),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/basel/testimonial', $token_prefix . '=' . $this->session->data[$token_prefix], true),
      		'separator' => ' :: '
   		);
							
		if (!isset($this->request->get['testimonial_id'])) {
			$data['action'] = $this->url->link('extension/basel/testimonial/insert', $token_prefix . '=' . $this->session->data[$token_prefix], true);		
		} else {
			$data['action'] = $this->url->link('extension/basel/testimonial/update', $token_prefix . '=' . $this->session->data[$token_prefix] . '&testimonial_id=' . $this->request->get['testimonial_id'], true);
		}
		
		$data['cancel'] = $this->url->link('extension/basel/testimonial', $token_prefix . '=' . $this->session->data[$token_prefix], true);

		$data['token'] = $this->session->data[$token_prefix];
		
		if (isset($this->request->get['testimonial_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$testimonial_info = $this->model_extension_basel_testimonial->getTestimonial($this->request->get['testimonial_id']);
		}
				
		$this->load->model('localisation/language');
		
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		if (isset($this->request->post['testimonial_description'])) {
			$data['testimonial_description'] = $this->request->post['testimonial_description'];
		} elseif (isset($this->request->get['testimonial_id'])) {
			$data['testimonial_description'] = $this->model_extension_basel_testimonial->getTestimonialDescriptions($this->request->get['testimonial_id']);
		} else {
			$data['testimonial_description'] = array();
		}
		
		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
		'store_id' => 0,
		'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();
		
		foreach ($stores as $store) {
		$data['stores'][] = array(
		'store_id' => $store['store_id'],
		'name'     => $store['name']
		);
		}

		if (isset($this->request->post['testimonial_store'])) {
			$data['testimonial_store'] = $this->request->post['testimonial_store'];
		} elseif (isset($this->request->get['testimonial_id'])) {
			$data['testimonial_store'] = $this->model_extension_basel_testimonial->getTestimonialStores($this->request->get['testimonial_id']);
		} else {
			$data['testimonial_store'] = array(0);
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (isset($testimonial_info)) {
			$data['status'] = $testimonial_info['status'];
		} else {
			$data['status'] = 0;
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (isset($testimonial_info)) {
			$data['name'] = $testimonial_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($testimonial_info)) {
			$data['image'] = $testimonial_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($testimonial_info) && is_file(DIR_IMAGE . $testimonial_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($testimonial_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['org'])) {
			$data['org'] = $this->request->post['org'];
		} elseif (isset($testimonial_info)) {
			$data['org'] = $testimonial_info['org'];
		} else {
			$data['org'] = '';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/basel/testimonial_form', $data));

	}

	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/basel/testimonial')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		foreach ($this->request->post['testimonial_description'] as $language_id => $value) {
			if ((utf8_strlen($value['description']) < 2) || (utf8_strlen($value['description']) > 2400)) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}
		}

		return !$this->error;
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/basel/testimonial')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}