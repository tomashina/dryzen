<?php
class ControllerExtensionBaselPositionCategoryTop extends Controller {
	public function index() {
		
		if ((float)VERSION >= 3.0) {
		$module_prefix = 'module_';
		$module_load = 'setting/module';
		$module_path = 'model_setting_module';
		} else {
		$module_prefix = '';
		$module_load = 'extension/module';
		$module_path = 'model_extension_module';
		}
		
		$this->load->model('design/layout');

		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = false;
		}

		$layout_id = 0;

		if ($route == 'product/category' && isset($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$path = explode('_', (string)$this->request->get['path']);

			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$this->load->model($module_load);

		$data['modules'] = array();

		$modules = $this->model_design_layout->getLayoutModules($layout_id, 'cat_top');

		foreach ($modules as $module) {
			$part = explode('.', $module['code']);

			if (isset($part[0]) && $this->config->get($module_prefix . $part[0] . '_status')) {
				$module_data = $this->load->controller('extension/module/' . $part[0]);

				if ($module_data) {
					$data['modules'][] = $module_data;
				}
			}

			if (isset($part[1])) {
				$setting_info = $this->$module_path->getModule($part[1]);

				if ($setting_info && $setting_info['status']) {
					$module_data = $this->load->controller('extension/module/' . $part[0], $setting_info);

					if ($module_data) {
						$data['modules'][] = $module_data;
					}
				}
			}
		}

		return $this->load->view('common/position_category_top', $data);
	}
}