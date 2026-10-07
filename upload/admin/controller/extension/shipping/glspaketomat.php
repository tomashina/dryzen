<?php
class ControllerExtensionShippingGlspaketomat extends Controller {
	private $error = array();

	public function install() {
		$this->ensureGlsOrderColumn();
		$this->load->model('setting/setting');

		$defaults = array(
			'shipping_glspaketomat_cost_xs'      => '4.50',
			'shipping_glspaketomat_cost_s'       => '5.50',
			'shipping_glspaketomat_cost_m'       => '6.50',
			'shipping_glspaketomat_cost_l'       => '8.50',
			'shipping_glspaketomat_cost_xl'      => '10.50',
			'shipping_glspaketomat_free_total'   => '50.00',
			'shipping_glspaketomat_default_size' => 'S',
			'shipping_glspaketomat_tax_class_id' => 0,
			'shipping_glspaketomat_geo_zone_id'  => 6,
			'shipping_glspaketomat_status'       => 1,
			'shipping_glspaketomat_sort_order'   => 2
		);

		$this->model_setting_setting->editSetting(
			'shipping_glspaketomat',
			array_merge($defaults, $this->model_setting_setting->getSetting('shipping_glspaketomat'))
		);
	}

	public function index() {
		$this->load->language('extension/shipping/glspaketomat');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			foreach (array('xs', 's', 'm', 'l', 'xl') as $size) {
				$key = 'shipping_glspaketomat_cost_' . $size;

				if (isset($this->request->post[$key])) {
					$this->request->post[$key] = str_replace(',', '.', trim($this->request->post[$key]));
				}
			}

			if (isset($this->request->post['shipping_glspaketomat_free_total'])) {
				$this->request->post['shipping_glspaketomat_free_total'] = str_replace(',', '.', trim($this->request->post['shipping_glspaketomat_free_total']));
			}

			if ($this->validate()) {
				$this->request->post['shipping_glspaketomat_default_size'] = strtoupper($this->request->post['shipping_glspaketomat_default_size']);
				$this->request->post['shipping_glspaketomat_tax_class_id'] = isset($this->request->post['shipping_glspaketomat_tax_class_id']) ? max(0, (int)$this->request->post['shipping_glspaketomat_tax_class_id']) : 0;
				$this->request->post['shipping_glspaketomat_geo_zone_id'] = isset($this->request->post['shipping_glspaketomat_geo_zone_id']) ? max(0, (int)$this->request->post['shipping_glspaketomat_geo_zone_id']) : 0;
				$this->request->post['shipping_glspaketomat_status'] = !empty($this->request->post['shipping_glspaketomat_status']) ? 1 : 0;
				$this->request->post['shipping_glspaketomat_sort_order'] = (int)$this->request->post['shipping_glspaketomat_sort_order'];

				$this->model_setting_setting->editSetting('shipping_glspaketomat', $this->request->post);

				$this->session->data['success'] = $this->language->get('text_success');

				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
			}
		}

		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$data['error_costs'] = isset($this->error['costs']) ? $this->error['costs'] : array();
		$data['error_default_size'] = isset($this->error['default_size']) ? $this->error['default_size'] : '';
		$data['error_free_total'] = isset($this->error['free_total']) ? $this->error['free_total'] : '';
		$data['error_sort_order'] = isset($this->error['sort_order']) ? $this->error['sort_order'] : '';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/glspaketomat', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/glspaketomat', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		$defaults = array(
			'shipping_glspaketomat_cost_xs'      => '4.50',
			'shipping_glspaketomat_cost_s'       => '5.50',
			'shipping_glspaketomat_cost_m'       => '6.50',
			'shipping_glspaketomat_cost_l'       => '8.50',
			'shipping_glspaketomat_cost_xl'      => '10.50',
			'shipping_glspaketomat_free_total'   => '50.00',
			'shipping_glspaketomat_default_size' => 'S',
			'shipping_glspaketomat_tax_class_id' => 0,
			'shipping_glspaketomat_geo_zone_id'  => 6,
			'shipping_glspaketomat_status'       => 0,
			'shipping_glspaketomat_sort_order'   => 2
		);

		foreach ($defaults as $key => $default) {
			if (isset($this->request->post[$key])) {
				$data[$key] = $this->request->post[$key];
			} else {
				$value = $this->config->get($key);
				$data[$key] = ($value !== null && $value !== '') ? $value : $default;
			}
		}

		$data['package_sizes'] = array(
			array('code' => 'XS', 'key' => 'xs', 'field' => 'shipping_glspaketomat_cost_xs', 'cost' => $data['shipping_glspaketomat_cost_xs']),
			array('code' => 'S',  'key' => 's',  'field' => 'shipping_glspaketomat_cost_s',  'cost' => $data['shipping_glspaketomat_cost_s']),
			array('code' => 'M',  'key' => 'm',  'field' => 'shipping_glspaketomat_cost_m',  'cost' => $data['shipping_glspaketomat_cost_m']),
			array('code' => 'L',  'key' => 'l',  'field' => 'shipping_glspaketomat_cost_l',  'cost' => $data['shipping_glspaketomat_cost_l']),
			array('code' => 'XL', 'key' => 'xl', 'field' => 'shipping_glspaketomat_cost_xl', 'cost' => $data['shipping_glspaketomat_cost_xl'])
		);

		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/glspaketomat', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/glspaketomat')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach (array('xs', 's', 'm', 'l', 'xl') as $size) {
			$key = 'shipping_glspaketomat_cost_' . $size;
			$cost = isset($this->request->post[$key]) ? $this->request->post[$key] : '';

			if ($cost === '' || !is_numeric($cost) || (float)$cost < 0) {
				$this->error['costs'][$size] = sprintf($this->language->get('error_cost'), strtoupper($size));
			}
		}

		$default_size = isset($this->request->post['shipping_glspaketomat_default_size']) ? strtoupper(trim($this->request->post['shipping_glspaketomat_default_size'])) : '';

		if (!in_array($default_size, array('XS', 'S', 'M', 'L', 'XL'), true)) {
			$this->error['default_size'] = $this->language->get('error_default_size');
		}

		$free_total = isset($this->request->post['shipping_glspaketomat_free_total']) ? trim((string)$this->request->post['shipping_glspaketomat_free_total']) : '';

		if ($free_total === '' || !is_numeric($free_total) || (float)$free_total < 0) {
			$this->error['free_total'] = $this->language->get('error_free_total');
		}

		$sort_order = isset($this->request->post['shipping_glspaketomat_sort_order']) ? trim((string)$this->request->post['shipping_glspaketomat_sort_order']) : '';

		if ($sort_order === '' || !preg_match('/^\d+$/', $sort_order)) {
			$this->error['sort_order'] = $this->language->get('error_sort_order');
		}

		return !$this->error;
	}

	private function ensureGlsOrderColumn() {
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'gls_ps'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `gls_ps` TEXT NULL AFTER `shipping_code`");
		}
	}
}
