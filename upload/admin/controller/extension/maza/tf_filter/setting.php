<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */

class ControllerExtensionMazaTfFilterSetting extends Controller {
        private $error = array();
    
        public function index() {
		$this->load->language('extension/maza/tf_filter/setting');

		$this->document->setTitle($this->language->get('heading_title'));

                
                $data = array();
                
                // Header
                $header_data = array();
                $header_data['title'] = $this->language->get('heading_title');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-tf-general', 'href' => false),
                    array('name' => $this->language->get('tab_cron'), 'id' => 'tab-tf-cron', 'href' => false)
                );
                
                $header_data['menu_active'] = 'tab-tf-general';
                $header_data['buttons'][] = array( // Button save
                    'id' => 'button-save',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => 'form-tf-setting',
                );
                $header_data['form_target_id'] = 'form-tf-setting';
                
                $data['tf_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                $this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_tf_filter', $this->request->post);

			$data['success'] = $this->language->get('text_success');
		}
                
                
                $data['action'] = $this->url->link('extension/maza/tf_filter/setting', 'user_token=' . $this->session->data['user_token'], true);
                
                // Default Setting
                $setting = array();
                $setting['module_tf_filter_sub_category'] = 0;
                $setting['module_tf_filter_cron_status'] = array();
                
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } else {
                    $setting = array_merge($setting, $this->model_setting_setting->getSetting('module_tf_filter')); 
                }

                // Data
                $data = array_merge($data, $setting);
                
                // Cron url
                if($_SERVER['HTTPS']){
                    $data['cron_url'] = HTTPS_CATALOG . 'index.php?route=extension/maza/tf_cron&username=[USERNAME]&password=[PASSWORD]';
                } else {
                    $data['cron_url'] = HTTP_CATALOG . 'index.php?route=extension/maza/tf_cron&username=[USERNAME]&password=[PASSWORD]';
                }
                
                $data['user_token'] = $this->session->data['user_token'];
                
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
                if(isset($this->error['warning'])){
                        $data['warning'] = $this->error['warning'];
                }
                foreach($this->error as $key => $val){
                        $data['err_' . $key] = $val;
                }
                
                $url = '';
                
                // Columns
                $column_left_data = array();
                $column_left_data['code'] = 'tf_filter';
                if ($this->user->hasPermission('access', 'extension/maza/tf_filter')) {
                    $column_left_data['menus'][] = array(
                            'id'       => 'tf-menu-filter',
                            'icon'     => 'fa-filter',
                            'name'     => $this->language->get('text_filter'),
                            'active'   => FALSE,
                            'href'     => $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true),
                            'children' => array()
                    );
                }
                
                if ($this->user->hasPermission('access', 'extension/maza/tf_filter/setting')) {
                    $column_left_data['menus'][] = array(
                            'id'       => 'tf-menu-setting',
                            'icon'     => 'fa-cog',
                            'name'     => $this->language->get('text_setting'),
                            'active'   => TRUE,
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
		$this->response->setOutput($this->load->view('extension/maza/tf_filter/setting', $data));
	}
        
        protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter/setting')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
