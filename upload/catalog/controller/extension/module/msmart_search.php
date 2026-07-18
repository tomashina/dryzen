<?php  
class ControllerExtensionModuleMsmartSearch extends Controller {
	
	private $_name = 'msmart_search';
	
	public function savephrase(){
		$this->load->model('extension/module/msmart_search');
		
		if( null != ( $phrase = isset( $this->request->get['phrase'] ) ? $this->request->get['phrase'] : null ) ) {
			$this->model_extension_module_msmart_search->addToDatabase($phrase);
		}
	}

	private function prepareProduct( $product ) {
		require_once DIR_SYSTEM . 'library/msmart_search_mobile.php';
		
		/* @var $mobile Mobile_Detect_MFS */
		$mobile = new Mobile_Detect_MSS();
		
		/* @var $config array */
		$config = (array) $this->config->get( $this->_name . '_lf' );
		
		if( empty( $config['mode'] ) || $mobile->isMobile() ) {
			$config['mode'] = 'standard';
		}
			
		/* @var $width int */
		$width = isset( $config['img_width'] ) ? abs( (int) $config['img_width'] ) : 40;

		/* @var $height int */
		$height = isset( $config['img_height'] ) ? abs( (int) $config['img_height'] ) : 40;
		
		if( ! empty( $config['mode'] ) && $config['mode'] == 'tabs' ) {
			$width = isset( $config['img_width_tabs'] ) ? abs( (int) $config['img_width_tabs'] ) : 122;
			$height = isset( $config['img_height_tabs'] ) ? abs( (int) $config['img_height_tabs'] ) : 122;			
		}

		/* @var $price string|null */
		$price = null;

		/* @var $special string|null */
		$special = null;

		if( $this->customer->isLogged() || ! $this->config->get('config_customer_price') ) {
			if( version_compare( VERSION, '2.2.0.0', '>=' ) ) {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
			}

			if( ! empty( $product['special'] ) ) {
				if( version_compare( VERSION, '2.2.0.0', '>=' ) ) {
					$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
				}
			}
		}
		
		/* @var $img string */
		$img = null;
		
		if( ! empty( $config['show_image'] ) || ( ! empty( $config['mode'] ) && $config['mode'] == 'tabs' ) ) {
			if( $product['image'] ) {
				$img = $this->model_tool_image->resize( $product['image'], $width, $height );
			} else {
				$img = $this->model_tool_image->resize('placeholder.png', $width, $height );
			}
		}

		return array(
			'type' => 'product',
			'id' => $product['product_id'],
			'name' => html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8'),
			'url' => $this->url->link('product/product', 'product_id=' . $product['product_id'], 'SSL'),
			'img' => $img,
			'img_w' => $width,
			'img_h' => $height,
			'price' => empty( $config['show_price'] ) ? null : $price,
			'special' => empty( $config['show_price'] ) ? null : $special,
			'manufacturer' => empty( $config['show_manufacturer'] ) ? null : $product['manufacturer'],
			'model' => empty( $config['show_model'] ) ? null : $product['model'],
		);
	}
	
	public function generateExtraPhrases( $products, $phrase, $config ) {
		/* @var $extra_phrases array */
		$extra_phrases = array();
			
		/* @var $config array */
		$config = (array) $this->config->get( $this->_name . '_lf' );
			
		/* @var $limit_similar_phrases int */
		$limit_similar_phrases = isset( $config['limit_similar_phrases'] ) ? (int) $config['limit_similar_phrases'] : 10;
		
		foreach( $products as $product ) {
			/* @var $name string */
			$name = html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8');

			/* @var $start int */
			$start = mb_strpos( $name, $phrase, 0, 'utf8' );

			while( $start > 0 && mb_substr( $name, $start, 1, 'utf8' ) != ' ' ) {
				$start--;
			}
			
			/* @var $phrase_strlen int */
			$phrase_strlen = mb_strlen( $phrase, 'utf8' );
			
			/* @var $name_strlen int */
			$name_strlen = mb_strlen( $name, 'utf8' );

			/* @var $end int */
			if( 
				$start + $phrase_strlen + 1 > $name_strlen
					||
				false === ( $end = mb_strpos( $name, ' ', $start + $phrase_strlen + 1, 'utf8' ) )
					|| 
				false === ( $end = mb_strpos( $name, ' ', $end + 1, 'utf8' ) ) 
			) {
				$end = $name_strlen;
			}

			/* @var $extra_phrase string */
			$extra_phrase = trim( mb_strtolower( mb_substr( $name, $start, $end - $start, 'utf8' ), 'utf8' ) );

			if( false !== ( $end = mb_strrpos( $extra_phrase, ' ', 0, 'utf8' ) ) ) {
				/* @var $extra_phrase_2 string */
				$extra_phrase_2 = trim( mb_strtolower( mb_substr( $name, $start, $end - $start, 'utf8' ), 'utf8' ) );

				if( $extra_phrase_2 !== '' ) {
					$extra_phrases[] = $extra_phrase_2;
				}
			}

			if( $extra_phrase !== '' ) {
				$extra_phrases[] = $extra_phrase;
			}
		}
			
		$extra_phrases = array_unique( $extra_phrases );
		$extra_phrases = array_slice( $extra_phrases, 0, $limit_similar_phrases );
		
		return $extra_phrases;
	}

