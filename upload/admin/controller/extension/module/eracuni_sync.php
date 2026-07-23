<?php
class ControllerExtensionModuleEracuniSync extends Controller {
	private $error = array();

	private $defaults = array(
		'module_eracuni_sync_status'             => '1',
		'module_eracuni_sync_stock_status'       => '1',
		'module_eracuni_sync_price_status'       => '1',
		'module_eracuni_sync_code_field'         => 'model',
		'module_eracuni_sync_price_field'        => 'retailPrice',
		'module_eracuni_sync_stock_mode'         => 'available',
		'module_eracuni_sync_warehouse_code'     => '',
		'module_eracuni_sync_timeout'            => '45',
		'module_eracuni_sync_cron_key'           => ''
	);

	public function index() {
		$this->load->language('extension/module/eracuni_sync');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
			$post = array();

			foreach ($this->defaults as $key => $default) {
				$post[$key] = isset($this->request->post[$key]) ? $this->request->post[$key] : $default;
			}

			$post['module_eracuni_sync_warehouse_code'] = trim((string)$post['module_eracuni_sync_warehouse_code']);
			$post['module_eracuni_sync_cron_key'] = trim((string)$post['module_eracuni_sync_cron_key']);

			if ($post['module_eracuni_sync_cron_key'] === '') {
				$post['module_eracuni_sync_cron_key'] = $this->generateToken();
			}

			$this->model_setting_setting->editSetting('module_eracuni_sync', $post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/module/eracuni_sync', 'user_token=' . $this->session->data['user_token'], true));
		}

		$data = $this->language->all();
		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$data['error_cron_key'] = isset($this->error['cron_key']) ? $this->error['cron_key'] : '';
		$data['error_timeout'] = isset($this->error['timeout']) ? $this->error['timeout'] : '';

		$data['breadcrumbs'] = array(
			array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			),
			array(
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			),
			array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/eracuni_sync', 'user_token=' . $this->session->data['user_token'], true)
			)
		);

		$data['action'] = $this->url->link('extension/module/eracuni_sync', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['test_url'] = $this->url->link('extension/module/eracuni_sync/testConnection', 'user_token=' . $this->session->data['user_token'], true);
		$data['sync_url'] = $this->url->link('extension/module/eracuni_sync/sync', 'user_token=' . $this->session->data['user_token'], true);

		foreach ($this->defaults as $key => $default) {
			if (isset($this->request->post[$key])) {
				$data[$key] = $this->request->post[$key];
			} else {
				$value = $this->config->get($key);
				$data[$key] = ($value !== null && $value !== '') ? $value : $default;
			}
		}

		if ($data['module_eracuni_sync_cron_key'] === '') {
			$data['module_eracuni_sync_cron_key'] = $this->generateToken();
		}

		$catalog_url = defined('HTTPS_CATALOG') ? HTTPS_CATALOG : (defined('HTTP_CATALOG') ? HTTP_CATALOG : $this->config->get('config_url'));
		$data['cron_url'] = rtrim($catalog_url, '/') . '/index.php?route=extension/module/eracuni_sync/cron&key=' . rawurlencode($data['module_eracuni_sync_cron_key']);

		$this->load->library('eracuni/synchronizer');
		$data['connection'] = $this->synchronizer->getConnectionInfo();
		$data['connection_missing'] = implode(', ', $data['connection']['missing']);
		$data['last_stock'] = $this->decodeSummary($this->config->get('module_eracuni_sync_last_stock'));
		$data['last_prices'] = $this->decodeSummary($this->config->get('module_eracuni_sync_last_prices'));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/eracuni_sync', $data));
	}

	public function testConnection() {
		$this->load->language('extension/module/eracuni_sync');
		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/module/eracuni_sync')) {
			$json = array('success' => false, 'message' => $this->language->get('error_permission'));
		} else {
			try {
				$this->load->library('eracuni/synchronizer');
				$json = $this->synchronizer->testConnection();
			} catch (\Throwable $exception) {
				$json = array('success' => false, 'message' => $exception->getMessage());
			}
		}

		$this->jsonResponse($json);
	}

	public function sync() {
		$this->load->language('extension/module/eracuni_sync');
		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/module/eracuni_sync')) {
			$json = array('success' => false, 'message' => $this->language->get('error_permission'));
		} else {
			$type = isset($this->request->post['type']) ? (string)$this->request->post['type'] : '';

			try {
				$this->load->library('eracuni/synchronizer');

				if ($type === 'stock' && !$this->config->get('module_eracuni_sync_stock_status')) {
					throw new \RuntimeException('Sinkronizacija zalihe nije ukljucena u postavkama modula.');
				} elseif ($type === 'prices' && !$this->config->get('module_eracuni_sync_price_status')) {
					throw new \RuntimeException('Sinkronizacija cijena nije ukljucena u postavkama modula.');
				} elseif ($type === 'stock') {
					$json = $this->synchronizer->syncStock();
				} elseif ($type === 'prices') {
					$json = $this->synchronizer->syncPrices();
				} else {
					throw new \RuntimeException('Nepoznata vrsta sinkronizacije.');
				}
			} catch (\Throwable $exception) {
				$json = array('success' => false, 'message' => $exception->getMessage());
			}
		}

		$this->jsonResponse($json);
	}

	public function install() {
		$defaults = $this->defaults;
		$defaults['module_eracuni_sync_cron_key'] = $this->generateToken();
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('module_eracuni_sync', $defaults);
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/eracuni_sync');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/eracuni_sync');
	}

	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_eracuni_sync');
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/eracuni_sync')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$timeout = isset($this->request->post['module_eracuni_sync_timeout']) ? (int)$this->request->post['module_eracuni_sync_timeout'] : 0;

		if ($timeout < 5 || $timeout > 120) {
			$this->error['timeout'] = $this->language->get('error_timeout');
		}

		$key = isset($this->request->post['module_eracuni_sync_cron_key']) ? trim((string)$this->request->post['module_eracuni_sync_cron_key']) : '';

		if ($key !== '' && !preg_match('/^[A-Za-z0-9_-]{24,128}$/', $key)) {
			$this->error['cron_key'] = $this->language->get('error_cron_key');
		}

		return !$this->error;
	}

	private function generateToken() {
		try {
			return bin2hex(random_bytes(24));
		} catch (\Throwable $exception) {
			return hash('sha256', uniqid((string)mt_rand(), true));
		}
	}

	private function decodeSummary($value) {
		$decoded = json_decode((string)$value, true);
		return is_array($decoded) ? $decoded : array();
	}

	private function jsonResponse(array $json) {
		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
}
