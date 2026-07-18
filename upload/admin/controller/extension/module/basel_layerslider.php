<?php
class ControllerExtensionModuleBaselLayerslider extends Controller {
	private $error = array();
	public function index() {
		
		if ((float)VERSION >= 3.0) {
			$model_module_load = 'setting/module';
			$model_module_path = 'model_setting_module';
			$token_prefix = 'user_token';
			$modules_url = 'marketplace/extension';
			$module_prefix = 'module_';
		} else {
			$model_module_load = 'extension/module';
			$model_module_path = 'model_extension_module';
			$token_prefix = 'token';
			$modules_url = 'extension/extension';
			$module_prefix = '';
		}
		
		$this->load->language('extension/module/basel_layerslider');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addStyle('view/javascript/basel/layerslider.css');
		$this->document->addStyle('view/javascript/basel/css/bootstrap-colorpicker.min.css');
		$this->document->addStyle('view/javascript/basel/css/jquery-ui.css');
		$this->document->addScript('view/javascript/basel/jquery-ui.js');
		$this->document->addScript('view/javascript/basel/js/bootstrap-colorpicker.min.js');
		$this->document->addScript('view/javascript/basel/js/removeclasses.js');
		
		$this->load->model('tool/image');
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
			$data['base_url'] = HTTPS_CATALOG;
		} else {
			$server = $this->config->get('config_url');
			$data['base_url'] = HTTP_CATALOG;
		}
			
