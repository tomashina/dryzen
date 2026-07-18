<?php  
class ControllerExtensionModuleBaselProducts extends Controller {
	public function index($setting) {

    	$this->load->model('catalog/product');
		$this->load->model('extension/basel/basel');
		$this->load->language('basel/basel_theme');	
  		
		$data['basel_button_quickview'] = $this->language->get('basel_button_quickview');
		$data['basel_text_new'] = $this->language->get('basel_text_new');
		$data['basel_text_out_of_stock'] = $this->language->get('basel_text_out_of_stock');
		$data['basel_text_days'] = $this->language->get('basel_text_days');
		$data['basel_text_hours'] = $this->language->get('basel_text_hours');
		$data['basel_text_mins'] = $this->language->get('basel_text_mins');
		$data['basel_text_secs'] = $this->language->get('basel_text_secs');
		
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['text_tax'] = $this->language->get('text_tax');
		
		// RTL support
		$data['direction'] = $this->language->get('direction');
		if ($this->language->get('direction') == 'rtl') { $data['tooltip_align'] = 'right'; } else { $data['tooltip_align'] = 'left'; }
		
		// Block title
		$data['block_title'] = $setting['use_title'];
		$data['title_preline'] = false;
		$data['title'] = false;
		$data['title_subline'] = false;
		$data['link_title'] = false;
		
		$data['contrast'] = $setting['contrast'];
		$data['items_mobile_fw'] = $this->config->get('items_mobile_fw');
		
		if (!empty($setting['title_pl'][$this->config->get('config_language_id')])) {
		$data['title_preline'] = html_entity_decode($setting['title_pl'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['title_m'][$this->config->get('config_language_id')])) {
		$data['title'] = html_entity_decode($setting['title_m'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['title_b'][$this->config->get('config_language_id')])) {
		$data['title_subline'] = html_entity_decode($setting['title_b'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['link_title'][$this->config->get('config_language_id')])) {
		$data['link_title'] = html_entity_decode($setting['link_title'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		
		$data['tabstyle'] = $setting['tabstyle'];
		$data['carousel'] = $setting['carousel'];
		$data['carousel_a'] = $setting['carousel_a'];
		$data['carousel_b'] = $setting['carousel_b'];
		$data['columns'] = $setting['columns'];
		$data['rows'] = $setting['rows'];
		$data['use_margin'] = $setting['use_margin'];
		$data['margin'] = $setting['margin'];
		$data['img_width'] = $setting['image_width'];
		$data['use_button'] = $setting['use_button'];
		$data['link_href'] = $setting['link_href'];
		$data['countdown_status'] = $setting['countdown'];	
		$data['basel_list_style'] = $this->config->get('basel_list_style');
		$data['stock_badge_status'] = $this->config->get('stock_badge_status');
		$data['basel_text_out_of_stock'] = $this->language->get('basel_text_out_of_stock');
		$data['default_button_cart'] = $this->language->get('button_cart');
		$data['salebadge_status'] = $this->config->get('salebadge_status');
		
		static $module = 0;
		
		$data['tabs'] = array();

		$this->load->model('tool/image');
		
		$tabs = $this->config->get('showintabs_tab');
		
		$tabs = isset($tabs) ? $tabs : array();

    	foreach ($tabs as $key => $tab) {
			if(in_array($key, $setting['selected_tabs']['tabs'])) {
				if (!empty($tab['title'][$this->config->get('config_language_id')])) {
					$title = $tab['title'][$this->config->get('config_language_id')];
				}else{
					$title = 'Tab';
				}	
	
				$products = array();
	
				switch ($tab['data_source']) {
					case 'SP': //Select Products
						$results = $this->getSelectProducts($tab,$setting['limit']);
						break;
					case 'PG': //Product Group
						$results = $this->getProductGroups($tab,$setting['limit']);
						break;
					case 'CQ': //Custom Query
						$results = $this->getCustomQuery($tab,$setting['limit']);
						break;
					default: // Empty
						$this->log->write('SHOW_IN_TAB::ERROR: The tab don\'t have product configured.');
						break;
				}
				
				if (isset($results)) {
				foreach ($results as $result) {
					if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
					} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['image_width'], $setting['image_height']);
					}
					
					$images = $this->model_catalog_product->getProductImages($result['product_id']);
					if(isset($images[0]['image']) && !empty($images[0]['image'])){
					$images =$images[0]['image'];
				   	} else {
					$images = false;
					}
					
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

						if($this->session->data['currency']=='HRK'){
                        $priceeur = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 'EUR');
	                    }
	                    else{
	                        $priceeur = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 'HRK');

	                    }
					} else {
						$price = false;
						$priceeur  ='';
					}
							
					if ((float)$result['special']) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

						if($this->session->data['currency']=='HRK'){
                        $specialeur = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')),  'EUR');
                    }
                    else{
                        $specialeur = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')),  'HRK');

                    }
						$date_end = $this->model_extension_basel_basel->getSpecialEndDate($result['product_id']);
					} else {
						$special = false;
						 $specialeur  ='';
						$date_end = false;
					}
					
					if ( (float)$result['special'] && ($this->config->get('salebadge_status')) ) {
						if ($this->config->get('salebadge_status') == '2') {
							$sale_badge = '-' . number_format(((($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')))-($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'))))/(($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')))/100)), 0, ',', '.') . '%';
						} else {
							$sale_badge = $this->language->get('basel_text_sale');
						}		
					} else {
						$sale_badge = false;
					}

					$image2 = $this->model_catalog_product->getProductImages($result['product_id']);
					if(isset($image2[0]['image']) && !empty($image2[0]['image']) && $this->config->get('basel_thumb_swap')){
						$image2 = $image2[0]['image'];
					} else {
						$image2 = false;
					}

					if (strtotime($result['date_available']) > strtotime('-' . $this->config->get('newlabel_status') . ' day')) {
						$is_new = true;
					} else {
						$is_new = false;
					}
					if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}	
					if ($this->config->get('config_review_status')) {
						$rating = $result['rating'];
					} else {
						$rating = false;
					}
					
					$products[] = array(
						'product_id' => $result['product_id'],
						'quantity'  => $result['quantity'],
						'thumb'   	 => $image,
						'thumb2' 	 => $this->model_tool_image->resize($image2, $setting['image_width'], $setting['image_height']),
						'sale_end_date' => $date_end['date_end'] ?? '',
						'name'    	 => $result['name'],
						'price'   	 => $price,

						  'priceeur'       => $priceeur,
                    'specialeur'     => $specialeur,
						'new_label'  => $is_new,
						'sale_badge' => $sale_badge,
						'special' 	 => $special,


						'tax'        => $tax,
						'minimum'    => $result['minimum'] > 0 ? $result['minimum'] : 1,
						'rating'     => $rating,
						'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
						'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					);
				}
				}

				$data['tabs'][] = array(
					'title' => $title,
					'products' => $products
				);
			}
    	}
		
		
    	$data['button_cart'] = $this->language->get('button_cart');
		
		$data['module'] = $module++;

		if ($this->config->get('theme_default_directory') == 'basel')
		return $this->load->view('extension/module/basel_products', $data);
		
  	}

  	private function getProductGroups( $tabInfo , $limit ){
  		$results = array();

  		switch ( $tabInfo['product_group'] ) {
  			case 'BS':
  				$results = $this->model_catalog_product->getBestSellerProducts($limit);
  				break;
  			case 'LA':
  				$results = $this->model_catalog_product->getLatestProducts($limit);
  				break;
  			case 'SP':
  				$results = $this->model_catalog_product->getProductSpecials(array('start' => 0,'limit' => $limit));
  				break;
  			case 'PP':
  				$results = $this->model_catalog_product->getPopularProducts($limit);
  				break;
  		}
  		return $results;
  	}

  	private function getSelectProducts( $tabInfo , $limit ){
  		$results = array();

  		if(isset($tabInfo['products'])){
  			$limit_count = 0;
			foreach ( $tabInfo['products'] as $product ) {
				if ($limit_count++ == $limit) break;
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				if ($product_info) {
					$results[$product['product_id']] = $this->model_catalog_product->getProduct($product['product_id']);
				}
			}
		}

		return $results;

  	}

  	private function getCustomQuery( $tabInfo , $limit){
  		$results = array();

  		if ( $tabInfo['sort'] == 'rating' || $tabInfo['sort'] == 'p.date_added') {
  			$order = 'DESC';
  		}else{
  			$order = 'ASC';
  		}

  		$data = array(
  			'filter_category_id' => $tabInfo['filter_category']=='ALL' ? '' : $tabInfo['filter_category'], 
  			'filter_manufacturer_id' => $tabInfo['filter_manufacturer']=='ALL' ? '' : $tabInfo['filter_manufacturer'], 
  			'sort' => $tabInfo['sort'], 
  			'order' => $order,
  			'start' => 0,
  			'limit' => $limit
  		);

  		$results = $this->model_catalog_product->getProducts($data);

		return $results;
  	}

}