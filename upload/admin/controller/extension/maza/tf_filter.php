<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */

class ControllerExtensionMazaTfFilter extends Controller {
        private $error = array();
    
        public function index() {
		$this->load->language('extension/maza/tf_filter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter');

		$this->getList();
	}
        
        /**
         * Add filter
         */
        public function add() {
		$this->load->language('extension/maza/tf_filter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_tf_filter->addFilter($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
                        if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
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
                        
			$this->response->redirect($this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Edit filter
         */
	public function edit() {
		$this->load->language('extension/maza/tf_filter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_tf_filter->editFilter($this->request->get['filter_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Delete individual filter
         */
	public function delete() {
		$this->load->language('extension/maza/tf_filter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $filter_id) {
				$this->model_extension_maza_tf_filter->deleteFilter($filter_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        /**
         * copy individual filter
         */
	public function copy() {
		$this->load->language('extension/maza/tf_filter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter');
                $this->load->model('extension/maza/tf_filter/value');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $filter_id) {
				$new_filter_id = $this->model_extension_maza_tf_filter->copyFilter($filter_id);
                                $this->model_extension_maza_tf_filter_value->copyValues($filter_id, $new_filter_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
        
        
        /**
         * Get list of filter
         */
        protected function getList() {
                if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
                if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
                
                if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'sort_order';
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
                
                // Header
                $header_data = array();
                $header_data['title'] = $this->language->get('text_list');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array();
//                $header_data['menu'][] = array('name' => $this->language->get('tab_filter'), 'id' => 'tab-tf-filter', 'href' => false);
//                $header_data['menu'][] = array('name' => $this->language->get('tab_setting'), 'id' => 'tab-tf-setting', 'href' => $this->url->link('extension/maza/tf_filter/setting', 'user_token=' . $this->session->data['user_token'] . $url));
                
                $header_data['menu_active'] = 'tab-tf-filter';
                
                $header_data['buttons'][] = array( // Button sync
                    'id' => 'button-sync',
                    'name' => '',
                    'class' => 'btn-default',
                    'tooltip' => $this->language->get('button_sync'),
                    'icon' => 'fa-refresh',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array( // Button add
                    'id' => 'button-add',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_add'),
                    'icon' => 'fa-plus',
                    'href' => $this->url->link('extension/maza/tf_filter/add', 'user_token=' . $this->session->data['user_token'], true),
                    'target' => false,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array( // Button copy
                    'id' => 'button-copy',
                    'name' => '',
                    'tooltip' => $this->language->get('button_copy'),
                    'icon' => 'fa-copy',
                    'class' => 'btn-default',
                    'href' => FALSE,
                    'target' => FALSE,
                    'formaction' => $this->url->link('extension/maza/tf_filter/copy', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'form_target_id' => 'form-tf-filter',
                );
                $header_data['buttons'][] = array( // Button delete
                    'id' => 'button-delete',
                    'name' => '',
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'class' => 'btn-danger',
                    'href' => FALSE,
                    'target' => FALSE,
                    'formaction' => $this->url->link('extension/maza/tf_filter/delete', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'form_target_id' => 'form-tf-filter',
                );
                $header_data['buttons'][] = array( // Button cancel
                    'id' => 'button-cancel',
                    'name' => '',
                    'class' => 'btn-default',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true),
                    'target' => false,
                    'form_target_id' => false,
                );
                $header_data['form_target_id'] = 'form-tf-filter';
                
                $data['tf_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                // Filter list
                $url = '';
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
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
                
                $data['add'] = $this->url->link('extension/maza/tf_filter/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/maza/tf_filter/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
                
                $this->load->model('tool/image');
                
                $data['filters'] = array();

		$filter_data = array(
                        'filter_name' => $filter_name,
                        'filter_status' => $filter_status,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$filter_total = $this->model_extension_maza_tf_filter->getTotalFilters($filter_data);

		$results = $this->model_extension_maza_tf_filter->getFilters($filter_data);

		foreach ($results as $result) {
			$data['filters'][] = array(
				'filter_id' => $result['filter_id'],
				'name'        => $result['name'],
                                'status'      => $result['status']?$this->language->get('text_enabled'):$this->language->get('text_disabled'),
				'sort_order'  => $result['sort_order'],
                                'date_sync' => date($this->language->get('date_format_short'), strtotime($result['date_sync'])),
				'edit'        => $this->url->link('extension/maza/tf_filter/edit', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $result['filter_id'] . $url, true),
                                'value_edit'        => $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $result['filter_id'] . $url . '&page=1', true),
			);
		}

		if(isset($this->session->data['warning'])){
                        $data['warning'] = $this->session->data['warning'];
                        unset($this->session->data['warning']);
                }elseif(isset($this->error['warning'])){
                        $data['warning'] = $this->error['warning'];
                }
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
                
                // Sort order
		$url = '';
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);
                $data['sort_status'] = $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
                $data['sort_date_sync'] = $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . '&sort=date_sync' . $url, true);
                
                $data['sort'] = $sort;
		$data['order'] = $order;
                
                // Pagination
		$url = '';
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }
                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
                }

		$pagination = new Pagination();
		$pagination->total = $filter_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
                
                $data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;

		$data['results'] = sprintf($this->language->get('text_pagination'), ($filter_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($filter_total - $this->config->get('config_limit_admin'))) ? $filter_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $filter_total, ceil($filter_total / $this->config->get('config_limit_admin')));
                
                $data['default_url'] = '&user_token=' . $this->session->data['user_token'];

                $data['user_token'] = $this->session->data['user_token'];
                
                // Columns
                $column_left_data = array();
                $column_left_data['code'] = 'tf_filter';
                if ($this->user->hasPermission('access', 'extension/maza/tf_filter')) {
                    $column_left_data['menus'][] = array(
                            'id'       => 'tf-menu-filter',
                            'icon'     => 'fa-filter',
                            'name'     => $this->language->get('text_filter'),
                            'active'   => (strpos($this->request->get['route'], 'extension/maza/tf_filter') === 0)?TRUE: FALSE,
                            'href'     => $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true),
                            'children' => array()
                    );
                }
                
                if ($this->user->hasPermission('access', 'extension/maza/tf_filter/setting')) {
                    $column_left_data['menus'][] = array(
                            'id'       => 'tf-menu-setting',
                            'icon'     => 'fa-cog',
                            'name'     => $this->language->get('text_setting'),
                            'active'   => (strpos($this->request->get['route'], 'extension/maza/tf_filter/setting') === 0)?TRUE: FALSE,
                            'href'     => $this->url->link('extension/maza/tf_filter/setting', 'user_token=' . $this->session->data['user_token'] . $url, true),
                            'children' => array()
                    );
                }
                
                $this->config->set('template_engine', 'twig');
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['tf_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['tf_column_left'] = $this->load->controller('extension/maza/common/column_left/module', $column_left_data);
                
                $this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('extension/maza/tf_filter_list', $data));
        }
        
        /**
         * Form to add or edit Filter
         */
        protected function getForm() {
                $this->load->model('localisation/language');
                $this->load->model('setting/store');
                $this->load->model('tool/image');
                $this->load->model('catalog/attribute');
                $this->load->model('extension/maza/catalog/attribute_group');
                $this->load->model('catalog/option');
                $this->load->model('catalog/filter');
                $this->load->model('catalog/category');
                
                if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
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
                
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
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
                
                $data = array();
                
                // Header
                $header_data = array();
                $header_data['title'] = !isset($this->request->get['filter_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-tf-general', 'href' => false),
                    array('name' => $this->language->get('tab_layout'), 'id' => 'tab-tf-layout', 'href' => false),
                    array('name' => $this->language->get('tab_key'), 'id' => 'tab-tf-key', 'href' => false)
                );
                if(isset($this->request->get['filter_id'])){
                    $header_data['menu'][] = array('name' => $this->language->get('tab_value'), 'id' => 'tab-tf-value', 'href' => $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url . '&page=1', true));
                }
                
                $header_data['menu_active'] = 'tab-tf-general';
                $header_data['buttons'][] = array(
                    'id' => 'button-save',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => 'form-tf-filter',
                );
                $header_data['buttons'][] = array(
                    'id' => 'button-cancel',
                    'name' => '',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['form_target_id'] = 'form-tf-filter';
                
                $data['tf_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                if (!isset($this->request->get['filter_id'])) {
			$data['action'] = $this->url->link('extension/maza/tf_filter/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/tf_filter/edit', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true);
		}
                
                // Setting
                $setting = array();
                $setting['filter_description'] = array();
                $setting['sort_order'] = 10;
                $setting['filter_language_id'] = $this->config->get('config_language_id');
                $setting['status'] = true;
                $setting['filter_category'] = array();
                $setting['setting'] = array();
                $setting['setting']['collapse'] = 0;
                $setting['setting']['input_type'] = 'checkbox';
                $setting['setting']['list_type'] = 'text';
                
                // Value
                $setting['setting']['value_sync_status'] = 1;
                $setting['setting']['value_image_width'] = 30;
                $setting['setting']['value_image_height'] = 30;
                
                // Key
                $setting['setting']['key_attribute'] = array();
                $setting['setting']['key_option'] = array();
                $setting['setting']['key_filter_group'] = array();
                $setting['setting']['key_product_name'] = 0;
                $setting['setting']['key_product_description'] = 0;
                $setting['setting']['key_product_tags'] = 0;
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } elseif(isset($this->request->get['filter_id'])) {
                    $setting = array_merge($setting, $this->model_extension_maza_tf_filter->getFilter($this->request->get['filter_id']));
                    $setting['filter_description'] = $this->model_extension_maza_tf_filter->getFilterDescriptions($this->request->get['filter_id']);
                    $setting['filter_category'] = $this->model_extension_maza_tf_filter->getFilterCategories($this->request->get['filter_id']);
                }

                // Data
                $data = array_merge($data, $setting);
                
                // Show in category
                $data['filter_categories'] = array();
                
                if(isset($setting['filter_category'])){
                    foreach($setting['filter_category'] as $category_id){
                        $category_info = $this->model_catalog_category->getCategory($category_id);

                        if($category_info){
                            $data['filter_categories'][] = array(
                                'category_id' => $category_info['category_id'],
                                'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
                            );
                        }
                    }
                }
                
                // key attribute
                $data['key_attributes'] = array();
                
                if(isset($setting['setting']['key_attribute'])){
                    foreach($setting['setting']['key_attribute'] as $attribute_id){
                        $attribute_info = $this->model_catalog_attribute->getAttribute($attribute_id);

                        if($attribute_info){
                            $attribute_group_info = $this->model_extension_maza_catalog_attribute_group->getAttributeGroup($attribute_info['attribute_group_id']);
                            
                            $data['key_attributes'][] = array(
                                'attribute_id' => $attribute_info['attribute_id'],
                                'name' => $attribute_info['name'] . ' [' . $attribute_group_info['name'] . ']',
                            );
                        }
                    }
                }
                
                
                // key options
                $data['key_options'] = array();
                
                if(isset($setting['setting']['key_option'])){
                    foreach($setting['setting']['key_option'] as $option_id){
                        $option_info = $this->model_catalog_option->getOption($option_id);

                        if($option_info && in_array($option_info['type'], ['select', 'radio', 'checkbox'])){
                            $data['key_options'][] = array(
                                'option_id' => $option_info['option_id'],
                                'name' => $option_info['name'],
                            );
                        }
                    }
                }
                
                
                // key filter groups
                $data['key_filter_groups'] = array();
                
                if(isset($setting['setting']['key_filter_group'])){
                    foreach($setting['setting']['key_filter_group'] as $filter_group_id){
                        $filter_group_info = $this->model_catalog_filter->getFilterGroup($filter_group_id);

                        if($filter_group_info){
                            $data['key_filter_groups'][] = array(
                                'filter_group_id' => $filter_group_info['filter_group_id'],
                                'name' => $filter_group_info['name'],
                            );
                        }
                    }
                }
                
                
                $data['input_types'] = array(
                    array('code' => 'radio', 'text' => $this->language->get('text_radio')),
                    array('code' => 'checkbox', 'text' => $this->language->get('text_checkbox')),
                );
                $data['list_types'] = array(
                    array('code' => 'image', 'text' => $this->language->get('text_image')),
                    array('code' => 'text', 'text' => $this->language->get('text_text')),
                    array('code' => 'both', 'text' => $this->language->get('text_both'))
                );
                
                // Image
//                if (is_file(DIR_IMAGE . $setting['image'])) {
//			$data['thumb'] = $this->model_tool_image->resize($setting['image'], 100, 100);
//		} else {
//			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
//		}
//                $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
                
                
                // General
                $data['languages'] = $this->model_localisation_language->getLanguages();
                $data['user_token'] = $this->session->data['user_token'];
                
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}
                if(isset($this->error['warning'])){
                        $data['warning'] = $this->error['warning'];
                }
                foreach($this->error as $key => $val){
                    $data['err_' . $key] = $val;
                }
                
                // Columns
                $column_left_data = array();
                $column_left_data['code'] = 'tf_filter';
                if ($this->user->hasPermission('access', 'extension/maza/tf_filter')) {
                    $column_left_data['menus'][] = array(
                            'id'       => 'tf-menu-filter',
                            'icon'     => 'fa-filter',
                            'name'     => $this->language->get('text_filter'),
                            'active'   => (strpos($this->request->get['route'], 'extension/maza/tf_filter') === 0)?TRUE: FALSE,
                            'href'     => $this->url->link('extension/maza/tf_filter', 'user_token=' . $this->session->data['user_token'] . $url, true),
                            'children' => array()
                    );
                }
                
                if ($this->user->hasPermission('access', 'extension/maza/tf_filter/setting')) {
                    $column_left_data['menus'][] = array(
                            'id'       => 'tf-menu-setting',
                            'icon'     => 'fa-cog',
                            'name'     => $this->language->get('text_setting'),
                            'active'   => (strpos($this->request->get['route'], 'extension/maza/tf_filter/setting') === 0)?TRUE: FALSE,
                            'href'     => $this->url->link('extension/maza/tf_filter/setting', 'user_token=' . $this->session->data['user_token'] . $url, true),
                            'children' => array()
                    );
                }
                
                $this->config->set('template_engine', 'twig');
                $data['header'] = $this->load->controller('extension/maza/common/header/main');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['tf_footer'] = $this->load->controller('extension/maza/common/footer');
                $data['tf_column_left'] = $this->load->controller('extension/maza/common/column_left/module', $column_left_data);
                
                $this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('extension/maza/tf_filter_form', $data));
	}
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['filter_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 100)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}
                
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
        
        protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
        protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/maza/tf_filter');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_maza_tf_filter->getFilters($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'filter_id' => $result['filter_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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
