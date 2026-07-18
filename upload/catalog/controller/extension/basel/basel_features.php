<?php
class ControllerExtensionBaselBaselFeatures extends Controller {

// Lang curr title 
public function lang_curr_title() {
		
		$this->load->model('localisation/language');
		$this->load->model('localisation/currency');
				
		$curr_code = '<span>' . $this->session->data['currency'] . '</span>';
		$lang_code = $this->session->data['language'];

		$output = '';
		// Language
		$data['languages'] = array();
		$results2 = $this->model_localisation_language->getLanguages();
		foreach ($results2 as $result2) {
			if ($result2['status']) {
				$languages[] = array(
					'name' => $result2['name'],
					'code' => $result2['code']
				);
			}
		}
		foreach ($languages as $language) { 
		if (count($languages) > 1) {
     	if ($language['code'] == $lang_code) { 
		$output .= '<span>' . $language['name'] . '</span>';
		}}}
		// Currency
		$data['currencies'] = array();
		$results = $this->model_localisation_currency->getCurrencies();
		foreach ($results as $result) {
			if ($result['status']) {
				$currencies[] = array();
			}
		}
		if (count($currencies) > 1) {
		$output .= $curr_code; 
		}
		
		return ($output);
}

// Add to Cart
public function add_to_cart() {
	
		if ((float)VERSION >= 3.0) {
			$extension_load = 'setting/extension';
			$extension_path = 'model_setting_extension';
			$total_prefix = 'total_';
		} else {
			$extension_load = 'extension/extension';
			$extension_path = 'model_extension_extension';
			$total_prefix = '';
		}
		
		$this->load->language('checkout/cart');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if (isset($this->request->post['quantity']) && ((int)$this->request->post['quantity'] >= $product_info['minimum'])) {
				$quantity = (int)$this->request->post['quantity'];
			} else {
				$quantity = $product_info['minimum'] ? $product_info['minimum'] : 1;
			}

			if (isset($this->request->post['option'])) {
				$option = array_filter($this->request->post['option']);
			} else {
				$option = array();
			}

			$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

			foreach ($product_options as $product_option) {
				if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
					$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
				}
			}

			if (isset($this->request->post['recurring_id'])) {
				$recurring_id = $this->request->post['recurring_id'];
			} else {
				$recurring_id = 0;
			}

			$recurrings = $this->model_catalog_product->getProfiles($product_info['product_id']);

			if ($recurrings) {
				$recurring_ids = array();

				foreach ($recurrings as $recurring) {
					$recurring_ids[] = $recurring['recurring_id'];
				}

				if (!in_array($recurring_id, $recurring_ids)) {
					$json['error']['recurring'] = $this->language->get('error_recurring_required');
				}
			}

			if (!$json) {

				$this->cart->add($this->request->post['product_id'], $quantity, $option, $recurring_id);
						
				if ($this->config->get('basel_cart_action') == 'redirect_cart') $json['success_redirect'] = $this->url->link('checkout/cart');
				if ($this->config->get('basel_cart_action') == 'redirect_checkout') $json['success_redirect'] = $this->url->link('checkout/checkout', '', true);
				
				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));
				
				$this->load->model('tool/image');
				if ($product_info['image'])	{
				$json['image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_default_image_cart_width'), $this->config->get('theme_default_image_cart_height'));
				} else {
				$json['image'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_default_image_cart_width'), $this->config->get('theme_default_image_cart_height'));
				}
				
				// Unset all shipping and payment methods
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);

				// Totals
				$this->load->model($extension_load);

				$totals = array();
				$taxes = $this->cart->getTaxes();
				$total = 0;
		
				// Because __call can not keep var references so we put them into an array. 			
				$total_data = array(
					'totals' => &$totals,
					'taxes'  => &$taxes,
					'total'  => &$total
				);

				// Display prices
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$sort_order = array();

					$results = $this->$extension_path->getExtensions('total');

					foreach ($results as $key => $value) {
						$sort_order[$key] = $this->config->get($total_prefix . $value['code'] . '_sort_order');
					}

					array_multisort($sort_order, SORT_ASC, $results);

					foreach ($results as $result) {
						if ($this->config->get($total_prefix . $result['code'] . '_status')) {
							$this->load->model('extension/total/' . $result['code']);

							// We have to put the totals in an array so that they pass by reference.
							$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
						}
					}

					$sort_order = array();

					foreach ($totals as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $totals);
				}
				
			$json['total_items'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
			$json['total_amount'] = $this->currency->format($total, $this->session->data['currency']);

			} else {
				$json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
}


