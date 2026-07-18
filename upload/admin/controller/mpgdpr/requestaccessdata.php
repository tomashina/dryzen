<?php
class ControllerMpGdprRequestAccessData extends Controller {
	private $error = array();

	public function delete() {
		$this->load->language('mpgdpr/requestaccessdata');

		$this->document->setTitle($this->language->get('heading_title'));


		$this->load->model('mpgdpr/mpgdpr');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $mpgdpr_datarequest_id) {
				$this->model_mpgdpr_mpgdpr->deleteRequestAccessData($mpgdpr_datarequest_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

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

			if (isset($this->request->get['filter_date_send'])) {
				$url .= '&filter_date_send=' . $this->request->get['filter_date_send'];
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

			$this->response->redirect($this->url->link('mpgdpr/requestaccessdata', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url, $this->mpgdpr->ssl));
		}

		$this->index();
	}

	public function index() {
		$this->load->language('mpgdpr/requestaccessdata');

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

		if (isset($this->request->get['filter_date_send'])) {
			$filter_date_send = $this->request->get['filter_date_send'];
		} else {
			$filter_date_send = null;
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

		if (isset($this->request->get['filter_date_send'])) {
			$url .= '&filter_date_send=' . $this->request->get['filter_date_send'];
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
			'href' => $this->url->link('mpgdpr/requestaccessdata', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url, $this->mpgdpr->ssl)
		);


		$data['token'] = $this->session->data[$this->mpgdpr->token];
		$data['var_token'] = $this->mpgdpr->token;

		$data['delete'] = $this->url->link('mpgdpr/requestaccessdata/delete', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url, $this->mpgdpr->ssl);


		$data['requests'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'filter_status' => $filter_status,
			'filter_request_id' => $filter_request_id,
			'filter_email' => $filter_email,
			'filter_date_send' => $filter_date_send,
			'filter_date_start' => $filter_date_start,
			'filter_date_end' => $filter_date_end,
			'filter_time_lap_value' => $filter_time_lap_value,
			'filter_time_lap' => $filter_time_lap,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$requestaccessdata_total = $this->model_mpgdpr_mpgdpr->getTotalRequestAccessDatas($filter_data);

		$results = $this->model_mpgdpr_mpgdpr->getRequestAccessDatas($filter_data);

		$customer_model = $this->mpgdpr->getAdminCustomerModelString();

		// enable action buttons only when request is confirmed
		$data['requestaccess_confirmed'] = $this->mpgdpr->requestaccess_confirmed;

		foreach ($results as $result) {

			$email = '';
			$customer_info = $this->{$customer_model}->getCustomer($result['customer_id']);
			if($customer_info) {
				$email = $customer_info['email'];
			}

			$status_text = '';

			if($result['status']==$this->mpgdpr->requestaccess_expire) {
				$status_text = $this->language->get('text_expire');
			} else if($result['status']==$this->mpgdpr->requestaccess_confirmed) {
				$status_text = $this->language->get('text_confirmed');
			} else if($result['status']==$this->mpgdpr->requestaccess_awating) {
				$status_text = $this->language->get('text_awating');
			} else if($result['status']==$this->mpgdpr->requestaccess_reportsend) {
				$status_text = $this->language->get('text_reportsend');
			} else if($result['status']==$this->mpgdpr->requestaccess_deny) {
				$status_text = $this->language->get('text_deny');
			}


			$data['requests'][] = array(
				'mpgdpr_datarequest_id' => $result['mpgdpr_datarequest_id'],
				'email'        => $email,
				'date_send'        => $result['date_send'] != '0000-00-00' ? $result['date_send'] : '',
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
		$data['text_send_report'] = $this->language->get('text_send_report');
		$data['text_request_id'] = $this->language->get('text_request_id');
		$data['text_deny_reason'] = $this->language->get('text_deny_reason');
		$data['text_loading'] = $this->language->get('text_loading');


		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_request_id'] = $this->language->get('entry_request_id');
		$data['entry_date_send'] = $this->language->get('entry_date_send');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_time_lap_value'] = $this->language->get('entry_time_lap_value');
		$data['entry_time_lap'] = $this->language->get('entry_time_lap');
		$data['entry_days'] = $this->language->get('entry_days');
		$data['entry_weeks'] = $this->language->get('entry_weeks');
		$data['entry_months'] = $this->language->get('entry_months');
		$data['entry_years'] = $this->language->get('entry_years');

		$data['entry_download'] = $this->language->get('entry_download');
		$data['entry_denyreason'] = $this->language->get('entry_denyreason');
		
		$data['help_download'] = $this->language->get('help_download');


		$data['column_email'] = $this->language->get('column_email');
		$data['column_request_id'] = $this->language->get('column_request_id');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_send'] = $this->language->get('column_date_send');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_action'] = $this->language->get('column_action');
						
		$data['button_deny'] = $this->language->get('button_deny');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_sendreport'] = $this->language->get('button_sendreport');
		$data['button_scheduledreport'] = $this->language->get('button_scheduledreport');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_upload'] = $this->language->get('button_upload');

		$data['error_date_send'] = $this->language->get('error_date_send');

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

		if (isset($this->request->get['filter_date_send'])) {
			$url .= '&filter_date_send=' . $this->request->get['filter_date_send'];
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
		$pagination->total = $requestaccessdata_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('mpgdpr/requestaccessdata', $this->mpgdpr->token.'=' . $this->session->data[$this->mpgdpr->token] . $url . '&page={page}', $this->mpgdpr->ssl);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($requestaccessdata_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($requestaccessdata_total - $this->config->get('config_limit_admin'))) ? $requestaccessdata_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $requestaccessdata_total, ceil($requestaccessdata_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['filter_status'] = $filter_status;
		$data['filter_request_id'] = $filter_request_id;
		$data['filter_date_send'] = $filter_date_send;
		$data['filter_email'] = $filter_email;
		$data['filter_date_start'] = $filter_date_start;
		$data['filter_date_end'] = $filter_date_end;
		$data['filter_time_lap_value'] = $filter_time_lap_value;
		$data['filter_time_lap'] = $filter_time_lap;

		$data['requestaccess_statuses'] = array();
		$data['requestaccess_statuses'][] = array(
			'value' => $this->mpgdpr->requestaccess_expire,
			'text' => $this->language->get('text_expire'),
		);
		$data['requestaccess_statuses'][] = array(
			'value' => $this->mpgdpr->requestaccess_confirmed,
			'text' => $this->language->get('text_confirmed'),
		);
		$data['requestaccess_statuses'][] = array(
			'value' => $this->mpgdpr->requestaccess_awating,
			'text' => $this->language->get('text_awating'),
		);
		$data['requestaccess_statuses'][] = array(
			'value' => $this->mpgdpr->requestaccess_reportsend,
			'text' => $this->language->get('text_reportsend'),
		);
		$data['requestaccess_statuses'][] = array(
			'value' => $this->mpgdpr->requestaccess_deny,
			'text' => $this->language->get('text_deny'),
		);

		$data['last_upload_code'] = '';
		$data['upload_file'] = '';
		if(isset($this->session->data['last_upload_code'])) {
			$data['last_upload_code'] = $this->session->data['last_upload_code'];
			$upload_info = $this->model_mpgdpr_mpgdpr->getUploadByCode($this->session->data['last_upload_code']);
			if($upload_info) {
				$data['upload_file'] = $upload_info['name'];
			}
		}
		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->mpgdpr->view('mpgdpr/requestaccessdata', $data));
	}


	// upload attachment file and return the name.
	public function uploadAttachment() {
		$this->load->language('mpgdpr/requestaccessdata');
		$json = array();

		// create gdpr_accessdata reports folder first
		$dir = 'mpgdpr_accessdata/';
		$this->mpgdpr->mkdir(DIR_UPLOAD . $dir);

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array();

			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('mpgdpr_file_ext_allowed'));

			$filetypes = explode("\n", $extension_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('mpgdpr_file_mime_allowed'));

			$filetypes = explode("\n", $mime_allowed);

			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['file']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}


		if (!$json) {
			$file = $filename . '.' . token(32);

			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $dir. $file);

			// Hide the uploaded file name so people can not link to it directly.
			$this->load->model('mpgdpr/mpgdpr');

			$this->session->data['last_upload_code'] = $json['code'] = $this->model_mpgdpr_mpgdpr->addUpload($filename, $file);

			$json['filename'] = $filename;

			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
 
	public function sendReportAction() {
		$json = array();
		$this->load->language('mpgdpr/requestaccessdata');
		$this->load->model('mpgdpr/mpgdpr');
		if(empty($this->request->get['o']) || $this->request->get['o'] != 1) {
			$json['error'] = $this->language->get('error_invalid');
		}
		if(empty($this->request->post['attachment'])) {
			$json['attachment'] = $this->language->get('error_attachment');
		}
		
		if(empty($this->request->post['date_send']) || !empty($this->request->post['date_send']) && $this->request->post['date_send']=='0000-00-00' ) {
			$json['date_send'] = $this->language->get('error_date_send');
		}

		if(isset($this->session->data['last_upload_code']) && isset($this->request->post['attachment']) && ($this->session->data['last_upload_code'] != $this->request->post['attachment'])) {
			$json['attachment'] = $this->language->get('error_upload');
		}
		if(!$json) {			
			// insert updates and close the popup 
			$this->model_mpgdpr_mpgdpr->updateRequestAccessDataAndSendReport($this->request->post);
			$json['text_reportsend'] = $this->language->get('text_reportsend');
			$json['success'] = $this->language->get('success_accessdata_sendreport');
			if(isset($this->session->data['last_upload_code'])) {
				unset($this->session->data['last_upload_code']);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function denyAction() {
		$json = array();
		$this->load->language('mpgdpr/requestaccessdata');
		$this->load->model('mpgdpr/mpgdpr');
		if(empty($this->request->get['o']) || $this->request->get['o'] != 1) {
			$json['error'] = $this->language->get('error_invalid');
		}
		if ((utf8_strlen($this->request->post['denyreason']) < 3) || (utf8_strlen($this->request->post['denyreason']) > 10000)) {
				$json['denyreason'] = $this->language->get('error_denyreason');
		}

		if(!$json) {
			// insert updates and close the popup 
			$this->model_mpgdpr_mpgdpr->updateRequestAccessDataAndDeny($this->request->post);
			$json['text_deny'] = $this->language->get('text_deny');
			$json['success'] = $this->language->get('success_accessdata_deny');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'mpgdpr/requestaccessdata')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
