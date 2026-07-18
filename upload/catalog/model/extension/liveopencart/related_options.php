<?php

//  Related Options / Связанные опции
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ModelExtensionLiveopencartRelatedOptions extends Model {

	public $cache_sets_by_poids                = [];
	public $cache_ro_data                      = [];
	private $liveprice_settings                = false;
	private $module_installed_status           = null;
	private $module_installed_status_liveprice = null;
	
	public function __construct() {
		call_user_func_array( ['parent', '__construct'], func_get_args());
		
		\liveopencart\ext\ro::getInstance($this->registry);
	}
	
	public function getThemeName($override_theme_name = '') {
		
		if ( $override_theme_name ) {
			$this->theme_name = $override_theme_name;
		}
		
		if (!$this->theme_name) {
			$theme_name = '';
			
			$ro_settings = $this->config->get('related_options');
			
			if ( !empty($ro_settings['custom_theme_id']) ) {
				$theme_name = $ro_settings['custom_theme_id'];
			} else {
			
				if ($this->config->get('config_theme') == 'theme_default' || $this->config->get('config_theme') == 'default') {
					$theme_name = $this->config->get('theme_default_directory');
				} else {
					$theme_name = substr($this->config->get('config_theme'), 0, 6) == 'theme_' ? substr($this->config->get('config_theme'), 6) : $this->config->get('config_theme') ;
				}
				
				// shorten theme name
				$themes_shorten = $this->getAdaptedThemes();
				foreach ( $themes_shorten as $theme_shorten ) {
					$theme_shorten_length = strlen($theme_shorten);
					if ( substr($theme_name, 0, $theme_shorten_length) == $theme_shorten ) {
						$theme_name = substr($theme_name, 0, $theme_shorten_length);
						break;
					}
				}
			}
			$this->theme_name = $theme_name;
		}
		return $this->theme_name;
	}
	
	protected function getAdaptedThemes() {
		
		$dir_of_themes = $this->getBasicDirOfTemplates();
		
		$themes = glob($dir_of_themes . '*' , GLOB_ONLYDIR);
		if ( $themes ) {
			$themes = array_map( 'basename', $themes );
			
			if ( ($default_key = array_search('default', $themes)) !== false ) {
				unset($themes[$default_key]);
			}
			
			usort($themes, function($a, $b) {
				return strlen($b) - strlen($a);
			});
			return $themes;
		} else {
			return [];
		}
		
	}
	
	public function getBasicDirOfExtension() {
		return DIR_TEMPLATE.'extension_liveopencart/related_options/';
	}

	public function getBasicDirOfTemplates() {
		return $this->getBasicDirOfExtension().'themes/';
	}
	
	public function getBasicScripts() {
		
		$scripts = [];
		
		$scripts[] = $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/liveopencart.select_option_toggle.js');
		$scripts[] = $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/liveopencart.ro_common.js');
		$scripts[] = $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/liveopencart.related_options.js');
		$scripts[] = $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/liveopencart.ro_init.js');
		
		return $scripts;
	}
	
	public function getScriptCommon() {
		return $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/product_page_common.js');
	}

	public function getScriptProductPage() {
		return $this->getScriptPathWithVersion('view/theme/extension_liveopencart/related_options/js/product_page_with_related_options.js');
	}

	public function getScriptProductPageTheme() {
		$script_path = 'view/theme/extension_liveopencart/related_options/themes/'.$this->getThemeName().'/code.js';
		if ( file_exists(DIR_APPLICATION.$script_path) ) {
			return $this->getScriptPathWithVersion($script_path);
		}
		$script_path = 'view/theme/extension_liveopencart/related_options/themes/'.strtolower($this->getThemeName()).'/code.js';
		if ( file_exists(DIR_APPLICATION.$script_path) ) {
			return $this->getScriptPathWithVersion($script_path);
		}
	}
	
	public function getScriptPathWithVersion($path) {
		$basic_dir = 'catalog/';
		return $basic_dir.$path.'?v='.filemtime(DIR_APPLICATION.$path);
	}
	
	public function clearCaches() {
		$this->cache_sets_by_poids = [];
		$this->cache_ro_data       = [];
	}

	public function getCacheROData() {
		return $this->cache_ro_data;
	}
	
	public function getProductControllerData($data, $return_all_scripts = false, $basic_data = []) {
		$this->load->language('extension/liveopencart/related_options');
		
		$data['ro_installed'] = $this->installed();
		
		if ( $data['ro_installed'] ) {
		
			if ( isset($data['ro_product_id']) ) {
				$ro_product_id = $data['ro_product_id'];
			} elseif ( isset($data['product_id']) ) {
				$ro_product_id = $data['product_id'];
			} elseif ( isset($this->request->get['pid']) ) {
				$ro_product_id = $this->request->get['pid'];
			} elseif ( isset($this->request->get['product_id']) ) {
				$ro_product_id = $this->request->get['product_id'];
			} elseif ( isset($this->request->post['product_id']) ) {
				$ro_product_id = $this->request->post['product_id'];
			} elseif ( isset($this->request->get['product_id']) ) {
				$ro_product_id = $this->request->get['product_id'];
			} else {
				$ro_product_id = $this->request->get['product'];
			}
			
			$data['ro_installed']  = $this->installed();
			$data['ro_settings']   = $this->config->get('related_options');
			$data['ro_product_id'] = $ro_product_id;
			$data['ro_theme_name'] = $this->getThemeName( isset($data['ro_override_theme_name']) ? $data['ro_override_theme_name'] : '' );
			
			$ro_params = [
				'product_id'    => $ro_product_id,
				'for_front_end' => true,
			];
			if ( !empty($data['ro_get_ro_combs_params']) ) {
				$ro_params = array_merge($ro_params, $data['ro_get_ro_combs_params']);
			}
			$data['ro_data'] = $this->liveopencart_ext_ro->getRODataByParams($ro_params);
			
			if (!empty($data['options'])) { // filter by possible ro_combs (and ro_data received above can be empty in some cases, thus we do not check it in this 'if')
				$data['options'] = $this->filterProductOptionsByPossibleROCombs($ro_product_id, $data['options']);
			}
			
			//$data['ro_data'] 		= $this->liveopencart_ext_ro->getROData($ro_product_id, true);
			if ( !empty($this->request->get['filter_name']) ) {
				$data['ro_filter_name'] = $this->request->get['filter_name'];
			}
			if ( !empty($this->request->get['search']) ) {
				$data['ro_search'] = $this->request->get['search'];
			}
			
			if ( $return_all_scripts ) { // add the basic module scripts too
				$data['ro_scripts'] = $this->getBasicScripts();
			}
			
			// the common part and the part for option reset
			if ( !empty($data['ro_data']) || !empty($data['ro_settings']['show_clear_options']) ) {
				$data['ro_scripts'][] = $this->getScriptCommon();
			}
		
			// the part when the product has related options
			//if ( !empty($data['ro_data']) ) {
				$data['ro_scripts'][] = $this->getScriptProductPage();
				$theme_script         = $this->getScriptProductPageTheme();
				if ( $theme_script ) {
					$data['ro_scripts'][] = $theme_script;
				}
			//}
			
			$this->load->model('catalog/product');
			$ro_product               = $this->model_catalog_product->getProduct($ro_product_id);
			$data['ro_product_model'] = empty($ro_product['model']) ? '' : $ro_product['model'];

			if ( !empty($this->request->get['roid']) && $this->hasRelatedOptionsIdInROData($this->request->get['roid'], $data['ro_data']) ) {
				$ro_id                 = (int)$this->request->get['roid'];
				$data['ros_to_select'] = [ $ro_id ];
				$default_product_data  = $this->liveopencart_ext_ro->getProductInfoForROId($ro_product, $ro_id, $data['ro_data'] );
				if ( isset($default_product_data['model']) ) {
					$data['ro_default_product_model'] = $default_product_data['model'];
				}
				$data['ro_default_product_data'] = $default_product_data;
				
			} else {
				$data['ros_to_select'] = $this->getROCombSelectedByDefault($ro_product_id, isset($this->request->get['search']) ? $this->request->get['search'] : '');
			}
			
			$data['ro_product_page_script'] = $this->render( 'extension_liveopencart/related_options/tpl/product_page_script', array_merge($basic_data, $data) );
			// for some custom adaptations
			$data['ro_product_page_script_init'] = $this->render( 'extension_liveopencart/related_options/tpl/product_page_script_init', array_merge($basic_data, $data) );

		}
		return $data;
	}
	
	protected function filterProductOptionsByPossibleROCombs($product_id, $product_options) {
		
		$ro_data = $this->liveopencart_ext_ro->getROData($product_id, true, true, true); // allow zero quantity here to filter only by existence of combs
		if ($ro_data) {
			
			$ro_combs   = call_user_func_array('array_merge', array_column($ro_data, 'ro'));
			$ro_po_ids  = [];
			$ro_pov_ids = [];
			
			$sets_of_options = array_column($ro_combs, 'options');

			$sets_of_po_ids = array_map('array_keys', $sets_of_options);
			$ro_po_ids      = call_user_func_array('array_merge', $sets_of_po_ids);
			$ro_po_ids      = array_unique($ro_po_ids);
			
			$sets_of_pov_ids = array_map('array_values', $sets_of_options);
			$ro_pov_ids      = call_user_func_array('array_merge', $sets_of_pov_ids);
			$ro_pov_ids      = array_unique($ro_pov_ids);
			
			foreach ($product_options as &$product_option) {
				if (in_array($product_option['product_option_id'], $ro_po_ids)) {
					$product_option['product_option_value'] = array_filter($product_option['product_option_value'], function($pov)use($ro_pov_ids){
						return in_array($pov['product_option_value_id'], $ro_pov_ids);
					});
				}
			}
			unset($product_option);
		}
		return $product_options;
	}
	
	public function getScriptsAll() {
		
		$scripts = $this->getBasicScripts();

		$scripts[] = $this->getScriptCommon();
		
		$scripts[]    = $this->getScriptProductPage();
		$theme_script = $this->getScriptProductPageTheme();
		if ( $theme_script ) {
			$scripts[] = $theme_script;
		}
		return $scripts;
	}
	
	protected function hasRelatedOptionsIdInROData($ro_id, $ro_data) {
		
		if ( $ro_data ) {
			foreach ( $ro_data as $ro_dt ) {
				if ( in_array($ro_id, array_column($ro_dt['ro'], 'relatedoptions_id') ) ) {
					return true;
				}
			}
		}
		return false;
	}
	
	private function render($route, $data) {
		
		$ReflectionMethod = new \ReflectionMethod('Template', '__construct');
		$params           = [];
		foreach ($ReflectionMethod->getParameters() as $param_reflection ) {
			$params[] = $param_reflection->getName();
		}
		
		if ( !empty($params[1]) && $params[1] == 'registry' ) { // $this->registry is added for compatibility with d_twig_manager.xml
			$template = new Template($this->registry->get('config')->get('template_engine'), $this->registry);
		} else { // std
			$template = new Template($this->registry->get('config')->get('template_engine'));
		}
		
		// $this->registry is added for compatibility with d_twig_manager.xml
		//$template = new Template($this->registry->get('config')->get('template_engine'), $this->registry);
		
		foreach ($this->language->all() as $key => $value) {
			$template->set($key, $value);
		}
		
		foreach ($data as $key => $value) {
			$template->set($key, $value);
		}
		
		$classMethod = new ReflectionMethod($template,'render');
		if ( count($classMethod->getParameters()) > 2 )  {
			if ( $classMethod->getParameters()[1]->name == 'cache' && $classMethod->getParameters()[2]->name == 'registry' ) {
				$output = $template->render( $route, false, $this->registry ); // for some mods ($route, $cache=false, $registry)
			} else {
				$output = $template->render( $route, $this->registry ); // for some mods ($route, $registry, $cache=false)
			}
		} else { // std
			$output = $template->render( $route );
		}
		
		return $output;
	}
	
	public function getRODataForProductList($product_id) {
		
		if ( $this->installed() && ( $this->getThemeName() == 'themeXXX' || $this->getThemeName() == 'theme725' ) ) {
			return $this->liveopencart_ext_ro->getROData($product_id, true);
		}
		
	}
	
	public function getROCombSelectedByDefault($product_id, $search_request = '') {
		$ro_settings   = $this->config->get('related_options');
		$ros_to_select = false;
		if ( $search_request && !empty($ro_settings['spec_model']) ) {
			$ros_to_select = $this->getRelatedOptionsIdsFromSearch($product_id, $search_request);
		}
		if ( !$ros_to_select && isset($ro_settings['select_first']) && $ro_settings['select_first'] == 1 ) {
			$ros_to_select = $this->getRelatedOptionsIdsAutoSelectFirst($product_id);
		}
		return $ros_to_select;
	}
	
	public function getDefaultRelatedOptions($product_id) {
		
		if ($this->installed()) {
			$options = [];
			$ro_ids  = $this->getROCombSelectedByDefault($product_id);
			if ($ro_ids) {
				$ro_combs_assoc_all = $this->liveopencart_ext_ro->getAllROCombsAssoc($product_id);
				foreach ($ro_ids as $ro_id) {
					if (isset($ro_combs_assoc_all[$ro_id]))	{
						$ro_comb = $ro_combs_assoc_all[$ro_id];
						foreach ($ro_comb['options'] as $po_id => $pov_id) {
							if (!isset($options[$po_id])) {
								$options[$po_id] = $pov_id;
							}
						}
					}
				}
			}
			return $options;
		}
	}

	// << orders editing
	public function getOrderOptions($order_id, $order_product_id) {
		
		// comp with old code
		\liveopencart\ext\ro::getInstance($this->registry)->getOrderOptions($order_id, $order_product_id);
	}
	
	// returns only switched-on additional fields (sku, upc, location)
	public function getAdditionalFields($include_model = false) {
		
		$fields = [];
		
		if ($this->installed()) {
			$ro_settings = $this->config->get('related_options');
			$std_fields  = ['sku', 'upc', 'ean', 'jan', 'location'];
			if ( $include_model ) {
				array_unshift($std_fields, 'model');
			}
			foreach ($std_fields as $field) {
				if ( isset($ro_settings['spec_'.$field]) && $ro_settings['spec_'.$field] ) {
					$fields[] = $field;
				}
			}
		}
		
		return $fields;
	}
	
	public function getCustomFields($product_info, $ro_combs) {
		
		// comp for old code
		return \liveopencart\ext\ro::getInstance($this->registry)->getCustomFields($product_info, $ro_combs);
		
	}
	
	public function updateOrderProductAdditionalFields($product, $order_product_id) {
		
		// comp for old code
		return \liveopencart\ext\ro::getInstance($this->registry)->updateOrderProductAdditionalFields($product, $order_product_id);
		
	}
	
	public function getRelatedOptionsIdsAutoSelectFirst($product_id) {
		
		$ro_ids             = [];
		$ro_data            = $this->liveopencart_ext_ro->getROData($product_id, true);
		$ro_combs_assoc_all = $this->liveopencart_ext_ro->getAllROCombsAssoc($product_id, false); // we need 'options_original' and they are present in non-frontend version of ro_data only
		
		$existing_options = [];
		foreach ($ro_data as $ro_dt) {
			
			$ro_combs = [];
			if ( $existing_options ) { // filter combinations by option values from previous combinations
				
				foreach ( $ro_dt['ro'] as $ro ) {
					
					$ro_comb = $ro_combs_assoc_all[$ro['relatedoptions_id']];
					
					$all_values_equal = true;
					foreach ($ro_comb['options_original'] as $option_id => $option_value_id) {
						if ( isset($existing_options[$option_id]) && $existing_options[$option_id] != $option_value_id ) {
							$all_values_equal = false;
							break;
						}
					}
					if ( $all_values_equal ) {
						$ro_combs[] = $ro_comb;
					}
				}
				
			} else {
				foreach ( $ro_dt['ro'] as $ro ) {
					$ro_combs[] = $ro_combs_assoc_all[$ro['relatedoptions_id']];
				}
				//$ro_combs = $ro_dt['ro'];
			}
			
			$ro_default = [];
			
			foreach ( $ro_combs as $ro ) {
				if ($ro['defaultselect']) {
					$ro_default[] = $ro;
				}
			}
			
			$ro_comb = false;
			if ( count($ro_default) == 0 ) {
				$ro_default = $ro_combs;
			}
			
			foreach ($ro_default as $ro) {
				if ($ro_comb === false || $ro_comb['defaultselectpriority'] > $ro['defaultselectpriority']) {
					$ro_comb = $ro;
				}
			}
			
			if ($ro_comb) {
				$ro_ids[] = $ro_comb['relatedoptions_id'];
				foreach ( $ro_comb['options_original'] as $option_id => $option_value_id ) {
					$existing_options[$option_id] = $option_value_id;
				}
			}
		}
		
		return $ro_ids;
	}
	
	public function getRelatedOptionsIdsFromSearch($product_id, $search_string) {
		
		$ro_settings = $this->config->get('related_options');
		
		if ( isset($ro_settings['spec_model']) ) {
			
			$sql_where_addon_disabled = "";
			if ( !empty($ro_settings['spec_disabled']) ) {
				$sql_where_addon_disabled = " AND RO.disabled = 0 ";
			}
			
			if ( $ro_settings['spec_model'] == 2 || $ro_settings['spec_model'] == 3 ) {
			
				$query = $this->db->query("
					SELECT RS.*
					FROM `".DB_PREFIX."relatedoptions_search` RS
						,`".DB_PREFIX."relatedoptions` RO
					WHERE RS.product_id = ".(int)$product_id."
					  AND LCASE(RS.`model`) = '" . $this->db->escape(utf8_strtolower($search_string)) . "'
					  AND FIND_IN_SET(RO.relatedoptions_id, RS.ro_ids)
					  ".$sql_where_addon_disabled."
				");
				
				if ($query->num_rows) {
					return explode(',',$query->row['ro_ids']);
				}
				
			} elseif ( $ro_settings['spec_model'] == 1 ) {
				
				$query = $this->db->query("
					SELECT RO.*
					FROM `".DB_PREFIX."relatedoptions` RO
					WHERE RO.product_id = ".(int)$product_id."
						AND LCASE(RO.`model`) = '" . $this->db->escape(utf8_strtolower($search_string)) . "'
						".$sql_where_addon_disabled."
				");
				
				$ro_ids = [];
				foreach ($query->rows as $row) {
					$ro_ids[] = $row['relatedoptions_id'];
				}
				return $ro_ids;
				
			}
		}
		return false;
	}
	
	// get price and stock
  public function getJournal2Price($product_id, $price, $special = false) {
		
		if ($this->installed()) {
			
			$this->load->model('catalog/product');
			
			$product_options = $this->model_catalog_product->getProductOptions($product_id);
			$options         = [];
			foreach ($product_options as $option) {
				if (!in_array($option['type'], ['select', 'radio', 'image', 'block', 'color'])) continue;
							
				$option_ids = Journal2Utils::getProperty($this->request->post, 'option.' . $option['product_option_id'], []);
				
				if (is_scalar($option_ids)) {
					$options[$option['product_option_id']] = $option_ids;
				} elseif (is_array($option_ids) && count($option_ids) > 0) {
					$options[$option['product_option_id']] = $option_ids[0];
				}
			}
			
			return $this->liveopencart_ext_ro->getProductPriceWithRoByProductOptions($product_id, $options, $price, $special);
			
		}
			
		return false;
	}
	
	// get price and stock
  public function getJournalPrice($product_id, $price, $quantity, $special = false, $product_info = false) {
		
		if ($this->installed()) {
			
			$ro_settings = $this->config->get('related_options');
			if ( $ro_settings && is_array($ro_settings) ) {
				
				if ( !$this->model_catalog_product ) {
					$this->load->model('catalog/product');
				}
				$product_options = $this->model_catalog_product->getProductOptions($product_id);
				$options         = [];
				foreach ($product_options as $option) {
					if (!in_array($option['type'], ['select', 'radio', 'image', 'block', 'color'])) continue;
								
					$option_ids = isset($this->request->post['option'][$option['product_option_id']]) ? $this->request->post['option'][$option['product_option_id']] : [];
					
					if (is_scalar($option_ids)) {
						$options[$option['product_option_id']] = $option_ids;
					} elseif (is_array($option_ids) && count($option_ids) > 0) {
						$options[$option['product_option_id']] = $option_ids[0];
					}
				}
				
				$ro_price_data = $this->liveopencart_ext_ro->getProductPriceWithRoByProductOptions($product_id, $options, $price, $quantity, $special, $product_info);
				
				if ( empty($ro_settings['spec_ofs']) && empty($ro_settings['spec_inss']) ) {
					unset($ro_settings['stock']);
				}
				
				return $ro_price_data;

			}
		}
		return false;
	}
	
	// check is there enough product quantity for related options (for all products in cart)
	public function updateCartProductsStockStatuses($products) {
		
		if ($this->installed()) {
			if (is_array($products)) {
				foreach ($products as &$product) {
					if ($product['stock']) {
						if (isset($product['option']) && is_array($product['option'])) {
							$poids = [];
							foreach ($product['option'] as $option) {
								if ($option) {
									$poids[$option['product_option_id']] = $option['product_option_value_id'];
								}
							}
							if (count($poids) > 0) {
								$product['stock'] = $this->getProductCartIsInStock($product['product_id'], $poids, $product['quantity']);
							}
						}
					}
				}
				unset($product);
			}
		}
		return $products;
		
	}
	
	private function getROCombsWithQuantitiesInCartByProductId($p_product_id) {
		
		$qtys = [];
		
		$products = $this->cart->getProducts();
		foreach ($products as $product) {
			if ($product['product_id'] == $p_product_id) {
				$cart_options = [];
				foreach ($product['option'] as $option) {
					$cart_options[$option['product_option_id']] = $option['product_option_value_id'];
				}
				
				$ro_combs = $this->getROCombsByPOIds($p_product_id, $cart_options, true, true);
				if ($ro_combs) {
					foreach ($ro_combs as $ro_comb) {
						if ( !isset($qtys[$ro_comb['relatedoptions_id']]) ) {
							$qtys[$ro_comb['relatedoptions_id']] = 0;
						}
						$qtys[$ro_comb['relatedoptions_id']] += $product['quantity'];
					}
				}
			}
		}
		return $qtys;
	}
	
	public function getROFreeQuantitiesByOptions($product_id, $options, $quantity_per_options = []) {
		
		$result = [];
		
		if ( ($options || $quantity_per_options) && $product_id ) {
			
			$quantity = false;
		
			$ro_settings = $this->config->get('related_options');
		
			$ro_combs_in_cart = $this->getROCombsWithQuantitiesInCartByProductId($product_id);
		
			$qtys = [];
			if ( $options ) {
				$ro_combs = $this->getROCombsByPOIds($product_id, $options, true, true);
				if ($ro_combs) {
					foreach ($ro_combs as $ro_comb) {
						$qtys[$ro_comb['relatedoptions_id']] = MAX(0, $ro_comb['quantity']);
						if ( !empty($ro_settings['spec_disabled']) && !empty($ro_comb['disabled']) ) {
							$qtys[$ro_comb['relatedoptions_id']] = 0; // consider disabled RO combs as out of stock
						}
					}
				}
				
				foreach ( $qtys as $relatedoptions_id => &$qty ) {
					if ( !empty($ro_combs_in_cart[$relatedoptions_id]) ) {
						$ro_in_cart_quantity = $ro_combs_in_cart[$relatedoptions_id];
						$qty                 = MAX(0, $qty - $ro_in_cart_quantity);
					}
				}
				unset($qty);
				
				foreach ($qtys as $qty) {
					if ($quantity === false) {
						$quantity = $qty;
					} else {
						$quantity = MIN($quantity, $qty);
					}
				}
			}
			$result['quantity'] = $quantity;
			
			// check for specific option view (separate quantity inputs/selects for option values )
			// should return quantities allowed to add to cart (available) only for option combs where customer is set greater quantity (to display only warnings)
			if ( $quantity_per_options ) {
				
				foreach ( $quantity_per_options as $product_option_id => $quantity_per_option ) { // generally, there should be only on product option (product_option_value_id)
					if ( $quantity_per_option ) {
						foreach ( $quantity_per_option as $product_option_value_id => $product_option_value_quantity ) {
							$product_option_value_quantity = (int)$product_option_value_quantity;
							if ( $product_option_value_quantity ) {
								$current_options                     = $options;
								$current_options[$product_option_id] = $product_option_value_id;
								$qtys                                = [];
								
								$ro_combs = $this->getROCombsByPOIds($product_id, $current_options, true, true);
								if ($ro_combs) {
									foreach ($ro_combs as $ro_comb) {
										$qtys[$ro_comb['relatedoptions_id']] = MAX(0, $ro_comb['quantity']);
										if ( !empty($ro_settings['spec_disabled']) && !empty($ro_comb['disabled']) ) {
											$qtys[$ro_comb['relatedoptions_id']] = 0; // consider disabled RO combs as out of stock
										}
									}
								}
								foreach ( $qtys as $relatedoptions_id => &$qty ) {
									if ( !empty($ro_combs_in_cart[$relatedoptions_id]) ) {
										$ro_in_cart_quantity = $ro_combs_in_cart[$relatedoptions_id];
										$qty                 = MAX(0, $qty - $ro_in_cart_quantity);
									}
								}
								unset($qty);
								if ( $qtys ) {
									$current_quantity = false;
									foreach ($qtys as $qty) {
										if ($current_quantity === false) {
											$current_quantity = $qty;
										} else {
											$current_quantity = MIN($current_quantity, $qty);
										}
									}
									if ( $product_option_value_quantity > $current_quantity ) {
										if ( !isset($result['quantity_per_option_value']) ) {
											$result['quantity_per_option_value'] = [];
										}
										$result['quantity_per_option_value'][$product_option_value_id] = $current_quantity;
									}
								}
							}
						}
					}
				}
			}
			
		}
		
		return $result;
		
	}
	
	// check is there's enough quantity for related options
	public function getProductCartIsInStock($product_id, $options, $quantity) {
		
		$ro_settings = $this->config->get('related_options');
		$ro_combs    = $this->getROCombsByPOIds($product_id, $options, true);
		//$ro_combs = $this->getROCombsByPOIds($product_id, $options);
		$stock_ok = true;
		if ($ro_combs) {
			foreach ($ro_combs as $ro_comb) {
				$stock_ok = $stock_ok && ($quantity <= $ro_comb['quantity'] || !empty($ro_settings['allow_zero_select']));
				// consider disabled RO combs as out of stock
				$stock_ok = $stock_ok && ( empty($ro_settings['spec_disabled']) || empty($ro_comb['disabled']) );
			}
		}
		
		return $stock_ok;
		
	}
	
	// returns information for all relevant related options combinations
	// discounts and specials for current customer
	// if there's not price, discount or special for combination, this data takes from product
	// all options values from related options combination should be equal to options given as parameter of function
	// (it's possible to have more options in parameter than in a related options combination)
	public function getROCombsByPOIds($product_id, $param_options, $use_cache = false, $p_allow_zero_quantity = -1, $use_ro_data_cache = false) {
		
		// comp for old code
		return \liveopencart\ext\ro::getInstance($this->registry)->getROCombsByPOIds($product_id, $param_options, $use_cache, $p_allow_zero_quantity, $use_ro_data_cache);
		
	}
  
	// comp for old code (on update)
	function check_order_product_table() {
		return \liveopencart\ext\ro::getInstance($this->registry)->checkTableOrderProduct();
	}
	
	// comp for old code (on update)
	public function get_ro_data($product_id, $for_front_end = false, $p_allow_zero_quantity = -1, $use_cache = false) {
		return \liveopencart\ext\ro::getInstance($this->registry)->getROData($product_id, $for_front_end, $p_allow_zero_quantity, $use_cache);
	}

	public function installed() {
		
		return \liveopencart\ext\ro::getInstance($this->registry)->installed();
	}
}
