<?php
class ControllerMailOrder extends Controller {
	public function index(&$route, &$args) {
		if (isset($args[0])) {
			$order_id = $args[0];
		} else {
			$order_id = 0;
		}

		if (isset($args[1])) {
			$order_status_id = $args[1];
		} else {
			$order_status_id = 0;
		}	

		if (isset($args[2])) {
			$comment = $args[2];
		} else {
			$comment = '';
		}
		
		if (isset($args[3])) {
			$notify = $args[3];
		} else {
			$notify = '';
		}
						
		// We need to grab the old order status ID
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			// If order status is 0 then becomes greater than 0 send main html email
			if (!$order_info['order_status_id'] && $order_status_id) {
				$this->add($order_info, $order_status_id, $comment, $notify);
			} 
			
			// If order status is not 0 then send update text email
			if ($order_info['order_status_id'] && $order_status_id && $notify) {
				$this->edit($order_info, $order_status_id, $comment, $notify);
			}		
		}
	}
		
	public function add($order_info, $order_status_id, $comment, $notify) {
		// Check for any downloadable products
		$download_status = false;

		$order_products = $this->model_checkout_order->getOrderProducts($order_info['order_id']);
		
		foreach ($order_products as $order_product) {
			// Check if there are any linked downloads
			$product_download_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$order_product['product_id'] . "'");

			if ($product_download_query->row['total']) {
				$download_status = true;
			}
		}
		
		// Load the language for any mails that might be required to be sent out
		$language = new Language($order_info['language_code']);
		$language->load($order_info['language_code']);
		$language->load('mail/order_add');

		// HTML Mail
		$data['title'] = sprintf($language->get('text_subject'), $order_info['store_name'], $order_info['order_id']);

		$data['text_greeting'] = sprintf($language->get('text_greeting'), $order_info['store_name']);
		$data['text_link'] = $language->get('text_link');
		$data['text_download'] = $language->get('text_download');
		$data['text_order_detail'] = $language->get('text_order_detail');
		$data['text_instruction'] = $language->get('text_instruction');
		$data['text_order_id'] = $language->get('text_order_id');
		$data['text_date_added'] = $language->get('text_date_added');
		$data['text_payment_method'] = $language->get('text_payment_method');
		$data['text_shipping_method'] = $language->get('text_shipping_method');
		$data['text_email'] = $language->get('text_email');
		$data['text_telephone'] = $language->get('text_telephone');
		$data['text_ip'] = $language->get('text_ip');
		$data['text_order_status'] = $language->get('text_order_status');
		$data['text_payment_address'] = $language->get('text_payment_address');
		$data['text_shipping_address'] = $language->get('text_shipping_address');
		$data['text_product'] = $language->get('text_product');
		$data['text_model'] = $language->get('text_model');
		$data['text_quantity'] = $language->get('text_quantity');
		$data['text_price'] = $language->get('text_price');
		$data['text_total'] = $language->get('text_total');
		$data['text_footer'] = $language->get('text_footer');
		$data['text_legal_guarantee_heading'] = $language->get('text_legal_guarantee_heading');
		$data['text_legal_guarantee_intro'] = $language->get('text_legal_guarantee_intro');
		$data['text_legal_guarantee_more'] = $language->get('text_legal_guarantee_more');
		$data['text_withdrawal_heading'] = $language->get('text_withdrawal_heading');
		$data['text_withdrawal_intro'] = $language->get('text_withdrawal_intro');

		$data['logo'] = $order_info['store_url'] . 'image/' . $this->config->get('config_logo');
		$data['store_name'] = $order_info['store_name'];
		$data['store_url'] = $order_info['store_url'];
		$store_url = rtrim((string)$order_info['store_url'], '/') . '/';
		$data['legal_guarantee_image'] = $store_url . 'index.php?route=common/header/legalGuaranteeImage';
		$data['legal_guarantee_eu_url'] = 'https://europa.eu/youreurope/jamstva_hr';
		$data['withdrawal_rights_url'] = $store_url . 'index.php?route=information/information&amp;information_id=17';
		$data['customer_id'] = $order_info['customer_id'];
		$data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];

		if ($download_status) {
			$data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
		} else {
			$data['download'] = '';
		}

		$data['order_id'] = $order_info['order_id'];
		$data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
		// Payment method titles can contain card-logo markup used at checkout.
		// Order emails only need the readable payment method name.
		$data['payment_method'] = trim(strip_tags(html_entity_decode($order_info['payment_method'], ENT_QUOTES, 'UTF-8')));
		$data['shipping_method'] = $this->formatShippingMethodForEmail($order_info, $language);
		$data['email'] = $order_info['email'];
		$data['telephone'] = $order_info['telephone'];
		$data['ip'] = $order_info['ip'];

		  if ($order_info['payment_code'] == 'bank_transfer') {

           

              $data['scanimage'] = HTTP_SERVER.'image/tmp/'.$order_info['order_id'].'.png';



          }

        //$data['collect_date'] = date($language->get('date_format_short'), strtotime($order_info['collect_date']));

		$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
		if ($order_status_query->num_rows) {
			$data['order_status'] = $order_status_query->row['name'];
		} else {
			$data['order_status'] = '';
		}

		if ($comment && $notify) {
			$data['comment'] = nl2br($comment);
		} else {
			$data['comment'] = '';
		}

		if ($order_info['payment_address_format']) {
			$format = $order_info['payment_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$format = dryzen_hide_county_from_address_format($format, $order_info['payment_country_id']);

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
		);

		$replace = array(
			'firstname' => $order_info['payment_firstname'],
			'lastname'  => $order_info['payment_lastname'],
			'company'   => $order_info['payment_company'],
			'address_1' => $order_info['payment_address_1'],
			'address_2' => $order_info['payment_address_2'],
			'city'      => $order_info['payment_city'],
			'postcode'  => $order_info['payment_postcode'],
			'zone'      => $order_info['payment_zone'],
			'zone_code' => $order_info['payment_zone_code'],
			'country'   => $order_info['payment_country']
		);

		$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

		if ($order_info['shipping_address_format']) {
			$format = $order_info['shipping_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$format = dryzen_hide_county_from_address_format($format, $order_info['shipping_country_id']);

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
		);

		$replace = array(
			'firstname' => $order_info['shipping_firstname'],
			'lastname'  => $order_info['shipping_lastname'],
			'company'   => $order_info['shipping_company'],
			'address_1' => $order_info['shipping_address_1'],
			'address_2' => $order_info['shipping_address_2'],
			'city'      => $order_info['shipping_city'],
			'postcode'  => $order_info['shipping_postcode'],
			'zone'      => $order_info['shipping_zone'],
			'zone_code' => $order_info['shipping_zone_code'],
			'country'   => $order_info['shipping_country']
		);

		$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

		$this->load->model('tool/upload');

		// Products
		$data['products'] = array();

		foreach ($order_products as $order_product) {
			$option_data = array();

			$order_options = $this->model_checkout_order->getOrderOptions($order_info['order_id'], $order_product['order_product_id']);

			foreach ($order_options as $order_option) {
				if ($order_option['type'] != 'file') {
					$value = $order_option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($order_option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $order_option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
				);
			}

			$data['products'][] = array(
				'name'     => $order_product['name'],
				'model'    => $order_product['model'],
				'option'   => $option_data,
				'quantity' => $order_product['quantity'],
				'price'    => $this->currency->format($order_product['price'] + ($this->config->get('config_tax') ? $order_product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'    => $this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $order_product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
			);
		}

		// Vouchers
		$data['vouchers'] = array();

		$order_vouchers = $this->model_checkout_order->getOrderVouchers($order_info['order_id']);

		foreach ($order_vouchers as $order_voucher) {
			$data['vouchers'][] = array(
				'description' => $order_voucher['description'],
				'amount'      => $this->currency->format($order_voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
			);
		}

		// Order Totals
		$data['totals'] = array();
		
		$order_totals = $this->model_checkout_order->getOrderTotals($order_info['order_id']);

		foreach ($order_totals as $order_total) {


            if($order_info['currency_code']=='HRK'){
                $text =  $this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']);
            }
            else{
                $text = $this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value']);
            }

			$data['totals'][] = array(
				'title' => $order_total['title'],
				'text'  => $text,
			);
		}
	
		$this->load->model('setting/setting');
		
		$from = $this->model_setting_setting->getSettingValue('config_email', $order_info['store_id']);
		
		if (!$from) {
			$from = $this->config->get('config_email');
		}

		$html = $this->load->view('mail/order_add', $data);
		$sender = html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8');
		$subject = html_entity_decode(sprintf($language->get('text_subject'), $order_info['store_name'], $order_info['order_id']), ENT_QUOTES, 'UTF-8');

		foreach ($this->getOrderMailRecipients($order_info['email']) as $recipient) {
			// Render once, but create and send a complete standalone message for
			// every recipient so each mailbox gets the same HTML and text body.
			$single_part_html = $recipient['context'] !== 'customer confirmation';
			$mail = $this->createOrderConfirmationMail($recipient['email'], $from, $sender, $subject, $html, $single_part_html);
			$this->sendSafely($mail, $order_info['order_id'], $recipient['context']);
		}
	}
	
	public function edit($order_info, $order_status_id, $comment) {
		$language = new Language($order_info['language_code']);
		$language->load($order_info['language_code']);
		$language->load('mail/order_edit');

		$data['text_order_id'] = $language->get('text_order_id');
		$data['text_date_added'] = $language->get('text_date_added');
		$data['text_order_status'] = $language->get('text_order_status');
		$data['text_link'] = $language->get('text_link');
		$data['text_comment'] = $language->get('text_comment');
		$data['text_footer'] = $language->get('text_footer');

		$data['order_id'] = $order_info['order_id'];
		$data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
		
		$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
		if ($order_status_query->num_rows) {
			$data['order_status'] = $order_status_query->row['name'];
		} else {
			$data['order_status'] = '';
		}

		if ($order_info['customer_id']) {
			$data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_info['order_id'];
		} else {
			$data['link'] = '';
		}

		$data['comment'] = strip_tags($comment);

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
		$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode(sprintf($language->get('text_subject'), $order_info['store_name'], $order_info['order_id']), ENT_QUOTES, 'UTF-8'));
		$mail->setText($this->load->view('mail/order_edit', $data));
		$this->sendSafely($mail, $order_info['order_id'], 'customer status update');
	}
	
	// Admin Alert Mail
	public function alert(&$route, &$args) {
		// The full HTML order confirmation is already sent to the store and
		// configured order-alert recipients in add(). The legacy alert
		// is intentionally disabled because it can create a second empty email.
		return;
	}

	private function formatShippingMethodForEmail($order_info, $language) {
		$shipping_method = isset($order_info['shipping_method']) ? $order_info['shipping_method'] : '';
		$shipping_code = isset($order_info['shipping_code']) ? $order_info['shipping_code'] : '';

		if (in_array($shipping_code, array('glsshop.glsshop', 'glspaketomat.glspaketomat'), true)) {
			$point = $this->parseGlsPickupPoint(isset($order_info['gls_ps']) ? $order_info['gls_ps'] : '');

			if ($point['location'] === '' && $point['point_id'] === '') {
				return $shipping_method;
			}

			$title_key = $shipping_code === 'glspaketomat.glspaketomat' ? 'text_gls_parcel_locker' : 'text_gls_parcel_shop';
			$lines = array(
				'<strong>' . htmlspecialchars($language->get($title_key), ENT_QUOTES, 'UTF-8') . '</strong>'
			);

			if ($point['location'] !== '') {
				$lines[] = '<strong>' . htmlspecialchars($language->get('text_gls_pickup_location'), ENT_QUOTES, 'UTF-8') . ':</strong> ' . htmlspecialchars($point['location'], ENT_QUOTES, 'UTF-8');
			}

			if ($point['point_id'] !== '') {
				$lines[] = '<strong>' . htmlspecialchars($language->get('text_gls_pickup_id'), ENT_QUOTES, 'UTF-8') . ':</strong> ' . htmlspecialchars($point['point_id'], ENT_QUOTES, 'UTF-8');
			}

			return $shipping_method . '<br /><br />' . implode('<br />', $lines);
		}

		if ($shipping_code !== 'boxnow.boxnow') {
			return $shipping_method;
		}

		$boxnow = $this->parseBoxNowLocation(isset($order_info['boxnow']) ? $order_info['boxnow'] : '');

		if ($boxnow['address'] === '' && $boxnow['locker_id'] === '') {
			return $shipping_method;
		}

		$lines = array(
			'<strong>' . htmlspecialchars($language->get('text_boxnow_locker'), ENT_QUOTES, 'UTF-8') . '</strong>'
		);

		if ($boxnow['address'] !== '') {
			$lines[] = '<strong>' . htmlspecialchars($language->get('text_boxnow_address'), ENT_QUOTES, 'UTF-8') . ':</strong> ' . htmlspecialchars($boxnow['address'], ENT_QUOTES, 'UTF-8');
		}

		if ($boxnow['locker_id'] !== '') {
			$lines[] = '<strong>' . htmlspecialchars($language->get('text_boxnow_locker_id'), ENT_QUOTES, 'UTF-8') . ':</strong> ' . htmlspecialchars($boxnow['locker_id'], ENT_QUOTES, 'UTF-8');
		}

		return $shipping_method . '<br /><br />' . implode('<br />', $lines);
	}

	private function parseBoxNowLocation($value) {
		$value = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8'))));
		$address = '';
		$locker_id = '';

		if ($value === '' || stripos($value, 'undefined') !== false) {
			return array('address' => '', 'locker_id' => '');
		}

		$separator = strrpos($value, ';');

		if ($separator !== false) {
			$address = trim(substr($value, 0, $separator));
			$locker_id = trim(substr($value, $separator + 1));
		} else {
			// Older BOX NOW integrations stored only the locker identifier.
			$locker_id = $value;
		}

		if ($address === '-' || $address === '-, -') {
			$address = '';
		}

		return array(
			'address'   => $address,
			'locker_id' => $locker_id
		);
	}

	private function parseGlsPickupPoint($value) {
		$value = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8'))));

		if ($value === '' || stripos($value, 'undefined') !== false) {
			return array('location' => '', 'point_id' => '');
		}

		$separator = strrpos($value, ';');

		if ($separator === false) {
			return array('location' => '', 'point_id' => $value);
		}

		return array(
			'location' => trim(substr($value, 0, $separator)),
			'point_id' => trim(substr($value, $separator + 1))
		);
	}

	private function getOrderMailRecipients($customer_email) {
		$recipients = array();
		$seen = array();

		$this->addOrderMailRecipient($recipients, $seen, $customer_email, 'customer confirmation');
		$this->addOrderMailRecipient($recipients, $seen, $this->config->get('config_email'), 'store confirmation');

		if (in_array('order', (array)$this->config->get('config_mail_alert'))) {
			$additional_emails = preg_split('/[\s,;]+/', (string)$this->config->get('config_mail_alert_email'), -1, PREG_SPLIT_NO_EMPTY);

			foreach ($additional_emails as $email) {
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$this->addOrderMailRecipient($recipients, $seen, $email, 'additional store confirmation');
				}
			}
		}

		return $recipients;
	}

	private function addOrderMailRecipient(&$recipients, &$seen, $email, $context) {
		$email = trim((string)$email);
		$key = strtolower($email);

		if ($email === '' || isset($seen[$key])) {
			return;
		}

		$seen[$key] = true;
		$recipients[] = array(
			'email'   => $email,
			'context' => $context
		);
	}

	private function createOrderConfirmationMail($recipient, $from, $sender, $subject, $html, $single_part_html = false) {
		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
		$mail->setTo($recipient);
		$mail->setFrom($from);
		$mail->setSender($sender);
		$mail->setSubject($subject);
		$mail->setText($this->orderHtmlToText($html));
		$mail->setHtml($html);
		$mail->setSinglePartHtml($single_part_html);

		$legal_notice = defined('DIR_IMAGE') ? rtrim(DIR_IMAGE, '/\\') . '/catalog/legal/eu-legal-guarantee-hr.png' : '';

		if ($legal_notice !== '' && is_file($legal_notice) && is_readable($legal_notice)) {
			$mail->addAttachment($legal_notice);
		}

		return $mail;
	}

	private function orderHtmlToText($html) {
		$text = preg_replace('/<\s*br\s*\/?\s*>/i', "\n", (string)$html);
		$text = preg_replace('/<\/(?:p|div|h[1-6]|tr|td|th|li|table)>/i', "\n", $text);
		$text = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
		$text = preg_replace("/[ \t]+\n/", "\n", $text);
		$text = preg_replace("/\n{3,}/", "\n\n", $text);

		return trim($text);
	}

	private function sendSafely($mail, $order_id, $context) {
		try {
			$mail->send();
		} catch (\Throwable $exception) {
			$this->log->write('Order mail failed (' . $context . ', order ' . (int)$order_id . '): ' . $exception->getMessage());
		}
	}
}
