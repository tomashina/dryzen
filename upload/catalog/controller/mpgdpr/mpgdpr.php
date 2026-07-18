<?php
class ControllerMpGdprMpGdpr extends Controller {

	public function acceptanceOfPp() {
		if($this->config->get('mpgdpr_cbstatus') && $this->config->get('mpgdpr_cbpolicy') && $this->config->get('mpgdpr_cbpptrack')) {

			$data['cbpolicy_page'] = $this->config->get('mpgdpr_cbpolicy_page');
			if(!$data['cbpolicy_page']) {
				$data['cbpolicy_page'] = $this->config->get('config_account_id');
			}

			if($data['cbpolicy_page']) {
				$this->load->language('mpgdpr/gdpr');
				$this->load->model('mpgdpr/mpgdpr');
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation($data['cbpolicy_page']);
				if($information_info) {
					$insert_data = array(
						'customer_id' => $this->customer->getId(),
						'policy_id' => $information_info['information_id'],
						'policy_title' => $information_info['title'],
						'policy_description' => $information_info['description'],
					);

					/*13 sep 2019 gdpr session starts*/

					$mpgdpr_policyacceptance_id = $this->model_mpgdpr_mpgdpr->addPolicyAcceptance($this->mpgdpr->codepolicyacceptcookieconsent, $insert_data);

					// Add to request log
					$request_data = array(
						'customer_id' => $this->customer->getId(),
						'email' => $this->customer->getEmail(),
						'date' => date('Y-m-d H:i:s'),
						'custom_string' => sprintf($this->language->get('text_gdpr_policyacceptcookieconsent_custom_msg'), $mpgdpr_policyacceptance_id ),
					);
					$mpgdpr_requestlist_id = $this->model_mpgdpr_mpgdpr->addRequest($this->mpgdpr->codepolicyacceptcookieconsent, $request_data);
					/*13 sep 2019 gdpr session ends*/
				}
			}
		}
	}
}