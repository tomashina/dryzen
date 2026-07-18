<?php
class ControllerMpGdprRequestList extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('mpgdpr/requestlist');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/stylesheet/mpgdpr/mpgdpr.css');

		$this->load->model('mpgdpr/mpgdpr');

		if (isset($this->request->get['filter_request_id'])) {
			$filter_request_id = $this->request->get['filter_request_id'];
		} else {
			$filter_request_id = '';
		}

		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_useragent'])) {
			$filter_useragent = $this->request->get['filter_useragent'];
		} else {
			$filter_useragent = null;
		}

		if (isset($this->request->get['filter_server_ip'])) {
			$filter_server_ip = $this->request->get['filter_server_ip'];
		} else {
			$filter_server_ip = null;
		}

		if (isset($this->request->get['filter_client_ip'])) {
			$filter_client_ip = $this->request->get['filter_client_ip'];
		} else {
			$filter_client_ip = null;
		}

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = null;
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = null;
		}

		if (isset($this->request->get['filter_time_lap_value'])) {
			$filter_time_lap_value = $this->request->get['filter_time_lap_value'];
		} else {
			$filter_time_lap_value = null;
		}

		if (isset($this->request->get['filter_time_lap'])) {
			$filter_time_lap = $this->request->get['filter_time_lap'];
		} else {
			$filter_time_lap = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'r.date_added';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}


		$url = '';

		if (isset($this->request->get['filter_request_id'])) {
			$url .= '&filter_request_id=' . $this->request->get['filter_request_id'];
		}

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_useragent'])) {
			$url .= '&filter_useragent=' . urlencode(html_entity_decode($this->request->get['filter_useragent'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_server_ip'])) {
			$url .= '&filter_server_ip=' . urlencode(html_entity_decode($this->request->get['filter_server_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_client_ip'])) {
			$url .= '&filter_client_ip=' . urlencode(html_entity_decode($this->request->get['filter_client_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_time_lap_value'])) {
			$url .= '&filter_time_lap_value=' . urlencode(html_entity_decode($this->request->get['filter_time_lap_value'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_time_lap'])) {
			$url .= '&filter_time_lap=' . urlencode(html_entity_decode($this->request->get['filter_time_lap'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('common/dashboard', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token], $this->mpgdpr->ssl)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('mpgdpr/requestlist', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url, $this->mpgdpr->ssl)
		);


		$data['token'] = $this->session->data[$this->mpgdpr->token];
		$data['var_token'] = $this->mpgdpr->token;

		$data['delete'] = $this->url->link('mpgdpr/requestlist/delete', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url, $this->mpgdpr->ssl);

		$data['requests'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'filter_request_id' => $filter_request_id,
			'filter_type' => $filter_type,
			'filter_email' => $filter_email,
			'filter_useragent' => $filter_useragent,
			'filter_server_ip' => $filter_server_ip,
			'filter_client_ip' => $filter_client_ip,
			'filter_date_start' => $filter_date_start,
			'filter_date_end' => $filter_date_end,
			'filter_time_lap_value' => $filter_time_lap_value,
			'filter_time_lap' => $filter_time_lap,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$requestlist_total = $this->model_mpgdpr_mpgdpr->getTotalRequests($filter_data);

		$results = $this->model_mpgdpr_mpgdpr->getRequests($filter_data);

		$customer_model = $this->mpgdpr->getAdminCustomerModelString();

		foreach ($results as $result) {

			$customer_info = $this->{$customer_model}->getCustomer($result['customer_id']);
			$email = $result['email'];
			if($customer_info) {
				$email = $customer_info['email'];
			}

			$data['requests'][] = array(
				'mpgdpr_requestlist_id' => $result['mpgdpr_requestlist_id'],
				'email'        => $email,
				'type'        => $this->mpgdpr->getRequestName($result['requessttype']),
				/*13 sep 2019 gdpr session starts*/
				'custom_string'        => $result['custom_string'],//html_entity_decode($result['custom_string'], ENT_QUOTES, 'UTF-8'),
				/*13 sep 2019 gdpr session ends*/
				'acceptlanguage'        => $result['accept_language'],
				'useragent'        => $result['user_agent'],
				'server_ip'        => $result['server_ip'],
				'client_ip'        => $result['client_ip'],
				/*13 sep 2019 gdpr session starts*/
				'date_added'        => date($this->language->get('datetime_format'), strtotime($result['date_added'])) ,
				/*13 sep 2019 gdpr session ends*/
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_select'] = $this->language->get('text_select');


		$data['entry_request_id'] = $this->language->get('entry_request_id');
		$data['entry_server_ip'] = $this->language->get('entry_server_ip');
		$data['entry_type'] = $this->language->get('entry_type');
		$data['entry_client_ip'] = $this->language->get('entry_client_ip');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_useragent'] = $this->language->get('entry_useragent');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_time_lap_value'] = $this->language->get('entry_time_lap_value');
		$data['entry_time_lap'] = $this->language->get('entry_time_lap');
		$data['entry_days'] = $this->language->get('entry_days');
		$data['entry_weeks'] = $this->language->get('entry_weeks');
		$data['entry_months'] = $this->language->get('entry_months');
		$data['entry_years'] = $this->language->get('entry_years');

		$data['column_request_id'] = $this->language->get('column_request_id');
		$data['column_server_ip'] = $this->language->get('column_server_ip');
		$data['column_type'] = $this->language->get('column_type');
		/*13 sep 2019 gdpr session starts*/
		$data['column_custom_string'] = $this->language->get('column_custom_string');
		/*13 sep 2019 gdpr session ends*/
		$data['column_client_ip'] = $this->language->get('column_client_ip');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_useragent'] = $this->language->get('column_useragent');
		$data['column_acceptlanguage'] = $this->language->get('column_acceptlanguage');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_other'] = $this->language->get('column_other');

		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_export'] = $this->language->get('button_export');

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

		if (isset($this->request->get['filter_request_id'])) {
			$url .= '&filter_request_id=' . $this->request->get['filter_request_id'];
		}

		if (isset($this->request->get['filter_type'])) {
			$url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_useragent'])) {
			$url .= '&filter_useragent=' . urlencode(html_entity_decode($this->request->get['filter_useragent'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_server_ip'])) {
			$url .= '&filter_server_ip=' . urlencode(html_entity_decode($this->request->get['filter_server_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_client_ip'])) {
			$url .= '&filter_client_ip=' . urlencode(html_entity_decode($this->request->get['filter_client_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

		if (isset($this->request->get['filter_time_lap_value'])) {
			$url .= '&filter_time_lap_value=' . urlencode(html_entity_decode($this->request->get['filter_time_lap_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_time_lap'])) {
			$url .= '&filter_time_lap=' . urlencode(html_entity_decode($this->request->get['filter_time_lap'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $requestlist_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('mpgdpr/requestlist', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url . '&page={page}', $this->mpgdpr->ssl);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($requestlist_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($requestlist_total - $this->config->get('config_limit_admin'))) ? $requestlist_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $requestlist_total, ceil($requestlist_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['filter_request_id'] = $filter_request_id;
		$data['filter_type'] = $filter_type;
		$data['filter_email'] = $filter_email;
		$data['filter_useragent'] = $filter_useragent;
		$data['filter_server_ip'] = $filter_server_ip;
		$data['filter_client_ip'] = $filter_client_ip;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_time_lap_value'] = $filter_time_lap_value;
		$data['filter_time_lap'] = $filter_time_lap;

		$data['request_types'] = $this->mpgdpr->getRequestTypes();


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->mpgdpr->view('mpgdpr/requestlist', $data));
	}

	public function delete() {
		$this->load->language('mpgdpr/requestlist');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('mpgdpr/mpgdpr');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpgdpr_requestlist_id) {
				$this->model_mpgdpr_mpgdpr->deleteRequestList($mpgdpr_requestlist_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_request_id'])) {
				$url .= '&filter_request_id=' . $this->request->get['filter_request_id'];
			}

			if (isset($this->request->get['filter_type'])) {
				$url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_useragent'])) {
				$url .= '&filter_useragent=' . urlencode(html_entity_decode($this->request->get['filter_useragent'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_server_ip'])) {
				$url .= '&filter_server_ip=' . urlencode(html_entity_decode($this->request->get['filter_server_ip'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_client_ip'])) {
				$url .= '&filter_client_ip=' . urlencode(html_entity_decode($this->request->get['filter_client_ip'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_date_start'])) {
				$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
			}

			if (isset($this->request->get['filter_date_end'])) {
				$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
			}

			if (isset($this->request->get['filter_time_lap_value'])) {
				$url .= '&filter_time_lap_value=' . urlencode(html_entity_decode($this->request->get['filter_time_lap_value'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_time_lap'])) {
				$url .= '&filter_time_lap=' . urlencode(html_entity_decode($this->request->get['filter_time_lap'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('mpgdpr/requestlist', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url, $this->mpgdpr->ssl));
		}

		$this->index();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'mpgdpr/requestlist')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
