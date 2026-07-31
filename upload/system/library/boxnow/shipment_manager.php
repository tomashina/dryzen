<?php
namespace Boxnow;

class ShipmentManager {
	private $registry;
	protected $db;
	protected $config;
	protected $log;
	private $schema_installed = false;

	public function __construct($registry) {
		$this->registry = $registry;
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
		$this->log = $registry->get('log');

		if ($registry->has('load')) {
			$registry->get('load')->language('extension/shipping/boxnow');
		}
	}

	public function installSchema() {
		if ($this->schema_installed) {
			return;
		}

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "boxnow_shipment` (
			`boxnow_shipment_id` int(11) NOT NULL AUTO_INCREMENT,
			`order_id` int(11) NOT NULL,
			`order_number` varchar(64) NOT NULL,
			`reference_number` varchar(128) NOT NULL DEFAULT '',
			`parcel_id` varchar(64) NOT NULL DEFAULT '',
			`locker_id` varchar(64) NOT NULL DEFAULT '',
			`status` varchar(64) NOT NULL DEFAULT '',
			`creation_attempted_at` datetime NULL DEFAULT NULL,
			`creation_error` text NULL,
			`email_sent_at` datetime NULL DEFAULT NULL,
			`email_error` text NULL,
			`label_email_sent_at` datetime NULL DEFAULT NULL,
			`label_email_error` text NULL,
			`label_email_recipients` text NULL,
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

		$this->addShipmentColumn('creation_attempted_at', "datetime NULL DEFAULT NULL AFTER `status`");
		$this->addShipmentColumn('creation_error', "text NULL AFTER `creation_attempted_at`");
		$this->addShipmentColumn('email_sent_at', "datetime NULL DEFAULT NULL AFTER `creation_error`");
		$this->addShipmentColumn('email_error', "text NULL AFTER `email_sent_at`");
		$this->addShipmentColumn('label_email_sent_at', "datetime NULL DEFAULT NULL AFTER `email_error`");
		$this->addShipmentColumn('label_email_error', "text NULL AFTER `label_email_sent_at`");
		$this->addShipmentColumn('label_email_recipients', "text NULL AFTER `label_email_error`");
		$this->schema_installed = true;
	}

	public function getShipmentByOrderId($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "boxnow_shipment` WHERE order_id = '" . (int)$order_id . "' ORDER BY boxnow_shipment_id DESC LIMIT 1");

