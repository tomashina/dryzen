<?php
class ControllerExtensionModuleBaselLayerslider extends Controller {
	public function index($setting) {
		static $module = 0;
		
		// Add required sources
		$this->document->addScript('catalog/view/theme/basel/js/masterslider.js');
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
			$base_url = HTTPS_SERVER;
		} else {
			$server = $this->config->get('config_url');
			$base_url = HTTP_SERVER;
		}

		// Load Google Fonts		
		if (isset($setting['g_fonts'])) {  
			$data['g_fonts'] = array();
			$import = '';
			foreach ($setting['g_fonts'] as $g_font) {
				$import .= $g_font['import'] . '%7C';
			}
			$this->document->addStyle('//fonts.googleapis.com/css?family=' . $import);
		}
		
		// General Settings
		$data['width'] = $setting['width'];
		$data['height'] = $setting['height'];
		$data['minheight'] = $setting['minheight'];
		$data['fullwidth'] = $setting['fullwidth'];
		$data['margin_bottom'] = $setting['margin_bottom'];
		$data['loop'] = $setting['loop'];
		$data['speed'] = $setting['speed'];
		$data['nav_buttons'] = $setting['nav_buttons'];
		$data['slide_transition'] = $setting['slide_transition'];
		$data['nav_timer_bar'] = $setting['nav_timer_bar'];
		$data['nav_bullets'] = $setting['nav_bullets'];

		// Slides & Layers
		if (isset($setting['sections'])) {        
            $data['sections'] = array();

            //$section_row = 0;
            function sortslides($a, $b) {return strcmp($a['sort_order'], $b['sort_order']);} usort($setting['sections'], 'sortslides');
            foreach($setting['sections'] as $section) {
				
                $groups = array();

                //$group_row = 0;

                if (isset($section['groups'])) {
                    foreach($section['groups'] as $group){
                       
					   if (isset($group['description'][$this->config->get('config_language_id')])){
                           $description = html_entity_decode($group['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
                       } else {
                           $description = false;
                       }
					   
					   if (isset($group['image'][$this->config->get('config_language_id')])){
                           $image = $base_url . 'image/' . $group['image'][$this->config->get('config_language_id')];
                       } else {
                           $image = false;
                       }
					   
					   if (isset($group['top'][$this->config->get('config_language_id')])){
                           $top = (($group['top'][$this->config->get('config_language_id')]) - ($setting['height'] / 2));
                       } else {
                           $top = 0;
                       }
					   
					   if (isset($group['left'][$this->config->get('config_language_id')])){
                           $left = $group['left'][$this->config->get('config_language_id')];
                       } else {
                           $left = 50;
                       }
					   
					   if (isset($group['font'])){ $font = $group['font']; } else { $font = ''; }
					   if (isset($group['fontweight'])){ $fontweight = $group['fontweight']; } else { $fontweight = ''; }
					   if (isset($group['fontsize'])){ $fontsize = $group['fontsize']; } else { $fontsize = ''; }
					   if (isset($group['color'])){ $color = $group['color']; } else { $color = ''; }
					   if (isset($group['minheight'])){ $minheight = $group['minheight']; } else { $minheight = ''; }
					   if (isset($group['bg'])){ $bg = $group['bg']; } else { $bg = ''; }
					   if (isset($group['padding'])){ $padding = $group['padding']; } else { $padding = ''; }
					   if (isset($group['radius'])){ $radius = $group['radius']; } else { $radius = ''; }
					   if (isset($group['customcss'])){ $customcss = $group['customcss']; } else { $customcss = ''; }
					   if (isset($group['start'])){ $start = $group['start']; } else { $start = '300'; }
					   if (isset($group['end'])){ $end = $group['end']; } else { $end = '5700'; }
					   if (isset($group['durationin'])){ $durationin = $group['durationin']; } else { $durationin = '500'; }
					   if (isset($group['durationout'])){ $durationout = $group['durationout']; } else { $durationout = '500'; }
					   if (isset($group['easingin'])){ $easingin = $group['easingin']; } else { $easingin = 'linear'; }
					   if (isset($group['easingout'])){ $easingout = $group['easingout']; } else { $easingout = 'linear'; }
					   if (isset($group['easingout'])){ $easingout = $group['easingout']; } else { $easingout = 'linear'; }
					   if (isset($group['transitionin'])){ $transitionin = $group['transitionin']; } else { $transitionin = ''; }
					   if (isset($group['transitionout'])){ $transitionout = $group['transitionout']; } else { $transitionout = ''; }
					   if (isset($group['button_class'])){ $button_class = $group['button_class']; } else { $button_class = 'ls_btn ls_btn_dark'; }
					   if (isset($group['button_target'])){ $button_target = $group['button_target']; } else { $button_target = false; }
					   if (isset($group['button_href'])){ $button_href = $group['button_href']; } else { $button_href = '#'; }
					   if (isset($group['p_index'])){ $p_index = $group['p_index']; } else { $p_index = '0'; }
					   if (isset($group['sort_order'])){ $sort_order = $group['sort_order']; } else { $sort_order = '0'; }
					   
                       //$group_row++;
						
                       //$groups[$group['sort_order']] = array(
					   $groups[] = array(
                           //'id'          		=> $group_row,
						   'type'        		=> $group['type'],
						   'description' 		=> $description,
						   'image'   	 		=> $image,
						   'left'   	 		=> $left,
						   'top'   		 		=> $top,
						   'font'   			=> $font,
						   'p_index'   			=> $p_index,
						   'sort_order'   		=> $sort_order,
						   'minheight'   		=> $minheight,
						   'fontweight'   		=> $fontweight,
						   'fontsize'   		=> $fontsize,
						   'color'   			=> $color,
						   'bg'   				=> $bg,
						   'padding'   			=> $padding,
						   'radius'   			=> $radius,
						   'customcss'   		=> $customcss, 
						   'start'   			=> $start,
						   'end'   				=> $end, 
						   'durationin'   		=> $durationin,
						   'durationout'   		=> $durationout,
						   'easingin'   		=> $easingin,
						   'easingout'   		=> $easingout, 
						   'transitionin'   	=> $transitionin,
						   'transitionout'   	=> $transitionout, 
						   'button_class'   	=> $button_class, 
						   'button_target'   	=> $button_target, 
						   'button_href'   		=> $button_href
                       );
                   }
                }
			
				usort($groups, function ($a, $b) { return $a['sort_order'] - $b['sort_order']; });

                //$section_row++;
								
				//$data['sections'][$section['sort_order']] = array(
				$data['sections'][] = array(
					'link'   => $section['link'],
					'link_new_window'   => $section['link_new_window'],
					'duration'   => $section['duration'],
					'slide_kenburn'   => $section['slide_kenburn'],
					'bg_color' => $section['bg_color'],
					'is_bg' => $section['thumb_image'],
					'thumb_image'   => $base_url . 'image/' . $section['thumb_image'],
					'sort_order'   => $section['sort_order'],
					'groups'  => $groups
				);
            }
			
			usort($data['sections'], function ($a, $b) { return $a['sort_order'] - $b['sort_order']; });
				
			//ksort($data['sections']);
			
			$data['module'] = $module++;
		
			if ($this->config->get('theme_default_directory') == 'basel')
			return $this->load->view('extension/module/basel_layerslider', $data);
		}
	}
}