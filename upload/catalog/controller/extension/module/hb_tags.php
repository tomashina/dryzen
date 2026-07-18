<?php
class ControllerExtensionModuleHbTags extends Controller {
    public function cron(){
        $this->load->model('extension/module/hb_tags');

        $this->model_extension_module_hb_tags->addlog("--CRON JOB TRIGGERED--");

        $authkey = (isset($this->request->get['key'])) ? $this->request->get['key'] : '';

        if ($this->authenticate_key($authkey)){
            $this->model_extension_module_hb_tags->run_batch();
        }

        echo 'Operation Done!';  
    }

    public function manual(){
        $json = [];
        $this->load->model('extension/module/hb_tags');

        $this->model_extension_module_hb_tags->addlog("--MANUAL MODE--");

        $authkey = (isset($this->request->get['key'])) ? $this->request->get['key'] : '';

        if ($this->authenticate_key($authkey)){
            $limit_start = 0;
            $limit_count = 10;

            $products_total = $this->model_extension_module_hb_tags->getTotalEmptyTagsProducts();
            $products       = $this->model_extension_module_hb_tags->getEmptyTagsProducts($limit_start, $limit_count);
            
            if ($products_total > 0){
                if ($products_total > $limit_count) {
                    $json['success'] = 'Processing '.$limit_count.' of remaining '.$products_total.' products';
                    $json['next'] = str_replace('&amp;', '&', $this->url->link('extension/module/hb_tags/manual', 'key=' . $authkey, true));
                } else {
                    $json['next'] = '';
                    $json['success'] = 'Completed: Product Tags Generated';
                    $json['complete'] = true;
                }
                
                foreach ($products as $product){
                    $data['product_id'] = $product['product_id'];
                    $data['language_id'] = $product['language_id'];
    
                    $this->model_extension_module_hb_tags->generate_tags($data);
                }
            }else {
                $json['error'] = 'Product Tags are already available for all products.';
            }
        }else{
            $json['error'] = 'Invalid Key';
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

    public function manual_selected(){
        $json = [];
        $this->load->model('extension/module/hb_tags');

        $this->model_extension_module_hb_tags->addlog("--MANUAL SELECTED MODE--");

        $authkey = (isset($this->request->get['key'])) ? $this->request->get['key'] : '';

        if ($this->authenticate_key($authkey)){
            $this->load->model('localisation/language');
		    $languages = $this->model_localisation_language->getLanguages();

            $selected = (isset($this->request->post['selected']))? $this->request->post['selected'] : [];

            if (empty($selected)){
                $json['error'] = 'No Records Selected';
            }

            if (!$json){
                foreach ($selected as $id) {
                    $data['product_id'] = $id;
                    foreach ($languages as $language){
                        $data['language_id'] = $language['language_id'];
                        $this->model_extension_module_hb_tags->generate_tags($data);
                    }                    
                }
    
                $json['success'] = 'Product Tags Generated for the selected products across all languages';
            }
        }else{
            $json['error'] = 'Invalid Key';
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

    private function authenticate_key($key){
        $this->load->model('extension/module/hb_tags');

        if ($key == $this->config->get('hb_tags_authkey')){
            return true;
        }else{
            $this->model_extension_module_hb_tags->addlog('Invalid Authentication: Key: '.$key);
            return false;
        }
    }	
}
