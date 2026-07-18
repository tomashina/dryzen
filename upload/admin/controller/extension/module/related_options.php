<?php

//  Related Options / Связанные опции
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ControllerExtensionModuleRelatedOptions extends liveopencart\lib\v0023\ControllerAdminExtension {
	
	protected $extension_code = 'related_options'; // for paths and urls
	
	protected $xlsx_lib;
	
	protected $export_field_options                = 'options_values_ids';
	protected $export_field_product_name           = 'product_name_info_only';
	protected $export_field_description_of_options = 'description_of_options_info_only';
	protected $export_field_discounts              = 'discounts';
	protected $export_field_specials               = 'specials';
	protected $export_field_custom                 = 'custom_fields';
	protected $export_field_custom_prefix          = 'field:';
	protected $export_sheet_variants_name          = 'Product variants';
	
	public function __construct() {
		call_user_func_array( ['parent', '__construct'] , func_get_args());
		
		\liveopencart\ext\ro::getInstance($this->registry);
	}
	
	public function index() {
		
		$mod_language = $this->load->language('extension/module/related_options');
		
		$links = $this->getLinks();

		$this->document->setTitle($this->language->get('module_name'));
		
		$this->load->model('setting/setting');
		$this->load->model('extension/module/related_options');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
	  
			if (isset($this->request->post['variants'])) {
				$this->model_extension_module_related_options->setVariantsOfRelatedOptions($this->request->post['variants']);
				unset($this->request->post['variants']);
			} else {
				$this->model_extension_module_related_options->setVariantsOfRelatedOptions([], false);
			}
	  
			$this->model_setting_setting->editSetting('related_options', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($links['redirect']);
	  
		}
	
		$data['breadcrumbs'] = $links['breadcrumbs'];
		$data['action']      = $links['action'];
		$data['cancel']      = $links['cancel'];
		
		$data['action_export'] = $this->getLinkWithToken( $this->getRouteExtension('', 'export'), '&type='.$this->extension_type);
		
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->checkROOCmodStatus();
		
		$data['version_pro']             = $this->liveopencart_ext_ro->versionPRO();
		$data['related_options_version'] = $this->liveopencart_ext_ro->getCurrentVersion();
		
		if ( $this->getXLXSLib()->getAvailability() ) {
			$data['import_export_is_possible'] = true;
		} else {
			$data['xlsx_lib_error']        = true;
			$data['xlsx_lib_name']         = $this->getXLXSLib()->getName();
			$data['lib_install_available'] = $this->user->hasPermission('modify', 'extension/module/product_option_image_pro');
		}
	
		if (!empty($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
	
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		
		if ( $this->isDebug() ) {
			$data['ro_vue_source'] = 'https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.esm.browser.js';
		} else {
			$data['ro_vue_source'] = HTTPS_SERVER.$this->getResourceLinkWithVersion('view/javascript/liveopencart/related_options/vue@2.6.11/dist/vue.esm.browser.min.js');
		}
		$data['ro_module_page_script'] = './'.$this->getResourceLinkWithVersion('view/javascript/liveopencart/related_options/module_page.js');
		
		$data['modules'] = [];
		if (isset($this->request->post['related_options'])) {
			$data['modules'] = $this->request->post['related_options'];
		} elseif ($this->config->get('related_options')) {
			$data['modules'] = $this->config->get('related_options');
		}
		
		$data['settings_main']             = $this->getSettingsMain($mod_language);
		$data['settings_customer_section'] = $this->getSettingsCustomerSection($mod_language);
		$data['additional_fields']         = $this->getAdditionalFields($mod_language);
		
		$data['module_version'] = $this->liveopencart_ext_ro->getCurrentVersion();

		$data['config_admin_language'] = $this->config->get('config_admin_language');
		
		$data['extension_code'] = $this->liveopencart_ext_ro->getExtensionCode();
	
		$data['export_new_action'] = $this->url->link('extension/module/related_options/export_new', '&user_token=' . $this->session->data['user_token'], 'SSL');
		$data['export_new_fields'] = $this->getExportNewFields();
		$related_options_export    = $this->model_setting_setting->getSetting('related_options_export');
		if ( !empty($related_options_export['related_options_export']) ) {
			$data['export_new_settings'] = $related_options_export['related_options_export'];
		}
		//$data['export_new_PHPExcelExists'] 	= $this->model_extension_module_related_options->PHPExcelExists();
	
		$data['min_product_id'] = $this->model_extension_module_related_options->getMinProductIdWithRO();
		$data['max_product_id'] = $this->model_extension_module_related_options->getMaxProductIdWithRO();
		
		$data['options']          = $this->model_extension_module_related_options->getCompatibleOptions();
		$data['variants_options'] = $this->getVariantsDecoded();
		$data['ro_texts']         = $this->getLanguageTexts(['text_confirm_variant_removing', 'text_if_any', 'confirm_ro_variant_field_remove']);
	
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
				
		$this->response->setOutput($this->load->view('extension/module/related_options', $data));
	
	}
	
	public function saveVariant() {
		$json = [];
		
		$variants = isset($this->request->post['variants']) ? $this->request->post['variants'] : false ;
		
		if (!$variants) {
			$json['error'] = 'No data received in request'; // should never happen
		} elseif (count($variants) > 1) {
			$json['error'] = 'To many variants received in request '.count($variants); // should never happen
		} else {
			$this->load->model('extension/module/related_options');
			$rovs_ids = $this->model_extension_module_related_options->updateSomeROVs($variants, false);
			if ( !$rovs_ids ) {
				$json['error'] = 'Variant not saved'; // should never happen
			} else {
				$json['variant'] = $this->model_extension_module_related_options->getVariant($rovs_ids[0]);
				
				if (!$json['variant']) {
					$json['error'] = 'Variant not found after saving '.$rovs_ids[0]; // should never happen
				}
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function removeVariant() {
		$json = [];
		
		$rov_id = isset($this->request->post['rov_id']) ? $this->request->post['rov_id'] : false ;
		
		if (!$rov_id) {
			$json['error'] = 'No variant id received in request'; // should never happen
		} else {
			
			$this->load->model('extension/module/related_options');
			$variant = $this->model_extension_module_related_options->getVariant($rov_id);
			
			if ($variant) {
				$this->model_extension_module_related_options->removeVariant($rov_id);
			}
			
			$json['success'] = true;
			
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getVariant() {
		
		$json = [];
		
		$rov_id = isset($this->request->get['rov_id']) ? (int)$this->request->get['rov_id'] : 0 ;
		
		$this->load->model('extension/module/related_options');
		
		$json['variant'] = $this->model_extension_module_related_options->getVariant($rov_id);
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	protected function getLanguageTexts($keys) {
		
		$texts = [];
		foreach ($keys as $key) {
			$texts[$key] = $this->language->get($key);
		}
		return $texts;
	}
	
	protected function checkROOCmodStatus() {
	
		$this->load->model('catalog/product');
		
		if ( !method_exists('\ModelCatalogProduct', 'getROOCmodStatus') ) {
			$this->error['warning'] = $this->language->get('error_modificaton');
		}

	}
	
	protected function getVariantsDecoded() {
		$this->load->model('extension/module/related_options');
		$variants_data = $this->model_extension_module_related_options->getVariants(false, true);
		
		$variants = $variants_data['sorted'];
		
		foreach ( $variants as &$variant ) {
			$variant['name'] = $this->decodeHTML($variant['name']);
			foreach ( $variant['options'] as &$option ) {
				$option['name'] = $this->decodeHTML($option['name']);
			}
			unset($option);
		}
		unset($variant);
		return $variants;
	}
	
	protected function getXLXSLib($force_using_php_excel = false) {
		
		if ( $force_using_php_excel && (!$this->xlsx_lib || $this->xlsx_lib->getName() != 'PHPExcel' ) ) {
			$this->xlsx_lib = $this->getNewLibInstance('vendors\php_excel', $this->registry);
		}
		
		if ( !$this->xlsx_lib ) {
			$box_spout = $this->getNewLibInstance('vendors\box_spout', $this->registry);
			
			if ( $box_spout->getPossibility() && !$force_using_php_excel ) {
				$this->xlsx_lib = $box_spout;
			} else{
				$this->xlsx_lib = $this->getNewLibInstance('vendors\php_excel', $this->registry);
			}
		}
		return $this->xlsx_lib;
	}
	
	public function installXLSXLib() {
		
		$json = [];
		
		$this->loadLanguage();
		
		if ( !$this->user->hasPermission('modify', 'extension/module/related_options')) {
			
			$json['error'] = $this->language->get('error_permission');
			
		} else {
			
			$result = $this->getXLXSLib()->install();
			
			if ( $result ) {
				$json['error'] = $result;
			}
			
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function getExportNewFields() {
		$fields = [
			'product_id',
			'product_model',
			$this->export_field_product_name,
			$this->export_field_options,
			$this->export_field_description_of_options,
			'quantity',
			'model',
			'sku',
			'upc',
			'ean',
			'jan',
			'location',
			'price_prefix',
			'price',
			'defaultselect',
			'defaultselectpriority',
			'weight_prefix',
			'weight',
			'stock_status_id',
			$this->export_field_discounts,
			$this->export_field_specials,
		];
		
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$fields[] = $this->export_field_custom;
		}
		
		return $fields;
	}
	
	public function productForm($data) {
		
		$data['max_input_vars']                 = (int)ini_get('max_input_vars');
		$data['warning_max_input_vars']         = sprintf($this->language->get('warning_max_input_vars'), $data['max_input_vars']);
		$data['max_number_of_combinations']     = 100000;
		$data['confirm_number_of_combinations'] = 2000;
		$data['related_options_title']          = $this->language->get('module_name');
		
		$this->load->model('extension/module/related_options');
		
		$data['ro_installed'] = $this->liveopencart_ext_ro->installed();
		if ( $data['ro_installed'] ) {
			$data['variants_options'] = $this->model_extension_module_related_options->getVariants(true, true);
			$data['ro_all_options']   = $this->model_extension_module_related_options->getCompatibleOptionValues();
			
			$ro_settings            = $this->config->get('related_options');
			$data['ro_version_pro'] = $this->liveopencart_ext_ro->versionPRO();
			if ( !$data['ro_version_pro'] ) {
				unset($ro_settings['pagination']);
				unset($ro_settings['allow_empty']);
				unset($ro_settings['spec_customer_groups']);
			}
			
			$data['ro_settings'] = $ro_settings;
			$data['ro_version']  = $this->liveopencart_ext_ro->getCurrentVersion();
			
			$data['ro_texts'] = $this->getTextsProductEditPage($data);
			
			$ro_product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
		
			if (isset($this->request->post['ro_data'])) {
				$data['ro_data'] = $this->request->post['ro_data'];
			} elseif ( $ro_product_id ) {
				$data['ro_data'] = $this->model_extension_module_related_options->getROData($ro_product_id);
			} else {
				$data['ro_data'] = [];
			}
			
			$this->addScriptsProductForm();
		}
		
		return $data;
	}
	
	public function getTextsProductEditPage($data) {
		
		$text_keys = [
			'related_options_title',
			'text_ro_set_options_variants',
			'entry_ro_use',
			'text_ro_all_options',
			'entry_add_all_variants',
			'entry_add_product_variants',
			'entry_delete_all_combs',
			'entry_options_values',
			'entry_model',
			'entry_sku',
			'entry_upc',
			'entry_ean',
			'entry_jan',
			'entry_location',
			'entry_stock_status',
			'entry_weight',
			'entry_price',
			'tab_discount',
			'tab_special',
			'entry_select_first_short',
			'entry_customer_group',
			'entry_quantity',
			'entry_add_related_options',
			'entry_add_discount',
			'entry_del_discount_title',
			'entry_add_special',
			'entry_del_special_title',
			'entry_priority',
			'text_combs_will_be_added',
			'warning_max_input_vars',
			'max_input_vars',
			'entry_select_first_priority',
			'button_remove',
			'warning_equal_options',
			'text_delete_all_combs',
			'max_number_of_combinations',
			'confirm_number_of_combinations',
			'text_combs_number',
			'text_combs_number_out_of_limit',
			'text_combs_number_is_big',
			'text_combs_all_exist',
			'entry_ro_variant',
			'entry_related_options_quantity',
			'text_yes',
			'text_no',
			'entry_copy_comb_button_help_title',
			'entry_allow_zero_select',
			'entry_allow_zero_select_help',
			'text_use_global_setting',
			'entry_spec_inss',
			'entry_spec_disabled',
			'entry_spec_customer_groups',
		];
		
		$texts = [];
		foreach ($text_keys as $text_key) {
			if ( isset($data[$text_key]) ) {
				$texts[$text_key] = $data[$text_key];
			} else {
				$texts[$text_key] = $this->language->get($text_key);
			}
		}
		return $texts;
	}
	
	protected function addScriptsProductForm() {
		
		$script     = 'view/javascript/liveopencart/related_options/ro_product_edit_page.js';
		$script_pro = 'view/javascript/liveopencart/related_options/ro_product_edit_page_pro.js';
		
		$this->document->addScript( $this->liveopencart_ext_ro->getResourceLinkWithVersion($script) );
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$this->document->addScript( $this->liveopencart_ext_ro->getResourceLinkWithVersion($script_pro) );
		}
	}
	
	protected function prepareField($mod_language, $name, $values = false, $parent = '', $with_delimiters = false, $type = '') {
		
		$title = isset($mod_language['entry_ro_'.$name]) ? $mod_language['entry_ro_'.$name] : $mod_language['entry_'.$name] ;
		$help  = isset($mod_language['entry_ro_'.$name.'_help']) ? $mod_language['entry_ro_'.$name.'_help'] : $mod_language['entry_'.$name.'_help'];
		
		$delimiters     = [];
		$delimiter_keys = ['_delimiter_product', '_delimiter_ro'];
		if ( $with_delimiters ) {
			foreach ( $delimiter_keys as $delimiter_key ) {
				$delimiter_name = $name.$delimiter_key;
				$delimiters[]   = ['name' => $delimiter_name, 'title' => (isset($mod_language['entry_ro_'.$delimiter_name]) ? $mod_language['entry_ro_'.$delimiter_name] : $mod_language['entry_'.$delimiter_name]) ];
			}
		}
		
		return [
			'name'       => $name,
			'title'      => $title,
			'help'       => $help,
			'type'       => $type,
			'parent'     => $parent,
			'values'     => $values,
			'delimiters' => $delimiters,
		];
	}
	
	protected function getAdditionalFields($mod_language) {
		$fields = [];
		
		foreach ($mod_language as $lang_key => $lang_val) {
			$$lang_key = $lang_val;
		}
		
		$values = [
			0 => $entry_spec_model_0,
			1 => $entry_spec_model_1,
			2 => $entry_spec_model_2,
			3 => $entry_spec_model_3,
		];
		$fields[] = $this->prepareField($mod_language, 'spec_model', $values, '', true);
		$fields[] = $this->prepareField($mod_language, 'spec_sku');
		$fields[] = $this->prepareField($mod_language, 'spec_upc');
		$fields[] = $this->prepareField($mod_language, 'spec_ean');
		$fields[] = $this->prepareField($mod_language, 'spec_jan');
		$fields[] = $this->prepareField($mod_language, 'spec_location');
		$fields[] = $this->prepareField($mod_language, 'spec_inss');
		$fields[] = $this->prepareField($mod_language, 'spec_ofs');
		$fields[] = $this->prepareField($mod_language, 'spec_weight');
		$fields[] = $this->prepareField($mod_language, 'spec_price_prefix');
		$fields[] = $this->prepareField($mod_language, 'spec_price');
		$fields[] = $this->prepareField($mod_language, 'spec_price_discount');
		$fields[] = $this->prepareField($mod_language, 'spec_price_special');
		
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$fields[] = $this->prepareField($mod_language, 'spec_customer_groups');
		}
		$fields[] = $this->prepareField($mod_language, 'spec_disabled');
		
		return $fields;
	}
	
	protected function getSettingsMain($mod_language) {
		$fields = [];
		
		foreach ($mod_language as $lang_key => $lang_val) {
			$$lang_key = $lang_val;
		}
		
		$fields[] = $this->prepareField($mod_language, 'update_quantity');
		
		$fields[] = $this->prepareField($mod_language, 'update_options');
		$fields[] = $this->prepareField($mod_language, 'update_options_remove', false, 'update_options');
		
		$values = [
			0 => $text_subtract_stock_from_product,
			1 => $text_subtract_stock_from_product_first_time,
			2 => $text_yes,
			3 => $text_no,
		];
		$fields[] = $this->prepareField($mod_language, 'subtract_stock', $values, 'update_options');
		
		$values = [
			0 => $text_yes,
			1 => $text_no,
			2 => $text_required_first_time,
		];
		$fields[] = $this->prepareField($mod_language, 'required', $values, 'update_options');
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$fields[] = $this->prepareField($mod_language, 'pagination');
		}
		$fields[] = $this->prepareField($mod_language, 'copy_comb_button');
		
		$fields[] = $this->prepareField($mod_language, 'disable_all_options_variant');
		$fields[] = $this->prepareField($mod_language, 'ro_use_variants');
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$fields[] = $this->prepareField($mod_language, 'ro_edit_variants_separately', false, 'ro_use_variants');
		}
				
		return $fields;
	}
	
	protected function getSettingsCustomerSection($mod_language) {
		
		$fields = [];
		
		foreach ($mod_language as $lang_key => $lang_val) {
			$$lang_key = $lang_val;
		}
	
		$fields[] = $this->prepareField($mod_language, 'allow_zero_select');
		
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$values = [
				0 => $text_no,
				1 => $text_yes,
				2 => $text_ro_only_for_last_option,
			];
			$fields[] = $this->prepareField($mod_language, 'fade_out_of_stock_option_values');
			//$fields[] = $this->prepareField($mod_language, 'fade_out_of_stock_option_values', $values, 'allow_zero_select');
		}
		
		$fields[] = $this->prepareField($mod_language, 'stock_control');
		
		$values = [
			0 => $option_show_clear_options_not,
			1 => $option_show_clear_options_top,
			2 => $option_show_clear_options_bot,
		];
		$fields[] = $this->prepareField($mod_language, 'show_clear_options', $values);
		if ( $this->liveopencart_ext_ro->versionPRO() ) {
			$fields[] = $this->prepareField($mod_language, 'allow_empty');
		}
		$fields[] = $this->prepareField($mod_language, 'hide_inaccessible');
		
		$fields[] = $this->prepareField($mod_language, 'hide_option');
		$fields[] = $this->prepareField($mod_language, 'unavailable_not_required');
		
		$values = [
			0 => $option_select_first_not,
			1 => $option_select_first,
			2 => $option_select_first_last,
			3 => $option_select_first_always,
			4 => $option_select_first_of_first,
		];
		$fields[] = $this->prepareField($mod_language, 'select_first', $values);
		$fields[] = $this->prepareField($mod_language, 'defaults_to_cart', false, 'select_first');
		$fields[] = $this->prepareField($mod_language, 'step_by_step');
		
		$fields[] = $this->prepareField($mod_language, 'custom_theme_id', false, '', false, 'input');
		
		return $fields;
		
	}
	
	public function export_new() {
		
		$this->loadLanguage();
		
		if ( $this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['export_fields'])
			&& is_array($this->request->post['export_fields']) && count($this->request->post['export_fields']) > 0 ) {
			
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('related_options_export', ['related_options_export' => $this->request->post] );
			
			$this->load->model('extension/module/related_options');
			$this->makeExport();
			exit;
		}
		
	}
	
	// separate entry for standard fields: array('column'=>$columns_cnt)
	// one entry for all options fields: array( option_id => array('column=>'$columns_cnt, ... ) )
	protected function makeExportGetListOfColumns($rov, $fields, $simple_columns = false) {
		$columns = [];
		// show columns before options_values_ids
		$columns_cnt = 0;
		foreach ( $fields as $field ) { // header
			if ( $simple_columns || ($field != $this->export_field_discounts && $field != $this->export_field_specials) ) {
				if ( !$simple_columns && $field == $this->export_field_custom ) {
					if ( $this->liveopencart_ext_ro->versionPRO() ) {
						if ( !empty($rov['data']['fields']) ) {
							foreach ( $rov['data']['fields'] as $variant_field ) {
								$field_name = $this->export_field_custom_prefix.json_encode([
									'key'        => $variant_field['key'],
									'name'       => $variant_field['name'],
									'type'       => $variant_field['type'],
									'sort_order' => $variant_field['sort_order'],
								]);
								$columns[$field_name] = ['column' => $columns_cnt, 'variant_field' => $variant_field];
								$columns_cnt++;
							}
						}
					}
				} else {
				
					$columns[$field] = ['column' => $columns_cnt];
					$columns_cnt++;
				}
			}
		}
		return $columns;
	}
	
	protected function makeExportPutHeader($sheet_info, $columns) {
		$sheet_info->addRow();
		foreach ($columns as $field => $column) {
			$sheet_info->setLastRowValue($column['column'], $field);
			//$sheet_data[$column['column']] = $field;
			//$sheet->setCellValueByColumnAndRow($column['column'], 1, $field); // rows numeration starts from 1
		}
	}
	
	protected function makeExportPutToSheet($rov, $sheet_info, $rows, $fields, $ro_options) {
		
		$columns = $this->makeExportGetListOfColumns($rov, $fields);
		
		if ( !$sheet_info->getNumRows() ) {
			$this->makeExportPutHeader($sheet_info, $columns);
		}
		
		foreach ( $rows as $row ) {
			$sheet_info->addRow();
			
			foreach ( $fields as $field ) {
			
				$relatedoptions_id = $row['relatedoptions_id'];
				if ( isset($ro_options[$relatedoptions_id]) ) {
					$ro_option_ids = $ro_options[$relatedoptions_id]['option_ids'];
				} else {
					$ro_option_ids = [];
				}
			
				if ( $field == $this->export_field_options ) {
					// put options
					$options_values_ids = '';
					foreach ( $rov['data']['options'] as $rov_option ) {
						
						if ( isset($ro_option_ids[$rov_option['option_id']]) ) {
							// to place in the right order
							$options_values_ids .= ($options_values_ids == '' ? '' : ',').$rov_option['option_id'].":".$ro_option_ids[$rov_option['option_id']];
						}
					}
					$col_num = $columns[$field]['column'];
					$sheet_info->setLastRowValue($col_num, $options_values_ids);
					
				} elseif ( $field == $this->export_field_description_of_options ) {
					
					$description_of_options = '';
					if ( !empty($ro_options[$relatedoptions_id]['description']) ) {
						$description_of_options = $ro_options[$relatedoptions_id]['description'];
					} else {
						$description_of_options = '';
					}
					$col_num = $columns[$field]['column'];
					$sheet_info->setLastRowValue($col_num, $description_of_options);
					
				} elseif ( $field == $this->export_field_product_name ) {
					
					if (!empty($row[$field])) {
						$col_num = $columns[$field]['column'];
						$sheet_info->setLastRowValue($col_num, $this->decodeHTML($row[$field]));
					}
					
				} elseif ( $field != $this->export_field_discounts && $field != $this->export_field_specials ) {
					
					if ( $field == $this->export_field_custom ) {
						if ( $this->liveopencart_ext_ro->versionPRO() ) {
							foreach ( $columns as $column_field => $column ) {
								if ( substr($column_field, 0, strlen($this->export_field_custom_prefix)) == $this->export_field_custom_prefix ) {
									$field_value = '';
									if ( !empty($row['fields']) ) {
										foreach ($row['fields'] as $variant_field_value) {
											if ( $variant_field_value['relatedoptions_field_id'] == $column['variant_field']['relatedoptions_field_id'] ) {
												$field_value = $variant_field_value['value'];
												break;
											}
										}
									}
									if ( $field_value ) {
										$col_num = $columns[$column_field]['column'];
										$sheet_info->setLastRowValue($col_num, $field_value);
									}
								}
							}
							
						}
						
					} else {
					
						// put other fields
						if (!empty($row[$field])) {
							$col_num = $columns[$field]['column'];
							$sheet_info->setLastRowValue($col_num, $row[$field]);
						}
					}
				}
			}
			unset($sheet_data);
			
		}
		
	}
	
	protected function makeExportPutToSheetSimpleColumns($rov, $sheet_info, $rows, $fields) {
		
		$columns = $this->makeExportGetListOfColumns($rov, $fields, true);
		
		if ( !$sheet_info->getNumRows() ) {
			$this->makeExportPutHeader($sheet_info, $columns);
		}
		
		foreach ( $rows as $row ) {
			$sheet_info->addRow();
			
			foreach ( $fields as $field ) {
			
				if (!empty($row[$field])) {
					$col_num = $columns[$field]['column'];
					$sheet_info->setLastRowValue($col_num, $row[$field]);
				}
				
			}
			unset($sheet_data);
			
		}
		
	}
	
	protected function makeExportForVariant($fields, $sheet_info, $sheet_discounts, $sheet_specials, $sheet_product_variants, $rov, $product_ids_start_end) {
		
		$rov_id = !empty($rov['rov_id']) ? $rov['rov_id'] : 0 ;
		
		$products_variants = $this->model_extension_module_related_options->getProductsROVariantsByROVariantIdForExport($rov_id);
		$this->makeExportPutToSheetSimpleColumns($rov, $sheet_product_variants, $products_variants, ['product_id', 'variant_name', 'options_ids', 'allow_zero_select']);
		
		$ro_options = $this->model_extension_module_related_options->getVariantROOptionsToExport($rov_id, $product_ids_start_end);
		
		$ro_combs = $this->model_extension_module_related_options->getVariantROCombsToExport($rov_id, $product_ids_start_end);
		$this->makeExportPutToSheet($rov, $sheet_info, $ro_combs, $fields, $ro_options);
		
		// discounts
		if ( $sheet_discounts && in_array($this->export_field_discounts, $fields) ) {

			$ro_discounts = $this->model_extension_module_related_options->getVariantRODiscountsToExport($rov_id, $product_ids_start_end);
			
			$fields_d = ['product_id', $this->export_field_options, $this->export_field_description_of_options, 'customer_group_id', 'quantity', 'price'];
			
			$this->makeExportPutToSheet($rov, $sheet_discounts, $ro_discounts, $fields_d, $ro_options);
		}
		
		// specials
		if ( $sheet_specials && in_array($this->export_field_specials, $fields) ) {
			
			$ro_specials = $this->model_extension_module_related_options->getVariantROSpecialsToExport($rov_id, $product_ids_start_end);
			
			$fields_s = ['product_id', $this->export_field_options, $this->export_field_description_of_options, 'customer_group_id', 'price'];
			
			$this->makeExportPutToSheet($rov, $sheet_specials, $ro_specials, $fields_s, $ro_options);
		}
		
	}
	
	//protected function removeSymbolsUnusableInSheetNames($str) {
	//	return str_replace(['*', '\\', '/', '[', ']'], ' ' , $str);
	//}
	
	public function makeExport() {
		
		$this->loadLanguage();
		
		$ro_settings   = $this->config->get('related_options');
		$export_fields = $this->request->post['export_fields'];
		
		$product_ids_start_end  = [];
		$export_only_variant_id = false;
		if ( !empty($this->request->post['export_new_method']) ) {
			if ( $this->request->post['export_new_method'] == 1 ) {
				$product_ids_start_end['start'] = ( empty($this->request->post['export_new_start_product_id']) ? 0 : (int)$this->request->post['export_new_start_product_id'] );
				$product_ids_start_end['end']   = ( empty($this->request->post['export_new_end_product_id']) ? $this->getMaxProductIdWithRO() : (int)$this->request->post['export_new_end_product_id'] );
			} elseif ( $this->request->post['export_new_method'] == 2 && !empty($this->request->post['export_new_variant_id']) ) {
				$export_only_variant_id = (int)$this->request->post['export_new_variant_id'];
			}
		}
		
		$sheet_product_variants = $this->getXLXSLib()->getNewSheetInfo($this->export_sheet_variants_name);
		
		// to get all variant options in necessary order
		$rovs_all = $this->model_extension_module_related_options->getVariants(true);
		
		$rovs = $this->model_extension_module_related_options->getROVariantsHavingCombs($product_ids_start_end, $export_only_variant_id);
		
		$sheets_infos   = [];
		$sheets_infos[] = $sheet_product_variants;
		foreach ( $rovs as $rov ) {
			//$sheets_infos[] = $this->getXLXSLib()->getNewSheetInfo($this->removeSymbolsUnusableInSheetNames('RO '.( !empty($rov['name']) ? $rov['name'] : '') ));
			$sheet_name = 'RO '.( !empty($rov['name']) ? $rov['name'] : '' );
			if (strlen($sheet_name) > 31) { // too long
				$sheet_name = 'RO too long name, ID '.$rov['rov_id'];
			}
			$sheets_infos[] = $this->getXLXSLib()->getNewSheetInfo($sheet_name);
		}
		
		$sheet_discounts = false;
		if ( in_array('discounts', $export_fields) && !empty($ro_settings['spec_price_discount']) ) {
			$sheets_infos[]  = $this->getXLXSLib()->getNewSheetInfo('Discounts');
			$sheet_discounts = $sheets_infos[count($sheets_infos) - 1];
		}
		$sheet_specials = false;
		if ( in_array('discounts', $export_fields) && !empty($ro_settings['spec_price_special']) ) {
			$sheets_infos[] = $this->getXLXSLib()->getNewSheetInfo('Specials');
			$sheet_specials = $sheets_infos[count($sheets_infos) - 1];
		}
		
		// fill data
		foreach ( $rovs as $rov_index => $rov ) {
			
			$sheet_info = $sheets_infos[$rov_index + 1]; // first sheet - $sheet_product_variants
			
			$rov['data'] = $rovs_all[$rov['rov_id']];
			
			$this->makeExportForVariant($export_fields, $sheet_info, $sheet_discounts, $sheet_specials, $sheet_product_variants, $rov, $product_ids_start_end);
			
		}
		
		$this->getXLXSLib()->exportSheetsInfosToBrowser($sheets_infos, 'ro_export.xlsx');
		
	}
	
	public function import_new() {
		
		$this->loadLanguage();
		
		$json = $this->makeImport();
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	// option_id:option_value_id, ...
	protected function makeImportPrepareOptionsValues($values) {
		
		$arr = explode( ',', trim($values) );
		foreach ( $arr as &$val ) {
			$sub_arr = explode(':', trim($val));
			$sub_arr = array_map('intval', $sub_arr);
			
			$val = implode( ':', $sub_arr);
		}
		unset($val);
		
		return implode(',', $arr);
	}
	
	// option_id:option_value_id, ...
	protected function makeImportParceOptions($values_string) {
		
		if ( $values_string ) {
		
			$options = [];
			$arr     = explode( ',', trim($values_string) );
			foreach ($arr as $val) {
				$sub_arr         = explode(':', $val);
				$option_id       = 0;
				$option_value_id = 0;
				if ( count($sub_arr) ) {
					$option_id = (int)$sub_arr[0];
					if ( count($sub_arr) > 1 ) {
						$option_value_id = (int)$sub_arr[1];
					}
				}
				$options[$option_id] = $option_value_id;
			}
			
			return $options;
			
		} else {
			return [];
		}
		
	}
	
	protected function makeImportReadHeader($sheet_info, $requred_columns) {
		
		$result = ['error' => []];
		
		$row = isset($sheet_info->data[0]) ? $sheet_info->data[0] : [];
		
		foreach ( $row as &$val ) {
			if ( substr($val, 0, strlen($this->export_field_custom_prefix)) != $this->export_field_custom_prefix ) {
				$val = utf8_strtolower( trim($val) );
			}
		}
		unset($val);
		$head = array_flip($row);
			
		foreach ( $requred_columns as $column ) {
			if ( !isset($head[$column]) ) {
				$result['error'] = '"'.$column.'" '.$this->language->get('entry_import_new_error_not_found').' "'.$sheet_info->name.'" ';
			}
		}
		
		$result['head'] = $head;
		return $result;
		
	}
	
	// read discounts or special
	protected function makeImportReadDS($sheet_info, $products, $fields, $sheet_name, $import_ds_only = false) {
		
		$result = ['errors' => []];
		
		$requred_columns = ['product_id', $this->export_field_options];
		
		if ( $sheet_info->getNumRows() > 1) {
		
			$header_read_result = $this->makeImportReadHeader($sheet_info, $requred_columns);
			$head               = $header_read_result['head'];
			if ( $header_read_result['error'] ) {
				$result['errors'][] = $header_read_result['error'];
			}
			
			if ( !$result['errors'] ) {
				
				for ($row_index = 1;$row_index < $sheet_info->getNumRows();$row_index++) {
					//$row = $data[$i];
					$ds                        = [];
					$ro_options_string         = '';
					$ro_options_string_ordered = '';
					$options                   = [];
					$product_id                = 0;
					foreach ( $fields as $field ) {
						if ( isset($head[$field]) ) {
							if ( $field == $this->export_field_options ) {
								$ro_options_string = $this->makeImportPrepareOptionsValues( $sheet_info->getValue($row_index, (int)$head[$field]) );
								$options           = $this->makeImportParceOptions($ro_options_string);
								$ro_options_string_ordered = $this->getOrderedOptionsSearchString($options);
							} elseif ( $field == 'product_id') {
								$product_id = (int)$sheet_info->getValue($row_index, (int)$head[$field]);
							} else {
								$ds[$field] = (string)$sheet_info->getValue($row_index, (int)$head[$field]);
							}
						}
					}
					
					if ( $import_ds_only ) { // no RO combs from the file, so we have to read them from DB (not entirely, but only basically - to control their existence)
						if ( !isset($products[$product_id]) ) {
							
							$ro_data = $this->model_extension_module_related_options->getRODataCached($product_id);
							if ($ro_data) {
								foreach ($ro_data as $ro_dt) {
									foreach ($ro_dt['ro'] as $ro_comb) {
										$options_string_ordered                         = $this->getOrderedOptionsSearchString($ro_comb['options']);
										$products[$product_id][$options_string_ordered] = [];
									}
								}
							}
						}
					}
					
					if ( !isset($products[$product_id]) || !isset($products[$product_id][$ro_options_string_ordered]) ) {
						$result['errors'][] = $this->language->get('entry_import_new_error_no_ro').' "'.$sheet_info->name.'"  #'.($row_index + 1).' ('.$product_id.': '.$ro_options_string.')';
					} else {
						$products[$product_id][$ro_options_string_ordered][$sheet_name][] = $ds;
						if (!isset($products[$product_id][$ro_options_string_ordered]['options'])) {
							$products[$product_id][$ro_options_string_ordered]['options'] = $options;
						}
					}
				}
			}
		
		} else {
			// it's not an error for discounts and specials
			//$result['errors'][] = $this->language->get('entry_import_new_error_no_data').' "'.$sheet->getTitle().'" ';
		}
		
		if ($import_ds_only) { // remove ro combs having no discounts updated
			foreach ($products as $p_id => &$product_ro_combs)  {
				$product_ro_combs = array_filter($product_ro_combs, function($product_ro_comb){
					return !empty($product_ro_comb['options']);
				});
			}
			unset($product_ro_combs);
		}
		
		$result['products'] = $products;
		return $result;
		
	}
	
	protected function orderOptionsByKeys($options) {
		$options_ids = $this->model_extension_module_related_options->getOptionsIdsOrderedCached();
		return array_filter(array_replace(array_fill_keys($options_ids, false), $options));
	}
	
	protected function getOrderedOptionsSearchString($options) {
		$options = $this->orderOptionsByKeys($options);
		$parts   = [];
		array_walk($options, function($v, $k) use (&$parts){
			$parts[] = ''.$k.':'.$v;
		}, $options);
		return implode(',', $parts);
	}
	
	protected function makeImportReadRO($sheet_info, $products) {
		
		$result = ['errors' => [], 'warnings' => []];
		
		$requred_columns = ['product_id', $this->export_field_options];
		
		if ( $sheet_info->getNumRows() > 1) {
			
			$header_read_result = $this->makeImportReadHeader($sheet_info, $requred_columns);
			$head               = $header_read_result['head'];
			if ( $header_read_result['error'] ) {
				$result['errors'][] = $header_read_result['error'];
			}
			
			// put to $products[] all combinations without separating by variants
			if ( !$result['errors'] ) {
				
				if ( $this->liveopencart_ext_ro->versionPRO() ) { // prepare custom variant fields
					$variant_field_columns = [];
					foreach ( $head as $column_name => $column_num ) {
						if ( substr($column_name, 0, strlen($this->export_field_custom_prefix)) == $this->export_field_custom_prefix ) {
							
							$variant_field_columns[] = [
								'column_name'   => $column_name,
								'column_num'    => $column_num,
								'variant_field' => json_decode(substr($column_name, strlen($this->export_field_custom_prefix)), true),
							];
							
						}
					}
				}
			
				for ($row_index = 1;$row_index < $sheet_info->getNumRows();$row_index++) {
					
					//$row = $data[$i];
					$ro_comb                   = [];
					$ro_options_string         = '';
					$ro_options_string_ordered = '';
					$product_id                = 0;
					foreach ( $this->getExportNewFields() as $field ) {
						
						if ( isset($head[$field]) ) {
							if ( $field == $this->export_field_options ) {
								$ro_options_string = $this->makeImportPrepareOptionsValues( $sheet_info->getValue($row_index, (int)$head[$field]) );
								$ro_comb['options']        = $this->makeImportParceOptions($ro_options_string);
								$ro_options_string_ordered = $this->getOrderedOptionsSearchString($ro_comb['options']);
							} elseif ( $field == 'product_id') {
								$product_id = (int)$sheet_info->getValue($row_index, (int)$head[$field]);
							} elseif ( $field != $this->export_field_discounts && $field != $this->export_field_specials )  {
								$ro_comb[$field] = (string)$sheet_info->getValue($row_index, (int)$head[$field]);
							}
						} else {
							if ( $field == $this->export_field_custom ) {
								
								if ( !isset($ro_comb['fields']) ) {
									$ro_comb['fields'] = [];
								}
								if ( $this->liveopencart_ext_ro->versionPRO() && !empty($variant_field_columns) ) {
									
									foreach ( $variant_field_columns as $variant_field_column ) {
										
										$cell_value = (string)$sheet_info->getValue($row_index, (int)$variant_field_column['column_num']);
										
										if ( $cell_value ) {
											
											$variant_field_value          = $variant_field_column['variant_field'];
											$variant_field_value['value'] = (string)$sheet_info->getValue($row_index, (int)$variant_field_column['column_num']);
											$ro_comb['fields'][]          = $variant_field_value;
										}
									}
									
								}
							}
						}
					}
					
					if ( !isset($products[$product_id]) ) {
						$products[$product_id] = [];
					}
					$products[$product_id][$ro_options_string_ordered] = $ro_comb;
				}
			}
		} else {
			$result['warnings'][] = $this->language->get('entry_import_new_error_no_data').' "'.$sheet_info->name.'" ';
		}
		$result['products'] = $products;
		
		return $result;
	}
	
	protected function makeImportGetProductsVariantsFromSheet($sheet) {
		$products_variants = $sheet->getAsAssocArrayTableByHead();
		array_walk($products_variants, function(&$product_variant){
			$options_ids = explode(',',$product_variant['options_ids']);
			$options_ids = array_map('intval', $options_ids);
			$options_ids = array_filter($options_ids);
			sort($options_ids);
			$product_variant['_options_ids_sorted'] = $options_ids;
		});
		return $products_variants;
	}
	
	public function makeImport() {
		
		$json = ['errors' => [], 'warnings' => []];
		
		$this->load->model('extension/module/related_options');
		
		if ( !$this->user->hasPermission('modify', 'extension/module/related_options')) {
			
			$json['errors'][] = $this->language->get('error_permission');
			
		} elseif (!empty($this->request->files['file_import']['name']) && $this->request->files['file_import']['tmp_name'] ) {
			
			$real_file_name = $this->request->files['file_import']['tmp_name'];
			
			$force_php_excel = strtolower(substr($real_file_name, -4)) == '.xls';
			
			if ( $this->getXLXSLib($force_php_excel)->getAvailability() ) {
				$sheets_infos = $this->getXLXSLib()->getSheetsInfosFromFile($real_file_name, 0);
				
			} else{
				$json['errors'][] = sprintf($this->language->get('error_xlsx_lib_is_not_found'), $this->getXLXSLib()->getName());
				if ( $force_php_excel ) {
					$json['errors'][count($json['errors']) - 1] += ' '.$this->language->get('error_php_excel_is_necessary_for_xls');
				}
			}
			
			$import_delete_before         = isset($this->request->post['import_delete_before']) ? $this->request->post['import_delete_before'] : false;
			$import_without_remove_method = isset($this->request->post['import_without_remove_method']) ? $this->request->post['import_without_remove_method'] : false;
			$import_discounts_only        = !$import_delete_before && $import_without_remove_method == '1';
			
			$sheet_discounts = false;
			$sheet_specials  = false;
			
			$products = [];
			
			$products_variants             = false;
			$sheet_products_variants_index = false;
			
			if ( !$import_discounts_only ) {
				foreach ($sheets_infos as $sheet_index => $sheet_info) {
					if ( $sheet_info->name == $this->export_sheet_variants_name ) {
						$products_variants             = $this->makeImportGetProductsVariantsFromSheet($sheet_info);
						$sheet_products_variants_index = $sheet_index;
						break;
					}
				}
			}
			
			// read RO sheets
			foreach ($sheets_infos as $sheet_index => $sheet_info) {
				
				if ( $sheet_products_variants_index !== false && $sheet_products_variants_index == $sheet_index ) {
					continue;
				}
				
				$sheet_title = trim(utf8_strtolower($sheet_info->name));
				
				if ( $sheet_title == $this->export_field_discounts ) {
					$sheet_discounts = $sheet_info;
				} else if ( $sheet_title == $this->export_field_specials ) {
					$sheet_specials = $sheet_info;
				} elseif ( substr($sheet_title, 0, 2) == 'ro' ) {
					
					if ( !$import_discounts_only ) {
					
						// import combination
						$result   = $this->makeImportReadRO($sheet_info, $products);
						$products = $result['products'];
						if ( $result['errors'] ) {
							$json['errors'] = $json['errors'] + $result['errors'];
						}
						if ( $result['warnings'] ) {
							$json['warnings'] = $json['warnings'] + $result['warnings'];
						}
					}
				} else {
					
					if ( !$import_discounts_only ) {
						$json['errors'][] = $this->language->get('entry_import_new_error_skipped').' '.$sheet_info->name;
					}
				}
			}
			
			if ( $products || $import_discounts_only ) {
			
				// read Discounts and Specials sheets
				if ( $sheet_discounts ) {
					
					$result   = $this->makeImportReadDS($sheet_discounts, $products, ['product_id', $this->export_field_options, 'customer_group_id', 'quantity', 'price'], 'discounts', $import_discounts_only);
					$products = $result['products'];
					
					if ( $result['errors'] ) {
						$json['errors'] = $json['errors'] + $result['errors'];
					}
				}
				
				if ( $sheet_specials && !$import_discounts_only ) {
					$result   = $this->makeImportReadDS($sheet_specials, $products, ['product_id', $this->export_field_options, 'customer_group_id', 'price'], 'specials');
					$products = $result['products'];
					if ( $result['errors'] ) {
						$json['errors'] = $json['errors'] + $result['errors'];
					}
				}
			} else {
				$json['errors'][] = $this->language->get('entry_import_new_error_no_sheets');
			}
			
			if ( !$json['errors'] || $import_discounts_only ) {
				
				if ($import_delete_before == 1) {
					$this->model_extension_module_related_options->removeRelatedOptions();
				}
				
				$ro_cnt = 0;
				foreach ($products as $product_id => $export_ro_combs) {
					$ro_cnt += count($export_ro_combs);
					
					if ($import_delete_before == 2) {
						$this->model_extension_module_related_options->removeRelatedOptions($product_id);
					}
					
					$ro_data = $this->model_extension_module_related_options->getRODataCached($product_id);
					
					$new_ro_combs = [];
					
					foreach ($export_ro_combs as $export_ro_comb) {
						
						$new_options_ids = array_keys($export_ro_comb['options']);
						$new_options_ids = array_map('intval', $new_options_ids);
						$new_options_ids = array_filter($new_options_ids);
						sort($new_options_ids);
						
						$ro_found = false;
						foreach ($ro_data as &$ro_dt) {
							
							if ( !isset($ro_dt['_options_ids_sorted']) ) {
								$ro_dt['_options_ids_sorted'] = $ro_dt['options_ids'];
								sort($ro_dt['_options_ids_sorted']);
							}
							
							// combination set is relevant, let's find current new combination in this set
							if ( $new_options_ids == $ro_dt['_options_ids_sorted'] ) {
							//if ( !array_diff($new_options_ids, $ro_dt['options_ids']) && !array_diff($ro_dt['options_ids'], $new_options_ids) && count($export_ro_comb['options']) == count($ro_dt['options_ids']) ) {
							//if ( !array_diff_assoc($new_options_ids, $ro_dt['options_ids']) && count($export_ro_comb['options']) == count($ro_dt['options_ids']) ) {
							
								if ( empty($ro_dt['_product_variant_updated']) && $products_variants ) {
									foreach ( $products_variants as $product_variant ) {
										if ( $product_variant['product_id'] == $product_id && $product_variant['_options_ids_sorted'] == $ro_dt['_options_ids_sorted'] ) {
											if (isset($product_variant['allow_zero_select'])) {
												if ( utf8_strtolower(trim($product_variant['allow_zero_select'])) == 'no' ) {
													$ro_dt['allow_zero_select'] = 1;
												} elseif ( utf8_strtolower(trim($product_variant['allow_zero_select'])) == 'yes' ) {
													$ro_dt['allow_zero_select'] = 2;
												} else {
													$ro_dt['allow_zero_select'] = 0;
												}
											}
											break;
										}
									}
									$ro_dt['_product_variant_updated'] = true;
								}
							
								if ( !isset($ro_dt['_option_combs']) ) {
									// preserve keys
									$ro_dt['_option_combs'] = array_combine(array_keys($ro_dt['ro']), array_column($ro_dt['ro'], 'options'));
									// $ro_dt['_option_combs'] = array_column($ro_dt['ro'], 'options');
								}
								$ro_comb_index = array_search($export_ro_comb['options'], $ro_dt['_option_combs']);
								
								if ( $ro_comb_index !== false ) {
									$ro_comb              = &$ro_dt['ro'][$ro_comb_index];
									$ro_comb['discounts'] = [];
									if (!$import_discounts_only) {
										$ro_comb['specials'] = [];
									}
									$ro_comb = array_merge($ro_comb, $export_ro_comb);
									unset($ro_comb);
									$ro_found = true;
								}
								
								// relevant combination is not found, but combination set is relevant, let's add this combination to this set
								if (!$ro_found) {
									
									$ro_dt['ro'][] = $export_ro_comb;
									$ro_found      = true;
								}
								break;
							}
						}
						unset($ro_dt);
						if ( !$ro_found ) { // if there is no relevant variant of related option, add new variant (to product)
							
							if ( count($ro_data) == 0 || $this->liveopencart_ext_ro->versionPRO() ) { // import few ro variants per product only for ROPRO
							
								$new_ro_combs_set = [
									'rovp_id'                        => '',
									'use'                            => true,
									'related_options_variant_search' => true,
									'ro'                             => [$export_ro_comb],
									'options_ids'                    => $new_options_ids,
								];
							
								$ro_data[] = $new_ro_combs_set;
							}
						}
						
					}
					
					$product_data = ['ro_data_included' => true, 'ro_data' => $ro_data];
					
					$this->model_extension_module_related_options->setROData($product_id, $product_data);
					
				}
				$json['products']       = count($products);
				$json['relatedoptions'] = $ro_cnt;
				
				$json['success'] = $this->language->get('entry_import_new_ok');
				
			}
			
		} else {
			$json['errors'][] = $this->language->get('entry_import_new_error_not_uploaded');
		}
		
		return $json;
	}
  
	public function import() { // (old) to import from old versions of RO and ROPRO
		
		$this->loadLanguage();
		$this->load->model('extension/module/related_options');
		
		$json = [];
		
		if ( !$this->user->hasPermission('modify', 'extension/module/related_options')) {
			
			$json['error'] = $this->language->get('error_permission');
			
		} elseif (!empty($this->request->files['file']['name']) && $this->request->files['file']['tmp_name'] ) {
			
			$real_file_name = $this->request->files['file']['tmp_name'];
			
			$force_php_excel = strtolower(substr($real_file_name, -4)) == '.xls';
			
			if ( $this->getXLXSLib($force_php_excel)->getAvailability() ) {
				$data = $this->getXLXSLib()->getSheetDataFromFile($real_file_name, 0);
			} else{
				$json['error'] = sprintf($this->language->get('error_xlsx_lib_is_not_found'), $this->getXLXSLib()->getName());
				if ( $force_php_excel ) {
					$json['error'] .= ' '.$this->language->get('error_php_excel_is_necessary_for_xls');
				}
			}
			
			if ( !isset($json['error']) ) {
			
				if (count($data) > 1) {
					
					$head = array_flip($data[0]);
					
					if (!isset($head['product_id'])) {
						$json['error'] = "product_id not found";
					}
					
					if (!isset($head['quantity'])) {
						$json['error'] = "quantity not found";
					}
					
					if (!isset($head['option_id1'])) {
						$json['error'] = "option_id1 not found";
					}
					
					if (!isset($head['option_value_id1'])) {
						$json['error'] = "option_value_id1 not found";
					}
					
					if (!isset($json['error'])) {
						
						$f_options = [];
						for ($i = 1;$i <= 100;$i++) {
							if ( isset($head['option_id'.$i]) && isset($head['option_value_id'.$i]) ) {
								$f_options[] = $i;
							}
						}
						
						$products = [];
						
						for ($i = 1;$i < count($data);$i++) {
							
							$row = $data[$i];
							
							$product_id = (int)$row[$head['product_id']];
							if (!isset($products[$product_id])) {
								$products[$product_id] = ['relatedoptions' => [], 'related_options_use' => true, 'related_options_variant_search' => true];
							}
							
							$options = [];
							foreach ($f_options as $opt_num) {
								if ((int)$row[$head['option_id'.$opt_num]] != 0) {
									$options[(int)$row[$head['option_id'.$opt_num]]] = (int)$row[$head['option_value_id'.$opt_num]];
								}
							}
							
							$products[$product_id]['relatedoptions'][] = [
								'options'         => $options,
								'quantity'        => $row[(int)$head['quantity']],
								'price_prefix'    => isset($head['price_prefix']) ? (string)$row[(int)$head['price_prefix']] : '',
								'price'           => isset($head['price']) ? (float)$row[(int)$head['price']] : 0,
								'model'           => isset($head['relatedoptions_model']) ? $row[(int)$head['relatedoptions_model']] : '',
								'sku'             => isset($head['relatedoptions_sku']) ? $row[(int)$head['relatedoptions_sku']] : '',
								'upc'             => isset($head['relatedoptions_upc']) ? $row[(int)$head['relatedoptions_upc']] : '',
								'ean'             => isset($head['relatedoptions_ean']) ? $row[(int)$head['relatedoptions_ean']] : '',
								'jan'             => isset($head['relatedoptions_jan']) ? $row[(int)$head['relatedoptions_jan']] : '',
								'stock_status_id' => isset($head['stock_status_id']) ? $row[(int)$head['stock_status_id']] : '',
								'weight_prefix'   => isset($head['weight_prefix']) ? $row[(int)$head['weight_prefix']] : '',
								'weight'          => isset($head['weight']) ? $row[(int)$head['weight']] : '',
							];
							
						}
						
						$this->load->model('extension/module/related_options');
						
						if (isset($this->request->post['import_delete_before']) && $this->request->post['import_delete_before'] == 1) {
							$this->model_extension_module_related_options->removeRelatedOptions();
						}
						
						$ro_cnt = 0;
						foreach ($products as $product_id => $product) {
							$ro_cnt += count($product['relatedoptions']);
							
							if (isset($this->request->post['import_delete_before']) && $this->request->post['import_delete_before'] == 2) {
								$this->model_extension_module_related_options->removeRelatedOptions($product_id);
							}
							
							$ro_data = $this->model_extension_module_related_options->getROData($product_id);
							
							$new_ro_combs = [];
							
							foreach ($product['relatedoptions'] as $new_ro) {
								
								$new_options_ids = [];
								foreach ($new_ro['options'] as $option_id => $option_value_id) {
									$new_options_ids[] = $option_id;
								}
								
								$ro_found = false;
								foreach ($ro_data as &$ro_dt) {
									
									// combination set is relevant, let's find current new combination in this set
									if ( !array_diff_assoc($new_options_ids, $ro_dt['options_ids']) && count($new_ro['options']) == count($ro_dt['options_ids']) ) {
										
										foreach ($ro_dt['ro'] as &$ro_comb) {
											if ( !array_diff_assoc($new_ro['options'], $ro_comb['options']) && count($new_ro['options']) == count($ro_comb['options'])) {
												// refresh relevant combination field accordingly to new combination
												foreach ($ro_comb as $ro_comb_key => &$ro_comb_value) {
													if (isset($new_ro[$ro_comb_key])) {
														$ro_comb_value = $new_ro[$ro_comb_key];
													}
												}
												unset($ro_comb_value);
												$ro_found = true;
												break;
											}
										}
										unset($ro_comb);
										
										// relevant combination is not found, but combination set is relevant, let's add this combination to this set
										if (!$ro_found) {
											$ro_dt['ro'][] = $new_ro;
											$ro_found      = true;
										}
									}
								}
								unset($ro_dt);
								if (!$ro_found) { // if there's not relevant set of options combinations, let's add new set
									
									$new_ro_combs_set = [
										'rovp_id'                        => '',
										'use'                            => true,
										'related_options_variant_search' => true,
										'ro'                             => [$new_ro],
										'options_ids'                    => $new_options_ids,
									];
									
									$ro_data[] = $new_ro_combs_set;
									
								}
							}
							
							$product_data = ['ro_data_included' => true, 'ro_data' => $ro_data];
							$this->model_extension_module_related_options->setROData($product_id, $product_data);
							
						}
						$json['products']       = count($products);
						$json['relatedoptions'] = $ro_cnt;
						
						$json['success'] = $this->language->get('entry_import_ok');
						
					}
					
				} else {
					$json['error'] = "empty table";
				}
				
			} else {
				$json['error'] = "file not uploaded";
			}
			
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function updateProductDataByRO() {
	  
		$product_id = (int)$this->request->get['product_id'];
		if ( !$this->model_extension_module_related_options ) {
			$this->load->model('extension/module/related_options');
		}
		$this->model_extension_module_related_options->updateStandardProductDataByRO($product_id);
	  
	}
	
	public function install() {
		$this->load->model('extension/module/related_options');
		$this->model_extension_module_related_options->install();
		
		$this->load->model('setting/setting');
		$msettings = [
			'related_options' => [
				'update_quantity'             => 1,
				'update_options'              => 1,
				'ro_use_variants'             => 1,
				'disable_all_options_variant' => 1,
				'related_options_version'     => $this->liveopencart_ext_ro->getCurrentVersion(),
			],
		];
		$this->model_setting_setting->editSetting('related_options', $msettings);
		
		$this->model_setting_setting->editSetting('module_related_options', ['module_related_options_status' => 1]); // status = enabled
	}
  
	public function uninstall() {
		$this->load->model('extension/module/related_options');
		$this->model_extension_module_related_options->uninstall();
	}
  
	protected function validate() {
		if ( !$this->user->hasPermission('modify', 'extension/module/related_options')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	public function setROCombQuantity() {
		$json = [];
		if ( $this->liveopencart_ext_ro->installed() ) {
			$this->load->model('extension/module/related_options');
		
			$ro_id    = (int)$this->request->post['ro_id'];
			$quantity = (int)$this->request->post['quantity'];
		
			$this->model_extension_module_related_options->setROCombQuantityAndUpdateStandardQuantities($ro_id, $quantity);
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	protected function getProductROQuantityEditFormHTML($product_id) {
		if ( $this->liveopencart_ext_ro->installed() ) {
			$this->load->model('extension/module/related_options');
		
			$data['user_token'] = $this->session->data['user_token'];
			$data['ro_data']    = $this->model_extension_module_related_options->getROData($product_id);
			foreach ( $data['ro_data'] as &$ro_dt ) {
				foreach ( $ro_dt['ro'] as &$ro_comb ) {
					$ro_comb['name'] = $this->model_extension_module_related_options->getROCombName($ro_comb['relatedoptions_id']);
				}
			}
			unset($ro_dt);
			
			return $this->load->view('extension/module/related_options_quantities_edit', $data);
		}
	}
	
	public function productListAddQuantitiesEdit() {
		if ( $this->liveopencart_ext_ro->installed() ) {

			$this->document->addStyle( $this->liveopencart_ext_ro->getResourceLinkWithVersion('view/javascript/liveopencart/related_options/quantities_edit.css') );
			$this->document->addScript( $this->liveopencart_ext_ro->getResourceLinkWithVersion('view/javascript/liveopencart/related_options/quantities_edit.js') );
			
			$this->event->register('view/catalog/product_list/before', new Action('extension/module/related_options/eventViewCatalogProductListBeforeAddQuantityEdit'));
			$this->event->register('view/catalog/product_list/after', new Action('extension/module/related_options/eventViewCatalogProductListAfterAddQuantityEdit'));
		}
	}
	
	public function eventViewCatalogProductListBeforeAddQuantityEdit(&$route, &$data, &$template) { // add data to products
		
		if ( $this->liveopencart_ext_ro->installed() ) {
			
			if ( !empty($data['products']) ) {
				$this->load->model('extension/module/related_options');
				
				foreach ( $data['products'] as &$product ) {
					$product['ro_quantities_edit'] = $this->getProductROQuantityEditFormHTML($product['product_id']);
					
					// remove product options used in RO from the standard (by another modules) product option quantities to edit
					if ( $product['ro_quantities_edit'] && !empty($product['option_data']['product_options']) ) {
						$pov_ids = $this->model_extension_module_related_options->getProductOptionsValuesUsedInRO($product['product_id']);
						if ( $pov_ids ) {
							$option_data = ['product_options' => []];
							foreach ($product['option_data']['product_options'] as $product_option_dt) {
								$povs = [];
								if ( !empty($product_option_dt['product_option_value']) ) {
									foreach ( $product_option_dt['product_option_value'] as $pov ) {
										if ( !in_array($pov['product_option_value_id'], $pov_ids) ) {
											$povs[] = $pov;
										}
									}
								}
								if ( $povs ) {
									$po                               = $product_option_dt;
									$po['product_option_value']       = $povs;
									$option_data['product_options'][] = $po;
								}
							}
							$product['option_data'] = $option_data;
						}
						
					}
				}
				unset($product);
			}
		}
	}
	
	public function eventViewCatalogProductListAfterAddQuantityEdit(&$route, &$args, &$output) {
		if ( $this->liveopencart_ext_ro->installed() ) {
			// move custom code (added by OCMod and already rendered on this stage) into product-name td
			$output = preg_replace('~(\Q</td>\E\s*<td data-ro-quantities-edit=.*)~', '', $output); // use such trick because of necessity for comp with another modification (Option Quantity In Admin Product List)
		}
	}
}
