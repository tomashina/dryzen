<?php
class ControllerExtensionShippingFreeByZone extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/free_by_zone');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$rules = isset($this->request->post['shipping_free_by_zone_rules']) ? $this->request->post['shipping_free_by_zone_rules'] : array();

			$this->request->post['shipping_free_by_zone_rules'] = $this->normaliseRules($rules);
			$this->request->post['shipping_free_by_zone_status'] = isset($this->request->post['shipping_free_by_zone_status']) ? (int)$this->request->post['shipping_free_by_zone_status'] : 0;
			$this->request->post['shipping_free_by_zone_sort_order'] = isset($this->request->post['shipping_free_by_zone_sort_order']) ? (int)$this->request->post['shipping_free_by_zone_sort_order'] : 0;
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_free_by_zone', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
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
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/free_by_zone', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/free_by_zone', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		if (isset($this->request->post['shipping_free_by_zone_rules'])) {
			$data['shipping_free_by_zone_rules'] = $this->request->post['shipping_free_by_zone_rules'];
		} else {
			$data['shipping_free_by_zone_rules'] = $this->config->get('shipping_free_by_zone_rules');
		}

		if (!is_array($data['shipping_free_by_zone_rules'])) {
			$data['shipping_free_by_zone_rules'] = array(
				array(
					'geo_zone_id' => 0,
					'total'       => '0.00',
					'status'      => 1
				)
			);
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['shipping_free_by_zone_status'])) {
			$data['shipping_free_by_zone_status'] = $this->request->post['shipping_free_by_zone_status'];
		} else {
			$data['shipping_free_by_zone_status'] = $this->config->get('shipping_free_by_zone_status');
		}

		if (isset($this->request->post['shipping_free_by_zone_sort_order'])) {
			$data['shipping_free_by_zone_sort_order'] = $this->request->post['shipping_free_by_zone_sort_order'];
		} else {
			$data['shipping_free_by_zone_sort_order'] = $this->config->get('shipping_free_by_zone_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/free_by_zone', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/free_by_zone')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error && !empty($this->request->post['shipping_free_by_zone_status'])) {
			$enabled_rules = 0;
			$geo_zone_ids = array();

			foreach ($this->request->post['shipping_free_by_zone_rules'] as $index => $rule) {
				if (empty($rule['status'])) {
					continue;
				}

				$enabled_rules++;

				if (!is_numeric($rule['total']) || (float)$rule['total'] < 0) {
					$this->error['warning'] = sprintf($this->language->get('error_total'), $index + 1);
					break;
				}

				$geo_zone_id = (int)$rule['geo_zone_id'];

				if (isset($geo_zone_ids[$geo_zone_id])) {
					$this->error['warning'] = sprintf($this->language->get('error_duplicate'), $index + 1);
					break;
				}

				$geo_zone_ids[$geo_zone_id] = true;
			}

			if (!$this->error && !$enabled_rules) {
				$this->error['warning'] = $this->language->get('error_rules');
			}
		}

		return !$this->error;
	}

	protected function normaliseRules($rules) {
		$normalised = array();

		if (!is_array($rules)) {
			return $normalised;
		}

		foreach ($rules as $rule) {
			if (!is_array($rule)) {
				continue;
			}

			$total = isset($rule['total']) ? trim(str_replace(',', '.', $rule['total'])) : '';

			$normalised[] = array(
				'geo_zone_id' => isset($rule['geo_zone_id']) ? (int)$rule['geo_zone_id'] : 0,
				'total'       => $total,
				'status'      => !empty($rule['status']) ? 1 : 0
			);
		}

		return $normalised;
	}
}
