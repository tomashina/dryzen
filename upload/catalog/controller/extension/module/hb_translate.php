<?php
class ControllerExtensionModuleHbTranslate extends Controller {
    public function cron() {
        if (!$this->config->get('hb_translate_cron_enable')){
            die('Cron is not enabled');
        }

        $this->load->model('extension/module/hb_translate');

        $this->model_extension_module_hb_translate->addlog("--CRON JOB TRIGGERED--");

        $authkey    = (isset($this->request->get['key'])) ? $this->request->get['key'] : '';

        if ($this->authenticate_key($authkey)){
            $this->model_extension_module_hb_translate->fix_empty_fields();

            $this->load->model('localisation/language');
            $languages = $this->model_localisation_language->getLanguages();

            $languageMap = [];
            foreach ($languages as $language) {
                $languageMap[$language['language_id']] = $language;
            }

            $limit_count = $this->config->get('hb_translate_batch_count');

            $data['master_language_id'] = $this->config->get('hb_translate_master_language');
            $data['source_language_code'] = isset($languageMap[$data['master_language_id']]) ? $languageMap[$data['master_language_id']]['code'] : '';

            $types = array('product', 'category', 'information', 'manufacturer');
            foreach ($types as $type){
                $data['type'] = $type;

                $columns = $this->model_extension_module_hb_translate->getColumns($type);
                if (!empty($columns)) {
                    foreach ($columns as $column){
                        $data['column'] = $column;
                        $records = $this->model_extension_module_hb_translate->getEmpty($data, $limit_count);
                        
                        if (!empty($records)) {
                            foreach ($records as $record) {
                                $data['id'] = $record['id'];
                                $data['target_language_id'] = $record['language_id'];
                                $data['target_language_code'] = isset($languageMap[$record['language_id']]) ? $languageMap[$record['language_id']]['code'] : '';
                                $data['attribute_id'] = (isset($record['attribute_id'])) ? $record['attribute_id'] : '0';
                                
                                $this->model_extension_module_hb_translate->translate_column_data($data);
                            } 
                        }else{
                            $this->model_extension_module_hb_translate->addlog("No empty fields found for ".$type."- Field: ".$column);
                        }
                    }
                }else{
                    $this->model_extension_module_hb_translate->addlog("No fields enabled for ".$type);
                }
            }

            $data['column']     = (isset($this->request->post['column'])) ? $this->request->post['column'] : '';

        }

        echo 'Operation Done!';        
    }

    private function authenticate_key($key){
        $this->load->model('extension/module/hb_translate');

        if ($key == $this->config->get('hb_translate_cron_key')){
            return true;
        }else{
            $this->model_extension_module_hb_translate->addlog('Invalid Authentication: Key: '.$key);
            return false;
        }
    }

    public function generate(){
        $json = [];
        $this->load->model('extension/module/hb_translate');

		$authkey    = (isset($this->request->post['authkey'])) ? $this->request->post['authkey'] : '';

        $this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

        $languageMap = [];
        foreach ($languages as $language) {
            $languageMap[$language['language_id']] = $language;
        }

        $data['master_language_id'] = $this->config->get('hb_translate_master_language');
        $data['source_language_code'] = isset($languageMap[$data['master_language_id']]) ? $languageMap[$data['master_language_id']]['code'] : '';

        $data['type']       = (isset($this->request->post['type'])) ? $this->request->post['type'] : '';
        $data['column']     = (isset($this->request->post['column'])) ? $this->request->post['column'] : '';

        $previous_total      = (isset($this->request->post['previous_total'])) ? $this->request->post['previous_total'] : '0';
		
        if ($this->authenticate_key($authkey)){
            switch ($data['type']) {
                case 'product':
                    $enabled_fields = $this->config->get('hb_translate_fields_product');
                    break;
                
                case 'category':
                    $enabled_fields = $this->config->get('hb_translate_fields_category');
                    break;

                case 'information':
					$enabled_fields = $this->config->get('hb_translate_fields_information');
                    break;
                
                case 'manufacturer':
                    $enabled_fields = $this->config->get('hb_translate_fields_manufacturer');
                    break;

                default:
                    $enabled_fields = array();
                    break;
            }

            if (is_array($enabled_fields) && in_array($data['column'], $enabled_fields)){
                $limit_count = 5;
                $total_empty = $this->model_extension_module_hb_translate->getTotalEmpty($data);

                if ($total_empty > 0){
                    if ($total_empty == $previous_total){
                        $json['error'] = 'Invalid Source Data for '.$data['type'].' and column '.$data['column'];
                    }else{
                        $records = $this->model_extension_module_hb_translate->getEmpty($data, $limit_count);
    
                        foreach ($records as $record) {
                            $data['id'] = $record['id'];
                            $data['target_language_id'] = $record['language_id'];
                            $data['target_language_code'] = isset($languageMap[$record['language_id']]) ? $languageMap[$record['language_id']]['code'] : '';
                            $data['attribute_id'] = (isset($record['attribute_id'])) ? $record['attribute_id'] : '0';
                            
                            if (!$this->model_extension_module_hb_translate->translate_column_data($data)) {
                                $json['error'] = 'Issue in your API, check work log!';
                                break; // Exit the loop
                            }else{
                                if ($total_empty > $limit_count) {
                                    $json['success'] = 'Processing '.$limit_count.' of remaining '.$total_empty.' records';
                                    $json['next'] = 'set';
                                    $json['previous_total'] = $total_empty;
                                } else {
                                    $json['success'] = 'Completed: '.$data['column']. ' generated for ' .$data['type'];
                                }
                            }
                        }                          
                    }              
                }else{
                    $json['error'] = $data['column'].' already available for all '.$data['type'];
                }
            }else{
                $json['error'] = 'Cannot generate. Column '.$data['column'].' is disabled in the setting for '.$data['type'];
            }
            
        }
			
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
	
}