<?php
class ControllerExtensionBaselProductTabs extends Controller { 
	private $error = array();

	public function index() {
						
		$this->load->language('basel/product_tabs');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/basel/product_tabs');
				
		$this->getList();
	}

	public function add() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/product_tabs');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/basel/product_tabs');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_basel_product_tabs->addTab($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL')); 
		}

		$this->getForm();
	}

	public function edit() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/product_tabs');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/basel/product_tabs');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_basel_product_tabs->editTab($this->request->get['tab_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$this->load->language('basel/product_tabs');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/basel/product_tabs');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $tab_id) {
				$this->model_extension_basel_product_tabs->deleteTab($tab_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('extension/basel/product_tabs/add', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL');
		$data['delete'] = $this->url->link('extension/basel/product_tabs/delete', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL');
		
		$data['product_tabs'] = array();

		$filter_data = array(
			'filter_name'	=> $filter_name,
			'sort'  		=> $sort,
			'order' 		=> $order,
			'start' 		=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 		=> $this->config->get('config_limit_admin')
		);

		$tab_total = $this->model_extension_basel_product_tabs->getTotalProductTabs($filter_data);

		$results = $this->model_extension_basel_product_tabs->getProductTabs($filter_data);

		foreach ($results as $result) {
			$data['product_tabs'][] = array(
				'tab_id' 	  => $result['tab_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'status'	  => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'        => $this->url->link('extension/basel/product_tabs/edit', $token_prefix . '=' . $this->session->data[$token_prefix] . '&tab_id=' . $result['tab_id'] . $url, 'SSL'),
				'delete'      => $this->url->link('extension/basel/product_tabs/delete', $token_prefix . '=' . $this->session->data[$token_prefix] . '&tab_id=' . $result['tab_id'] . $url, 'SSL')
			);
		}

		$ocb_languages_list = array(
			'heading_title',
			'text_no_results',
			'text_confirm',
			'column_name',
			'column_sort_order',
			'column_status',
			'column_action',
			'entry_name',
			'button_filter',
			'button_add',
			'button_edit',
			'button_delete',
		);
		
		foreach ($ocb_languages_list as $lang_list) {
			$data[$lang_list] = $this->language->get($lang_list);
		}
		
		$data['token'] = $this->session->data[$token_prefix];
		
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . '&sort=name' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . '&sort=sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$pagination = new Pagination();
		$pagination->total = $tab_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($tab_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($tab_total - $this->config->get('config_limit_admin'))) ? $tab_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $tab_total, ceil($tab_total / $this->config->get('config_limit_admin')));
		
		$data['filter_name'] = $filter_name;
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/basel/product_tabs_list', $data));
	}

	protected function getForm() {
		
		if ((float)VERSION >= 3.0) {$token_prefix = 'user_token';} else {$token_prefix = 'token';}
		
		$ocb_languages_form = array(
			'heading_title',
			'text_no',
			'text_yes',
			'text_enabled',
			'text_disabled',
			'text_select_all',
			'text_unselect_all',
			'entry_name',
			'entry_global',
			'help_global',
			'entry_description',
			'entry_related',
			'entry_related_categories',
			'help_related',
			'entry_sort_order',
			'entry_status',
			'button_save',
			'button_cancel'
		);
		
		foreach ($ocb_languages_form as $lang_form) {
			$data[$lang_form] = $this->language->get($lang_form);
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}


		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $token_prefix . '=' . $this->session->data[$token_prefix], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL')
		);

		if (!isset($this->request->get['tab_id'])) {
			$data['action'] = $this->url->link('extension/basel/product_tabs/add', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('extension/basel/product_tabs/edit', $token_prefix . '=' . $this->session->data[$token_prefix] . '&tab_id=' . $this->request->get['tab_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('extension/basel/product_tabs', $token_prefix . '=' . $this->session->data[$token_prefix] . $url, 'SSL');

		if (isset($this->request->get['tab_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$tab_info = $this->model_extension_basel_product_tabs->getProductTab($this->request->get['tab_id']);
		}

		$data['token'] = $this->session->data[$token_prefix];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['tab_description'])) {
			$data['tab_description'] = $this->request->post['tab_description'];
		} elseif (isset($this->request->get['tab_id'])) {
			$data['tab_description'] = $this->model_extension_basel_product_tabs->getProductTabsDescriptions($this->request->get['tab_id']);
		} else {
			$data['tab_description'] = array();
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($tab_info)) {
			$data['sort_order'] = $tab_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($tab_info)) {
			$data['status'] = $tab_info['status'];
		} else {
			$data['status'] = 1;
		}
		
		if (isset($this->request->post['global'])) {
			$data['global'] = $this->request->post['global'];
		} elseif (!empty($tab_info)) {
			$data['global'] = $tab_info['global'];
		} else {
			$data['global'] = 0;
		}

		// Single products
		$this->load->model('catalog/product');
		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['tab_id'])) {
			$products = $this->model_extension_basel_product_tabs->getProductTabsProducts($this->request->get['tab_id']);
		} else {
			$products = array();
		}

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}
		
		// Single categories
		$this->load->model('catalog/category');
		if (isset($this->request->post['category_related'])) {
			$categories = $this->request->post['category_related'];
		} elseif (isset($this->request->get['tab_id'])) {
			$categories = $this->model_extension_basel_product_tabs->getProductTabsCategories($this->request->get['tab_id']);
		} else {
			$categories = array();
		}

		$data['category_relateds'] = array();

		foreach ($categories as $category_id) {
			$related_category_info = $this->model_catalog_category->getCategory($category_id);

			if ($related_category_info) {
				$data['category_relateds'][] = array(
					'category_id' => $related_category_info['category_id'],
					'name'       => $related_category_info['name']
				);
			}
		}
		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/basel/product_tabs_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/basel/product_tabs')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['tab_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/basel/product_tabs')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/basel/product_tabs');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_basel_product_tabs->getProductTabs($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'tab_id' 	=> $result['tab_id'],
					'name'   	=> strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}
		
		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	
	
}