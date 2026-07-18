<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */

class ControllerExtensionModuleTfFilter extends Controller {
        private $error = array();
        
        public function index() {
                $this->load->language('extension/module/tf_filter');

		$this->document->setTitle($this->language->get('heading_title'));
                
                $this->load->model('setting/module');
                
                // Header
                $header_data = array();
                $header_data['title'] = $this->language->get('heading_title');
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-tf-general', 'href' => false),
                    array('name' => $this->language->get('tab_data'), 'id' => 'tab-tf-data', 'href' => false),
                    array('name' => $this->language->get('tab_layout'), 'id' => 'tab-tf-layout', 'href' => false),
                );
                
                $header_data['menu_active'] = 'tab-tf-general';
                
                // Buttons
                $header_data['buttons'][] = array( // Button save
                    'id' => 'button-save',
                    'name' => $this->language->get('button_save'),
                    'tooltip' => false,
                    'icon' => 'fa-save',
                    'class' => 'btn-primary',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-tf-filter',
                );
                $header_data['buttons'][] = array( // Button cancel
                    'id' => 'button-cancel',
                    'name' => $this->language->get('button_cancel'),
                    'tooltip' => false,
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
                    'target' => '_self',
                    'form_target_id' => false,
                );
                if (isset($this->request->get['module_id'])) {
                    $header_data['buttons'][] = array( // Button delete
                        'id' => 'button-delete',
                        'name' => $this->language->get('button_delete'),
                        'tooltip' => false,
                        'icon' => 'fa-trash',
                        'class' => 'btn-danger',
                        'href' => $this->url->link('extension/module/tf_filter/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true),
                        'target' => '_self',
                        'form_target_id' => false,
                    );
                }
                
                
                // Form submit id
                $header_data['form_target_id'] = 'form-tf-filter';
                
                $data['tf_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Submit form and save module in case of no error
                if($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()){
                    
                    if (!isset($this->request->get['module_id'])) {
                            $module_id = $this->model_setting_module->addModule('tf_filter', $this->request->post);
                    } else {
                            $this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
                    }
                    
                    $this->session->data['success'] = $this->language->get('text_success');
                    
                    // Add module id in url and redirect to it after newly added module
                    if(isset($module_id)){
                       $this->response->redirect($this->url->link('extension/module/tf_filter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module_id, true)); 
                    }
                }
                
                if(isset($this->session->data['warning'])){
                    $data['warning'] = $this->session->data['warning'];
                    unset($this->session->data['warning']);
                } elseif(isset($this->error['warning'])){
                    $data['warning'] = $this->error['warning'];
                }
                
                if(isset($this->session->data['success'])){
                    $data['success'] = $this->session->data['success'];
                    unset($this->session->data['success']);
                }
                
                foreach ($this->error as $label => $error) {
                    $data['err_' . $label] = $error;
                }
                
                $url = '';
                
                if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/module/tf_filter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
		}
                
                
                if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_setting = $this->model_setting_module->getModule($this->request->get['module_id']);
                } else {
                        $module_setting = array();
                }
                
                // Language
                $this->load->model('localisation/language');
                $data['languages'] = $this->model_localisation_language->getLanguages();
                
                $data['language_id'] = $this->config->get('config_language_id');
                
                // Setting
                $setting = array();
                
                // General
                $setting['name']              =   ''; // Name of module
                $setting['status']            =   false; // status of module
                $setting['title']             =   array(); // Heading Title of module
                $setting['count_product']     =   1;
//                $setting['sub_category']      =   1;
                $setting['cache']             =   0;
                $setting['ajax']              =   0;
                $setting['delay']             =   2;
                $setting['reset_all']         =   1;
                $setting['reset_group']       =   1;
                $setting['overflow']          =   'scroll';
		$setting['hide_zero_filter']  =   false;
                
                
                // Filter type
                $setting['filter']            =   array();
                $setting['filter']['price']   =   array(
                    'status' => 1,
                    'title' => array(),
                    'sort_order' => 1,
                    'collapse' => 0
                );
                $setting['filter']['sub_category'] =   array(
                    'status' => 1,
                    'title' => array(),
                    'sort_order' => 2,
                    'collapse' => 0,
                    'input_type' => 'checkbox',
                    'list_type' => 'text',
                    'image_width' => 30,
                    'image_height' => 30,
                    'search' => 0
                );
                $setting['filter']['manufacturer'] =   array(
                    'status' => 1,
                    'title' => array(),
                    'sort_order' => 2,
                    'collapse' => 0,
                    'input_type' => 'checkbox',
                    'list_type' => 'image',
                    'image_width' => 40,
                    'image_height' => 40,
                    'search' => -1
                );
                $setting['filter']['search'] =   array(
                    'status' => 0,
                    'title' => array(),
                    'placeholder' => array(),
                    'collapse' => 0,
                    'description' => 1,
                    'sort_order' => 3
                );
                $setting['filter']['availability'] =   array(
                    'status' => 1,
                    'title' => array(),
                    'sort_order' => 4,
                    'collapse' => 0,
                    'stock_status' => 1,
                    'input_type' => 'checkbox',
                );
                $setting['filter']['discount']   =   array(
                    'status' => 1,
                    'title' => array(),
                    'sort_order' => 5,
                    'collapse' => 0
                );
                $setting['filter']['rating']   =   array(
                    'status' => 1,
                    'title' => array(),
                    'sort_order' => 99,
                    'collapse' => 0
                );
                $setting['filter']['custom']   =   array(
                    'status' => 1,
                    'search' => -1,
                    'require_category' => 1
                );
                $setting['filter']['filter']   =   array(
                    'status' => 1,
                    'collapse' => 0,
                    'search' => -1,
                    'require_category' => 1
                );
                
                // layout
                $setting['collapsed']       =   0;
                $setting['column_xs']       =   1;
                $setting['column_sm']       =   1;
                $setting['column_md']       =   1;
                $setting['column_lg']       =   1;
                
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } else {
                    $setting = array_merge($setting, $module_setting); 
                }
                
                
                $data = array_merge($data, $setting);
                
                // Text
                $data['help_custom'] = sprintf($this->language->get('help_custom'), $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
                $data['help_filter'] = sprintf($this->language->get('help_filter'), $this->url->link('catalog/filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
                
                // Data
                $data['list_search_status'] = array(
                    array('code' => 1, 'text' => $this->language->get('text_always')),
                    array('code' => 0, 'text' => $this->language->get('text_disabled')),
                    array('code' => -1, 'text' => $this->language->get('text_on_demand'))
                );
                $data['input_types'] = array(
                    array('code' => 'radio', 'text' => $this->language->get('text_radio')),
                    array('code' => 'checkbox', 'text' => $this->language->get('text_checkbox'))
                );
//                $data['category_input_types'] = array(
//                    array('code' => 'radio', 'text' => $this->language->get('text_radio')),
//                    array('code' => 'checkbox', 'text' => $this->language->get('text_checkbox')),
//                    array('code' => 'link', 'text' => $this->language->get('text_link'))
//                );
                $data['list_types'] = array(
                    array('code' => 'image', 'text' => $this->language->get('text_image')),
                    array('code' => 'text', 'text' => $this->language->get('text_text')),
                    array('code' => 'both', 'text' => $this->language->get('text_both'))
                );
                $data['overflow_types'] = array(
                    array('code' => 'scroll', 'text' => $this->language->get('text_scroll')),
                    array('code' => 'more', 'text' => $this->language->get('text_more')),
                );
                
                $data['user_token'] = $this->session->data['user_token'];
                
                $column_left_data = array();
                $column_left_data['code'] = 'tf_filter';
                if ($this->user->hasPermission('access', 'extension/maza/tf_filter')) {
                    $column_left_data['menus'][] = array(
                            'id'       => 'tf-menu-filter',
                            'icon'     => 'fa-filter',
                            'name'     => $this->language->get('text_filter'),
                            'active'   => (strpos($this->request->get['route'], 'extension/maza/tf_filter') === 0)?TRUE: FALSE,
                            'href'     => $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true),
                            'children' => array()
                    );
                }
                
                if ($this->user->hasPermission('access', 'extension/maza/tf_filter/setting')) {
                    $column_left_data['menus'][] = array(
                            'id'       => 'tf-menu-setting',
                            'icon'     => 'fa-cog',
                            'name'     => $this->language->get('text_setting'),
                            'active'   => (strpos($this->request->get['route'], 'extension/maza/tf_filter/setting') === 0)?TRUE: FALSE,
                            'href'     => $this->url->link('extension/maza/tf_filter/setting', 'user_token=' . $this->session->data['user_token'] . $url, true),
                            'children' => array()
                    );
                }
                
                $this->config->set('template_engine', 'twig');
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['tf_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['tf_column_left'] = $this->load->controller('extension/maza/common/column_left/module', $column_left_data);
                
                $this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('extension/module/tf_filter', $data));
        }
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/tf_filter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
                
                // Module name
                if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_module_name');
		}
                
                if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
        
        public function delete() {
                $this->load->language('extension/module/tf_filter');
                $this->load->model('setting/module');
                
                $url = '';
                
                if(isset($this->request->get['module_id']) && $this->validateDelete()){
                        $this->model_setting_module->deleteModule($this->request->get['module_id']);
                        
                        $this->session->data['success'] = $this->language->get('text_success');
                        
                        $this->response->redirect($this->url->link('extension/module/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
                } else {
                        $this->response->redirect($this->url->link('extension/module/tf_filter', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true));
                }
                
        }
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/tf_filter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        public function install(){
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tf_filter` (
                        `filter_id` int(11) NOT NULL AUTO_INCREMENT,
                        `status` tinyint(1) NOT NULL,
                        `sort_order` INT(11) NOT NULL,
                        `filter_language_id` int(11) NOT NULL,
                        `setting` TEXT NOT NULL,
                        `date_added` datetime NOT NULL,
                        `date_modified` datetime NOT NULL,
                        `date_sync` datetime NOT NULL,
                        PRIMARY KEY (`filter_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");
                
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tf_filter_description` (
                        `filter_id` int(11) NOT NULL,
                        `language_id` int(11) NOT NULL,
                        `name` varchar(100) NOT NULL,
                        PRIMARY KEY (`filter_id`,`language_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");
                
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tf_filter_to_category` (
                        `filter_id` int(11) NOT NULL,
                        `category_id` int(11) NOT NULL,
                        PRIMARY KEY (`filter_id`,`category_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");
                
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tf_filter_value` (
                        `value_id` int(11) NOT NULL AUTO_INCREMENT,
                        `filter_id` int(11) NOT NULL,
                        `status` tinyint(1) NOT NULL,
                        `sort_order` INT(11) NOT NULL,
                        `image` varchar(255),
                        `regex` tinyint(1) NOT NULL DEFAULT 0,
                        `value` varchar(1000) NOT NULL,
                        PRIMARY KEY (`value_id`),
                        KEY filter_id (`filter_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");
                
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tf_filter_value_description` (
                        `value_id` int(11) NOT NULL,
                        `language_id` int(11) NOT NULL,
                        `name` varchar(100) NOT NULL,
                        PRIMARY KEY (`value_id`,`language_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");
                
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tf_filter_value_to_product` (
                        `value_id` int(11) NOT NULL,
                        `product_id` int(11) NOT NULL,
                        `trash` TINYINT(1) NOT NULL DEFAULT 0,
                        PRIMARY KEY (`value_id`, product_id),
                        INDEX(`product_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
                ");
                
                // Table oc_product_attribute
                $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_attribute`");
                
                foreach($query->rows as $row){
                    if($row['Field'] == 'text' && $row['Type'] !== 'varchar(1000)'){
                        $this->db->query("ALTER TABLE " . DB_PREFIX . "product_attribute CHANGE `text` `text` VARCHAR(1000) NOT NULL");
                    }
                }
                
                $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "product_attribute`");
                
                $key_names = array_column($query->rows, 'Key_name');
                
                if(!in_array('tf_attribute_id',$key_names)){
                    $this->db->query("CREATE INDEX tf_attribute_id ON " . DB_PREFIX . "product_attribute (`attribute_id`, `language_id`)");
                }
                if(!in_array('tf_text',$key_names)){
                    $this->db->query("CREATE INDEX tf_text ON " . DB_PREFIX . "product_attribute (`text`)");
                }
                
                // Table oc_option_value
                $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "option_value`");
                
                $key_names = array_column($query->rows, 'Key_name');
                
                if(!in_array('tf_option_id',$key_names)){
                    $this->db->query("CREATE INDEX tf_option_id ON " . DB_PREFIX . "option_value (`option_id`)");
                }
                
                // Table oc_option_value_description
                $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "option_value_description`");
                
                $key_names = array_column($query->rows, 'Key_name');
                
                if(!in_array('tf_option_id',$key_names)){
                    $this->db->query("CREATE INDEX tf_option_id ON " . DB_PREFIX . "option_value_description (`option_id`, `language_id`, `name`)");
                }
                
                // Table oc_filter_description
                $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "filter_description`");
                
                $key_names = array_column($query->rows, 'Key_name');
                
                if(!in_array('tf_filter_group_id',$key_names)){
                    $this->db->query("CREATE INDEX tf_filter_group_id ON " . DB_PREFIX . "filter_description (`filter_group_id`, `language_id`, `name`)");
                }
                
                // Install event
                $this->model_setting_event->deleteEventByCode('tf_catalog_product_before_getProducts');
                $this->model_setting_event->deleteEventByCode('tf_catalog_product_before_getTotalProducts');
                $this->model_setting_event->addEvent('tf_catalog_product_before_getProducts', 'catalog/model/catalog/product/getProducts/before', 'extension/module/tf_filter/event/getProducts');
                $this->model_setting_event->addEvent('tf_catalog_product_before_getTotalProducts', 'catalog/model/catalog/product/getTotalProducts/before', 'extension/module/tf_filter/event/getTotalProducts');
        }
        
