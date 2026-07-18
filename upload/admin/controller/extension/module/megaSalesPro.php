<?php
class ControllerExtensionModuleMegaSalesPro extends Controller {
	private $error = array(); 

	public function index() {   

		$this->load->model('extension/module/megaSalesPro');
		$this->check_the_tables();

		$this->load->language('extension/module/megaSalesPro');
		$this->document->setTitle($this->language->get('heading_title'));

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_date_start'] = $this->language->get('text_date_start');
		$data['text_date_start_must'] = $this->language->get('text_date_start_must');
		$data['text_date_end'] = $this->language->get('text_date_end');
		$data['text_date_end_must'] = $this->language->get('text_date_end_must');
		$data['text_discount'] = $this->language->get('text_discount');
		$data['text_discount_type'] = $this->language->get('text_discount_type');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_value_off'] = $this->language->get('text_value_off');
		$data['text_customer_group'] = $this->language->get('text_customer_group');
		$data['text_customer_groups'] = $this->language->get('text_customer_groups');
		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_product_category'] = $this->language->get('text_product_category');
		$data['text_categories'] = $this->language->get('text_categories');
		$data['text_for_categories'] = $this->language->get('text_for_categories');
		$data['text_exclude_child'] = $this->language->get('text_exclude_child');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_for_manufacturers'] = $this->language->get('text_for_manufacturers');
		$data['text_product_filter'] = $this->language->get('text_product_filter');
		$data['text_for_filters'] = $this->language->get('text_for_filters');
		$data['text_products_to_exclude'] = $this->language->get('text_products_to_exclude');
		$data['text_priority'] = $this->language->get('text_priority');
		$data['text_lower_higher'] = $this->language->get('text_lower_higher');
		$data['text_round_prices'] = $this->language->get('text_round_prices');
		$data['text_round_example'] = $this->language->get('text_round_example');
		$data['text_remove_individual_sales'] = $this->language->get('text_remove_individual_sales');
		$data['text_specials_will_be_removed'] = $this->language->get('text_specials_will_be_removed');
		$data['text_remove_sale'] = $this->language->get('text_remove_sale');
		$data['text_will_delete'] = $this->language->get('text_will_delete');
		$data['text_are_you_sure'] = $this->language->get('text_are_you_sure');
		$data['text_filters'] = $this->language->get('text_filters');
		$data['text_manufacturers'] = $this->language->get('text_manufacturers');
		$data['text_excluded_products'] = $this->language->get('text_excluded_products');
		$data['text_other_settings'] = $this->language->get('text_other_settings');
		$data['text_actions'] = $this->language->get('text_actions');
		$data['text_all_sales'] = $this->language->get('text_all_sales');
		$data['text_add_new_sale'] = $this->language->get('text_add_new_sale');
		$data['text_remove_all_sales_tab'] = $this->language->get('text_remove_all_sales_tab');
		$data['text_remove_all_sales'] = $this->language->get('text_remove_all_sales');
		$data['text_no_sales'] = $this->language->get('text_no_sales');
		$data['text_exclude_child_label'] = $this->language->get('text_exclude_child_label');
		$data['text_remove_previous_label'] = $this->language->get('text_remove_previous_label');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_go_to_extension'] = $this->language->get('text_go_to_extension');
		$data['text_all'] = $this->language->get('text_all');

		$url = '';
		$this->load->model('setting/setting');
		    
		$success_link = $this->url->link('extension/module/megaSalesPro', 'user_token=' . $this->session->data['user_token'], 'SSL');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$arr['module_megaSalesPro_status'] = $this->request->post['module_megaSalesPro_status'];
			$this->model_setting_setting->editSetting('module_megaSalesPro', $arr);

			if ( (isset($this->request->post['clear_all_sales'])) && ($this->request->post['clear_all_sales']==1) ) {
				$this->model_extension_module_megaSalesPro->remove_all_specials();
			} else if ( (isset($this->request->post['edit_sale_id'])) && ($this->request->post['edit_sale_id']>0) ) {
				$this->delete_sale($this->request->post['edit_sale_id']);
				$this->save_changes($this->request->post);
			} else {
				$this->save_changes($this->request->post);
			}			

			$this->session->data['success'] = $data['text_success'].' <a href="'.$success_link.'">'.$data['text_go_to_extension'].'</a>';
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'));

		} else if ( (isset($this->request->get['delete_id'])) && ($this->request->get['delete_id']>0) ) {
			$this->delete_sale($this->request->get['delete_id']);
			$this->session->data['success'] = $data['text_success'].' <a href="'.$success_link.'">'.$data['text_go_to_extension'].'</a>';			
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => 'Home',
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => 'Module',
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => 'Mega Sales Pro',
			'href'      => $this->url->link('extension/module/megaSalesPro', 'user_token=' . $this->session->data['user_token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['action'] = $this->url->link('extension/module/megaSalesPro', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], 'SSL');

		if (isset($this->request->post['module_megaSalesPro_status'])) {
			$data['module_megaSalesPro_status'] = $this->request->post['module_megaSalesPro_status'];
		} else {
			$data['module_megaSalesPro_status'] = $this->config->get('module_megaSalesPro_status');
		}		

		$this->load->model('catalog/product');
		$data['products'] = array();

		$data['user_token'] = $this->session->data['user_token'];		

		$data['modules'] = array();

		if (isset($this->request->post['megaSalesPro_module'])) {
			$data['modules'] = $this->request->post['megaSalesPro_module'];
		} elseif ($this->config->get('megaSalesPro_module')) { 
			$data['modules'] = $this->config->get('megaSalesPro_module');
		}	

		$data['all_sales'] = $this->get_sales_list();

/* ************************************************************************************************* */

		$this->load->model('customer/customer_group');
      	$data['customer_group_list'] = $this->model_customer_customer_group->getCustomerGroups(); 

      	$this->load->model('catalog/manufacturer');
      	$data['manufacturer_list'] = $this->model_catalog_manufacturer->getManufacturers();

		$this->load->model('catalog/category');
		$cat_order['sort'] = 'name';
		$cat_order['order_by'] = 'DESC';
      	$data['category_list'] = $categories = $this->model_catalog_category->getCategories($cat_order);

      	$this->load->model('catalog/filter');
      	$filterz = $this->model_catalog_filter->getFilters(0);  
      	$this->array_sort_by_column($filterz, 'group');
       	$data['filter_list'] = $filterz;

/* ************************************************************************************************* */

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['footer'] = $this->load->controller('common/footer');
		$data['column_left'] = $this->load->controller('common/column_left');

		if ( (isset($this->request->get['sale_id'])) && ($this->request->get['sale_id']>0) ) {
			$sale_data = $this->get_sales_list($this->request->get['sale_id']);
			if (!empty($sale_data[0])) {
				$sale_data[0]['selected_categories'] = $this->model_extension_module_megaSalesPro->get_selected_category_ids($this->request->get['sale_id']);
				$sale_data[0]['selected_filters'] = $this->model_extension_module_megaSalesPro->get_selected_filter_ids($this->request->get['sale_id']);
				$sale_data[0]['selected_manufacturers'] = $this->model_extension_module_megaSalesPro->get_selected_manufacturer_ids($this->request->get['sale_id']);
				$sale_data[0]['selected_customer_groups'] = $this->model_extension_module_megaSalesPro->get_selected_customer_group_ids($this->request->get['sale_id']);
				$sale_data[0]['sale_id'] = $this->request->get['sale_id'];	
				$sale_data[0]['exclude_child'] = $sale_data[0]['exclude_child'];			
				$sale_data[0]['delete_url'] = $this->url->link('extension/module/megaSalesPro', 'user_token=' . $this->session->data['user_token'] . '&delete_id=' . $sale_data[0]['sale_id'] . $url, true);

				$data['sale_data'] = $sale_data[0];
			} else {
				$data['sale_data'] = array();
			}
			
			$this->response->setOutput($this->load->view('extension/module/megaSalesProEdit', $data));
		} else {
			$this->response->setOutput($this->load->view('extension/module/megaSalesPro', $data));
		}

	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/megaSalesPro')) {
					$this->error['warning'] = 'Warning: You do not have permission to modify module Mega Sales Pro!';
		}		

		return !$this->error;	
	}

	private function delete_sale($sale_id) {
		$this->load->model('catalog/product');
		
		$data_array = $this->get_sales_list($sale_id);
		if (!empty($data_array[0])) {
			$data = $data_array[0];
		}

		if (isset($data)) {

				$data['id'] = $sale_id;

				if (!empty($data['customer_groups'])) {
					$temp_cg = array(); 
					foreach ($data['customer_groups'] as $cg) {
						$temp_cg[] = $cg['customer_group_id'];
						$this->db->query("DELETE FROM " . DB_PREFIX . "mega_customer_group_to_sale where sale_id=".$data['id']);
					}
					$data['customer_groups'] = $temp_cg;
				}

				if (!empty($data['categories'])) {
					$temp_cat = array(); 
					foreach ($data['categories'] as $cat) {
						$temp_cat[] = $cat['category_id'];
						$this->db->query("DELETE FROM " . DB_PREFIX . "mega_category_to_sale where sale_id=".$data['id']);
					}
					$data['categories'] = $temp_cat;
				}

				if (!empty($data['manufacturers'])) {
					$temp_man = array(); 
					foreach ($data['manufacturers'] as $man) {
						$temp_man[] = $man['manufacturer_id'];
						$this->db->query("DELETE FROM " . DB_PREFIX . "mega_manufacturer_to_sale where sale_id=".$data['id']);
					}
					$data['manufacturers'] = $temp_man;
				}

				if (!empty($data['filters'])) {
					$temp_filt = array(); 
					foreach ($data['filters'] as $filt) {
						$temp_filt[] = $filt['filter_id'];
						$this->db->query("DELETE FROM " . DB_PREFIX . "mega_filter_to_sale where sale_id=".$data['id']);
					}
					$data['filters'] = $temp_filt;
				}

				if (!empty($data['excluded_products'])) {
					$temp_exp = array(); 
					foreach ($data['excluded_products'] as $exp) {
						$temp_exp[] = $exp['product_id'];
						$this->db->query("DELETE FROM " . DB_PREFIX . "mega_exclude_products where sale_id=".$data['id']);
					}
					$data['excluded_products'] = $temp_exp;
				}

				/*print_r($data);
				break;*/

				$this->delete_products($data);
				$this->db->query("DELETE FROM " . DB_PREFIX . "mega_sales where id=".$data['id']);
		}	

	}

	private function delete_products($array) {
		$this->load->model('catalog/product');

		$selected_categories = array();
		if (!empty($array['categories'])) {			
			if ($array['exclude_child']==1) {
				$selected_categories = $array['categories'];
			} else {
				foreach ($array['categories'] as $category_id) {
					$categories_temp = $this->getFullCategoryPath($category_id);

					if (!empty($categories_temp)) {
						foreach ($categories_temp as $one_category) {
							if (!in_array($one_category['category_id'], $selected_categories)) {
								$selected_categories[] = $one_category['category_id'];
							}
						}
					}
				}
			}
		}

		$array['selected_categories'] = $selected_categories;

		$product_list = $this->getTheProducs($array);

		if (!empty($product_list)) {
			foreach ($product_list as $product) {
				$sql = "DELETE FROM " . DB_PREFIX . "product_special WHERE product_id=".$product['product_id']." AND date_start='".$array['date_start']."' AND date_end='".$array['date_end']."' AND priority='".$array['priority']."' ";

				if (!empty($array['customer_groups'])) {
					$sql .= " AND (";
					foreach ($array['customer_groups'] as $customer_group) {
								$sql .= " customer_group_id = '" . (int)$customer_group . "' OR";
					}
					$sql = substr($sql, 0, -2).')';
				}
				$query = $this->db->query($sql);
			}
		}
		return 1;
	}	

	private function get_sales_list($id=0) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mega_sales";

		if ($id>0) {
			$sql .= " where id=".$id;
		} 

		$sql .= " order by id desc";

		$query = $this->db->query($sql);
		$sales_list = $query->rows;

		$sales_array = array();
		$m = 0;
		$url = '';

		if (!empty($sales_list)) {
			foreach ($sales_list as $sale) {
				$sales_array[$m]['date_start'] = $sale['date_start'];
				$sales_array[$m]['date_end'] = $sale['date_end'];
				$sales_array[$m]['discount_value'] = $sale['discount_value'];
				$sales_array[$m]['discount_type'] = $sale['discount_type'];
				$sales_array[$m]['priority'] = $sale['priority'];
				$sales_array[$m]['exclude_child'] = $sale['exclude_child'];
				$sales_array[$m]['round_prices'] = $sale['round_prices'];
				$sales_array[$m]['remove_individual_specials'] = $sale['remove_individual_specials'];

				$sales_array[$m]['filters'] = $this->model_extension_module_megaSalesPro->get_sale_filters($sale['id']);
				$sales_array[$m]['categories'] = $this->model_extension_module_megaSalesPro->get_sale_categories($sale['id']);
				$sales_array[$m]['manufacturers'] = $this->model_extension_module_megaSalesPro->get_sale_manufacturers($sale['id']);
				$sales_array[$m]['customer_groups'] = $this->model_extension_module_megaSalesPro->get_sale_customer_groups($sale['id']);
				$sales_array[$m]['excluded_products'] = $this->model_extension_module_megaSalesPro->get_sale_excluded_products($sale['id']);
				
				$sales_array[$m]['edit'] = $this->url->link('extension/module/megaSalesPro', 'user_token=' . $this->session->data['user_token'] . '&sale_id=' . $sale['id'] . $url, true);

				$m++;
			}
		}
		return $sales_array;
	}

	

	

