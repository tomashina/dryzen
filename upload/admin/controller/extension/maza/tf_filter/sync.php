<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */

class ControllerExtensionMazaTfFilterSync extends Controller {
        private $error = array();
        
        public function index(){
                $json = array();
                
                $this->load->language('extension/maza/tf_filter/sync');
                $this->load->model('extension/maza/tf_filter');
                $this->load->model('extension/maza/tf_filter/sync');
                
                if($this->validate()){
                    set_time_limit(0);
                    session_write_close();
                    ignore_user_abort(1);
                
                    if(isset($this->request->post['filter_id'])){ // Sync single filter
                        $this->syncFilter($this->request->post['filter_id']);
                        
                    } elseif(isset($this->request->post['selected'])){ // Sync selected filter
                        foreach($this->request->post['selected'] as $filter_id){
                            $this->syncFilter($filter_id);
                        }
                        
                    } else { // Sync all filters
                        $start = 0;
                        $limit = 5;
                        
                        do{
                            $filters = $this->model_extension_maza_tf_filter->getFilters(['status' => 1, 'start' => $start, 'limit' => $limit, 'sort' => 'date_sync', 'order' => 'ASC']);
                            
                            foreach($filters as $filter){
                                $this->syncFilter($filter['filter_id']);
                            }
                            
                            $start = $start + $limit;
                        } while ($filters);
                    }
                }
                
                if(isset($this->error['warning'])){
                    $json['error'] = $this->error['warning'];
                } else {
                    $json['success'] = $this->language->get('text_success');
                }
                
                   
                $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
        }
        
        private function syncFilter($filter_id){
                // Lock file status
                $process_lock_file = fopen(DIR_STORAGE . 'cache/temp_filter.lock.' . $filter_id,"w+");
                
                if (!flock($process_lock_file,LOCK_EX|LOCK_NB)){
                    // Unable to obtain lock, the previous process is still going on.
                    $this->error['warning'] = $this->language->get('error_process_lock');
                }
                
                $filter_info = $this->model_extension_maza_tf_filter->getFilter($filter_id);
                
                if($filter_info){
                    // Sync filter to values
                    $this->syncFilterToValues($filter_info);
                    
                    // Sync filter to products
                    $this->syncFilterToProducts($filter_info);
                    
                    // Set date sync
                    $this->db->query("UPDATE " . DB_PREFIX . "tf_filter SET date_sync = NOW() WHERE filter_id = '" . (int)$filter_id . "'");
                }
                
                flock($process_lock_file,LOCK_UN); // Unlock process file
        }
        
        private function syncFilterToValues($filter_info){
                if($filter_info['setting']['value_sync_status']){
                    
                    // Options
                    if(!empty($filter_info['setting']['key_option'])){
                        $option_values = $this->model_extension_maza_tf_filter_sync->getOptionsValues($filter_info['setting']['key_option']);
                        $this->model_extension_maza_tf_filter_sync->addValues($filter_info['filter_id'], $filter_info['filter_language_id'], $option_values);
                    }

                    // Filter group
                    if(!empty($filter_info['setting']['key_filter_group'])){
                        $filter_group_values = $this->model_extension_maza_tf_filter_sync->getFilterGroupsValues($filter_info['setting']['key_filter_group']);
                        $this->model_extension_maza_tf_filter_sync->addValues($filter_info['filter_id'], $filter_info['filter_language_id'], $filter_group_values);
                    }

                    // Attributes
                    if(!empty($filter_info['setting']['key_attribute'])){
                        $attribute_values = $this->model_extension_maza_tf_filter_sync->getAttributeValues($filter_info['setting']['key_attribute'], $filter_info['filter_language_id']);
                        $this->model_extension_maza_tf_filter_sync->addValues($filter_info['filter_id'], $filter_info['filter_language_id'], $attribute_values);
                    }
                }
        }
        
        private function syncFilterToProducts($filter_info){
                $setting = array();
                
                if(!empty($filter_info['setting']['key_attribute'])){
                    $setting['attributes'] = $filter_info['setting']['key_attribute'];
                }
                
                if(!empty($filter_info['setting']['key_filter_group'])){
                    $setting['filter_groups'] = $filter_info['setting']['key_filter_group'];
                }
                
                if(!empty($filter_info['setting']['key_option'])){
                    $setting['options'] = $filter_info['setting']['key_option'];
                }
                
                $setting['product_name']        = $filter_info['setting']['key_product_name'];
                $setting['product_description'] = $filter_info['setting']['key_product_description'];
                $setting['product_tags']        = $filter_info['setting']['key_product_tags'];
                
                $this->model_extension_maza_tf_filter_sync->addProductsToValue($filter_info['filter_id'], $filter_info['filter_language_id'], $setting);
        }
        
        private function validate(){
                if(!$this->user->hasPermission('modify', 'extension/maza/tf_filter/sync')){
                    $this->error['warning'] = $this->language->get('error_permission');
                }
                
                return !$this->error;
        }
}
