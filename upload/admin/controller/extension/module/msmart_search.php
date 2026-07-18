<?php
/**
 * Search Filter
 * 
 * @author info@ocdemo.eu <info@ocdemo.eu> 
 */
class ControllerExtensionModuleMsmartSearch extends Controller {
	
	private $_name = 'msmart_search';
	
	private $_version = '3.0.6';
	
	private $error = array();
	
	private $data = array();
	
	private $cache = array();
	
	////////////////////////////////////////////////////////////////////////////
	
	public function __construct($registry) {
		parent::__construct($registry);
		
		$this->_cache_dir = DIR_SYSTEM . 'cache_mfs';
		
		$this->data['HTTP_URL'] = $this->httpUrl();

		$this->load->model('extension/module/msmart_search');
		
		// language
		$this->data = array_merge($this->data, $this->language->load('extension/module/' . $this->_name));
	}
	
	private function httpUrl() {
		$url = '';
	
		if( class_exists( 'MijoShop' ) ) {
			$url = HTTP_CATALOG . 'opencart/admin/';
		}
		
		return $url;
	}
	
	protected function render( $view ) {
		// current tab
		$this->data['tab_active'] = $view;
		$this->data['heading_panel_title'] = $this->language->get('text_edit');
		
		// tab's links
		$this->data['tab_config_link']			= $this->url->link('extension/module/' . $this->_name, 'user_token=' . $this->session->data['user_token'], true);
		$this->data['tab_live_filter_link']		= $this->url->link('extension/module/' . $this->_name . '/live_filter', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['tab_settings_link']		= $this->url->link('extension/module/' . $this->_name . '/settings', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['tab_search_history_link']	= $this->url->link('extension/module/' . $this->_name . '/search_history', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['tab_replace_phrase_link']	= $this->url->link('extension/module/' . $this->_name . '/replace_phrase', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['tab_recommended_link']		= $this->url->link('extension/module/' . $this->_name . '/recommended_products', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['tab_extra_fields_link']	= $this->url->link('extension/module/' . $this->_name . '/extra_fields', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['tab_about_link']			= $this->url->link('extension/module/' . $this->_name . '/about', 'user_token=' . $this->session->data['user_token'], true);
		
		$this->data['_name'] = $this->_name;
		$this->data['action_back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);
		
		// breadcrumbs
		$this->data['breadcrumbs'] = array(
			array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/dashboard' , 'user_token=' . $this->session->data['user_token'], true),
				'separator' => false
			),
			array(
				'text'      => $this->language->get('text_modules'),
				'href'      => $this->url->link('marketplace/extension' , 'user_token=' . $this->session->data['user_token'], true),
				'separator' => false
			),
			array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('extension/module/' . $this->_name, 'user_token=' . $this->session->data['user_token'], true),
				'separator' => ' :: '
			)
		);
		
		$this->_messages();
		
		$curr_ver = $this->config->get('msmart_search_version');
		
		// install/update
		if( ! $curr_ver || version_compare( $curr_ver, $this->_version, '<' ) ) {			
			$this->load->model('setting/setting');
			$this->load->model('setting/store');
			
			$stores = array(0);
			
			foreach( $this->model_setting_store->getStores() as $row ) {
				$stores[] = $row['store_id'];
			}
			
			foreach( $stores as $store_id ) {
				$this->model_setting_setting->editSetting('msmart_search_version', array(
					'msmart_search_version' => $this->_version
				), $store_id);
			}
			
			if( $curr_ver ) {
				$this->load->model('extension/module/msmart_search');

				$this->model_extension_module_msmart_search->update( false, $curr_ver );
				
				$this->updateCssFile();
				$this->updateJsFile();
				
				$this->session->data['success'] = $this->language->get('success_updated');
			
				$this->response->redirect($this->url->link('extension/module/' . $this->_name . '/about', 'user_token=' . $this->session->data['user_token'] . '&refresh_ocmod_cache=1', true));
			}
		}
		
		// title
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput( $this->load->view('extension/module/' . $this->_name . '/' . $view, $this->data) );
	}
	
	/**
	 * Recommended products
	 */
	public function recommended_products() {
		if( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->checkPermission() ) {

			$this->model_extension_module_msmart_search->saveSettings($this->_name . '_recommended', $this->request->post['data']);
			
			$this->session->data['success'] = $this->language->get('text_success');		
			$this->response->redirect($this->url->link('extension/module/' . $this->_name . '/recommended_products', 'user_token=' . $this->session->data['user_token'], true));
		}
		
		$this->data['data'] = (array) $this->config->get( $this->_name . '_recommended' );

		$this->load->model('catalog/product');
			
		if(!empty($this->data['data']['recommended_products'])){
			foreach ($this->data['data']['recommended_products'] as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					$this->data['data']['products'][] = array(
						'product_id' => $product_info['product_id'],
						'name'       => $product_info['name']
					);
				}
			}
		}
				
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->data['user_token'] = $this->session->data['user_token'];
		$this->data['action_save'] = $this->url->link('extension/module/' . $this->_name . '/recommended_products', 'user_token=' . $this->session->data['user_token'], true);
		
		$this->render( 'recommended_products' );
	}

	/**
	 * Config
	 */
	public function index() {
		if( $this->request->get['route'] == 'extension/extension/module/install' ) {
			$this->install();
			
			return;
		} else if( $this->request->get['route'] == 'extension/extension/module/uninstall' ) {
			$this->uninstall();
			
			return;
		}
		
		if( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->checkPermission() ) {
			$this->model_extension_module_msmart_search->saveSettings($this->_name, $this->request->post[$this->_name]);
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/' . $this->_name, 'user_token=' . $this->session->data['user_token'], true));
		}
		
		$this->data['action_save'] = $this->url->link('extension/module/' . $this->_name, 'user_token=' . $this->session->data['user_token'], true);
				
		/* @var $settings array */
		$settings = (array) $this->config->get( $this->_name );
		
		if( ! isset( $settings['fields_categories'] ) ) {
			$settings['fields_categories'] = array( 'name' => array( 'sort_order' => 1 ) );
		}
		
		$this->data['settings'] = $settings;
		$this->data['groups'] = array(
			'products' => array(
				'general' => array(
					'fields' => array(
						'name', 'description', 'manufacturer', 
					),
				),
				'options' => array(
					'fields' => array(
						'option_value', 'option_name',
					),
				),
				'attributes' => array(
					'fields' => array(
						'attribute_value', 'attribute_name', 'attribute_group', 
					),
				),
				'data' => array(
					'fields' => array(
						'model', 'sku', 'upc', 'ean', 'jan', 'isbn', 'mpn', 'location',
					),
				),
				'meta' => array(
					'fields' => array(
						'meta_title', 'meta_description', 'meta_keyword', 'tag',
					),
				),
			),
			'categories' => array(
				'general' => array(
					'fields' => array(
						'name', 'description',
					),
				),
				'meta' => array(
					'fields' => array(
						'meta_title', 'meta_description', 'meta_keyword',
					),
				)
			)
		);
		
		/* @var $extra_fields_keys array */
		$extra_fields_keys = array(
			'product' => 'products',
			'category' => 'categories',
		);
		
		/* @var $extra_field array */
		foreach( $this->model_extension_module_msmart_search->getExtraFields(array()) as $extra_field ) {
			/* @var $key string */
			$key = $extra_fields_keys[$extra_field['type']];
			
			if( ! isset( $this->data['groups'][$key]['extra_fields'] ) ) {
				$this->data['groups'][$key]['extra_fields'] = array(
					'fields' => array(),
				);
			}
			
			$this->data['groups'][$key]['extra_fields']['fields'][] = array(
				'name' => 'extra_field_' . $extra_field['id'],
				'label' => '`' . $extra_field['config']['condition']['table'] . '`.`' . $extra_field['config']['condition']['column'] . '`',
			);
		}
		
		foreach( $this->data['groups'] as $type => $items ) {
			foreach( $items as $group => $items2 ) {
				if( ! isset( $this->data['groups'][$type][$group]['thead'] ) ) {
					$this->data['groups'][$type][$group]['thead'] = '';
				}
				
				if( ! isset( $this->data['groups'][$type][$group]['tbody'] ) ) {
					$this->data['groups'][$type][$group]['tbody'] = '';
				}
				
				$this->data['groups'][$type][$group]['thead'] .= '<tr style="color: #000; background: #dddddd; border-color: #000;">';
				
				for( $i = 0; $i < min(array( count( $items2['fields'] ), 2 )); $i++ ) {
					$this->data['groups'][$type][$group]['thead'] .= sprintf( '<td width="20%%" style="border: 1px solid #a1a1a1;">%s</td>', $this->language->get('text_field') );
					$this->data['groups'][$type][$group]['thead'] .= sprintf( '<td width="5%%" style="border: 1px solid #a1a1a1;" class="text-center">%s</td>', $this->language->get('text_enabled') );
					$this->data['groups'][$type][$group]['thead'] .= sprintf( '<td width="5%%" style="border: 1px solid #a1a1a1;" class="text-center">%s</td>', $this->language->get('text_sort_order') );
				}
				
				$this->data['groups'][$type][$group]['thead'] .= '</tr>';				
				
				for( $i = 0; $i < count( $items2['fields'] ); $i+=min(array( count( $items2['fields'] ), 2 )) ) {
					$this->data['groups'][$type][$group]['tbody'] .= '<tr>';
					
					for( $j = $i; $j < $i+min(array( count( $items2['fields'] ), 2 )) && $j < count( $items2['fields'] ); $j++ ) {
						/* @var $field mixed */
						$field = $items2['fields'][$j];
						
						if( ! is_array( $field ) ) {
							$field = array(
								'name' => $field,
								'label' => $this->language->get('text_field_'.$field)
							);
						}
						
						$this->data['groups'][$type][$group]['tbody'] .= sprintf( '<td style="vertical-align: middle">%s</td>', $field['label'] );
						
						$this->data['groups'][$type][$group]['tbody'] .= sprintf( 
							'<td class="text-center" style="vertical-align: middle" data-toggle="tooltip" data-html="1" title="%s"><i id="status-%s-%s" class="fa fa-%s"></i></td>',
							$this->language->get( 'text_enable_disable_guide' ),
							$type,
							$field['name'],
							isset( $settings['fields'.($type=='products'?'':'_'.$type)][$field['name']]['sort_order'] ) && $settings['fields'.($type=='products'?'':'_'.$type)][$field['name']]['sort_order'] !== '' ? 'check' : 'remove'
						);
						
						$this->data['groups'][$type][$group]['tbody'] .= sprintf( 
							'<td><input type="text" class="form-control" data-field="%s" data-type="%s" name="%s][%s][sort_order]" value="%s" /></td>', 
							$field['name'], 
							$type,
							$this->_name . '[fields' . ( $type=='products'?'':'_'.$type ),
							$field['name'],
							isset( $settings['fields'.($type=='products'?'':'_'.$type)][$field['name']]['sort_order'] ) ? $settings['fields'.($type=='products'?'':'_'.$type)][$field['name']]['sort_order'] : ''
						);
					}
					
					$this->data['groups'][$type][$group]['tbody'] .= '</tr>';
				}
			}
		}
		
		$this->render( 'index' );
	}
	
	private function jsFile() {
		return DIR_SYSTEM . '../catalog/view/javascript/mss/js_params.js';
	}
	
	private function jsFilePermissionsToSave() {
		return file_exists( $this->jsFile() ) && is_writable( $this->jsFile() );
	}
	
	private function updateJsFile() {
		if( ! $this->jsFilePermissionsToSave() ) return;
		
		/* @var $config array */
		$config = (array) $this->config->get( $this->_name . '_lf' );
		
		foreach( array( 'custom_css' ) as $key ) {
			if( isset( $config[$key] ) ) {
				unset( $config[$key] );
			}
		}
		
		/* @var $settings array */
		$settings = (array) $this->config->get( $this->_name . '_s' );
		
		if( ! empty( $settings['history_enabled'] ) ) {
			$config['history_enabled'] = '1';
		}
		
		$js = 'var msmartSearchParams = ' . json_encode(array(
			'lf' => $config
		)).';';
		
		file_put_contents( $this->jsFile(), $js );
	}
	
	private function cssFile() {
		return DIR_SYSTEM . '../catalog/view/theme/default/stylesheet/mss/style-2.css';
	}
	
	private function cssFilePermissionsToSave() {
		return file_exists( $this->cssFile() ) && is_writable( $this->cssFile() );
	}
	
	private function updateCssFile() {
		if( ! $this->cssFilePermissionsToSave() ) return;
		
		/* @var $config array */
		$config = (array) $this->config->get( $this->_name . '_lf' );
		
		$custom_colors = isset( $config['custom_color_css'] ) ? (array) $config['custom_color_css'] : array();
		
		$css = $this->_createCss( $custom_colors);
		
		$css .= $css ? "\n" : '';
		
		/* @var $css string */
		$css .= isset( $config['custom_css'] ) ? $config['custom_css'] : '';
		
		file_put_contents( $this->cssFile(), $css );
	}
	
	protected function _createCss( $custom_colors){
		$css = array();
		
		if( !empty($custom_colors['product_header'])){
			$css[ '.msmart-search-live-filter .tt-menu .mss-header-products'][] = 'background: #' . trim( $custom_colors['product_header'], '#' ) . ' !important';
		}
		
		if( !empty($custom_colors['categories_header'])){
			$css[ '.msmart-search-live-filter .tt-menu .mss-header-categories'][] = 'background: #' . trim( $custom_colors['categories_header'], '#' ) . ' !important';
		}
		
		if( !empty($custom_colors['header_text'])){
			$css[ '.msmart-search-live-filter .tt-menu .mss-header-products, .mss-header-categories, .mss-button-more'][] = 'color: #' . trim( $custom_colors['header_text'], '#' ) . ' !important';
		}
		
		if( !empty($custom_colors['results_background'])){
			$css[ '.msmart-search-live-filter .tt-menu .tt-suggestion, .msmart-search-live-filter .tt-menu .mslf-product-list a'][] = 'background: #' . trim( $custom_colors['results_background'], '#' ) . ' !important';
		}
		
		if( !empty($custom_colors['results_text'])){
			$css[ '.msmart-search-live-filter .tt-menu .tt-suggestion, .tt-suggestion small, .msmart-search-live-filter .tt-menu .mslf-product-list a'][] = 'color: #' . trim( $custom_colors['results_text'], '#' ) . ' !important';
		}
		
		if( !empty($custom_colors['price_results'])){
			$css[ '.msmart-search-live-filter .tt-menu .mslf-price, .msmart-search-live-filter .tt-menu .mslf-product-list a .product-price'][] = 'color: #' . trim( $custom_colors['price_results'], '#' ) . ' !important';
		}
		
		if( !empty($custom_colors['border_results'])){
			$css[ '.msmart-search-live-filter .tt-menu .tt-suggestion:first-child'][] = 'border-top: none !important';
			$css[ '.msmart-search-live-filter .tt-suggestion'][] = 'border-top: 1px solid #' . trim( $custom_colors['border_results'], '#' ) . ' !important';
			$css[ '.msmart-search-live-filter .tt-menu .tt-dataset-products tr td:nth-child(2), .msmart-search-live-filter .tt-menu .tt-dataset-categories tr td:nth-child(2)'][] = 'border-left: 1px solid #' . trim( $custom_colors['border_results'], '#' ) . ' !important';
		
			$css[] = '
			.msmart-search-live-filter .mslf-product-list a {
				border-top: 1px solid #' . trim( $custom_colors['border_results'], '#' ) . ' !important;
				border-right: 1px solid #' . trim( $custom_colors['border_results'], '#' ) . ' !important;
				border-bottom: 1px solid #' . trim( $custom_colors['border_results'], '#' ) . ' !important;
			}
		';
		}
		
		if( !empty($custom_colors['view_all'])){
			$css[ '.msmart-search-live-filter .tt-menu .mss-button-more'][] = 'background: #' . trim( $custom_colors['view_all'], '#' ) . ' !important';
		}
		
		if( !empty($custom_colors['results_hover'])){
			$css[] = '
				.msmart-search-live-filter .tt-menu .tt-suggestion:hover, .msmart-search-live-filter .tt-menu .mslf-product-list a:hover {
					-moz-box-shadow:    inset 0 0 15px #' . trim( $custom_colors['results_hover'], '#' ) . ' !important;
					-webkit-box-shadow: inset 0 0 15px #' . trim( $custom_colors['results_hover'], '#' ) . ' !important;
					box-shadow:         inset 0 0 15px #' . trim( $custom_colors['results_hover'], '#' ) . ' !important;
				}
			';
		}
		
		/* @var $code string */
		$code = '';
		
		foreach( $css as $key => $val ) {
			if( is_array( $val ) ) {
				$code .= $code ? "\n" : '';
				$code .= $key . " {\n";
				foreach( $val as $val2 ) {
					$code .= "\t" . $val2 . ";\n";
				}
				$code .= "\n}";
			} else {
				$code .= $code ? "\n" : '';
				$code .= $val;
			}
		}
		
		return $code;
	}
	
	/**
	 * Extra fields
	 */
	public function extra_fields() {		
		/* @var $page int */
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		/* @var $limit int */
		$limit = $this->config->get('config_limit_admin');
		
		/* @var $extra_fields array */
		$extra_fields = array();
		
		foreach( $this->model_extension_module_msmart_search->getExtraFields(array(
				'start' => ($page - 1) * $limit, 
				'limit' => $limit 
			)) as $extra_field 
		) {
			$extra_fields[] = array_replace( $extra_field, array(
				'edit_url' => $this->url->link('extension/module/' . $this->_name . '/add_extra_field', 'user_token=' . $this->session->data['user_token'] . '&extra_field_id=' . $extra_field['id'], true),
				'delete_url' => $this->url->link('extension/module/' . $this->_name . '/delete_extra_field', 'user_token=' . $this->session->data['user_token'] . '&extra_field_id=' . $extra_field['id'], true),
			));
		}
				
		$this->data['add_extra_field_url'] = $this->url->link('extension/module/' . $this->_name . '/add_extra_field', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['extra_fields'] = $extra_fields;
		
		$this->pagination($this->model_extension_module_msmart_search->getTotalReplacedPhrases(), 'extension/module/' . $this->_name . '/replace_phrase')
			->render( 'extra_fields' );
	}
	
	public function delete_extra_field() {		
		/* @var $extra_field_id int */
		if( null != ( $extra_field_id = empty( $this->request->get['extra_field_id'] ) ? '' : $this->request->get['extra_field_id'] ) ) {
			$this->model_extension_module_msmart_search->deleteExtraField( $extra_field_id );
					
			$this->session->data['success'] = $this->language->get('text_success');
		}
		
		$this->response->redirect($this->url->link('extension/module/' . $this->_name . '/extra_fields', 'user_token=' . $this->session->data['user_token'], true));
	}
	
	public function add_extra_field() {		
		/* @var $extra_field array */
		$extra_field = array();
		
		/* @var $extra_field_id int */
		if( null != ( $extra_field_id = empty( $this->request->get['extra_field_id'] ) ? '' : $this->request->get['extra_field_id'] ) ) {
			/* @var $record array */
			if( null != ( $record = $this->model_extension_module_msmart_search->getExtraField( $extra_field_id ) ) ) {
				$this->data['type'] = $record['type'];
				
				if( ! empty( $record['config']['joins'] ) ) {
					foreach( $record['config']['joins'] as $join ) {
						$extra_field[] = array(
							'action' => 'join',
							'join_table' => $join['table'],
							'join_column' => $join['column'],
							'on_table' => $join['on_table'],
							'on_column' => $join['on_column'],
							'condition_table' => '',
							'condition_column' => '',
							'condition_type' => '',
						);
					}
				}
				
				$extra_field[] = array(
					'action' => 'condition',
					'join_table' => '',
					'join_column' => '',
					'on_table' => '',
					'on_column' => '',
					'condition_table' => $record['config']['condition']['table'],
					'condition_column' => $record['config']['condition']['column'],
					'condition_type' => $record['config']['condition']['type'],
				);
			}
		}		
		
		if( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->checkPermission() ) {
			if( null != ( $extra_field = empty( $this->request->post['extra_field'] ) ? array() : $this->request->post['extra_field'] ) ) {				
				/* @var $config array */
				$config = array();
				
				/* @var $type string */
				$type = isset( $this->request->post['type'] ) ? $this->request->post['type'] : null;
				
				/* @var $level array */
				foreach( $extra_field as $level ) {
					if( $level['action'] == 'join' ) {
						if( ! empty( $level['join_table'] ) && ! empty( $level['join_column'] ) && ! empty( $level['on_table'] ) && ! empty( $level['on_column'] ) ) {
							$config['joins'][] = array(
								'table' => $level['join_table'],
								'table_lc' => $this->_has_language_column( $level['join_table'] ),
								'column' => $level['join_column'],
								'on_table' => $level['on_table'],
								'on_table_lc' => $this->_has_language_column( $level['on_table'] ),
								'on_column' => $level['on_column'],
							);
						} else {
							$this->data['_error_warning'] = $this->language->get('error_please_fill_all_fields');
							
							break;
						}
					} else if( $level['action'] == 'condition' ) {
						if( ! empty( $level['condition_table'] ) && ! empty( $level['condition_column'] ) && ! empty( $level['condition_type'] ) ) {
							$config['condition'] = array(
								'table' => $level['condition_table'],
								'table_lc' => $this->_has_language_column( $level['condition_table'] ),
								'column' => $level['condition_column'],
								'type' => $level['condition_type'],
							);
						}
					}
				}
				
				if( empty( $config['condition'] ) ) {
					$this->data['_error_warning'] = $this->language->get('error_please_add_condition');
				} else if( empty( $type ) ) {
					$this->data['_error_warning'] = $this->language->get('error_please_select_type');
				} else if( empty( $this->data['_error_warning'] ) ) {
					if( $extra_field_id ) {
						$this->model_extension_module_msmart_search->updateExtraField( $extra_field_id, $type, $config );
					} else {
						$this->model_extension_module_msmart_search->addExtraField( $type, $config );
					}
					
					$this->session->data['success'] = $this->language->get('text_success');

					$this->response->redirect($this->url->link('extension/module/' . $this->_name . '/extra_fields', 'user_token=' . $this->session->data['user_token'], true));
				}
			}
		}
		
		$this->data['extra_field'] = json_encode( $extra_field );
		$this->data['tab_active'] = 'extra_fields';
		$this->data['db_tables'] = $this->get_tables();
		$this->data['get_columns_url'] = $this->url->link('extension/module/' . $this->_name . '/get_table_columns', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['action_save'] = $this->url->link('extension/module/' . $this->_name . '/add_extra_field', 'user_token=' . $this->session->data['user_token'] . ( $extra_field_id ? '&extra_field_id='.$extra_field_id : '' ), true);
		$this->data['extra_field_id'] = $extra_field_id;
		
		$this->render( 'add_extra_field' );
	}
	
	private function _has_language_column( $table ) {
		return in_array( 'language_id', $this->_get_table_columns( $table ) );
	}
	
	private function _get_tables() {
		if( isset( $this->cache[__METHOD__] ) ) {
			return $this->cache[__METHOD__];
		}
		
		foreach( $this->model_extension_module_msmart_search->getDbTables() as $row ) {
			$this->cache[__METHOD__][] = preg_replace( '/^' . preg_quote( DB_PREFIX, '/' ) . '/', '', current( $row ) );
		}
		
		return $this->cache[__METHOD__];
	}
	
	private function get_tables() {		
		/* @var $tables array */
		$tables = array();
		
		if( $this->hasPermission() ) {
			$tables = $this->_get_tables();
		} else {
			$tables[] = $this->language->get('error_permission');
		}
		
		return $tables;
	}
	
	private function _get_table_columns( $table ) {
		/* @var $columns array */
		$columns = array();
		
		/* @var $tables array */
		$tables = $this->_get_tables();
		
		if( in_array( $table, $tables ) ) {
			foreach( $this->model_extension_module_msmart_search->getDbTableColumns( $table ) as $row ) {
				$columns[] = $row['Field'];
			}
		}
		
		return $columns;			
	}
	
	public function get_table_columns() {
		/* @var $columns array */
		$columns = array();
		
		if( $this->hasPermission() ) {
			if( ! empty( $this->request->post['table'] ) ) {
				$columns = $this->_get_table_columns( $this->request->post['table'] );
			}
		} else {
			$columns[] = $this->language->get('error_permission');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($columns));
	}
	
	/**
	 * Replace phrase
	 */
	public function replace_phrase() {		
		/* @var $page int */
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		/* @var $limit int */
		$limit = $this->config->get('config_limit_admin');
				
		$this->data['phrase_url'] = $this->url->link('extension/module/' . $this->_name . '/replace_phrase_action', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['phrases'] = $this->model_extension_module_msmart_search->getReplacedPhrases( ($page - 1) * $limit, $limit );
		
		$this->pagination($this->model_extension_module_msmart_search->getTotalReplacedPhrases(), 'extension/module/' . $this->_name . '/replace_phrase')
			->render( 'replace_phrase' );
	}
	
	public function replace_phrase_action() {		
		/* @var $json array */
		$json = array();
		
		if( ! empty( $this->request->get['action'] ) && $this->checkPermission() ) {
			switch( $this->request->get['action'] ) {
				case 'insert' : 
					if( 
						NULL != ( $phrase = isset( $this->request->post['phrase'] ) ? $this->request->post['phrase'] : '' ) &&
						NULL != ( $alias = isset( $this->request->post['alias'] ) ? $this->request->post['alias'] : '' ) 
					) {
						$phrase = trim( $phrase );
						$alias = trim( $alias );
						$regex = empty( $this->request->post['regex'] ) ? 0 : 1;
						
						if( $this->model_extension_module_msmart_search->getReplacedPhrase(array( 'search' => $phrase )) ) {
							$json['error'] = $this->language->get('error_phrase_exists');
						} else {
							if( $regex ) {
								if( @ preg_match( $phrase, null ) === false ) {
									$json['error'] = $this->language->get('error_phrase_regex');
								}
							}
							
							if( empty( $json['error'] ) ) {
								$json = array(
									'action' => 'add',
									'phrase' => $phrase,
									'alias' => $alias,
									'regex' => $regex,
									'id' => $this->model_extension_module_msmart_search->addReplacedPhrase(array(
										'search' => $phrase,
										'replaced' => $alias,
										'regex' => $regex
									))
								);
							}
						}
					}
					break;
				case 'remove' : 
					if( ! empty( $this->request->post['id'] ) ) {
						$this->model_extension_module_msmart_search->deleteReplacedPhrase( $this->request->post['id'] );

						$json = array(
							'action' => 'delete',
							'id' => $this->request->post['id']
						);
					}
					break;
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Live filter
	 */
	public function live_filter() {
		if (file_exists('view/stylesheet/mss/css/colorpicker.css')) {
			$this->document->addStyle('view/stylesheet/mss/css/colorpicker.css');
		}
		if (file_exists('view/javascript/mss/colorpicker.js')) {
			$this->document->addScript('view/javascript/mss/colorpicker.js');
		}
		
		/* @var $errors array */
		$errors = array();
		
		if( ! $this->jsFilePermissionsToSave() ) {
			$errors[] = $this->language->get( 'error_js_file' );
		} else if( ! $this->cssFilePermissionsToSave() ) {
			$errors[] = $this->language->get( 'error_css_file' );
		}
		
		if( $errors ) {
			$this->_setErrors(array(
				'warning' => implode( '<br /><br />', $errors )
			));
		}
		
		if( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->checkPermission() ) {
			$this->model_extension_module_msmart_search->saveSettings($this->_name . '_lf', $this->request->post[$this->_name]);
			$this->model_extension_module_msmart_search->saveSettings($this->_name . '_lf_enabled', empty($this->request->post[$this->_name]['enabled'])?'0':'1');
			
			$this->config->set( $this->_name . '_lf', $this->request->post[$this->_name] );
			
			$this->updateJsFile();
			$this->updateCssFile();
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/' . $this->_name . '/live_filter', 'user_token=' . $this->session->data['user_token'], true));
		}
		
		$this->data['action_save'] = $this->url->link('extension/module/' . $this->_name . '/live_filter', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['settings'] = (array) $this->config->get( $this->_name . '_lf' );
		
		$this->render( 'live_filter' );
	}

	/**
	 * Config
	 */
	public function settings() {		
		/* @var $errors array */
		$errors = array();
		
		if( ! $this->jsFilePermissionsToSave() ) {
			$errors[] = $this->language->get( 'error_js_file' );
		} else if( ! $this->cssFilePermissionsToSave() ) {
			$errors[] = $this->language->get( 'error_css_file' );
		}
		
		if( $errors ) {
			$this->_setErrors(array(
				'warning' => implode( '<br /><br />', $errors )
			));
		}
		
		if( $this->request->server['REQUEST_METHOD'] == 'POST' && $this->checkPermission() ) {
			if( empty( $this->request->post[$this->_name]['required_number_of_results'] ) ) {
				$this->request->post[$this->_name]['required_number_of_results'] = 1;
			}
			
			$this->request->post[$this->_name]['required_number_of_results'] = (int) $this->request->post[$this->_name]['required_number_of_results'];
			
			if( $this->request->post[$this->_name]['required_number_of_results'] < 1 ) {
				$this->request->post[$this->_name]['required_number_of_results'] = 1;
			}
			
			$this->model_extension_module_msmart_search->saveSettings($this->_name . '_s', $this->request->post[$this->_name]);
			$this->model_extension_module_msmart_search->saveSettings($this->_name . '_enabled', empty($this->request->post[$this->_name]['enabled'])?'0':'1');
			
			$this->config->set( $this->_name . '_s', $this->request->post[$this->_name] );
			
			$this->updateJsFile();
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/' . $this->_name . '/settings', 'user_token=' . $this->session->data['user_token'], true));
		}
		
		$this->data['action_save'] = $this->url->link('extension/module/' . $this->_name . '/settings', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['settings'] = (array) $this->config->get( $this->_name . '_s' );
		
		$this->render( 'settings' );
	}
	
	/**
	 * Search history
	 */
	public function search_history() {		
		/* @var $config array */
		$config = $this->config->get($this->_name . '_s');
		
		if( empty( $config['history_enabled'] ) ) {
			$this->data['_error_warning2'] = $this->language->get('text_search_history_is_disabled');
		}
		
		/* @var $page int */
		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
		
		/* @var $filter_data array */
		$filter_data = array(
			'limit' => $this->config->get('config_limit_admin'),
			'start' => ( $page - 1 ) * $this->config->get('config_limit_admin'),
		);
		
		/* @var $url string */
		$url = '';
		
		if( $this->request->server['REQUEST_METHOD'] == 'POST' || $this->request->server['REQUEST_METHOD'] == 'GET' && $this->checkPermission() ) {
			if( ! empty( $this->request->post['list_id'] ) ) {
				$phrase_to_delete = $this->request->post['list_id'];
				
				foreach( $phrase_to_delete as $id ) {
					$this->model_extension_module_msmart_search->deleteSearchHistory( $id );
				}
			}
			
			if( ! empty( $this->request->get['action'] ) ) {
				$url .= '&action=search';

				if( null != ( $phrase = isset( $this->request->get['phrase'] ) ? $this->request->get['phrase'] : null ) ) {
					$filter_data['phrase'] = $phrase;
					$url .= '&phrase='.$phrase;
					$this->data['phrase'] = $phrase;
				}

				if( null != ( $e_mail = isset( $this->request->get['e_mail'] ) ? $this->request->get['e_mail'] : null )) {
					$filter_data['email'] = $e_mail;

					$url .= '&e_mail='.$e_mail;					
					$this->data['phrase'] = $e_mail;
				}

				if( 
					null != ( $date_start = isset( $this->request->get['date_start'] ) ? $this->request->get['date_start'] : '' ) &&
					null != ( $date_end = isset( $this->request->get['date_end'] ) ? $this->request->get['date_end'] : '' )
				) {
					$filter_data['date_start'] = $date_start;
					$filter_data['date_end'] = $date_end;

					$url .= '&date_start=' . $date_start . '&date_end=' . $date_end;
					$this->data['date_start'] = $date_start;
					$this->data['date_end'] = $date_end;
				}

				$this->data['reset_results'] = 0;	
			}
		}
		
		$this->data['top_20'] = $this->model_extension_module_msmart_search->getTopSearchHistory( 20 );
		$this->data['history'] = $this->model_extension_module_msmart_search->getSearchHistory( $filter_data );
		
		$this->data['action_del_history'] = $this->url->link('extension/module/' . $this->_name . '/clear_all_history', 'user_token=' . $this->session->data['user_token'], true);
		$this->data['action_save'] = $this->url->link('extension/module/' . $this->_name . '/search_history', 'user_token=' . $this->session->data['user_token'], true);
		
		$this->pagination( $this->model_extension_module_msmart_search->getTotalSearchHistory( $filter_data ), 'extension/module/' . $this->_name . '/search_history', $url )
			->render( 'search_history' );
	}
	
	private function pagination( $total, $route, $url = '' ) {
		/* @var $limit int */
		$limit = $this->config->get('config_limit_admin');
		
		/* @var $page int */
		$page = isset( $this->request->get['page'] ) ? $this->request->get['page'] : 1;
		
		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link( $route, 'user_token=' . $this->session->data['user_token']. '&page={page}' . $url, true);
		
		$this->data['pagination'] = $pagination->render();
		$this->data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $limit) + 1 : 0, ((($limit - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit), $total, ceil($total / $limit));
		
		return $this;
	}
	
	public function clear_all_history(){
		$this->db->query("TRUNCATE " . DB_PREFIX . "msmart_search_history" );
		$this->response->redirect($this->url->link('extension/module/' . $this->_name . '/search_history', 'user_token=' . $this->session->data['user_token'], true));
	}
	
	/**
	 * About
	 */
	public function about() {		
		$this->data['ext_version'] = $this->_version;
		
		$this->render( 'about' );
	}
	
	/**
	 * Autocomplete
	 */
	public function autocomplete() {
		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($this->model_extension_module_msmart_search->autocomplete($this->request->post)));
	}
	
	////////////////////////////////////////////////////////////////////////////
	
	/**
	 * Check permissions
	 * 
	 * @return boolean
	 */
	protected function hasPermission() {
		if( ! $this->user->hasPermission('modify', 'extension/module/' . $this->_name) )
			return false;
		
		return true;
	}
	
	/**
	 * @return boolean
	 */
	protected function checkPermission() {
		if( ! $this->hasPermission() ) {
			$this->_setErrors(array(
				'warning'	=> $this->language->get( 'error_permission' )
			));
			
			return false;
		}
		
		return true;
	}
	
	/**
	 * Set errors
	 * 
	 * @param array $errors
	 */
	private function _setErrors( $errors ) {
		foreach( $errors as $name => $default ) {
			$this->data['_error_' . $name] = str_replace( '{HTTP_URL}', $this->httpUrl(), isset( $this->error[$name] ) ? $this->error[$name] : $default );
		}
	}
	
	private function _emptyRedirect( $url ) {
		if( ! empty( $this->data['_success'] ) ) {
			$this->session->data['success'] = $this->data['_success'];
		}
		
		if( ! empty( $this->data['_error_warning'] ) ) {
			$this->session->data['error'] = $this->data['_error_warning'];
		}
		
		$this->response->redirect( $url );
	}
	
	private function _messages() {		
		// notifications
		if( isset( $this->session->data['success'] ) ) {
			$this->data['_success'] = $this->session->data['success'];
			
			unset( $this->session->data['success'] );
		}
		
		if( isset( $this->session->data['error'] ) ) {
			$this->_setErrors(array(
				'warning' => $this->session->data['error']
			));
			
			unset( $this->session->data['error'] );
		}
	}
	
	/**
	 * Installation
	 */
	public function install() {		
		$this->language->load('extension/module/' . $this->_name);
		
		// load models
		$this->load->model('setting/extension');
		$this->load->model('extension/module/msmart_search');
		$this->load->model('user/user_group');
		
		$this->model_extension_module_msmart_search->install();
		
		/**
		 * Check if the extension is on the list
		 */
		if( ! in_array( $this->_name, $this->model_setting_extension->getInstalled('module') ) ) {
			$this->model_extension_extension->install('module', $this->_name);
		}
		
		/**
		 * Check if is duplicate
		 */
		$idx = 0;
		foreach( $this->db->query( "SELECT * FROM " . DB_PREFIX . "extension WHERE code='" . $this->_name . "' AND type='module'")->rows as $row ) {
			if( $idx ) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "extension WHERE extension_id='" . (int) $row['extension_id'] . "'");
			}
			
			$idx++;
		}

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/' . $this->_name);
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/' . $this->_name);

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/' . $this->_name);
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/' . $this->_name);
		
		$this->model_setting_setting->editSetting($this->_name . '_installed', array(
			$this->_name . '_installed' => '1'
		));
		
		$this->model_setting_setting->editSetting('module_msmart_search', array(
			'module_msmart_search_status' => '1'
		));
		
		$this->session->data['success'] = $this->language->get('success_install');
	}
	
	/**
	 * Uninstall
	 */
	public function uninstall() {
		$this->language->load('extension/module/' . $this->_name);	
			
		/**
		 * Check if extension is on the list
		 */
		$this->load->model('setting/extension');
		$this->load->model('extension/module/msmart_search');
		$this->load->model('setting/setting');
		
		$this->model_extension_module_msmart_search->uninstall();
			
		if( in_array( $this->_name, $this->model_setting_extension->getInstalled('module') ) ) {
			$this->model_extension_extension->uninstall('module', $this->_name);
		}
		
		if( empty( $this->request->get['error'] ) ) {
			$this->session->data['success'] = $this->language->get('success_uninstall');
		}
		
		$this->model_setting_setting->deleteSetting('module_msmart_search');
		
		$this->model_setting_setting->deleteSetting($this->_name . '_installed');
	}
}