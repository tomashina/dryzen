<?php
class ControllerMarketingMarketing extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('marketing/marketing');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/marketing');

		$this->getList();
	}

	public function info() {
		$this->load->language('marketing/marketing');
		$this->load->model('marketing/marketing');
		$this->document->setTitle($this->language->get('heading_title'));

		$marketing_id = isset($this->request->get['marketing_id']) ? (int)$this->request->get['marketing_id'] : 0;
		$marketing_info = $this->model_marketing_marketing->getMarketing($marketing_id);

		if (!$marketing_info) {
			$this->error['warning'] = $this->language->get('error_not_found');
			$this->getList();
			return;
		}

		$page = isset($this->request->get['page']) ? max(1, (int)$this->request->get['page']) : 1;
		$limit = max(1, (int)$this->config->get('config_limit_admin'));
		$total = $this->model_marketing_marketing->getTotalMarketingOrders($marketing_id);
		$data['orders'] = array();

		foreach ($this->model_marketing_marketing->getMarketingOrders($marketing_id, ($page - 1) * $limit, $limit) as $result) {
			$data['orders'][] = array(
				'order_id' => (int)$result['order_id'],
				'customer' => html_entity_decode($result['customer'], ENT_QUOTES, 'UTF-8'),
				'status' => html_entity_decode($result['order_status'], ENT_QUOTES, 'UTF-8'),
				'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'view' => $this->user->hasPermission('access', 'sale/order') ? $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$result['order_id'], true) : ''
			);
		}

		$data['name'] = html_entity_decode($marketing_info['name'], ENT_QUOTES, 'UTF-8');
		$data['tracking_url'] = $this->getTrackingBaseUrl() . '?tracking=' . rawurlencode(html_entity_decode($marketing_info['code'], ENT_QUOTES, 'UTF-8'));
		$data['text_tracking_help'] = $this->getTrackingHelp();
		$data['cancel'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'], true);
		$data['edit'] = $this->url->link('marketing/marketing/edit', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $marketing_id, true);
		$data['breadcrumbs'] = array(
			array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)),
			array('text' => $this->language->get('heading_title'), 'href' => $data['cancel']),
			array('text' => $data['name'], 'href' => $this->url->link('marketing/marketing/info', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $marketing_id, true))
		);

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('marketing/marketing/info', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $marketing_id . '&page={page}', true);
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), $total ? ($page - 1) * $limit + 1 : 0, min($page * $limit, $total), $total, ceil($total / $limit));
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('marketing/marketing_info', $data));
	}

	public function add() {
		$this->load->language('marketing/marketing');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/marketing');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_marketing_marketing->addMarketing($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_code'])) {
				$url .= '&filter_code=' . $this->request->get['filter_code'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('marketing/marketing');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/marketing');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_marketing_marketing->editMarketing($this->request->get['marketing_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_code'])) {
				$url .= '&filter_code=' . $this->request->get['filter_code'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('marketing/marketing');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('marketing/marketing');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $marketing_id) {
				$this->model_marketing_marketing->deleteMarketing($marketing_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_code'])) {
				$url .= '&filter_code=' . $this->request->get['filter_code'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_code'])) {
			$filter_code = $this->request->get['filter_code'];
		} else {
			$filter_code = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'm.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('marketing/marketing/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('marketing/marketing/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['marketings'] = array();
		$data['text_tracking_help'] = $this->getTrackingHelp();

		$filter_data = array(
			'filter_name'       => $filter_name,
			'filter_code'       => $filter_code,
			'filter_date_added' => $filter_date_added,
			'sort'              => $sort,
			'order'             => $order,
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);

		$marketing_total = $this->model_marketing_marketing->getTotalMarketings($filter_data);

		$results = $this->model_marketing_marketing->getMarketings($filter_data);

		foreach ($results as $result) {
			$clicks = (int)$result['clicks'];
			$orders = (int)$result['orders'];

			$data['marketings'][] = array(
				'marketing_id' => $result['marketing_id'],
				'name'         => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
				'code'         => html_entity_decode($result['code'], ENT_QUOTES, 'UTF-8'),
				'tracking_url' => $this->getTrackingBaseUrl() . '?tracking=' . rawurlencode(html_entity_decode($result['code'], ENT_QUOTES, 'UTF-8')),
				'clicks'       => $clicks,
				'orders'       => $orders,
				'revenue'      => $this->currency->format((float)$result['revenue'], $this->config->get('config_currency'), 1),
				'conversion'   => number_format($clicks > 0 ? $orders / $clicks * 100 : 0, 2, $this->language->get('decimal_point'), $this->language->get('thousand_point')) . '%',
				'info'         => $this->url->link('marketing/marketing/info', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . (int)$result['marketing_id'], true),
				'date_added'   => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'         => $this->url->link('marketing/marketing/edit', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $result['marketing_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . '&sort=m.name' . $url, true);
		$data['sort_code'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . '&sort=m.code' . $url, true);
		$data['sort_date_added'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . '&sort=m.date_added' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $marketing_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($marketing_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($marketing_total - $this->config->get('config_limit_admin'))) ? $marketing_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $marketing_total, ceil($marketing_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = html_entity_decode($filter_name, ENT_QUOTES, 'UTF-8');
		$data['filter_code'] = html_entity_decode($filter_code, ENT_QUOTES, 'UTF-8');
		$data['filter_date_added'] = html_entity_decode($filter_date_added, ENT_QUOTES, 'UTF-8');

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/marketing_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['marketing_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_code'])) {
			$url .= '&filter_code=' . $this->request->get['filter_code'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['marketing_id'])) {
			$data['action'] = $this->url->link('marketing/marketing/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('marketing/marketing/edit', 'user_token=' . $this->session->data['user_token'] . '&marketing_id=' . $this->request->get['marketing_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('marketing/marketing', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['marketing_id'])) {
			$marketing_info = $this->model_marketing_marketing->getMarketing($this->request->get['marketing_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['store'] = $this->getTrackingBaseUrl();
		$data['code_locked'] = !empty($marketing_info);

		if (isset($this->request->post['name']) && is_scalar($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($marketing_info)) {
			$data['name'] = $marketing_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['description']) && is_scalar($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
		} elseif (!empty($marketing_info)) {
			$data['description'] = $marketing_info['description'];
		} else {
			$data['description'] = '';
		}

		if (!empty($marketing_info)) {
			$data['code'] = $marketing_info['code'];
		} elseif (isset($this->request->post['code']) && is_scalar($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		} else {
			$data['code'] = bin2hex(random_bytes(12));
		}

		// OpenCart stores request values as HTML entities. Decode once before
		// Twig escapes them for display to keep subsequent form saves stable.
		foreach (array('name', 'description', 'code') as $field) {
			$data[$field] = html_entity_decode($data[$field], ENT_QUOTES, 'UTF-8');
		}

		$data['tracking_url'] = $data['store'] . '?tracking=' . rawurlencode($data['code']);
		$data['text_tracking_help'] = $this->getTrackingHelp();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/marketing_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'marketing/marketing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach (array('name', 'description', 'code') as $field) {
			if (!isset($this->request->post[$field]) || !is_scalar($this->request->post[$field])) {
				$this->request->post[$field] = '';
			}
		}

		if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		$marketing_id = isset($this->request->get['marketing_id']) ? (int)$this->request->get['marketing_id'] : 0;
		$marketing_info = $marketing_id ? $this->model_marketing_marketing->getMarketing($marketing_id) : array();

		if ($marketing_id && !$marketing_info) {
			$this->error['warning'] = $this->language->get('error_not_found');
		}

		if ($marketing_info && $this->request->post['code'] !== $marketing_info['code']) {
			$this->error['code'] = $this->language->get('error_code_locked');
		} elseif (!$marketing_info && !preg_match('/^[A-Za-z0-9_-]{1,64}$/D', $this->request->post['code'])) {
			$this->error['code'] = $this->language->get('error_code');
		} elseif ($this->model_marketing_marketing->isTrackingCodeInUse($this->request->post['code'], $marketing_id)) {
			$this->error['code'] = $this->language->get('error_exists');
		}

		return !$this->error;
	}

	private function getTrackingBaseUrl() {
		$store = defined('HTTPS_CATALOG') && HTTPS_CATALOG ? HTTPS_CATALOG : HTTP_CATALOG;

		return rtrim($store, '/') . '/';
	}

	private function getTrackingHelp() {
		$this->load->model('localisation/order_status');
		$complete_statuses = (array)$this->config->get('config_complete_status');
		$names = array();

		foreach ($this->model_localisation_order_status->getOrderStatuses() as $status) {
			if (in_array($status['order_status_id'], $complete_statuses)) {
				$names[] = html_entity_decode($status['name'], ENT_QUOTES, 'UTF-8');
			}
		}

		return sprintf($this->language->get('text_tracking_help'), $names ? implode(', ', $names) : $this->language->get('text_no_complete_statuses'));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'marketing/marketing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ((array)$this->request->post['selected'] as $marketing_id) {
			if ($this->model_marketing_marketing->hasMarketingHistory((int)$marketing_id)) {
				$this->error['warning'] = $this->language->get('error_history');
				break;
			}
		}

		return !$this->error;
	}
}
