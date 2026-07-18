<?php
defined('TA_LOCAL') || define('TA_LOCAL', 100);

defined('TA_PREFETCH') || define('TA_PREFETCH', 1000);

/**
  * Validate date
  *
  **/
if (!function_exists("validate_date")) {
	function validate_date($date, $format = 'Y-m-d H:i:s') {
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}

/**
  * Sort columns by index key
  *
  **/
if (!function_exists('column_sort')) {
	function column_sort($a, $b) {
		if ($a['index'] == $b['index']) {
			return 0;
		}
		return ($a['index'] < $b['index']) ? -1 : 1;
	}
}

/**
  * Filter columns by display value
  *
  **/
if (!function_exists('column_display')) {
	function column_display($a) {
		return (isset($a['display'])) ? (int)$a['display'] : false;
	}
}

/**
  * Remaps an array keys to SQL id fields
  *
  **/
if (!function_exists('array_remap_key_to_id')) {
	function array_remap_key_to_id($key, $results) {
		$new_array = array();

		foreach ($results as $result) {
			if (isset($result[$key])) {
				$new_array[$result[$key]] = $result;
			}
		}

		return $new_array;
	}
}

class ControllerExtensionModuleCatalogProduct extends ControllerExtensionModuleProductQuickEdit {
	protected $start_time = 0;
	private $error = array();
	protected $alert = array(
		'error'     => array(),
		'warning'   => array(),
		'success'   => array(),
		'info'      => array()
	);

	public function __construct($registry) {
		parent::__construct($registry);
		global $execution_start_time;
		$this->start_time = $execution_start_time;

		if (!$this->config->get('module_product_quick_edit_installed') || !$this->config->get('module_product_quick_edit_status')) {
			$url = $this->urlParams();
			$this->response->redirect($this->url->link('catalog/product', $url, true));
		}
	}

	public function index() {
		$this->getBase();
	}

	public function delete() {
		$this->action('delete');
	}

	public function copy() {
		$this->action('copy');
	}

	public function settings() {
		$this->load->language('extension/module/catalog/product');

		$ajax_request = isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

		if ($ajax_request && $this->request->server['REQUEST_METHOD'] == 'POST') {
			$response = array(
				"values"    => array(),
				"reset"     => false
			);

			if ($this->validateSettings($this->request->post)) {
				$this->load->model('setting/setting');

				$settings = $this->model_setting_setting->getSetting('module_product_quick_edit');

				if (isset($this->request->post['module_product_quick_edit_items_per_page'])) {
					$settings['module_product_quick_edit_items_per_page'] = $this->request->post['module_product_quick_edit_items_per_page'];
					if ($settings['module_product_quick_edit_items_per_page'] != $this->request->post['module_product_quick_edit_items_per_page']) {
						$response['reset'] = true;
					}
				}

				if (isset($this->request->post['module_product_quick_edit_server_side_caching'])) {
					if ($settings['module_product_quick_edit_server_side_caching'] != $this->request->post['module_product_quick_edit_server_side_caching']) {
						$response['reload'] = true;
					}
					$settings['module_product_quick_edit_server_side_caching'] = $this->request->post['module_product_quick_edit_server_side_caching'];
				}

				if (isset($this->request->post['module_product_quick_edit_client_side_caching'])) {
					if ($settings['module_product_quick_edit_client_side_caching'] != $this->request->post['module_product_quick_edit_client_side_caching']) {
						$response['reload'] = true;
					}
					$settings['module_product_quick_edit_client_side_caching'] = $this->request->post['module_product_quick_edit_client_side_caching'];
				}

				if (isset($this->request->post['module_product_quick_edit_cache_size'])) {
					if ($settings['module_product_quick_edit_cache_size'] !=  $this->request->post['module_product_quick_edit_cache_size']) {
						$response['reload'] = true;
					}
					$settings['module_product_quick_edit_cache_size'] = $this->request->post['module_product_quick_edit_cache_size'];
				}

				if (isset($this->request->post['module_product_quick_edit_alternate_row_colour'])) {
					$settings['module_product_quick_edit_alternate_row_colour'] = $this->request->post['module_product_quick_edit_alternate_row_colour'];
				}

				if (isset($this->request->post['module_product_quick_edit_row_hover_highlighting'])) {
					$settings['module_product_quick_edit_row_hover_highlighting'] = $this->request->post['module_product_quick_edit_row_hover_highlighting'];
				}

				if (isset($this->request->post['module_product_quick_edit_highlight_status'])) {
					$settings['module_product_quick_edit_highlight_status'] = $this->request->post['module_product_quick_edit_highlight_status'];
				}

				if (isset($this->request->post['module_product_quick_edit_highlight_filtered_columns'])) {
					$settings['module_product_quick_edit_highlight_filtered_columns'] = $this->request->post['module_product_quick_edit_highlight_filtered_columns'];
				}

				if (isset($this->request->post['module_product_quick_edit_highlight_actions'])) {
					$settings['module_product_quick_edit_highlight_actions'] = $this->request->post['module_product_quick_edit_highlight_actions'];
				}

				if (isset($this->request->post['module_product_quick_edit_quick_edit_on'])) {
					if ($settings['module_product_quick_edit_quick_edit_on'] != $this->request->post['module_product_quick_edit_quick_edit_on']) {
						$response['reload'] = true;
					}
					$settings['module_product_quick_edit_quick_edit_on'] = $this->request->post['module_product_quick_edit_quick_edit_on'];
				}

				if (isset($this->request->post['module_product_quick_edit_price_relative_to'])) {
					$settings['module_product_quick_edit_price_relative_to'] = $this->request->post['module_product_quick_edit_price_relative_to'];
				}

				if (isset($this->request->post['module_product_quick_edit_list_view_image_width'])) {
					$settings['module_product_quick_edit_list_view_image_width'] = $this->request->post['module_product_quick_edit_list_view_image_width'];
				}

				if (isset($this->request->post['module_product_quick_edit_list_view_image_height'])) {
					$settings['module_product_quick_edit_list_view_image_height'] = $this->request->post['module_product_quick_edit_list_view_image_height'];
				}

				if (isset($this->request->post['module_product_quick_edit_filter_sub_category'])) {
					if ($settings['module_product_quick_edit_filter_sub_category'] !=  $this->request->post['module_product_quick_edit_filter_sub_category']) {
						$response['reset'] = true;
					}
					$settings['module_product_quick_edit_filter_sub_category'] = $this->request->post['module_product_quick_edit_filter_sub_category'];
				}

				if (isset($this->request->post['module_product_quick_edit_debug_mode'])) {
					$settings['module_product_quick_edit_debug_mode'] = $this->request->post['module_product_quick_edit_debug_mode'];
				}

				if (isset($this->request->post['module_product_quick_edit_search_bar'])) {
					$settings['module_product_quick_edit_search_bar'] = $this->request->post['module_product_quick_edit_search_bar'];
				}

				if (isset($this->request->post['module_product_quick_edit_batch_edit'])) {
					$settings['module_product_quick_edit_batch_edit'] = $this->request->post['module_product_quick_edit_batch_edit'];
				}

				if (isset($this->request->post['module_product_quick_edit_show_success_message'])) {
					$settings['module_product_quick_edit_show_success_message'] = $this->request->post['module_product_quick_edit_show_success_message'];
				}

				if (isset($this->request->post['module_product_quick_edit_default_sort'])) {
					if ($settings['module_product_quick_edit_default_sort'] != $this->request->post['module_product_quick_edit_default_sort']) {
						$response['reset'] = true;
						$response['reload'] = true;
					}
					$settings['module_product_quick_edit_default_sort'] = $this->request->post['module_product_quick_edit_default_sort'];
				}

				if (isset($this->request->post['module_product_quick_edit_default_order'])) {
					if ($settings['module_product_quick_edit_default_order'] != $this->request->post['module_product_quick_edit_default_order']) {
						$response['reset'] = true;
						$response['reload'] = true;
					}
					$settings['module_product_quick_edit_default_order'] = $this->request->post['module_product_quick_edit_default_order'];
				}

				// Loop through columns
				if (isset($this->request->post['index']['columns'])) {
					foreach ($settings['module_product_quick_edit_catalog_products'] as $column => $attr) {
						$display = (isset($this->request->post['display']['columns'][$column])) ? true : false;
						if ($settings['module_product_quick_edit_catalog_products'][$column]['display'] != $display) {
							$response['reload'] = true;
						}
						$settings['module_product_quick_edit_catalog_products'][$column]['display'] = $display;

						if (isset($this->request->post['index']['columns'][$column])) {
							if ($settings['module_product_quick_edit_catalog_products'][$column]['index'] != $this->request->post['index']['columns'][$column]) {
								$response['reload'] = true;
							}
							$settings['module_product_quick_edit_catalog_products'][$column]['index'] = $this->request->post['index']['columns'][$column];
						}
					}
				}

				// Loop through actions
				if (isset($this->request->post['index']['actions'])) {
					foreach ($settings['module_product_quick_edit_catalog_products_actions'] as $action => $attr) {
						$display = (isset($this->request->post['display']['actions'][$action])) ? true : false;
						if ($settings['module_product_quick_edit_catalog_products_actions'][$action]['display'] != $display) {
							$response['reload'] = true;
						}
						$settings['module_product_quick_edit_catalog_products_actions'][$action]['display'] = $display;

						if (isset($this->request->post['index']['actions'][$action])) {
							if ($settings['module_product_quick_edit_catalog_products_actions'][$action]['index'] != $this->request->post['index']['actions'][$action]) {
								$response['reload'] = true;
							}
							$settings['module_product_quick_edit_catalog_products_actions'][$action]['index'] = $this->request->post['index']['actions'][$action];
						}
					}
				}

				$this->model_setting_setting->editSetting('module_product_quick_edit', $settings);

				if (!empty($response['reload'])) {
					$this->session->data['success'] = $this->language->get('text_setting_updated');
				}
				$this->alert['success']['updated'] = $this->language->get('text_setting_updated');
			}

			$response = array_merge($response, array("errors" => $this->error), array("alerts" => $this->alert));

			$response['query_count'] = DB::$query_count;
			$response['page_time'] = microtime(true) - $this->start_time;

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
			return;
		}

		$this->response->redirect($this->url->link('extension/module/product_quick_edit/view', $this->urlParams(), true));
	}

	public function data() {
		$ajax_request = isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

		if ($ajax_request) {
			$this->load->language('extension/module/catalog/product');
			$this->load->model('extension/module/product_quick_edit');

			$all_columns = $this->config->get('module_product_quick_edit_catalog_products');

			uasort($all_columns, 'column_sort');
			$columns = array_filter($all_columns, 'column_display');

			$actions = $this->config->get('module_product_quick_edit_catalog_products_actions');

			uasort($actions, 'column_sort');
			$actions = array_filter($actions, 'column_display');

			$displayed_columns = array_keys($columns);
			$displayed_actions = array_keys($actions);

			$filter_data = array(
				'search'    => '',
				'filter'    => array(),
				'sort'      => array(),
				'start'     => '',
				'limit'     => '',
				'columns'   => $displayed_columns,
				'actions'   => $displayed_actions
			);

			/*
			 * Paging
			 */
			if (isset($this->request->post['start']) && isset($this->request->post['length']) && $this->request->post['length'] != '-1') {
				$filter_data['start'] = (int)$this->request->post['start'];
				$filter_data['limit'] = (int)$this->request->post['length'];
			}

			/*
			 * Ordering
			 */
			if (isset($this->request->post['order']) && count($this->request->post['order'])) {
				for ($i = 0, $len = count($this->request->post['order']); $i < $len; $i++) {
					// Convert the column index into the column data property
					$column_idx = intval($this->request->post['order'][$i]['column']);
					$request_column = $this->request->post['columns'][$column_idx];
					$column_name = $request_column['data'];

					// if ($request_column['orderable'] == 'true' && $columns[$displayed_columns[$column_idx]]['sort']) {
					if ($request_column['orderable'] == 'true' && $columns[$column_name]['sort']) {
						$filter_data['sort'][] = array(
							'column' => $columns[$column_name]['sort'],
							'order' => ($this->request->post['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC')
						);
					}
				}
			}

			/*
			 * Filtering
			 * NOTE this does not match the built-in DataTables filtering which does it
			 * word by word on any field. It would be possible to do it here, but performance
			 * on large databases would be very poor
			 */
			if (isset($this->request->post['search']) && isset($this->request->post['search']['value']) && $this->request->post['search']['value'] != '') {
				$filter_data['search'] = $this->request->post['search']['value'];
			}

			// Individual column filtering
			for ($i = 0, $len = count($this->request->post['columns']); $i < $len; $i++) {
				$request_column = $this->request->post['columns'][$i];
				$column_name = $request_column['data'];
				if (isset($request_column['searchable']) && $request_column['searchable'] == 'true' && isset($request_column['search']['value']) && $request_column['search']['value'] != '') {
					// $filter_data['filter'][$displayed_columns[$i]] = $this->request->post['sSearch_' . $i];
					$filter_data['filter'][$column_name] = $request_column['search']['value'];
				}
			}

			if (isset($this->request->post['filter_special_price'])) {
				$filter_data['filter']['special_price'] = $this->request->post['filter_special_price'];
			}

			if (in_array('image', $displayed_columns)) {
				$this->load->model('tool/image');
			}

			$results = $this->model_extension_module_product_quick_edit->getProducts($filter_data);

			$iFilteredTotal = $this->model_extension_module_product_quick_edit->getFilteredTotalProducts();
			$iTotal = $this->model_extension_module_product_quick_edit->getTotalProducts();

			// For price with tax
			$tax = new Cart\Tax($this->registry);

			if ($this->config->get('config_tax_default') == 'shipping') {
				$tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			}

			if ($this->config->get('config_tax_default') == 'payment') {
				$tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			}

			$tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

			/*
			 * Output
			 */
			$output = array(
				'draw'              => (int)$this->request->post['draw'],
				'recordsTotal'      => $iTotal,
				'recordsFiltered'   => $iFilteredTotal,
				'data'              => array(),
				// 'error'             => ''
			);

			if (in_array('view_in_store', $displayed_columns)) {
				$this->load->model('setting/store');
				$_stores = $this->model_setting_store->getStores(array());

				$stores = array(
					'0' => array(
						'store_id'  => 0,
						'name'      => $this->config->get('config_name'),
						'url'       => HTTP_CATALOG
					)
				);

				foreach ($_stores as $store) {
					$stores[$store['store_id']] = array(
						'store_id'  => $store['store_id'],
						'name'      => $store['name'],
						'url'       => $store['url']
					);
				}
			} else {
				$stores = array();
			}

			foreach ($results as $result) {
				$product = array();

				for ($i = 0, $len = count($displayed_columns); $i < $len; $i++) {
					switch ($displayed_columns[$i]) {
						case 'selector':
							$value = '';
							break;
						case 'download':
						case 'filter':
						case 'store':
						case 'category':
							$_ids = explode('_', $result[$displayed_columns[$i]]);
							$_texts = explode('<br/>', $result[$displayed_columns[$i] . '_text']);
							$_data = array();
							foreach ($_ids as $idx => $value) {
								try {
									$_data[] = array('id' => $value, 'text' => $_texts[$idx]);
								} catch (Exception $e) {
									$this->log->write("PQE: DB inconsistency in category data: p{$result['product_id']}:c{$value}");
								}
							}
							$product[$displayed_columns[$i] . '_data'] = $_data;
							$value = $_ids;
							break;
						case 'image':
							$w = (int)$this->config->get('module_product_quick_edit_list_view_image_width');
							$h = (int)$this->config->get('module_product_quick_edit_list_view_image_height');

							if (is_file(DIR_IMAGE . $result['image'])) {
								$image = $this->model_tool_image->resize($result['image'], $w, $h);
							} else {
								$image = $this->model_tool_image->resize('no_image.png', $w, $h);
							}

							$value = $result['image'];
							$product['image_thumb'] = $image;
							$product['image_alt'] = html_entity_decode(isset($result['image_alt']) ? $result['image_alt'] : $result['name'], ENT_QUOTES, 'UTF-8');
							$product['image_title'] = html_entity_decode(isset($result['image_title']) ? $result['image_title'] : $result['name'], ENT_QUOTES, 'UTF-8');
							break;
						case 'id':
							$value = $result['product_id'];
							break;
						case 'subtract':
						case 'shipping':
							$value = (int)$result[$displayed_columns[$i]];
							$product[$displayed_columns[$i] . '_text'] = $result[$displayed_columns[$i] . '_text'];
							break;
						case 'date_available':
							$date = new DateTime($result[$displayed_columns[$i]]);
							$value = $date->format('Y-m-d');
							// $product['date_available_text'] = $date->format($this->language->get('date_format_short'));
							$product['date_available_text'] = $date->format('Y-m-d');
							break;
						case 'date_added':
						case 'date_modified':
							$date = new DateTime($result[$displayed_columns[$i]]);
							$value = $date->format('Y-m-d H:i:s');
							// $product[$displayed_columns[$i] . '_text'] = $date->format($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'));
							$product[$displayed_columns[$i] . '_text'] = $date->format('Y-m-d H:i:s');
							break;
						case 'stock_status':
						case 'tax_class':
						case 'length_class':
						case 'weight_class':
						case 'manufacturer':
							$value = (int)$result[$displayed_columns[$i] . '_id'];
							$product[$displayed_columns[$i] . '_text'] = (is_null($result[$displayed_columns[$i] . '_text'])) ? '' : $result[$displayed_columns[$i] . '_text'];
							break;
						case 'quantity':
							$value = (int)$result['quantity'];
							break;
						case 'status':
							$value = (int)$result['status'];
							$product['status_text'] = $result['status_text'];

							if ($this->config->get('module_product_quick_edit_highlight_status')) {
								$product['status_class'] = (int)$result['status'] ? 'success' : 'danger';
							}
							break;
						case 'price':
							$product['special'] = $result['special_price'];
							$value = $result['price'];
							break;
						case 'gross_price':
							$product['gross_special'] = $result['special_price'] ? sprintf('%.4f',round((float)$tax->calculate($result['special_price'], $result['tax_class_id']), 4)) : null;
							$value = sprintf('%.4f',round((float)$tax->calculate($result['price'], $result['tax_class_id']), 4));
							break;
						case 'view_in_store':
							$product_stores = explode('_', $result['store_ids']);
							$_stores = array();

							foreach ($product_stores as $store) {
								if (!in_array($store, array_keys($stores)))
									continue;

								$_stores[] = array(
									'url' => $stores[$store]['url'] . 'index.php?route=product/product&product_id=' . $result['product_id'],
									'name' => $stores[$store]['name'],
								);
							}

							$value = $_stores;
							break;
						case 'action':

							$_buttons = array();

							foreach ($actions as $action => $attr) {
								switch ($action) {
									case 'edit':
										$_buttons[] = array(
											'type'  => $attr['type'],
											'action'=> $action,
											'title' => $this->language->get('action_' . $action),
											'url'   => html_entity_decode($this->url->link('catalog/product/edit', '&product_id=' . $result['product_id'] . '&user_token=' . $this->session->data['user_token'] . '&pqer=1', true), ENT_QUOTES, 'UTF-8'),
											'icon'  => 'pencil',
											'name'  => null,
											'rel'   => json_encode(array()),
											// 'data'  => 0,
											'class' => $attr['class'],
										);
										break;
									case 'view':
										$_buttons[] = array(
											'type'  => $attr['type'],
											'action'=> $action,
											'title' => $this->language->get('action_' . $action),
											'url'   => html_entity_decode(HTTP_CATALOG . 'index.php?route=product/product&product_id=' . $result['product_id'], ENT_QUOTES, 'UTF-8'),
											'icon'  => 'eye',
											'name'  => null,
											'rel'   => json_encode(array()),
											// 'data'  => 0,
											'class' => $attr['class'],
										);
										break;
									default:
										$_buttons[] = array(
											'type'  => $attr['type'],
											'action'=> $action,
											'title' => $this->language->get('action_' . $action),
											'url'   => null,
											'icon'  => null,
											'name'  => $this->language->get('action_' . $attr['short']),
											'rel'   => json_encode($attr['rel']),
											// 'data'  => isset($result[$action . '_exist']) ? (int)$result[$action . '_exist'] : 0,
											'class' => $attr['class'],
										);
										$product[$action . '_exist'] = isset($result[$action . '_exist']) ? (int)$result[$action . '_exist'] : 0;
										break;
								}
							}

							$value = $_buttons;
							break;
						default:
							$value = isset($result[$displayed_columns[$i]]) ? $result[$displayed_columns[$i]] : '';
							break;
					}

					$product[$displayed_columns[$i]] = $value;
				}

				$product['id'] = $result['product_id'];
				$product['DT_RowId'] = 'p_' . $result['product_id'];
				// $product['DT_RowClass'] = '';
				// $product['DT_RowData'] = '';

				$output['data'][] = $product;
			}

			$output['query_count'] = DB::$query_count;
			$output['page_time'] = microtime(true) - $this->start_time;

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($output));
		}
	}

	public function filter() {
		if ($this->request->server['REQUEST_METHOD'] == 'GET' && isset($this->request->get['type'])) {
			$response = array();
			switch ($this->request->get['type']) {
				case 'product':
					$this->load->model('extension/module/product_quick_edit');

					$results = array();

					if (isset($this->request->get['query'])) {
						if (is_array($this->request->get['query']) && isset($this->request->get['multiple'])) {
							$results = array();

							foreach ((array)$this->request->get['query'] as $value) {
								$result =  $this->model_extension_module_product_quick_edit->getProduct($value);
								$results[] = $result;
							}
						} else {
							$filter_data = array(
								'search'    => $this->request->get['query'],
								'filter'    => array(),
								'sort'      => array(),
								'start'     => '',
								'limit'     => '',
								'columns'   => array('name')
							);

							$results = $this->model_extension_module_product_quick_edit->getProducts($filter_data);
						}
					}

					foreach ($results as $result) {
						$result['name'] = html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8');
						$response[] = array(
							'value'     => $result['name'],
							'user_tokens'    => explode(' ', $result['name']),
							'id'        => $result['product_id'],
							'model'     => $result['model']
						);
					}
					break;
				case 'category':
					$this->load->model('catalog/category');

					if (isset($this->request->get['query'])) {
						if (is_array($this->request->get['query']) && isset($this->request->get['multiple'])) {
							$results = array();

							foreach ((array)$this->request->get['query'] as $value) {
								$result =  $this->model_catalog_category->getCategory($value);
								$result['name'] = $result['path'] ? $result['path'] . '&nbsp;&nbsp;&gt;&nbsp;&nbsp;' . $result['name'] : $result['name'];
								$results[] = $result;
							}
						} else {
							$filter_data = array(
								'filter_name' => $this->request->get['query'],
								'sort' => 'name'
							);

							$results = $this->model_catalog_category->getCategories($filter_data);

							if (stripos($this->language->get('text_none'), $this->request->get['query']) !== false) {
								$response[] = array(
										'value'     => $this->language->get('text_none'),
										'user_tokens'    => explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))),
										'id'        => '*',
										'path'      => '',
										'full_name' => $this->language->get('text_none')
									);
							}
						}
					} else {
						$results = $this->cache->get('category.all');

						if ($results === false || is_null($results)) {
							$results = $this->model_catalog_category->getCategories(array('sort' => 'name'));
							$this->cache->set('category.all', $results);
						}

						$response[] = array(
								'value'     => $this->language->get('text_none'),
								'user_tokens'    => array_merge(explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))), (array)trim($this->language->get('text_none'))),
								'id'        => '*',
								'path'      => '',
								'full_name' => $this->language->get('text_none')
							);
					}

					foreach ($results as $result) {
						$result['name'] = html_entity_decode(str_replace('&nbsp;', '', $result['name']), ENT_QUOTES, 'UTF-8');
						$parts = explode('>', $result['name']);
						$last_part = array_pop($parts);

						$response[] = array(
							'value'     => $last_part,
							'user_tokens'    => explode('>', $result['name']),
							'id'        => $result['category_id'],
							'path'      => $parts ? implode(' > ', $parts) . ' > ' : '',
							'full_name' => $result['name']
						);
					}
					break;
				case 'manufacturer':
					$this->load->model('catalog/manufacturer');

					if (isset($this->request->get['query'])) {
						$filter_data = array(
							'filter_name' => $this->request->get['query']
						);

						$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

						if (stripos($this->language->get('text_none'), $this->request->get['query']) !== false) {
							$response[] = array(
									'value'     => $this->language->get('text_none'),
									'user_tokens'    => explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))),
									'id'        => '*',
								);
						}
					} else {
						$results = $this->cache->get('manufacturers.all');

						if ($results === false || is_null($results)) {
							$results = $this->model_catalog_manufacturer->getManufacturers(array());
							$this->cache->set('manufacturers.all', $results);
						}

						$response[] = array(
								'value'     => $this->language->get('text_none'),
								'user_tokens'    => array_merge(explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))), (array)trim($this->language->get('text_none'))),
								'id'        => '*',
							);
					}

					foreach ($results as $result) {
						$result['name'] = html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8');
						$response[] = array(
							'value'     => $result['name'],
							'user_tokens'    => explode(' ', $result['name']),
							'id'        => $result['manufacturer_id'],
						);
					}
					break;
				case 'download':
					$this->load->model('catalog/download');

					if (isset($this->request->get['query'])) {
						if (is_array($this->request->get['query']) && isset($this->request->get['multiple'])) {
							$results = array();

							foreach ((array)$this->request->get['query'] as $value) {
								$result =  $this->model_catalog_download->getDownload($value);
								$results[] = $result;
							}
						} else {
							$filter_data = array(
								'filter_name' => $this->request->get['query']
							);

							$results = $this->model_catalog_download->getDownloads($filter_data);

							if (stripos($this->language->get('text_none'), $this->request->get['query']) !== false) {
								$response[] = array(
										'value'     => $this->language->get('text_none'),
										'user_tokens'    => explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))),
										'id'        => '*',
									);
							}
						}
					} else {
						$results = $this->cache->get('downloads.all');

						if ($results === false || is_null($results)) {
							$results = $this->model_catalog_download->getDownloads(array());
							$this->cache->set('downloads.all', $results);
						}

						$response[] = array(
								'value'     => $this->language->get('text_none'),
								'user_tokens'    => array_merge(explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))), (array)trim($this->language->get('text_none'))),
								'id'        => '*',
							);
					}

					foreach ($results as $result) {
						$result['name'] = html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8');
						$response[] = array(
							'value'     => $result['name'],
							'user_tokens'    => explode(' ', $result['name']),
							'id'        => $result['download_id'],
						);
					}
					break;
				case 'filter':
					$this->load->model('catalog/filter');
					$this->load->model('extension/module/product_quick_edit');

					if (isset($this->request->get['query'])) {
						if (is_array($this->request->get['query']) && isset($this->request->get['multiple'])) {
							$results = array();

							foreach ((array)$this->request->get['query'] as $value) {
								$result =  $this->model_catalog_filter->getFilter($value);
								$idx = null;
								foreach ($results as $key => $value) {
									if ($value['filter_group_id'] == $result['filter_group_id']) {
										$idx = $key;
										break;
									}
								}

								if (is_null($idx)) {
									$idx = count($results);
									$results[$idx] = array(
										'filter_group_id'   => $result['filter_group_id'],
										'name'              => $result['group'],
										'filters'           => array()
									);
								}

								$results[$idx]['filters'][] = array(
									'filter_id' => $result['filter_id'],
									'name'      => $result['name'],
								);
							}
						} else {
							$filter_data = array(
								'filter_name' => $this->request->get['query']
							);

							$results = $this->model_extension_module_product_quick_edit->getFiltersByGroup($filter_data);

							if (stripos($this->language->get('text_none'), $this->request->get['query']) !== false) {
								$response[] = array(
										'value'     => $this->language->get('text_none'),
										'user_tokens'    => explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))),
										'group'     => '',
										'id'        => '*',
										'full_name' => $this->language->get('text_none')
									);
							}
						}
					} else {
						$results = $this->cache->get('filters.all');

						if ($results === false || is_null($results)) {
							$results = $this->model_extension_module_product_quick_edit->getFiltersByGroup();
							$this->cache->set('filters.all', $results);
						}

						$response[] = array(
								'value'     => $this->language->get('text_none'),
								'user_tokens'    => array_merge(explode(' ', trim(str_replace('---', '', $this->language->get('text_none')))), (array)trim($this->language->get('text_none'))),
								'group'     => '',
								'id'        => '*',
								'full_name' => $this->language->get('text_none')
							);
					}

					foreach ($results as $fg) {
						foreach ($fg['filters'] as $f) {
							$name = strip_tags(html_entity_decode($fg['name'] . ' > ' . $f['name'], ENT_QUOTES, 'UTF-8'));
							$response[] = array(
								'value'     => $f['name'],
								'user_tokens'    => explode(' ', strip_tags(html_entity_decode($fg['name'] . ' ' . $f['name'], ENT_QUOTES, 'UTF-8'))),
								'group'     => $fg['name'] . ' > ',
								'group_name'=> $fg['name'],
								'id'        => $f['filter_id'],
								'full_name' => $name
							);
						}
					}
					break;
				case 'attributes':
					$this->load->model('extension/module/product_quick_edit');

					$results = array();
					if (isset($this->request->get['query'])) {
						if (is_array($this->request->get['query']) && isset($this->request->get['multiple'])) {
							// TODO: if needed
						} else {
							$filter_data = array(
								'filter_name' => $this->request->get['query']
							);

							$results = $this->model_extension_module_product_quick_edit->getAttributesByGroup($filter_data);
						}
					} else {
						// TODO: if needed
					}

					foreach ($results as $ag) {
						foreach ($ag['attributes'] as $a) {
							$name = strip_tags(html_entity_decode($ag['name'] . ' > ' . $a['name'], ENT_QUOTES, 'UTF-8'));
							$response[] = array(
								'value'     => $a['name'],
								'user_tokens'    => explode(' ', strip_tags(html_entity_decode($ag['name'] . ' ' . $a['name'], ENT_QUOTES, 'UTF-8'))),
								'group'     => $ag['name'],
								'group_name'=> $ag['name'],
								'id'        => $a['attribute_id'],
								'full_name' => $name
							);
						}
					}
					break;
				case 'name':
				case 'model':
				case 'sku':
				case 'upc':
				case 'ean':
				case 'jan':
				case 'isbn':
				case 'mpn':
				case 'location':
					$results = array();

					$this->load->model('extension/module/product_quick_edit');

					if (isset($this->request->get['query'])) {
						$results = $this->model_extension_module_product_quick_edit->filterKeywords($this->request->get['type'], $this->request->get['query']);
					}

					foreach ($results as $result) {
						$result = html_entity_decode($result, ENT_QUOTES, 'UTF-8');
						$response[] = array(
							'value'     => $result,
							'user_tokens'    => explode(' ', $result),
						);
					}
					break;
				case 'options':
					$this->load->language('catalog/option');

					$this->load->model('catalog/option');

					$this->load->model('tool/image');

					$results = array();

					if (isset($this->request->get['query'])) {
						if (is_array($this->request->get['query']) && isset($this->request->get['multiple'])) {
							// TODO: if needed
						} else {
							$filter_data = array(
								'filter_name' => $this->request->get['query']
							);

							$results = $this->model_catalog_option->getOptions($filter_data);
						}
					} else {
						// TODO: if needed
					}

					foreach ($results as $option) {
						$option_value_data = array();

						if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
							$option_values = $this->model_catalog_option->getOptionValues($option['option_id']);

							foreach ($option_values as $option_value) {
								if (is_file(DIR_IMAGE . $option_value['image'])) {
									$image = $this->model_tool_image->resize($option_value['image'], 50, 50);
								} else {
									$image = $this->model_tool_image->resize('no_image.png', 50, 50);
								}

								$option_value_data[] = array(
									'option_value_id' => $option_value['option_value_id'],
									'name'            => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
									'image'           => $image
								);
							}

							$sort_order = array();

							foreach ($option_value_data as $key => $value) {
								$sort_order[$key] = $value['name'];
							}

							array_multisort($sort_order, SORT_ASC, $option_value_data);
						}

						$type = '';

						if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
							$type = $this->language->get('text_choose');
						}

						if ($option['type'] == 'text' || $option['type'] == 'textarea') {
							$type = $this->language->get('text_input');
						}

						if ($option['type'] == 'file') {
							$type = $this->language->get('text_file');
						}

						if ($option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
							$type = $this->language->get('text_date');
						}

						$name = strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8'));
						$response[] = array(
							'value'         => $name,
							'user_tokens'        => explode(' ', strip_tags($name)),
							'category'      => $type,
							'type'          => $option['type'],
							'id'            => $option['option_id'],
							'option_value'  => $option_value_data
						);
					}
					break;
				default:
					break;
			}
		}

		// $response['query_count'] = DB::$query_count;
		// $response['page_time'] = microtime(true) - $this->start_time;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
	}

	public function load() {
		$this->load->language('extension/module/catalog/product');

		$response = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateLoadData($this->request->post)) {
			$languages = array();

			$this->load->model('catalog/product');
			$this->load->model('localisation/language');

			$langs = $this->model_localisation_language->getLanguages();
			foreach($langs as $lang) {
				unset($lang['image']);
				$languages[$lang['language_id']] = $lang;
			}

			$id = $this->request->post['id'];
			$column = $this->request->post['column'];
			$ids = array_diff(empty($this->request->post['ids']) ? array() : (array)$this->request->post['ids'], (array)$id);

			$product_info = $this->model_catalog_product->getProduct($id);
			$title_prefix = $product_info['name'] . " - ";

			$data['module_product_quick_edit_alternate_row_colour'] = $this->config->get('module_product_quick_edit_alternate_row_colour');
			$data['module_product_quick_edit_row_hover_highlighting'] = $this->config->get('module_product_quick_edit_row_hover_highlighting');

			$response['data'] = array();

			switch ($column) {
				case 'tag':
				case 'name':
					$result = $this->model_catalog_product->getProductDescriptions($id);

					foreach($languages as $language_id => $language) {
						$response['data'][] = array(
							'lang'  => $language_id,
							'value' => isset($result[$language_id][$column]) ? html_entity_decode($result[$language_id][$column], ENT_QUOTES, 'UTF-8') : '',
							'title' => $language['name'],
							'image' => "language/{$language['code']}/{$language['code']}.png"
						);
					}
					$response['success'] = true;
					break;
				case 'seo_urls':
					$data['languages'] = $languages;
					$data['product_id'] = $id;
					$data['column'] = $column;
					$data['product_seo_url'] = array();

					$this->load->model('setting/store');

					$data['stores'] = array();

					$data['stores']['0'] = array(
						'store_id' => 0,
						'name'     => $this->language->get('text_default')
					);

					$stores = $this->model_setting_store->getStores();

					foreach ($stores as $store) {
						$data['stores'][$store['store_id']] = array(
							'store_id' => $store['store_id'],
							'name'     => $store['name']
						);
					}

					$data['product_seo_url'] = $this->model_catalog_product->getProductSeoUrls($id);

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_seo_urls');
					$response['success'] = true;
					break;
				case 'attributes':
					$data['languages'] = $languages;

					$data['product_id'] = $id;
					$data['column'] = $column;
					$data['product_attributes'] = array();

					$this->load->model('catalog/attribute');

					$product_attributes = $this->model_catalog_product->getProductAttributes($id);

					foreach ($product_attributes as $product_attr) {
						$attribute_info = $this->model_catalog_attribute->getAttribute($product_attr['attribute_id']);

						if ($attribute_info) {
							$product_attribute = array(
								'attribute_id'  => $product_attr['attribute_id'],
								'name'          => html_entity_decode($attribute_info['name'], ENT_QUOTES, 'UTF-8'),
								'values'        => array()
							);

							foreach ($product_attr['product_attribute_description'] as $language_id => $value) {
								if (!isset($languages[$language_id])) continue;
								$product_attribute['values'][] = array(
									'value'         => html_entity_decode($value['text'], ENT_QUOTES, 'UTF-8'),
									'language_id'   => $language_id
								);
							}

							$data['product_attributes'][] = $product_attribute;
						}
					}

					$data['typeahead'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=attributes&query=%QUERY' . $this->urlParams(), true), ENT_QUOTES, 'UTF-8');

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_attributes');
					$response['success'] = true;
					break;
				case 'discounts':
					$data['product_id'] = $id;
					$data['column'] = $column;

					$data['use_gross_price'] = $this->config->get('module_product_quick_edit_use_gross_price_for_actions');

					$this->load->model('customer/customer_group');
					$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

					$data['product_discounts'] = $this->model_catalog_product->getProductDiscounts($id);

					if ($data['use_gross_price']) {
						$tax = new Cart\Tax($this->registry);

						if ($this->config->get('config_tax_default') == 'shipping') {
							$tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
						}

						if ($this->config->get('config_tax_default') == 'payment') {
							$tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
						}

						$tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

						$this->load->model('catalog/product');

						$product = $this->model_catalog_product->getProduct($id);

						foreach ($data['product_discounts'] as $key => &$value) {
							$value['price'] = sprintf('%.4f',round((float)$tax->calculate($value['price'], $product['tax_class_id']), 4));
						}
					}

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_discounts');
					$response['success'] = true;
					break;
				case 'specials':
					$data['product_id'] = $id;
					$data['column'] = $column;

					$data['use_gross_price'] = $this->config->get('module_product_quick_edit_use_gross_price_for_actions');

					$this->load->model('customer/customer_group');
					$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

					$data['product_specials'] = $this->model_catalog_product->getProductSpecials($id);

					if ($data['use_gross_price']) {
						$tax = new Cart\Tax($this->registry);

						if ($this->config->get('config_tax_default') == 'shipping') {
							$tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
						}

						if ($this->config->get('config_tax_default') == 'payment') {
							$tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
						}

						$tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

						$this->load->model('catalog/product');

						$product = $this->model_catalog_product->getProduct($id);

						foreach ($data['product_specials'] as $key => &$value) {
							$value['price'] = sprintf('%.4f',round((float)$tax->calculate($value['price'], $product['tax_class_id']), 4));
						}
					}

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_specials');
					$response['success'] = true;
					break;
				case 'filters':
					// $data['text_select_filter'] = $this->language->get('text_select_filter');

					$data['product_id'] = $id;
					$data['column'] = $column;

					$this->load->model('extension/module/product_quick_edit');

					$results = $this->cache->get('filters.all');

					if ($results === false || is_null($results)) {
						$results = $this->model_extension_module_product_quick_edit->getFiltersByGroup();
						$this->cache->set('filters.all', $results);
					}

					$data['filters'] = $results;
					$data['product_filters'] = $this->model_catalog_product->getProductFilters($id);

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_filters');
					$response['success'] = true;
					break;
				case 'recurrings':
					$data['product_id'] = $id;
					$data['column'] = $column;

					$this->load->model('catalog/recurring');
					$this->load->model('customer/customer_group');
					$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

					$data['recurrings'] = $this->model_catalog_recurring->getRecurrings();
					$data['product_recurrings'] = $this->model_catalog_product->getRecurrings($id);

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_recurrings');
					$response['success'] = true;
					break;
				case 'related':
					$data['product_id'] = $id;
					$data['column'] = $column;

					$data['user_token'] = $this->session->data['user_token'];
					$data['filter'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', '', true), ENT_QUOTES, 'UTF-8');

					$results = $this->model_catalog_product->getProductRelated($id);
					$data['product_related'] = array();

					foreach ($results as $product_id) {
						$related_info = $this->model_catalog_product->getProduct($product_id);

						if ($related_info) {
							$data['product_related'][$product_id] = array(
								'product_id' => $related_info['product_id'],
								'name'       => html_entity_decode($related_info['name'], ENT_QUOTES, 'UTF-8'),
								'model'      => $related_info['model']
							);
						}
					}

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_related');
					$response['success'] = true;
					break;
				case 'descriptions':
					$data['default_language'] = $this->config->get('config_language_id');

					$data['product_id'] = $id;
					$data['column'] = $column;

					$data['languages'] = $languages;
					$data['product_description'] = array();
					$description = $this->model_catalog_product->getProductDescriptions($id);
					foreach ($description as $key => $value) {
						$value['description'] = html_entity_decode($value['description']);
						$data['product_description'][$key] = $value;
					}

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_descriptions');
					$response['success'] = true;
					break;
				case 'images':
					$additional_image_width = 100;
					$additional_image_height = 100;

					$data['product_id'] = $id;
					$data['column'] = $column;

					$this->load->model('tool/image');

					$data['user_token'] = $this->session->data['user_token'];
					$data['additional_image_width'] = $additional_image_width;
					$data['additional_image_height'] = $additional_image_height;

					$product_images = $this->model_catalog_product->getProductImages($id);
					$data['no_image'] = $this->model_tool_image->resize('no_image.png', $additional_image_width, $additional_image_height);
					$data['product_images'] = array();

					foreach ($product_images as $product_image) {
						if (is_file(DIR_IMAGE . $product_image['image'])) {
							$image = $product_image['image'];
						} else {
							$image = 'no_image.png';
						}

						$data['product_images'][] = array(
							'image'      => $image,
							'thumb'      => $this->model_tool_image->resize($image, $additional_image_width, $additional_image_height),
							'sort_order' => $product_image['sort_order']
						);
					}

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_images');
					$response['success'] = true;
					break;
				case 'options':
					$data['product_id'] = $id;
					$data['column'] = $column;

					$data['use_gross_price'] = $this->config->get('module_product_quick_edit_use_gross_price_for_actions');

					$this->load->model('catalog/option');

					$product_options = $this->model_catalog_product->getProductOptions($id);
					$data['product_options'] = array();

					if ($data['use_gross_price']) {
						$tax = new Cart\Tax($this->registry);

						if ($this->config->get('config_tax_default') == 'shipping') {
							$tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
						}

						if ($this->config->get('config_tax_default') == 'payment') {
							$tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
						}

						$tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

						$this->load->model('catalog/product');

						$product = $this->model_catalog_product->getProduct($id);
					}

					foreach ($product_options as $product_option) {
						$product_option_value_data = array();

						if (isset($product_option['product_option_value'])) {
							foreach ($product_option['product_option_value'] as $product_option_value) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'quantity'                => $product_option_value['quantity'],
									'subtract'                => $product_option_value['subtract'],
									'price'                   => $data['use_gross_price'] ? sprintf('%.4f',round((float)$tax->calculate($product_option_value['price'], $product['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), 4)) : $product_option_value['price'],
									'price_prefix'            => $product_option_value['price_prefix'],
									'points'                  => $product_option_value['points'],
									'points_prefix'           => $product_option_value['points_prefix'],
									'weight'                  => $product_option_value['weight'],
									'weight_prefix'           => $product_option_value['weight_prefix']
								);
							}
						}

						$data['product_options'][] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => html_entity_decode($product_option['name'], ENT_QUOTES, 'UTF-8'),
							'type'                 => $product_option['type'],
							'value'                => isset($product_option['value']) ? html_entity_decode($product_option['value'], ENT_QUOTES, 'UTF-8') : '',
							'required'             => $product_option['required']
						);
					}

					$data['option_values'] = array();

					foreach ($data['product_options'] as $product_option) {
						if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
							if (!isset($data['option_values'][$product_option['option_id']])) {
								$data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);

								foreach ((array)$data['option_values'][$product_option['option_id']] as $idx => $value) {
									if (isset($data['option_values'][$product_option['option_id']][$idx]['name'])) {
										$data['option_values'][$product_option['option_id']][$idx]['name'] = html_entity_decode($data['option_values'][$product_option['option_id']][$idx]['name'], ENT_QUOTES, 'UTF-8');
									}
								}
							}
						}
					}

					$data['typeahead'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=options&query=%QUERY' . $this->urlParams(), true), ENT_QUOTES, 'UTF-8');

					$template = 'extension/module/catalog/product_qe_form';

					$response['data'] = $this->load->view($template, $data);
					$response['title'] = $title_prefix . $this->language->get('action_options');
					$response['success'] = true;
					break;
				default:
					$this->alert['error']['load'] = $this->language->get('error_load_data');
					break;
			}
		}
		$response = array_merge($response, array("errors" => $this->error), array("alerts" => $this->alert));

		$response['query_count'] = DB::$query_count;
		$response['page_time'] = microtime(true) - $this->start_time;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
	}

	public function reload() {
		$this->load->language('extension/module/catalog/product');

		$response = array('success' => false);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateReloadData($this->request->post)) {
			$response['values'] = array();

			foreach ($this->request->post['data'] as $column => $products) {
				foreach ($products as $id) {
					switch ($column) {
						case 'price':
							$this->load->model('catalog/product');

							$product = $this->model_catalog_product->getProduct($id);

							$response['values'][$id][$column] = sprintf('%.4f',round((float)$product['price'], 4));
							// $response['values'][$id][$column] = $this->currency->format($product['price'], $this->config->get('config_currency'));

							$special = false;
							$product_specials = $this->model_catalog_product->getProductSpecials($id);

							foreach ($product_specials  as $product_special) {
								if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
									$special = $product_special['price'];
									break;
								}
							}

							if ($special) {
								$response['values'][$id]['special'] = sprintf('%.4f',round((float)$special, 4));
								// $response['values'][$id]['special'] = $this->currency->format($special, $this->config->get('config_currency'));
							} else {
								$response['values'][$id]['special'] = null;
							}
							$response['success'] = true;
							break;
						case 'gross_price':
							$tax = new Cart\Tax($this->registry);

							if ($this->config->get('config_tax_default') == 'shipping') {
								$tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
							}

							if ($this->config->get('config_tax_default') == 'payment') {
								$tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
							}

							$tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

							$this->load->model('catalog/product');

							$product = $this->model_catalog_product->getProduct($id);

							$response['values'][$id][$column] = sprintf('%.4f',round((float)$tax->calculate($product['price'], $product['tax_class_id']), 4));
							// $response['values'][$id][$column] = $this->currency->format($tax->calculate($product['price'], $product['tax_class_id']), $this->config->get('config_currency'));

							$special = false;
							$product_specials = $this->model_catalog_product->getProductSpecials($id);

							foreach ($product_specials  as $product_special) {
								if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
									$special = $product_special['price'];
									break;
								}
							}

							if ($special) {
								$response['values'][$id]['gross_special'] = sprintf('%.4f',round((float)$tax->calculate($special, $product['tax_class_id']), 4));
								// $response['values'][$id]['gross_special'] = $this->currency->format($tax->calculate($special, $product['tax_class_id']), $this->config->get('config_currency'));
							} else {
								$response['values'][$id]['gross_special'] = null;
							}
							$response['success'] = true;
							break;
						case 'filter':
							$this->load->model('catalog/product');

							$_filters = $this->cache->get('filters.all');

							if ($_filters === false || is_null($_filters)) {
								$this->load->model('extension/module/product_quick_edit');
								$_filters = $this->model_extension_module_product_quick_edit->getFiltersByGroup();
								$this->cache->set('filters.all', $_filters);
							}

							$product_filters = $this->model_catalog_product->getProductFilters($id);

							$filters = array();

							foreach ($_filters as $fg) {
								foreach ($fg['filters'] as $filter) {
									if (in_array($filter['filter_id'], (array)$product_filters))
										$filters[] = array('id' => (int)$filter['filter_id'], 'text' => strip_tags(html_entity_decode($fg['name'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8')));
								}
							}
							$response['values'][$id][$column] = $product_filters;
							$response['values'][$id]['filter_data'] = $filters;
							$response['success'] = true;
							break;
						case 'filters':
							$this->load->model('catalog/product');

							$product_filters = $this->model_catalog_product->getProductFilters($id);

							$response['values'][$id]['filters_exist'] = (int)$this->config->get('module_product_quick_edit_highlight_actions') * count($product_filters);
							$response['success'] = true;
							break;
						default:
							$this->alert['error']['load'] = $this->language->get('error_load_data');
							break;
					}
				}
			}
		}

		$response = array_merge($response, array("errors" => $this->error), array("alerts" => $this->alert));

		$response['query_count'] = DB::$query_count;
		$response['page_time'] = microtime(true) - $this->start_time;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
	}

	public function update() {
		$this->load->language('extension/module/catalog/product');

		$this->load->model('extension/module/product_quick_edit');

		$response = array('success' => false);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateUpdateData($this->request->post)) {
			$id = (array)$this->request->post['id'];
			$column = $this->request->post['column'];
			$value = $this->request->post['value'];
			$lang_id = $this->config->get('config_language_id');
			$expression = !is_array($value) && strpos(trim($value), "#") === 0 && preg_match('/^#\s*(?P<operator>[+-\/\*])\s*(?P<operand>-?\d+\.?\d*)(?P<percent>%)?$/', trim($value)) === 1;
			$add = isset($this->request->post['add']) ? $this->request->post['add'] : false;
			$remove = isset($this->request->post['remove']) ? $this->request->post['remove'] : false;

			if (isset($this->request->post['ids'])) {
				$id = array_unique(array_merge($id, (array)$this->request->post['ids']));
			}

			$results = array('done' => array(), 'failed' => array());
			$_results = array();

			// Special case for related products batch edit
			if ($column == "related" && count($id) > 1) {
				$result = $this->model_extension_module_product_quick_edit->quickEditProduct($id, $column, $value, $this->request->post);
				if ($result !== false) {
					foreach ($id as $_id) {
						$_results[$_id] = $result;
					}
					$results['done'] = $id;
				} else {
					$results['failed'] = $id;
				}
			} else {
				foreach ($id as $_id) {
					$result = $this->model_extension_module_product_quick_edit->quickEditProduct($_id, $column, $value, $this->request->post);
					if ($result !== false) {
						$_results[$_id] = $result;
						$results['done'][] = $_id;
					} else {
						$results['failed'][] = $_id;
					}
				}
			}

			if ($results['done']) {
				$response['success'] = true;

				if ((int)$this->config->get('module_product_quick_edit_show_success_message')) {
					if (count($results['done']) > 1) {
						$this->alert['success']['updated'] = sprintf($this->language->get('text_success_update_count'), $this->language->get('column_' . $this->request->post['column']), count($results['done']));
					} else {
						$this->alert['success']['updated'] = sprintf($this->language->get('text_success_update'), $this->language->get('column_' . $this->request->post['column']));
					}
				}
				if ($column == 'discounts') {
					if ((int)$this->config->get('module_product_quick_edit_highlight_actions')) {
						if ($add || $remove) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column . '_exist'] = $this->model_extension_module_product_quick_edit->productDiscountsExist($_id);
							}
							$response['value'] = $response['values'][$id[0]][$column . '_exist'];
						} else {
							$response['value'] = $this->model_extension_module_product_quick_edit->productDiscountsExist($results['done'][0]);
						}
					} else {
						$response['value'] = 0;
					}
				} else if ($column == 'specials') {
					if ((int)$this->config->get('module_product_quick_edit_highlight_actions')) {
						if ($add || $remove) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column . '_exist'] = $this->model_extension_module_product_quick_edit->productSpecialsExist($_id);
							}
							$response['value'] = $response['values'][$id[0]][$column . '_exist'];
						} else {
							$response['value'] = $this->model_extension_module_product_quick_edit->productSpecialsExist($results['done'][0]);
						}
					} else {
						$response['value'] = 0;
					}
				} else if ($column == 'descriptions') {
					$response['value'] = (int)$this->config->get('module_product_quick_edit_highlight_actions');
				} else if ($column == 'related') {
					if ((int)$this->config->get('module_product_quick_edit_highlight_actions')) {
						if ($add || $remove) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column . '_exist'] = $this->model_extension_module_product_quick_edit->productRelatedExist($_id);
							}
							$response['value'] = $response['values'][$id[0]][$column . '_exist'];
						} else {
							$response['value'] = $this->model_extension_module_product_quick_edit->productRelatedExist($results['done'][0]);
						}
					} else {
						$response['value'] = 0;
					}
				} else if ($column == 'filters') {
					if ((int)$this->config->get('module_product_quick_edit_highlight_actions')) {
						if ($add || $remove) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column . '_exist'] = $this->model_extension_module_product_quick_edit->productFiltersExist($_id);
							}
							$response['value'] = $response['values'][$id[0]][$column . '_exist'];
						} else {
							$response['value'] = $this->model_extension_module_product_quick_edit->productFiltersExist($results['done'][0]);
						}
					} else {
						$response['value'] = 0;
					}
				} else if ($column == 'recurrings') {
					if ((int)$this->config->get('module_product_quick_edit_highlight_actions')) {
						if ($add || $remove) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column . '_exist'] = $this->model_extension_module_product_quick_edit->productRecurringsExist($_id);
							}
							$response['value'] = $response['values'][$id[0]][$column . '_exist'];
						} else {
							$response['value'] = $this->model_extension_module_product_quick_edit->productRecurringsExist($results['done'][0]);
						}
					} else {
						$response['value'] = 0;
					}
				} else if ($column == 'attributes') {
					if ((int)$this->config->get('module_product_quick_edit_highlight_actions')) {
						if ($add || $remove) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column . '_exist'] = $this->model_extension_module_product_quick_edit->productAttributesExist($_id);
							}
							$response['value'] = $response['values'][$id[0]][$column . '_exist'];
						} else {
							$response['value'] = $this->model_extension_module_product_quick_edit->productAttributesExist($results['done'][0]);
						}
					} else {
						$response['value'] = 0;
					}
				} else if ($column == 'images') {
					if ((int)$this->config->get('module_product_quick_edit_highlight_actions')) {
						if ($add || $remove) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column . '_exist'] = $this->model_extension_module_product_quick_edit->productImagesExist($_id);
							}
							$response['value'] = $response['values'][$id[0]][$column . '_exist'];
						} else {
							$response['value'] = $this->model_extension_module_product_quick_edit->productImagesExist($results['done'][0]);
						}
					} else {
						$response['value'] = 0;
					}
				} else if ($column == 'options') {
					if ((int)$this->config->get('module_product_quick_edit_highlight_actions')) {
						if ($add || $remove) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column . '_exist'] = $this->model_extension_module_product_quick_edit->productOptionsExist($_id);
							}
							$response['value'] = $response['values'][$id[0]][$column . '_exist'];
						} else {
							$response['value'] = $this->model_extension_module_product_quick_edit->productOptionsExist($results['done'][0]);
						}
					} else {
						$response['value'] = 0;
					}
				} else if ($column == 'seo') {
					$this->load->model('catalog/product');
					$product_seo_keywords = $this->model_catalog_product->getProductSeoUrls($results['done'][0]);
					$response['value'] = (int)$this->config->get('module_product_quick_edit_highlight_actions') * count($product_seo_keywords);
				} else if (in_array($column, array('sort_order', 'points', 'minimum', 'viewed', 'quantity'))) {
					if ($expression) {
						$response['value'] = (int)$_results[$id[0]];
						if (count($id) > 1) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column] = (int)$_results[$_id];
							}
						}
					} else {
						$response['value'] = (int)$value;
					}
				} else if (in_array($column, array('subtract', 'shipping'))) {
					$response['value'] = (int)$value;
					$response['values']['*'][$column . '_text'] = ((int)$value) ? $this->language->get('text_yes') : $this->language->get('text_no');
					$response['values']['*'][$column] = (int)$value;
				} else if ($column == 'status') {
					$response['value'] = (int)$value;
					$response['values']['*'][$column . '_text'] = ((int)$value) ? $this->language->get('text_enabled') : $this->language->get('text_disabled');
					$response['values']['*'][$column] = (int)$value;
					if ($this->config->get('module_product_quick_edit_highlight_status')) {
						$response['values']['*'][$column . '_class'] = (int)$value ? 'success' : 'danger';
					}
				} else if (in_array($column, array('weight', 'length', 'width', 'height'))) {
					if ($expression) {
						$response['value'] = sprintf('%.4f',round((float)$_results[$id[0]], 4));
						if (count($id) > 1) {
							foreach ($id as $_id) {
								$response['values'][$_id][$column] = sprintf('%.4f',round((float)$_results[$_id], 4));
							}
						}
					} else {
						$response['value'] = sprintf('%.4f',round((float)$value, 4));
					}
				} else if ($column == 'image') {
					$this->load->model('tool/image');

					$w = (int)$this->config->get('module_product_quick_edit_list_view_image_width');
					$h = (int)$this->config->get('module_product_quick_edit_list_view_image_height');

					if (is_file(DIR_IMAGE . $value)) {
						$image = $this->model_tool_image->resize($value, $w, $h);
					} else {
						$image = $this->model_tool_image->resize('no_image.png', $w, $h);
					}

					$response['value'] = $value;
					$response['values']['*'][$column . '_thumb'] = $image;
					$response['values']['*'][$column] = $value;
				} else if ($column == 'date_available') {
					$date = new DateTime($value);
					$response['value'] = $value;
					// $response[$column . '_text'] = $date->format($this->language->get('date_format_short'));
					$response['values']['*'][$column . '_text'] = $date->format('Y-m-d');
					$response['values']['*'][$column] = $value;
				} else if (in_array($column, array('date_added', 'date_modified'))) {
					$date = new DateTime($value);
					$response['value'] = $value;
					// $response[$column . '_text'] = $date->format($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'));
					$response['values']['*'][$column . '_text'] = $date->format('Y-m-d H:i:s');
					$response['values']['*'][$column] = $value;
				} else if ($column == 'tax_class') {
					$this->load->model('localisation/tax_class');
					$tax_class = $this->model_localisation_tax_class->getTaxClass((int)$value);
					if ($tax_class) {
						$response['value'] = (int)$tax_class['tax_class_id'];
						$response['values']['*'][$column . '_text'] = $tax_class['title'];
						$response['values']['*'][$column] = (int)$tax_class['tax_class_id'];
					} else {
						$response['value'] = '';
						$response['values']['*'][$column . '_text'] = '';
						$response['values']['*'][$column] = '';
					}
				} else if ($column == 'stock_status') {
					$this->load->model('localisation/stock_status');
					$stock_status = $this->model_localisation_stock_status->getStockStatus((int)$value);
					if ($stock_status) {
						$response['value'] = (int)$stock_status['stock_status_id'];
						$response['values']['*'][$column . '_text'] = $stock_status['name'];
						$response['values']['*'][$column] = (int)$stock_status['stock_status_id'];
					} else {
						$response['value'] = '';
						$response['values']['*'][$column . '_text'] = '';
						$response['values']['*'][$column] = '';
					}
				} else if ($column == 'length_class') {
					$this->load->model('localisation/length_class');
					$length_class = $this->model_localisation_length_class->getLengthClass((int)$value);
					if ($length_class) {
						$response['value'] = (int)$length_class['length_class_id'];
						$response['values']['*'][$column . '_text'] = $length_class['title'];
						$response['values']['*'][$column] = (int)$length_class['length_class_id'];
					} else {
						$response['value'] = '';
						$response['values']['*'][$column . '_text'] = '';
						$response['values']['*'][$column] = '';
					}
				} else if ($column == 'weight_class') {
					$this->load->model('localisation/weight_class');
					$weight_class = $this->model_localisation_weight_class->getWeightClass((int)$value);
					if ($weight_class) {
						$response['value'] = (int)$weight_class['weight_class_id'];
						$response['values']['*'][$column . '_text'] = $weight_class['title'];
						$response['values']['*'][$column] = (int)$weight_class['weight_class_id'];
					} else {
						$response['value'] = '';
						$response['values']['*'][$column . '_text'] = '';
						$response['values']['*'][$column] = '';
					}
				} else if ($column == 'manufacturer') {
					$this->load->model('catalog/manufacturer');
					$manufacturer = $this->model_catalog_manufacturer->getManufacturer((int)$value);
					if ($manufacturer) {
						$response['value'] = (int)$manufacturer['manufacturer_id'];
						$response['values']['*'][$column . '_text'] = $manufacturer['name'];
						$response['values']['*'][$column] = (int)$manufacturer['manufacturer_id'];
					} else {
						$response['value'] = 0;
						$response['values']['*'][$column . '_text'] = '';
						$response['values']['*'][$column] = 0;
					}
				} else if (in_array($column, array('name', 'tag'))) {
					if (is_array($value)) {
						$response['value'] = '';
						foreach ((array)$value as $v) {
							if ($v['lang'] == $lang_id) {
								$response['value'] = $v['value'];
							}
						}
					} else {
						$response['value'] = $value;
					}
				} else if ($column == 'category') {
					$_categories = $this->cache->get('category.all');

					if ($_categories === false || is_null($_categories)) {
						$this->load->model('catalog/category');
						$_categories = $this->model_catalog_category->getCategories(array('sort' => 'name'));
						$this->cache->set('category.all', $_categories);
					}

					if ($add || $remove) {
						$response['value'] = $_results[$id[0]]['id'];

						foreach ($id as $_id) {
							if (is_array($_results[$_id])) {
								$response['values'][$_id][$column] = array_map("intval", $_results[$_id]['id']);
								$response['values'][$_id][$column . '_data'] = array_map(function($a) { return array_combine(array('id', 'text'), $a); }, array_map(null, array_map("intval", $_results[$_id]['id']), $_results[$_id]['text']));
							} else {
								$response['values'][$_id][$column] = array();
								$response['values'][$_id][$column . '_data'] = array();
							}
						}
					} else {
						$categories = array();

						foreach ($_categories as $category) {
							if (in_array($category['category_id'], (array)$value)) {
								$categories[] = array('id' => (int)$category['category_id'], 'text' => $category['name']);
							}
						}

						$response['value'] = $value;
						$response['values']['*'][$column] = $value;
						$response['values']['*'][$column . '_data'] = $categories;
					}
				} else if ($column == 'store') {
					$this->load->model('setting/store');
					$__stores = $this->model_setting_store->getStores(array());

					$_stores = array(
						'0' => array(
							'store_id'  => 0,
							'name'      => $this->config->get('config_name'),
							'url'       => HTTP_CATALOG
						)
					);

					foreach ($__stores as $store) {
						$_stores[$store['store_id']] = array(
							'store_id'  => $store['store_id'],
							'name'      => $store['name'],
							'url'       => $store['url']
						);
					}

					if ($add || $remove) {
						$response['value'] = $_results[$id[0]]['id'];

						foreach ($id as $_id) {
							if (is_array($_results[$_id])) {
								$response['values'][$_id][$column] = array_map("intval", $_results[$_id]['id']);
								$response['values'][$_id][$column . '_data'] = array_map(function($a) { return array_combine(array('id', 'text'), $a); }, array_map(null, array_map("intval", $_results[$_id]['id']), $_results[$_id]['text']));
							} else {
								$response['values'][$_id][$column] = array();
								$response['values'][$_id][$column . '_data'] = array();
							}
						}
					} else {
						$stores = array();

						foreach ($_stores as $store) {
							if ($value && in_array($store['store_id'], (array)$value)) {
								$stores[] = array('id' => (int)$store['store_id'], 'text' => $store['name']);
							}
						}
						$response['value'] = $value;
						$response['values']['*'][$column] = $value;
						$response['values']['*'][$column . '_data'] = $stores;
					}
				} else if ($column == 'filter') {
					$_filters = $this->cache->get('filters.all');

					if ($_filters === false || is_null($_filters)) {
						$this->load->model('extension/module/product_quick_edit');
						$_filters = $this->model_extension_module_product_quick_edit->getFiltersByGroup();
						$this->cache->set('filters.all', $_filters);
					}

					if ($add || $remove) {
						$response['value'] = $_results[$id[0]]['id'];

						foreach ($id as $_id) {
							if (is_array($_results[$_id])) {
								$response['values'][$_id][$column] = array_map("intval", $_results[$_id]['id']);
								$response['values'][$_id][$column . '_data'] = array_map(function($a) { return array_combine(array('id', 'text'), $a); }, array_map(null, array_map("intval", $_results[$_id]['id']), $_results[$_id]['text']));
							} else {
								$response['values'][$_id][$column] = array();
								$response['values'][$_id][$column . '_data'] = array();
							}
						}
					} else {
						$filters = array();

						foreach ($_filters as $fg) {
							foreach ($fg['filters'] as $filter) {
								if (in_array($filter['filter_id'], (array)$value)) {
									$filters[] = array('id' => (int)$filter['filter_id'], 'text' => strip_tags(html_entity_decode($fg['name'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8')));
								}
							}
						}
						$response['value'] = $value;
						$response['values']['*'][$column] = $value;
						$response['values']['*'][$column . '_data'] = $filters;
					}
				} else if ($column == 'download') {
					$_downloads = $this->cache->get('downloads.all');

					if ($_downloads === false || is_null($_downloads)) {
						$this->load->model('catalog/download');
						$_downloads = $this->model_catalog_download->getDownloads(array());
						$this->cache->set('downloads.all', $_downloads);
					}

					if ($add || $remove) {
						$response['value'] = $_results[$id[0]]['id'];

						foreach ($id as $_id) {
							if (is_array($_results[$_id])) {
								$response['values'][$_id][$column] = array_map("intval", $_results[$_id]['id']);
								$response['values'][$_id][$column . '_data'] = array_map(function($a) { return array_combine(array('id', 'text'), $a); }, array_map(null, array_map("intval", $_results[$_id]['id']), $_results[$_id]['text']));
							} else {
								$response['values'][$_id][$column] = array();
								$response['values'][$_id][$column . '_data'] = array();
							}
						}
					} else {
						$downloads = array();

						foreach ($_downloads as $download) {
							if (in_array($download['download_id'], (array)$value))
								$downloads[] = array('id' => (int)$download['download_id'], 'text' => $download['name']);
						}
						$response['value'] = $value;
						$response['values']['*'][$column] = $value;
						$response['values']['*'][$column . '_data'] = $downloads;
					}
				} else if ($column == 'price') {
					$this->load->model('catalog/product');

					if ($expression) {
						$response['value'] = sprintf('%.4f',round((float)$_results[$id[0]], 4));
						// $response['value'] = $this->currency->format($_results[$id[0]], $this->config->get('config_currency'));
					} else {
						$response['value'] = sprintf('%.4f',round((float)$value, 4));
						// $response['value'] = $this->currency->format($value, $this->config->get('config_currency'));
					}

					foreach ($id as $_id) {
						$special = false;
						$product_specials = $this->model_catalog_product->getProductSpecials($_id);
						foreach ($product_specials  as $product_special) {
							if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
								$special = $product_special['price'];
								break;
							}
						}
						if ($special) {
							$response['values'][$_id]['special'] = sprintf('%.4f',round((float)$special, 4));
							// $response['values'][$_id]['special'] = $this->currency->format($special, $this->config->get('config_currency'));
						}
						$response['values'][$_id][$column] = sprintf('%.4f',round((float)$_results[$_id], 4));
						// $response['values'][$_id][$column] = $this->currency->format($_results[$_id], $this->config->get('config_currency'));
					}
				} else if ($column == 'gross_price') {
					$tax = new Cart\Tax($this->registry);

					if ($this->config->get('config_tax_default') == 'shipping') {
						$tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
					}

					if ($this->config->get('config_tax_default') == 'payment') {
						$tax->setPaymentAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
					}

					$tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));

					$this->load->model('catalog/product');

					$product = $this->model_catalog_product->getProduct($id[0]);

					if ($expression) {
						$response['value'] = sprintf('%.4f',round((float)$tax->calculate($_results[$id[0]], $product['tax_class_id']), 4));
						// $response['value'] = $this->currency->format($_results[$id[0]], $this->config->get('config_currency'));
					} else {
						$response['value'] = sprintf('%.4f',round((float)$tax->calculate($value, $product['tax_class_id']), 4));
						// $response['value'] = $this->currency->format($value, $this->config->get('config_currency'));
					}

					foreach ($id as $_id) {
						$special = false;
						$product = $this->model_catalog_product->getProduct($_id);
						$product_specials = $this->model_catalog_product->getProductSpecials($_id);
						foreach ($product_specials  as $product_special) {
							if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
								$special = $product_special['price'];
								break;
							}
						}
						if ($special) {
							$response['values'][$_id]['special_with_price'] = sprintf('%.4f',round((float)$tax->calculate($special, $product['tax_class_id']), 4));
							// $response['values'][$_id]['special_with_price'] = $this->currency->format($tax->calculate($special, $product['tax_class_id']), $this->config->get('config_currency'));
						}
						$response['values'][$_id][$column] = sprintf('%.4f',round((float)$tax->calculate($_results[$_id], $product['tax_class_id']), 4));
						// $response['values'][$_id][$column] = $this->currency->format($tax->calculate($_results[$_id], $product['tax_class_id']), $this->config->get('config_currency'));
					}
				} else
					$response['value'] = $value;
			} else {
				$this->alert['error']['update'] = $this->language->get('error_update');
				// $response['msg'] = $this->language->get('error_update');
			}

			$response['results'] = $results;

			if ($results['failed']) {
				$this->load->model('catalog/product');

				$failed_products = array();

				foreach ($results['failed'] as $_id) {
					$product = $this->model_catalog_product->getProduct($_id);
					if ($product) {
						$failed_products[] = $product['name'];
					}
				}

				$this->alert['warning']['update'] = sprintf($this->language->get('text_error_update'), implode(', ', $failed_products));
				// $response['alerts']['warning']['error_update'] = sprintf($this->language->get('text_error_update'), implode(', ', $failed_products));
			}
		}

		$response = array_merge($response, array("errors" => $this->error), array("alerts" => $this->alert));

		$response['query_count'] = DB::$query_count;
		$response['page_time'] = microtime(true) - $this->start_time;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
	}

	public function clear_cache() {
		$ajax_request = isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

		$this->load->language('extension/module/catalog/product');

		$response = array();

		if ($this->validateAction("clear_cache")) {
			$this->cache->delete('pqe.products');
			$this->cache->delete('manufacturers');
			$this->cache->delete('downloads');
			$this->cache->delete('filters');
			$this->cache->delete('category');

			if ($ajax_request) {
				$this->alert['success']['cache_cleared'] = $this->language->get('text_success_clear_cache');
			} else {
				$this->session->data['success'] = $this->language->get('text_success_clear_cache');
			}
		} else {
			if (!$ajax_request) {
				$this->session->data['errors'] = $this->error;
				$this->session->data['alerts'] = $this->alert;
			}
		}

		if ($ajax_request) {
			$response = array_merge($response, array("errors" => $this->error), array("alerts" => $this->alert));

			$response['query_count'] = DB::$query_count;
			$response['page_time'] = microtime(true) - $this->start_time;

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
		} else {
			$url = $this->urlParams();
			$this->response->redirect($this->url->link('extension/module/product_quick_edit/view', 'dTc=1' . $url, true));
		}
	}

	protected function action($action) {
		$ajax_request = isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

		if ($ajax_request) {
			$this->load->language('extension/module/catalog/product');

			$this->load->model('catalog/product');

			$response = array();

			if (isset($this->request->post['selected']) && $this->validateAction($action)) {
				foreach ((array)$this->request->post['selected'] as $product_id) {
					switch ($action) {
						case 'copy':
							$this->model_catalog_product->copyProduct($product_id);
							break;
						case 'delete':
							$this->model_catalog_product->deleteProduct($product_id);
							break;
					}
				}

				$response['reset'] = true;
				$this->alert['success']['done'] = sprintf($this->language->get('text_success_' . $action), count((array)$this->request->post['selected']));
			}

			$response = array_merge($response, array("errors" => $this->error), array("alerts" => $this->alert));

			$response['query_count'] = DB::$query_count;
			$response['page_time'] = microtime(true) - $this->start_time;

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($response, JSON_UNESCAPED_SLASHES));
			return;
		} else {
			$url = $this->urlParams();
			$this->response->redirect($this->url->link('extension/module/product_quick_edit/view', $url, true));
		}
	}

	protected function getBase() {
		if (!$this->checkPrerequisites()) {
			$this->showErrorPage();
			return;
		}

		if (isset($this->session->data['errors'])) {
			$this->error = array_merge($this->error, (array)$this->session->data['errors']);

			unset($this->session->data['errors']);
		}

		if (isset($this->session->data['alerts'])) {
			$this->alert = array_merge($this->alert, (array)$this->session->data['alerts']);

			unset($this->session->data['alerts']);
		}

		if (isset($this->request->get['search'])) {
			$search = html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8');
		} else {
			$search = '';
		}

		$this->load->language('extension/module/catalog/product');

		$this->load->model('catalog/product');
		$this->load->model('extension/module/product_quick_edit');

		$this->document->setTitle($this->language->get('heading_title'));

		$items_per_page = $this->config->get('module_product_quick_edit_items_per_page');
		$data['items_per_page'] = ($items_per_page) ? $items_per_page : $this->config->get('config_limit_admin');

		$data['module_product_quick_edit_server_side_caching'] = $this->config->get('module_product_quick_edit_server_side_caching');
		$data['module_product_quick_edit_client_side_caching'] = $this->config->get('module_product_quick_edit_client_side_caching');
		$data['module_product_quick_edit_cache_size'] = $this->config->get('module_product_quick_edit_cache_size');
		$data['module_product_quick_edit_alternate_row_colour'] = $this->config->get('module_product_quick_edit_alternate_row_colour');
		$data['module_product_quick_edit_row_hover_highlighting'] = $this->config->get('module_product_quick_edit_row_hover_highlighting');
		$data['module_product_quick_edit_highlight_status'] = $this->config->get('module_product_quick_edit_highlight_status');
		$data['module_product_quick_edit_highlight_filtered_columns'] = $this->config->get('module_product_quick_edit_highlight_filtered_columns');
		$data['module_product_quick_edit_highlight_actions'] = $this->config->get('module_product_quick_edit_highlight_actions');
		$data['module_product_quick_edit_quick_edit_on'] = $this->config->get('module_product_quick_edit_quick_edit_on');
		$data['module_product_quick_edit_price_relative_to'] = $this->config->get('module_product_quick_edit_price_relative_to');
		$data['module_product_quick_edit_list_view_image_width'] = $this->config->get('module_product_quick_edit_list_view_image_width');
		$data['module_product_quick_edit_list_view_image_height'] = $this->config->get('module_product_quick_edit_list_view_image_height');
		$data['module_product_quick_edit_filter_sub_category'] = $this->config->get('module_product_quick_edit_filter_sub_category');
		$data['module_product_quick_edit_debug_mode'] = $this->config->get('module_product_quick_edit_debug_mode');
		$data['module_product_quick_edit_search_bar'] = $this->config->get('module_product_quick_edit_search_bar');
		$data['module_product_quick_edit_batch_edit'] = $this->config->get('module_product_quick_edit_batch_edit');
		$data['module_product_quick_edit_show_success_message'] = $this->config->get('module_product_quick_edit_show_success_message');
		$data['module_product_quick_edit_default_sort'] = $this->config->get('module_product_quick_edit_default_sort');
		$data['module_product_quick_edit_default_order'] = $this->config->get('module_product_quick_edit_default_order');

		$url = $this->urlParams();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'active'    => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/product_quick_edit/view', 'user_token=' . $this->session->data['user_token'], true),
			'active'    => true
		);

		$data['add'] = $this->url->link('catalog/product/add', $url, true);
		$data['copy'] = $this->url->link('extension/module/product_quick_edit/copy', $url, true);
		$data['delete'] = $this->url->link('extension/module/product_quick_edit/delete', $url, true);
		$data['source'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/data', $url, true), ENT_QUOTES, 'UTF-8');
		$data['load'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/load', 'user_token=' . $this->session->data['user_token'], true), ENT_QUOTES, 'UTF-8');
		$data['settings'] = $this->url->link('extension/module/product_quick_edit/settings', $url, true);
		$data['clear_cache'] = $this->url->link('extension/module/product_quick_edit/clear_cache', $url, true);
		$data['update'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/update', 'user_token=' . $this->session->data['user_token'], true), ENT_QUOTES, 'UTF-8');
		$data['reload'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/reload', 'user_token=' . $this->session->data['user_token'], true), ENT_QUOTES, 'UTF-8');
		$data['filter'] = html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', '', true), ENT_QUOTES, 'UTF-8');

		$data['clear_dt_cache'] = isset($this->session->data['success']);

		$this->load->model('setting/store');

		$multistore = $this->model_setting_store->getTotalStores();

		$actions = $this->config->get('module_product_quick_edit_catalog_products_actions');

		foreach ($actions as $action => $attr) {
			$actions[$action]['name'] = $this->language->get('action_' . $action);
		}
		uasort($actions, 'column_sort');
		$data['product_actions'] = $actions;

		$data['sorts'] = array();

		$columns = $this->config->get('module_product_quick_edit_catalog_products');

		foreach ($columns as $column => $attr) {
			$columns[$column]['name'] = $this->language->get('column_' . $column);

			if (strpos($attr['sort'], "p.") === 0 || $attr['sort'] == "pd.name") {
				$data['sorts'][$column]['name'] = $columns[$column]['name'];
				$data['sorts'][$column]['value'] = $attr['sort'];
			}

			if ($column == 'view_in_store' && !$multistore) {
				unset($columns[$column]);
			}
		}

		uasort($columns, 'column_sort');
		$data['product_columns'] = $columns;

		$columns = array_filter($columns, 'column_display');
		$displayed_actions = array_keys(array_filter($actions, 'column_display'));

		$displayed_columns = array_keys($columns);
		$related_columns = array_merge(array_map(function($v) { return $v['rel']; }, $columns), array_map(function($v) { return $v['rel']; }, $actions));
		$column_classes = array();
		$type_classes = array();
		$non_sortable = array();

		if (!is_array($columns)) {
			$displayed_columns = array('selector', 'image', 'name', 'model', 'price', 'quantity', 'status', 'action');
			$columns = array();
		} else {
			foreach($columns as $column => $attr) {
				if (empty($attr['sort'])) {
					$non_sortable[] = 'col_' . $column;
				}

				if (!empty($attr['type']) && !in_array($attr['type'], $type_classes)) {
					$type_classes[] = $attr['type'];
				}

				if (!empty($attr['align'])) {
					if (!empty($attr['type']) && $attr['editable']) {
						$column_classes[] = $attr['align'] . ' ' . $attr['type'];
					} else {
						$column_classes[] = $attr['align'];
					}
				} else {
					if (!empty($attr['type'])) {
						$column_classes[] = $attr['type'];
					} else {
						$column_classes[] = null;
					}
				}
			}
		}

		$data['columns'] = $displayed_columns;
		$data['actions'] = $displayed_actions;
		$data['related'] = $related_columns;
		$data['column_info'] = $columns;
		$data['non_sortable_columns'] = json_encode($non_sortable);
		$data['column_classes'] = $column_classes;
		$data['types'] = $type_classes;
		$data['typeahead'] = array();
		$data['active_filters'] = array();

		if (!$displayed_columns) {
			$this->alert['info']['select_columns'] = $this->language->get('text_select_columns');
		}

		foreach (array('name', 'model', 'sku', 'upc', 'ean', 'jan', 'isbn', 'mpn', 'location') as $column) {
			if (in_array($column, $displayed_columns)) {
				$url = $this->urlParams();
				$data['typeahead'][$column] = array(
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=' . $column . '&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			}
		}

		if (in_array('category', $displayed_columns)) {
			$this->load->model('catalog/category');
			$data['categories'] = false;
			$total_categories = $this->cache->get('category.total');

			if (isset($this->request->get['filter_category'])) {
				if ($this->request->get['filter_category'] == '*') {
					$data['active_filters']['category'] = array(
						"id"    => '*',
						"value" => html_entity_decode($this->language->get('text_none'))
					);
				} else {
					$cat =  $this->model_catalog_category->getCategory($this->request->get['filter_category']);
					if ($cat) {
						$data['active_filters']['category'] = array(
							"id"    => $this->request->get['filter_category'],
							"value" => html_entity_decode($cat['name'])
						);
					}
				}
			}

			if ($total_categories === false || is_null($total_categories)) {
				$total_categories = (int)$this->model_catalog_category->getTotalCategories();
				$this->cache->set('category.total', $total_categories);
			}

			if ($total_categories < TA_LOCAL) {
				$categories = $this->cache->get('category.all');

				if ($categories === false || is_null($categories)) {
					$categories = $this->model_catalog_category->getCategories(array('sort' => 'name'));
					$this->cache->set('category.all', $categories);
				}

				$data['categories'] = $categories;
			} else if ($total_categories < TA_PREFETCH) {
				$url = $this->urlParams();
				$data['typeahead']['category'] = array(
					'prefetch' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=category' . $url, true), ENT_QUOTES, 'UTF-8'),
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=category&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			} else {
				$url = $this->urlParams();
				$data['typeahead']['category'] = array(
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=category&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			}

			if ($total_categories < TA_LOCAL) {
				$cat_select = array();
				foreach ($data['categories'] as $cat) {
					$cat_select[] = array('id' => (int)$cat['category_id'], 'text' => html_entity_decode($cat['name'], ENT_QUOTES, 'UTF-8'));
				}
				$data['category_select'] = json_encode($cat_select);
			} else {
				$data['category_select'] = false;
			}
		}

		if (in_array('store', $displayed_columns)) {
			$this->load->model('setting/store');
			$_stores = $this->model_setting_store->getStores(array());

			$stores = array(
				'0' => array(
					'store_id'  => 0,
					'name'      => $this->config->get('config_name'),
					'url'       => HTTP_CATALOG
				)
			);

			foreach ($_stores as $store) {
				$stores[$store['store_id']] = array(
					'store_id'  => $store['store_id'],
					'name'      => $store['name'],
					'url'       => $store['url']
				);
			}

			$data['stores'] = $stores;

			$st_select = array();
			foreach ($stores as $st) {
				$st_select[] = array('id' => (int)$st['store_id'], 'text' => html_entity_decode($st['name'], ENT_QUOTES, 'UTF-8'));
			}
			$data['store_select'] = json_encode($st_select);
		}

		if (in_array('manufacturer', $displayed_columns)) {
			$this->load->model('catalog/manufacturer');

			$data['manufacturers'] = false;
			$total_manufacturers = $this->cache->get('manufacturers.total');

			if (isset($this->request->get['filter_manufacturer'])) {
				if ($this->request->get['filter_manufacturer'] == '*') {
					$data['active_filters']['manufacturer'] = array(
						"id"    => '*',
						"value" => html_entity_decode($this->language->get('text_none'))
					);
				} else {
					$man =  $this->model_catalog_manufacturer->getManufacturer($this->request->get['filter_manufacturer']);
					if ($man) {
						$data['active_filters']['manufacturer'] = array(
							"id"    => $this->request->get['filter_manufacturer'],
							"value" => html_entity_decode($man['name'])
						);
					}
				}
			}

			if ($total_manufacturers === false || is_null($total_manufacturers)) {
				$total_manufacturers = (int)$this->model_catalog_manufacturer->getTotalManufacturers();
				$this->cache->set('manufacturers.total', $total_manufacturers);
			}

			if ($total_manufacturers < TA_LOCAL) {
				$manufacturers = $this->cache->get('manufacturers.all');

				if ($manufacturers === false || is_null($manufacturers)) {
					$manufacturers = $this->model_catalog_manufacturer->getManufacturers(array());
					$this->cache->set('manufacturers.all', $manufacturers);
				}

				$data['manufacturers'] = (array)$manufacturers;
			} else if ($total_manufacturers < TA_PREFETCH) {
				$url = $this->urlParams();
				$data['typeahead']['manufacturer'] = array(
					'prefetch' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=manufacturer' . $url, true), ENT_QUOTES, 'UTF-8'),
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=manufacturer&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			} else {
				$url = $this->urlParams();
				$data['typeahead']['manufacturer'] = array(
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=manufacturer&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			}

			if ($total_manufacturers < TA_LOCAL) {
				$m_select = array();
				foreach ($data['manufacturers'] as $m) {
					$m_select[] = array('value' => (int)$m['manufacturer_id'], 'text' => html_entity_decode($m['name'], ENT_QUOTES, 'UTF-8'));
				}
				$data['manufacturer_select'] = json_encode($m_select);
			} else {
				$data['manufacturer_select'] = false;
			}
		}

		if (in_array('download', $displayed_columns)) {
			$this->load->model('catalog/download');

			$data['downloads'] = false;
			$total_downloads = $this->cache->get('downloads.total');

			if (isset($this->request->get['filter_download'])) {
				if ($this->request->get['filter_download'] == '*') {
					$data['active_filters']['download'] = array(
						"id"    => '*',
						"value" => html_entity_decode($this->language->get('text_none'))
					);
				} else {
					$dl =  $this->model_catalog_download->getDownload($this->request->get['filter_download']);
					if ($dl) {
						$data['active_filters']['download'] = array(
							"id"    => $this->request->get['filter_download'],
							"value" => html_entity_decode($dl['name'])
						);
					}
				}
			}

			if ($total_downloads === false || is_null($total_downloads)) {
				$total_downloads = (int)$this->model_catalog_download->getTotalDownloads();
				$this->cache->set('downloads.total', $total_downloads);
			}

			if ($total_downloads < TA_LOCAL) {
				$downloads = $this->cache->get('downloads.all');

				if ($downloads === false || is_null($downloads)) {
					$downloads = $this->model_catalog_download->getDownloads(array());
					$this->cache->set('downloads.all', $downloads);
				}

				$data['downloads'] = $downloads;
			} else if ($total_downloads < TA_PREFETCH) {
				$url = $this->urlParams();
				$data['typeahead']['download'] = array(
					'prefetch' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=download' . $url, true), ENT_QUOTES, 'UTF-8'),
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=download&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			} else {
				$url = $this->urlParams();
				$data['typeahead']['download'] = array(
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=download&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			}

			if ($total_downloads < TA_LOCAL) {
				$dl_select = array();
				foreach ($data['downloads'] as $dl) {
					$dl_select[] = array('id' => (int)$dl['download_id'], 'text' => html_entity_decode($dl['name'], ENT_QUOTES, 'UTF-8'));
				}
				$data['download_select'] = json_encode($dl_select);
			} else {
				$data['download_select'] = false;
			}
		}

		if (in_array('filter', $displayed_columns)) {
			$this->load->model('catalog/filter');
			$this->load->model('extension/module/product_quick_edit');

			$data['filters'] = false;
			$total_filters = $this->cache->get('filters.total');

			if (isset($this->request->get['filter_filter'])) {
				if ($this->request->get['filter_filter'] == '*') {
					$data['active_filters']['filter'] = array(
						"id"    => '*',
						"value" => html_entity_decode($this->language->get('text_none'))
					);
				} else {
					$fltr =  $this->model_catalog_filter->getFilter($this->request->get['filter_filter']);
					if ($fltr) {
						$data['active_filters']['filter'] = array(
							"id"    => $this->request->get['filter_filter'],
							"value" => html_entity_decode($fltr['name'])
						);
					}
				}
			}

			if ($total_filters === false || is_null($total_filters)) {
				$total_filters = (int)$this->model_extension_module_product_quick_edit->getTotalFilters();
				$this->cache->set('filters.total', $total_filters);
			}

			if ($total_filters < TA_LOCAL) {
				$filters = $this->cache->get('filters.all');

				if ($filters === false || is_null($filters)) {
					$filters = $this->model_extension_module_product_quick_edit->getFiltersByGroup();
					$this->cache->set('filters.all', $filters);
				}

				$data['filters'] = $filters;
			} else if ($total_filters < TA_PREFETCH) {
				$url = $this->urlParams();
				$data['typeahead']['filter'] = array(
					'prefetch' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=filter' . $url, true), ENT_QUOTES, 'UTF-8'),
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=filter&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			} else {
				$url = $this->urlParams();
				$data['typeahead']['filter'] = array(
					'remote' => html_entity_decode($this->url->link('extension/module/product_quick_edit/filter', 'type=filter&query=%QUERY' . $url, true), ENT_QUOTES, 'UTF-8')
				);
			}

			if ($total_filters < TA_LOCAL) {
				$f_select = array();
				foreach ($data['filters'] as $fg) {
					$group = array('text' => $fg['name'], 'children' => array());
					foreach ($fg['filters'] as $f) {
						$group['children'][] = array(
							'id'    => (int)$f['filter_id'],
							'text'  => strip_tags(html_entity_decode($fg['name'] . ' &gt; ' . $f['name'], ENT_QUOTES, 'UTF-8'))
						);
					}
					$f_select[] = $group;
				}
				$data['filter_select'] = json_encode($f_select);
			} else {
				$data['filter_select'] = false;
			}
		}

		if (in_array('tax_class', $displayed_columns)) {
			$this->load->model('localisation/tax_class');
			$tax_classes = $this->model_localisation_tax_class->getTaxClasses(array());

			$data['tax_classes'] = $tax_classes;

			$tc_select = array();
			$tc_select[] = array('value' => 0, 'text' => $this->language->get('text_none'));
			foreach ($tax_classes as $tc) {
				$tc_select[] = array('value' => (int)$tc['tax_class_id'], 'text' => $tc['title']);
			}
			$data['tax_class_select'] = json_encode($tc_select);
		}

		if (in_array('stock_status', $displayed_columns)) {
			$this->load->model('localisation/stock_status');
			$stock_statuses = $this->model_localisation_stock_status->getStockStatuses(array());

			$data['stock_statuses'] = $stock_statuses;

			$ss_select = array();
			foreach ($stock_statuses as $ss) {
				$ss_select[] = array('value' => (int)$ss['stock_status_id'], 'text' => $ss['name']);
			}
			$data['stock_status_select'] = json_encode($ss_select);
		}

		if (in_array('length_class', $displayed_columns)) {
			$this->load->model('localisation/length_class');
			$length_classes = $this->model_localisation_length_class->getLengthClasses(array());

			$data['length_classes'] = $length_classes;

			$lc_select = array();
			foreach ($length_classes as $lc) {
				$lc_select[] = array('value' => (int)$lc['length_class_id'], 'text' => $lc['title']);
			}
			$data['length_class_select'] = json_encode($lc_select);
		}

		if (in_array('weight_class', $displayed_columns)) {
			$this->load->model('localisation/weight_class');
			$weight_classes = $this->model_localisation_weight_class->getWeightClasses(array());

			$data['weight_classes'] = $weight_classes;

			$wc_select = array();
			foreach ($weight_classes as $wc) {
				$wc_select[] = array('value' => (int)$wc['weight_class_id'], 'text' => $wc['title']);
			}
			$data['weight_class_select'] = json_encode($wc_select);
		}

		if (in_array('image', $displayed_columns)) {
			$this->load->model('tool/image');

			$w = (int)$this->config->get('module_product_quick_edit_list_view_image_width');
			$h = (int)$this->config->get('module_product_quick_edit_list_view_image_height');

			$data['no_image'] = $this->model_tool_image->resize('no_image.png', $w, $h);
			$data['list_view_image_width'] = $w;
			$data['list_view_image_height'] = $h;
		}

		if (isset($this->session->data['error'])) {
			$this->error = $this->session->data['error'];

			unset($this->session->data['error']);
		}

		if (isset($this->error['warning'])) {
			$this->alert['warning']['warning'] = $this->error['warning'];
			unset($this->error['warning']);
		}

		if (isset($this->error['error'])) {
			$this->alert['error']['error'] = $this->error['error'];
			unset($this->error['error']);
		}

		if (isset($this->session->data['success'])) {
			$this->alert['success']['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		}


		if (in_array("descriptions", $displayed_actions)) {
			$this->document->addStyle('view/javascript/summernote/summernote.css');
		}
		$this->document->addStyle('view/stylesheet/pqe/catalog.min.css?v=' . EXTENSION_VERSION);

		if (in_array("descriptions", $displayed_actions)) {
			$this->document->addScript('view/javascript/summernote/summernote.js');
		}
		$this->document->addScript('view/javascript/pqe/catalog.min.js?v=' . EXTENSION_VERSION);

		$data['search'] = $search;

		$data['errors'] = $this->error;

		$data['user_token'] = $this->session->data['user_token'];

		$data['alerts'] = $this->alert;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['text_page_created'] = sprintf($this->language->get('text_page_created'), microtime(true) - $this->start_time, DB::$query_count);

		$template = 'extension/module/catalog/product_list';

		$this->response->setOutput($this->load->view($template, $data));
	}

	protected function showErrorPage($data = array()) {
		$this->load->language('extension/module/catalog/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
			'active'    => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/product_quick_edit/view', 'user_token=' . $this->session->data['user_token'], true),
			'active'    => true
		);

		$url = $this->urlParams();

		$data['cancel'] = $this->url->link('catalog/product', $url, true);

		parent::showErrorPage($data);
	}

	protected function validatePermission() {
		$errors = false;

		if (!$this->user->hasPermission('modify', 'extension/module/product_quick_edit')) {
			$errors = true;
			$this->alert['error']['permission'] = $this->language->get('error_permission');
		}

		return !$errors;
	}

	protected function validateAction($action) {
		return $this->validatePermission();
	}

	protected function validateLoadData($data) {
		$errors = !$this->validatePermission();

		if (!isset($data['id']) || !isset($data['column'])) {
			$errors = true;
			$this->alert['error']['update'] = $this->language->get('error_update');
		}

		return !$errors;
	}

	protected function validateReloadData($data) {
		$errors = !$this->validatePermission();

		if (!isset($data['data'])) {
			$errors = true;
			$this->alert['error']['update'] = $this->language->get('error_update');
		}

		return !$errors;
	}

	protected function validateUpdateData($data) {
		$errors = !$this->validatePermission();

		if (!isset($data['id']) || !isset($data['column']) || !isset($data['value']) || !isset($data['old'])) {
			$errors = true;
			$this->alert['error']['update'] = $this->language->get('error_update');
		}

		$id = $data['id'];
		$column = $data['column'];
		$value = $data['value'];

		if (in_array($column, array('quantity', 'sort_order', 'minimum', 'points', 'viewed', 'price', 'length', 'width', 'height', 'weight')) && strpos(trim($value), "#") === 0 && preg_match('/^#\s*(?P<operation>[+-\/\*])\s*(?P<operand>-?\d+\.?\d*)(?P<percent>%)?$/', trim($value)) !== 1) {
			$errors = true;
			$this->error['error'] = $this->language->get('error_expression');
		}

		if ($column == 'model' && (utf8_strlen($value) < 1 || utf8_strlen($value) > 64)) {
			$errors = true;
			$this->error['error'] = $this->language->get('error_model');
		}

		if (in_array($column, array('name', 'tag'))) {
			foreach ((array)$value as $v) {
				if (!isset($v['value']) || !isset($v['lang'])) {
					$errors = true;
					$this->error['error'] = $this->language->get('error_update');
				} else {
					if ($column == 'name' && (utf8_strlen($v['value']) < 1 || utf8_strlen($v['value']) > 255)) {
						$errors = true;
						$this->error['value'][] = array('lang' => $v['lang'], 'text' => $this->language->get('error_name'));
					}
				}
			}
		}

		if ($column == 'seo_urls' && isset($value['product_seo_url'])) {
			if (isset($data['ids'])) {
				$errors = true;
				$this->error['error'] = $this->language->get('error_batch_edit_seo');
			} else {
				$this->load->model('design/seo_url');

				foreach ((array)$value['product_seo_url'] as $store_id => $language) {
					foreach ($language as $language_id => $keyword) {
						if (!empty($keyword)) {
							if (count(array_keys($language, $keyword)) > 1) {
								$errors = true;
								$this->error['seo_urls'][$store_id]['values'][$language_id]['value'] = $this->language->get('error_unique');
							}

							$seo_urls = $this->model_design_seo_url->getSeoUrlsByKeyword($keyword);

							foreach ($seo_urls as $seo_url) {
								if ($seo_url['store_id'] == $store_id && $seo_url['query'] != 'product_id=' . $id) {
									$errors = true;
									$this->error['seo_urls'][$store_id]['values'][$language_id]['value'] = $this->language->get('error_keyword');

									break;
								}
							}
						}
					}
				}
			}
		}

		if ($column == 'date_available') {
			if (!validate_date($value, 'Y-m-d')) {
				$errors = true;
				$this->error['error'] = $this->language->get('error_date');
			}
		}

		if ($column == 'date_added') {
			if (!validate_date($value, 'Y-m-d H:i:s')) {
				$errors = true;
				$this->error['error'] = $this->language->get('error_datetime');
			}
		}

		if ($column == 'discounts' && isset($value['product_discount'])) {
			foreach ((array)$value['product_discount'] as $idx => $v) {
				if (strpos(trim($v['price']), "#") === 0 && preg_match('/^#\s*(?P<operation>[+-\/\*])\s*(?P<operand>-?\d+\.?\d*)(?P<percent>%)?$/', trim($v['price'])) !== 1) {
					$errors = true;
					$this->error['discounts'][$idx]['price'] = $this->language->get('error_expression');
				}
				if ($v['date_start'] != "" && !validate_date($v['date_start'], 'Y-m-d')) {
					$errors = true;
					$this->error['discounts'][$idx]['date_start'] = $this->language->get('error_date');
				}
				if ($v['date_end'] != "" && !validate_date($v['date_end'], 'Y-m-d')) {
					$errors = true;
					$this->error['discounts'][$idx]['date_end'] = $this->language->get('error_date');
				}
			}
		}

		if ($column == 'specials' && isset($value['product_special'])) {
			foreach ((array)$value['product_special'] as $idx => $v) {
				if (strpos(trim($v['price']), "#") === 0 && preg_match('/^#\s*(?P<operation>[+-\/\*])\s*(?P<operand>-?\d+\.?\d*)(?P<percent>%)?$/', trim($v['price'])) !== 1) {
					$errors = true;
					$this->error['specials'][$idx]['price'] = $this->language->get('error_expression');
				}
				if ($v['date_start'] != "" && !validate_date($v['date_start'], 'Y-m-d')) {
					$errors = true;
					$this->error['specials'][$idx]['date_start'] = $this->language->get('error_date');
				}
				if ($v['date_end'] != "" && !validate_date($v['date_end'], 'Y-m-d')) {
					$errors = true;
					$this->error['specials'][$idx]['date_end'] = $this->language->get('error_date');
				}
			}
		}

		if ($column == 'options' && isset($value['product_option'])) {
			foreach ((array)$value['product_option'] as $idx1 => $product_option) {
				if (isset($product_option['product_option_value'])) {
					foreach ((array)$product_option['product_option_value'] as $idx2 => $v) {
						if (strpos(trim($v['price']), "#") === 0 && preg_match('/^#\s*(?P<operation>[+-\/\*])\s*(?P<operand>-?\d+\.?\d*)(?P<percent>%)?$/', trim($v['price'])) !== 1) {
							$errors = true;
							$this->error['options'][$idx1]['product_option_value'][$idx2]['price'] = $this->language->get('error_expression');
						}
					}
				}
			}
		}

		if ($column == 'recurrings' && isset($value['product_recurring'])) {
			foreach ((array)$value['product_recurring'] as $idx => $v) {
				if (!isset($v['recurring_id']) || $v['recurring_id'] == "") {
					$errors = true;
					$this->error['recurrings'][$idx]['recurring_id'] = $this->language->get('error_recurring');
				}
			}
		}

		if ($column == 'attributes' && isset($value['product_attribute'])) {
			foreach ((array)$value['product_attribute'] as $idx => $v) {
				if (!isset($v['attribute_id']) || $v['attribute_id'] == "") {
					$errors = true;
					$this->error['attributes'][$idx]['id'] = $this->language->get('error_attribute');
				}
			}
		}

		if ($column == 'descriptions') {
			foreach ((array)$value['product_description'] as $language_id => $v) {
				if (utf8_strlen($v['meta_title']) < 3 || utf8_strlen($v['meta_title']) > 255) {
					$errors = true;
					$this->error['descriptions'][$language_id]['meta_title'] = $this->language->get('error_meta_title');
				}
			}
		}

		if ($errors && empty($this->alert['warning']['warning'])) {
			$this->alert['warning']['warning'] = $this->language->get('error_warning');
		}

		return !$errors;
	}

	protected function validateSettings(&$data) {
		$errors = !$this->validatePermission();

		if (isset($data['module_product_quick_edit_items_per_page'])) {
			if (!is_numeric($data['module_product_quick_edit_items_per_page'])) {
				$errors = true;
				$this->error['module_product_quick_edit_items_per_page'] = $this->language->get('error_numeric');
			} else if ((int)$data['module_product_quick_edit_items_per_page'] < -1 || (int)$data['module_product_quick_edit_items_per_page'] == 0) {
				$errors = true;
				$this->error['module_product_quick_edit_items_per_page'] = $this->language->get('error_items_per_page');
			}
		}

		if (isset($data['module_product_quick_edit_list_view_image_width'])) {
			if (!is_numeric($data['module_product_quick_edit_list_view_image_width']) || (int)$data['module_product_quick_edit_list_view_image_width'] < 1) {
				$errors = true;
				$this->error['module_product_quick_edit_list_view_image_width'] = $this->language->get('error_image_width');
			}
		}

		if (isset($data['module_product_quick_edit_list_view_image_height'])) {
			if (!is_numeric($data['module_product_quick_edit_list_view_image_height']) || (int)$data['module_product_quick_edit_list_view_image_height'] < 1) {
				$errors = true;
				$this->error['module_product_quick_edit_list_view_image_height'] = $this->language->get('error_image_height');
			}
		}

		if (isset($data['module_product_quick_edit_cache_size'])) {
			if (!is_numeric($data['module_product_quick_edit_cache_size']) || (int)$data['module_product_quick_edit_cache_size'] < 1 || (int)$data['module_product_quick_edit_cache_size'] < (int)$this->config->get('module_product_quick_edit_items_per_page')) {
				$errors = true;
				$this->error['module_product_quick_edit_cache_size'] = $this->language->get('error_cache_size');
			}
		}

		if ($errors && empty($this->alert['warning']['warning'])) {
			$this->alert['warning']['warning'] = $this->language->get('error_warning');
		}

		return !$errors;
	}

	protected function urlParams() {
		$url = '&user_token=' . $this->session->data['user_token'];

		return $url;
	}
}
