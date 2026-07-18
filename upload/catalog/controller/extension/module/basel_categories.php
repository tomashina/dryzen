<?php
class ControllerExtensionModuleBaselCategories extends Controller {
	public function index($setting) {
		
		static $module = 0;
		
		// Load models
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->language('basel/basel_theme');
		
		$data['basel_text_view_products'] = $this->language->get('basel_text_view_products');
		
		// RTL support
		$data['direction'] = $this->language->get('direction');
		
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
		
		$data['contrast'] = $setting['contrast'];
		$data['columns'] = $setting['columns'];
		$data['carousel'] = $setting['carousel'];
		$data['carousel_a'] = $setting['carousel_a'];
		$data['carousel_b'] = $setting['carousel_b'];
		$data['rows'] = $setting['rows'];
		$data['use_margin'] = $setting['use_margin'];
		$data['margin'] = $setting['margin'];
		$data['img_width'] = $setting['image_width'];
		$data['view_subs'] = $setting['subs'];
		$data['count'] = $setting['count'];
	
		$data['categories'] = array();
		
		if (!empty($setting['category'])) {
			
		  foreach ($setting['category'] as $category) {
			
			$category_info = $this->model_catalog_category->getCategory($category);
			
			// Childrens
			$childrens = $this->model_catalog_category->getCategories($category);
			
			$children = array_slice($childrens, 0, $setting['limit']);
			
			$children_data = array();
			foreach($children as $child) {
				$filter_data_child = array('filter_category_id' => $child['category_id'], 'filter_sub_category' => true);
				$children_data[] = array(
					'category_id' => $child['category_id'],
					'name' => $child['name'] . ($setting['count'] ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data_child) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $category_info['category_id'] . '_' . $child['category_id'])
				);
			}
			
			// Top Levels
			$filter_data_top = array('filter_category_id' => $category_info['category_id'], 'filter_sub_category' => true);
			if ($category_info['image']) {
				$image = $this->model_tool_image->resize($category_info['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $setting['image_width'], $setting['image_height']);
			}
			$data['categories'][] = array(
				'category_id' => $category_info['category_id'],
				'thumb'       => $image,
				'name'        => $category_info['name'] . ($setting['count'] && $setting['subs'] ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data_top) . ')' : ''),
				'products'    => $this->model_catalog_product->getTotalProducts($filter_data_top) . $this->language->get('basel_text_products'),
				'href'        => $this->url->link('product/category', 'path=' . $category_info['category_id']),
				'children'    => $children_data
			);
		  }
		}

		$data['module'] = $module++;
		
		if ($this->config->get('theme_default_directory') == 'basel')
		return $this->load->view('extension/module/basel_categories', $data);
	}
}