	public function autocomplete() {
		$this->language->load('extension/module/msmart_search');
		$this->load->model('extension/module/msmart_search');
		
		/* @var $phrase string */
		$phrase = isset( $this->request->get['phrase'] ) ? $this->request->get['phrase'] : null;
	
		/* @var $response array */
		$response = array(
			'results' => array(),
			'lang' => array(
				'text_button_view_all' => $this->language->get('text_button_view_all'),
				'text_products' => $this->language->get('text_products'),
				'text_categories' => $this->language->get('text_categories'),
				'direction' => $this->language->get('direction'),
				'text_top_results' => $this->language->get('text_top_results')
			)
		);
		
		if( $phrase ) {
			/* @var $extra_phrases array */
			$extra_phrases = array();
			
			/* @var $config array */
			$config = (array) $this->config->get( $this->_name . '_lf' );
			
			/* @var $limit int */
			$limit = isset( $config['limit'] ) ? (int) $config['limit'] : 10;
			
			/* @var $mode string */
			$mode = empty( $config['mode'] ) ? 'standard' : $config['mode'];
			
			if( ! class_exists( 'msmart_search' ) ) {
				if( class_exists( '\VQMod' ) ) {
					require_once \VQMod::modCheck(modification(DIR_SYSTEM . 'library/msmart_search.php'));
				} else {
					require_once modification(DIR_SYSTEM . 'library/msmart_search.php');
				}
			}
			
			$this->load->model('tool/image');
			
			if( ! empty( $config['enabled'] ) ) {
				/* @var $products array */
				$products = Msmart_Search::make( $this )->filterData(array(
					'limit'							=> $mode == 'tabs' && $limit < 100 ? 100 : $limit,
					'start'							=> 0,
					'filter_name'					=> $phrase,
					'not_save_in_search_history'	=> true
				))->getProducts();

				foreach( $products as $product ) {
					if( count( $response['results'] ) < $limit ) {					
						$response['results'][] = $this->prepareProduct( $product );
					}
				}
				
				if( $mode == 'tabs' ) {
					$extra_phrases = $this->generateExtraPhrases( $products, $phrase, $config );
				}
				
				/* recommended products if no search results */
				if(empty($response['results'])){
					$recommended_data = $this->config->get( 'msmart_search_recommended' );
					if(!empty($recommended_data['recommended_in_live_search'])){
						$this->load->model('catalog/product');
						foreach($recommended_data['recommended_products'] as $product_id) {
							$product_info = $this->model_catalog_product->getProduct($product_id);
							if ($product_info) {
								if( count( $response['results'] ) < $limit ) {					
									$response['results'][] = $this->prepareProduct( $product_info );
								}
							}
						}
						
						/* @var $response array upg */
						if( ! empty( $recommended_data['description'][$this->config->get('config_language_id')]['content'] ) ) {
							$response['lang']['text_products'] = $recommended_data['description'][$this->config->get('config_language_id')]['content'];
						}
					}
				}
			}
					
			if( $mode == 'tabs' ) {
				$this->load->model('extension/module/msmart_search');

				if( ( $replacedPhrase = $this->model_extension_module_msmart_search->checkPhrase( $phrase ) ) == false ){
					$replacedPhrase = $phrase;
				}

				/* @var $extra_phrase string */
				foreach( $extra_phrases as $extra_phrase ) {
					if( $extra_phrase == trim( mb_strtolower( $phrase, 'utf8' ) ) ) {
						continue;
					}
					
					foreach( Msmart_Search::make( $this )->reset()->filterData(array(
						'limit'							=> $limit,
						'start'							=> 0,
						'filter_name'					=> $extra_phrase,
						'not_save_in_search_history'	=> true
					))->getProducts() as $product ) {
						$response['results'][] = array_replace( $this->prepareProduct( $product ), array(
							'extra_phrase' => $extra_phrase,
						));
					}
				}
			}

			if( $mode == 'standard' && ! empty( $config['enabled_categories'] ) ) {
				$this->load->model('extension/module/msmart_search');
						
				if( ( $replacedPhrase = $this->model_extension_module_msmart_search->checkPhrase( $phrase ) ) == false ){
					$replacedPhrase = $phrase;
				}
			
				/* @var $sql string */
				$sql = "
					SELECT
						*
					FROM
						`" . DB_PREFIX . "category` AS `c`
					INNER JOIN
						`" . DB_PREFIX . "category_description` AS `cd`
					ON
						`c`.`category_id` = `cd`.`category_id` AND `cd`.`language_id` = " . (int) $this->config->get( 'config_language_id' ) . "
					INNER JOIN 
						`" . DB_PREFIX . "category_to_store` AS `c2s` 
					ON 
						`c`.`category_id` = `c2s`.`category_id` AND `c2s`.`store_id` = '" . (int)$this->config->get('config_store_id') . "'
					WHERE
						`c`.`status` = '1' AND
						( " . Msmart_Search::make( $this )->prepareConditionsForCategories( $replacedPhrase ) . " )
				";
				
				/** Support for Customer Group Restrict */
				if( null != ( $cgr = $this->config->get( 'customer_group_restrict' ) ) ) {
					/* @var $customer_group_id int */
					$customer_group_id = $this->customer->isLogged() ? (int) $this->customer->getGroupId() : $this->config->get('config_customer_group_id');

					$sql .= ' AND ( 
						`c`.`mod_customer_group_restrict` IS NULL 
							OR 
						' . ( $cgr['mode_category'] == 'unavailable' ? 'NOT' : '' ) . ' FIND_IN_SET( ' . $customer_group_id . ', `c`.`mod_customer_group_restrict` )
					)';
				}
				
				$sql .= "
					ORDER BY
						`c`.`sort_order` ASC, `cd`.`name` ASC
					LIMIT
						" . $limit . "
				";
				
				/* @var $category array */
				foreach( $this->db->query( $sql )->rows as $category ) {
					/* @var $width int */
					$width = isset( $config['img_width_categories'] ) ? abs( (int) $config['img_width_categories'] ) : 40;

					/* @var $height int */
					$height = isset( $config['img_height_categories'] ) ? abs( (int) $config['img_height_categories'] ) : 40;
					
					/* @var $description string */
					if( null != ( $description = empty( $config['show_description_categories'] ) ? null : html_entity_decode( $category['description'], ENT_QUOTES, 'UTF-8') ) ) {
						$description = strip_tags( $description );
						
						if( mb_strlen( $description, 'utf8' ) > $config['description_max_length_categories'] ) {
							$description = mb_substr( $description, 0, $config['description_max_length_categories'] ) . '...';
						}
					}
					
					$response['results'][] = array(
						'type' => 'category',
						'id' => $category['category_id'],
						'name' => html_entity_decode($category['name'], ENT_QUOTES, 'UTF-8'),
						'description' => $description,
						'url' => $this->url->link('product/category', 'path=' . $category['category_id'], 'SSL'),
						'img' => empty( $config['show_image_categories'] ) ? null : $this->model_tool_image->resize( $category['image'], $width, $height ),
						'img_w' => $width,
						'img_h' => $height,
					);
				}
			}
		}
		
		echo '<div id="mss-response">' . base64_encode( json_encode( $response ) ) . '</div>';
		exit;
	}
}
?>