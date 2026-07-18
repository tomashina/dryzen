<?php
class ControllerExtensionModuleBaselProducts extends Controller {
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
		
		$this->load->language('extension/module/basel_products');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model($model_module_load);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->$model_module_path->addModule('basel_products', $this->request->post);
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
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_limit_help'] = $this->language->get('entry_limit_help');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_tabs'] = $this->language->get('entry_tabs');
		$data['entry_tabs_help'] = $this->language->get('entry_tabs_help');
		$data['entry_tabstyle'] = $this->language->get('entry_tabstyle');
		$data['entry_tabstyle_help'] = $this->language->get('entry_tabstyle_help');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['groups_text'] = $this->language->get('groups_text');
		$data['groups_url'] = $this->url->link('extension/basel/productgroups', $token_prefix . '=' . $this->session->data[$token_prefix], true);
		
		$data['text_grid5'] = $this->language->get('text_grid5');
		$data['text_grid4'] = $this->language->get('text_grid4');
		$data['text_grid3'] = $this->language->get('text_grid3');
		$data['text_grid2'] = $this->language->get('text_grid2');
		$data['text_grid1'] = $this->language->get('text_grid1');
		$data['text_gridz'] = $this->language->get('text_gridz');
		$data['text_sm'] = $this->language->get('text_sm');
		$data['entry_carousel'] = $this->language->get('entry_carousel');
		$data['entry_carousel_a'] = $this->language->get('entry_carousel_a');
		$data['entry_carousel_b'] = $this->language->get('entry_carousel_b');
		$data['entry_columns'] = $this->language->get('entry_columns');
		$data['entry_rows'] = $this->language->get('entry_rows');
		$data['entry_contrast'] = $this->language->get('entry_contrast');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_title_h'] = $this->language->get('entry_title_h');
		$data['entry_countdown'] = $this->language->get('entry_countdown');
		$data['entry_countdown_h'] = $this->language->get('entry_countdown_h');
		$data['entry_link_title'] = $this->language->get('entry_link_title');
		$data['entry_link_title_h'] = $this->language->get('entry_link_title_h');
		$data['entry_link_href'] = $this->language->get('entry_link_href');
		
		$data['text_use_block_title'] = $this->language->get('text_use_block_title');
		$data['text_block_pre_line'] = $this->language->get('text_block_pre_line');
		$data['text_block_title'] = $this->language->get('text_block_title');
		$data['text_block_sub_line'] = $this->language->get('text_block_sub_line');
		$data['text_use_button'] = $this->language->get('text_use_button');
		$data['text_tabs_default'] = $this->language->get('text_tabs_default');
		$data['text_tabs_floating'] = $this->language->get('text_tabs_floating');
		$data['text_tabs_floating_lg'] = $this->language->get('text_tabs_floating_lg');
		
		$data['text_use_margin'] = $this->language->get('text_use_margin');
		$data['text_margin'] = $this->language->get('text_margin');
		

		
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
				'href' => $this->url->link('extension/module/basel_products', $token_prefix . '=' . $this->session->data[$token_prefix], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/basel_products', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true)
			);			
		}
		
		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/basel_products', $token_prefix . '=' . $this->session->data[$token_prefix], true);
		} else {
			$data['action'] = $this->url->link('extension/module/basel_products', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true);
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
		
		if (isset($this->request->post['contrast'])) {
			$data['contrast'] = $this->request->post['contrast'];
		} elseif (!empty($module_info)) {
			$data['contrast'] = $module_info['contrast'];
		} else {
			$data['contrast'] = '';
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
		
		if (isset($this->request->post['tabstyle'])) {
			$data['tabstyle'] = $this->request->post['tabstyle'];
		} elseif (!empty($module_info)) {
			$data['tabstyle'] = $module_info['tabstyle'];
		} else {
			$data['tabstyle'] = 0;
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
		
		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = '8';
		}
		
		if (isset($this->request->post['image_width'])) {
			$data['image_width'] = $this->request->post['image_width'];
		} elseif (!empty($module_info)) {
			$data['image_width'] = $module_info['image_width'];
		} else {
			$data['image_width'] = '262';
		}
		
		if (isset($this->request->post['image_height'])) {
			$data['image_height'] = $this->request->post['image_height'];
		} elseif (!empty($module_info)) {
			$data['image_height'] = $module_info['image_height'];
		} else {
			$data['image_height'] = '334';
		}
		
		if (isset($this->request->post['selected_tabs'])) {
			$data['selected_tabs'] = $this->request->post['selected_tabs'];
		} elseif (!empty($module_info['selected_tabs'])) {
			$data['selected_tabs'] = $module_info['selected_tabs'];
		} else {
		}
		
		if (isset($this->request->post['countdown'])) {
			$data['countdown'] = $this->request->post['countdown'];
		} elseif (!empty($module_info)) {
			$data['countdown'] = $module_info['countdown'];
		} else {
			$data['countdown'] = '';
		}
		
		if (isset($this->request->post['columns'])) {
			$data['columns'] = $this->request->post['columns'];
		} elseif (!empty($module_info)) {
			$data['columns'] = $module_info['columns'];
		} else {
			$data['columns'] = '4';
		}
		
		if (isset($this->request->post['carousel'])) {
			$data['carousel'] = $this->request->post['carousel'];
		} elseif (!empty($module_info)) {
			$data['carousel'] = $module_info['carousel'];
		} else {
			$data['carousel'] = '';
		}
		
		if (isset($this->request->post['rows'])) {
			$data['rows'] = $this->request->post['rows'];
		} elseif (!empty($module_info)) {
			$data['rows'] = $module_info['rows'];
		} else {
			$data['rows'] = "1";
		}
		
		if (isset($this->request->post['carousel_a'])) {
			$data['carousel_a'] = $this->request->post['carousel_a'];
		} elseif (!empty($module_info)) {
			$data['carousel_a'] = $module_info['carousel_a'];
		} else {
			$data['carousel_a'] = 1;
		}
		
		if (isset($this->request->post['carousel_b'])) {
			$data['carousel_b'] = $this->request->post['carousel_b'];
		} elseif (!empty($module_info)) {
			$data['carousel_b'] = $module_info['carousel_b'];
		} else {
			$data['carousel_b'] = 0;
		}
		
		if (isset($this->request->post['use_button'])) {
			$data['use_button'] = $this->request->post['use_button'];
		} elseif (!empty($module_info)) {
			$data['use_button'] = $module_info['use_button'];
		} else {
			$data['use_button'] = 0;
		}
		
		if (isset($this->request->post['link_title'])) {
			$data['link_title'] = $this->request->post['link_title'];
		} elseif (!empty($module_info)) {
			$data['link_title'] = $module_info['link_title'];
		} else {
			$data['link_title'] = array();
		}
		
		if (isset($this->request->post['link_href'])) {
			$data['link_href'] = $this->request->post['link_href'];
		} elseif (!empty($module_info)) {
			$data['link_href'] = $module_info['link_href'];
		} else {
			$data['link_href'] = '';
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
		
		//Tabs
		$data['tabs'] = array();

		if (isset($this->request->post['showintabs_tab'])) {
			$data['tabs'] = $this->request->post['showintabs_tab'];
		} elseif ($this->config->get('showintabs_tab')) { 
			$data['tabs'] = $this->config->get('showintabs_tab');
		}	

		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		
		ksort($data['tabs']);

		foreach( $data['tabs'] as $keyTab => $tab ){
			
			if(isset($data['tabs'][$keyTab]['title'][$this->config->get('config_language_id')]) && $data['tabs'][$keyTab]['title'][$this->config->get('config_language_id')] != ''){
				$data['tabs'][$keyTab]['tab_title'] = $data['tabs'][$keyTab]['title'][$this->config->get('config_language_id')];
			}else{
				$data['tabs'][$keyTab]['tab_title'] = $this->language->get('text_tab') . $keyTab . $this->language->get('text_notitle');
			}
			
			if(isset($tab['products'])){
				foreach ( $tab['products'] as $value ) {
					$product_info = $this->model_catalog_product->getProduct($value['product_id']);
					
					if ($product_info) {			
						$data['tabs'][$keyTab]['products'][$value['product_id']] = array(
							'product_id'    => $value['product_id'],
							'name'          => $product_info['name'],
							'model'         => $product_info['model'],
						);
					}
				}
			}
		}


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/basel_products', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/basel_products')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}
		return !$this->error;
	}
}