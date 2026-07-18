<?php
class ControllerExtensionModuleHbAigen extends Controller {
    public function cron() {
        $this->load->model('extension/module/hb_aigen');
        $this->model_extension_module_hb_aigen->addlog('**CRON MODE STARTED**');

        $authkey = (isset($this->request->get['authkey'])) ? $this->request->get['authkey'] : '';

        if (!$this->authenticate($authkey)) {
            die('AUTHORIZATION FAILED');
        }

        if (!$this->config->get('hb_aigen_status')) {
            die('EXTENSION IS DISABLED');
        }

        if ($this->config->get('hb_aigen_one_language')) {
            $language_id = (int)$this->config->get('hb_aigen_language_id');
            $this->model_extension_module_hb_aigen->generateForLanguage($language_id);
        }else{
            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();

            foreach ($languages as $language) {
                $this->model_extension_module_hb_aigen->generateForLanguage($language['language_id']);
            }
        }        

        $this->model_extension_module_hb_aigen->addlog('**CRON MODE COMPLETED**');
        die('CRON MODE COMPLETED');
    }

    public function prompt_preview(){
        $json = [];

        $this->load->language('extension/module/hb_aigen');
        $this->load->model('extension/module/hb_aigen');

        if (!$this->authenticate($this->request->post['authkey'])) {
            $json['error'] = 'AUTHORIZATION FAILED';
        }		

        $type 			= (isset($this->request->post['type'])) ? trim($this->request->post['type']) : '';
        $item_id 		= (isset($this->request->post['item_id'])) ? (int)$this->request->post['item_id'] : '';
        $language_id 	= (isset($this->request->post['language_id'])) ? (int)$this->request->post['language_id'] : '';

        if (empty($type) or empty($item_id) or empty($language_id)) {
            $json['error'] = 'Invalid Data!';
        }        
        
        if (!$json){            
            $prompt = $this->model_extension_module_hb_aigen->generate_prompt($type, $item_id, $language_id);

            if (!empty($prompt)) {
                $json['success'] = nl2br(htmlspecialchars($prompt, ENT_NOQUOTES, 'UTF-8'));
            } else {
                $json['error'] = $this->language->get('error_prompt_template');
            }
        }		

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
	}

    public function generate_item(){
		$json = [];

        $this->load->language('extension/module/hb_aigen');
        $this->load->model('extension/module/hb_aigen');

        if (!$this->authenticate($this->request->post['authkey'])) {
            $json['error'] = 'AUTHORIZATION FAILED';
        }

        if (!$this->config->get('hb_aigen_status')) {
            $json['error'] = 'EXTENSION IS DISABLED';
        }

		$type 			= (isset($this->request->post['type'])) ? $this->request->post['type'] : '';
		$item_id 		= (isset($this->request->post['item_id'])) ? (int)$this->request->post['item_id'] : '';
		$language_id 	= (isset($this->request->post['language_id'])) ? (int)$this->request->post['language_id'] : '';

		if (empty($type) || empty($item_id)) {
			$json['error'] = $this->language->get('error_generate_content');
		}

		if (!$json){		
			if ($this->config->get('hb_aigen_one_language')) {
				$language_id = (int)$this->config->get('hb_aigen_language_id');

				if ($this->model_extension_module_hb_aigen->generateContent($type, $item_id, $language_id)) {
					$json['success'] = $this->language->get('success_generate');
				} else {
					$json['error'] = $this->language->get('error_generate_failed');
				}
			}else{
				$this->load->model('localisation/language');			
				$languages = $this->model_localisation_language->getLanguages();

				foreach ($languages as $language) {
					if ($this->model_extension_module_hb_aigen->generateContent($type, $item_id, $language['language_id'])) {
						$json['success'] = $this->language->get('success_generate');
					} else {
						$json['error'] = $this->language->get('error_generate_failed');
						break;
					}
				}
			}						
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function generate_selected(){
		$json = [];

        $this->load->language('extension/module/hb_aigen');
        $this->load->model('extension/module/hb_aigen');

        if (!$this->authenticate($this->request->post['authkey'])) {
            $json['error'] = 'AUTHORIZATION FAILED';
        }

        if (!$this->config->get('hb_aigen_status')) {
            $json['error'] = 'EXTENSION IS DISABLED';
        }
        
		$type 			= (isset($this->request->post['type'])) ? $this->request->post['type'] : '';

		$selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];

		if (empty($selected)){
			$json['error'] = $this->language->get('error_no_record_selected');
		}		

		if (empty($type)) {
			$json['error'] = $this->language->get('error_generate_content');
		}

		if (!$json){
			if ($this->config->get('hb_aigen_one_language')) {
				$language_id = (int)$this->config->get('hb_aigen_language_id');

				foreach ($selected as $item_id) {				
					if ($this->model_extension_module_hb_aigen->generateContent($type, $item_id, $language_id)) {
						$json['success'] = $this->language->get('success_generate');
					} else {
						$json['error'] = $this->language->get('error_generate_failed');
					}
				}
			}else{
				$this->load->model('localisation/language');			
				$languages = $this->model_localisation_language->getLanguages();

				foreach ($languages as $language) {
					foreach ($selected as $item_id) {				
						if ($this->model_extension_module_hb_aigen->generateContent($type, $item_id, $language['language_id'])) {
							$json['success'] = $this->language->get('success_generate');
						} else {
							$json['error'] = $this->language->get('error_generate_failed');
							break;
						}
					}
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

    private function authenticate($authkey){
        $actual_authkey = $this->config->get('hb_aigen_cron_key');
        if ($authkey != $actual_authkey or $authkey == '') {
            return false;
        }else{
            return true;
        }
    } 
}
