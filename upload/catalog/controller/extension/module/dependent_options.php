<?php
require_once(DIR_SYSTEM . 'library/equotix/dependent_options/equotix.php');
class ControllerExtensionModuleDependentOptions extends Equotix {
	protected $code = 'dependent_options';
	protected $extension_id = '14';
	
	public function index() {
		$this->load->model('tool/image');
		
		if (isset($this->request->get['parent_id'])) {
			$product_option_id = (int)$this->request->get['parent_id'];
		} else {
			$product_option_id = 0;
		}
		
		if (isset($this->request->get['value'])) {
			$product_option_value_id = (int)$this->request->get['value'];
		} else {
			$product_option_value_id = 0;
		}
		
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		$this->load->model( 'catalog/product');
		
		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$json = array();
		
		if (!$this->validated()) {
			$this->response->setOutput(json_encode($json));
			
			return;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_option_id = '" . (int)$product_option_id . "'");
		
		if ($query->num_rows) {
			$parent_option = (int)$query->row['option_id'];
		} else {
			$parent_option = 0;
		}
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$product_option_value_id . "'");
		
		if ($query->num_rows) {
			$option_value_id = (int)$query->row['option_value_id'];
		} else {
			$option_value_id = 0;
		}
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON o.option_id = po.option_id LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) LEFT JOIN " . DB_PREFIX . "product_option_parent pop ON pop.product_option_id = po.product_option_id WHERE pop.parent_option = '" . (int)$parent_option . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		if ($query->num_rows) {
			$json['option'] = array();
			
			foreach ($query->rows as $result) {
				if ($result['type'] == 'select' || $result['type'] == 'radio' || $result['type'] == 'checkbox' || $result['type'] == 'image') {
					$query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "product_option_value_to_option_value pov2ov ON pov2ov.product_option_value_id = pov.product_option_value_id LEFT JOIN " . DB_PREFIX . "option_value ov ON ov.option_value_id = pov.option_value_id LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON ovd.option_value_id = ov.option_value_id WHERE ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pov2ov.option_value_id = '" . (int)$option_value_id . "' AND pov.product_option_id = '" . (int)$result['product_option_id'] . "' ORDER BY ov.sort_order ASC");
				
					$option_value_data = array();
					
					if ($query1->num_rows) {
						foreach ($query1->rows as $option_value) {
							if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
								if (version_compare(VERSION, '2.2.0.0', '<')) {
									if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
										$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
									} else {
										$price = false;
									}
								} else {
									if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
										$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
									} else {
										$price = false;
									}
								}
								
								$option_value_data[] = array(
									'product_option_value_id' => $option_value['product_option_value_id'],
									'option_value_id'         => $option_value['option_value_id'],
									'name'                    => $option_value['name'],
									'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
									'price'                   => $price,
									'price_prefix'            => $option_value['price_prefix']
								);
							}
						}
					}
				} else {
					$query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value_to_option_value WHERE product_option_id = '" . (int)$result['product_option_id'] . "' AND product_option_value_id = '1' AND option_value_id = '" . (int)$option_value_id . "' AND product_id = '" . (int)$product_id . "'");
					
					if ($query1->num_rows) {
						$option_value_data = 1;
					} else {
						$option_value_data = 0;
					}
				}
				
				$json['option'][] = array(
					'product_option_id' => $result['product_option_id'],
					'option_id'         => $result['option_id'],
					'name'              => $result['name'],
					'type'              => $result['type'],
					'option_value'      => $option_value_data,
					'required'          => $result['required']
				);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function eventPostModelCatalogProductGetProductOptions($route, $args, &$output) {
		$product_id = $args[0];
		
		foreach ($output as $key => $product_option) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_parent WHERE product_id = '" . (int)$product_id . "' AND product_option_id = '" . (int)$product_option['product_option_id'] . "'");
			
			$output[$key]['parent_option'] = $query->num_rows ? $query->row['parent_option'] : 0;
		}
	}
}