<?php
class ControllerMpGdprApiLogin extends Controller {

	public function ignit() {


		// echo "API LOGIN FILE";
		$this->mpgdpr->log("Call API LOGIN FILE");

		// echo "\n";
		// echo "GET";
		// echo "\n\n";
		// print_r($this->request->get);

		$this->mpgdpr->log("_GET " . print_r($this->request->get, 1));

		// echo "\n";
		// echo "POST";
		// echo "\n\n";
		// print_r($this->request->post);

		$this->mpgdpr->log("_POST " . print_r($this->request->post, 1));

		// echo "\n";
		// echo "SERVER";
		// echo "\n\n";
		// print_r($this->request->server);

		$this->mpgdpr->log("_SERVER " . print_r($this->request->server, 1));

		$this->load->language('mpgdpr/api_login');

		$json = array();

		if(!isset($this->request->post['key']) || !isset($this->request->post['value']) || !isset($this->request->post['filename'])|| !isset($this->request->post['token'])) {

			$json['error'] = array();
			$json['error']['code'] = 49;
			$json['error']['msg'] = $this->language->get('error_msg_49');

		}

		if(
			(!isset($this->request->server['HTTP_X_OC_MPGDPR']) ||
			 (isset($this->request->server['HTTP_X_OC_MPGDPR']) && $this->request->server['HTTP_X_OC_MPGDPR'] != 1)
			) ||
			(!isset($this->request->server['HTTP_X_OC_MPGDPR_FRONT']) ||
			 (isset($this->request->server['HTTP_X_OC_MPGDPR_FRONT']) && $this->request->server['HTTP_X_OC_MPGDPR_FRONT'] != $this->request->post['token'])
			)
		) {

			$json['error'] = array();
			$json['error']['code'] = 53;
			$json['error']['msg'] = $this->language->get('error_msg_53');

		}

		if(!$json) {
			if(!empty($this->request->post['filename']) && !file_exists(DIR_CACHE . $this->request->post['filename'])) {
				$json['error'] = array();
				$json['error']['code'] = 50;
				$json['error']['msg'] = $this->language->get('error_msg_50');
			}
		}

		if(!$json) {
			$handle = fopen(DIR_CACHE . $this->request->post['filename'], 'r');

			$content = fread($handle, filesize(DIR_CACHE . $this->request->post['filename']));
			fclose($handle);

			$file_content = json_decode($content, true);

			if(!isset($file_content['key']) || !isset($file_content['value']) || !isset($file_content['customer_id']) || !isset($file_content['filename']) ) {
				$json['error'] = array();
				$json['error']['code'] = 51;
				$json['error']['msg'] = $this->language->get('error_msg_51');
			}
		}


		if(!$json) {
			if($file_content['key'] != $this->request->post['key'] || $file_content['value'] != $this->request->post['value'] || $file_content['filename'] != $this->request->post['filename'] || !$file_content['customer_id']) {
				$json['error'] = array();
				$json['error']['code'] = 52;
				$json['error']['msg'] = $this->language->get('error_msg_52');
			}
		}


		// echo "\n";
		// echo "file_content";
		// echo "\n\n";
		// print_r($file_content);

		// echo "\n";
		// echo "JSON";
		// echo "\n\n";
		// print_r($json);



		if(!$json) {

			$json = array_merge($json, $this->request->request);

			$this->load->model('mpgdpr/mpgdpr');
			$this->model_mpgdpr_mpgdpr->anonymouseCustomerData($file_content['customer_id']);
			$json['success'] = 'truefalse';

		}


		$this->mpgdpr->log("unlink file: " . DIR_CACHE . $this->request->post['filename']);

		@unlink(DIR_CACHE . $this->request->post['filename']);

		$this->mpgdpr->log("JSON RESPONSE " . print_r($json, 1));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}