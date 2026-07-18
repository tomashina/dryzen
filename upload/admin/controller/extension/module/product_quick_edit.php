<?php
defined('EXTENSION_NAME')           || define('EXTENSION_NAME',            'Product Quick Edit Plus');
defined('EXTENSION_VERSION')        || define('EXTENSION_VERSION',         '1.12.0');
defined('EXTENSION_ID')             || define('EXTENSION_ID',              '15274');
defined('EXTENSION_COMPATIBILITY')  || define('EXTENSION_COMPATIBILITY',   'OpenCart 3.0.0.x, 3.0.1.x, 3.0.2.x and 3.0.3.x');
defined('EXTENSION_STORE_URL')      || define('EXTENSION_STORE_URL',       'https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=' . EXTENSION_ID);
defined('EXTENSION_PURCHASE_URL')   || define('EXTENSION_PURCHASE_URL',    'https://www.opencart.com/index.php?route=marketplace/purchase&extension_id=' . EXTENSION_ID);
defined('EXTENSION_RATE_URL')       || define('EXTENSION_RATE_URL',        'https://www.opencart.com/index.php?route=account/rating/add&extension_id=' . EXTENSION_ID);
defined('EXTENSION_SUPPORT_EMAIL')  || define('EXTENSION_SUPPORT_EMAIL',   'support@opencart.ee');
defined('EXTENSION_SUPPORT_LINK')   || define('EXTENSION_SUPPORT_LINK',    'https://www.opencart.com/index.php?route=support/seller&extension_id=' . EXTENSION_ID);
defined('EXTENSION_SUPPORT_FORUM')  || define('EXTENSION_SUPPORT_FORUM',   'https://forum.opencart.com/viewtopic.php?f=123&t=116963');
defined('OTHER_EXTENSIONS')         || define('OTHER_EXTENSIONS',          'https://www.opencart.com/index.php?route=marketplace/extension&filter_member=bull5-i');

class ControllerExtensionModuleProductQuickEdit extends Controller {
	private $error = array();
	protected $alert = array(
		'error'     => array(),
		'warning'   => array(),
		'success'   => array(),
		'info'      => array()
	);

	private static $config_defaults = array(
		'module_product_quick_edit_installed'                     => 1,
		'module_product_quick_edit_installed_version'             => EXTENSION_VERSION,
		'module_product_quick_edit_status'                        => 0,
		'module_product_quick_edit_display_in_menu_as'            => 0,
		'module_product_quick_edit_use_gross_price_for_actions'   => 0,
		'module_product_quick_edit_alternate_row_colour'          => 0,
		'module_product_quick_edit_row_hover_highlighting'        => 0,
		'module_product_quick_edit_highlight_status'              => 0,
		'module_product_quick_edit_highlight_filtered_columns'    => 0,
		'module_product_quick_edit_highlight_actions'             => 0,
		'module_product_quick_edit_row_hover_highlighting'        => 0,
		'module_product_quick_edit_quick_edit_on'                 => 'click',
		'module_product_quick_edit_price_relative_to'             => 'previous', // 'product'
		'module_product_quick_edit_list_view_image_width'         => 40,
		'module_product_quick_edit_list_view_image_height'        => 40,
		'module_product_quick_edit_filter_sub_category'           => 0,
		'module_product_quick_edit_items_per_page'                => 25,
		'module_product_quick_edit_server_side_caching'           => 0,
		'module_product_quick_edit_client_side_caching'           => 1,
		'module_product_quick_edit_cache_size'                    => 1000,
		'module_product_quick_edit_debug_mode'                    => 0,
		'module_product_quick_edit_search_bar'                    => 1,
		'module_product_quick_edit_batch_edit'                    => 0,
		'module_product_quick_edit_show_success_message'          => 1,
		'module_product_quick_edit_default_sort'                  => 'pd.name',
		'module_product_quick_edit_default_order'                 => 'ASC',
		'module_product_quick_edit_services'                      => "W10=",
	);

