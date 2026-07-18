<?php
class ControllerExtensionModuleBaselContent extends Controller {
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
		
		$this->load->language('extension/module/basel_content');
		
		$this->document->addStyle('view/javascript/basel/basel_content.css');
		$this->document->addStyle('view/javascript/basel/css/bootstrap-colorpicker.min.css');
		$this->document->addStyle('view/javascript/basel/icons_list/fonts/style.css');
		$this->document->addStyle('view/javascript/basel/css/jquery-ui.css');
		$this->document->addScript('view/javascript/basel/jquery-ui.js');
		$this->document->addScript('view/javascript/basel/js/bootstrap-colorpicker.min.js');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/language');
		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model($model_module_load);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->$model_module_path->addModule('basel_content', $this->request->post);
			} else {
				$this->$model_module_path->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save']) && $this->request->post['save'] == 'stay' && $this->request->get['module_id']) {
				$this->response->redirect($this->url->link('extension/module/basel_content', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true)); 
			} else {
				$this->response->redirect($this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true));
            }
		}
		
		$data['token'] = $this->session->data[$token_prefix];
		
		if (isset($this->request->get['module_id'])) {
			$data['save_and_stay'] = true; 
		} else {
			$data['save_and_stay'] = false;
		}
		
		// Language variables
		$text_array = array(
			'heading_title',
			'text_edit',
			'text_enabled',
			'text_disabled',
			'entry_name',
			'entry_status',
			'button_save',
			'button_save_stay',
			'button_cancel',
			'text_confirm',
			'text_tab_content',
			'text_tab_template',
			'text_module_settings',
			'text_block_settings',
			'text_use_block_title',
			'text_block_pre_line',
			'text_block_title',
			'text_block_sub_line',
			'text_block_margin',
			'text_margin',
			'text_top',
			'text_right',
			'text_bottom',
			'text_left',
			'text_full_width_background',
			'text_use_background_color',
			'text_background_color',
			'text_use_background_image',
			'text_background_image',
			'text_background_parallax',
			'text_background_position',
			'text_background_repeat',
			'text_use_background_video',
			'text_background_video',
			'text_use_css',
			'text_css',
			'text_content_settings',
			'text_full_width_content',
			'text_zero_margin',
			'text_equal_height',
			'text_content_columns',
			'text_column',
			'text_add_column',
			'text_column_width',
			'text_width_per_device',
			'text_type',
			'text_select_type',
			'text_title_html',
			'text_position',
			'text_html_content',
			'text_enable_editor',
			'text_disable_editor',
			'text_view_icons',
			'text_view_shortcodes',
			'text_view_overlays',
			'text_html',
			'text_banner',
			'text_testimonial',
			'text_title_testimonial',
			'text_limit',
			'text_tm_columns',
			'text_tm_style',
			'text_tm_style_plain',
			'text_tm_style_plain_light',
			'text_tm_style_block',
			'text_title_banner',
			'text_title_banner2',
			'text_link_target',
			'text_banner_overlay',
			'text_banner_help',
			'text_position_banner',
			'text_btn_add_banner',
			'text_remove_banner',
			'text_overlay_position',
			'text_template',
			'text_action',
			'text_preview',
			'text_import',
			'text_icons_list',
			'text_preview_template',
			'text_layout_example',
			'text_page',
			'text_block',
			'text_content',
			'text_columns_settings'
		);

		foreach ( $text_array as $key ) {
			$data[ $key ] = $this->language->get( $key );
		}
		
		// Messages
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

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/basel_content', $token_prefix . '=' . $this->session->data[$token_prefix], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/basel_content', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/basel_content', $token_prefix . '=' . $this->session->data[$token_prefix], true);
		} else {
			$data['action'] = $this->url->link('extension/module/basel_content', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true);
		}
		$data['cancel'] = $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->$model_module_path->getModule($this->request->get['module_id']);		
		}
		
		if (isset($this->request->get['import_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$content = file_get_contents(DIR_APPLICATION . 'view/javascript/basel/content_templates/' . $this->request->get['import_id'] . '/content.txt');	
			$module_info = json_decode($content, true);
		}
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		
		if (isset($this->request->post['bg_image'])) {
			$data['bg_image'] = $this->request->post['bg_image'];
		} elseif (!empty($module_info)) {
			$data['bg_image'] = $module_info['bg_image'];
		} else {
			$data['bg_image'] = '';
		}
			
		if (isset($this->request->post['bg_image']) && is_file(DIR_IMAGE . $this->request->post['bg_image'])) {
		$data['image'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (isset($module_info['bg_image']) && is_file(DIR_IMAGE . $module_info['bg_image'])) {
		$data['image'] = $this->model_tool_image->resize($module_info['bg_image'], 100, 100);
		} else {
		$data['image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		if (isset($this->request->post['b_setting'])) {
			$data['b_setting'] = $this->request->post['b_setting'];
		} elseif (!empty($module_info)) {
			$data['b_setting'] = $module_info['b_setting'];
		} else {
			$data['b_setting'] = array();
		}
		
		if (isset($this->request->post['c_setting'])) {
			$data['c_setting'] = $this->request->post['c_setting'];
		} elseif (!empty($module_info)) {
			$data['c_setting'] = $module_info['c_setting'];
		} else {
			$data['c_setting'] = array();
		}
		
		// Column widths //
		$data['column_widths'][] = array();
		$data['column_widths'] = array(
			"col-sm-1" => "1/12",
			"col-sm-2" => "2/12",
			"col-sm-3" => "3/12",
			"col-sm-4" => "4/12",
			"col-sm-5" => "5/12",
			"col-sm-6" => "6/12",
			"col-sm-7" => "7/12",
			"col-sm-8" => "8/12",
			"col-sm-9" => "9/12",
			"col-sm-10" => "10/12",
			"col-sm-11" => "11/12",
			"col-sm-12" => "12/12",
			"custom" => $this->language->get('text_set_width_per_device')
		);
		
		// Column widths //
		$data['sm_widths'][] = array();
		$data['sm_widths'] = array(
			"col-xs-1" => "1/12",
			"col-xs-2" => "2/12",
			"col-xs-3" => "3/12",
			"col-xs-4" => "4/12",
			"col-xs-5" => "5/12",
			"col-xs-6" => "6/12",
			"col-xs-7" => "7/12",
			"col-xs-8" => "8/12",
			"col-xs-9" => "9/12",
			"col-xs-10" => "10/12",
			"col-xs-11" => "11/12",
			"col-xs-12" => "12/12",
			"hidden-xs" => $this->language->get('text_hidden')
		);
		
		$data['md_widths'][] = array();
		$data['md_widths'] = array(
			"col-sm-1" => "1/12",
			"col-sm-2" => "2/12",
			"col-sm-3" => "3/12",
			"col-sm-4" => "4/12",
			"col-sm-5" => "5/12",
			"col-sm-6" => "6/12",
			"col-sm-7" => "7/12",
			"col-sm-8" => "8/12",
			"col-sm-9" => "9/12",
			"col-sm-10" => "10/12",
			"col-sm-11" => "11/12",
			"col-sm-12" => "12/12",
			"hidden-sm" => $this->language->get('text_hidden')
		);
		
		$data['lg_widths'][] = array();
		$data['lg_widths'] = array(
			"col-md-1" => "1/12",
			"col-md-2" => "2/12",
			"col-md-3" => "3/12",
			"col-md-4" => "4/12",
			"col-md-5" => "5/12",
			"col-md-6" => "6/12",
			"col-md-7" => "7/12",
			"col-md-8" => "8/12",
			"col-md-9" => "9/12",
			"col-md-10" => "10/12",
			"col-md-11" => "11/12",
			"col-md-12" => "12/12",
			"hidden-md hidden-lg" => $this->language->get('text_hidden')
		);
		
		$data['overlay_positions'][] = array();
		$data['overlay_positions'] = array(
			"vertical-top text-left" => "top left",
			"vertical-top text-center" => "top center",
			"vertical-top text-right" => "top right",
			"vertical-middle text-left" => "middle left",
			"vertical-middle text-center" => "middle center",
			"vertical-middle text-right" => "middle right",
			"vertical-bottom text-left" => "bottom left",
			"vertical-bottom text-center" => "bottom center",
			"vertical-bottom text-right" => "bottom right"
		);
		

		if (isset($this->request->post['columns'])) {
			$data['columns'] = $this->request->post['columns'];
		} elseif (!empty($module_info['columns'])) {
			$columns = $module_info['columns'];
		} else {
			$columns = array();
		}
		
		$data['columns'] = array();
		
		foreach ($columns as $column) {
			$data['columns'][] = array(
				'w'   => $column['w'],
				'w_sm'   => $column['w_sm'],
				'w_md'   => $column['w_md'],
				'w_lg'   => $column['w_lg'],
				'type'   => $column['type'],
				'data1'  => (isset($column['data1']) ? $column['data1'] : ''),
				'data2'  => (isset($column['data2']) ? $column['data2'] : ''),
				'data3'  => (isset($column['data3']) ? $column['data3'] : ''),
				'data4'  => (isset($column['data4']) ? $column['data4'] : ''),
				'data5'  => (isset($column['data5']) ? $column['data5'] : ''),
				'data6'  => (isset($column['data6']) ? $column['data6'] : ''),
				'data7'  => (isset($column['data7']) ? $column['data7'] : ''),
				'data8'  => (isset($column['data8']) ? $column['data8'] : ''),
				'image'  => (isset($column['data2']) ? $this->model_tool_image->resize($column['data2'], 100, 100) : ''),
				'image2' => (isset($column['data4']) ? $this->model_tool_image->resize($column['data4'], 100, 100) : '')			
			);
		}
		
		/* Pre-made templates */
		$data['templates'] = array();
		$templates = glob(DIR_APPLICATION . 'view/javascript/basel/content_templates/*');
		if ($templates) {
		
		$module_prefix = '';
		if (isset($this->request->get['module_id'])) {
		$module_prefix = '&module_id=' . $this->request->get['module_id'];
		}
		
		foreach ($templates as $template) {
			$data['templates'][] = array(
			'template_id' 	=> basename($template),
			'import_url' => $this->url->link('extension/module/basel_content', $token_prefix . '=' . $this->session->data[$token_prefix] . $module_prefix . '&import_id=' . basename($template), true),
			'name'      => file_get_contents(DIR_APPLICATION . 'view/javascript/basel/content_templates/' . basename($template) . '/name.txt')
			);
		}
		$sort_order = array();
		foreach ($data['templates'] as $key => $value) {
			$sort_order[$key] = $value['name'];
		}
		
		array_multisort($sort_order, SORT_ASC, $data['templates']);
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/basel_content', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/basel_content')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 2) || (utf8_strlen($this->request->post['name']) > 94)) {
			$this->request->post['name'] = 'Content block';
		}

		return !$this->error;
	}
}