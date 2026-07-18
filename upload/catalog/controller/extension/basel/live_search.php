<?php
class ControllerExtensionBaselLiveSearch extends Controller {
	public function index() {
		$json = array();
		if (isset($this->request->get['filter_name'])) {
			$search = $this->request->get['filter_name'];
		} else {
			$search = '';
		}
		$tag           = $search;
		$category_id   = 0;
		$sub_category  = '';
		$sort          = 'p.sort_order';
		$order         = 'ASC';
		$page          = 1;
		$limit         = 5;
		$search_result = 0;
		$name_limit    = 25;
		$error         = false;
		
		$currency_code = $this->session->data['currency'];

		if(!$error){
		
		$this->load->language('basel/basel_theme');
		$json['basel_text_view_all'] = $this->language->get('basel_text_view_all');
		$json['search_url'] = $this->url->link('product/search');
		$json['basel_text_no_result'] = $this->language->get('basel_text_no_result');
		
			if (isset($this->request->get['filter_name'])) {
				$this->load->model('catalog/product');
				$this->load->model('tool/image');
				$filter_data = array(
					'filter_name'         => $search,
					'filter_tag'          => $tag,
					'filter_category_id'  => $category_id,
					'filter_sub_category' => $sub_category,
					'sort'                => $sort,
					'order'               => $order,
					'start'               => ($page - 1) * $limit,
					'limit'               => $limit
				);
				
				$results = $this->model_catalog_product->getProducts($filter_data);
				$search_result = $this->model_catalog_product->getTotalProducts($filter_data);
				$image_width        = $this->config->get('theme_default_image_cart_width');
				$image_height       = $this->config->get('theme_default_image_cart_height');
				$title_length       = '100';

				foreach ($results as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $image_width, $image_height);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $image_width, $image_height);
					}

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $currency_code);

						if($this->session->data['currency']=='HRK'){
	                        $priceeur = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 'EUR');
	                    }
	                    else{
	                        $priceeur  ='';

	                    }
					} else {
						$price = false;
						  $priceeur  ='';
					}

					if ((float)$result['special']) {
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $currency_code);

						if($this->session->data['currency']=='HRK'){
	                        $specialeur = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')),  'EUR');
	                    }
	                    else{
	                        $specialeur  ='';

	                    }
					} else {
						$special = false;
						$specialeur  ='';
					}

					if (strlen($result['name']) > $title_length) {
						$name = utf8_substr(strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')), 0, $title_length) . '..';
					} else {
						$name = html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8');
					}

					
					$json['total'] = (int)$search_result;
					$json['products'][] = array(
						'product_id'  => $result['product_id'],
						'image'       => $image,
						'name' 		  => $name,
						'price'       => $price,
						'special'     => $special,
							'priceeur'       => $priceeur,
						'specialeur'     => $specialeur,
						'url'         => $this->url->link('product/product', 'product_id=' . $result['product_id'])
					);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}