	private function generate_sale($array) {
		$this->load->model('catalog/product');

		$selected_categories = array();
		if (!empty($array['categories'])) {			
			if ($array['exclude_child']==1) {
				$selected_categories = $array['categories'];
			} else {
				foreach ($array['categories'] as $category_id) {
					$categories_temp = $this->getFullCategoryPath($category_id);

					if (!empty($categories_temp)) {
						foreach ($categories_temp as $one_category) {
							if (!in_array($one_category['category_id'], $selected_categories)) {
								$selected_categories[] = $one_category['category_id'];
							}
						}
					}
				}
			}
		}

		$array['selected_categories'] = $selected_categories;

		$product_list = $this->getTheProducs($array);

		if (!empty($product_list)) {
			foreach ($product_list as $product) {

				if ($array['remove_individual_specials']==1) {
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_special where product_id=".$product['product_id']);
				}

				if ($product['price']>0) {
					$product['new_price'] = 0;

					if ($array['discount_type']=='percent') {
						$product['new_price'] = (float)((float)$product['price'] - ((float)$product['price'] * (float) $array['discount_value'])/100);

						if ($array['round_prices'] == 1 ) {
							$product['new_price'] = $product['new_price'] * 2;
							$product['new_price'] = round($product['new_price'], 1);
							$product['new_price'] = $product['new_price'] / 2;
						}
					} else if ($array['discount_type']=='value') {
							$product['new_price'] = (float)((float)$product['price'] - (float) $array['discount_value']);
					}

					if ($product['new_price']>=0) {

						if (!empty($array['customer_groups'])) {
							foreach ($array['customer_groups'] as $customer_group_id) {
								$this->db->query("INSERT INTO " . DB_PREFIX . "product_special(product_id, customer_group_id, price, date_start, date_end, priority) VALUES('" . (int)$product['product_id'] . "','". (int)$customer_group_id ."','" . (float)$product['new_price'] . "','".$array['date_start']."','".$array['date_end']."', ".$array['priority'].")");
							}
						}
						
					}
				}
			}
		}

		return 1;
	}

