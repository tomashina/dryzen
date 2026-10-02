<?php
class ControllerExtensionFeedDigitalPricelist extends Controller {
	private $error = array();

	private $defaults = array(
		'feed_digital_pricelist_status'       => '1',
		'feed_digital_pricelist_archive_days' => '30',
		'feed_digital_pricelist_cron_key'     => '',
		'feed_digital_pricelist_object_type'  => 'webshop',
		'feed_digital_pricelist_object_address' => '',
		'feed_digital_pricelist_object_designation' => 'WEB-01',
		'feed_digital_pricelist_unit'         => 'kom'
	);

	public function index() {
		$this->load->language('extension/feed/digital_pricelist');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] === 'POST') && $this->validate()) {
			$post = array();

			foreach ($this->defaults as $key => $default) {
				$post[$key] = isset($this->request->post[$key]) ? trim((string)$this->request->post[$key]) : $default;
			}

			if ($post['feed_digital_pricelist_cron_key'] === '') {
				$post['feed_digital_pricelist_cron_key'] = $this->generateToken();
			}

			$post['feed_digital_pricelist_archive_days'] = (string)max(30, (int)$post['feed_digital_pricelist_archive_days']);
			$this->model_setting_setting->editSetting('feed_digital_pricelist', $post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/feed/digital_pricelist', 'user_token=' . $this->session->data['user_token'], true));
		}

		$data = $this->language->all();
		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$data['error_archive_days'] = isset($this->error['archive_days']) ? $this->error['archive_days'] : '';
		$data['error_cron_key'] = isset($this->error['cron_key']) ? $this->error['cron_key'] : '';
		$data['error_object_address'] = isset($this->error['object_address']) ? $this->error['object_address'] : '';
		$data['error_object_designation'] = isset($this->error['object_designation']) ? $this->error['object_designation'] : '';
		$data['error_unit'] = isset($this->error['unit']) ? $this->error['unit'] : '';
		$data['breadcrumbs'] = array(
			array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			),
			array(
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true)
			),
			array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/feed/digital_pricelist', 'user_token=' . $this->session->data['user_token'], true)
			)
		);

		$data['action'] = $this->url->link('extension/feed/digital_pricelist', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true);
		$data['generate_url'] = $this->url->link('extension/feed/digital_pricelist/generate', 'user_token=' . $this->session->data['user_token'], true);

		foreach ($this->defaults as $key => $default) {
			if (isset($this->request->post[$key])) {
				$data[$key] = $this->request->post[$key];
			} else {
				$value = $this->config->get($key);
				$data[$key] = ($value !== null && $value !== '') ? $value : $default;
			}
		}

		if ($data['feed_digital_pricelist_cron_key'] === '') {
			$data['feed_digital_pricelist_cron_key'] = $this->generateToken();
		}

		if ($data['feed_digital_pricelist_object_address'] === '') {
			$data['feed_digital_pricelist_object_address'] = (string)$this->config->get('config_address');
		}

		$catalog_url = $this->catalogUrl();
		$data['xml_url'] = $catalog_url . 'index.php?route=extension/feed/digital_pricelist&format=xml';
		$data['csv_url'] = $catalog_url . 'index.php?route=extension/feed/digital_pricelist&format=csv';
		$data['cron_url'] = $catalog_url . 'index.php?route=extension/feed/digital_pricelist/cron&key=' . rawurlencode($data['feed_digital_pricelist_cron_key']);

		try {
			$this->load->library('digital_pricelist/generator');
			$data['pricelist_status'] = $this->generator->getStatus();
		} catch (\Throwable $exception) {
			$this->log->write('Digital pricelist admin status failed: ' . $exception->getMessage());
			$data['pricelist_status'] = array('available' => false, 'generated_at' => '', 'product_count' => 0);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/feed/digital_pricelist', $data));
	}

	public function generate() {
		$this->load->language('extension/feed/digital_pricelist');

		if (!isset($this->request->server['REQUEST_METHOD']) || $this->request->server['REQUEST_METHOD'] !== 'POST') {
			return $this->jsonResponse(array('success' => false, 'message' => $this->language->get('error_method')));
		}

		if (!$this->user->hasPermission('modify', 'extension/feed/digital_pricelist')) {
			return $this->jsonResponse(array('success' => false, 'message' => $this->language->get('error_permission')));
		}

		try {
			$this->load->library('digital_pricelist/generator');
			$result = $this->generator->generate(true);
			$this->jsonResponse(array(
				'success'       => true,
				'message'       => $this->language->get('text_generated'),
				'generated_at'  => $result['generated_at'],
				'product_count' => (int)$result['product_count']
			));
		} catch (\Throwable $exception) {
			$this->log->write('Digital pricelist manual generation failed: ' . $exception->getMessage());
			$this->jsonResponse(array('success' => false, 'message' => $this->language->get('error_generation')));
		}
	}

	public function install() {
		$settings = $this->defaults;
		$settings['feed_digital_pricelist_cron_key'] = $this->generateToken();
		$settings['feed_digital_pricelist_object_address'] = (string)$this->config->get('config_address');
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('feed_digital_pricelist', $settings);
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/feed/digital_pricelist');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/feed/digital_pricelist');
	}

	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('feed_digital_pricelist');
		// Deliberately keep archived price lists in DIR_STORAGE for legal retention.
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/feed/digital_pricelist')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$archive_days = isset($this->request->post['feed_digital_pricelist_archive_days']) ? (int)$this->request->post['feed_digital_pricelist_archive_days'] : 0;

		if ($archive_days < 30 || $archive_days > 3650) {
			$this->error['archive_days'] = $this->language->get('error_archive_days');
		}

		$key = isset($this->request->post['feed_digital_pricelist_cron_key']) ? trim((string)$this->request->post['feed_digital_pricelist_cron_key']) : '';

		if ($key !== '' && !preg_match('/^[A-Za-z0-9_-]{24,128}$/', $key)) {
			$this->error['cron_key'] = $this->language->get('error_cron_key');
		}

		$address = isset($this->request->post['feed_digital_pricelist_object_address']) ? trim((string)$this->request->post['feed_digital_pricelist_object_address']) : '';
		$designation = isset($this->request->post['feed_digital_pricelist_object_designation']) ? trim((string)$this->request->post['feed_digital_pricelist_object_designation']) : '';
		$unit = isset($this->request->post['feed_digital_pricelist_unit']) ? trim((string)$this->request->post['feed_digital_pricelist_unit']) : '';

		if ($address === '' || strlen($address) > 255) {
			$this->error['object_address'] = $this->language->get('error_object_address');
		}

		if ($designation === '' || strlen($designation) > 64) {
			$this->error['object_designation'] = $this->language->get('error_object_designation');
		}

		if ($unit === '' || strlen($unit) > 16) {
			$this->error['unit'] = $this->language->get('error_unit');
		}

		return !$this->error;
	}

	private function catalogUrl() {
		$url = defined('HTTPS_CATALOG') && HTTPS_CATALOG ? HTTPS_CATALOG : (defined('HTTP_CATALOG') ? HTTP_CATALOG : $this->config->get('config_ssl'));
		return rtrim((string)$url, '/') . '/';
	}

	private function generateToken() {
		try {
			return bin2hex(random_bytes(24));
		} catch (\Throwable $exception) {
			return hash('sha256', uniqid((string)mt_rand(), true));
		}
	}

	private function jsonResponse(array $data) {
		$this->response->addHeader('Content-Type: application/json; charset=utf-8');
		$this->response->addHeader('Cache-Control: no-store');
		$this->response->addHeader('X-Content-Type-Options: nosniff');
		$this->response->setOutput(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}
}