		return $query->num_rows ? $query->row : array();
	}

	public function createShipment($order_id) {
		$order_id = (int)$order_id;

		if ($order_id < 1) {
			throw new \InvalidArgumentException($this->language('error_not_boxnow_order', 'Narudžba nije BOX NOW dostava.'));
		}

		$this->installSchema();

		return $this->withOrderLock($order_id, function () use ($order_id) {
			return $this->createShipmentUnlocked($order_id);
		});
	}

	public function getLabel($order_id) {
		$this->installSchema();
		$shipment = $this->getShipmentByOrderId($order_id);

		if (empty($shipment['parcel_id'])) {
			throw new \RuntimeException($this->language('error_missing_parcel_id', 'BOX NOW nije vratio broj pošiljke.'));
		}

		return $this->apiRequest('GET', '/api/v1/parcels/' . rawurlencode($shipment['parcel_id']) . '/label.pdf?type=pdf', null, true);
	}

	public function sendTrackingEmail($order_id) {
		$order_id = (int)$order_id;
		$this->installSchema();

		return $this->withOrderLock($order_id, function () use ($order_id) {
			return $this->sendTrackingEmailUnlocked($order_id);
		});
	}

	protected function sendTrackingEmailUnlocked($order_id) {
		$shipment = $this->getShipmentByOrderId($order_id);

		if (empty($shipment['parcel_id'])) {
			return array(
				'email_sent'  => false,
				'email_error' => $this->language('error_missing_parcel_id', 'BOX NOW nije vratio broj pošiljke.')
			);
		}

		if (!empty($shipment['email_sent_at'])) {
			return array(
				'email_sent'         => false,
				'email_already_sent' => true,
				'email_error'        => ''
			);
		}

		$order = $this->getOrder($order_id);

		if (!$order || empty($order['email']) || !filter_var($order['email'], FILTER_VALIDATE_EMAIL)) {
			$error = $this->language('error_missing_customer_email', 'Narudžba nema ispravnu email adresu kupca.');
			$this->markTrackingEmailError($shipment, $error);

			return array('email_sent' => false, 'email_error' => $error);
		}

		$language_code = $this->getOrderLanguageCode($order);
		$language = new \Language($language_code);
		$language->load('extension/shipping/boxnow');
		$store_url = !empty($order['store_url']) ? rtrim($order['store_url'], '/') . '/' : (defined('HTTPS_CATALOG') ? rtrim(HTTPS_CATALOG, '/') . '/' : '');
		$store_name = !empty($order['store_name']) ? $order['store_name'] : $this->config->get('config_name');
		$order_number = !empty($order['number_order']) ? $order['number_order'] : $order['order_id'];
		$tracking_url = $this->getTrackingUrl($shipment['parcel_id']);
		$data = array(
			'title'                => sprintf($language->get('mail_subject'), $store_name),
			'logo'                 => $store_url . 'image/' . $this->config->get('config_logo'),
			'store_name'           => $store_name,
			'store_url'            => $store_url,
			'firstname'            => $order['firstname'],
			'order_number'         => $order_number,
			'tracking_code'        => $shipment['parcel_id'],
			'tracking_url'         => $tracking_url,
			'tracking_status'      => $this->getStatusLabel($shipment['status'], $language),
			'shipping_method'      => $order['shipping_method'],
			'account_order_url'    => !empty($order['customer_id']) ? $store_url . 'index.php?route=account/order/info&order_id=' . $order_id : '',
			'mail_heading'         => $language->get('mail_heading'),
			'mail_greeting'        => sprintf($language->get('mail_greeting'), $order['firstname']),
			'mail_intro'           => sprintf($language->get('mail_intro'), $order_number),
			'mail_tracking_code'   => $language->get('mail_tracking_code'),
			'mail_tracking_status' => $language->get('mail_tracking_status'),
			'mail_shipping_method' => $language->get('mail_shipping_method'),
			'mail_track_button'    => $language->get('mail_track_button'),
			'mail_order_button'    => $language->get('mail_order_button'),
			'mail_note'            => $language->get('mail_note'),
			'mail_footer'          => sprintf($language->get('mail_footer'), $store_name)
		);
		$from = $this->getStoreSettingValue('config_email', isset($order['store_id']) ? (int)$order['store_id'] : 0, $this->config->get('config_email'));
		$mail = $this->createMail();
		$this->configureMail($mail);
		$mail->setTo($order['email']);
		$mail->setFrom($from);
		$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode($data['title'], ENT_QUOTES, 'UTF-8'));
		$mail->setText($this->buildTrackingEmailText($data));
		$mail->setHtml($this->buildTrackingEmailHtml($data));

		try {
			$mail->send();
			$this->markTrackingEmailSent($shipment);
			$this->addOrderHistory($order, $language->get('text_tracking_email_history') . ' ' . $shipment['parcel_id']);

			return array('email_sent' => true, 'email_error' => '');
		} catch (\Throwable $exception) {
			$this->markTrackingEmailError($shipment, $exception->getMessage());
			$this->log->write('BOX NOW tracking mail failed (order ' . $order_id . '): ' . $exception->getMessage());

			return array('email_sent' => false, 'email_error' => $exception->getMessage());
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

	public function sendLabelEmail($order_id, $force = false) {
		$order_id = (int)$order_id;
		$this->installSchema();

		return $this->withOrderLock($order_id, function () use ($order_id, $force) {
			return $this->sendLabelEmailUnlocked($order_id, $force);
		});
	}

	protected function sendLabelEmailUnlocked($order_id, $force = false) {
		$shipment = $this->getShipmentByOrderId($order_id);

		if (empty($shipment['parcel_id'])) {
			throw new \RuntimeException($this->language('error_missing_parcel_id', 'BOX NOW nije vratio broj pošiljke.'));
		}

		if (!$force && !empty($shipment['label_email_sent_at'])) {
			return array(
				'label_email_sent'         => false,
				'label_email_already_sent' => true,
				'label_email_error'        => ''
			);
		}

		$order = $this->getOrder($order_id);

		if (!$order) {
			throw new \RuntimeException($this->language('error_not_boxnow_order', 'Narudžba nije BOX NOW dostava.'));
		}

		$recipients = $this->getLabelEmailRecipients($order);

		if (!$recipients) {
			$error = $this->language('error_missing_label_email_recipients', 'Nema ispravnih internih email adresa za slanje BOX NOW adresnice.');
			$this->markLabelEmailError($shipment, $error);
			throw new \RuntimeException($error);
		}

		$temporary_label = '';

		try {
			$pdf = $this->getLabel($order_id);

			if (substr((string)$pdf, 0, 5) !== '%PDF-') {
				throw new \RuntimeException($this->language('error_invalid_label_pdf', 'BOX NOW nije vratio ispravnu PDF adresnicu.'));
			}

			$temporary_label = $this->createTemporaryLabel($shipment, $pdf);
			$store_name = !empty($order['store_name']) ? $order['store_name'] : $this->config->get('config_name');
			$from = $this->getStoreSettingValue('config_email', isset($order['store_id']) ? (int)$order['store_id'] : 0, $this->config->get('config_email'));

			if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
				$from = $recipients[0];
			}
			$order_number = !empty($shipment['order_number']) ? $shipment['order_number'] : (string)$order_id;
			$reference_number = !empty($shipment['reference_number']) ? $shipment['reference_number'] : $shipment['parcel_id'];
			$subject = sprintf('BOX NOW adresnica - narudžba %s - referenca %s', $order_number, $reference_number);
			$text = $this->buildLabelEmailText($order, $shipment);
			$html = $this->buildLabelEmailHtml($order, $shipment);
			$mail = $this->createMail();

			$this->configureMail($mail);
			$mail->setTo($recipients[0]);

			if (count($recipients) > 1) {
				$mail->setBcc(array_slice($recipients, 1));
			}

			$mail->setFrom($from);
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject($subject);
			$mail->setText($text);
			$mail->setHtml($html);
			$mail->addAttachment($temporary_label);
			$mail->send();

			$this->markLabelEmailSent($shipment, $recipients);
			$this->addOrderHistory($order, sprintf('BOX NOW adresnica poslana na: %s. Referenca: %s', implode(', ', $recipients), $reference_number));

			return array(
				'label_email_sent'       => true,
				'label_email_recipients' => $recipients,
				'label_email_error'      => ''
			);
		} catch (\Throwable $exception) {
			$this->markLabelEmailError($shipment, $exception->getMessage());
			$this->log->write('BOX NOW label mail failed (order ' . $order_id . '): ' . $exception->getMessage());
			throw $exception;
		} finally {
			if ($temporary_label !== '') {
				$this->cleanupTemporaryLabel($temporary_label);
			}
		}
	}

	protected function createShipmentUnlocked($order_id) {
		$existing = $this->getShipmentByOrderId($order_id);

		if (!empty($existing['parcel_id'])) {
			$existing['existing'] = true;
			return $existing;
		}

		if (trim($this->getConfig('client_id')) === '' || trim($this->getConfig('client_secret')) === '') {
			throw new \RuntimeException($this->language('error_missing_credentials', 'BOX NOW Client ID i Client Secret moraju biti upisani za kreiranje pošiljke.'));
		}

		$order = $this->getOrder($order_id);

		if (!$order || !isset($order['shipping_code']) || $order['shipping_code'] !== 'boxnow.boxnow') {
			throw new \RuntimeException($this->language('error_not_boxnow_order', 'Narudžba nije BOX NOW dostava.'));
		}

		$locker_id = $this->extractLockerId(isset($order['boxnow']) ? $order['boxnow'] : '');

		if ($locker_id === '') {
			throw new \RuntimeException($this->language('error_missing_locker', 'Narudžba nema odabran BOX NOW paketomat.'));
		}

		$order_number = $this->getOrderNumber($order);
		$total = number_format((float)$order['total'], 2, '.', '');
		$is_cod = $this->isCashOnDelivery($order);
		$item = array(
			'id'     => $order_number . '-1',
			'name'   => 'Order ' . $order_number,
			'value'  => $total,
			'weight' => $this->getOrderWeight($order_id)
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
				'contactNumber' => $this->normalizePhone(isset($order['telephone']) ? $order['telephone'] : ''),
				'contactEmail'  => isset($order['email']) ? $order['email'] : '',
				'contactName'   => trim((isset($order['firstname']) ? $order['firstname'] : '') . ' ' . (isset($order['lastname']) ? $order['lastname'] : '')),
				'locationId'    => $locker_id
			),
			'items'               => array($item)
		);

		$shipment = $this->ensureShipmentRecord($existing, $order_id, $order_number, $locker_id, $payload);

		try {
			$response = $this->apiRequest('POST', '/api/v1/delivery-requests', $payload);
			$reference_number = isset($response['referenceNumber']) ? trim((string)$response['referenceNumber']) : '';
			$parcel_id = !empty($response['parcels'][0]['id']) ? trim((string)$response['parcels'][0]['id']) : '';

			if ($parcel_id === '') {
				throw new \RuntimeException($this->language('error_missing_parcel_id', 'BOX NOW nije vratio broj pošiljke.'));
			}

			$this->recordShipmentCreated($shipment, $reference_number, $parcel_id, $response);
		} catch (\Throwable $exception) {
			$this->recordCreationError($shipment, $exception->getMessage());
			throw $exception;
		}

		return $this->getShipmentByOrderId($order_id);
	}

	protected function ensureShipmentRecord($existing, $order_id, $order_number, $locker_id, $payload) {
		$payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		if (!empty($existing['boxnow_shipment_id'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET order_number = '" . $this->db->escape($order_number) . "', locker_id = '" . $this->db->escape($locker_id) . "', status = 'creating', creation_attempted_at = NOW(), creation_error = NULL, payload = '" . $this->db->escape($payload_json) . "', date_modified = NOW() WHERE boxnow_shipment_id = '" . (int)$existing['boxnow_shipment_id'] . "'");
			return $this->getShipmentByOrderId($order_id);
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "boxnow_shipment` SET order_id = '" . (int)$order_id . "', order_number = '" . $this->db->escape($order_number) . "', locker_id = '" . $this->db->escape($locker_id) . "', status = 'creating', creation_attempted_at = NOW(), payload = '" . $this->db->escape($payload_json) . "', date_added = NOW(), date_modified = NOW()");

		return $this->getShipmentByOrderId($order_id);
	}

	protected function recordShipmentCreated($shipment, $reference_number, $parcel_id, $response) {
		$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET reference_number = '" . $this->db->escape($reference_number) . "', parcel_id = '" . $this->db->escape($parcel_id) . "', status = 'new', creation_error = NULL, response = '" . $this->db->escape(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . "', date_modified = NOW() WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");
	}

	protected function recordCreationError($shipment, $error) {
		if (!empty($shipment['boxnow_shipment_id'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET status = 'error', creation_error = '" . $this->db->escape((string)$error) . "', date_modified = NOW() WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");
		}
	}

	protected function getOrder($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "' LIMIT 1");

		return $query->num_rows ? $query->row : array();
	}

	protected function getOrderWeight($order_id) {
		$query = $this->db->query("SELECT SUM(IFNULL(p.weight, 0) * op.quantity) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "product` p ON (p.product_id = op.product_id) WHERE op.order_id = '" . (int)$order_id . "'");
		$weight = isset($query->row['total']) ? (float)$query->row['total'] : 0.0;

		if ($weight <= 0) {
			$weight = 1.0;
		}

		return (float)number_format($weight, 3, '.', '');
	}

	protected function getLabelEmailRecipients($order) {
		$store_id = isset($order['store_id']) ? (int)$order['store_id'] : 0;
		$recipients = array();
		$seen = array();
		$primary = $this->getStoreSettingValue('config_email', $store_id, $this->config->get('config_email'));
		$this->addRecipient($recipients, $seen, $primary);
		$alerts = $this->getStoreSettingValue('config_mail_alert', $store_id, $this->config->get('config_mail_alert'));

		if (is_string($alerts)) {
			$decoded = json_decode($alerts, true);
			$alerts = is_array($decoded) ? $decoded : preg_split('/[\s,;]+/', $alerts, -1, PREG_SPLIT_NO_EMPTY);
		}

		if (in_array('order', (array)$alerts, true)) {
			$additional = $this->getStoreSettingValue('config_mail_alert_email', $store_id, $this->config->get('config_mail_alert_email'));

			foreach (preg_split('/[\s,;]+/', (string)$additional, -1, PREG_SPLIT_NO_EMPTY) as $email) {
				$this->addRecipient($recipients, $seen, $email);
			}
		}

		return $recipients;
	}

	protected function getStoreSettingValue($key, $store_id, $fallback = null) {
		$query = $this->db->query("SELECT `value`, `serialized` FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "' ORDER BY setting_id DESC LIMIT 1");

		if (!$query->num_rows && $store_id) {
			$query = $this->db->query("SELECT `value`, `serialized` FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `key` = '" . $this->db->escape($key) . "' ORDER BY setting_id DESC LIMIT 1");
		}

		if (!$query->num_rows) {
			return $fallback;
		}

		if (!empty($query->row['serialized'])) {
			$decoded = json_decode($query->row['value'], true);
			return $decoded !== null ? $decoded : $fallback;
		}

		return $query->row['value'];
	}

	protected function createMail() {
		return new \Mail($this->config->get('config_mail_engine'));
	}

	protected function configureMail($mail) {
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
	}

	protected function createTemporaryLabel($shipment, $pdf) {
		$base_directory = defined('DIR_CACHE') && is_dir(DIR_CACHE) && is_writable(DIR_CACHE) ? DIR_CACHE : sys_get_temp_dir();
		$temporary = tempnam($base_directory, 'boxnow-mail-');

		if ($temporary === false) {
			throw new \RuntimeException('Nije moguće pripremiti privremenu BOX NOW adresnicu.');
		}

		@unlink($temporary);

		if (!mkdir($temporary, 0700)) {
			throw new \RuntimeException('Nije moguće pripremiti privremeni direktorij za BOX NOW adresnicu.');
		}

		$order_number = !empty($shipment['order_number']) ? $shipment['order_number'] : $shipment['order_id'];
		$filename = 'BOX-NOW-adresnica-' . preg_replace('/[^a-zA-Z0-9._-]+/', '-', (string)$order_number) . '.pdf';
		$path = $temporary . DIRECTORY_SEPARATOR . $filename;

		if (file_put_contents($path, $pdf, LOCK_EX) !== strlen($pdf)) {
			@unlink($path);
			@rmdir($temporary);
			throw new \RuntimeException('Nije moguće spremiti privremenu BOX NOW adresnicu.');
		}

		return $path;
	}

	protected function cleanupTemporaryLabel($path) {
		if (is_file($path)) {
			@unlink($path);
		}

		$directory = dirname($path);

		if (is_dir($directory)) {
			@rmdir($directory);
		}
	}

	protected function markLabelEmailSent($shipment, $recipients) {
		$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET label_email_sent_at = NOW(), label_email_error = NULL, label_email_recipients = '" . $this->db->escape(json_encode(array_values($recipients))) . "', date_modified = NOW() WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");
	}

	protected function markLabelEmailError($shipment, $error) {
		if (!empty($shipment['boxnow_shipment_id'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET label_email_error = '" . $this->db->escape((string)$error) . "', date_modified = NOW() WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");
		}
	}

	protected function markTrackingEmailSent($shipment) {
		$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET email_sent_at = NOW(), email_error = NULL, date_modified = NOW() WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");
	}

	protected function markTrackingEmailError($shipment, $error) {
		if (!empty($shipment['boxnow_shipment_id'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "boxnow_shipment` SET email_error = '" . $this->db->escape((string)$error) . "', date_modified = NOW() WHERE boxnow_shipment_id = '" . (int)$shipment['boxnow_shipment_id'] . "'");
		}
	}

	protected function addOrderHistory($order, $comment) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order_history` SET order_id = '" . (int)$order['order_id'] . "', order_status_id = '" . (int)$order['order_status_id'] . "', notify = '0', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}

	protected function withOrderLock($order_id, $callback) {
		$lock_name = 'dryzen_boxnow_order_' . (int)$order_id;
		$query = $this->db->query("SELECT GET_LOCK('" . $this->db->escape($lock_name) . "', 10) AS acquired");

		if (!$query->num_rows || (int)$query->row['acquired'] !== 1) {
			throw new \RuntimeException('BOX NOW obrada narudžbe je već u tijeku. Pokušajte ponovno.');
		}

		try {
			return call_user_func($callback);
		} finally {
			$this->db->query("SELECT RELEASE_LOCK('" . $this->db->escape($lock_name) . "')");
		}
	}

	protected function apiRequest($method, $endpoint, $payload = null, $binary = false, $authenticated = true) {
		if (!function_exists('curl_init')) {
			throw new \RuntimeException('BOX NOW API: cURL is not available.');
		}

		$url = rtrim($this->getConfig('api_url', 'https://api-production.boxnow.hr'), '/') . $endpoint;
		$headers = array('Accept: ' . ($binary ? 'application/pdf' : 'application/json'));
		$partner_id = $this->getConfig('partner_id');

		if ($partner_id !== '') {
			$headers[] = 'X-PartnerID: ' . $partner_id;
		}

		if ($authenticated) {
			$headers[] = 'Authorization: Bearer ' . $this->getAccessToken();
		}

		if ($payload !== null) {
			$headers[] = 'Content-Type: application/json';
		}

		$handle = curl_init($url);
		curl_setopt_array($handle, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_HTTPHEADER     => $headers,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2
		));

		if ($payload !== null) {
			$json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

			if ($json === false) {
				curl_close($handle);
				throw new \RuntimeException('BOX NOW API: nije moguće pripremiti JSON zahtjev.');
			}

			curl_setopt($handle, CURLOPT_POSTFIELDS, $json);
		}

		$body = curl_exec($handle);
		$error_number = curl_errno($handle);
		$error = curl_error($handle);
		$http_code = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
		curl_close($handle);

		if ($error_number) {
			throw new \RuntimeException('BOX NOW API: ' . $error);
		}

		if ($http_code < 200 || $http_code >= 300) {
			throw new \RuntimeException('BOX NOW API HTTP ' . $http_code . ': ' . (string)$body);
		}

		if ($binary) {
			return (string)$body;
		}

		$decoded = json_decode((string)$body, true);

		if (!is_array($decoded)) {
			throw new \RuntimeException('BOX NOW API returned invalid JSON.');
		}

		return $decoded;
	}

	protected function getAccessToken() {
		$response = $this->apiRequest('POST', '/api/v1/auth-sessions', array(
			'grant_type'    => 'client_credentials',
			'client_id'     => $this->getConfig('client_id'),
			'client_secret' => $this->getConfig('client_secret')
		), false, false);

		if (empty($response['access_token'])) {
			throw new \RuntimeException('BOX NOW API: access token missing.');
		}

		return $response['access_token'];
	}

	protected function getConfig($key, $default = '') {
		$value = $this->config->get('shipping_boxnow_' . $key);

		return ($value !== null && $value !== '') ? $value : $default;
	}

	protected function getOrderNumber($order) {
		$number = !empty($order['number_order']) ? $order['number_order'] : $order['order_id'];

		return $this->getConfig('order_prefix', 'DRYZEN-') . $number;
	}

	protected function extractLockerId($value) {
		$value = trim((string)$value);

		if ($value === '' || stripos($value, 'undefined') !== false) {
			return '';
		}

		if (strpos($value, ';') !== false) {
			$parts = explode(';', $value);
			return trim(end($parts));
		}

		if (strpos($value, '_') !== false) {
			$parts = explode('_', $value);
			return trim(end($parts));
		}

		return $value;
	}

	protected function normalizePhone($phone) {
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

	protected function isCashOnDelivery($order) {
		$payment_code = strtolower(isset($order['payment_code']) ? (string)$order['payment_code'] : '');
		$payment_method = strtolower(isset($order['payment_method']) ? (string)$order['payment_method'] : '');

		return $payment_code === 'cod' || strpos($payment_code, 'cod') !== false || strpos($payment_method, 'pouze') !== false;
	}

	protected function getOrderLanguageCode($order) {
		if (!empty($order['language_id'])) {
			$query = $this->db->query("SELECT `code` FROM `" . DB_PREFIX . "language` WHERE language_id = '" . (int)$order['language_id'] . "' LIMIT 1");

			if ($query->num_rows && !empty($query->row['code'])) {
				return $query->row['code'];
			}
		}

		return $this->config->get('config_language');
	}

	private function addShipmentColumn($name, $definition) {
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "boxnow_shipment` LIKE '" . $this->db->escape($name) . "'");

		if (!$query->num_rows) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "boxnow_shipment` ADD `" . $this->db->escape($name) . "` " . $definition);
		}
	}

	private function addRecipient(&$recipients, &$seen, $email) {
		$email = trim((string)$email);
		$key = strtolower($email);

		if ($email === '' || isset($seen[$key]) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return;
		}

		$seen[$key] = true;
		$recipients[] = $email;
	}

	private function buildLabelEmailText($order, $shipment) {
		$lines = array(
			'BOX NOW adresnica je u privitku.',
			'',
			'Narudžba: ' . (!empty($shipment['order_number']) ? $shipment['order_number'] : $order['order_id']),
			'BOX NOW referenca: ' . (!empty($shipment['reference_number']) ? $shipment['reference_number'] : '-'),
			'Broj pošiljke: ' . $shipment['parcel_id'],
			'Paketomat: ' . (!empty($shipment['locker_id']) ? $shipment['locker_id'] : '-'),
			'Kupac: ' . trim($order['firstname'] . ' ' . $order['lastname'])
		);

		return implode("\n", $lines);
	}

	private function buildLabelEmailHtml($order, $shipment) {
		$escape = function ($value) {
			return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
		};
		$order_number = !empty($shipment['order_number']) ? $shipment['order_number'] : $order['order_id'];
		$reference_number = !empty($shipment['reference_number']) ? $shipment['reference_number'] : '-';
		$locker_id = !empty($shipment['locker_id']) ? $shipment['locker_id'] : '-';

		return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family:Arial,Helvetica,sans-serif;color:#222">'
			. '<h2>BOX NOW adresnica</h2><p>PDF adresnica za paket nalazi se u privitku.</p>'
			. '<table cellpadding="7" cellspacing="0" border="1" style="border-collapse:collapse;border-color:#ddd">'
			. '<tr><td><strong>Narudžba</strong></td><td>' . $escape($order_number) . '</td></tr>'
			. '<tr><td><strong>BOX NOW referenca</strong></td><td>' . $escape($reference_number) . '</td></tr>'
			. '<tr><td><strong>Broj pošiljke</strong></td><td>' . $escape($shipment['parcel_id']) . '</td></tr>'
			. '<tr><td><strong>Paketomat</strong></td><td>' . $escape($locker_id) . '</td></tr>'
			. '<tr><td><strong>Kupac</strong></td><td>' . $escape(trim($order['firstname'] . ' ' . $order['lastname'])) . '</td></tr>'
			. '</table></body></html>';
	}

	private function getStatusLabel($status, $language) {
		$status = strtolower(trim((string)$status));
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

		return $text . "\n" . $data['mail_note'] . "\n\n" . $data['mail_footer'];
	}

	private function buildTrackingEmailHtml($data) {
		$escape = function ($value) {
			return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
		};
		$buttons = '';

		if ($data['tracking_url'] !== '') {
			$buttons .= '<a href="' . $escape($data['tracking_url']) . '" style="display:inline-block;margin:0 8px 8px 0;padding:12px 20px;background:#111827;border-radius:6px;color:#fff;font-weight:bold;text-decoration:none">' . $escape($data['mail_track_button']) . '</a>';
		}

		if ($data['account_order_url'] !== '') {
			$buttons .= '<a href="' . $escape($data['account_order_url']) . '" style="display:inline-block;padding:11px 19px;border:1px solid #111827;border-radius:6px;color:#111827;font-weight:bold;text-decoration:none">' . $escape($data['mail_order_button']) . '</a>';
		}

		return '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;background:#f4f5f7;color:#2f3542;font-family:Arial,Helvetica,sans-serif">'
			. '<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:28px 12px"><table width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;background:#fff">'
			. '<tr><td align="center" style="padding:24px;border-bottom:1px solid #e8eaed"><a href="' . $escape($data['store_url']) . '"><img src="' . $escape($data['logo']) . '" alt="' . $escape($data['store_name']) . '" style="max-width:220px;max-height:72px;border:0"></a></td></tr>'
			. '<tr><td style="padding:30px 32px"><h1 style="font-size:26px">' . $escape($data['mail_heading']) . '</h1><p>' . $escape($data['mail_greeting']) . '</p><p>' . $escape($data['mail_intro']) . '</p>'
			. '<table width="100%" cellpadding="8" cellspacing="0" style="margin:20px 0;background:#f7f8fa;border:1px solid #e3e6ea"><tr><td><strong>' . $escape($data['mail_tracking_code']) . ':</strong></td><td>' . $escape($data['tracking_code']) . '</td></tr>'
			. '<tr><td><strong>' . $escape($data['mail_tracking_status']) . ':</strong></td><td>' . $escape($data['tracking_status']) . '</td></tr><tr><td><strong>' . $escape($data['mail_shipping_method']) . ':</strong></td><td>' . $escape($data['shipping_method']) . '</td></tr></table>'
			. $buttons . '<p style="margin-top:20px;color:#606875">' . $escape($data['mail_note']) . '</p><p style="color:#606875">' . $escape($data['mail_footer']) . '</p></td></tr></table></td></tr></table></body></html>';
	}

	private function language($key, $fallback) {
		if ($this->registry->has('language')) {
			$value = $this->registry->get('language')->get($key);

			if ($value !== $key) {
				return $value;
			}
		}

		return $fallback;
	}
}

// OpenCart 3 constructs a library class name directly from its route, so
// boxnow/shipment_manager is instantiated as boxnow\shipment_manager.
if (!class_exists(__NAMESPACE__ . '\\shipment_manager', false)) {
	class_alias(__NAMESPACE__ . '\\ShipmentManager', __NAMESPACE__ . '\\shipment_manager');
}