	private function getTheProducs($data = array()) {

						$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) ";

						if (!empty($data['filters'])) {
					      $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";     
					    }		

					    if (!empty($data['selected_categories'])) {
							$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";			
						}	

						$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";


						if (!empty($data['selected_categories'])) {
							$sql .= " AND (";
							foreach ($data['selected_categories'] as $category_id) {
								$sql .= " p2c.category_id = '" . (int)$category_id . "' OR";
							}
							$sql = substr($sql, 0, -2).')';
						}

						if (!empty($data['filters'])) {
							$sql .= " AND (";
							foreach ($data['filters'] as $filter_id) {
								$sql .= " pf.filter_id = '" . (int)$filter_id . "' OR";
							}
							$sql = substr($sql, 0, -2).')';
						} 

						if (!empty($data['manufacturers'])) {
							$sql .= " AND (";
							foreach ($data['manufacturers'] as $manufacturer_id) {
								$sql .= " p.manufacturer_id = '" . (int)$manufacturer_id . "' OR";
							}
							$sql = substr($sql, 0, -2).')';
						}

						if (!empty($data['exclude_products'])) {
							$sql .= " AND (";
							foreach ($data['exclude_products'] as $prod_id) {
								$sql .= " p.product_id != '" . (int)$prod_id . "' AND";
							}
							$sql = substr($sql, 0, -3).')';
						}
						
