<?php
class ControllerMpGdprRequestAnonymouse extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('mpgdpr/requestanonymouse');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/stylesheet/mpgdpr/mpgdpr.css');
		$this->load->model('mpgdpr/mpgdpr');

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_request_id'])) {
			$filter_request_id = $this->request->get['filter_request_id'];
		} else {
			$filter_request_id = null;
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_date_deletion'])) {
			$filter_date_deletion = $this->request->get['filter_date_deletion'];
		} else {
			$filter_date_deletion = null;
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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_request_id'])) {
			$url .= '&filter_request_id=' . urlencode(html_entity_decode($this->request->get['filter_request_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_deletion'])) {
			$url .= '&filter_date_deletion=' . $this->request->get['filter_date_deletion'];
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
			'href' => $this->url->link('mpgdpr/requestanonymouse', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url, $this->mpgdpr->ssl)
		);


		$data['token'] = $this->session->data[$this->mpgdpr->token];
		$data['var_token'] = $this->mpgdpr->token;

		$data['delete'] = $this->url->link('mpgdpr/requestanonymouse/delete', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url, $this->mpgdpr->ssl);

		$data['requests'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'filter_status' => $filter_status,
			'filter_request_id' => $filter_request_id,
			'filter_email' => $filter_email,
			'filter_date_deletion' => $filter_date_deletion,
			'filter_date_start' => $filter_date_start,
			'filter_date_end' => $filter_date_end,
			'filter_time_lap_value' => $filter_time_lap_value,
			'filter_time_lap' => $filter_time_lap,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$requestanonymouse_total = $this->model_mpgdpr_mpgdpr->getTotalRequestAnonymouses($filter_data);

		$results = $this->model_mpgdpr_mpgdpr->getRequestAnonymouses($filter_data);

		$customer_model = $this->mpgdpr->getAdminCustomerModelString();

		foreach ($results as $result) {

			$customer_info = $this->{$customer_model}->getCustomer($result['customer_id']);
			$email = '';
			if($customer_info) {
				$email = $customer_info['email'];
			}

			$status_text = '';

			if($result['status']==$this->mpgdpr->requestanonymouse_expire) {
				$status_text = $this->language->get('text_expire');
			} else if($result['status']==$this->mpgdpr->requestanonymouse_confirmed) {
				$status_text = $this->language->get('text_confirmed');
			} else if($result['status']==$this->mpgdpr->requestanonymouse_awating) {
				$status_text = $this->language->get('text_awating');
			} else if($result['status']==$this->mpgdpr->requestanonymouse_complete) {
				$status_text = $this->language->get('text_complete');
			} else if($result['status']==$this->mpgdpr->requestanonymouse_deny) {
				$status_text = $this->language->get('text_deny');
			}


			$data['requests'][] = array(
				'mpgdpr_deleteme_id' => $result['mpgdpr_deleteme_id'],
				'email'        => $email,
				'date_deletion'        => $result['date_deletion'] != '0000-00-00' ? $result['date_deletion'] : '',
				'status_text'        => $status_text,
				'status'        => $result['status'],
				'date_added'        => $result['date_added'] ,
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_all'] = $this->language->get('text_all');

		$data['text_request_id'] = $this->language->get('text_request_id');
		$data['text_deny_reason'] = $this->language->get('text_deny_reason');
		$data['text_deleteanonymouse_warning'] = $this->language->get('text_deleteanonymouse_warning');


		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_request_id'] = $this->language->get('entry_request_id');
		$data['entry_date_deletion'] = $this->language->get('entry_date_deletion');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_time_lap_value'] = $this->language->get('entry_time_lap_value');
		$data['entry_time_lap'] = $this->language->get('entry_time_lap');
		$data['entry_days'] = $this->language->get('entry_days');
		$data['entry_weeks'] = $this->language->get('entry_weeks');
		$data['entry_months'] = $this->language->get('entry_months');
		$data['entry_years'] = $this->language->get('entry_years');

		$data['entry_denyreason'] = $this->language->get('entry_denyreason');


		$data['column_email'] = $this->language->get('column_email');
		$data['column_request_id'] = $this->language->get('column_request_id');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_deletion'] = $this->language->get('column_date_deletion');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_action'] = $this->language->get('column_action');
						
		$data['button_deny'] = $this->language->get('button_deny');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_delete_customer'] = $this->language->get('button_delete_customer');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['error_date_deletion'] = $this->language->get('error_date_deletion');

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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_request_id'])) {
			$url .= '&filter_request_id=' . urlencode(html_entity_decode($this->request->get['filter_request_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_date_deletion'])) {
			$url .= '&filter_date_deletion=' . $this->request->get['filter_date_deletion'];
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
		$pagination->total = $requestanonymouse_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('mpgdpr/requestanonymouse', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url . '&page={page}', $this->mpgdpr->ssl);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($requestanonymouse_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($requestanonymouse_total - $this->config->get('config_limit_admin'))) ? $requestanonymouse_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $requestanonymouse_total, ceil($requestanonymouse_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['filter_status'] = $filter_status;
		$data['filter_request_id'] = $filter_request_id;
		$data['filter_date_deletion'] = $filter_date_deletion;
		$data['filter_email'] = $filter_email;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_time_lap_value'] = $filter_time_lap_value;
		$data['filter_time_lap'] = $filter_time_lap;

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['deletion_statuses'] = array();
		$data['deletion_statuses'][] = array(
			'value' => $this->mpgdpr->requestanonymouse_expire,
			'text' => $this->language->get('text_expire'),
		);
		$data['deletion_statuses'][] = array(
			'value' => $this->mpgdpr->requestanonymouse_confirmed,
			'text' => $this->language->get('text_confirmed'),
		);
		$data['deletion_statuses'][] = array(
			'value' => $this->mpgdpr->requestanonymouse_awating,
			'text' => $this->language->get('text_awating'),
		);
		$data['deletion_statuses'][] = array(
			'value' => $this->mpgdpr->requestanonymouse_complete,
			'text' => $this->language->get('text_complete'),
		);		
		$data['deletion_statuses'][] = array(
			'value' => $this->mpgdpr->requestanonymouse_deny,
			'text' => $this->language->get('text_deny'),
		);

		$data['requestanonymouse_confirmed'] = $this->mpgdpr->requestanonymouse_confirmed;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->mpgdpr->view('mpgdpr/requestanonymouse', $data));
	}

	public function deleteAction() {
		$json = array();
		$this->load->language('mpgdpr/requestanonymouse');
		$this->load->model('mpgdpr/mpgdpr');
		if(empty($this->request->get['o']) || $this->request->get['o'] != 1) {
			$json['error'] = $this->language->get('error_invalid');
		}
		if(empty($this->request->post['date_deletion']) || !empty($this->request->post['date_deletion']) && $this->request->post['date_deletion']=='0000-00-00' ) {
			$json['date_deletion'] = $this->language->get('error_date_deletion');
		}
		if(!$json) {
			// insert updates and close the popup 
			$this->model_mpgdpr_mpgdpr->updateRequestAnonymouseAndAnonymouse($this->request->post);
			$json['text_complete'] = $this->language->get('text_complete');
			$json['success'] = $this->language->get('success_delete');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function denyAction() {
		$json = array();
		$this->load->language('mpgdpr/requestanonymouse');
		$this->load->model('mpgdpr/mpgdpr');
		if(empty($this->request->get['o']) || $this->request->get['o'] != 1) {
			$json['error'] = $this->language->get('error_invalid');
		}
		if(empty($this->request->post['date_deletion']) || !empty($this->request->post['date_deletion']) && $this->request->post['date_deletion']=='0000-00-00' ) {
			$json['date_deletion'] = $this->language->get('error_date_deletion');
		}
		if ((utf8_strlen($this->request->post['denyreason']) < 3) || (utf8_strlen($this->request->post['denyreason']) > 10000)) {
				$json['denyreason'] = $this->language->get('error_denyreason');
		}
		if(!$json) {
			$this->model_mpgdpr_mpgdpr->updateRequestAnonymouseAndDeny($this->request->post); 
			$json['text_deny'] = $this->language->get('text_deny');
			$json['success'] = $this->language->get('success_deny');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