		$this->load->model($model_module_load);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->$model_module_path->addModule('basel_layerslider', $this->request->post);
			} else {
				$this->$model_module_path->editModule($this->request->get['module_id'], $this->request->post);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			if (isset($this->request->post['save']) && $this->request->post['save'] == 'stay' && $this->request->get['module_id']) {
				$this->response->redirect($this->url->link('extension/module/basel_layerslider', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true)); 
			} else {
				$this->response->redirect($this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true));
            }
		}
		
		if (isset($this->request->get['module_id'])) {
			$data['has_module_id'] = true; 
		} else {
			$data['has_module_id'] = false;
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['tab_section'] = $this->language->get('tab_section');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_add_section'] = $this->language->get('text_add_section');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['text_nav_buttons'] = $this->language->get('text_nav_buttons');
		$data['text_nav_bullets'] = $this->language->get('text_nav_bullets');
		$data['text_nav_timer_bar'] = $this->language->get('text_nav_timer_bar');
		$data['h3_module_settings'] = $this->language->get('h3_module_settings');
		$data['h3_slideshow_sizing'] = $this->language->get('h3_slideshow_sizing');
		$data['h3_slide_navigation'] = $this->language->get('h3_slide_navigation');
		$data['button_save'] = $this->language->get('button_save');
        $data['button_save_stay'] = $this->language->get('button_save_stay');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_add_section'] = $this->language->get('button_add_section');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['text_general_settings'] = $this->language->get('text_general_settings');
		$data['text_google_fonts'] = $this->language->get('text_google_fonts');
		$data['text_slides'] = $this->language->get('text_slides');
		$data['text_preview_language'] = $this->language->get('text_preview_language');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_minheight'] = $this->language->get('entry_minheight');
		$data['entry_fullwidth'] = $this->language->get('entry_fullwidth');
		$data['entry_margin_bottom'] = $this->language->get('entry_margin_bottom');
		$data['entry_loop'] = $this->language->get('entry_loop');
		$data['entry_speed'] = $this->language->get('entry_speed');
		$data['text_name'] = $this->language->get('text_name');
		$data['text_import'] = $this->language->get('text_import');
		$data['fonts_help_block'] = $this->language->get('fonts_help_block');
		$data['text_slide_settings'] = $this->language->get('text_slide_settings');
		$data['text_sort_order'] = $this->language->get('text_sort_order');
		$data['text_duration'] = $this->language->get('text_duration');
		$data['text_slide_transition'] = $this->language->get('text_slide_transition');
		$data['text_link_target'] = $this->language->get('text_link_target');
		$data['text_slide_preview'] = $this->language->get('text_slide_preview');
		$data['text_change_slide_bg'] = $this->language->get('text_change_slide_bg');
		$data['text_add_text_layer'] = $this->language->get('text_add_text_layer');
		$data['text_add_button_layer'] = $this->language->get('text_add_button_layer');
		$data['text_add_image_layer'] = $this->language->get('text_add_image_layer');
		$data['text_layer_list'] = $this->language->get('text_layer_list');
		$data['text_layer_in'] = $this->language->get('text_layer_in');
		$data['text_layer_out'] = $this->language->get('text_layer_out');
		$data['text_system_fonts'] = $this->language->get('text_system_fonts');
		$data['text_google_fonts'] = $this->language->get('text_google_fonts');	
		$data['text_slide_duration'] = $this->language->get('text_slide_duration');
		$data['text_layer_settings'] = $this->language->get('text_layer_settings');
		$data['text_layer_content'] = $this->language->get('text_layer_content');
		$data['text_layer_position'] = $this->language->get('text_layer_position');
		$data['text_offset_left'] = $this->language->get('text_offset_left');
		$data['text_offset_top'] = $this->language->get('text_offset_top');
		$data['text_layer_style'] = $this->language->get('text_layer_style');
		$data['text_font_family'] = $this->language->get('text_font_family');
		$data['text_font_weight'] = $this->language->get('text_font_weight');
		$data['text_font_size'] = $this->language->get('text_font_size');
		$data['text_color'] = $this->language->get('text_color');
		$data['text_background'] = $this->language->get('text_background');
		$data['text_padding'] = $this->language->get('text_padding');
		$data['text_border_radius'] = $this->language->get('text_border_radius');
		$data['text_custom_css'] = $this->language->get('text_custom_css');
		$data['text_animation_in'] = $this->language->get('text_animation_in');
		$data['text_animation_out'] = $this->language->get('text_animation_out');
		$data['text_effect'] = $this->language->get('text_effect');
		$data['text_easing'] = $this->language->get('text_easing');
		$data['text_duration'] = $this->language->get('text_duration');
		$data['text_button_text'] = $this->language->get('text_button_text');
		$data['text_button_href'] = $this->language->get('text_button_href');
		$data['text_button_target'] = $this->language->get('text_button_target');
		$data['text_button_class'] = $this->language->get('text_button_class');
		$data['text_layer_sort_order'] = $this->language->get('text_layer_sort_order');
		$data['text_layer_parallax'] = $this->language->get('text_layer_parallax');
		$data['text_slide_kenburn'] = $this->language->get('text_slide_kenburn');
		$data['text_bg_color'] = $this->language->get('text_bg_color');
		$data['text_heading_minheight'] = $this->language->get('text_heading_minheight');
		$data['text_layer_minheight'] = $this->language->get('text_layer_minheight');
		$data['text_circle_arrows'] = $this->language->get('text_circle_arrows');
		$data['text_simple_arrows'] = $this->language->get('text_simple_arrows');
		

		// Ken Burns //
		$data['slide_kenburns'][] = array();
		$data['slide_kenburns'] = array(
			"0" => "Disabled",
			"zoom-light" => "Zoom in (Light)",
			"zoom-left-light" => "Zoom in + Move Left (Light)",
			"zoom-right-light" => "Zoom in + Move Right (Light)",
			"zoom-medium" => "Zoom in (Medium)",
			"zoom-left-medium" => "Zoom in + Move Left (Medium)",
			"zoom-right-medium" => "Zoom in + Move Right (Medium)",
			"zoom-hard" => "Zoom in (Hard)",
			"zoom-left-hard" => "Zoom in + Move Left (Hard)",
			"zoom-right-hard" => "Zoom in + Move Right (Hard)"
		);
		
		// System Fonts //
		$data['system_fonts'][] = array();
		$data['system_fonts'] = array(
			"Arial, Helvetica Neue, Helvetica, sans-serif" => "Arial",
			"Comic Sans MS, Comic Sans MS, cursive" => "Comic sans",
			"Courier New, Courier New, monospace" => "Courier New",
			"Georgia, Times, Times New Roman, serif" => "Georgia",
			"Impact, Charcoal, sans-serif" => "Impact",
			"Lucida Sans Typewriter, Lucida Console, Monaco, Bitstream Vera Sans Mono, monospace" => "Lucida Sans Typewriter",
			"Palatino, Palatino Linotype, Palatino LT STD, Book Antiqua, Georgia, serif" => "Palatino Linotype",
			"Tahoma, Verdana, Segoe, sans-serif" => "Tahoma",
			"Times New Roman, Times, Baskerville, Georgia, serif" => "Times New Roman",
			"Trebuchet MS, Lucida Grande, Lucida Sans Unicode, Lucida Sans, Tahoma, sans-serif" => "Trebuchet",
			"Verdana, Geneva, sans-serif" => "Verdana"
		);
		
		// Transitoins In 
		$data['transitions'][] = array();
		$data['transitions'] = array(
			"fade()" 								=> "Fade",
			"left(short)" 							=> "Left (Short)",
			"left(long)" 							=> "Left (Long)",
			"left(1200)" 							=> "Left (Extra Long)",
			"right(short)" 							=> "Right (Short)",
			"right(long)" 							=> "Right (Long)",
			"right(1200)" 							=> "Right (Extra Long)",
			"top(short)" 							=> "Top (Short)",
			"top(long)" 							=> "Top (Long)",
			"top(1200)" 							=> "Top (Extra Long)",
			"bottom(short)" 						=> "Bottom (Short)",
			"bottom(long)" 							=> "Bottom (Long)",
			"bottom(1200)" 							=> "Bottom (Extra Top)",
			"front(200)" 							=> "Front (Short)",
			"front(1500)" 							=> "Front (Long)",
			"back(200)" 							=> "Back (Short)",
			"back(1500)" 							=> "Back (Long)",
			"rotate(300,c)" 						=> "Rotate",
			"rotateleft(45|180,long,br,true)" 		=> "Rotate (Left)",
			"rotateright(-45|-180,long,bl,true)" 	=> "Rotate (Right)",
			"rotatetop(45|180,short,tr,true)" 	 	=> "Rotate (Top)",
			"rotatebottom(45|180,short,bl,true)" 	=> "Rotate (Bottom)",
			"rotatefront(300,800,c,true)" 			=> "Rotate (Front)",
			"rotateback(300,800,c,true)" 			=> "Rotate (Back)"
		);
		
		// Easings 
		$data['easings'][] = array();
		$data['easings'] = array(
			"linear"            => "linear",
			"ease"              => "ease",
			"easeIn"            => "ease-in",
			"easeOut"           => "ease-out",
			"easeInOut"         => "ease-in-out",
			"easeInCubic"       => "easeInCubic",
			"easeOutCubic"      => "easeOutCubic",
			"easeInOutCubic"    => "easeInOutCubic",
			"easeInCirc"        => "easeInCirc",
			"easeOutCirc"       => "easeOutCirc",
			"easeInOutCirc"     => "easeInOutCirc",
			"easeInExpo"        => "easeInExpo",
			"easeOutExpo"       => "easeOutExpo",
			"easeInOutExpo"     => "easeInOutExpo",
			"easeInQuad"        => "easeInQuad",
			"easeOutQuad"       => "easeOutQuad",
			"easeInOutQuad"     => "easeInOutQuad",
			"easeInQuart"       => "easeInQuart",
			"easeOutQuart"      => "easeOutQuart",
			"easeInOutQuart"    => "easeInOutQuart",
			"easeInQuint"       => "easeInQuint",
			"easeOutQuint"      => "easeOutQuint",
			"easeInOutQuint"    => "easeInOutQuint",
			"easeInSine"        => "easeInSine",
			"easeOutSine"       => "easeOutSine",
			"easeInOutSine"     => "easeInOutSine",
			"easeInBack"        => "easeInBack",
			"easeOutBack"       => "easeOutBack"			
		);
		
		// Font Weights 
		$data['fontweights'][] = array();
		$data['fontweights'] = array(
			"400" 		=> "400 (Normal)",
			"100" 		=> "100",
			"200" 		=> "200",
			"300" 		=> "300",
			"500" 		=> "500",
			"600" 		=> "600",
			"700" 		=> "700",
			"800" 		=> "800",
			"900" 		=> "900"
		);
		
		// Button Classes
		$data['button_classes'][] = array();
		$data['button_classes'] = array(
			"btn btn-link" 								=> "Dark - Plain Text Link",
			"btn btn-tiny btn-primary" 					=> "Dark - Extra Small",
			"btn btn-tiny btn-outline" 					=> "Dark - Extra Small (Outline)",
			"btn btn-sm btn-primary" 					=> "Dark - Small",
			"btn btn-sm btn-outline" 					=> "Dark - Small (Outline)",
			"btn btn-primary" 							=> "Dark - Medium",
			"btn btn-outline" 							=> "Dark - Medium (Outline)",
			"btn btn-lg btn-primary" 					=> "Dark - Large",
			"btn btn-lg btn-outline" 					=> "Dark - Large (Outline)",
			"btn btn-link-light" 						=> "Light - Plain Text Link",
			"btn btn-tiny btn-light" 					=> "Light - Extra Small",
			"btn btn-tiny btn-light-outline" 			=> "Light - Extra Small (Outline)",
			"btn btn-sm btn-light" 						=> "Light - Small",
			"btn btn-sm btn-light-outline" 				=> "Light - Small (Outline)",
			"btn btn-light" 							=> "Light - Medium",
			"btn btn-light-outline" 					=> "Light - Medium (Outline)",
			"btn btn-lg btn-light" 						=> "Light - Large",
			"btn btn-lg btn-light-outline" 				=> "Light - Large (Outline)",
			"btn btn-tiny btn-contrast" 				=> "Contrast Color - Extra Small",
			"btn btn-tiny btn-contrast-outline" 		=> "Contrast Color - Extra Small (Outline)",
			"btn btn-sm btn-contrast" 					=> "Contrast Color - Small",
			"btn btn-sm btn-contrast-outline" 			=> "Contrast Color - Small (Outline)",
			"btn btn-contrast" 							=> "Contrast Color - Medium",
			"btn btn-contrast-outline" 					=> "Contrast Color - Medium (Outline)",
			"btn btn-lg btn-contrast" 					=> "Contrast Color - Large",
			"btn btn-lg btn-contrast-outline" 			=> "Contrast Color - Large (Outline)",
		);
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/basel_layerslider', $token_prefix . '=' . $this->session->data[$token_prefix], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/basel_layerslider', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true)
			);			
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/basel_layerslider', $token_prefix . '=' . $this->session->data[$token_prefix], true);
		} else {
			$data['action'] = $this->url->link('extension/module/basel_layerslider', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true);
		}
		
		$data['cancel'] = $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true);
	
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->$model_module_path->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '1140';
		}
		
		
		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '500';
		}
		
		if (isset($this->request->post['minheight'])) {
			$data['minheight'] = $this->request->post['minheight'];
		} elseif (!empty($module_info)) {
			$data['minheight'] = $module_info['minheight'];
		} else {
			$data['minheight'] = '0';
		}
		
		if (isset($this->request->post['fullwidth'])) {
			$data['fullwidth'] = $this->request->post['fullwidth'];
		} elseif (!empty($module_info)) {
			$data['fullwidth'] = $module_info['fullwidth'];
		} else {
			$data['fullwidth'] = '0';
		}
		
		if (isset($this->request->post['loop'])) {
			$data['loop'] = $this->request->post['loop'];
		} elseif (!empty($module_info)) {
			$data['loop'] = $module_info['loop'];
		} else {
			$data['loop'] = '0';
		}
		
		if (isset($this->request->post['speed'])) {
			$data['speed'] = $this->request->post['speed'];
		} elseif (!empty($module_info)) {
			$data['speed'] = $module_info['speed'];
		} else {
			$data['speed'] = '20';
		}
		
		if (isset($this->request->post['margin_bottom'])) {
			$data['margin_bottom'] = $this->request->post['margin_bottom'];
		} elseif (!empty($module_info)) {
			$data['margin_bottom'] = $module_info['margin_bottom'];
		} else {
			$data['margin_bottom'] = '63px';
		}
		
		if (isset($this->request->post['slide_transition'])) {
			$data['slide_transition'] = $this->request->post['slide_transition'];
		} elseif (!empty($module_info)) {
			$data['slide_transition'] = $module_info['slide_transition'];
		} else {
			$data['slide_transition'] = 'basic';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['lang'] = $this->request->post['lang'];
		} elseif (!empty($module_info)) {
			$data['lang'] = $module_info['lang'];
		} else {
			$data['lang'] = $this->config->get('config_language_id');
		}
		
		if (isset($this->request->post['nav_buttons'])) {
			$data['nav_buttons'] = $this->request->post['nav_buttons'];
		} elseif (!empty($module_info)) {
			$data['nav_buttons'] = $module_info['nav_buttons'];
		} else {
			$data['nav_buttons'] = 'simple-arrows';
		}
		
		if (isset($this->request->post['nav_bullets'])) {
			$data['nav_bullets'] = $this->request->post['nav_bullets'];
		} elseif (!empty($module_info)) {
			$data['nav_bullets'] = $module_info['nav_bullets'];
		} else {
			$data['nav_bullets'] = false;
		}
		
		if (isset($this->request->post['nav_timer_bar'])) {
			$data['nav_timer_bar'] = $this->request->post['nav_timer_bar'];
		} elseif (!empty($module_info)) {
			$data['nav_timer_bar'] = $module_info['nav_timer_bar'];
		} else {
			$data['nav_timer_bar'] = 'true';
		}
		
		

		
		// Google Web Fonts From Module
		if (isset($this->request->post['g_fonts'])) {
			$data['g_fonts'] = $this->request->post['g_fonts'];
		} elseif (!empty($module_info)) {
			if (isset($module_info['g_fonts'])) {
			$g_fonts = $module_info['g_fonts'];
			}
		} else {
			$g_fonts = array();
		}
		
		$data['g_fonts'] = array();
		
		if (isset($g_fonts)) {
		
		foreach ($g_fonts as $g_font) {
			
		$this->document->addStyle('http://fonts.googleapis.com/css?family=' . $g_font['import']);
				
			$data['g_fonts'][] = array(
				'name'   => $g_font['name'],
				'import'   => $g_font['import']
			);
		}
		}
		
		// Google Web Fonts From Theme
		if ($this->config->get('basel_typo_status')) {
			$basel_fonts = $this->config->get('basel_fonts');
		} else {
			$basel_fonts = array();
			$basel_fonts[] = array ( 
			'import' => 'Karla:400,400i,700,700i', 
			'name' => "'Karla', sans-serif" 
			);
			$basel_fonts[] = array ( 
			'import' => 'Lora:400,400i', 
			'name' => "'Lora', serif" 
			);
		}
		$data['basel_fonts'] = array();
		if (isset($basel_fonts)) {
		
		foreach ($basel_fonts as $basel_font) {
			
		$this->document->addStyle('http://fonts.googleapis.com/css?family=' . $basel_font['import']);
				
			$data['basel_fonts'][] = array(
				'name'   => $basel_font['name'],
				'import'   => $basel_font['import']
			);
		}
		}
		
		// Custom Colors From Theme
		$data['contrast_color'] = '';
		if ($this->config->get('basel_design_status')) {
			$data['contrast_color'] = $this->config->get('basel_contrast_btn_bg');
		}
		
		// Slides & Layers
        if (isset($this->request->post['sections'])) {
			$data['sections'] = $this->request->post['sections'];
		} elseif (!empty($module_info)) {
			if (isset($module_info['sections'])) {
			$sections = $module_info['sections'];
			}
		} else {
			$sections = array();
		}
		
		$data['sections'] = array();
		
		if (isset($sections)) {
		foreach ($sections as $section) {
			
			$groups = array();
			
			$i = 0;
			
            if (isset($section['groups'])) {
				foreach($section['groups'] as $group) {
					$groups[$i] = $group;
					$i++;
				}
			usort($groups, function ($a, $b) { return $a['sort_order'] - $b['sort_order']; });
			}
			
			$data['sections'][] = array(
				'sort_order'   => $section['sort_order'],
				'link'   => $section['link'],
				'link_new_window'   => $section['link_new_window'],
				'duration'   => $section['duration'],
				'slide_kenburn'   => $section['slide_kenburn'],
				'bg_color'   => $section['bg_color'],
				'thumb_image'   => $section['thumb_image'],
				'groups'  => $groups
			);
		}
		usort($data['sections'], function ($a, $b) { return $a['sort_order'] - $b['sort_order']; });
		}
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/module/basel_layerslider', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/basel_layerslider')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->request->post['name'] = "Unnamed Slideshow";
		}
				
		return !$this->error;
	}
}