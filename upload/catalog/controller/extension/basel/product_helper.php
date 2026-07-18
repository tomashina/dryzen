<?php

	$data['full_width_tabs'] = $this->config->get('full_width_tabs');
	$data['product_tabs_style'] = $this->config->get('product_tabs_style');
	$data['img_w'] = $this->config->get('theme_default_image_thumb_width');
	$data['img_h'] = $this->config->get('theme_default_image_thumb_height');
	$data['img_a_w'] = $this->config->get('theme_default_image_additional_width');
	$data['img_a_h'] = $this->config->get('theme_default_image_additional_height');
	$data['meta_description_status'] = $this->config->get('meta_description_status');
	$data['product_page_countdown'] = $this->config->get('product_page_countdown');
	$data['meta_description'] = $product_info['meta_description'];	
	$data['product_layout'] = $this->config->get('product_layout');
	$data['qty'] = $product_info['quantity'];
	$data['basel_price_update'] = $this->config->get('basel_price_update');
	$data['basel_sharing_style'] = $this->config->get('basel_sharing_style');
	$data['review_qty'] = $product_info['reviews'];
	$data['currency_code'] = $this->session->data['currency'];
	$data['button_reviews'] = $this->language->get('button_reviews');
	$data['basel_text_share'] = $this->language->get('basel_text_share');
	$data['currency_code'] = $this->session->data['currency'];
	$data['hover_zoom'] = $this->config->get('basel_hover_zoom');
	$data['current_href'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);
	$this->document->addLink($data['thumb'], 'image');
	$data['basel_share_btn'] = $this->config->get('basel_share_btn');
	$data['basel_rel_prod_grid'] = $this->config->get('basel_rel_prod_grid');
	$data['items_mobile_fw'] = $this->config->get('items_mobile_fw');
	if (strtotime($product_info['date_available']) > strtotime('-' . $this->config->get('newlabel_status') . ' day')) $data['is_new'] = true;
	$data['basel_text_offer_ends'] = $this->language->get('basel_text_offer_ends');
	$price_snippet = preg_replace("/[^0-9,.]/","", $data['price']);
	$data['price_snippet'] = str_replace( ',', '.', $price_snippet);
	if ((float)$product_info['special']) {
	$date_end = $this->model_extension_basel_basel->getSpecialEndDate($product_info['product_id']);
	$data['sale_end_date'] = $date_end['date_end'] ?? '';
	$special_snippet = preg_replace("/[^0-9,.]/","", $data['special']);
	$data['special_snippet'] = str_replace( ',', '.', $special_snippet);
	}
	if ($this->config->get('product_layout') == 'full-width') $this->document->addScript('catalog/view/theme/basel/js/theia-sticky-sidebar.min.js');
	
	if ( (float)$product_info['special'] && ($this->config->get('salebadge_status')) ) {
		if ($this->config->get('salebadge_status') == '2') {
			$data['sale_badge'] = '-' . number_format(((($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))-($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'))))/(($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))/100)), 0, ',', '.') . '%';
		} else {
			$data['sale_badge'] = $this->language->get('basel_text_sale');
		}		
	} else {
		$data['sale_badge'] = false;
	}
		
	
	// RTL support
	$data['direction'] = $this->language->get('direction');
	
	// Product Questions and Answers
	$data['question_status'] = $this->config->get('product_question_status');
	$data['product_questions'] = $this->load->controller('extension/basel/question');
	$data['basel_tab_questions'] = $this->language->get('basel_tab_questions');
	$data['basel_button_ask'] = $this->language->get('basel_button_ask');
	$this->load->model('extension/basel/question');
	$data['questions_total'] = $this->model_extension_basel_question->getTotalQuestionsByProductId($this->request->get['product_id']);
	
	// Product Tabs
	$this->load->model('extension/basel/product_tabs');
	$data['product_tabs'] = $this->model_extension_basel_product_tabs->getExtraTabsProduct($this->request->get['product_id']);
	
	// SEO Reviews
	$data['text_no_reviews'] = $this->language->get('text_no_reviews');
	$this->load->model('catalog/review');
	if (isset($this->request->get['page'])) {
	$page = $this->request->get['page'];
	} else {
	$page = 1;
	}
	$data['seo_reviews'] = array();
	$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);
	$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);
	foreach ($results as $result) {
	$data['seo_reviews'][] = array(
	'author'     => $result['author'],
	'text'       => nl2br($result['text']),
	'rating'     => (int)$result['rating'],
	'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
	);
	}
	$pagination = new Pagination();
	$pagination->total = $review_total;
	$pagination->page = $page;
	$pagination->limit = 5;
	$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');
	$data['pagination'] = $pagination->render();
	$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));