						$sql .= " GROUP BY p.product_id";
						$query = $this->db->query($sql);

						return $query->rows;
	} 

	private function save_changes($array) {

		$this->load->model('catalog/product');

		if ( (!empty($array['megasale_start'])) && (!empty($array['megasale_end'])) && (!empty($array['megasale_discount_value'])) ) {
				$data = array();

				$data['date_start'] = trim($array['megasale_start']);
				$data['date_end'] = trim($array['megasale_end']);
				$data['discount_value'] = trim($array['megasale_discount_value']);
				$data['discount_type'] = trim($array['megasale_discount_type']);
				$data['priority'] = $array['megasale_priority'];

				if ( (isset($array['exclude_child'])) && ($array['exclude_child']==1) ) { $data['exclude_child'] = 1; } else { $data['exclude_child'] = 0; }
				
				if ( (isset($array['megasale_remove_individual_sales'])) && ($array['megasale_remove_individual_sales']==1) ) { $data['remove_individual_specials'] = 1; } else { $data['remove_individual_specials'] = 0; }				

				if ( (isset($array['megasale_round_prices'])) && ($array['megasale_round_prices']==1) ) { $data['round_prices'] = 1; } else { $data['round_prices'] = 0; }

				$data['id'] = $this->model_extension_module_megaSalesPro->new_sale_to_db($data);

				if (!empty($array['megasale_customer_group'])) {
					foreach ($array['megasale_customer_group'] as $customer_group) {
						$data['customer_groups'][] = $customer_group;
					}

					$this->model_extension_module_megaSalesPro->customer_groups_to_sale($data);

				}

				if ( (!empty($array['megasale_product_category'])) && (isset($array['enable_category_filter'])) && ($array['enable_category_filter']==1) ) {
					foreach ($array['megasale_product_category'] as $category) {
						$data['categories'][] = $category;
					}
					$this->model_extension_module_megaSalesPro->categories_to_sale($data);
				}

				if ( (!empty($array['megasale_product_manufacturer']))  && (isset($array['enable_manufacturer_filter'])) && ($array['enable_manufacturer_filter']==1) ) {
					foreach ($array['megasale_product_manufacturer'] as $manufacturer) {
						$data['manufacturers'][] = $manufacturer;
					}
					$this->model_extension_module_megaSalesPro->manufacturers_to_sale($data);
				}

				if ( (!empty($array['megasale_product_filter'])) && (isset($array['enable_filter_filter'])) && ($array['enable_filter_filter']==1) ) {
					foreach ($array['megasale_product_filter'] as $filter) {
						$data['filters'][] = $filter;
					}
					$this->model_extension_module_megaSalesPro->filters_to_sale($data);
				}

				if (!empty($array['product'])) {
					foreach ($array['product'] as $product) {
						$data['exclude_products'][] = $product;
					}
					$this->model_extension_module_megaSalesPro->exclude_products($data);
				}

				$this->generate_sale($data);
		}
	}

	private function getFullCategoryPath($category_id=0) {
		$sql = "SELECT * from " . DB_PREFIX . "category_path WHERE path_id=".$category_id;
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function check_the_tables() {

    /* remove_old version */

    $query01 = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "mega_sales_pro' ");
    $msp_old_exist = count($query01->rows);

    if ($msp_old_exist>0) {
      $old_sales = $this->get_all_sales();

      if (!empty($old_sales)) {
        $arr = array();
        foreach ($old_sales as $one_sale) {
          $arr[$one_sale['id']] = 1;
        }

        $clear = $this->remove_selected($arr);
        $sql = "DROP TABLE `".DB_PREFIX."mega_sales_pro` ";
          $this->db->query( $sql );
      }
    }

    $this->model_extension_module_megaSalesPro->add_new_tables();
	return 1;
  }	


