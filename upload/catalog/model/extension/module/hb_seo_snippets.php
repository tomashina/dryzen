<?php
class ModelExtensionModuleHbSeoSnippets extends Model {	
	public function get_stock_status_id($product_id) {
		$query = $this->db->query("SELECT stock_status_id FROM ".DB_PREFIX."product WHERE product_id = '".(int)$product_id."'");
		if ($query->row) {
			return $query->row['stock_status_id'];
		}else {
			return '0';
		}
	}
	
	public function product_sd($product_info, $data) {		
		$this->load->model('catalog/product');

		$ldjson = '';

		if ($this->config->get('hb_snippets_prod_enable') || $this->config->get('hb_snippets_og_enable') || $this->config->get('hb_snippets_tc_enable')) {
			$currencycode = (isset($this->session->data['currency'])) ? $this->session->data['currency'] : $this->config->get('config_currency');

			$description = ($this->config->get('hb_snippets_description') == 'description') ? preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", htmlentities(strip_tags($data['description']))) : $product_info['meta_description'];
			
			$description = preg_replace('/\s{2,}/', ' ', trim($description));
			
			$product_id 	= $product_info['product_id'];
			$name  			= $product_info['name'];
			$model 			= $product_info['model'];
			$url			= $this->url->link('product/product','product_id='.$product_id);
			$review_count 	= $product_info['reviews'];

			if ((float)$product_info['special']) {
				$price = (float)$product_info['special'];
			}else{
				$price = (float)$product_info['price'];
			}
			
			$actual_price = (float)$product_info['price'];		

			$formatted_price =  $this->currency->format($price, $currencycode);
			
			$currency_value = $this->currency->getValue($currencycode);
			$price 			= $price * $currency_value;
			$actual_price 	= $actual_price * $currency_value;
			
			if ($this->config->get('hb_snippets_incl_tax')) {
				$price 			= $this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax'));
				$actual_price 	= $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
			}			
			
			$price = number_format($price, 2, '.', '');
			$actual_price = number_format($actual_price, 2, '.', '');
			
			if ($this->config->get('hb_snippets_prod_enable')) {	
				if ($product_info['quantity'] > 0){
					$availability = 'https://schema.org/InStock';
				}else{
					$stock_status_id = $this->get_stock_status_id($product_id);
					if ($this->config->get('hb_snippets_stock')) {
						$availability = $this->config->get('hb_snippets_stock');
						$availability = 'https://schema.org/'.$availability[$stock_status_id];
					}else{
						$availability = 'https://schema.org/OutOfStock';
					}
				}

				$sku = ($product_info['sku']) ? $product_info['sku'] : $product_id;
				$mpn = ($product_info['mpn']) ? $product_info['mpn'] : $product_id;

				$product_images = [];

				if ($this->config->get('hb_snippets_img_enable')) {
					if ($product_info['image']) {
						$product_images[] = [
							'@context'=> 'https://schema.org/',
							'@type'=> 'ImageObject',
							'url' => $this->config->get('config_url').'image/'.$product_info['image'],
							'contentUrl' => $this->config->get('config_url').'image/'.$product_info['image'],
							'license' => $this->config->get('hb_snippets_img_license'),
							'acquireLicensePage' => $this->config->get('hb_snippets_img_acquire'),
							'creditText' => $this->config->get('hb_snippets_img_credit'),
							'creator' => ['@type'=> 'Organization', 'name' => $this->config->get('hb_snippets_img_creator')],
							'copyrightNotice' => '© '.date('Y', strtotime($product_info['date_added'])).' '.$this->config->get('hb_snippets_img_copyright'),
						];
					}
					$additional_image = $this->model_catalog_product->getProductImages($product_id);
					foreach ($additional_image as $image) {
						$product_images[] = [
							'@context'=> 'https://schema.org/',
							'@type'=> 'ImageObject',
							'url' => $this->config->get('config_url').'image/'.$image['image'],
							'contentUrl' => $this->config->get('config_url').'image/'.$image['image'],
							'license' => $this->config->get('hb_snippets_img_license'),
							'acquireLicensePage' => $this->config->get('hb_snippets_img_acquire'),
							'creditText' => $this->config->get('hb_snippets_img_credit'),
							'creator' => ['@type'=> 'Organization', 'name' => $this->config->get('hb_snippets_img_creator')],
							'copyrightNotice' => '© '.date('Y', strtotime($product_info['date_added'])).' '.$this->config->get('hb_snippets_img_copyright'),
						];
					}
				}else{
					if ($product_info['image']) {
						$product_images[] = $data['popup'];
					}
	
					if (!empty($data['images'])) {
						foreach ($data['images'] as $image) {
							$product_images[] = $image['popup'];
						}
					}
				}				

				$brand_name = ($product_info['manufacturer'])? $product_info['manufacturer'] : $this->config->get('hb_snippets_brand');
				$brand = array('@type' => 'Brand', '@id' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']), 'name' => $brand_name );

				$review_data = array();
				$review_query = $this->db->query("SELECT * FROM `".DB_PREFIX."review` WHERE product_id = '".(int)$product_id."' AND status = 1");
				if ($review_query->rows) {
					$reviews = $review_query->rows;
					
					foreach ($reviews as $rev) {
						$raw_text = $rev['text'];
						$title = strtok($raw_text, '.,!'); // Get the first segment of the review
						if (strlen($title) > 65) {
							$title = substr($title, 0, 62) . '...'; // Truncate to 65 characters and add "..."
						}

						$reviewRating =  array(
							'@type'			=> 'Rating',
							'ratingValue'	=> $rev['rating'],
							'bestRating'	=> '5',
							'worstRating'	=>	'1'
						);

						$author = array(
							'@type'			=> 'Person',
							'name'			=> $rev['author'],
						);

						$review_data[] = array(
							'@type'			=> 	'Review',
							'headline'		=> 	$title,
							'reviewRating'	=> 	$reviewRating,
							'author'		=> 	$author,
							'reviewBody'	=> 	htmlentities($rev['text']),
							'datePublished'	=>	date('Y-m-d', strtotime($rev['date_added']))
						);
					}
				}

				$aggregateRating = array();
				
				if ($review_count > 0) {
					$aggregateRating = array(
						'@type'			=> 	'AggregateRating',
						'ratingValue'	=>	$data['rating'],
						'reviewCount'	=>	$review_count,
						'bestRating'	=> 	'5',
						'worstRating'	=> 	'1',
					);
				}

				//OFFERS
				$offers = array(
					'@type'           => 'Offer',
					'url'             => $url,
					'availability'    => $availability,
					'itemCondition'    => 'https://schema.org/NewCondition',
					'price'           => $actual_price,
					'priceCurrency'   => $currencycode,
				);

				if ($price < $actual_price){
					$offers['salePrice'] = $price;
				}
				
				if ($this->config->get('hb_snippets_pricevalid')) {
					$price_date = $this->config->get('hb_snippets_pricevaliddate');
					
					$pricedate_query = $this->db->query(
						"SELECT date_end FROM `" . DB_PREFIX . "product_special` " .
						"WHERE product_id = '" . (int)$product_id . "' " .
						"AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' " .
						"AND date_end > now() ORDER BY priority ASC LIMIT 1"
					);

					if ($pricedate_query->row) {
						$price_date = date('Y-m-d', strtotime($pricedate_query->row['date_end']));
					}
				}				

				if (isset($price_date)) {
					$offers['priceValidUntil'] = $price_date;
				}

				if ($availability === 'https://schema.org/OnlineOnly') {
					$offers['deliveryMethod'] = 'https://schema.org/OnlineDelivery';
				}

				//SHIPPNIG DETAILS
				if ($this->config->get('hb_snippets_shipping')) {
					$shipping_rules = $this->config->get('hb_snippets_shipping_rules');
					if ($shipping_rules) {
						$shipping = [];

						foreach ($shipping_rules as $rule) {
							$rule_parts = explode(":", $rule);
							list($range, $country_region, $rate, $currency, $handling_range, $transit_range) = $rule_parts;
						
							// Split the country-region into country and (optionally) region
							$country_region_parts = explode("-", $country_region);
							$country = $country_region_parts[0];
							$region = isset($country_region_parts[1]) ? $country_region_parts[1] : null;
						
							// Split the range into min and max
							list($min_price, $max_price) = explode("-", $range);
							list($handling_min, $handling_max) = explode("-", $handling_range);
							list($transit_min, $transit_max) = explode("-", $transit_range);
						
							// Check if the product price falls within the range
							if ($price >= $min_price && $price <= $max_price) {
								$shipping_details = array(
									'@type' => 'OfferShippingDetails',
									'shippingRate' => array(
										'@type' => 'MonetaryAmount',
										'currency' => $currency,
										'value' => $rate
									),
									'shippingDestination' => array(
										'@type' => 'DefinedRegion',
										'addressCountry' => $country
									),
									'deliveryTime' => array(
										'@type' => 'ShippingDeliveryTime',
										'handlingTime' => array(
											'@type' => 'QuantitativeValue',
											'minValue' => (int)$handling_min,
											'maxValue' => (int)$handling_max,
											'unitCode' => 'DAY'
										),
										'transitTime' => array(
											'@type' => 'QuantitativeValue',
											'minValue' => (int)$transit_min,
											'maxValue' => (int)$transit_max,
											'unitCode' => 'DAY'
										)
									)
								);
						
								// Add region only if specified
								if ($region) {
									$shipping_details['shippingDestination']['addressRegion'] = $region;
								}
						
								$shipping[] = $shipping_details;
							}
						}
						$offers['shippingDetails'] = $shipping;
					}		
				}

				//RETURN POLICY
				if ($this->config->get('hb_snippets_return')) {
					$return_policy_rules = $this->config->get('hb_snippets_return_rules');
					if ($return_policy_rules) {
						$return_policies = [];

						// Define mappings for return policy categories
						$return_policy_category_map = [
							"MRFRW" => "https://schema.org/MerchantReturnFiniteReturnWindow",
							"MRNP" => "https://schema.org/MerchantReturnNotPermitted"
						];

						// Define mappings for return methods
						$return_method_map = [
							"RBM" => "https://schema.org/ReturnByMail",
							"RTK" => "https://schema.org/ReturnAtKiosk",
							"RIS" => "https://schema.org/ReturnInStore"
						];

						// Define mappings for return fees
						$return_fees_map = [
							"RFCR" => "https://schema.org/ReturnFeesCustomerResponsibility",
							"FR" => "https://schema.org/FreeReturn",
							"RSF" => "https://schema.org/ReturnShippingFees"
						];

						foreach ($return_policy_rules as $rule) {
							$rule_parts = explode(":", $rule);
							$country = $rule_parts[0];
							$returnPolicyCategoryCode = $rule_parts[1];

							// Map returnPolicyCategory
							if (!isset($return_policy_category_map[$returnPolicyCategoryCode])) {
								continue; // Skip if category is invalid
							}
							$returnPolicyCategory = $return_policy_category_map[$returnPolicyCategoryCode];

							$return_policy = [
								"@type" => "MerchantReturnPolicy",
								"applicableCountry" => $country,
								"returnPolicyCategory" => $returnPolicyCategory
							];

							if ($returnPolicyCategoryCode === "MRFRW") {
								$merchantReturnDays = isset($rule_parts[2]) ? (int)$rule_parts[2] : null;
								$returnMethodCode = isset($rule_parts[3]) ? $rule_parts[3] : null;
								$returnFeesCode = isset($rule_parts[4]) ? $rule_parts[4] : null;

								$return_policy["merchantReturnDays"] = $merchantReturnDays;

								// Map and add return method
								if (isset($return_method_map[$returnMethodCode])) {
									$return_policy["returnMethod"] = $return_method_map[$returnMethodCode];
								}

								// Map and add return fees
								if (isset($return_fees_map[$returnFeesCode])) {
									$return_policy["returnFees"] = $return_fees_map[$returnFeesCode];
								}

								// Handle return shipping fees
								if ($returnFeesCode === "RSF" && isset($rule_parts[5], $rule_parts[6])) {
									$returnShippingFeesAmount = $rule_parts[5];
									$currency = $rule_parts[6];

									$return_policy["returnShippingFeesAmount"] = [
										"@type" => "MonetaryAmount",
										"value" => $returnShippingFeesAmount,
										"currency" => $currency
									];
								}
							}

							$return_policies[] = $return_policy;
						}

						$offers['hasMerchantReturnPolicy'] = $return_policies;
					}
				}

				//seller
				$offer['sellers'] = ['@type' => 'Organization', 'name' => $this->config->get('config_name')];

				$product_snippet = array(
					'@context' 			=> 	'https://schema.org/',
					'@type'				=> 	'Product',
					'@id'				=> 	$url.'#product',
					'sku'				=> 	$sku,
					'mpn'				=> 	$mpn,
					'image'				=> 	$product_images,
					'name'				=> 	$data['heading_title'],
					'description'		=> 	$description,
					'productID'			=> 	$product_id,
					'brand'				=>	$brand,
					'review'			=> 	$review_data,
					'aggregateRating'	=> 	$aggregateRating,
					'offers'			=> 	$offers,
				);
				
				$ldjson .= '<script type="application/ld+json">';
				$ldjson .= json_encode($product_snippet);
				$ldjson .= "</script>";
			}

			//OPEN GRAPH
			if ($this->config->get('hb_snippets_og_enable')){
				$hb_snippets_ogp = $this->config->get('hb_snippets_ogp');
				if (strlen($hb_snippets_ogp) > 4){				
					$hb_snippets_ogp = str_replace('{name}',$name,$hb_snippets_ogp);
					$hb_snippets_ogp = str_replace('{model}',$model,$hb_snippets_ogp);
					$hb_snippets_ogp = str_replace('{brand}',$brand_name,$hb_snippets_ogp);
					$hb_snippets_ogp = str_replace('{price}',$formatted_price,$hb_snippets_ogp);
				}else{
					$hb_snippets_ogp = $name;
				}
				
				if (strlen($this->config->get('hb_snippets_og_id')) > 5 ){
					$this->document->setOpengraph('fb:app_id', $this->config->get('hb_snippets_og_id'));
				}
				$this->document->setOpengraph('og:title', $hb_snippets_ogp);
				$this->document->setOpengraph('og:type', 'product');
				$this->document->setOpengraph('og:site_name', $this->config->get('config_name'));
				
				$this->load->model('tool/image');
				if ($product_info['image']) {
					$snippet_thumb = $this->model_tool_image->resize($product_info['image'], $this->config->get('hb_snippets_og_piw'), $this->config->get('hb_snippets_og_pih'));
					$this->document->setOpengraph('og:image', $snippet_thumb);
					$this->document->setOpengraph('og:image:width', $this->config->get('hb_snippets_og_piw'));
					$this->document->setOpengraph('og:image:height', $this->config->get('hb_snippets_og_pih'));
				} 
				
				$this->document->setOpengraph('og:url', $this->url->link('product/product', 'product_id=' . $product_id));
				$this->document->setOpengraph('og:description', $description);
				
				/*if (!empty($data['images'])) {
					foreach ($data['images'] as $additional_image){
						$this->document->setOpengraph('og:image', $additional_image['popup']);	
						$this->document->setOpengraph('og:image:width', $this->config->get('hb_snippets_og_piw'));
						$this->document->setOpengraph('og:image:height', $this->config->get('hb_snippets_og_pih'));
					}
				}*/
				
				if ((float)$product_info['special']) {
					$this->document->setOpengraph('product:sale_price:amount', $price);
					$this->document->setOpengraph('product:sale_price:currency', $currencycode);
					$this->document->setOpengraph('product:original_price:amount', $actual_price);
					$this->document->setOpengraph('product:original_price:currency', $currencycode);
				} else {
					$this->document->setOpengraph('product:original_price:amount', $price);
					$this->document->setOpengraph('product:original_price:currency', $currencycode);
				}

				if ($product_info['quantity'] > 0){
					$this->document->setOpengraph('og:availability', 'instock');
				} else {
					$this->document->setOpengraph('og:availability', 'oos');
				}
				
				if (!empty($data['products'])) {
					foreach ($data['products'] as $product){
						$this->document->setOpengraph('og:see_also', $product['href']);
					}
				}
			}
			//TWITTER CARDS
			if ($this->config->get('hb_snippets_tc_enable')){
				$hb_snippets_tcp = $this->config->get('hb_snippets_tcp');
				if (strlen($hb_snippets_tcp) > 4){				
					$hb_snippets_tcp = str_replace('{name}',$name,$hb_snippets_tcp);
					$hb_snippets_tcp = str_replace('{model}',$model,$hb_snippets_tcp);
					$hb_snippets_tcp = str_replace('{brand}',$brand_name,$hb_snippets_tcp);
					$hb_snippets_tcp = str_replace('{price}',$formatted_price,$hb_snippets_tcp);
				}else{
					$hb_snippets_tcp = $name;
				}
				
				$this->document->setTwittercard('twitter:card', 'summary_large_image');
				$this->document->setTwittercard('twitter:site', $this->config->get('hb_snippets_tc_username'));
				$this->document->setTwittercard('twitter:title', $hb_snippets_tcp);
				$this->document->setTwittercard('twitter:description', $description);
				if ($product_info['image']) {
					$this->document->setTwittercard('twitter:image', $data['popup']);
				}
			}
		}
		
		$this->document->setStructureddata($ldjson);
	}
	
	public function category_social($category_info){
		$this->load->model('tool/image');
		if ($this->config->get('hb_snippets_og_enable')){
			$hb_snippets_ogc = $this->config->get('hb_snippets_ogc');
			if (strlen($hb_snippets_ogc) > 4){
				$ogc_name = $category_info['name'];
				$hb_snippets_ogc = str_replace('{name}',$ogc_name,$hb_snippets_ogc);
			}else{
				$hb_snippets_ogc = $category_info['name'];
			}
			
			if (strlen($this->config->get('hb_snippets_og_id')) > 5 ){
			    $this->document->setOpengraph('fb:app_id', $this->config->get('hb_snippets_og_id'));
			}
			$this->document->setOpengraph('og:title', $hb_snippets_ogc);
            $this->document->setOpengraph('og:type', 'product.group');
			$this->document->setOpengraph('og:site_name', $this->config->get('config_name'));
			$this->document->setOpengraph('og:url', $this->url->link('product/category', 'path=' . $category_info['category_id']));
			if ($category_info['image']) {
				$image = $this->model_tool_image->resize($category_info['image'], $this->config->get('hb_snippets_og_ciw'), $this->config->get('hb_snippets_og_cih'));
				$this->document->setOpengraph('og:image', $image);
				$this->document->setOpengraph('og:image:width', $this->config->get('hb_snippets_og_ciw'));
				$this->document->setOpengraph('og:image:height', $this->config->get('hb_snippets_og_cih'));
			}
			$this->document->setOpengraph('og:description', $category_info['meta_description']);
		}
		
		//TWITTER CARDS
		if ($this->config->get('hb_snippets_tc_enable')){
			$hb_snippets_tcc = $this->config->get('hb_snippets_tcc');
			if (strlen($hb_snippets_tcc) > 4){
				$tcc_name = $category_info['name'];
				$hb_snippets_tcc = str_replace('{name}',$tcc_name,$hb_snippets_tcc);
			}else{
				$hb_snippets_tcc = $category_info['name'];
			}
			
			$this->document->setTwittercard('twitter:card', 'summary_large_image');
			$this->document->setTwittercard('twitter:site', $this->config->get('hb_snippets_tc_username'));
			$this->document->setTwittercard('twitter:title', $hb_snippets_tcc);
			$this->document->setTwittercard('twitter:description', $category_info['meta_description']);
			if ($category_info['image']) {
				$image = $this->model_tool_image->resize($category_info['image'], $this->config->get('hb_snippets_og_ciw'), $this->config->get('hb_snippets_og_cih'));
			    $this->document->setTwittercard('twitter:image', $image);
			}
		}
	}
	
	public function information_social($information_info) {
		if ($this->config->get('hb_snippets_og_enable')) {
			$config_url = $this->config->get('config_url');
			$og_img = $this->config->get('hb_snippets_og_img');
			$fb_app_id = $this->config->get('hb_snippets_og_id');
	
			// Open Graph
			if (!empty($fb_app_id) && strlen($fb_app_id) > 5) {
				$this->document->setOpengraph('fb:app_id', $fb_app_id);
			}
	
			$this->document->setOpengraph('og:title', $information_info['title']);
			$this->document->setOpengraph('og:type', 'website');
			$this->document->setOpengraph('og:site_name', $this->config->get('config_name'));
			$this->document->setOpengraph('og:url', $this->url->link('information/information', 'information_id=' . $information_info['information_id']));
			$this->document->setOpengraph('og:description', $information_info['meta_description']);
	
			if ($og_img) {
				$this->document->setOpengraph('og:image', $config_url . 'image/' . $og_img);
				$this->document->setOpengraph('og:image:width', $this->config->get('hb_snippets_og_diw'));
				$this->document->setOpengraph('og:image:height', $this->config->get('hb_snippets_og_dih'));
			}
		}
	
		// Twitter Cards
		if ($this->config->get('hb_snippets_tc_enable')) {
			$this->document->setTwittercard('twitter:card', 'summary_large_image');
			$this->document->setTwittercard('twitter:site', $this->config->get('hb_snippets_tc_username'));
			$this->document->setTwittercard('twitter:title', $information_info['title']);
			$this->document->setTwittercard('twitter:description', $information_info['meta_description']);
	
			if ($og_img) {
				$this->document->setTwittercard('twitter:image', $config_url . 'image/' . $og_img);
			}
		}
	}	
	
	public function home_social() {
		//$this->load->model('tool/image');

		// Open Graph
		if ($this->config->get('hb_snippets_og_enable')) {
			$config_url = $this->config->get('config_url');
			$og_img = $this->config->get('hb_snippets_og_img');

			if (strlen($this->config->get('hb_snippets_og_id')) > 5) {
				$this->document->setOpengraph('fb:app_id', $this->config->get('hb_snippets_og_id'));
			}

			$this->document->setOpengraph('og:title', $this->config->get('config_meta_title'));
			$this->document->setOpengraph('og:type', 'website');
			$this->document->setOpengraph('og:site_name', $this->config->get('config_name'));
			$this->document->setOpengraph('og:url', $config_url);
			$this->document->setOpengraph('og:description', $this->config->get('config_meta_description'));

			if ($og_img) {
				$this->document->setOpengraph('og:image', $config_url . 'image/' . $og_img);
				$this->document->setOpengraph('og:image:width', $this->config->get('hb_snippets_og_diw'));
				$this->document->setOpengraph('og:image:height', $this->config->get('hb_snippets_og_dih'));
			}
		}

		// Twitter Cards
		if ($this->config->get('hb_snippets_tc_enable')) {
			$this->document->setTwittercard('twitter:card', 'summary_large_image');
			$this->document->setTwittercard('twitter:site', $this->config->get('hb_snippets_tc_username'));
			$this->document->setTwittercard('twitter:title', $this->config->get('config_meta_title'));
			$this->document->setTwittercard('twitter:description', $this->config->get('config_meta_description'));

			if ($og_img) {
				$this->document->setTwittercard('twitter:image', $config_url . 'image/' . $og_img);
			}
		}
	}
	
	public function getProductCategory(int $product_id): array{
		$query = $this->db->query("SELECT c.category_id, c.parent_id FROM " . DB_PREFIX . "product_to_category p2c LEFT JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id) WHERE product_id = '" . (int)$product_id . "' ORDER BY parent_id DESC LIMIT 1");
		if ($query->row){
			return $query->row;
		}else{
			return [];
		}
	}

	public function getParentCategory(int $category_id): int{
		$query = $this->db->query("SELECT parent_id FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "' LIMIT 1");
		if ($query->row){
			return $query->row['parent_id'];
		}else{
			return '0';
		}
	}

	public function isCategoryActive(int $category_id): bool{
		$query = $this->db->query("SELECT count(*) as total FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "' AND status = 1");
		if ($query->row['total'] > 0){
			return true;
		}else{
			return false;
		}
	 }

	public function breadcrumbs_sd($breadcrumbs, $options = []) {		
		if ($this->config->get('hb_snippets_bc_enable')) {
			$ldjson = '';
			$itemlist = [];
			$i = 1;

			if ($this->config->get('hb_snippets_bc_type') == 'smart' && !empty($options)) {
				$type   = $options['type'];
				$id 	= $options['id'];
				$title 	= $options['title'];

				$this->load->model('catalog/category');

				if ($type == 'product' && $id > 0){
					$breadcrumbs = [];
					$breadcrumbs[] = [
						'text' => $this->language->get('text_home'),
						'href' => $this->url->link('common/home')
					];

					$category = $this->getProductCategory($id);
					if (!empty($category)){
						$sub_category_id 	= $category['category_id'];
						$parent_category_id = $category['parent_id'];

						$parent_path_id = '';
						if ($parent_category_id != 0 && $this->isCategoryActive($parent_category_id)) {
							$parent_category_info = $this->model_catalog_category->getCategory($parent_category_id);

							$breadcrumbs[] = [
								'text' => isset($parent_category_info['name']) ? $parent_category_info['name'] : $parent_category_id,
								'href' => $this->url->link('product/category', 'path=' . $parent_category_id)
							];

							$parent_path_id = $parent_category_id.'_';
						}

						$sub_category_info = $this->model_catalog_category->getCategory($sub_category_id);

						if ($sub_category_info) {
							$breadcrumbs[] = [
								'text' => isset($sub_category_info['name']) ? $sub_category_info['name'] : $sub_category_id,
								'href' => $this->url->link('product/category', 'path=' .$parent_path_id.$sub_category_id)
							];
						}						
					}					

					$breadcrumbs[] = [
						'text' => $title,
						'href' => $this->url->link('product/product',  'product_id=' . $id)
					];
				}

				if ($type == 'category' && $id > 0){
					$breadcrumbs = [];
					$breadcrumbs[] = [
						'text' => $this->language->get('text_home'),
						'href' => $this->url->link('common/home')
					];

					$parent_category_id = $this->getParentCategory($id);
					
					$parent_path_id = '';
					if ($parent_category_id != 0) {
						$parent_category_info = $this->model_catalog_category->getCategory($parent_category_id);

						$breadcrumbs[] = [
							'text' => $parent_category_info['name'],
							'href' => $this->url->link('product/category', 'path=' . $parent_category_id)
						];

						$parent_path_id = $parent_category_id.'_';
					}
									

					$breadcrumbs[] = [
						'text' => $title,
						'href' => $this->url->link('product/category', 'path=' .$parent_path_id.$id)
					];
				}
			}			
			
			if (!empty($breadcrumbs)) {
				array_shift($breadcrumbs); //removing the first array element which is usually the home
				foreach ($breadcrumbs as $breadcrumb) {	
					$itemlist[] = array(
						'@type'			=> 	'ListItem',
						'position'		=>  $i,
						'name'			=>  $breadcrumb['text'],
						'item'			=>  $breadcrumb['href']
					);

					$i++;
				}
			}					

			$breadcrumb_snippet = array(
				'@context' 			=> 	'https://schema.org/',
				'@type'				=> 	'BreadcrumbList',
				'itemListElement'   =>	$itemlist
			);
			
			$ldjson .= '<!--huntbee breadcrumb structured data--><script type="application/ld+json">';
			$ldjson .= json_encode($breadcrumb_snippet);
			$ldjson .= "</script>";

		} else {
			$ldjson = '';
		}
		
		$this->document->setStructureddata($ldjson);
	}
	
	public function local_business() {
		$ldjson = $this->config->get('hb_snippets_local_enable') ? html_entity_decode($this->config->get('hb_snippets_local_snippet'), ENT_QUOTES, 'UTF-8') : '';
	
		$this->document->setStructureddata($ldjson);
	}
	
	public function knowledge_graph() {
		if (!$this->config->get('hb_snippets_kg_enable')) {
			$this->document->setStructureddata('');
			return;
		}
	
		$store_id = (int)$this->config->get('config_store_id');
		$store_url = $this->config->get('config_url') ?: HTTPS_SERVER;
	
		// Prepare Contact Points
		$contactPoint = array_map(function ($contact) {
			return [
				'@type'       => 'ContactPoint',
				'telephone'   => $contact['n'],
				'contactType' => $contact['t']
			];
		}, $this->config->get('hb_snippets_contact') ?? []);
	
		$contactPoint = array_merge($contactPoint, array_map(function ($email) {
			return [
				'@type'       => 'ContactPoint',
				'email'       => $email['email'],
				'contactType' => $email['type']
			];
		}, $this->config->get('hb_snippets_emails') ?? []));
	
		// Prepare Social Media Links
		$sameAs = $this->config->get('hb_snippets_socials') ?? [];
	
		// Prepare Home Snippet
		$home_snippet = [];
		if ($this->config->get('hb_snippets_logo')) {
			$home_snippet = [
				'@context'       => 'https://schema.org/',
				'@type'          => 'OnlineStore',
				'name'           => $this->config->get('config_name'),
				'url'            => $store_url,
				'logo'           => $store_url . 'image/' . $this->config->get('hb_snippets_logo'),
				'description'    => $this->config->get('config_meta_description'),
				'telephone'      => $this->config->get('config_telephone'),
				'email'          => $this->config->get('config_email'),
				'address'        => [
					'@type'            => 'PostalAddress',
					'streetAddress'    => $this->config->get('hb_snippets_local_st'),
					'addressLocality'  => $this->config->get('hb_snippets_local_location'),
					'addressRegion'    => $this->config->get('hb_snippets_local_state'),
					'postalCode'       => $this->config->get('hb_snippets_local_postal'),
					'addressCountry'   => $this->config->get('hb_snippets_local_country')
				],
				'contactPoint'   => $contactPoint,
				'sameAs'         => $sameAs
			];
	
			// Add VAT ID if available
			if (!empty($this->config->get('hb_snippets_vat'))) {
				$home_snippet['vatID'] = $this->config->get('hb_snippets_vat');
			}
		}
	
		// Generate JSON-LD
		$ldjson = $home_snippet ? '<!--huntbee home-logo structured data--><script type="application/ld+json">' . json_encode($home_snippet) . '</script>' : '';
	
		$this->document->setStructureddata($ldjson);
	}
	
	public function site_search() {
		if (!$this->config->get('hb_snippets_search_enable')) {
			$this->document->setStructureddata('');
			return;
		}
	
		$store_url = $this->config->get('config_url') ?: HTTPS_SERVER;
		$search_link = $this->url->link('product/search', 'search=');
	
		$snippet = [
			'@context'        => 'https://schema.org/',
			'@type'           => 'WebSite',
			'url'             => $store_url,
			'potentialAction' => [
				'@type'       => 'SearchAction',
				'target'      => $search_link . '{search_term_string}',
				'query-input' => 'required name=search_term_string'
			]
		];
	
		$ldjson = '<!--huntbee sitelinks search box structured data--><script type="application/ld+json">';
		$ldjson .= json_encode($snippet);
		$ldjson .= '</script>';
	
		$this->document->setStructureddata($ldjson);
	}
	
	public function itemlist($products) {
		if (!$this->config->get('hb_snippets_list_enable') || empty($products)) {
			$this->document->setStructureddata('');
			return;
		}
	
		$itemlist = array_map(function ($product, $index) {
			return [
				'@type'    => 'ListItem',
				'position' => $index + 1,
				'name'     => $product['name'],
				'image'    => $product['thumb'],
				'url'      => $product['href']
			];
		}, $products, array_keys($products));
	
		$itemlist_snippet = [
			'@context'         => 'https://schema.org/',
			'@type'            => 'ItemList',
			'itemListElement'  => $itemlist
		];
	
		$ldjson = '<!--huntbee category structured data--><script type="application/ld+json">';
		$ldjson .= json_encode($itemlist_snippet);
		$ldjson .= '</script>';
	
		$this->document->setStructureddata($ldjson);
	}	
	
}