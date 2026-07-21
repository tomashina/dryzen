<?php
class ControllerExtensionShippingBoxnow extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/boxnow');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('extension/shipping/boxnow');

		$this->model_extension_shipping_boxnow->installSchema();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_boxnow', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		$data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

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
			'href' => $this->url->link('extension/shipping/boxnow', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/boxnow', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		$defaults = array(
			'shipping_boxnow_api_url'          => 'https://api-production.boxnow.hr',
			'shipping_boxnow_partner_id'       => '15236',
			'shipping_boxnow_warehouse_id'     => '2',
			'shipping_boxnow_client_id'        => '',
			'shipping_boxnow_client_secret'    => '',
			'shipping_boxnow_webhook_secret'   => '',
			'shipping_boxnow_tracking_url'      => 'https://track.boxnow.hr/?track={parcel}',
			'shipping_boxnow_origin_name'      => $this->config->get('config_name'),
			'shipping_boxnow_origin_email'     => $this->config->get('config_email'),
			'shipping_boxnow_origin_phone'     => $this->config->get('config_telephone'),
			'shipping_boxnow_order_prefix'     => 'DRYZEN-',
			'shipping_boxnow_cost'             => '0.00',
			'shipping_boxnow_free_total'       => '',
			'shipping_boxnow_compartment_size' => '2',
			'shipping_boxnow_tax_class_id'     => 0,
			'shipping_boxnow_geo_zone_id'      => 0,
			'shipping_boxnow_status'           => 0,
			'shipping_boxnow_sort_order'       => 0
		);

		foreach ($defaults as $key => $default) {
			if (isset($this->request->post[$key])) {
				$data[$key] = $this->request->post[$key];
			} else {
				$value = $this->config->get($key);
				$data[$key] = ($value !== null && $value !== '') ? $value : $default;
			}
		}

		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$catalog = defined('HTTPS_CATALOG') ? HTTPS_CATALOG : HTTP_CATALOG;
		$data['webhook_url'] = sprintf($this->language->get('help_webhook_url'), $catalog . 'index.php?route=extension/shipping/boxnow/webhook');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/boxnow', $data));
	}

	public function install() {
		$this->load->model('extension/shipping/boxnow');
		$this->model_extension_shipping_boxnow->installSchema();
	}

	public function createShipment() {
		$this->load->language('extension/shipping/boxnow');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/shipping/boxnow')) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (empty($this->request->get['order_id'])) {
			$json['error'] = $this->language->get('error_not_boxnow_order');
		} else {
			try {
				$this->load->model('extension/shipping/boxnow');
				$shipment = $this->model_extension_shipping_boxnow->createShipment((int)$this->request->get['order_id']);

				$json['success'] = !empty($shipment['existing']) ? $this->language->get('text_shipment_exists') : $this->language->get('text_shipment_created');

				if (!empty($shipment['email_sent'])) {
					$json['success'] .= ' ' . $this->language->get('text_tracking_email_sent');
				} elseif (!empty($shipment['email_error'])) {
					$json['warning'] = $this->language->get('error_tracking_email_failed');
				}

				$json['parcel_id'] = isset($shipment['parcel_id']) ? $shipment['parcel_id'] : '';
				$json['reference_number'] = isset($shipment['reference_number']) ? $shipment['reference_number'] : '';
				$json['tracking_url'] = $this->model_extension_shipping_boxnow->getTrackingUrl($json['parcel_id']);
				$json['label'] = str_replace('&amp;', '&', $this->url->link('extension/shipping/boxnow/label', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true));
			} catch (Exception $e) {
				$json['error'] = $e->getMessage();
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function sendTrackingEmail() {
		$this->load->language('extension/shipping/boxnow');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/shipping/boxnow')) {
			$json['error'] = $this->language->get('error_permission');
		} elseif (empty($this->request->get['order_id'])) {
			$json['error'] = $this->language->get('error_not_boxnow_order');
		} else {
			try {
				$this->load->model('extension/shipping/boxnow');
				$result = $this->model_extension_shipping_boxnow->sendTrackingEmail((int)$this->request->get['order_id']);

				if (!empty($result['email_sent'])) {
					$json['success'] = $this->language->get('text_tracking_email_sent');
				} elseif (!empty($result['email_already_sent'])) {
					$json['success'] = $this->language->get('text_tracking_email_already_sent');
				} else {
					$json['error'] = $this->language->get('error_tracking_email_failed');
				}
			} catch (\Throwable $e) {
				$json['error'] = $this->language->get('error_tracking_email_failed');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function label() {
		$this->load->language('extension/shipping/boxnow');

		if (!$this->user->hasPermission('access', 'extension/shipping/boxnow')) {
			$this->response->redirect($this->url->link('error/permission', 'user_token=' . $this->session->data['user_token'], true));
		}

		$order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;

		try {
			$this->load->model('extension/shipping/boxnow');
			$pdf = $this->model_extension_shipping_boxnow->getLabel($order_id);

			$this->response->addHeader('Content-Type: application/pdf');
			$this->response->addHeader('Content-Disposition: inline; filename="boxnow-' . $order_id . '.pdf"');
			$this->response->setOutput($pdf);
		} catch (Exception $e) {
			$this->session->data['error_warning'] = $e->getMessage();
			$this->response->redirect($this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true));
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/boxnow')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
