<?php
class ControllerExtensionModuleBaselCarousel extends Controller {
	public function index($setting) {
		
		static $module = 0;
		
		// Load models
		$this->load->model('tool/image');
		$this->load->model('design/banner');
		
		// Block title
		$data['block_title'] = $setting['use_title'];
		$data['title_preline'] = false;
		$data['title'] = false;
		$data['title_subline'] = false;
		
		if (!empty($setting['title_pl'][$this->config->get('config_language_id')])) {
		$data['title_preline'] = html_entity_decode($setting['title_pl'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['title_m'][$this->config->get('config_language_id')])) {
		$data['title'] = html_entity_decode($setting['title_m'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		if (!empty($setting['title_b'][$this->config->get('config_language_id')])) {
		$data['title_subline'] = html_entity_decode($setting['title_b'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
		}
		
		// RTL support
		$data['direction'] = $this->language->get('direction');
		
		$data['contrast'] = $setting['contrast'];
		$data['columns'] = $setting['columns'];
		$data['carousel_a'] = $setting['carousel_a'];
		$data['carousel_b'] = $setting['carousel_b'];
		$data['rows'] = $setting['rows'];
		$data['use_margin'] = $setting['use_margin'];
		$data['margin'] = $setting['margin'];
		
		if (isset($setting['autoplay'])) {
			$data['autoplay'] = $setting['autoplay'];
		} else {
			$data['autoplay'] = false;
		}
			
		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$data['banners'][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height'])
				);
			}
		}

		$data['module'] = $module++;
		
		if ($this->config->get('theme_default_directory') == 'basel')
		return $this->load->view('extension/module/basel_carousel', $data);
	}
}