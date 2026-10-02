<?php
class ControllerAccountReturn extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/return', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/return');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('account/return');

		$page = isset($this->request->get['page']) ? max(1, (int)$this->request->get['page']) : 1;
		$url = isset($this->request->get['page']) ? '&page=' . $page : '';
		$data['breadcrumbs'] = array(
			array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home')),
			array('text' => $this->language->get('text_account'), 'href' => $this->url->link('account/account', '', true)),
			array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('account/return', $url, true))
		);

		$data['returns'] = array();
		$return_total = $this->model_account_return->getTotalReturns();
		$results = $this->model_account_return->getReturns(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$data['returns'][] = array(
				'return_id'  => $result['return_id'],
				'order_id'   => !empty($result['invoice_number']) ? $result['invoice_number'] : $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'       => $this->url->link('account/return/info', 'return_id=' . (int)$result['return_id'] . $url, true)
			);
		}

		$pagination = new Pagination();
		$pagination->total = $return_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('account/return', 'page={page}', true);
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), $return_total ? (($page - 1) * 10) + 1 : 0, min($page * 10, $return_total), $return_total, max(1, (int)ceil($return_total / 10)));
		$data['continue'] = $this->url->link('account/account', '', true);

		$this->addLayoutData($data);
		$this->response->setOutput($this->load->view('account/return_list', $data));
	}

	public function info() {
		$this->load->language('account/return');
		$return_id = isset($this->request->get['return_id']) ? (int)$this->request->get['return_id'] : 0;

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/return/info', 'return_id=' . $return_id, true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->model('account/return');
		$return_info = $this->model_account_return->getReturn($return_id);

		if (!$return_info) {
			$this->document->setTitle($this->language->get('text_return'));
			$data['breadcrumbs'] = array(
				array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home')),
				array('text' => $this->language->get('text_account'), 'href' => $this->url->link('account/account', '', true)),
				array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('account/return', '', true))
			);
			$data['continue'] = $this->url->link('account/return', '', true);
			$this->addLayoutData($data);
			$this->response->setOutput($this->load->view('error/not_found', $data));
			return;
		}

		$this->document->setTitle($this->language->get('text_return'));
		$this->load->library('return_request');
		$data['breadcrumbs'] = array(
			array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home')),
			array('text' => $this->language->get('text_account'), 'href' => $this->url->link('account/account', '', true)),
			array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('account/return', '', true)),
			array('text' => $this->language->get('text_return'), 'href' => $this->url->link('account/return/info', 'return_id=' . $return_id, true))
		);

		$data['return_id'] = $return_info['return_id'];
		$data['order_id'] = $return_info['invoice_number'] !== '' ? $return_info['invoice_number'] : $return_info['order_id'];
		$return_date = !empty($return_info['invoice_date']) && $return_info['invoice_date'] !== '0000-00-00' ? $return_info['invoice_date'] : $return_info['date_ordered'];
		$data['date_ordered'] = $return_date ? date($this->language->get('date_format_short'), strtotime($return_date)) : '';
		$data['date_added'] = date($this->language->get('date_format_short'), strtotime($return_info['date_added']));
		$data['firstname'] = $return_info['firstname'];
		$data['lastname'] = $return_info['lastname'];
		$data['email'] = $return_info['email'];
		$data['telephone'] = $return_info['telephone'];
		$data['request_type'] = $return_info['request_type'];
		$data['request_type_label'] = $return_info['request_type'] === 'return' ? $this->language->get('text_type_return') : $this->language->get('text_type_withdrawal');
		$data['return_products'] = $this->return_request->decodeItems($return_info['return_items'], array(array(
			'name' => $return_info['product'], 'code' => $return_info['model'], 'quantity' => $return_info['quantity'], 'price' => ''
		)));
		$data['refund_iban'] = $return_info['refund_iban'];
		$data['reason'] = $return_info['reason'];
		$data['opened'] = $return_info['opened'] ? $this->language->get('text_yes') : $this->language->get('text_no');
		$data['comment'] = nl2br(htmlspecialchars($return_info['comment'], ENT_QUOTES, 'UTF-8'));
		$data['action'] = $return_info['action'];
		$data['histories'] = array();

		foreach ($this->model_account_return->getReturnHistories($return_id) as $result) {
			$data['histories'][] = array(
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'status'     => $result['status'],
				'comment'    => nl2br(htmlspecialchars($result['comment'], ENT_QUOTES, 'UTF-8'))
			);
		}

		$data['continue'] = $this->url->link('account/return', '', true);
		$this->addLayoutData($data);
		$this->response->setOutput($this->load->view('account/return_info', $data));
	}

	public function add() {
		$this->load->language('account/return');
		$this->load->model('account/return');
		$this->load->model('account/order');
		$this->load->model('localisation/return_reason');
		$this->load->library('return_request');
		if (empty($this->session->data['return_request_token'])) {
			$this->session->data['return_request_token'] = bin2hex(random_bytes(32));
		}

		$order_info = $this->getOwnedOrder(isset($this->request->get['order_id']) ? $this->request->get['order_id'] : 0);

		if ($this->request->server['REQUEST_METHOD'] === 'POST') {
			$this->normaliseSubmission();

			if ($this->validate()) {
				$return_id = $this->model_account_return->addReturn($this->request->post);
				$this->sendSubmissionEmails($return_id, $this->request->post);
				$this->session->data['return_request_id'] = $return_id;
				unset($this->session->data['return_request_token']);
				$this->response->redirect($this->url->link('account/return/success', '', true));
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
		$data['breadcrumbs'] = array(
			array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home')),
			array('text' => $this->language->get('text_account'), 'href' => $this->url->link('account/account', '', true)),
			array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('account/return/add', '', true))
		);

		foreach (array('warning', 'order_id', 'date_ordered', 'firstname', 'lastname', 'email', 'telephone', 'return_products', 'reason', 'refund_iban', 'declaration') as $key) {
			$data['error_' . $key] = isset($this->error[$key]) ? $this->error[$key] : '';
		}

		$data['action'] = $this->url->link('account/return/add', '', true);
		$data['return_request_token'] = $this->session->data['return_request_token'];
		$data['order_id'] = $this->postOrDefault('order_id', $order_info ? $order_info['order_id'] : 0);
		$data['invoice_number'] = $this->postOrDefault('invoice_number', $this->getInvoiceNumber($order_info));
		$data['invoice_date'] = $this->postOrDefault('invoice_date', $order_info ? date('Y-m-d', strtotime($order_info['date_added'])) : '');
		$data['firstname'] = $this->postOrDefault('firstname', $order_info ? $order_info['firstname'] : ($this->customer->isLogged() ? $this->customer->getFirstName() : ''));
		$data['lastname'] = $this->postOrDefault('lastname', $order_info ? $order_info['lastname'] : ($this->customer->isLogged() ? $this->customer->getLastName() : ''));
		$data['email'] = $this->postOrDefault('email', $order_info ? $order_info['email'] : ($this->customer->isLogged() ? $this->customer->getEmail() : ''));
		$data['telephone'] = $this->postOrDefault('telephone', $order_info ? $order_info['telephone'] : ($this->customer->isLogged() ? $this->customer->getTelephone() : ''));
		$data['request_type'] = $this->postOrDefault('request_type', 'withdrawal');
		$data['return_reason_id'] = $this->postOrDefault('return_reason_id', '');
		$data['refund_iban'] = $this->postOrDefault('refund_iban', '');
		$data['comment'] = $this->postOrDefault('comment', '');
		$data['declaration'] = !empty($this->request->post['declaration']);
		$data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();
		$data['return_products'] = isset($this->request->post['return_products']) ? $this->request->post['return_products'] : $this->getPrefilledItems($order_info);

		if (!$data['return_products']) {
			$data['return_products'] = array(array('name' => '', 'code' => '', 'quantity' => 1, 'price' => ''));
		}

		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('return', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}

		$data['text_agree'] = '';
		$data['policy_url'] = $this->url->link('information/information', 'information_id=17', true);

		if ($this->config->get('config_return_id')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));

			if ($information_info) {
				$data['policy_url'] = $this->url->link('information/information', 'information_id=' . (int)$this->config->get('config_return_id'), true);
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/agree', 'information_id=' . (int)$this->config->get('config_return_id'), true), $information_info['title']);
			}
		}

		$data['agree'] = !empty($this->request->post['agree']);
		$data['back'] = $this->customer->isLogged() ? $this->url->link('account/account', '', true) : $this->url->link('common/home');
		$this->addLayoutData($data);
		$this->response->setOutput($this->load->view('account/return_form', $data));
	}

	protected function validate() {
		$post = $this->request->post;
		$session_token = isset($this->session->data['return_request_token']) ? $this->session->data['return_request_token'] : '';
		$post_token = isset($post['return_request_token']) ? (string)$post['return_request_token'] : '';

		if ($session_token === '' || $post_token === '' || !hash_equals($session_token, $post_token)) {
			$this->error['warning'] = $this->language->get('error_security');
		}

		if (utf8_strlen($post['invoice_number']) < 1 || utf8_strlen($post['invoice_number']) > 64) {
			$this->error['order_id'] = $this->language->get('error_order_id');
		}

		$date = DateTime::createFromFormat('!Y-m-d', $post['invoice_date']);
		if (!$date || $date->format('Y-m-d') !== $post['invoice_date'] || $date > new DateTime('today')) {
			$this->error['date_ordered'] = $this->language->get('error_date_ordered');
		}

		if (utf8_strlen($post['firstname']) < 1 || utf8_strlen($post['firstname']) > 32) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if (utf8_strlen($post['lastname']) < 1 || utf8_strlen($post['lastname']) > 32) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if (utf8_strlen($post['email']) > 96 || !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (utf8_strlen($post['telephone']) < 3 || utf8_strlen($post['telephone']) > 32) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}

		if (!$this->return_request->validateItems($post['return_products'])) {
			$this->error['return_products'] = $this->language->get('error_return_products');
		}

		if ($post['request_type'] === 'return' && !$this->isValidReason($post['return_reason_id'])) {
			$this->error['reason'] = $this->language->get('error_reason');
		}

		if ($post['refund_iban'] !== '' && !preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/', preg_replace('/\s+/', '', strtoupper($post['refund_iban'])))) {
			$this->error['refund_iban'] = $this->language->get('error_refund_iban');
		}

		if (empty($post['declaration'])) {
			$this->error['declaration'] = $this->language->get('error_declaration');
		}

		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('return', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		if ($this->config->get('config_return_id')) {
			$this->load->model('catalog/information');
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));
			if ($information_info && empty($post['agree'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_form');
		}

		return !$this->error;
	}

	public function success() {
		$this->load->language('account/return');
		$this->document->setTitle($this->language->get('heading_title'));
		$return_id = isset($this->session->data['return_request_id']) ? (int)$this->session->data['return_request_id'] : 0;
		unset($this->session->data['return_request_id']);
		$data['breadcrumbs'] = array(
			array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home')),
			array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('account/return/add', '', true))
		);
		$data['text_message'] = $return_id ? sprintf($this->language->get('text_message'), $return_id) : $this->language->get('text_message_generic');
		$data['continue'] = $this->url->link('common/home');
		$this->addLayoutData($data);
		$this->response->setOutput($this->load->view('common/success', $data));
	}

	private function normaliseSubmission() {
		$this->request->post['request_type'] = $this->return_request->normaliseType(isset($this->request->post['request_type']) ? $this->request->post['request_type'] : '');
		$this->request->post['return_products'] = $this->return_request->normaliseItems(isset($this->request->post['return_products']) ? $this->request->post['return_products'] : array());
		$this->request->post['invoice_number'] = trim(strip_tags(isset($this->request->post['invoice_number']) ? $this->request->post['invoice_number'] : ''));
		$this->request->post['invoice_date'] = trim(isset($this->request->post['invoice_date']) ? $this->request->post['invoice_date'] : '');
		$this->request->post['date_ordered'] = $this->request->post['invoice_date'];
		$this->request->post['firstname'] = trim(strip_tags(isset($this->request->post['firstname']) ? $this->request->post['firstname'] : ''));
		$this->request->post['lastname'] = trim(strip_tags(isset($this->request->post['lastname']) ? $this->request->post['lastname'] : ''));
		$this->request->post['email'] = trim(isset($this->request->post['email']) ? $this->request->post['email'] : '');
		$this->request->post['telephone'] = trim(strip_tags(isset($this->request->post['telephone']) ? $this->request->post['telephone'] : ''));
		$this->request->post['refund_iban'] = strtoupper(trim(isset($this->request->post['refund_iban']) ? $this->request->post['refund_iban'] : ''));
		$this->request->post['comment'] = trim(isset($this->request->post['comment']) ? $this->request->post['comment'] : '');
		$this->request->post['return_reason_id'] = isset($this->request->post['return_reason_id']) ? (int)$this->request->post['return_reason_id'] : 0;
		if ($this->request->post['request_type'] === 'withdrawal') {
			$this->request->post['return_reason_id'] = 0;
		}
		$this->request->post['declaration'] = !empty($this->request->post['declaration']) ? 1 : 0;
		$this->request->post['agree'] = !empty($this->request->post['agree']) ? 1 : 0;
		$this->request->post['opened'] = 0;
		$this->request->post['order_id'] = $this->getOwnedOrder(isset($this->request->post['order_id']) ? $this->request->post['order_id'] : 0) ? (int)$this->request->post['order_id'] : 0;
		$this->request->post['product_id'] = 0;
		$this->request->post['submitted_ip'] = isset($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '';
	}

	private function isValidReason($return_reason_id) {
		foreach ($this->model_localisation_return_reason->getReturnReasons() as $reason) {
			if ((int)$reason['return_reason_id'] === (int)$return_reason_id) {
				return true;
			}
		}

		return false;
	}

	private function getOwnedOrder($order_id) {
		if (!$this->customer->isLogged() || !(int)$order_id) {
			return false;
		}

		return $this->model_account_order->getOrder((int)$order_id);
	}

	private function getInvoiceNumber($order_info) {
		if (!$order_info) {
			return '';
		}

		if (!empty($order_info['invoice_no'])) {
			return $order_info['invoice_prefix'] . $order_info['invoice_no'];
		}

		return (string)$order_info['order_id'];
	}

	private function getPrefilledItems($order_info) {
		if (!$order_info) {
			return array();
		}

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		$items = array();

		foreach ($this->model_account_order->getOrderProducts($order_info['order_id']) as $product) {
			if ($product_id && (int)$product['product_id'] !== $product_id) {
				continue;
			}

			$items[] = array(
				'name'     => $product['name'],
				'code'     => $product['model'],
				'quantity' => 1,
				'price'    => number_format((float)$product['price'] + (float)$product['tax'], 2, '.', '')
			);

			if ($product_id) {
				break;
			}
		}

		return $items;
	}

	private function sendSubmissionEmails($return_id, $submission) {
		$reason = '';
		foreach ($this->model_localisation_return_reason->getReturnReasons() as $return_reason) {
			if ((int)$return_reason['return_reason_id'] === (int)$submission['return_reason_id']) {
				$reason = $return_reason['name'];
				break;
			}
		}

		$submission['reason'] = $reason;
		$submission['submitted_at'] = date('Y-m-d H:i:s');
		$labels = array(
			'return_id' => $this->language->get('mail_label_return_id'),
			'request_type' => $this->language->get('mail_label_request_type'),
			'type_withdrawal' => $this->language->get('text_type_withdrawal'),
			'type_return' => $this->language->get('text_type_return'),
			'submitted_at' => $this->language->get('mail_label_submitted_at'),
			'invoice_number' => $this->language->get('entry_invoice_number'),
			'invoice_date' => $this->language->get('entry_invoice_date'),
			'customer' => $this->language->get('mail_label_customer'),
			'email' => $this->language->get('entry_email'),
			'telephone' => $this->language->get('entry_telephone'),
			'items' => $this->language->get('text_return_products_title'),
			'reason' => $this->language->get('entry_reason'),
			'comment' => $this->language->get('entry_fault_detail'),
			'refund_iban' => $this->language->get('entry_refund_iban')
		);

		$store_name = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$customer_text = $this->return_request->buildMailText($labels, $return_id, $submission, $this->language->get('mail_return_customer_intro'), $this->language->get('mail_return_customer_footer'));
		$admin_text = $this->return_request->buildMailText($labels, $return_id, $submission, $this->language->get('mail_return_admin_intro'), $this->language->get('mail_return_admin_footer'));

		$this->sendMail($submission['email'], sprintf($this->language->get('mail_return_customer_subject'), $store_name, $return_id), $customer_text, '');
		$this->sendMail($this->config->get('config_email'), sprintf($this->language->get('mail_return_admin_subject'), $store_name, $return_id), $admin_text, $submission['email']);
	}

	private function sendMail($to, $subject, $text, $reply_to) {
		try {
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			$mail->setTo($to);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			if ($reply_to !== '') {
				$mail->setReplyTo($reply_to);
			}
			$mail->setSubject($subject);
			$mail->setText($text);
			$mail->send();
		} catch (Throwable $exception) {
			$this->log->write('Return request email failed: ' . $exception->getMessage());
		}
	}

	private function postOrDefault($key, $default) {
		return isset($this->request->post[$key]) ? $this->request->post[$key] : $default;
	}

	private function addLayoutData(&$data) {
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
	}
}
