<?php
	$this->load->language('basel/basel_theme');
	// Widhlist Items
	if ($this->customer->isLogged()) {
		$this->load->model('account/wishlist');
		$data['wishlist_counter'] = $this->model_account_wishlist->getTotalWishlist();
	} else {
		$data['wishlist_counter'] = (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0);
	}
	// Cart Items
	$data['cart_items'] = $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0);
	
	// Cart Total
	$data['cart_amount'] = $this->load->controller('extension/basel/basel_features/total_amount');
	
	// Language/Currency Title
	$data['lang_curr_title'] = $this->load->controller('extension/basel/basel_features/lang_curr_title');
	$lang_id = $this->config->get('config_language_id');
	
	// Basel Search
	$data['basel_search'] = $this->load->controller('extension/basel/basel_features/basel_search');
	
	// Default menu with 3 levels
	$data['default_menu'] = $this->load->controller('extension/basel/default_menu');
	
	// Top Module Position
	if ($this->config->get('theme_default_directory') == 'basel') {
	$data['position_top'] = $this->load->controller('extension/basel/position_top');
		if ($this->config->get('config_maintenance')) {
		$this->user = new Cart\User($this->registry);
		if (!$this->user->isLogged()){ 
		$data['position_top'] = false;
		}
		}
	}
	
	// Datas
	$data['basel_header'] = $this->config->get('basel_header');
	$promo_message = $this->config->get('basel_promo');
	$promo_message2 = $this->config->get('basel_promo2');
	$data['promo_message'] = '';
	if (isset($promo_message[$lang_id]))
	$data['promo_message'] = html_entity_decode($promo_message[$lang_id], ENT_QUOTES, 'UTF-8');
	$data['promo_message2'] = '';
	if (isset($promo_message2[$lang_id]))
	$data['promo_message2'] = html_entity_decode($promo_message2[$lang_id], ENT_QUOTES, 'UTF-8');
	$data['top_line_style'] = $this->config->get('top_line_style');
	$data['top_line_width'] = $this->config->get('top_line_width');
	$data['main_header_width'] = $this->config->get('main_header_width');
	$data['main_menu_align'] = $this->config->get('main_menu_align');
	$data['header_login'] = $this->config->get('header_login');
	$data['header_search'] = $this->config->get('header_search');
	$data['basel_search_scheme'] = 'dark-search';
	
	$data['use_custom_links'] = $this->config->get('use_custom_links');
	if ($this->config->get('use_custom_links')) {
	$basel_links = $this->config->get('basel_links');
	$data['basel_links'] = array();
	function sortlinks($a, $b) {return strcmp($a['sort'], $b['sort']);} usort($basel_links, 'sortlinks');		
		foreach ($basel_links as $basel_link) {
			if(isset($basel_link['text'][$lang_id])) {
			if (isset($basel_link['target']) && is_array($basel_link['target'])) {
				if (isset($basel_link['target'][$lang_id])) {
					$link_target = $basel_link['target'][$lang_id];
				} elseif (isset($basel_link['target'][(string)$lang_id])) {
					$link_target = $basel_link['target'][(string)$lang_id];
				} else {
					$link_target = reset($basel_link['target']);
				}
			} else {
				$link_target = isset($basel_link['target']) ? $basel_link['target'] : '';
			}
			$data['basel_links'][] = array(
				'text' => html_entity_decode($basel_link['text'][$lang_id], ENT_QUOTES, 'UTF-8'),
				'target' => $link_target,
				'sort' => $basel_link['sort']
			);
		}
	  }
	}

	// Custom CSS
	$data['basel_custom_css_status'] = $this->config->get('basel_custom_css_status');
	$data['basel_custom_css'] = html_entity_decode($this->config->get('basel_custom_css'), ENT_QUOTES, 'UTF-8');
		
	// Custom Javascript
	$data['basel_custom_js_status'] = $this->config->get('basel_custom_js_status');
	$data['basel_custom_js'] = html_entity_decode($this->config->get('basel_custom_js'), ENT_QUOTES, 'UTF-8');
	
	// Menu Management
	$data['primary_menu'] = $this->config->get('primary_menu');
	$data['secondary_menu'] = $this->config->get('secondary_menu');
	if (($this->config->get('primary_menu') > 0) || ($this->config->get('secondary_menu') > 0)) {
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['basel_button_quickview'] = $this->language->get('basel_button_quickview');
		$data['basel_text_sale'] = $this->language->get('basel_text_sale');
		$data['basel_text_new'] = $this->language->get('basel_text_new');
		$data['basel_text_days'] = $this->language->get('basel_text_days');
		$data['basel_text_hours'] = $this->language->get('basel_text_hours');
		$data['basel_text_mins'] = $this->language->get('basel_text_mins');
		$data['basel_text_secs'] = $this->language->get('basel_text_secs');
		$data['countdown_status'] = $this->config->get('countdown_status');
		$data['salebadge_status'] = $this->config->get('salebadge_status');
		$this->load->model('extension/basel/basel_megamenu');
		$data['lang_id'] = $this->config->get('config_language_id');
		$data['logo_maxwidth'] = $this->config->get('logo_maxwidth');
	}
	if ($this->config->get('primary_menu') > 0) {
		$data['primary_menu_desktop'] = $this->model_extension_basel_basel_megamenu->getMenu($this->config->get('primary_menu'), $mobile = false);
		$data['primary_menu_mobile'] = $this->model_extension_basel_basel_megamenu->getMenu($this->config->get('primary_menu'), $mobile = true);
	}
	if ($this->config->get('secondary_menu') > 0) {
		$data['secondary_menu_desktop'] = $this->model_extension_basel_basel_megamenu->getMenu($this->config->get('secondary_menu'), $mobile = false);
		$data['secondary_menu_mobile'] = $this->model_extension_basel_basel_megamenu->getMenu($this->config->get('secondary_menu'), $mobile = true);
	}
	
	// Body Class
	$data['basel_body_class'] = '';
	$data['basel_body_class'] .= ' product-style' . $this->config->get('basel_list_style');
	$data['basel_body_class'] .= ' ' . $this->config->get('basel_cart_icon');
	if ($this->config->get('catalog_mode')) $data['basel_body_class'] .= ' catalog_mode';
	if ($this->config->get('basel_sticky_header')) $data['basel_body_class'] .= ' sticky-enabled';
	if ($this->config->get('basel_home_overlay_header')) $data['basel_body_class'] .= ' home-fixed-header';
	if (!$this->config->get('wishlist_status')) $data['basel_body_class'] .= ' wishlist_disabled';
	if (!$this->config->get('compare_status')) $data['basel_body_class'] .= ' compare_disabled';
	if (!$this->config->get('quickview_status')) $data['basel_body_class'] .= ' quickview_disabled';
	if (!$this->config->get('ex_tax_status')) $data['basel_body_class'] .= ' hide_ex_tax';
	if ($this->config->get('items_mobile_fw')) $data['basel_body_class'] .= ' mobile_1';
	if ($this->config->get('basel_cut_names')) $data['basel_body_class'] .= ' cut-names';
	if (!$this->config->get('basel_back_btn')) $data['basel_body_class'] .= ' basel-back-btn-disabled';
	if ($this->config->get('basel_main_layout')) $data['basel_body_class'] .= ' boxed-layout';
	if ($this->config->get('basel_content_width')) $data['basel_body_class'] .= ' ' . $this->config->get('basel_content_width');
	if ($this->config->get('basel_widget_title_style')) $data['basel_body_class'] .= ' widget-title-style' . $this->config->get('basel_widget_title_style');
	
	// Title styles
	if (isset($this->request->get['route'])) {
	$route = (string)$this->request->get['route'];
	if (isset($this->request->get['product_id'])) {
		$data['basel_body_class'] .= ' ' . $this->config->get('basel_titles_product');
	} elseif (isset($this->request->get['path']) || isset($this->request->get['manufacturer_id']) || ($route == 'product/search') || ($route == 'product/special')) {
		$data['basel_body_class'] .= ' ' . $this->config->get('basel_titles_listings');
	} elseif (preg_match('(affiliate|account)', $route) === 1) {
		$data['basel_body_class'] .= ' ' . $this->config->get('basel_titles_account');
	} elseif (preg_match('(checkout)', $route) === 1) {
		$data['basel_body_class'] .= ' ' . $this->config->get('basel_titles_checkout');
	} elseif ($route == 'information/contact') {
		$data['basel_body_class'] .= ' ' . $this->config->get('basel_titles_contact');
	} elseif (substr($route, 0, 15) == 'extension/blog/') {
		$data['basel_body_class'] .= ' ' . $this->config->get('basel_titles_blog');
	} else {
		$data['basel_body_class'] .= ' ' . $this->config->get('basel_titles_default');
	}
	}
	
	// For page specific css
	if ((float)VERSION >= 3.0) {
	if (isset($this->request->get['route'])) {
		if (isset($this->request->get['product_id'])) {
			$class = '-' . $this->request->get['product_id'];
		} elseif (isset($this->request->get['path'])) {
			$class = '-' . $this->request->get['path'];
		} elseif (isset($this->request->get['manufacturer_id'])) {
			$class = '-' . $this->request->get['manufacturer_id'];
		} elseif (isset($this->request->get['information_id'])) {
			$class = '-' . $this->request->get['information_id'];
		} else {
			$class = '';
		}
		$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
	} else {
		$data['class'] = 'common-home';
	}
	}
	
	// Top promotion message
	$data['notification_status'] = false;
	$data['top_promo_width'] = $this->config->get('basel_top_promo_width');
	$data['top_promo_close'] = $this->config->get('basel_top_promo_close');
	$data['top_promo_align'] = $this->config->get('basel_top_promo_align');
	$data['top_promo_text'] = '';
	$top_promo_text = $this->config->get('basel_top_promo_text');
	if (isset($top_promo_text[$lang_id]))
	$data['top_promo_text'] = html_entity_decode($top_promo_text[$lang_id], ENT_QUOTES, 'UTF-8');
	if (($this->config->get('basel_top_promo_status')) && (!isset($_COOKIE['basel_top_promo']))) {
	$data['notification_status'] = true;
	}
	
	// Mandatory CSS
	if ($this->cache->get('basel_mandatory_css_store_' . $this->config->get('config_store_id'))) {
		$data['basel_mandatory_css'] = $this->cache->get('basel_mandatory_css_store_' . $this->config->get('config_store_id'));
		} else {
		$madatory_css = '.top_line {line-height:' . $this->config->get('top_line_height') . 'px;}';
		$madatory_css .= '.header-main,.header-main .sign-in,#logo {line-height:' . $this->config->get('main_header_height') . 'px;height:' . $this->config->get('main_header_height') . 'px;}';
		$madatory_css .= '.sticky-enabled.sticky-active .sticky-header.short:not(.slidedown) .header-main,.sticky-enabled.offset250 .sticky-header.slidedown .header-main,.sticky-enabled.sticky-active .sticky-header.short .header-main .sign-in,.sticky-enabled.sticky-active .sticky-header.short:not(.slidedown) .header-main #logo,.sticky-enabled.sticky-active .header6 .sticky-header.short .header-main #logo {line-height:' . $this->config->get('main_header_height_sticky') . 'px;height:' . $this->config->get('main_header_height_sticky') . 'px;}';
		$madatory_css .= '@media (max-width: 991px) {.header-main,.sticky-enabled.offset250 .sticky-header.slidedown .header-main,#logo,.sticky-enabled.sticky-active .sticky-header.short .header-main #logo {line-height:' . $this->config->get('main_header_height_mobile') . 'px;height:' . $this->config->get('main_header_height_mobile') . 'px;}}';
		$madatory_css .= '.table-cell.menu-cell,.main-menu:not(.vertical) > ul,.main-menu:not(.vertical) > ul > li,.main-menu:not(.vertical) > ul > li > a,.main-menu:not(.vertical) > ul > li.dropdown-wrapper > a .fa-angle-down,.main-menu.vertical .menu-heading {line-height:' . $this->config->get('menu_height_normal') . 'px;height:' . $this->config->get('menu_height_normal') . 'px;}';
		$madatory_css .= '.sticky-enabled.sticky-active .table-cell.menu-cell:not(.vertical),.sticky-enabled.sticky-active .main-menu:not(.vertical) > ul,.sticky-enabled.sticky-active .main-menu:not(.vertical) > ul > li,.sticky-enabled.sticky-active .main-menu:not(.vertical) > ul > li > a,.sticky-enabled.sticky-active .main-menu:not(.vertical) > ul > li.dropdown-wrapper > a .fa-angle-down {line-height:' . $this->config->get('menu_height_sticky') . 'px;height:' . $this->config->get('menu_height_sticky') . 'px;}';
		$search_height = round($this->config->get('menu_height_normal') * 0.7);
		$madatory_css .= '.full-search-wrapper .search-main input,.full-search-wrapper .search-category select {height:' . $search_height . 'px;min-height:' . $search_height . 'px;}';
		$madatory_css .= '@media (min-width: 992px) {.sticky-enabled.sticky-active .header3 .sticky-header-placeholder,.sticky-enabled.offset250 .header5 .header-main {padding-bottom:' . $this->config->get('menu_height_sticky') . 'px;}}';
		$madatory_css .= '#logo img {max-width:' . $this->config->get('logo_maxwidth') . 'px;}';
		$this->cache->set('basel_mandatory_css_store_' . $this->config->get('config_store_id'), $madatory_css);
		$data['basel_mandatory_css'] = $this->cache->get('basel_mandatory_css_store_' . $this->config->get('config_store_id'));
	}	
	
	// Custom colors
	if ($this->config->get('basel_design_status')) {
	$data['basel_styles_status'] = $this->config->get('basel_design_status');
	$data['basel_search_scheme'] = $this->config->get('basel_search_scheme');
		if ($this->cache->get('basel_styles_cache_store_' . $this->config->get('config_store_id'))) {
		$data['basel_styles_cache'] = $this->cache->get('basel_styles_cache_store_' . $this->config->get('config_store_id'));
		} else {
		$styles = 'a:hover, a:focus, .menu-cell .dropdown-inner a:hover, .link-hover-color:hover, .primary-color, .cm_item .primary-color, .nav-tabs.text-center.nav-tabs-sm > li.active {color:' . $this->config->get('basel_primary_accent_color') . ';}';
		$styles .= '.primary-bg-color, .widget-title-style2 .widget .widget-title-separator:after, .nav-tabs.text-center.nav-tabs-sm > li.active > a:after,.nav-tabs > li > a:hover,.nav-tabs > li > a:focus,.nav-tabs > li.active > a,.nav-tabs > li.active > a:hover,.nav-tabs > li.active > a:focus {background-color:' . $this->config->get('basel_primary_accent_color') . ';}';
		$styles .= 'div.ui-slider-range.ui-widget-header, .ui-state-default, .ui-widget-content .ui-state-default {background:' . $this->config->get('basel_primary_accent_color') . ' !important;}';
		$styles .= '.primary-color-border, .nav-tabs {border-color:' . $this->config->get('basel_primary_accent_color') . '!important;}';
		$styles .= '.top_notificaiton {background-color:' . $this->config->get('basel_top_note_bg') . ';}';
		$styles .= '.top_notificaiton, .top_notificaiton a {color:' . $this->config->get('basel_top_note_color') . ';}';
		$styles .= '.top_line {background-color:' . $this->config->get('basel_top_line_bg') . ';}';
		$styles .= '.top_line, .top_line a {color:' . $this->config->get('basel_top_line_color') . ';}';
		$styles .= '.top_line .anim-underline:after, .top_line .links ul > li + li:before, .top_line .links .setting-ul > .setting-li:before {background-color:' . $this->config->get('basel_top_line_color') . ';}';
		$styles .= '.header-style {background-color:' . $this->config->get('basel_header_bg') . ';}';
		$styles .= '.header-main, .header-main a:not(.btn), .header-main .main-menu > ul > li > a:hover {color:' . $this->config->get('basel_header_color') . ';}';
		$styles .= '.header-main .sign-in:after, .header-main .anim-underline:after, .header-main .sign-in .anim-underline:after {background-color:' . $this->config->get('basel_header_color') . ';}';
		$styles .= '.main-menu:not(.vertical) > ul > li:hover > a > .top, .header-main .shortcut-wrapper:hover .icon-magnifier, .header-main #cart:hover .shortcut-wrapper {opacity:0.8;}';
		$styles .= '.shortcut-wrapper .counter {background-color:' . $this->config->get('basel_header_accent') . ';}';
		$styles .= '.header-bottom, .menu-style {background-color:' . $this->config->get('basel_header_menu_bg') . ';}';
		$styles .= '.menu-style .main-menu a > .top,.menu-style .main-menu a > .fa-angle-down, .menu-style .main-menu .search-trigger {color:' . $this->config->get('basel_header_menu_color') . ';}';
		$styles .= '.menu-tag.sale {background-color:' . $this->config->get('basel_menutag_sale_bg') . ';}';
		$styles .= '.menu-tag.sale:before {color:' . $this->config->get('basel_menutag_sale_bg') . ';}';
		$styles .= '.menu-tag.new {background-color:' . $this->config->get('basel_menutag_new_bg') . ';}';
		$styles .= '.menu-tag.new:before {color:' . $this->config->get('basel_menutag_new_bg') . ';}';
		$styles .= '.vertical-menu-bg, .vertical-menu-bg.dropdown-content {background-color:' . $this->config->get('basel_vertical_menu_bg') . ';}';
		$styles .= '.main-menu.vertical > ul > li:hover > a {background-color:' . $this->config->get('basel_vertical_menu_bg_hover') . ';}';
		$styles .= '.title_in_bc .breadcrumb-holder {background-color:' . $this->config->get('basel_bc_bg_color') . ';}';
		$styles .= '.title_in_bc .breadcrumb-holder, .title_in_bc .breadcrumb-holder .basel-back-btn {color:' . $this->config->get('basel_bc_color') . ';}';
		$styles .= '.title_in_bc .basel-back-btn>i,.title_in_bc .basel-back-btn>i:after {background-color:' . $this->config->get('basel_bc_color') . ';}';
		if ($this->config->get('basel_bc_bg_img')) {
		$styles .= '.title_in_bc .breadcrumb-holder {background-position:' . $this->config->get('basel_bc_bg_img_pos') . ';background-repeat:' . $this->config->get('basel_bc_bg_img_repeat') . ';background-size:' . $this->config->get('basel_bc_bg_img_size') . ';background-attachment:' . $this->config->get('basel_bc_bg_img_att') . ';background-image:url(' . $server . 'image/' . $this->config->get('basel_bc_bg_img') . ');}';}		
		$styles .= '.btn-primary, a.btn-primary,.btn-neutral {background-color:' . $this->config->get('basel_default_btn_bg') . ';color:' . $this->config->get('basel_default_btn_color') . ';}';
		$styles .= '.btn-primary:hover,.btn-primary.active,.btn-primary:focus,.btn-default:hover,.btn-default.active,.btn-default:focus {background-color:' . $this->config->get('basel_default_btn_bg_hover') . '!important;color:' . $this->config->get('basel_default_btn_color_hover') . ' !important;}';
		$styles .= '.btn-contrast-outline {border-color:' . $this->config->get('basel_contrast_btn_bg') . ';color:' . $this->config->get('basel_contrast_btn_bg') . ';}';
		$styles .= '.btn-contrast, a.btn-contrast, .btn-contrast-outline:hover {background-color:' . $this->config->get('basel_contrast_btn_bg') . ';}';
		$styles .= '.sale_badge {background-color:' . $this->config->get('basel_salebadge_bg') . ';color:' . $this->config->get('basel_salebadge_color') . '}';
		$styles .= '.new_badge {background-color:' . $this->config->get('basel_newbadge_bg') . ';color:' . $this->config->get('basel_newbadge_color') . '}';
		$styles .= '.price, #cart-content .totals tbody > tr:last-child > td:last-child {color:' . $this->config->get('basel_price_color') . '}';
		$styles .= '#footer {background-color:' . $this->config->get('basel_footer_bg') . ';}';
		$styles .= '#footer, #footer a, #footer a:hover, #footer h5 {color:' . $this->config->get('basel_footer_color') . ';}';
		$styles .= '#footer .footer-copyright:before {background-color:' . $this->config->get('basel_footer_color') . ';opacity:0.05;}';
		$styles .= '#footer h5:after {background-color:' . $this->config->get('basel_footer_h5_sep') . ';}';
		$styles .= 'body.boxed-layout {background-color:' . $this->config->get('basel_body_bg_color') . ';}';
		if ($this->config->get('basel_body_bg_img')) {
		$styles .= 'body.boxed-layout {background-position:' . $this->config->get('basel_body_bg_img_pos') . ';background-repeat:' . $this->config->get('basel_body_bg_img_repeat') . ';background-size:' . $this->config->get('basel_body_bg_img_size') . ';background-attachment:' . $this->config->get('basel_body_bg_img_att') . ';background-image:url(' . $server . 'image/' . $this->config->get('basel_body_bg_img') . ');}';
		}
		$this->cache->set('basel_styles_cache_store_' . $this->config->get('config_store_id'), $styles);
		$data['basel_styles_cache'] = $this->cache->get('basel_styles_cache_store_' . $this->config->get('config_store_id'));
		}
	}
	
	// Custom Fonts
	if ($this->config->get('basel_typo_status')) {
	$data['basel_typo_status'] = $this->config->get('basel_typo_status');
	// Add import link to head tag
	$basel_fonts = $this->config->get('basel_fonts');
	$data['basel_fonts'] = array();
	$font_list = '';
	if (isset($basel_fonts)) {
	foreach ($basel_fonts as $basel_font) {
		$font_list .= $basel_font['import'] . '%7C';
	}
	}
	$this->document->addStyle('//fonts.googleapis.com/css?family=' . $font_list);
	if ($this->cache->get('basel_fonts_cache_store_' . $this->config->get('config_store_id'))) {
	$data['basel_fonts_cache'] = $this->cache->get('basel_fonts_cache_store_' . $this->config->get('config_store_id'));
	} else {
	$font_styles = 'body,.product-name.main-font,.gridlist .single-product .product-name,.gridlist .single-blog .blog-title,#bc-h1-holder #page-title {font-family:' . $this->config->get('body_font_fam') . ';}';
	if (!$this->config->get('body_font_italic_status')) {
	$font_styles .= '.header-main .sign-in .anim-underline,.special_countdown p,.blog .blog_stats i,label i,.cm_item i {font-style:normal;}';
	}
	$font_styles .= 'b, strong, .nav-tabs > li > a, #cart-content .totals tbody > tr:last-child > td, .main-menu .dropdown-inner .static-menu > ul > li.has-sub > a, .main-menu.vertical > ul > li > a, .table-bordered > thead > tr > td, .table-bordered > tbody > tr > td.total-cell, .table-bordered.totals tbody > tr:last-child > td:last-child, .compare-table table tbody tr td:first-child, .totals-slip .table-holder table tr:last-child td, .panel-group .panel-heading .panel-title, .badge i, .product-style1 .grid .single-product .price-wrapper .btn-outline, .options .name label, .dropdown-inner h4.column-title, .product-style6 .grid .single-product .price, .product-style6 .single-product .btn-contrast {font-weight:' . $this->config->get('body_font_bold_weight') . ';}';
	$font_styles .= '.product-name, .blog-title, .product-h1 h1, .contrast-heading, .contrast-font {font-family:' . $this->config->get('contrast_font_fam') . ';}';
	$font_styles .= '.promo-style-2 h3, .promo-style-4 h3,.table-bordered > tbody > tr > td.price-cell,.grid .single-product .price,.menu-product .sale-counter div,.table.specification > tbody > tr > td b, .bordered-list-title {font-size:' . $this->config->get('body_font_size_16') . ';}';
	$font_styles .= '.table.products .remove,.full-search-wrapper .search-category select,.blog_post .blog_comment,.video-jumbotron p,.compare-table table tbody tr td:first-child,.grid .single-product .product-name,.grid .single-product .product-name:hover,.list .single-product .product-name {font-size:' . $this->config->get('body_font_size_15') . ';}';
	$font_styles .= 'body,input,textarea,select,.form-control,.icon-element,.main-menu > ul > li,.grid-holder .item,.cm_content .cm_item,.instruction-box .caption a,.btn,a.button,input.button,button.button,a.button-circle,.single-product .price .price-old,.special_countdown p,.list .item.single-product .price-tax, .form-control,label,.icon-element {font-size:' . $this->config->get('body_font_size_14') . ';}';
	$font_styles .= 'small,.form-control.input-sm,.shortcut-wrapper,.header5 .links > ul > li > a,.header5 .setting-ul > .setting-li > a,.breadcrumb,.sign-up-field .sign-up-respond span,.badge i,.special_countdown div i,.top_line {font-size:' . $this->config->get('body_font_size_13') . ';}';
	$font_styles .= '.tooltip,.links ul > li > a,.setting-ul > .setting-li > a,.table.products .product-name,#cart-content .totals, .main-menu.vertical > ul > li > a,.single-blog .banner_wrap .tags a,.bordered-list a {font-size:' . $this->config->get('body_font_size_12') . ';}';
	$font_styles .= 'h1, h2, h3, h4, h5, h6 {font-family:' . $this->config->get('headings_fam') . ';font-weight:' . $this->config->get('headings_weight') . ';}';
	$font_styles .= '.panel-group .panel-heading .panel-title, legend {font-size:' . $this->config->get('headings_size_sm') . ';}';
	$font_styles .= '.title_in_bc .login-area h2, .panel-body h2, h3.lined-title.lg, .grid1 .single-blog .blog-title, .grid2 .single-blog .blog-title {font-size:' . $this->config->get('headings_size_lg') . ';}';
	$font_styles .= 'h1, .product-info .table-cell.right h1#page-title {font-family:' . $this->config->get('h1_inline_fam') . ';font-size:' . $this->config->get('h1_inline_size') . ';font-weight:' . $this->config->get('h1_inline_weight') . ';text-transform:' . $this->config->get('h1_inline_trans') . ';letter-spacing:' . $this->config->get('h1_inline_ls') . ';}';
	$font_styles .= '.title_in_bc .breadcrumb-holder #title-holder {font-family:' . $this->config->get('h1_breadcrumb_fam') . ';}';
	
	$font_styles .= '.title_in_bc .breadcrumb-holder #title-holder #page-title, .title_in_bc.tall_height_bc .breadcrumb-holder #title-holder #page-title, .title_in_bc.extra_tall_height_bc .breadcrumb-holder #title-holder #page-title {font-size:' . $this->config->get('h1_breadcrumb_size') . ';font-weight:' . $this->config->get('h1_breadcrumb_weight') . ';text-transform:' . $this->config->get('h1_breadcrumb_trans') . ';letter-spacing:' . $this->config->get('h1_breadcrumb_ls') . ';}';
	$font_styles .= '.widget .widget-title .main-title {font-family:' . $this->config->get('widget_lg_fam') . ';font-size:' . $this->config->get('widget_lg_size') . ';font-weight:' . $this->config->get('widget_lg_weight') . ';text-transform:' . $this->config->get('widget_lg_trans') . ';letter-spacing:' . $this->config->get('widget_lg_ls') . ';}';
	$font_styles .= '.lang-curr-wrapper h4, .column .widget .widget-title .main-title, #footer h5, .dropdown-inner h4.column-title b, .blog_post .section-title {font-family:' . $this->config->get('widget_sm_fam') . ';font-size:' . $this->config->get('widget_sm_size') . ';font-weight:' . $this->config->get('widget_sm_weight') . ';text-transform:' . $this->config->get('widget_sm_trans') . ';letter-spacing:' . $this->config->get('widget_sm_ls') . ';}';
	$font_styles .= '.main-menu:not(.vertical) > ul > li > a > .top {font-family:' . $this->config->get('menu_font_fam') . ';font-size:' . $this->config->get('menu_font_size') . ';font-weight:' . $this->config->get('menu_font_weight') . ';text-transform:' . $this->config->get('menu_font_trans') . ';letter-spacing:' . $this->config->get('menu_font_ls') . ';}';
	$this->cache->set('basel_fonts_cache_store_' . $this->config->get('config_store_id'), $font_styles);
	$data['basel_fonts_cache'] = $this->cache->get('basel_fonts_cache_store_' . $this->config->get('config_store_id'));
	} 
	} else {
	$this->document->addStyle('//fonts.googleapis.com/css?family=Karla:400,400i,700,700i%7CLora:400,400i');
	}
