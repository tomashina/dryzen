<?php
class ControllerExtensionModuleBaselMegamenu extends Controller {
    public function index($setting) {
        $this->load->model('extension/basel/basel_megamenu');
		
		$module_id = (isset($setting['moduleid']) && $setting['moduleid']) ? $setting['moduleid'] : 0;
        $data['menu'] = $this->model_extension_basel_basel_megamenu->getMenu($module_id, $mobile = false);	
		
        $lang_id = $this->config->get('config_language_id');
        $data['lang_id'] = $this->config->get('config_language_id');
		
		$this->load->language('basel/basel');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['basel_button_quickview'] = $this->language->get('basel_button_quickview');
		$data['basel_text_sale'] = $this->language->get('basel_text_sale');
		$data['basel_text_new'] = $this->language->get('basel_text_new');
		$data['basel_text_days'] = $this->language->get('basel_text_days');
		$data['basel_text_hours'] = $this->language->get('basel_text_hours');
		$data['basel_text_mins'] = $this->language->get('basel_text_mins');
		$data['basel_text_secs'] = $this->language->get('basel_text_secs');
		$data['countdown_status'] = $this->config->get('countdown_status');
		$data['salebadge_status'] = $this->config->get('salebadge_status');
		
		$this->load->language('extension/module/category');
		$data['heading_title'] = $this->language->get('heading_title');
   		
		if ($this->config->get('theme_default_directory') == 'basel')
		return $this->load->view('extension/module/basel_megamenu', $data);
    }	
}