	private static $column_defaults = array(
		'module_product_quick_edit_catalog_products'      => array(
			'selector'          => array('display' => 1, 'editable' => 0, 'index' =>   0, 'align' => 'text-center', 'type' =>           '', 'sort' => ''                , 'rel' => array()),
			'id'                => array('display' => 0, 'editable' => 0, 'index' =>   5, 'align' =>   'text-left', 'type' =>           '', 'sort' => 'p.product_id'    , 'rel' => array()),
			'image'             => array('display' => 1, 'editable' => 1, 'index' =>  10, 'align' => 'text-center', 'type' =>   'image_qe', 'sort' => ''                , 'rel' => array()),
			'category'          => array('display' => 0, 'editable' => 1, 'index' =>  20, 'align' =>   'text-left', 'type' =>     'cat_qe', 'sort' => ''                , 'rel' => array()),
			'manufacturer'      => array('display' => 0, 'editable' => 1, 'index' =>  30, 'align' =>   'text-left', 'type' => 'manufac_qe', 'sort' => 'm.name'          , 'rel' => array()),
			'name'              => array('display' => 1, 'editable' => 1, 'index' =>  40, 'align' =>   'text-left', 'type' =>    'name_qe', 'sort' => 'pd.name'         , 'rel' => array()),
			'tag'               => array('display' => 0, 'editable' => 1, 'index' =>  50, 'align' =>   'text-left', 'type' =>     'tag_qe', 'sort' => ''                , 'rel' => array()),
			'model'             => array('display' => 1, 'editable' => 1, 'index' =>  60, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.model'         , 'rel' => array()),
			'price'             => array('display' => 1, 'editable' => 1, 'index' =>  70, 'align' =>  'text-right', 'type' =>   'price_qe', 'sort' => 'p.price'         , 'rel' => array('gross_price')),
			'gross_price'       => array('display' => 0, 'editable' => 1, 'index' =>  75, 'align' =>  'text-right', 'type' =>   'price_qe', 'sort' => 'p.price'         , 'rel' => array('price')),
			'quantity'          => array('display' => 1, 'editable' => 1, 'index' =>  80, 'align' =>  'text-right', 'type' =>     'qty_qe', 'sort' => 'p.quantity'      , 'rel' => array()),
			'sku'               => array('display' => 0, 'editable' => 1, 'index' =>  90, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.sku'           , 'rel' => array()),
			'upc'               => array('display' => 0, 'editable' => 1, 'index' => 100, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.upc'           , 'rel' => array()),
			'ean'               => array('display' => 0, 'editable' => 1, 'index' => 110, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.ean'           , 'rel' => array()),
			'jan'               => array('display' => 0, 'editable' => 1, 'index' => 120, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.jan'           , 'rel' => array()),
			'isbn'              => array('display' => 0, 'editable' => 1, 'index' => 130, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.isbn'          , 'rel' => array()),
			'mpn'               => array('display' => 0, 'editable' => 1, 'index' => 140, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.mpn'           , 'rel' => array()),
			'location'          => array('display' => 0, 'editable' => 1, 'index' => 150, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.location'      , 'rel' => array()),
			'tax_class'         => array('display' => 0, 'editable' => 1, 'index' => 170, 'align' =>   'text-left', 'type' => 'tax_cls_qe', 'sort' => 'tc.title'        , 'rel' => array('gross_price')),
			'minimum'           => array('display' => 0, 'editable' => 1, 'index' => 180, 'align' =>  'text-right', 'type' =>         'qe', 'sort' => 'p.minimum'       , 'rel' => array()),
			'subtract'          => array('display' => 0, 'editable' => 1, 'index' => 190, 'align' => 'text-center', 'type' =>  'yes_no_qe', 'sort' => 'p.subtract'      , 'rel' => array()),
			'stock_status'      => array('display' => 0, 'editable' => 1, 'index' => 200, 'align' =>   'text-left', 'type' =>   'stock_qe', 'sort' => 'ss.name'         , 'rel' => array()),
			'shipping'          => array('display' => 0, 'editable' => 1, 'index' => 210, 'align' => 'text-center', 'type' =>  'yes_no_qe', 'sort' => 'p.shipping'      , 'rel' => array()),
			'date_added'        => array('display' => 0, 'editable' => 1, 'index' => 215, 'align' =>   'text-left', 'type' =>'datetime_qe', 'sort' => 'p.date_added'    , 'rel' => array()),
			'date_available'    => array('display' => 0, 'editable' => 1, 'index' => 220, 'align' =>   'text-left', 'type' =>    'date_qe', 'sort' => 'p.date_available', 'rel' => array()),
			'date_modified'     => array('display' => 0, 'editable' => 0, 'index' => 230, 'align' =>   'text-left', 'type' =>'datetime_qe', 'sort' => 'p.date_modified' , 'rel' => array()),
			'length'            => array('display' => 0, 'editable' => 1, 'index' => 240, 'align' =>   'text-left', 'type' =>         'qe', 'sort' => 'p.length'        , 'rel' => array()),
			'width'             => array('display' => 0, 'editable' => 1, 'index' => 250, 'align' =>  'text-right', 'type' =>         'qe', 'sort' => 'p.width'         , 'rel' => array()),
			'height'            => array('display' => 0, 'editable' => 1, 'index' => 260, 'align' =>  'text-right', 'type' =>         'qe', 'sort' => 'p.height'        , 'rel' => array()),
			'weight'            => array('display' => 0, 'editable' => 1, 'index' => 270, 'align' =>  'text-right', 'type' =>         'qe', 'sort' => 'p.weight'        , 'rel' => array()),
			'length_class'      => array('display' => 0, 'editable' => 1, 'index' => 280, 'align' =>   'text-left', 'type' =>  'length_qe', 'sort' => 'lc.title'        , 'rel' => array()),
			'weight_class'      => array('display' => 0, 'editable' => 1, 'index' => 290, 'align' =>   'text-left', 'type' =>  'weight_qe', 'sort' => 'wc.title'        , 'rel' => array()),
			'points'            => array('display' => 0, 'editable' => 1, 'index' => 300, 'align' =>  'text-right', 'type' =>         'qe', 'sort' => 'p.points'        , 'rel' => array()),
			'filter'            => array('display' => 0, 'editable' => 1, 'index' => 310, 'align' =>   'text-left', 'type' =>  'filter_qe', 'sort' => ''                , 'rel' => array('filters')),
			'download'          => array('display' => 0, 'editable' => 1, 'index' => 320, 'align' =>   'text-left', 'type' =>      'dl_qe', 'sort' => ''                , 'rel' => array()),
			'store'             => array('display' => 0, 'editable' => 1, 'index' => 330, 'align' =>   'text-left', 'type' =>   'store_qe', 'sort' => ''                , 'rel' => array()),
			'sort_order'        => array('display' => 1, 'editable' => 1, 'index' => 340, 'align' =>  'text-right', 'type' =>         'qe', 'sort' => 'p.sort_order'    , 'rel' => array()),
			'status'            => array('display' => 1, 'editable' => 1, 'index' => 350, 'align' => 'text-center', 'type' =>  'status_qe', 'sort' => 'p.status'        , 'rel' => array()),
			'viewed'            => array('display' => 0, 'editable' => 1, 'index' => 360, 'align' =>  'text-right', 'type' =>         'qe', 'sort' => 'p.viewed'        , 'rel' => array()),
			'view_in_store'     => array('display' => 0, 'editable' => 0, 'index' => 370, 'align' =>   'text-left', 'type' =>           '', 'sort' => ''                , 'rel' => array()),
			'action'            => array('display' => 1, 'editable' => 0, 'index' => 380, 'align' =>  'text-right', 'type' =>           '', 'sort' => ''                , 'rel' => array()),
		),
		'module_product_quick_edit_catalog_products_actions' => array(
			'attributes'        => array('display' => 0, 'index' =>  0, 'short' => 'attr',  'type' =>    'attr_qe', 'class' =>            '', 'rel' => array()),
			'discounts'         => array('display' => 0, 'index' =>  1, 'short' => 'dscnt', 'type' =>   'dscnt_qe', 'class' =>            '', 'rel' => array()),
			'images'            => array('display' => 0, 'index' =>  2, 'short' => 'img',   'type' =>  'images_qe', 'class' =>            '', 'rel' => array()),
			'filters'           => array('display' => 0, 'index' =>  3, 'short' => 'fltr',  'type' => 'filters_qe', 'class' =>            '', 'rel' => array('filter')),
			'options'           => array('display' => 0, 'index' =>  4, 'short' => 'opts',  'type' =>  'option_qe', 'class' =>            '', 'rel' => array()),
			'recurrings'        => array('display' => 0, 'index' =>  5, 'short' => 'rec',   'type' =>   'recur_qe', 'class' =>            '', 'rel' => array()),
			'related'           => array('display' => 0, 'index' =>  6, 'short' => 'rel',   'type' => 'related_qe', 'class' =>            '', 'rel' => array()),
			'specials'          => array('display' => 0, 'index' =>  7, 'short' => 'spcl',  'type' => 'special_qe', 'class' =>            '', 'rel' => array('price', 'gross_price')),
			'descriptions'      => array('display' => 0, 'index' =>  8, 'short' => 'desc',  'type' =>   'descr_qe', 'class' =>            '', 'rel' => array()),
			'seo_urls'          => array('display' => 0, 'index' =>  9, 'short' => 'seo',   'type' =>     'seo_qe', 'class' =>            '', 'rel' => array()),
			'view'              => array('display' => 1, 'index' => 10, 'short' => 'vw',    'type' =>       'view', 'class' =>            '', 'rel' => array()),
			'edit'              => array('display' => 1, 'index' => 11, 'short' => 'ed',    'type' =>       'edit', 'class' => 'btn-primary', 'rel' => array()),
		)
	);

	private static $event_hooks = array(
		'admin_module_product_quick_edit_product_form'         => array('trigger' => 'admin/view/catalog/product_form/before',                     'action' => 'extension/module/product_quick_edit/product_form_hook'),
		'admin_module_product_quick_edit_product_edit'         => array('trigger' => 'admin/model/catalog/product/editProduct/after',              'action' => 'extension/module/product_quick_edit/product_edit_hook'),
		'admin_module_product_quick_edit_product_list'         => array('trigger' => 'admin/controller/catalog/product/before',                    'action' => 'extension/module/product_quick_edit/product_list_hook'),
		'admin_module_product_quick_edit_menu'                 => array('trigger' => 'admin/view/common/column_left/before',                       'action' => 'extension/module/product_quick_edit/menu_hook'),
		'admin_module_product_quick_edit_product_add'          => array('trigger' => 'admin/model/catalog/product/addProduct/after',               'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_product_delete'       => array('trigger' => 'admin/model/catalog/product/deleteProduct/after',            'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_manufacturer_add'     => array('trigger' => 'admin/model/catalog/manufacturer/addManufacturer/after',     'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_manufacturer_edit'    => array('trigger' => 'admin/model/catalog/manufacturer/editManufacturer/after',    'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_manufacturer_delete'  => array('trigger' => 'admin/model/catalog/manufacturer/deleteManufacturer/after',  'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_download_add'         => array('trigger' => 'admin/model/catalog/download/addDownload/after',             'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_download_edit'        => array('trigger' => 'admin/model/catalog/download/editDownload/after',            'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_download_delete'      => array('trigger' => 'admin/model/catalog/download/deleteDownload/after',          'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_filter_add'           => array('trigger' => 'admin/model/catalog/filter/addFilter/after',                 'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_filter_edit'          => array('trigger' => 'admin/model/catalog/filter/editFilter/after',                'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
		'admin_module_product_quick_edit_filter_delete'        => array('trigger' => 'admin/model/catalog/filter/deleteFilter/after',              'action' => 'extension/module/product_quick_edit/clear_cache_hook'),
	);

	public function __construct($registry) {
		parent::__construct($registry);
		$this->config->load('pqep');
	}

	public function index() {
		$this->load->language('extension/module/product_quick_edit');

		$this->document->setTitle($this->language->get('extension_name'));

		$this->load->model('setting/setting');

		$ajax_request = isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && !$ajax_request && $this->validateForm($this->request->post)) {
			$original_settings = $this->model_setting_setting->getSetting('module_product_quick_edit');

			foreach (self::$config_defaults as $setting => $default) {
				$value = $this->config->get($setting);
				if ($value === null) {
					$original_settings[$setting] = $default;
				}
			}

			foreach (self::$column_defaults as $page => $columns) {
				$page_conf = $this->config->get($page);

				if ($page_conf === null) {
					$page_conf = $value;
				}

				foreach ($columns as $column => $attributes) {
					if (!isset($page_conf[$column])) {
						$page_conf[$column] = $attributes;
					} else {
						foreach ($attributes as $key => $value) {
							if (!isset($page_conf[$column][$key])) {
								$page_conf[$column][$key] = $value;
							} else {
								switch ($key) {
									case 'display':
									case 'index':
										break;
									default:
										$page_conf[$column][$key] = $value;
										break;
								}
							}
						}

						foreach (array_diff(array_keys($page_conf[$column]), array_keys($columns[$column])) as $key) {
							unset($page_conf[$column]);
						}
					}
				}

				foreach (array_diff(array_keys($page_conf), array_keys($columns)) as $key) {
					unset($page_conf[$key]);
				}

				$this->request->post[$page] = $page_conf;
			}

			$settings = array_merge($original_settings, $this->request->post);
			$settings['module_product_quick_edit_installed_version'] = $original_settings['module_product_quick_edit_installed_version'];

			$this->model_setting_setting->editSetting('module_product_quick_edit', $settings);

			$this->session->data['success'] = $this->language->get('text_success_update');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		} else if ($this->request->server['REQUEST_METHOD'] == 'POST' && $ajax_request) {
			$response = array();

			if ($this->validateForm($this->request->post)) {
				$original_settings = $this->model_setting_setting->getSetting('module_product_quick_edit');

				foreach (self::$config_defaults as $setting => $default) {
					$value = $this->config->get($setting);
					if ($value === null) {
						$original_settings[$setting] = $default;
					}
				}

				foreach (self::$column_defaults as $page => $columns) {
					$page_conf = $this->config->get($page);

					if ($page_conf === null) {
						$page_conf = $value;
					}

					foreach ($columns as $column => $attributes) {
						if (!isset($page_conf[$column])) {
							$page_conf[$column] = $attributes;
						} else {
							foreach ($attributes as $key => $value) {
								if (!isset($page_conf[$column][$key])) {
									$page_conf[$column][$key] = $value;
								} else {
									switch ($key) {
										case 'display':
										case 'index':
											break;
										default:
											$page_conf[$column][$key] = $value;
											break;
									}
								}
							}

							foreach (array_diff(array_keys($page_conf[$column]), array_keys($columns[$column])) as $key) {
								unset($page_conf[$column][$key]);
							}
						}
					}

					foreach (array_diff(array_keys($page_conf), array_keys($columns)) as $key) {
						unset($page_conf[$key]);
					}

					$this->request->post[$page] = $page_conf;
				}

				$settings = array_merge($original_settings, $this->request->post);
				$settings['module_product_quick_edit_installed_version'] = $original_settings['module_product_quick_edit_installed_version'];

				if ((int)$original_settings['module_product_quick_edit_status'] != (int)$this->request->post['module_product_quick_edit_status'] || (int)$original_settings['module_product_quick_edit_display_in_menu_as'] != (int)$this->request->post['module_product_quick_edit_display_in_menu_as']) {
					$response['reload'] = true;
					$this->session->data['success'] = $this->language->get('text_success_update');
				}

				$this->model_setting_setting->editSetting('module_product_quick_edit', $settings);

				$this->alert['success']['updated'] = $this->language->get('text_success_update');
			} else {
				if (!$this->checkVersion()) {
					$response['reload'] = true;
				}
			}

			$response = array_merge($response, array("errors" => $this->error), array("alerts" => $this->alert));

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
			return;
		}

		$data['heading_title'] = $this->language->get('extension_name');
		$data['text_other_extensions'] = sprintf($this->language->get('text_other_extensions'), OTHER_EXTENSIONS);

		$data['ext_name'] = EXTENSION_NAME;
		$data['ext_version'] = EXTENSION_VERSION;
		$data['ext_id'] = EXTENSION_ID;
		$data['ext_compatibility'] = EXTENSION_COMPATIBILITY;
		$data['ext_store_url'] = EXTENSION_STORE_URL;
		$data['ext_rate_url'] = EXTENSION_RATE_URL;
		$data['ext_purchase_url'] = EXTENSION_PURCHASE_URL;
		$data['ext_support_email'] = EXTENSION_SUPPORT_EMAIL;
		$data['ext_support_link'] = EXTENSION_SUPPORT_LINK;
		$data['ext_support_forum'] = EXTENSION_SUPPORT_FORUM;
		$data['other_extensions_url'] = OTHER_EXTENSIONS;
		$data['oc_version'] = VERSION;
		$data['php_version'] = phpversion();
		$data['installed_extensions'] = (array)$this->config->get('pqe_plus_extensions');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'active'    => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_extension'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
			'active'    => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('extension_name'),
			'href'      => $this->url->link('extension/module/product_quick_edit', 'user_token=' . $this->session->data['user_token'], true),
			'active'    => true
		);

		$data['save'] = $this->url->link('extension/module/product_quick_edit', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		$data['upgrade'] = $this->url->link('extension/module/product_quick_edit/upgrade', 'user_token=' . $this->session->data['user_token'], true);
		$data['extension_installer'] = $this->url->link('extension/installer', 'user_token=' . $this->session->data['user_token'], true);
		$data['modifications'] = $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'], true);
		$data['events'] = $this->url->link('marketplace/event', 'user_token=' . $this->session->data['user_token'], true);
		$data['services'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/services', 'user_token=' . $this->session->data['user_token'], true), ENT_QUOTES, 'UTF-8');

		if (!$this->checkPrerequisites()) {
			$this->showErrorPage($data);
			return;
		}

		$this->checkVersion(true);

		$data['update_pending'] = !$this->checkVersion();

		if (!$data['update_pending']) {
			$this->updateEventHooks();
		}

		$data['ssl'] = (
				(int)$this->config->get('config_secure') ||
				isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ||
				!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ||
				!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'
			) ? 's' : '';

		# Loop through all settings for the post/config values
		foreach (array_keys(self::$config_defaults) as $setting) {
			if (isset($this->request->post[$setting])) {
				$data[$setting] = $this->request->post[$setting];
			} else {
				$data[$setting] = $this->config->get($setting);
				if ($data[$setting] === null) {
					if (!isset($this->alert['warning']['unsaved']) && $this->checkVersion())  {
						$this->alert['warning']['unsaved'] = $this->language->get('error_unsaved_settings');
					}
					if (isset(self::$config_defaults[$setting])) {
						$data[$setting] = self::$config_defaults[$setting];
					}
				}
			}
		}

		$data['installed_version'] = $this->installedVersion();

		foreach (self::$column_defaults as $page => $columns) {
			$conf = $this->config->get($page);
			if (!is_array($conf)) {
				if (!isset($this->alert['warning']['unsaved']) && $this->checkVersion())  {
					$this->alert['warning']['unsaved'] = $this->language->get('error_unsaved_settings');
				}
				$conf = $columns;
			}

			foreach ($columns as $column => $attributes) {
				if (!isset($conf[$column])) {
					if (!isset($this->alert['warning']['unsaved']) && $this->checkVersion())  {
						$this->alert['warning']['unsaved'] = $this->language->get('error_unsaved_settings');
					}
					$conf[$column] = $attributes;
				}

				foreach ($attributes as $key => $value) {
					if (!isset($conf[$column][$key])) {
						if (!isset($this->alert['warning']['unsaved']) && $this->checkVersion())  {
							$this->alert['warning']['unsaved'] = $this->language->get('error_unsaved_settings');
						}
						$conf[$column][$key] = $value;
					}
					switch ($key) {
						case 'display':
						case 'index':
							break;
						default:
							if ($conf[$column][$key] != $value) {
								if (!isset($this->alert['warning']['unsaved']) && $this->checkVersion())  {
									$this->alert['warning']['unsaved'] = $this->language->get('error_unsaved_settings');
								}
							}
							break;
					}
				}

				if (array_diff(array_keys($conf[$column]), array_keys($columns[$column])) && !isset($this->alert['warning']['unsaved']) && $this->checkVersion()) {
					$this->alert['warning']['unsaved'] = $this->language->get('error_unsaved_settings');
				}
			}

			if (array_diff(array_keys($conf), array_keys($columns)) && !isset($this->alert['warning']['unsaved']) && $this->checkVersion()) {
				$this->alert['warning']['unsaved'] = $this->language->get('error_unsaved_settings');
			}
		}

		if (isset($this->session->data['error'])) {
			$this->error = $this->session->data['error'];

			unset($this->session->data['error']);
		}

		if (isset($this->error['warning'])) {
			$this->alert['warning']['warning'] = $this->error['warning'];
		}

		if (isset($this->error['error'])) {
			$this->alert['error']['error'] = $this->error['error'];
		}

		if (isset($this->session->data['success'])) {
			$this->alert['success']['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		}

		$this->document->addStyle('view/stylesheet/pqe/module.min.css?v=' . EXTENSION_VERSION);

		$this->document->addScript('view/javascript/pqe/module.min.js?v=' . EXTENSION_VERSION);

		$data['errors'] = $this->error;

		$data['user_token'] = $this->session->data['user_token'];

		$data['alerts'] = $this->alert;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$template = 'extension/module/product_quick_edit';

		$this->response->setOutput($this->load->view($template, $data));
	}

	// Catalog > Products
	public function view() {
		return $this->load->controller('extension/module/catalog/product');
	}

	public function delete() {
		return $this->load->controller('extension/module/catalog/product/delete');
	}

	public function copy() {
		return $this->load->controller('extension/module/catalog/product/copy');
	}

	public function settings() {
		return $this->load->controller('extension/module/catalog/product/settings');
	}

	public function data() {
		return $this->load->controller('extension/module/catalog/product/data');
	}

	public function filter() {
		return $this->load->controller('extension/module/catalog/product/filter');
	}

	public function load() {
		return $this->load->controller('extension/module/catalog/product/load');
	}

	public function reload() {
		return $this->load->controller('extension/module/catalog/product/reload');
	}

	public function update() {
		return $this->load->controller('extension/module/catalog/product/update');
	}

	public function clear_cache() {
		return $this->load->controller('extension/module/catalog/product/clear_cache');
	}

	// Other
	public function install() {
		$this->load->language('extension/module/product_quick_edit');

		$this->registerEventHooks();

		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('module_product_quick_edit', array_merge(self::$config_defaults, self::$column_defaults));
	}

	public function uninstall() {
		$this->load->language('extension/module/product_quick_edit');

		$this->removeEventHooks();

		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_product_quick_edit');
	}

	public function upgrade() {
		$this->load->language('extension/module/product_quick_edit');

		$ajax_request = isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

		$response = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateUpgrade()) {
			$this->load->model('setting/setting');

			$settings = array();

			// Go over all settings, add new values and remove old ones
			foreach (self::$config_defaults as $setting => $default) {
				$value = $this->config->get($setting);
				if ($value === null) {
					$settings[$setting] = $default;
				} else {
					$settings[$setting] = $value;
				}
			}

			foreach (self::$column_defaults as $page => $columns) {
				$setting = array();

				$conf = $this->config->get($page);

				if ($conf === null || !is_array($conf)) {
					$conf = $columns;
				}

				foreach ($columns as $column => $values) {
					$setting[$column] = array();

					foreach ($values as $key => $value) {
						if (!isset($conf[$column][$key])) {
							$setting[$column][$key] = $value;
						} else {
							$setting[$column][$key] = $conf[$column][$key];
						}
					}
				}

				$settings[$page] = $setting;
			}

			$settings['module_product_quick_edit_installed_version'] = EXTENSION_VERSION;

			$this->model_setting_setting->editSetting('module_product_quick_edit', $settings);

			$this->updateEventHooks();

			$this->session->data['success'] = sprintf($this->language->get('text_success_upgrade'), EXTENSION_VERSION);
			$this->alert['success']['upgrade'] = sprintf($this->language->get('text_success_upgrade'), EXTENSION_VERSION);

			$response['success'] = true;
			$response['reload'] = true;
		}

		$response = array_merge($response, array("errors" => $this->error), array("alerts" => $this->alert));

		if (!$ajax_request) {
			$this->session->data['errors'] = $this->error;
			$this->session->data['alerts'] = $this->alert;
			$this->response->redirect($this->url->link('extension/module/product_quick_edit', 'user_token=' . $this->session->data['user_token'], true));
		} else {
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
			return;
		}
	}

	public function services() {
		$this->load->language('extension/module/product_quick_edit');

		$services = base64_decode($this->config->get('module_product_quick_edit_services'));
		$response = json_decode($services, true);
		$force = isset($this->request->get['force']) && (int)$this->request->get['force'];

		if ($response && isset($response['expires']) && $response['expires'] >= strtotime("now") && !$force) {
			$response['cached'] = true;
		} else {
			$url = "https://www.opencart.ee/services/?eid=" . EXTENSION_ID . "&info=true&general=true&currency=" . $this->config->get('config_currency');
			$hostname = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '' ;

			if (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLOPT_TIMEOUT, 60);
				curl_setopt($ch, CURLOPT_USERAGENT, base64_encode("curl " . EXTENSION_ID));
				curl_setopt($ch, CURLOPT_REFERER, $hostname);
				$json = curl_exec($ch);
			} else {
				$json = false;
			}

			if ($json !== false) {
				$this->load->model('setting/setting');
				$settings = $this->model_setting_setting->getSetting('module_product_quick_edit');
				$settings['module_product_quick_edit_services'] = base64_encode($json);
				$this->model_setting_setting->editSetting('module_product_quick_edit', $settings);
				$response = json_decode($json, true);
			} else {
				$response = array();
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
	}

	// Image
	public function image() {
		$this->load->model('tool/image');

		if (isset($this->request->get['size'])) {
			$width = $height = (int)$this->request->get['size'];
		} else if (isset($this->request->get['width']) && isset($this->request->get['height'])) {
			$width = (int)$this->request->get['width'];
			$height = (int)$this->request->get['height'];
		} else {
			$width = $height = 100;
		}

		if (isset($this->request->get['image'])) {
			$this->response->setOutput($this->model_tool_image->resize(html_entity_decode($this->request->get['image'], ENT_QUOTES, 'UTF-8'), $width, $height));
		}
	}

	// Event hooks
	public function product_form_hook(&$route, &$data) {
		if ($this->config->get('module_product_quick_edit_status')) {
			if (!(int)$this->config->get('module_product_quick_edit_display_in_menu_as') || isset($this->request->get['pqer'])) {
				$this->session->data['pqe_redirect'] = 'extension/module/product_quick_edit/view';
			}
		}
	}

	public function product_edit_hook($route='', $data=array(), $output=null) {
		if (is_array($data) && !empty($data[0])) {
			$product_id = (int)$data[0];
		} else {
			$product_id = null;
		}

		if (is_array($data) && !empty($data[1])) {
			$data = $data[1];
		} else {
			$data = null;
		}

		if ($product_id && !empty($data) && $this->config->get('module_product_quick_edit_status')) {
			$this->load->model('extension/module/product_quick_edit');
			$this->model_extension_module_product_quick_edit->updateProductCache($product_id, $data);
		}
	}

	public function product_list_hook(&$route, &$data) {
		if ($this->config->get('module_product_quick_edit_status') && (!(int)$this->config->get('module_product_quick_edit_display_in_menu_as') || !empty($this->session->data['pqe_redirect']))) {
			if (!empty($this->session->data['pqe_redirect'])) {
				$route = $this->session->data['pqe_redirect'];
				unset($this->session->data['pqe_redirect']);
			} else {
				$route = 'extension/module/product_quick_edit/view';
			}
			$this->response->redirect($this->url->link($route, 'user_token=' . $this->session->data['user_token'] . "&dTc=1", true));
		}
	}

	public function menu_hook(&$route, &$data) {
		if ($this->config->get('module_product_quick_edit_status') && (int)$this->config->get('module_product_quick_edit_display_in_menu_as') && $this->user->hasPermission('access', 'extension/module/product_quick_edit')) {
			$this->load->language('extension/module/product_quick_edit');
			foreach ($data['menus'] as $l1_key => $l1_menu) {
				if (isset($l1_menu['id']) && $l1_menu['id'] == 'menu-catalog') {
					foreach ($l1_menu['children'] as $l2_key => $l2_menu) {
						if (strpos($l2_menu['href'], "route=catalog/product&") !== FALSE) {
							array_splice($l1_menu['children'], $l2_key + 1, 0, array(array(
								'name'      => $this->language->get('text_products_qe'),
								'href'      => $this->url->link('extension/module/product_quick_edit/view', 'user_token=' . $this->session->data['user_token'] . "&dTc=1", true),
								'children'  => array()
							)));

							$data['menus'][$l1_key]['children'] = $l1_menu['children'];
						}
					}
				}
			}
		}
	}

	public function clear_cache_hook($route, $data, $output) {
		if ($this->config->get('module_product_quick_edit_status')) {
			$this->log->write("clear_hook: " . $route);
			if (strpos($route, "catalog/product") !== false) {
				$this->cache->delete('pqe.products');
			} else if (strpos($route, "catalog/manufacturer") !== false) {
				$this->cache->delete('manufacturers');
			} else if (strpos($route, "catalog/download") !== false) {
				$this->cache->delete('downloads');
			} else if (strpos($route, "catalog/filter") !== false) {
				$this->cache->delete('filters');
			}
		}
	}

	protected function showErrorPage($data = array()) {
		$this->document->addStyle('view/stylesheet/pqe/module.min.css?v=' . EXTENSION_VERSION);

		$data['alerts'] = $this->alert;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$template = 'extension/module/product_quick_edit_error';

		$this->response->setOutput($this->load->view($template, $data));
	}

	// Private methods
	private function redirect($method) {
		return $this->load->controller('extension/module/catalog/product/' . $method);
	}

	private function registerEventHooks() {
		$this->load->model('extension/module/product_quick_edit');
		$this->load->model('setting/event');

		if (isset($this->model_extension_module_product_quick_edit->getEventByCodeTriggerAction) && is_callable($this->model_extension_module_product_quick_edit->getEventByCodeTriggerAction)) {
			foreach (self::$event_hooks as $code => $hook) {
				$event = $this->model_extension_module_product_quick_edit->getEventByCodeTriggerAction($code, $hook['trigger'], $hook['action']);

				if (!$event) {
					$this->model_setting_event->addEvent($code, $hook['trigger'], $hook['action']);
				}
			}
		} else {
			$this->alert['warning']['ocmod'] = $this->language->get('error_ocmod_script');
		}
	}

	private function removeEventHooks() {
		$this->load->model('setting/event');

		foreach (self::$event_hooks as $code => $hook) {
			$this->model_setting_event->deleteEventByCode($code);
		}
	}

	private function updateEventHooks() {
		$this->load->model('extension/module/product_quick_edit');
		$this->load->model('setting/event');

		if (isset($this->model_extension_module_product_quick_edit->getEventByCodeTriggerAction) && is_callable($this->model_extension_module_product_quick_edit->getEventByCodeTriggerAction)) {
			foreach (self::$event_hooks as $code => $hook) {
				$event = $this->model_extension_module_product_quick_edit->getEventByCodeTriggerAction($code, $hook['trigger'], $hook['action']);

				if (!$event) {
					$this->model_setting_event->addEvent($code, $hook['trigger'], $hook['action']);

					if (empty($this->alert['success']['hooks_updated'])) {
						$this->alert['success']['hooks_updated'] = $this->language->get('text_success_hooks_update');
					}
				}
			}

			// Delete old triggers
			$query = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "event WHERE `code` LIKE 'admin_module_product_quick_edit_%'");
			$events = array_keys(self::$event_hooks);

			foreach ($query->rows as $row) {
				if (!in_array($row['code'], $events)) {
					$this->model_setting_event->deleteEventByCode($row['code']);

					if (empty($this->alert['success']['hooks_updated'])) {
						$this->alert['success']['hooks_updated'] = $this->language->get('text_success_hooks_update');
					}
				}
			}
		} else {
			$this->alert['warning']['ocmod'] = $this->language->get('error_ocmod_script');
		}
	}

	protected function checkPrerequisites() {
		$errors = false;

		$this->load->language('extension/module/product_quick_edit', 'pqe');

		if (!$this->config->get('pqe_plus_ocmod_script_working')) {
			$errors = true;
			$this->alert['error']['ocmod'] = $this->language->get('pqe')->get('error_ocmod_script');
		} else if ($this->checkVersion() && $this->installedVersion() != $this->config->get('pqe_plus_version')) {
			$this->alert['warning']['ocmod_cache'] = sprintf($this->language->get('pqe')->get('error_ocmod_cache'), $this->url->link('marketplace/modification/refresh', 'user_token=' . $this->session->data['user_token'], true));
		}

		return !$errors;
	}

	protected function checkVersion($display_error = false) {
		$errors = false;

		$installed_version = $this->installedVersion();

		if ($installed_version != EXTENSION_VERSION) {
			$errors = true;

			if ($display_error) {
				$this->alert['info']['version'] = sprintf($this->language->get('error_version'), EXTENSION_VERSION);
			}
		}

		return !$errors;
	}

	private function validate() {
		$errors = false;

		if (!$this->user->hasPermission('modify', 'extension/module/product_quick_edit')) {
			$errors = true;
			$this->alert['error']['permission'] = $this->language->get('error_permission');
		}

		if (!$errors) {
			return $this->checkPrerequisites() && $this->checkVersion();
		} else {
			return false;
		}
	}

	private function validateForm(&$data) {
		$errors = false;

		if ($errors) {
			$errors = true;
			$this->alert['warning']['warning'] = $this->language->get('error_warning');
		}

		if (!$errors) {
			return $this->validate();
		} else {
			return false;
		}
	}

	private function validateUpgrade() {
		$errors = false;

		if (!$this->user->hasPermission('modify', 'extension/module/product_quick_edit')) {
			$errors = true;
			$this->alert['error']['permission'] = $this->language->get('error_permission');
		}

		return !$errors;
	}

	protected function installedVersion() {
		$installed_version = $this->config->get('module_product_quick_edit_installed_version');
		return $installed_version ? $installed_version : '1.7.1';
	}
}
