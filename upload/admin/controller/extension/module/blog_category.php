<?php
class ControllerExtensionModuleBlogCategory extends Controller {
	private $error = array();

	public function index() {
		
		if ((float)VERSION >= 3.0) {
			$model_module_load = 'setting/module';
			$model_module_path = 'model_setting_module';
			$token_prefix = 'user_token';
			$modules_url = 'marketplace/extension';
			$module_prefix = 'module_';
		} else {
			$model_module_load = 'extension/module';
			$model_module_path = 'model_extension_module';
			$token_prefix = 'token';
			$modules_url = 'extension/extension';
			$module_prefix = '';
		}
		
		$this->load->language('extension/module/blog_category');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting($module_prefix.'blog_category', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/blog_category', $token_prefix . '=' . $this->session->data[$token_prefix], true)
		);

		$data['action'] = $this->url->link('extension/module/blog_category', $token_prefix . '=' . $this->session->data[$token_prefix], true);

		$data['cancel'] = $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true);

		if (isset($this->request->post[$module_prefix.'blog_category_status'])) {
			$data[$module_prefix.'blog_category_status'] = $this->request->post[$module_prefix.'blog_category_status'];
		} else {
			$data[$module_prefix.'blog_category_status'] = $this->config->get($module_prefix.'blog_category_status');
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blog_category', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/blog_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}