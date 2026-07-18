<?php
class ControllerExtensionModuleBaselInstagram extends Controller {
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
		
		$this->load->language('extension/module/basel_instagram');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model($model_module_load);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->$model_module_path->addModule('basel_instagram', $this->request->post);
			} else {
				$this->$model_module_path->editModule($this->request->get['module_id'], $this->request->post);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true));
		}
		
		$this->load->model('localisation/language');
		
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_full_width'] = $this->language->get('entry_full_width');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_resolution'] = $this->language->get('entry_resolution');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['text_grid6'] = $this->language->get('text_grid6');
		$data['text_grid5'] = $this->language->get('text_grid5');
		$data['text_grid4'] = $this->language->get('text_grid4');
		$data['text_grid3'] = $this->language->get('text_grid3');
		$data['text_grid2'] = $this->language->get('text_grid2');
		$data['text_grid1'] = $this->language->get('text_grid1');
		$data['text_low'] = $this->language->get('text_low');
		$data['text_high'] = $this->language->get('text_high');
		$data['entry_columns'] = $this->language->get('entry_columns');
		$data['entry_columns_md'] = $this->language->get('entry_columns_md');
		$data['entry_columns_sm'] = $this->language->get('entry_columns_sm');
		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_title_h'] = $this->language->get('entry_title_h');
		$data['text_use_block_title'] = $this->language->get('text_use_block_title');
		$data['text_block_pre_line'] = $this->language->get('text_block_pre_line');
		$data['text_block_title'] = $this->language->get('text_block_title');
		$data['text_block_sub_line'] = $this->language->get('text_block_sub_line');
		$data['text_use_margin'] = $this->language->get('text_use_margin');
		$data['text_margin'] = $this->language->get('text_margin');
		$data['text_padding'] = $this->language->get('text_padding');
		$data['entry_inline_title'] = $this->language->get('entry_inline_title');
		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_access_token'] = $this->language->get('entry_access_token');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/basel_instagram', $token_prefix . '=' . $this->session->data[$token_prefix], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/basel_instagram', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true)
			);			
		}
		
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/basel_instagram', $token_prefix . '=' . $this->session->data[$token_prefix], true);
		} else {
			$data['action'] = $this->url->link('extension/module/basel_instagram', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true);
		}
		
		$data['cancel'] = $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true);
		
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->$model_module_path->getModule($this->request->get['module_id']);
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}
		
		if (isset($this->request->post['full_width'])) {
			$data['full_width'] = $this->request->post['full_width'];
		} elseif (!empty($module_info)) {
			$data['full_width'] = $module_info['full_width'];
		} else {
			$data['full_width'] = '';
		}
		
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}
		
		if (isset($this->request->post['use_title'])) {
			$data['use_title'] = $this->request->post['use_title'];
		} elseif (!empty($module_info)) {
			$data['use_title'] = $module_info['use_title'];
		} else {
			$data['use_title'] = 0;
		}
		
		if (isset($this->request->post['title_inline'])) {
			$data['title_inline'] = $this->request->post['title_inline'];
		} elseif (!empty($module_info)) {
			$data['title_inline'] = $module_info['title_inline'];
		} else {
			$data['title_inline'] = '';
		}
		
		if (isset($this->request->post['title_pl'])) {
			$data['title_pl'] = $this->request->post['title_pl'];
		} elseif (!empty($module_info)) {
			$data['title_pl'] = $module_info['title_pl'];
		} else {
			$data['title_pl'] = array();
		}
		
		if (isset($this->request->post['title_m'])) {
			$data['title_m'] = $this->request->post['title_m'];
		} elseif (!empty($module_info)) {
			$data['title_m'] = $module_info['title_m'];
		} else {
			$data['title_m'] = array();
		}
		
		if (isset($this->request->post['title_b'])) {
			$data['title_b'] = $this->request->post['title_b'];
		} elseif (!empty($module_info)) {
			$data['title_b'] = $module_info['title_b'];
		} else {
			$data['title_b'] = array();
		}
		
		if (isset($this->request->post['username'])) {
			$data['username'] = $this->request->post['username'];
		} elseif (!empty($module_info)) {
			$data['username'] = $module_info['username'];
		} else {
			$data['username'] = '';
		}
		
		if (isset($this->request->post['access_token'])) {
			$data['access_token'] = $this->request->post['access_token'];
		} elseif (!empty($module_info)) {
			$data['access_token'] = $module_info['access_token'];
		} else {
			$data['access_token'] = '';
		}
		
		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = '8';
		}
		
		if (isset($this->request->post['resolution'])) {
			$data['resolution'] = $this->request->post['resolution'];
		} elseif (!empty($module_info)) {
			$data['resolution'] = $module_info['resolution'];
		} else {
			$data['resolution'] = 'low_resolution';
		}
		
		if (isset($this->request->post['columns'])) {
			$data['columns'] = $this->request->post['columns'];
		} elseif (!empty($module_info)) {
			$data['columns'] = $module_info['columns'];
		} else {
			$data['columns'] = 'grid6';
		}
		
		if (isset($this->request->post['columns_md'])) {
			$data['columns_md'] = $this->request->post['columns_md'];
		} elseif (!empty($module_info)) {
			$data['columns_md'] = $module_info['columns_md'];
		} else {
			$data['columns_md'] = 'grid6';
		}
		
		if (isset($this->request->post['columns_sm'])) {
			$data['columns_sm'] = $this->request->post['columns_sm'];
		} elseif (!empty($module_info)) {
			$data['columns_sm'] = $module_info['columns_sm'];
		} else {
			$data['columns_sm'] = 'grid3';
		}
		
		if (isset($this->request->post['padding'])) {
			$data['padding'] = $this->request->post['padding'];
		} elseif (!empty($module_info)) {
			$data['padding'] = $module_info['padding'];
		} else {
			$data['padding'] = '10';
		}
		
		if (isset($this->request->post['use_margin'])) {
			$data['use_margin'] = $this->request->post['use_margin'];
		} elseif (!empty($module_info)) {
			$data['use_margin'] = $module_info['use_margin'];
		} else {
			$data['use_margin'] = 0;
		}
		
		if (isset($this->request->post['margin'])) {
			$data['margin'] = $this->request->post['margin'];
		} elseif (!empty($module_info)) {
			$data['margin'] = $module_info['margin'];
		} else {
			$data['margin'] = '60px';
		}
		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/basel_instagram', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/basel_instagram')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		return !$this->error;
	}
}