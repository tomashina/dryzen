<?php
class ModelExtensionShippingBoxnow extends Model {
	public function installSchema() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "boxnow_shipment` (
			`boxnow_shipment_id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`order_number` varchar(64) NOT NULL,
			`reference_number` varchar(128) NOT NULL DEFAULT '',
			`parcel_id` varchar(64) NOT NULL DEFAULT '',
			`locker_id` varchar(64) NOT NULL DEFAULT '',
			`status` varchar(64) NOT NULL DEFAULT '',
			`email_sent_at` datetime NULL DEFAULT NULL,
			`email_error` text NULL,
			`payload` mediumtext,
			`response` mediumtext,
			`webhook_payload` mediumtext,
			`date_added` datetime NOT NULL,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`boxnow_shipment_id`),
			KEY `order_id` (`order_id`),
			KEY `parcel_id` (`parcel_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "order` LIKE 'boxnow'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "order` ADD `boxnow` TEXT NULL AFTER `shipping_code`");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "boxnow_shipment` LIKE 'email_sent_at'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "boxnow_shipment` ADD `email_sent_at` datetime NULL DEFAULT NULL AFTER `status`");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "boxnow_shipment` LIKE 'email_error'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "boxnow_shipment` ADD `email_error` text NULL AFTER `email_sent_at`");
		}
	}

	public function getShipmentByOrderId($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "boxnow_shipment` WHERE order_id = '" . (int)$order_id . "' ORDER BY boxnow_shipment_id DESC LIMIT 1");

		return $query->num_rows ? $query->row : array();
	}

	public function createShipment($order_id) {
		$this->installSchema();

		$existing = $this->getShipmentByOrderId($order_id);

		if (!empty($existing['parcel_id'])) {
			$email_result = $this->sendTrackingEmail($order_id);
			$existing = array_merge($this->getShipmentByOrderId($order_id), $email_result);
			$existing['existing'] = true;
			return $existing;
		}

		$client_id = trim($this->getConfig('client_id'));
		$client_secret = trim($this->getConfig('client_secret'));

		if ($client_id === '' || $client_secret === '') {
			throw new Exception($this->language->get('error_missing_credentials'));
		}

		$this->load->model('sale/order');

		$order_info = $this->model_sale_order->getOrder($order_id);

		if (!$order_info || $order_info['shipping_code'] !== 'boxnow.boxnow') {
			throw new Exception($this->language->get('error_not_boxnow_order'));
		}

		$locker_id = $this->extractLockerId(isset($order_info['boxnow']) ? $order_info['boxnow'] : '');

		if ($locker_id === '') {
			throw new Exception($this->language->get('error_missing_locker'));
		}

		$order_number = $this->getOrderNumber($order_info);
		$is_cod = $this->isCashOnDelivery($order_info);
		$total = number_format((float)$order_info['total'], 2, '.', '');
		$weight = $this->getOrderWeight($order_id);

		$item = array(
			'id'     => $order_number . '-1',
			'name'   => 'Order ' . $order_number,
			'value'  => $total,
			'weight' => $weight
		);

		$compartment_size = (int)$this->getConfig('compartment_size', 2);

		if ($compartment_size > 0) {
			$item['compartmentSize'] = $compartment_size;
		}

		$payload = array(
			'orderNumber'         => $order_number,
			'invoiceValue'        => $total,
			'paymentMode'         => $is_cod ? 'cod' : 'prepaid',
			'amountToBeCollected' => $is_cod ? $total : '0.00',
			'allowReturn'         => true,
			'origin'              => array(
				'contactNumber' => $this->normalizePhone($this->getConfig('origin_phone', $this->config->get('config_telephone'))),
				'contactEmail'  => $this->getConfig('origin_email', $this->config->get('config_email')),
				'contactName'   => $this->getConfig('origin_name', $this->config->get('config_name')),
				'locationId'    => (string)$this->getConfig('warehouse_id', '2')
			),
			'destination'         => array(
				'contactNumber' => $this->normalizePhone($order_info['telephone']),
				'contactEmail'  => $order_info['email'],
				'contactName'   => trim($order_info['firstname'] . ' ' . $order_info['lastname']),
				'locationId'    => $locker_id
			),
			'items'               => array($item)
		);

		$response = $this->apiRequest('POST', '/api/v1/delivery-requests', $payload);

		$reference_number = isset($response['referenceNumber']) ? $response['referenceNumber'] : '';
		$parcel_id = '';

		if (!empty($response['parcels']) && isset($response['parcels'][0]['id'])) {
			$parcel_id = $response['parcels'][0]['id'];
		}

		if ($parcel_id === '') {
			throw new Exception($this->language->get('error_missing_parcel_id'));
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "boxnow_shipment` SET order_id = '" . (int)$order_id . "', order_number = '" . $this->db->escape($order_number) . "', reference_number = '" . $this->db->escape($reference_number) . "', parcel_id = '" . $this->db->escape($parcel_id) . "', locker_id = '" . $this->db->escape($locker_id) . "', status = 'new', payload = '" . $this->db->escape(json_encode($payload)) . "', response = '" . $this->db->escape(json_encode($response)) . "', date_added = NOW(), date_modified = NOW()");

		$email_result = $this->sendTrackingEmail($order_id);

		return array_merge($this->getShipmentByOrderId($order_id), $email_result);
	}

	public function sendTrackingEmail($order_id) {
		$this->installSchema();

		$shipment = $this->getShipmentByOrderId($order_id);

		if (empty($shipment['parcel_id'])) {
			return array(
				'email_sent'  => false,
				'email_error' => $this->language->get('error_missing_parcel_id')
			);
		}

		if (!empty($shipment['email_sent_at'])) {
			return array(
				'email_sent'         => false,
				'email_already_sent' => true,
				'email_error'        => ''
			);
		}

		$this->load->model('sale/order');
		$order_info = $this->model_sale_order->getOrder($order_id);

		if (!$order_info || empty($order_info['email'])) {
			$error = $this->language->get('error_missing_customer_email');
			$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET email_error = '" . $this->db->escape($error) . "' WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");

			return array(
				'email_sent'  => false,
				'email_error' => $error
			);
		}

		$language_code = !empty($order_info['language_code']) ? $order_info['language_code'] : $this->config->get('config_language');
		$language = new Language($language_code);
		$language->load('extension/shipping/boxnow');

		$store_url = !empty($order_info['store_url']) ? $order_info['store_url'] : (defined('HTTPS_CATALOG') ? HTTPS_CATALOG : HTTP_CATALOG);
		$store_url = rtrim($store_url, '/') . '/';
		$store_name = !empty($order_info['store_name']) ? $order_info['store_name'] : $this->config->get('config_name');
		$order_number = !empty($order_info['number_order']) ? $order_info['number_order'] : $order_info['order_id'];
		$tracking_url = $this->getTrackingUrl($shipment['parcel_id']);
		$status = $this->getStatusLabel($shipment['status'], $language_code);

		$data = array(
			'title'                => sprintf($language->get('mail_subject'), $store_name),
			'logo'                 => $store_url . 'image/' . $this->config->get('config_logo'),
			'store_name'           => $store_name,
			'store_url'            => $store_url,
			'firstname'            => $order_info['firstname'],
			'order_number'         => $order_number,
			'tracking_code'        => $shipment['parcel_id'],
			'tracking_url'         => $tracking_url,
			'tracking_status'      => $status,
			'shipping_method'      => $order_info['shipping_method'],
			'account_order_url'    => !empty($order_info['customer_id']) ? $store_url . 'index.php?route=account/order/info&order_id=' . (int)$order_id : '',
			'mail_heading'         => $language->get('mail_heading'),
			'mail_greeting'        => sprintf($language->get('mail_greeting'), $order_info['firstname']),
			'mail_intro'           => sprintf($language->get('mail_intro'), $order_number),
			'mail_tracking_code'   => $language->get('mail_tracking_code'),
			'mail_tracking_status' => $language->get('mail_tracking_status'),
			'mail_shipping_method' => $language->get('mail_shipping_method'),
			'mail_track_button'    => $language->get('mail_track_button'),
			'mail_order_button'    => $language->get('mail_order_button'),
			'mail_note'            => $language->get('mail_note'),
			'mail_footer'          => sprintf($language->get('mail_footer'), $store_name)
		);

		$this->load->model('setting/setting');
		$from = $this->model_setting_setting->getSettingValue('config_email', $order_info['store_id']);

		if (!$from) {
			$from = $this->config->get('config_email');
		}

		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
		$mail->setTo($order_info['email']);
		$mail->setFrom($from);
		$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode(sprintf($language->get('mail_subject'), $store_name), ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($this->load->view('mail/boxnow_tracking', $data));
		$mail->setText($this->buildTrackingEmailText($data));

		try {
			$mail->send();

			$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET email_sent_at = NOW(), email_error = NULL WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_info['order_status_id'] . "', notify = '0', comment = '" . $this->db->escape($language->get('text_tracking_email_history') . ' ' . $shipment['parcel_id']) . "', date_added = NOW()");

			return array(
				'email_sent'  => true,
				'email_error' => ''
			);
		} catch (\Throwable $exception) {
			$error = $exception->getMessage();
			$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET email_error = '" . $this->db->escape($error) . "' WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");
			$this->log->write('BOX NOW tracking mail failed (order ' . (int)$order_id . '): ' . $error);

			return array(
				'email_sent'  => false,
				'email_error' => $error
			);
		}
	}

	public function getTrackingUrl($parcel_id) {
		$parcel_id = trim((string)$parcel_id);

		if ($parcel_id === '') {
			return '';
		}

		$base_url = trim((string)$this->getConfig('tracking_url', 'https://track.boxnow.hr/?track={parcel}'));

		if ($base_url === '') {
			return '';
		}

		if (strpos($base_url, '{parcel}') !== false) {
			return str_replace('{parcel}', rawurlencode($parcel_id), $base_url);
		}

		if (strpos($base_url, 'track.boxnow.hr') !== false) {
			$base_url = preg_replace('#/track/?$#', '', rtrim($base_url, '/'));

			if (preg_match('/([?&]track=)([^&]*)/', $base_url)) {
				return preg_replace('/([?&]track=)([^&]*)/', '$1' . rawurlencode($parcel_id), $base_url);
			}

			return $base_url . (strpos($base_url, '?') !== false ? '&' : '?') . 'track=' . rawurlencode($parcel_id);
		}

		return rtrim($base_url, '/') . '/' . rawurlencode($parcel_id);
	}

	public function getStatusLabel($status, $language_code = '') {
		$status = strtolower(trim((string)$status));
		$language = new Language($language_code !== '' ? $language_code : $this->config->get('config_language'));
		$language->load('extension/shipping/boxnow');
		$keys = array(
			'created'             => 'status_created',
			'new'                 => 'status_new',
			'in-depot'            => 'status_in_transit',
			'in-transit'          => 'status_in_transit',
			'final-destination'   => 'status_final_destination',
			'delivered'           => 'status_delivered',
			'returned'            => 'status_returned',
			'expired'             => 'status_expired',
			'expired-return'      => 'status_expired',
			'canceled'            => 'status_canceled',
			'cancelled'           => 'status_canceled',
			'lost'                => 'status_missing',
			'missing'             => 'status_missing',
			'accepted-to-locker'  => 'status_in_progress',
			'accepted-for-return' => 'status_in_progress',
			'wait-for-load'       => 'status_wait_for_load'
		);

		if (isset($keys[$status])) {
			return $language->get($keys[$status]);
		}

		return $status !== '' ? sprintf($language->get('status_unknown'), $status) : $language->get('status_unavailable');
	}

	public function getLabel($order_id) {
		$this->installSchema();

		$shipment = $this->getShipmentByOrderId($order_id);

		if (empty($shipment['parcel_id'])) {
			throw new Exception($this->language->get('error_missing_locker'));
		}

		return $this->apiRequest('GET', '/api/v1/parcels/' . rawurlencode($shipment['parcel_id']) . '/label.pdf?type=pdf', null, true);
	}

	private function getAccessToken() {
		$response = $this->apiRequest('POST', '/api/v1/auth-sessions', array(
			'grant_type'    => 'client_credentials',
			'client_id'     => $this->getConfig('client_id'),
			'client_secret' => $this->getConfig('client_secret')
		), false, false);

		if (empty($response['access_token'])) {
			throw new Exception('BOX NOW API: access token missing.');
		}

		return $response['access_token'];
	}

	private function apiRequest($method, $endpoint, $payload = null, $binary = false, $authenticated = true) {
		if (!function_exists('curl_init')) {
			throw new Exception('BOX NOW API: cURL is not available.');
		}

		$url = rtrim($this->getConfig('api_url', 'https://api-production.boxnow.hr'), '/') . $endpoint;
		$headers = array(
			'Accept: ' . ($binary ? 'application/pdf' : 'application/json')
		);

		$partner_id = $this->getConfig('partner_id', '15236');

		if ($partner_id !== '') {
			$headers[] = 'X-PartnerID: ' . $partner_id;
		}

		if ($authenticated) {
			$headers[] = 'Authorization: Bearer ' . $this->getAccessToken();
		}

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_TIMEOUT, 45);

		if ($payload !== null) {
			$headers[] = 'Content-Type: application/json';
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		}

		$body = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		if ($error) {
			throw new Exception('BOX NOW API: ' . $error);
		}

		if ($http_code < 200 || $http_code >= 300) {
			throw new Exception('BOX NOW API HTTP ' . $http_code . ': ' . $body);
		}

		if ($binary) {
			return $body;
		}

		$json = json_decode($body, true);

		if (!is_array($json)) {
			throw new Exception('BOX NOW API returned invalid JSON: ' . $body);
		}

		return $json;
	}

	private function getConfig($key, $default = '') {
		$value = $this->config->get('shipping_boxnow_' . $key);

		return ($value !== null && $value !== '') ? $value : $default;
	}

	private function getOrderNumber($order_info) {
		$number = !empty($order_info['number_order']) ? $order_info['number_order'] : $order_info['order_id'];

		return $this->getConfig('order_prefix', 'DRYZEN-') . $number;
	}

	private function getOrderWeight($order_id) {
		$query = $this->db->query("SELECT SUM(IFNULL(p.weight, 0) * op.quantity) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "product` p ON (p.product_id = op.product_id) WHERE op.order_id = '" . (int)$order_id . "'");
		$weight = isset($query->row['total']) ? (float)$query->row['total'] : 0.0;

		if ($weight <= 0) {
			$weight = 1.0;
		}

		return (float)number_format($weight, 3, '.', '');
	}

	private function extractLockerId($boxnow) {
		$boxnow = trim((string)$boxnow);

		if ($boxnow === '' || stripos($boxnow, 'undefined') !== false) {
			return '';
		}

		if (strpos($boxnow, ';') !== false) {
			$parts = explode(';', $boxnow);
			return trim(end($parts));
		}

		if (strpos($boxnow, '_') !== false) {
			$parts = explode('_', $boxnow);
			return trim(end($parts));
		}

		return $boxnow;
	}

	private function normalizePhone($phone) {
		$phone = preg_replace('/[^0-9+]/', '', (string)$phone);

		if (strpos($phone, '00') === 0) {
			$phone = '+' . substr($phone, 2);
		}

		if (strpos($phone, '0') === 0) {
			$phone = '+385' . substr($phone, 1);
		}

		if ($phone !== '' && strpos($phone, '+') !== 0) {
			$phone = '+' . $phone;
		}

		return $phone;
	}

	private function isCashOnDelivery($order_info) {
		$payment_code = strtolower((string)$order_info['payment_code']);
		$payment_method = strtolower((string)$order_info['payment_method']);

		return $payment_code === 'cod' || strpos($payment_code, 'cod') !== false || strpos($payment_method, 'pouze') !== false;
	}

	private function buildTrackingEmailText($data) {
		$text = $data['mail_heading'] . "\n\n";
		$text .= $data['mail_greeting'] . "\n";
		$text .= strip_tags($data['mail_intro']) . "\n\n";
		$text .= $data['mail_tracking_code'] . ': ' . $data['tracking_code'] . "\n";
		$text .= $data['mail_tracking_status'] . ': ' . $data['tracking_status'] . "\n";
		$text .= $data['mail_shipping_method'] . ': ' . $data['shipping_method'] . "\n\n";

		if ($data['tracking_url'] !== '') {
			$text .= $data['mail_track_button'] . ': ' . $data['tracking_url'] . "\n";
		}

		if ($data['account_order_url'] !== '') {
			$text .= $data['mail_order_button'] . ': ' . $data['account_order_url'] . "\n";
		}

		$text .= "\n";
		$text .= $data['mail_note'] . "\n\n";
		$text .= $data['mail_footer'];

		return $text;
	}
}