        public function uninstall(){
//                $this->db->query("DROP TABLE " . DB_PREFIX . "tf_filter");
//                $this->db->query("DROP TABLE " . DB_PREFIX . "tf_filter_description");
//                $this->db->query("DROP TABLE " . DB_PREFIX . "tf_filter_to_category");
//                $this->db->query("DROP TABLE " . DB_PREFIX . "tf_filter_value");
//                $this->db->query("DROP TABLE " . DB_PREFIX . "tf_filter_value_description");
//                $this->db->query("DROP TABLE " . DB_PREFIX . "tf_filter_value_to_product");
                
                // Revert modifcation
                // Table oc_product_attribute
                $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_attribute`");
                
                foreach($query->rows as $row){
                    if($row['Field'] == 'text' && $row['Type'] == 'varchar(1000)'){
                        $this->db->query("ALTER TABLE " . DB_PREFIX . "product_attribute CHANGE `text` `text` text NOT NULL");
                    }
                }
                
                $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "product_attribute`");
                
                $key_names = array_column($query->rows, 'Key_name');
                
                if(in_array('tf_attribute_id',$key_names)){
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_attribute` DROP INDEX tf_attribute_id");
                }
                if(in_array('tf_text',$key_names)){
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . "product_attribute` DROP INDEX tf_text");
                }
                
                
                // Table oc_option_value
                $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "option_value`");
                
                $key_names = array_column($query->rows, 'Key_name');
                
                if(in_array('tf_option_id',$key_names)){
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . "option_value` DROP INDEX tf_option_id");
                }
                
                // Table oc_option_value_description
                $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "option_value_description`");
                
                $key_names = array_column($query->rows, 'Key_name');
                
                if(in_array('tf_option_id',$key_names)){
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . "option_value_description` DROP INDEX tf_option_id");
                }
                
                // Table oc_filter_description
                $query = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "filter_description`");
                
                $key_names = array_column($query->rows, 'Key_name');
                
                if(in_array('tf_filter_group_id',$key_names)){
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . "filter_description` DROP INDEX tf_filter_group_id");
                }
//                
                // Remove event
                $this->model_setting_event->deleteEventByCode('tf_catalog_product_before_getProducts');
                $this->model_setting_event->deleteEventByCode('tf_catalog_product_before_getTotalProducts');
        }
}
