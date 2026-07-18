<?php
class ControllerExtensionModuleImportApi extends Controller {
	private $error = array();
	private $fields = array();

	public function index() {
		$this->load->language('extension/module/import_api');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		
		if (!is_dir('../image/api/')) {
			mkdir('../image/api/', 0777, true);
		}
		
		$data['extra_fields_number'] = 3;
		$data['extra_modifications_number'] = 4;
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('import_api', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		
		$data['user_token'] = $this->session->data['user_token'];


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/import_api', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['test'] = $this->url->link('extension/module/import_api/test', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['import'] = $this->url->link('extension/module/import_api/import', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['action'] = $this->url->link('extension/module/import_api', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		$data['oc_fields'] = ['unique', 'model', 'name', 'description', 'price', 'special', 'quantity', 'image', 'brand', 'category', 'category_parent', 'attribute_name', 'attribute_value', 'option', 'option_value', 'option_price', 'option_weight', 'option_quantity', 'images', 'sku', 'mpn', 'ean', 'upc', 'jan', 'isbn', 'location', 'weight', 'minimum'];
		
		$data['import_api_fields'] = $this->config->get('import_api_fields') ?  html_entity_decode($this->config->get('import_api_fields'), ENT_QUOTES, 'UTF-8') : '';

		$data['api_fields'] = explode('##', $data['import_api_fields']);
		$config_fields = $this->config->get('import_api_field');
		$config_modifications = $this->config->get('import_api_modification');
		$config_combinations = $this->config->get('import_api_combination');
		
		if (isset($this->request->post['import_api_link'])) {
			$data['import_api_link'] = $this->request->post['import_api_link'];
		} else {
			$data['import_api_link'] = $this->config->get('import_api_link');
		}
		
		foreach($data['oc_fields'] as $oc_field){
			$data['saved_values'][$oc_field] = !empty($config_fields[$oc_field]) ? html_entity_decode($config_fields[$oc_field], ENT_QUOTES, 'UTF-8') : '';
			$data['entry_values'][$oc_field] = ($this->language->get('entry_field_' . $oc_field) != 'entry_field_' . $oc_field) ? $this->language->get('entry_field_' . $oc_field) : ucfirst($oc_field. ' field');			
		}
		
		for($i = 1; $i <= $data['extra_fields_number']; $i++){
			$data['saved_values']['field' . $i] = !empty($config_fields['field' . $i]) ? html_entity_decode($config_fields['field' . $i], ENT_QUOTES, 'UTF-8') : '';
			$data['entry_values']['field' . $i] = 'field' . $i;	
		}
		
		foreach($data['oc_fields'] as $oc_field){
			$data['saved_modifications'][$oc_field] = !empty($config_modifications[$oc_field]) ? $config_modifications[$oc_field] : '';
			$data['entry_modifications'][$oc_field] = ($this->language->get('entry_field_' . $oc_field) != 'entry_field_' . $oc_field) ? $this->language->get('entry_field_' . $oc_field) : ucfirst($oc_field. ' field');			
		}

		for($i = 1; $i <= $data['extra_modifications_number']; $i++){
			$data['saved_modifications']['modification' . $i] = !empty($config_modifications['modification' . $i]) ? $config_modifications['modification' . $i] : '';
			$data['entry_modifications']['modification' . $i] = 'modification' . $i;	
		}
		
		foreach($data['oc_fields'] as $oc_field){
			$data['saved_combinations'][$oc_field] = !empty($config_combinations[$oc_field]) ? $config_combinations[$oc_field] : '';
			$data['entry_combinations'][$oc_field] = ($this->language->get('entry_field_' . $oc_field) != 'entry_field_' . $oc_field) ? $this->language->get('entry_field_' . $oc_field) : ucfirst($oc_field. ' field');			
		}
		
		// Settings
		
		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['import_api_tax'])) {
			$data['import_api_tax'] = $this->request->post['import_api_tax'];
		} elseif (!empty($this->config->get('import_api_tax'))) {
			$data['import_api_tax'] = $this->config->get('import_api_tax');
		} else {
			$data['import_api_tax'] = 0;
		}
		
		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['import_api_stock_status_id'])) {
			$data['import_api_stock_status_id'] = $this->request->post['import_api_stock_status_id'];
		} elseif (!empty($this->config->get('import_api_stock_status_id'))) {
			$data['import_api_stock_status_id'] = $this->config->get('import_api_stock_status_id');
		} else {
			$data['import_api_stock_status_id'] = 0;
		}
		
		$this->load->model('localisation/weight_class');

		$data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();

		if (isset($this->request->post['import_api_weight_class_id'])) {
			$data['import_api_weight_class_id'] = $this->request->post['import_api_weight_class_id'];
		} elseif (!empty($this->config->get('import_api_weight_class_id'))) {
			$data['import_api_weight_class_id'] = $this->config->get('import_api_weight_class_id');
		} else {
			$data['import_api_weight_class_id'] = $this->config->get('config_weight_class_id');
		}
		
		if (isset($this->request->post['import_api_top_category'])) {
			$data['import_api_top_category'] = $this->request->post['import_api_top_category'];
		} elseif (!empty($this->config->get('import_api_top_category'))) {
			$data['import_api_top_category'] = $this->config->get('import_api_top_category');
		} else {
			$data['import_api_top_category'] = '';
		}
		
		if (isset($this->request->post['import_api_default_category'])) {
			$data['import_api_default_category'] = $this->request->post['import_api_default_category'];
		} elseif (!empty($this->config->get('import_api_default_category'))) {
			$data['import_api_default_category'] = $this->config->get('import_api_default_category');
		} else {
			$data['import_api_default_category'] = '';
		}
		
		if (isset($this->request->post['import_api_default_brand'])) {
			$data['import_api_default_brand'] = $this->request->post['import_api_default_brand'];
		} elseif (!empty($this->config->get('import_api_default_brand'))) {
			$data['import_api_default_brand'] = $this->config->get('import_api_default_brand');
		} else {
			$data['import_api_default_brand'] = '';
		}
		
		if (isset($this->request->post['import_api_attribute_group'])) {
			$data['import_api_attribute_group'] = $this->request->post['import_api_attribute_group'];
		} elseif (!empty($this->config->get('import_api_attribute_group'))) {
			$data['import_api_attribute_group'] = $this->config->get('import_api_attribute_group');
		} else {
			$data['import_api_attribute_group'] = 'General';
		}
		
		if (isset($this->request->post['import_api_default_option'])) {
			$data['import_api_default_option'] = $this->request->post['import_api_default_option'];
		} elseif (!empty($this->config->get('import_api_default_option'))) {
			$data['import_api_default_option'] = $this->config->get('import_api_default_option');
		} else {
			$data['import_api_default_option'] = 'Option';
		}
		
		if (isset($this->request->post['import_api_default_attribute'])) {
			$data['import_api_default_attribute'] = $this->request->post['import_api_default_attribute'];
		} elseif (!empty($this->config->get('import_api_default_attribute'))) {
			$data['import_api_default_attribute'] = $this->config->get('import_api_default_attribute');
		} else {
			$data['import_api_default_attribute'] = 'Attribute';
		}
		
		if (isset($this->request->post['import_api_multiplier'])) {
			$data['import_api_multiplier'] = $this->request->post['import_api_multiplier'];
		} elseif (!empty($this->config->get('import_api_multiplier'))) {
			$data['import_api_multiplier'] = $this->config->get('import_api_multiplier');
		} else {
			$data['import_api_multiplier'] = '';
		}
		
		if (isset($this->request->post['import_api_category_path'])) {
			$data['import_api_category_path'] = $this->request->post['import_api_category_path'];
		} elseif (!empty($this->config->get('import_api_category_path'))) {
			$data['import_api_category_path'] = $this->config->get('import_api_category_path');
		} else {
			$data['import_api_category_path'] = 0;
		}
		
		if (isset($this->request->post['import_api_start_index'])) {
			$data['import_api_start_index'] = $this->request->post['import_api_start_index'];
		} elseif (!empty($this->config->get('import_api_start_index'))) {
			$data['import_api_start_index'] = $this->config->get('import_api_start_index');
		} else {
			$data['import_api_start_index'] = 0;
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/import_api', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/import_api')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['import_api_link']) {
			$this->error['warning'] = $this->language->get('error_link');
		}
		
		if (!$this->request->post['import_api_field']['unique']) {
			$this->error['warning'] = $this->language->get('error_unique');
		}

		return !$this->error;
	}
	
	public function fields(){
		
		$link = html_entity_decode($this->request->post['import_api_link'], ENT_QUOTES, "UTF-8");

		$external_string = @file_get_contents($link);
		
		if($external_string === false){
			$json['error'] = 'External file not found. Check link in browser';
		} else {
			
			$json['error'] = '';
			//$ob = @simplexml_load_string($external_string);
			$ob = simplexml_load_string($external_string, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_PARSEHUGE);

			$json_string = @json_encode($ob);
			$php_array = @json_decode($json_string, true);
			
			if($php_array === null){
				$json['error'] = 'File is not in xml format';
			} else {				
				$this->search_keys($php_array, 'FEED');
				
				$json['field'] = $this->fields;
				$json['fields'] = implode('##', $this->fields);
			}			
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	function search_keys($array, $parent){
		if(isset($array[0])){
			$array = array('array('. count($array) .')' => $array[0]);
		}
		
		foreach($array as $key => $value){
			if(!is_array($value)){
				$this->fields[] = $parent.'->'.$key;
			} else {
				$this->search_keys($value, $parent .'->'. $key);
			}
		}		
	}
}