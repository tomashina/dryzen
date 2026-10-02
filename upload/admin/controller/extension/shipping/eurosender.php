<?php
class ControllerExtensionShippingEurosender extends Controller {
	private $error = array();

	public function index() {
		$data = $this->load->language('extension/shipping/eurosender');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->request->post = $this->sanitizeSettings($this->request->post);
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_eurosender', $this->request->post);

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
			'href' => $this->url->link('extension/shipping/eurosender', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/eurosender', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		$origin = $this->getOriginDefaults();
		$defaults = array(
			'shipping_eurosender_environment'        => 'sandbox',
			'shipping_eurosender_service_types'      => array('selection', 'regular_plus', 'express'),
			'shipping_eurosender_payment_method'     => 'credit',
			'shipping_eurosender_length'             => '20',
			'shipping_eurosender_width'              => '15',
			'shipping_eurosender_height'             => '10',
			'shipping_eurosender_packaging_weight'   => '0.20',
			'shipping_eurosender_fallback_item_weight' => '0.10',
			'shipping_eurosender_minimum_weight'     => '0.50',
			'shipping_eurosender_content'            => 'cosmetics',
			'shipping_eurosender_markup_type'        => 'fixed',
			'shipping_eurosender_markup_value'       => '0.00',
			'shipping_eurosender_fallback_rate'      => '',
			'shipping_eurosender_origin_name'        => $origin['name'],
			'shipping_eurosender_origin_email'       => $origin['email'],
			'shipping_eurosender_origin_phone'       => $origin['phone'],
			'shipping_eurosender_origin_address_1'   => $origin['address_1'],
			'shipping_eurosender_origin_address_2'   => $origin['address_2'],
			'shipping_eurosender_origin_city'        => $origin['city'],
			'shipping_eurosender_origin_postcode'    => $origin['postcode'],
			'shipping_eurosender_origin_country_code' => $origin['country_code'],
			'shipping_eurosender_tax_class_id'       => 0,
			'shipping_eurosender_geo_zone_id'        => 0,
			'shipping_eurosender_status'             => 0,
			'shipping_eurosender_sort_order'         => 0
		);

		foreach ($defaults as $key => $default) {
			if (isset($this->request->post[$key])) {
				$data[$key] = $this->request->post[$key];
			} else {
				$value = $this->config->get($key);
				$data[$key] = ($value !== null && $value !== '') ? $value : $default;
			}
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && !isset($this->request->post['shipping_eurosender_service_types'])) {
			$data['shipping_eurosender_service_types'] = array();
		}

		$data['service_types'] = array(
			'selection'    => $this->language->get('text_service_selection'),
			'regular_plus' => $this->language->get('text_service_regular_plus'),
			'express'      => $this->language->get('text_service_express')
		);
		$data['api_key_configured'] = $this->isApiKeyConfigured();

		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/eurosender', $data));
	}

	public function install() {
		$this->load->model('extension/shipping/eurosender');
		$this->model_extension_shipping_eurosender->installSchema();
	}

	public function previewShipment() {
		$this->load->language('extension/shipping/eurosender');
		$json = array();

		if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
			$json['error'] = $this->language->get('error_post_required');
		} elseif (!$this->user->hasPermission('modify', 'extension/shipping/eurosender') || !$this->user->hasPermission('modify', 'sale/order')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;

			if ($order_id < 1) {
				$json['error'] = $this->language->get('error_order_id');
			} else {
				try {
					$this->load->model('extension/shipping/eurosender');
					$preview = $this->model_extension_shipping_eurosender->previewShipment($order_id);
					$quote_price = number_format((float)$preview['quote_price'], 2, ',', '.');
					$currency = isset($preview['currency_code']) ? $preview['currency_code'] : 'EUR';
					$service_type = isset($preview['service_type']) ? (string)$preview['service_type'] : '';
					$service_key = 'text_service_' . $service_type;
					$service_label = $this->language->get($service_key);

					if ($service_label === $service_key) {
						$service_label = $service_type !== '' ? $service_type : 'Eurosender';
					}

					$json['confirmation_token'] = $preview['confirmation_token'];

					if (isset($preview['checkout_shipping_price']) && $preview['checkout_shipping_price'] !== null) {
						$checkout_price = (float)$preview['checkout_shipping_price'];
						$difference = (float)$preview['quote_price'] - $checkout_price;
						$json['confirmation'] = sprintf(
							$this->language->get('text_booking_price_confirmation'),
							$service_label,
							$quote_price,
							$currency,
							number_format($checkout_price, 2, ',', '.'),
							$currency,
							number_format($difference, 2, ',', '.'),
							$currency
						);
					} else {
						$json['confirmation'] = sprintf($this->language->get('text_booking_price_only_confirmation'), $service_label, $quote_price, $currency);
					}
				} catch (\Throwable $exception) {
					$json['error'] = $exception->getMessage();
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function createShipment() {
		$this->load->language('extension/shipping/eurosender');
		$json = array();

		if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
			$json['error'] = $this->language->get('error_post_required');
		} elseif (!$this->user->hasPermission('modify', 'extension/shipping/eurosender') || !$this->user->hasPermission('modify', 'sale/order')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;

			if ($order_id < 1) {
				$json['error'] = $this->language->get('error_order_id');
			} else {
				try {
					$this->load->model('extension/shipping/eurosender');
					$confirmation_token = isset($this->request->post['confirmation_token']) ? (string)$this->request->post['confirmation_token'] : '';
					$shipment = $this->model_extension_shipping_eurosender->createShipment($order_id, $confirmation_token);
					$json['success'] = !empty($shipment['existing']) ? $this->language->get('text_shipment_exists') : $this->language->get('text_shipment_created');
					$json['order_code'] = isset($shipment['order_code']) ? $shipment['order_code'] : '';

					if (isset($shipment['price_difference']) && $shipment['price_difference'] !== null && abs((float)$shipment['price_difference']) >= 0.01) {
						$json['warning'] = sprintf($this->language->get('text_price_changed'), number_format((float)$shipment['price_difference'], 2, ',', '.'), isset($shipment['currency_code']) ? $shipment['currency_code'] : 'EUR');
					}
				} catch (\Throwable $exception) {
					$json['error'] = $exception->getMessage();
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function refreshTracking() {
		$this->load->language('extension/shipping/eurosender');
		$json = array();

		if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
			$json['error'] = $this->language->get('error_post_required');
		} elseif (!$this->user->hasPermission('modify', 'extension/shipping/eurosender') || !$this->user->hasPermission('access', 'sale/order')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			$order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;

			if ($order_id < 1) {
				$json['error'] = $this->language->get('error_order_id');
			} else {
				try {
					$this->load->model('extension/shipping/eurosender');
					$shipment = $this->model_extension_shipping_eurosender->refreshTracking($order_id);
					$json['success'] = $this->language->get('text_tracking_refreshed');
					$json['status'] = $this->model_extension_shipping_eurosender->getStatusLabel(isset($shipment['status']) ? $shipment['status'] : '');
				} catch (\Throwable $exception) {
					$json['error'] = $exception->getMessage();
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function label() {
		$this->load->language('extension/shipping/eurosender');

		if (!$this->user->hasPermission('access', 'extension/shipping/eurosender') || !$this->user->hasPermission('access', 'sale/order')) {
			$this->response->redirect($this->url->link('error/permission', 'user_token=' . $this->session->data['user_token'], true));
			return;
		}

		$order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;

		try {
			$this->load->model('extension/shipping/eurosender');
			$pdf = $this->model_extension_shipping_eurosender->getLabel($order_id);
			$this->response->addHeader('Content-Type: application/pdf');
			$this->response->addHeader('Content-Disposition: inline; filename="eurosender-' . $order_id . '.pdf"');
			$this->response->addHeader('Cache-Control: private, no-store');
			$this->response->setOutput($pdf);
		} catch (\Throwable $exception) {
			$this->session->data['error_warning'] = $exception->getMessage();
			$this->response->redirect($this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true));
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/eurosender')) {
			$this->error['warning'] = $this->language->get('error_permission');
			return false;
		}

		$post = $this->request->post;
		$environment = isset($post['shipping_eurosender_environment']) ? $post['shipping_eurosender_environment'] : '';
		$payment_method = isset($post['shipping_eurosender_payment_method']) ? $post['shipping_eurosender_payment_method'] : '';
		$markup_type = isset($post['shipping_eurosender_markup_type']) ? $post['shipping_eurosender_markup_type'] : '';
		$service_types = isset($post['shipping_eurosender_service_types']) ? (array)$post['shipping_eurosender_service_types'] : array();

		if (!in_array($environment, array('sandbox', 'production'), true)) {
			$this->error['warning'] = $this->language->get('error_environment');
		} elseif (!$service_types) {
			$this->error['warning'] = $this->language->get('error_service_types');
		} elseif (!in_array($payment_method, array('credit', 'deferred'), true)) {
			$this->error['warning'] = $this->language->get('error_payment_method');
		} elseif (!in_array($markup_type, array('fixed', 'percent'), true)) {
			$this->error['warning'] = $this->language->get('error_markup_type');
		}

		$positive_fields = array(
			'shipping_eurosender_length',
			'shipping_eurosender_width',
			'shipping_eurosender_height',
			'shipping_eurosender_minimum_weight'
		);

		foreach ($positive_fields as $field) {
			if (!isset($post[$field]) || !is_numeric($post[$field]) || (float)$post[$field] <= 0) {
				$this->error['warning'] = $this->language->get('error_positive_number');
				break;
			}
		}

		$non_negative_fields = array(
			'shipping_eurosender_packaging_weight',
			'shipping_eurosender_fallback_item_weight',
			'shipping_eurosender_markup_value'
		);

		foreach ($non_negative_fields as $field) {
			if (!isset($post[$field]) || !is_numeric($post[$field]) || (float)$post[$field] < 0) {
				$this->error['warning'] = $this->language->get('error_non_negative_number');
				break;
			}
		}

		if (isset($post['shipping_eurosender_fallback_rate']) && $post['shipping_eurosender_fallback_rate'] !== '' && (!is_numeric($post['shipping_eurosender_fallback_rate']) || (float)$post['shipping_eurosender_fallback_rate'] < 0)) {
			$this->error['warning'] = $this->language->get('error_fallback_rate');
		}

		$country_code = isset($post['shipping_eurosender_origin_country_code']) ? strtoupper(trim($post['shipping_eurosender_origin_country_code'])) : '';

		if (!preg_match('/^[A-Z]{2}$/', $country_code)) {
			$this->error['warning'] = $this->language->get('error_country_code');
		}

		if (!empty($post['shipping_eurosender_status'])) {
			if (empty($post['shipping_eurosender_origin_name']) || empty($post['shipping_eurosender_origin_phone'])) {
				$this->error['warning'] = $this->language->get('error_origin_contact');
			} elseif (empty($post['shipping_eurosender_origin_email']) || !filter_var($post['shipping_eurosender_origin_email'], FILTER_VALIDATE_EMAIL)) {
				$this->error['warning'] = $this->language->get('error_origin_email');
			}

			foreach (array('shipping_eurosender_origin_address_1', 'shipping_eurosender_origin_city', 'shipping_eurosender_origin_postcode') as $field) {
				if (empty($post[$field])) {
					$this->error['warning'] = $this->language->get('error_origin_address');
					break;
				}
			}
		}

		return !$this->error;
	}

	private function sanitizeSettings($post) {
		$allowed_services = array('selection', 'regular_plus', 'express');
		$services = isset($post['shipping_eurosender_service_types']) ? (array)$post['shipping_eurosender_service_types'] : array();
		$post['shipping_eurosender_service_types'] = array_values(array_intersect($allowed_services, $services));

		$trim_fields = array(
			'shipping_eurosender_content',
			'shipping_eurosender_fallback_rate',
			'shipping_eurosender_origin_name',
			'shipping_eurosender_origin_email',
			'shipping_eurosender_origin_phone',
			'shipping_eurosender_origin_address_1',
			'shipping_eurosender_origin_address_2',
			'shipping_eurosender_origin_city',
			'shipping_eurosender_origin_postcode'
		);

		foreach ($trim_fields as $field) {
			if (isset($post[$field])) {
				$post[$field] = trim((string)$post[$field]);
			}
		}

		if (isset($post['shipping_eurosender_origin_country_code'])) {
			$post['shipping_eurosender_origin_country_code'] = strtoupper(trim((string)$post['shipping_eurosender_origin_country_code']));
		}

		return $post;
	}

	private function isApiKeyConfigured() {
		return defined('OC_ENV')
			&& is_array(OC_ENV)
			&& isset(OC_ENV['eurosender'])
			&& is_array(OC_ENV['eurosender'])
			&& !empty(OC_ENV['eurosender']['api_key']);
	}

	private function getOriginDefaults() {
		$address = preg_split('/\r\n|\r|\n/', trim((string)$this->config->get('config_address')));
		$address = array_values(array_filter(array_map('trim', $address), 'strlen'));
		$owner = trim((string)$this->config->get('config_owner'));
		$company = trim((string)$this->config->get('config_name'));
		$address_lines = array();
		$city = '';
		$postcode = '';

		foreach ($address as $line) {
			if ($this->sameText($line, $owner) || $this->sameText($line, $company) || preg_match('/^(?:OIB|MBS)\b/iu', $line)) {
				continue;
			}

			if ($postcode === '' && preg_match('/^([0-9]{4,10})\s+(.+?)(?:,\s*[^,]+)?$/u', $line, $matches)) {
				$postcode = $matches[1];
				$city = trim($matches[2]);
				continue;
			}

			$address_lines[] = $line;
		}
		$country_code = '';
		$country_id = (int)$this->config->get('config_country_id');

		if ($country_id) {
			$this->load->model('localisation/country');
			$country = $this->model_localisation_country->getCountry($country_id);

			if ($country && !empty($country['iso_code_2'])) {
				$country_code = strtoupper($country['iso_code_2']);
			}
		}

		return array(
			'name'         => $owner !== '' ? $owner : $company,
			'email'        => (string)$this->config->get('config_email'),
			'phone'        => (string)$this->config->get('config_telephone'),
			'address_1'    => isset($address_lines[0]) ? $address_lines[0] : '',
			'address_2'    => count($address_lines) > 1 ? implode(', ', array_slice($address_lines, 1)) : '',
			'city'         => $city,
			'postcode'     => $postcode,
			'country_code' => $country_code
		);
	}

	private function sameText($left, $right) {
		if ($right === '') {
			return false;
		}

		if (function_exists('mb_strtolower')) {
			return mb_strtolower(trim((string)$left), 'UTF-8') === mb_strtolower(trim((string)$right), 'UTF-8');
		}

		return strcasecmp(trim((string)$left), trim((string)$right)) === 0;
	}
}
