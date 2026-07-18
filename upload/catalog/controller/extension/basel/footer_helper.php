<?php
	$lang_id = $this->config->get('config_language_id');
	
	// Footer positions
	if ($this->config->get('theme_default_directory') == 'basel') {
	$data['position_bottom_half'] = $this->load->controller('extension/basel/position_bottom_half');
	$data['position_bottom'] = $this->load->controller('extension/basel/position_bottom');
		if ($this->config->get('config_maintenance')) {
		$this->user = new Cart\User($this->registry);
		if (!$this->user->isLogged()) { 
		$data['position_bottom_half'] = false; 
		$data['position_bottom'] = false; 
		}
		}
	}
	
	$footer_block_1 = $this->config->get('footer_block_1');
	$footer_block_2 = $this->config->get('footer_block_2');
	$footer_infoline_1 = $this->config->get('footer_infoline_1');
	$footer_infoline_2 = $this->config->get('footer_infoline_2');
	$footer_infoline_3 = $this->config->get('footer_infoline_3');
	$footer_block_title = $this->config->get('footer_block_title');
	$basel_copyright = $this->config->get('basel_copyright');
	$data['footer_block_1'] = '';
	$data['footer_block_2'] = '';
	$data['footer_infoline_1'] = '';
	$data['footer_infoline_2'] = '';
	$data['footer_infoline_3'] = '';
	$data['footer_block_title'] = '';
	$data['basel_copyright'] = '';
	if (isset($footer_block_1[$lang_id])) 
	$data['footer_block_1'] = html_entity_decode($footer_block_1[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_block_2[$lang_id])) 
	$data['footer_block_2'] = html_entity_decode($footer_block_2[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_infoline_1[$lang_id])) 
	$data['footer_infoline_1'] = html_entity_decode($footer_infoline_1[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_infoline_2[$lang_id])) 
	$data['footer_infoline_2'] = html_entity_decode($footer_infoline_2[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_infoline_3[$lang_id])) 
	$data['footer_infoline_3'] = html_entity_decode($footer_infoline_3[$lang_id], ENT_QUOTES, 'UTF-8');
	if (isset($footer_block_title[$lang_id])) 
	$data['footer_block_title'] = html_entity_decode($footer_block_title[$lang_id], ENT_QUOTES, 'UTF-8');	
	if (isset($basel_copyright[$lang_id])) 
	$data['basel_copyright'] = html_entity_decode(str_replace('{year}',date("Y"),$basel_copyright[$lang_id]), ENT_QUOTES, 'UTF-8');
	$data['custom_links'] = $this->config->get('overwrite_footer_links');
	$data['sticky_columns'] = $this->config->get('basel_sticky_columns');
	$data['basel_version'] = $this->config->get('basel_theme_version');
	$data['sticky_columns_offset'] = $this->config->get('basel_sticky_columns_offset');
	if ($this->config->get('basel_sticky_columns')) $this->document->addScript('catalog/view/theme/basel/js/theia-sticky-sidebar.min.js');
	
	// Footer links
	if ($this->config->get('basel_footer_columns')) {
		$basel_footer_columns = $this->config->get('basel_footer_columns');
	} else {
		$basel_footer_columns = array();
	}
	if ($this->config->get('basel_footer_columns')) {
	$data['basel_footer_columns'] = array();
	$count_columns = count($basel_footer_columns);
	if ($count_columns == 5) {
	$data['basel_columns_count'] = 'col-lg-20';
	} else {
	$data['basel_columns_count'] = 'col-lg-' . round(12/$count_columns);
	}
	function sortcolumns($a, $b) {return strcmp($a['sort'], $b['sort']);} usort($basel_footer_columns, 'sortcolumns');	
	foreach ($basel_footer_columns as $basel_footer_column) {
	  $links = array();
		if (isset($basel_footer_column['links'])) {
			foreach($basel_footer_column['links'] as $link){
			   if (isset($link['title'][$this->config->get('config_language_id')])){
				   $link_title = html_entity_decode($link['title'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			   } else {
				   $link_title = false;
			   }
			   if (isset($link['target']) && is_array($link['target'])) {
				   if (isset($link['target'][$lang_id])) {
					   $link_target = $link['target'][$lang_id];
				   } elseif (isset($link['target'][(string)$lang_id])) {
					   $link_target = $link['target'][(string)$lang_id];
				   } else {
					   $link_target = reset($link['target']);
				   }
			   } else {
				   $link_target = isset($link['target']) ? $link['target'] : '';
			   }
			   $links[] = array(
				   'title'      => $link_title,
				   'target'     => $link_target,
				   'sort'       => $link['sort']
			   );
			 }
		usort($links, function ($a, $b) { return $a['sort'] - $b['sort']; });
		}
	  if(isset($basel_footer_column['title'][$lang_id])) {
		$data['basel_footer_columns'][] = array(
			'title' => html_entity_decode($basel_footer_column['title'][$lang_id], ENT_QUOTES, 'UTF-8'),
			'sort' => $basel_footer_column['sort'],
			'links'  => $links
		);
	  }
	}
	}
	
	// Payment icon
	if ($this->request->server['HTTPS']) {
		$server = $this->config->get('config_ssl');
	} else {
		$server = $this->config->get('config_url');
	}
	
	if (is_file(DIR_IMAGE . $this->config->get('basel_payment_img'))) {
		$data['payment_img'] = $server . 'image/' . $this->config->get('basel_payment_img');
	} else {
		$data['payment_img'] = '';
	}
	
	// Popup
	$data['popup_delay'] = $this->config->get('basel_popup_note_delay');
	$data['popup_width_limit'] = $this->config->get('basel_popup_note_m');
	$data['view_popup'] = false;
	if ( ($this->config->get('basel_popup_note_status')) && (!isset($_COOKIE['basel_popup'])) ) {
		if ((!isset($_GET['route']) || (isset($_GET['route']) && $_GET['route'] == 'common/home')) || (!$this->config->get('basel_popup_note_home'))) {
			$data['view_popup'] = true;
			if ($this->config->get('basel_popup_note_once')) setcookie("basel_popup", "1", time()+60*60*24*30);
		}
	}
	
	// Cookie bar
	$data['view_cookie_bar'] = false;
	$this->load->language('basel/basel_theme');
	$data['basel_cookie_info'] = $this->language->get('basel_cookie_info');
	$data['basel_cookie_btn_close'] = $this->language->get('basel_cookie_btn_close');
	$data['basel_cookie_btn_more_info'] = $this->language->get('basel_cookie_btn_more_info');
	$data['href_more_info'] = $this->config->get('basel_cookie_bar_url');	
	if ( ($this->config->get('basel_cookie_bar_status')) && (!isset($_COOKIE['basel_cookie'])) ) {
	$data['view_cookie_bar'] = true;
	setcookie("basel_cookie", "1", time()+60*60*24*30);
	}	
