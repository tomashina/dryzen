<?php
class ControllerExtensionModuleBaselContent extends Controller {
	public function index($setting) {
		static $module = 1;
		$moduleId = $module;
		$is_newsletter_signup = (!empty($setting['module_id']) && (int)$setting['module_id'] === 48) || (!empty($setting['name']) && $setting['name'] === 'Newsletter Signup');
		
		$data['module'] = $module;
		$data['widget_module'] = $is_newsletter_signup ? 48 : (!empty($setting['module_id']) ? (int)$setting['module_id'] : $module);
		$data['newsletter_consent_mode'] = $is_newsletter_signup;
		
		$this->load->language('basel/basel_theme');
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
		// Language variables
		$data['basel_subscribe_email'] = $this->language->get('basel_subscribe_email');
		$data['basel_subscribe_btn'] = $this->language->get('basel_subscribe_btn');
		$data['basel_subscribe_consent_label'] = $this->language->get('basel_subscribe_consent_label');
		$data['basel_subscribe_consent_error'] = $this->language->get('basel_subscribe_consent_error');
		$data['basel_unsubscribe_btn'] = $this->language->get('basel_unsubscribe_btn');
		$data['basel_text_name'] = $this->language->get('basel_text_name');
		$data['basel_text_email'] = $this->language->get('basel_text_email');
		$data['basel_text_message'] = $this->language->get('basel_text_message');
		$data['basel_text_captcha'] = $this->language->get('basel_text_captcha');
		$data['basel_text_submit'] = $this->language->get('basel_text_submit');

		$current_language_code = '';

		if (!empty($this->session->data['language'])) {
			$current_language_code = strtolower($this->session->data['language']);
		} elseif ($this->config->get('config_language')) {
			$current_language_code = strtolower($this->config->get('config_language'));
		}

		if (strpos($current_language_code, 'hr') === 0) {
			if (
				empty($data['basel_subscribe_consent_label']) ||
				$data['basel_subscribe_consent_label'] === 'I agree to receive the newsletter and to the storage of my email address'
			) {
				$data['basel_subscribe_consent_label'] = 'Pristajem na primanje newslettera i pohranu svoje e-mail adrese';
			}

			if (
				empty($data['basel_subscribe_consent_error']) ||
				$data['basel_subscribe_consent_error'] === 'Please confirm newsletter consent.'
			) {
				$data['basel_subscribe_consent_error'] = 'Potvrdite privolu za primanje newslettera.';
			}
		}
		
		// Google map widget
		$data['basel_map_lat'] = $this->config->get('basel_map_lat');
		$data['basel_map_lon'] = $this->config->get('basel_map_lon');
		
		// Video background
		if ($setting['b_setting']['block_bgv']) {
			$this->document->addScript('catalog/view/theme/basel/js/jquery.tuber.js');
		}
		
		// Equal Height
		if ($setting['c_setting']['eh']) {
			$this->document->addScript('catalog/view/theme/basel/js/jquery.matchHeight.min.js');
		}
		
		// RTL support
		$data['direction'] = $this->language->get('direction');
				
		// Block
		$data['block_title'] = $setting['b_setting']['title'];
		$data['title_preline'] = false;
		$data['title'] = false;
		$data['title_subline'] = false;
		
		if (!empty($setting['b_setting']['title_pl'][$this->config->get('config_language_id')])) {
		$data['title_preline'] = html_entity_decode($setting['b_setting']['title_pl'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['b_setting']['title_m'][$this->config->get('config_language_id')])) {
		$data['title'] = html_entity_decode($setting['b_setting']['title_m'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['b_setting']['title_b'][$this->config->get('config_language_id')])) {
		$data['title_subline'] = html_entity_decode($setting['b_setting']['title_b'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		
		$data['module_margins'] = '';
		
		if ($setting['b_setting']['custom_m']) {
		$data['module_margins'] .= "margin-top:" . $setting['b_setting']['mt'] . "px;";
		$data['module_margins'] .= "margin-right:" . $setting['b_setting']['mr'] . "px;";
		$data['module_margins'] .= "margin-bottom:" . $setting['b_setting']['mb'] . "px;";
		$data['module_margins'] .= "margin-left:" . $setting['b_setting']['ml'] . "px;";
		}
		
		$data['block_style'] = '';
		
		if ($setting['b_setting']['block_bg']) {
		$data['block_style'] .= "background-color:" . $setting['b_setting']['bg_color'] . ";";
		}
		
		if ($setting['b_setting']['block_css']) {
		$data['block_style'] .= $setting['b_setting']['css'];
		}
		
		if ($setting['b_setting']['block_bgi']) {
		$data['block_style'] .= "background-image: url(" . $server . 'image/' . $setting['bg_image'] . ");";
		$data['block_style'] .= "background-position:" . $setting['b_setting']['bg_pos'] . ";";
		$data['block_style'] .= "background-repeat:" . $setting['b_setting']['bg_repeat'] . ";";
			if ($setting['b_setting']['bg_par']) {
			$data['block_style'] .= "background-attachment:fixed;";
			$this->document->addScript('catalog/view/theme/basel/js/parallax.min.js');
			}
		}
		
		$data['para_status'] = $setting['b_setting']['bg_par'];		
		$data['block_full_width'] = $setting['b_setting']['fw'];
		
		// Content
		$data['content_style'] = '';
		if ($setting['c_setting']['block_css']) {
		$data['content_style'] .= $setting['c_setting']['css'];
		}

		$data['content_full_width'] = $setting['c_setting']['fw'];
		$data['content_no_margin'] = $setting['c_setting']['nm'];
		$data['equal_height'] = $setting['c_setting']['eh'];
		$data['bg_video'] = $setting['b_setting']['block_bgv'];
		$data['video_id'] = $setting['b_setting']['bg_video'];
		$data['bg_img'] = $setting['b_setting']['block_bgi'];
		
		// Columns
		if (isset($setting['columns'])) {
			
			$data['columns'] = $setting['columns'];
            $data['columns'] = array();
            
            foreach($setting['columns'] as $column) {
                $this->load->model('tool/image');
				
				if ($column['type'] != "testimonial") {
					
					$this->load->model('extension/basel/testimonial');
					$results = $this->model_extension_basel_testimonial->getTestimonials($column['data1']);
					foreach ($results as $result) {
					
					if ($result['image']) {
						$image = $server . 'image/' . $result['image'];
					} else {
						$image = '';
					}
		
					$data['testimonials'][] = array(
						'description'	=> $result['description'],
						'name'		=> $result['name'],
						'image'		=> $image,
						'org'		=> $result['org']
					);
				  }
                }
				
                if ($column['w'] == "custom") {
					$column_class = $column['w_sm'] . ' ' . $column['w_md'] . ' ' . $column['w_lg'];
                } else {
                    $column_class = $column['w'];
                }
				
				if (isset($column['data1'][$this->config->get('config_language_id')])){
					
				if (strpos($column['data1'][$this->config->get('config_language_id')], '[map]') !== false) {
				$this->document->addScript('https://maps.google.com/maps/api/js?sensor=false&libraries=geometry&v=3.24&key=' . $this->config->get('basel_map_api') . '');
				$this->document->addScript('catalog/view/theme/basel/js/maplace.min.js');
				}

				$find = array(
				'[subscribe_field]', 
				'[unsubscribe_btn]',
				'[contact_form]', 
				'[map]'
				);
				
				$replace = array(
				$this->load->view('extension/module/content_widgets/subscribe_field', $data), 
				$this->load->view('extension/module/content_widgets/unsubscribe_btn', $data), 
				$this->load->view('extension/module/content_widgets/contact_form', $data), 
				$this->load->view('extension/module/content_widgets/map', $data)
				);

				$data1 = html_entity_decode(str_replace($find,$replace,$column['data1'][$this->config->get('config_language_id')]), ENT_QUOTES, 'UTF-8');
					
                } else {
                    $data1 = false;
                }
				
				if (isset($column['data2'])){
					$data2 = $server . 'image/' . $column['data2'];
                } else {
                    $data2 = false;
                }
				
				if (isset($column['data3'][$this->config->get('config_language_id')])){
					$data3 = html_entity_decode($column['data3'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');	
                } else {
                    $data3 = false;
                }
				
				if (isset($column['data4'])){
					$data4 = $server . 'image/' . $column['data4'];
                } else {
                    $data4 = false;
                }
				
				if (isset($column['data5'])){
					$data5 = $column['data5'];
                } else {
                    $data5 = false;
                }
				
				if (isset($column['data6'])){
					$data6 = $column['data6'];
                } else {
                    $data6 = false;
                }
				
				if (isset($column['data7'])){
					$data7 = $column['data7'];
                } else {
                    $data7 = false;
                }
				
				if (isset($column['data8'])){
					$data8 = $column['data8'];
                } else {
                    $data8 = false;
                }
				
                $data['columns'][] = array(
					'column_class' => $column_class,
					'type' => $column['type'],
					'data1' => $data1,
					'data2' => $data2,
					'data3' => $data3,
					'data4' => $data4,
					'data5' => $data5,
					'data6' => $data6,
					'data7' => $data7,
					'data8' => $data8
                );
            }
		}
		
		$data['module'] = $module++;
		$this->addExternalModuleStyles(
			$moduleId,
			$data['module_margins'],
			$data['block_style'],
			$data['content_style']
		);
		
		if ($this->config->get('theme_default_directory') == 'basel')
		return $this->load->view('extension/module/basel_content', $data);
	}

	private function addExternalModuleStyles($moduleId, $moduleMargins, $blockStyle, $contentStyle) {
		$css = '';
		$selector = '#mod' . (int) $moduleId;

		if (trim($moduleMargins) !== '') {
			$css .= $selector . '{' . trim($moduleMargins) . "}\n";
		}

		if (trim($blockStyle) !== '') {
			$css .= $selector . ' > .cm_block_wrapper > .cm_block{' . trim($blockStyle) . "}\n";
		}

		if (trim($contentStyle) !== '') {
			$css .= $selector . ' .cm_content{' . trim($contentStyle) . "}\n";
		}

		if ($css === '') {
			return;
		}

		$relativeDirectory = 'catalog/view/theme/basel/stylesheet/generated/';
		$directory = DIR_APPLICATION . 'view/theme/basel/stylesheet/generated/';
		$filename = 'dryzen-content-' . substr(hash('sha256', $css), 0, 16) . '.css';

		if (!is_dir($directory)) {
			mkdir($directory, 0755, true);
		}

		if (!is_file($directory . $filename)) {
			file_put_contents($directory . $filename, $css, LOCK_EX);
		}

		$this->document->addStyle($relativeDirectory . $filename);
	}
}
