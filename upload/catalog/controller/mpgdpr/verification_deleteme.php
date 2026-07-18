<?php
class ControllerMpGdprVerificationDeleteMe extends Controller {
	private $error = array();
	public function index() {

		$this->load->language('mpgdpr/verification_deleteme');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');
	
		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_gdpr_datarequest'),
			'href' => $this->url->link('mpgdpr/verification_deleteme', '', $this->mpgdpr->ssl)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['code'] = '';

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['entry_code'] = $this->language->get('entry_code');

		$data['text_message'] = $this->language->get('text_verifycode');

		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_verify'] = $this->language->get('button_verify');
				
		$data['action'] = $this->url->link('common/home', '', $this->mpgdpr->ssl);
	
	
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->mpgdpr->view('mpgdpr/verification_deleteme', $data));
	}

	public function verification() {
		$json = array();
		$this->load->language('mpgdpr/verification_deleteme');
		$this->load->language('mpgdpr/gdpr');
		$this->load->model('mpgdpr/mpgdpr');

		if(!isset($this->request->get['o']) || (isset($this->request->get['o']) && $this->request->get['o'] != 1) ) {
			$json['error'] = $this->language->get('error_invalid');
		}

		if(empty($this->request->post['code'])) {
			$json['code_empty'] = $this->language->get('error_code_empty');
		}

		if(!$json) {
			// validate code here
			$request_info = $this->model_mpgdpr_mpgdpr->getDeleteMeRequestByCode($this->request->post['code']);
	

			if(empty($request_info)) {
				$json['error'] = $this->language->get('error_code_invalid');		
			}
		}
		if(!$json) {
			// code found, lets check if expired or not. when status is awating confirmation
			$today = date('Y-m-d H:i:s');
			if(strtotime($today) > strtotime($request_info['expire_on']) && $request_info['status'] == $this->mpgdpr->requestanonymouse_awating ) {

				// expire the request as timeout
				$this->model_mpgdpr_mpgdpr->updateDeleteMeRequestStatus($request_info['mpgdpr_deleteme_id'], $this->mpgdpr->requestanonymouse_expire);

				$json['error'] = $this->language->get('error_code_expire');	
			}
		}

		if(!$json) {
			// code found, lets check if status awating or something else

			if($request_info['status'] == $this->mpgdpr->requestanonymouse_awating ) {

				// complete the verification here
				$this->model_mpgdpr_mpgdpr->updateDeleteMeRequestStatus($request_info['mpgdpr_deleteme_id'], $this->mpgdpr->requestanonymouse_confirmed);

				$json['success'] = $this->language->get('text_verify_success');	
			}
			// let check if what is the status of request now and response accordingly
			// if request status is expired
			if($request_info['status'] == $this->mpgdpr->requestanonymouse_expire ) {
				$json['error'] = $this->language->get('error_code_expire');	
			}
			// if request status is confirmed
			if($request_info['status'] == $this->mpgdpr->requestanonymouse_confirmed ) {
				$json['error'] = $this->language->get('text_verified');	
			}

			// here we check if json has respone or not. if not then say request unknow
			if(!$json) {
				$json['error'] = $this->language->get('text_request_unknown');	
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}