// Remove from cart
public function remove_from_cart() {
	
		if ((float)VERSION >= 3.0) {
			$extension_load = 'setting/extension';
			$extension_path = 'model_setting_extension';
			$total_prefix = 'total_';
		} else {
			$extension_load = 'extension/extension';
			$extension_path = 'model_extension_extension';
			$total_prefix = '';
		}
		
		$this->load->language('checkout/cart');

		$json = array();

		// Remove
		if (isset($this->request->post['key'])) {
			$this->cart->remove($this->request->post['key']);

			unset($this->session->data['vouchers'][$this->request->post['key']]);

			$this->session->data['success'] = $this->language->get('text_remove');

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);

			// Totals
			$this->load->model($extension_load);

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array. 			
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			// Display prices
			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$sort_order = array();

				$results = $this->$extension_path->getExtensions('total');

				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($total_prefix . $value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $results);

				foreach ($results as $result) {
					if ($this->config->get($total_prefix . $result['code'] . '_status')) {
						$this->load->model('extension/total/' . $result['code']);

						// We have to put the totals in an array so that they pass by reference.
						$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					}
				}

				$sort_order = array();

				foreach ($totals as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $totals);
			}

			$json['total_items'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
			$json['total_amount'] = $this->currency->format($total, $this->session->data['currency']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
}	
	
	
// Add to Compare
public function add_to_compare() {
		$this->load->language('product/compare');

		$json = array();

		if (!isset($this->session->data['compare'])) {
			$this->session->data['compare'] = array();
		}

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			if (!in_array($this->request->post['product_id'], $this->session->data['compare'])) {
				if (count($this->session->data['compare']) >= 4) {
					array_shift($this->session->data['compare']);
				}

				$this->session->data['compare'][] = $this->request->post['product_id'];
			}
			
			$this->load->model('tool/image');
			if ($product_info['image'])	{
			$json['image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_default_image_cart_width'), $this->config->get('theme_default_image_cart_height'));
			} else {
			$json['image'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_default_image_cart_width'), $this->config->get('theme_default_image_cart_height'));
			}
			if ($this->config->get('basel_compare_action') == 'redirect') $json['success_redirect'] = $this->url->link('product/compare');
			
			$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('product/compare'));

			$json['total'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
// Add to Wishlist
public function add_to_wishlist() {
		$this->load->language('account/wishlist');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			
		$this->load->model('tool/image');
		if ($product_info['image'])	{
		$json['image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_default_image_cart_width'), $this->config->get('theme_default_image_cart_height'));
		} else {
		$json['image'] = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_default_image_cart_width'), $this->config->get('theme_default_image_cart_height'));
		}
		if ($this->config->get('basel_wishlist_action') == 'redirect') $json['success_redirect'] = $this->url->link('account/wishlist');
			
			if ($this->customer->isLogged()) {
				// Edit customers cart
				$this->load->model('account/wishlist');

				$this->model_account_wishlist->addWishlist($this->request->post['product_id']);

				$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));

				$json['total'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
				$json['total_counter'] = $this->model_account_wishlist->getTotalWishlist();
			} else {
				if (!isset($this->session->data['wishlist'])) {
					$this->session->data['wishlist'] = array();
				}

				$this->session->data['wishlist'][] = $this->request->post['product_id'];

				$this->session->data['wishlist'] = array_unique($this->session->data['wishlist']);

				$json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true), $this->url->link('product/product', 'product_id=' . (int)$this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist'));

				$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
				$json['total_counter'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

// Search
public function basel_search() {
	
		$this->load->language('basel/basel_theme');
		
		$data['basel_text_search'] = $this->language->get('basel_text_search');
		$data['basel_text_category'] = $this->language->get('basel_text_category');		

		if (isset($this->request->get['search'])) {
			$data['search'] = $this->request->get['search'];
		} else {
			$data['search'] = '';
		}
		if (isset($this->request->get['category_id'])) {
			$data['category_id'] = $this->request->get['category_id'];
		} else {
			$data['category_id'] = '0';
		}
		
		$this->load->model('catalog/category');
		$data['categories'] = array();
		$categories_1 = $this->model_catalog_category->getCategories(0);
		foreach ($categories_1 as $category_1) {
			$level_2_data = array();
			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);
			foreach ($categories_2 as $category_2) {
				$level_3_data = array();
				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);
				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'category_id' => $category_3['category_id'],
						'name'        => $category_3['name'],
					);
				}
				$level_2_data[] = array(
					'category_id' => $category_2['category_id'],
					'name'        => $category_2['name'],
					'children'    => $level_3_data
				);
			}
			$data['categories'][] = array(
				'category_id' => $category_1['category_id'],
				'name'        => $category_1['name'],
				'children'    => $level_2_data
			);
		}
		if ($this->config->get('theme_default_directory') == 'basel') {
			if ($this->config->get('basel_header') == 'header6') {
				return $this->load->view('common/basel_search_full', $data);
			} else {
				return $this->load->view('common/basel_search', $data);
			}
		}
}
	
	
// Get Total Amount
public function total_amount() {
		
		if ((float)VERSION >= 3.0) {
			$extension_load = 'setting/extension';
			$extension_path = 'model_setting_extension';
			$total_prefix = 'total_';
		} else {
			$extension_load = 'extension/extension';
			$extension_path = 'model_extension_extension';
			$total_prefix = '';
		}
		
		$this->load->model($extension_load);
		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;
		$total_data = array(
			'totals' => &$totals,
			'taxes'  => &$taxes,
			'total'  => &$total
		);
		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			$sort_order = array();
			$results = $this->$extension_path->getExtensions('total');
			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($total_prefix . $value['code'] . '_sort_order');
			}
			array_multisort($sort_order, SORT_ASC, $results);
			foreach ($results as $result) {
				if ($this->config->get($total_prefix . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
				}
			}
		}
		return $this->currency->format($total, $this->session->data['currency']);
}

