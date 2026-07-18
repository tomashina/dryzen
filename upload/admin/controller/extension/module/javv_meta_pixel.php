<?php
class ControllerExtensionModuleJavvMetaPixel extends Controller {
	private $error = array();

	private $defaults = array(
		'module_javv_meta_pixel_status'             => '1',
		'module_javv_meta_pixel_pixel_id'           => '2208670019904141',
		'module_javv_meta_pixel_page_view_status'   => '1',
		'module_javv_meta_pixel_add_to_cart_status' => '1',
		'module_javv_meta_pixel_purchase_status'    => '1',
		'module_javv_meta_pixel_debug_status'       => '0'
	);

	public function index() {
		$this->load->language('extension/module/javv_meta_pixel');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			foreach ($this->defaults as $key => $default) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = $default;
				}
			}

			$this->request->post['module_javv_meta_pixel_pixel_id'] = trim($this->request->post['module_javv_meta_pixel_pixel_id']);

			$this->model_setting_setting->editSetting('module_javv_meta_pixel', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$data['error_pixel_id'] = isset($this->error['pixel_id']) ? $this->error['pixel_id'] : '';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/javv_meta_pixel', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/javv_meta_pixel', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		foreach ($this->defaults as $key => $default) {
			if (isset($this->request->post[$key])) {
				$data[$key] = $this->request->post[$key];
			} else {
				$value = $this->config->get($key);
				$data[$key] = ($value !== null && $value !== '') ? $value : $default;
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/javv_meta_pixel', $data));
	}

	public function install() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('module_javv_meta_pixel', $this->defaults);

		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/javv_meta_pixel');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/javv_meta_pixel');
	}

	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_javv_meta_pixel');
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/javv_meta_pixel')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$pixel_id = isset($this->request->post['module_javv_meta_pixel_pixel_id']) ? trim($this->request->post['module_javv_meta_pixel_pixel_id']) : '';

		if (!preg_match('/^[0-9]{5,}$/', $pixel_id)) {
			$this->error['pixel_id'] = $this->language->get('error_pixel_id');
		}

		return !$this->error;
	}
}
