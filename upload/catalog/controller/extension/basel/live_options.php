<?php
/**
 * Name: Ajax Live Options
 * Apply Version: 2.3.X.X
 * Version: 2.3.X.X
 * Author: 		Denise (rei7092@gmail.com)
 */
class ControllerExtensionBaselLiveOptions extends Controller {
		private $error = array(); 
		private $data  = array();
	
	public function __construct($params) {
    	parent::__construct($params);

		$this->options_container       = '.product-info';
		$this->special_price_container = '.live-price-new';
		$this->price_container         = '.live-price';
		$this->tax_price_container     = '.live-price-tax';
	}
	public function index() { 
 
		$json           = array();
		$options_makeup = $options_makeup_notax = 0;
		$currency_code = $this->session->data['currency'];

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = (int)$this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$this->load->model('catalog/product');

		// Cache name
		if (isset($this->request->post['option']) && is_array($this->request->post['option'])) {
			$options_hash = serialize($this->request->post['option']);
		} else {
			$options_hash = '';
		}

			$product_info = $this->model_catalog_product->getProduct($product_id);
			// Prepare data
			if ($product_info) {

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$this->data['price'] = $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$this->data['price'] = false;
				}

				if ((float)$product_info['special']) {
					$this->data['special'] = $this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$this->data['special'] = false;
				}

				// Discount
				$discount_price = $this->get_discount_price($product_id, $quantity);
				if($discount_price && !$this->data['special']){
					if ((float)$discount_price) {
						$this->data['price'] = $this->tax->calculate($discount_price, $product_info['tax_class_id'], $this->config->get('config_tax'));
					} else {
						$this->data['price'] = false;
					}
				}

				// If some options are selected
				if (isset($this->request->post['option']) && $this->request->post['option']) {
					$option_tax = $this->config->get('config_tax') ? 'P' : false;
					foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) { 
						foreach ($option['product_option_value'] as $option_value) {
							if (isset($this->request->post['option'][$option['product_option_id']])) {
								if(is_array($this->request->post['option'][$option['product_option_id']])){
									foreach ($this->request->post['option'][$option['product_option_id']] as $product_option_id) {
										if($product_option_id == $option_value['product_option_value_id']){
											$options_makeup += $this->get_options_makeup($option_value, $product_info['tax_class_id'], $option_tax);
											$options_makeup_notax += $this->get_options_makeup($option_value, 0, $option_tax);
										}
									}
								}
								elseif($this->request->post['option'][$option['product_option_id']] == $option_value['product_option_value_id']){
									$options_makeup += $this->get_options_makeup($option_value, $product_info['tax_class_id'], $option_tax);
									$options_makeup_notax += $this->get_options_makeup($option_value, 0, $option_tax);
								}
							}
						}
					}
				}

				if ($this->data['price']) {
					$json['new_price']['price'] = $this->currency->format(($this->data['price'] + $options_makeup) * $quantity, $currency_code);

						if($currency_code=='HRK'){
		               $json['new_price']['priceeur'] = $this->currency->format(($this->data['price'] + $options_makeup) * $quantity, 'EUR');
		            }
		            else{
		                $json['new_price']['priceeur'] = '';
		            }
				} else {
					$json['new_price']['price'] = false;
					  $json['new_price']['priceeur'] = '';
				}

				if ($this->data['special']) {
					$json['new_price']['special'] = $this->currency->format(($this->data['special'] + $options_makeup) * $quantity, $currency_code);

					if($currency_code=='HRK'){
		               $json['new_price']['specialeur'] = $this->currency->format(($this->data['special'] + $options_makeup) * $quantity, 'EUR');
		            }
		            else{
		                $json['new_price']['specialeur'] = '';
		            }
				} else {
					$json['new_price']['special'] = false;
					  $json['new_price']['specialeur'] = '';
				}

				if ($this->config->get('config_tax')) {
					$json['new_price']['tax'] = $this->currency->format(((float)$product_info['special'] ? ($product_info['special'] + $options_makeup) : ($product_info['price'] + $options_makeup_notax)) * $quantity, $currency_code );

						if($currency_code=='HRK'){
		               $json['new_price']['taxeur'] = $this->currency->format(((float)$product_info['special'] ? ($product_info['special'] + $options_makeup) : ($product_info['price'] + $options_makeup_notax)) * $quantity, 'EUR' );

		            }
		            else{
		                $json['new_price']['taxeur'] = '';
		            }


				} else {
					$json['new_price']['tax'] = false;
					     $json['new_price']['taxeur'] = '';
				}
				
				$json['success'] = true;
				} else {
					$json['success'] = false;
				}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  	}
	private function get_options_makeup($option_value, $tax_class_id, $tax_type, $param = 'price'){
		$options_makeup = 0;
		if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
			if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value[$param]) {
				$price = $this->tax->calculate($option_value[$param], $tax_class_id, $tax_type);
			} else {
				$price = false;
			}
			if ($price) {
				if ($option_value[$param.'_prefix'] === '+') {
					$options_makeup = $options_makeup + (float)$price;
				} else {
					$options_makeup = $options_makeup - (float)$price;
				}
			}
			unset($price);
		}
		return $options_makeup;
	}

	private function get_discount_price($product_id, $discount_quantity){
		$price = false;
		$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

		if ($product_discount_query->num_rows) {
			$price = (float)$product_discount_query->row['price'];
		}
		return $price;
	}
	function js() {
		header('Content-Type: application/javascript'); 
		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

		$js = <<<HTML
			var price_with_options_ajax_call = function() {
				$.ajax({
					type: 'POST',
					url: 'index.php?route=extension/basel/live_options/index&product_id=$product_id',
					data: $('{$this->options_container} input[type=\'text\'], {$this->options_container} input[type=\'number\'], {$this->options_container} input[type=\'hidden\'], {$this->options_container} input[type=\'radio\']:checked, {$this->options_container} input[type=\'checkbox\']:checked, {$this->options_container} select, {$this->options_container} textarea'),
					dataType: 'json',
					
					success: function(json) {
						if (json.success) {
							
							if ($('{$this->options_container} {$this->tax_price_container}').length > 0 && json.new_price.tax) {
								animation_on_change_price_with_options('{$this->options_container} {$this->tax_price_container}', json.new_price.tax + ' <small>' + json.new_price.taxeur + '</small>');
							}
							if ($('{$this->options_container} {$this->special_price_container}').length > 0 && json.new_price.special) {
								animation_on_change_price_with_options('{$this->options_container} {$this->special_price_container}', json.new_price.special + ' <small>' + json.new_price.specialeur + '</small>');
							}
							if ($('{$this->options_container} {$this->price_container}').length > 0 && json.new_price.price) {
								animation_on_change_price_with_options('{$this->options_container} {$this->price_container}', json.new_price.price + ' <small>' + json.new_price.priceeur + '</small>');
							}
						}
					},
					error: function(error) {
						console.log('error: '+error);
					}
				});
			}
			
			var animation_on_change_price_with_options = function(selector_class_or_id, new_html_content) {
				$(selector_class_or_id).fadeOut(250, function() {
					$(this).html(new_html_content).fadeIn(150);
				});
			}

			$(document).on('change', '{$this->options_container} input[type=\'text\'], {$this->options_container} input[type=\'number\'], {$this->options_container} input[type=\'hidden\'], {$this->options_container} input[type=\'radio\']:checked, {$this->options_container} input[type=\'checkbox\'], {$this->options_container} select, {$this->options_container} textarea, {$this->options_container} input[name=\'quantity\']', function () {
				
			price_with_options_ajax_call();
			});
		
HTML;
echo $js;
exit;
	}
}