// Newsletter Subscribe
public function subscribe() {
	
	$this->load->language('basel/basel_theme');
	
		$json = array();
		$module_id = isset($this->request->get['module']) ? (int)$this->request->get['module'] : 0;
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (empty($this->request->post['email']) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$json['error'] = $this->language->get('basel_subscribe_invalid_email');
			}
			if (!isset($json['error']) && $module_id === 48 && empty($this->request->post['consent'])) {
				$json['error'] = $this->language->get('basel_subscribe_consent_error');
			}
			if (!isset($json['error'])) {
				$this->load->model('extension/basel/newsletter'); 
			if($this->model_extension_basel_newsletter->checkRegistered($this->request->post)){
			   	$this->model_extension_basel_newsletter->UpdateRegistered($this->request->post,1);
				$json['success'] = $this->language->get('basel_subscribe_success');
			} else if ($this->model_extension_basel_newsletter->checkExist($this->request->post)){
				$json['error'] = $this->language->get('basel_subscribe_email_exist');
			} else {
				$this->model_extension_basel_newsletter->subscribe($this->request->post);
				$json['success'] = $this->language->get('basel_subscribe_success');
		   	} 
		  }
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
}
	
// Newsletter Unsubscribe
public function unsubscribe() {
	
	$this->load->language('basel/basel_theme');
	
		$json = array();
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if(!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)){
				$json['error'] = $this->language->get('basel_subscribe_invalid_email');
			}
			if (!isset($json['error'])) {
				$this->load->model('extension/basel/newsletter'); 
			if($this->model_extension_basel_newsletter->checkRegistered($this->request->post)){
			   	$this->model_extension_basel_newsletter->UpdateRegistered($this->request->post,0);
				$json['success'] = $this->language->get('basel_unsubscribe_unsubscribed');
			} else if (!$this->model_extension_basel_newsletter->checkExist($this->request->post)){
				$json['error'] = $this->language->get('basel_unsubscribe_not_found');
			} else {
				$this->model_extension_basel_newsletter->unsubscribe($this->request->post);
				$json['success'] = $this->language->get('basel_unsubscribe_unsubscribed');
		   	} 
		  }
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
}