/* OLD FUNCTIONS !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */

	private function remove_selected($array) {

		$sales_array = array();

		if (!empty($array)) {
			foreach ($array as $key => $value) {
				$sales_temp = $this->get_all_sales($key);
				if (isset($sales_temp[0])) {
					$sales_array[] = $sales_temp[0];
				}
				
			}
		}

		if (!empty($sales_array)) {
			foreach ($sales_array as $one_sale) {

					if ( (isset($one_sale['exclude_child'])) && ($one_sale['exclude_child']==1) ) {
						$exclude_child = 1;
					} else {
						$exclude_child = 0;
					}
				
					$product_list = array();

					if ($one_sale['product_category_id']!=='0') {
						if ($one_sale['product_filter_id']!=='0') {

								if ( ($exclude_child==1) && ($one_sale['product_category_id']>0) ) {
									$selected_categories[0]['category_id'] = $one_sale['product_category_id'];
								} else {
									$selected_categories = $this->getFullCategoryPath($one_sale['product_category_id']);
								}

								if (count($selected_categories)>0) {
									foreach ($selected_categories as $selected_category) {
										$prod = $this->getProductsMega(array('manufacturer_id' => $one_sale['manufacturer_id'], 'filter_category_id' => $selected_category['category_id'], 'filter_filter_id' => $one_sale['product_filter_id']));

										if (count($prod)>0) {
											foreach ($prod as $one_product) {
												array_push($product_list,$one_product);
											}
										}									
									}								
								}

						} else {

								if ( ($exclude_child==1) && ($one_sale['product_category_id']>0) ) {
									$selected_categories[0]['category_id'] = $one_sale['product_category_id'];
								} else {
									$selected_categories = $this->getFullCategoryPath($one_sale['product_category_id']);
								}

								if (count($selected_categories)>0) {
									foreach ($selected_categories as $selected_category) {
										$prod = $this->getProductsMega(array('manufacturer_id' => $one_sale['manufacturer_id'], 'filter_category_id' => $selected_category['category_id']));

										if (count($prod)>0) {
											foreach ($prod as $one_product) {
												array_push($product_list,$one_product);
											}
										}									
									}								
								}							
						}
					} else {
						if ($one_sale['product_filter_id']!=='0') {
							$product_list = $this->getProductsMega(array('manufacturer_id' => $one_sale['manufacturer_id'], 'filter_filter_id' => $one_sale['product_filter_id']));
						} else {
							$product_list = $this->getProductsMega(array('manufacturer_id' => $one_sale['manufacturer_id']));
						}
					}

					if (!empty($product_list)) {
						foreach ($product_list as $one_product) {
							 $this->db->query("DELETE FROM " . DB_PREFIX . "product_special where product_id=".$one_product['product_id']." and customer_group_id=".$one_sale['customer_group']." and date_start='".$one_sale['date_start']."' and date_end='".$one_sale['date_end']."' ");
						}
					}

				$this->db->query("DELETE FROM " . DB_PREFIX . "mega_sales_pro where id=".$one_sale['id']." ");
			}
			
		}

	}


	private function get_all_sales($id=0) {
		$sql = "SELECT * FROM " . DB_PREFIX . "mega_sales_pro ";

		if ($id>0) {
			$sql .= "where id=".$id;
		} 

		$sql .= " order by id desc";

		$query = $this->db->query($sql);

		return $query->rows;
	}


	private function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {

	      $sort_col = array();

	      foreach ($arr as $key=> $row) {

	          $sort_col[$key] = $row[$col];

	          }

	         array_multisort($sort_col, $dir, $arr);
	}


	

	private function getProductsMega($data = array()) {
						$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) ";

						if (!empty($data['filter_filter_id'])) {
					      $sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)";     
					    }		

					    if (!empty($data['filter_category_id'])) {
							$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";			
						}	

						$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

						if (!empty($data['filter_name'])) {
							$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
						}

						if (!empty($data['filter_model'])) {
							$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
						}

						if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
							$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
						}

						if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
							$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
						}

						if (isset($data['filter_category_id']) && ($data['filter_category_id']>0)) {
							$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
						}

					    if ( (isset($data['filter_filter_id'])) && ($data['filter_filter_id']>0) ) {
					      $sql .= " AND pf.filter_id = '" . (int)$data['filter_filter_id'] . "'";
					    }		 

						if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
							$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
						}

						if ( (isset($data['manufacturer_id'])) && ($data['manufacturer_id']>0) ) {
							$sql .= " AND p.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'";
						}
						
						$sql .= " GROUP BY p.product_id";

						$sort_data = array(
							'pd.name',
							'p.model',
							'p.price',
							'p.quantity',
							'p.status',
							'p.sort_order'
						);

						if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
							$sql .= " ORDER BY " . $data['sort'];
						} else {
							$sql .= " ORDER BY pd.name";
						}

						if (isset($data['order']) && ($data['order'] == 'DESC')) {
							$sql .= " DESC";
						} else {
							$sql .= " ASC";
						}

						if (isset($data['start']) || isset($data['limit'])) {
							if ($data['start'] < 0) {
								$data['start'] = 0;
							}

							if ($data['limit'] < 1) {
								$data['limit'] = 20;
							}

							$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
						}

						$query = $this->db->query($sql);

						return $query->rows;
	}   

}
?>