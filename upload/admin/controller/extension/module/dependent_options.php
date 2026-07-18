<?php
require_once(DIR_SYSTEM . 'library/equotix/dependent_options/equotix.php');
class ControllerExtensionModuleDependentOptions extends Equotix {
	protected $version = '2.0.0';
	protected $code = 'dependent_options';
	protected $extension = 'Dependent Options';
	protected $extension_id = '14';
	protected $purchase_url = 'dependent-options';
	protected $purchase_id = '13882';
	protected $error = array();
	
	public function index() {   
		$this->load->language('extension/module/dependent_options');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
				
		$data['heading_title'] = $this->language->get('text_heading');
		
		$data['text_no_config'] = $this->language->get('text_no_config');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_purchase_import'] = $this->language->get('text_purchase_import');
		
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_import'] = $this->language->get('tab_import');
		
		$data['button_cancel'] = $this->language->get('button_cancel');

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_heading'),
			'href'      => $this->url->link('extension/module/dependent_options', 'user_token=' . $this->session->data['user_token'], true)
   		);
		
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['token'] = $this->session->data['user_token'];
		
		if (file_exists(DIR_TEMPLATE . 'extension/module/dependent_options_import.twig')) {
			$data['import'] = true;
			
			include_once(DIR_APPLICATION . 'controller/extension/dependent_options_import.php');
		} else {
			$data['import'] = false;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->generateOutput('extension/module/dependent_options', $data);
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/dependent_options')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}

	public function install() {
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			return;
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_option_parent` (
			  `product_id` int(11) NOT NULL,
			  `product_option_id` int(11) NOT NULL,
			  `parent_option` int(11) NOT NULL,
			  PRIMARY KEY (`product_id`, `product_option_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;
		");
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_option_value_to_option_value` (
			  `product_option_value_to_option_value_id` int(11) NOT NULL AUTO_INCREMENT,
			  `product_option_id` int(11) NOT NULL,
			  `product_option_value_id` int(11) NOT NULL,
			  `option_value_id` int(11) NOT NULL,
			  `product_id` int(11) NOT NULL,
			  PRIMARY KEY (`product_option_value_to_option_value_id`)
			)
		");
		
		$this->load->model('setting/setting');

		$data = array(
			'module_dependent_options_status' => true
		);

		$this->model_setting_setting->editSetting('module_dependent_options', $data);
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->addEvent('module_dependent_options', 'admin/view/catalog/product_form/before', 'extension/module/dependent_options/eventPreViewCatalogProductForm');
		$this->model_setting_event->addEvent('module_dependent_options', 'admin/model/catalog/product/editProduct/before', 'extension/module/dependent_options/eventPreModelCatalogProductDelete');
		$this->model_setting_event->addEvent('module_dependent_options', 'admin/model/catalog/product/deleteProduct/before', 'extension/module/dependent_options/eventPreModelCatalogProductDelete');
		$this->model_setting_event->addEvent('module_dependent_options', 'catalog/model/catalog/product/getProductOptions/after', 'extension/module/dependent_options/eventPostModelCatalogProductGetProductOptions');
	}
	
	public function uninstall() {
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			return;
		}
		
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_option_parent`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "product_option_value_to_option_value`");
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->deleteEventByCode('module_dependent_options');
	}
	
	public function dependentoptionvalues() {
		$json = array();
		
		if (isset($this->request->get['parent_id'])) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON ovd.option_value_id = ov.option_value_id WHERE ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ov.option_id = '" . (int)$this->request->get['parent_id'] . "'");
		
			if ($query->num_rows) {
				$json['value'] = $query->rows;
			} else {
				$json['value'] = '';
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function eventPreViewCatalogProductForm($route, &$data) {
		$this->load->language('extension/module/dependent_options');
		
		$data['text_parent_option_value'] = $this->language->get('text_parent_option_value');
		$data['text_parent_option'] = $this->language->get('text_parent_option');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_display_field'] = $this->language->get('text_display_field');
		
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['product_id'])) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
				
			if ($product_info) {
				$parent_options_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "option` o LEFT JOIN `" . DB_PREFIX . "option_description` od ON od.option_id = o.option_id WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "' AND o.type = 'select'");
			
				$data['parent_options'] = $parent_options_query->rows;
				
				foreach ($data['product_options'] as $key => $product_option) {
					$parent_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_parent WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");
					
					if ($parent_option_query->num_rows) {
						$data['product_options'][$key]['parent_option'] = $parent_option_query->row['parent_option'];
						$product_option['parent_option'] = $parent_option_query->row['parent_option'];
					} else {
						$data['product_options'][$key]['parent_option'] = 0;
						$product_option['parent_option'] = 0;
					}
				
					$parent_option_values_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value ov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON ovd.option_value_id = ov.option_value_id WHERE ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ov.option_id = '" . (int)$product_option['parent_option'] . "'");
				
					$data['product_options'][$key]['parent_option_values'] = $parent_option_values_query->rows;
					
					$linked_option_values_query = $this->db->query("SELECT option_value_id FROM " . DB_PREFIX . "product_option_value_to_option_value WHERE product_id = '" . (int)$product_info['product_id'] . "' AND product_option_id = '" . (int)$product_option['product_option_id'] . "'");
				
					$linked_option_values_array = array();
				
					foreach ($linked_option_values_query->rows as $linked_option_value_id) {
						$linked_option_values_array[] = $linked_option_value_id['option_value_id'];
					}
					
					$data['product_options'][$key]['linked_option_value_id'] = $linked_option_values_array;
					
					foreach ($product_option['product_option_value'] as $key1 => $product_option_value) {
						$product_option_value_to_option_value_query = $this->db->query("SELECT option_value_id FROM " . DB_PREFIX . "product_option_value_to_option_value WHERE product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "'");
					
						$linked_option_values = array();
						
						foreach ($product_option_value_to_option_value_query->rows as $linked_option_value_id) {
							$linked_option_values[] = $linked_option_value_id['option_value_id'];
						}
					
						$data['product_options'][$key]['product_option_value'][$key1]['linked_option_value_id'] = $linked_option_values;
					}
				}
			}
		}
	}
	
	public function eventPreModelCatalogProductDelete($route, $args) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_parent WHERE product_id = '" . (int)$args[0] . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value_to_option_value WHERE product_id = '" . (int)$args[0] . "'");
	}
}