// Contact Form Captcha
public function basel_captcha() {
		$num1=rand(2,6);
		$num2=rand(2,6);
		$this->session->data['captcha_contact_form'] = $num1+$num2;
		$image = imagecreatetruecolor(58, 22);
		$width = imagesx($image);
		$height = imagesy($image);
		$black = imagecolorallocate($image, 100, 100, 100);
		$white = imagecolorallocate($image, 255, 255, 255);
		imagefilledrectangle($image, 0, 0, $width, $height, $white);
		imagestring($image, 4, 0, 3, "$num1"." + "."$num2"." =", $black);
		header('Content-type: image/png');
		imagepng($image);
		imagedestroy($image);
}
// Contact Form	
public function basel_send_message () {
	$this->load->language('basel/basel_theme');
	$json = array();
	if ($this->request->server['REQUEST_METHOD'] == 'POST') {
		if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 100)) {
			$json['error'] = $this->language->get('basel_error_name');
		}
		if ((utf8_strlen($this->request->post['email']) < 2) || (utf8_strlen($this->request->post['email']) > 60)) {
			$json['error'] = $this->language->get('basel_error_email');
		}
		if ((utf8_strlen($this->request->post['text']) < 5) || (utf8_strlen($this->request->post['text']) > 1000)) {
			$json['error'] = $this->language->get('basel_error_message');
		}
		if (empty($this->session->data['captcha_contact_form']) || ($this->session->data['captcha_contact_form'] != $this->request->post['captcha'])) {
			$json['error'] = $this->language->get('basel_error_captcha');

		}

		if (!isset($json['error'])) {
			$mail = new Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');			
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->request->post['email']);
			$mail->setSender(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode(sprintf($this->language->get('basel_email_subject'), $this->request->post['name']), ENT_QUOTES, 'UTF-8'));
			$mail->setText($this->request->post['text']);
			$mail->send();
			$json['success'] = $this->language->get('basel_text_success_form');
		}
	}
	$this->response->addHeader('Content-Type: application/json');
	$this->response->setOutput(json_encode($json));
}


// Popup
public function basel_popup() {
		$lang_id = $this->config->get('config_language_id');
		$data['module'] = '99';
		$this->load->language('basel/basel_theme');
		$data['basel_subscribe_email'] = $this->language->get('basel_subscribe_email');
		$data['basel_subscribe_btn'] = $this->language->get('basel_subscribe_btn');
		$data['popup_width'] = $this->config->get('basel_popup_note_w');
		$data['popup_height'] = $this->config->get('basel_popup_note_h');
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		if (is_file(DIR_IMAGE . $this->config->get('basel_popup_note_img'))) {
			$data['img'] = $server . 'image/' . $this->config->get('basel_popup_note_img');
		} else {
			$data['img'] = '';
		}
		
		$content_block = $this->config->get('basel_popup_note_block');
$data['popup_content_block'] = html_entity_decode(str_replace('{signup}',$this->load->view('extension/module/content_widgets/subscribe_field', $data),$content_block[$this->config->get('config_language_id')]), ENT_QUOTES, 'UTF-8');
		$content_title = $this->config->get('basel_popup_note_title');
		$data['popup_title'] = html_entity_decode($content_title[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		
		$this->response->setOutput($this->load->view('common/widgets/popup', $data));

}

}
