<?php
class ControllerExtensionModuleClickerCKEditor extends Controller {
	public $version;
	private $error = array();
	private $settings = array();
	private $js_path = DIR_APPLICATION . 'view/javascript/clicker_ckeditor/';

	public function __construct($registry) {
		parent::__construct($registry);

		$this->settings = $this->getSettings();
	}

	public function index() {
		$this->load->language('extension/module/clicker_ckeditor');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$data['heading_title'] = strip_tags($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->request->post = json_decode(json_encode($this->request->post, JSON_NUMERIC_CHECK),true);

			$this->model_setting_setting->editSetting('module_clicker_ckeditor', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

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
			'text' => strip_tags($this->language->get('heading_title')),
			'href' => $this->url->link('extension/module/clicker_ckeditor', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/clicker_ckeditor', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_clicker_ckeditor_status'])) {
			$data['module_clicker_ckeditor_status'] = $this->request->post['module_clicker_ckeditor_status'];
		} else {
			$data['module_clicker_ckeditor_status'] = $this->config->get('module_clicker_ckeditor_status');
		}

		$data['version'] = $this->getVersion();
		$data['oc_version'] = defined('VERSION') ? VERSION : 0;
		$data['settings'] = $this->settings;

		if (!empty($data['settings']['customConfigJson'])) {
			$data['settings']['customConfigJson'] = json_encode($data['settings']['customConfigJson'], JSON_PRETTY_PRINT);
		}

		$data['ck_languages'] = $this->getCKLanguages();
		$data['ck_skins'] = $this->getCKSkins();
		$data['cm_themes'] = $this->getCMThemes();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/clicker_ckeditor', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/clicker_ckeditor')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!empty($this->request->post['module_clicker_ckeditor_settings']['customConfigJson'])) {
			$test = json_decode(html_entity_decode($this->request->post['module_clicker_ckeditor_settings']['customConfigJson'], ENT_QUOTES, 'UTF-8'), true);
			$err_no = json_last_error();
			$err_msg = json_last_error_msg();

			if ($err_no) {
				$this->error['warning'] = 'customConfig JSON ERROR: ' . $err_no . ': ' . $err_msg;
			}
		}

		return !$this->error;
	}

	public function getSettings() {
		$settings = $this->config->get('module_clicker_ckeditor_settings') ? $this->config->get('module_clicker_ckeditor_settings') : array();

		$default_settings = json_decode(file_get_contents($this->js_path . '/clicker/defaults.json'), true);

		foreach ($default_settings as $key => $setting) {
			if (!isset($settings[$key])) {
				$settings[$key] = $setting;
			}
		}

		if (!empty($settings['customConfigJson'])) {
			$settings['customConfigJson'] = json_decode(html_entity_decode($settings['customConfigJson'], ENT_QUOTES, 'UTF-8'), true);
		}

		return $settings;
	}

	public function getPlugins() {
		$results = array();

		$files = glob($this->js_path . 'clicker/plugins/*', GLOB_ONLYDIR);

		if ($files) {
			$sort_order = array();

			foreach ($files as $file) {
				if (strpos(basename($file), '.') === 0) continue;

				$results[] = basename($file);

				$sort_order[] = utf8_strtolower(basename($file));
			}

			array_multisort($sort_order, SORT_ASC, SORT_NATURAL, $results);
		}

		return $results;
	}

	public function getCKLanguages() {
		$results = array();

		$files = glob($this->js_path . '/lang/*.js');

		if ($files) {
			$sort_order = array();

			foreach ($files as $file) {
				$path_parts = pathinfo($file);

				$results[$path_parts['filename']] = array(
					'id' => $path_parts['filename'],
					'title' => utf8_strtoupper($path_parts['filename'])
				);

				$sort_order[] = utf8_strtolower($path_parts['filename']);
			}

			array_multisort($sort_order, SORT_ASC, SORT_NATURAL, $results);
		}

		return $results;
	}

	public function getCKSkins() {
		$results = array();

		$files = glob($this->js_path . '/skins/*', GLOB_ONLYDIR);

		if ($files) {
			$sort_order = array();

			foreach ($files as $file) {
				$results[basename($file)] = array(
					'id' => basename($file),
					'title' => utf8_strtoupper(basename($file))
				);

				$sort_order[] = utf8_strtolower(basename($file));
			}

			array_multisort($sort_order, SORT_ASC, SORT_NATURAL, $results);
		}

		return $results;
	}

	public function getCMThemes() {
		$results = array();

		$files = glob($this->js_path . '/plugins/codemirror/theme/*.css');

		if ($files) {
			$sort_order = array();

			foreach ($files as $file) {
				$path_parts = pathinfo($file);

				$results[$path_parts['filename']] = array(
					'id' => $path_parts['filename'],
					'title' => utf8_strtoupper($path_parts['filename'])
				);

				$sort_order[] = utf8_strtolower($path_parts['filename']);
			}

			array_multisort($sort_order, SORT_ASC, SORT_NATURAL, $results);
		}

		return $results;
	}

	public function getVersion() {
		$query = $this->db->query("SELECT `code`, `version` FROM `" . DB_PREFIX . "modification` WHERE `code` = 'clicker_ckeditor'");

		$this->version = !empty($query->row['version']) ? trim($query->row['version']) : 0;

		return $this->version;
	}
}