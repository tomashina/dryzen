<?php
/**
 * @package		MazaTheme
 * @author		Jay padaliya
 * @copyright           Copyright (c) 2017, TemplateMaza
 * @license		One domain license
 * @link		http://www.templatemaza.com
 */

class ControllerExtensionMazaTfFilterValue extends Controller {
        private $error = array();
    
        public function index() {
		$this->load->language('extension/maza/tf_filter/value');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter/value');
                
		$this->getList();
	}
        
        /**
         * Add value
         */
        public function add() {
		$this->load->language('extension/maza/tf_filter/value');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter/value');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_tf_filter_value->addValue($this->request->get['filter_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
                        
                        if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['filter_regex'])){
                                $url .= '&filter_regex=' . $this->request->get['filter_regex'];
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
                        
			$this->response->redirect($this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Edit value
         */
	public function edit() {
		$this->load->language('extension/maza/tf_filter/value');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter/value');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_maza_tf_filter_value->editValue($this->request->get['value_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['filter_regex'])){
                                $url .= '&filter_regex=' . $this->request->get['filter_regex'];
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

			$this->response->redirect($this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true));
		}

		$this->getForm();
	}
        
        /**
         * Delete individual value
         */
	public function delete() {
		$this->load->language('extension/maza/tf_filter/value');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter/value');

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $value_id) {
				$this->model_extension_maza_tf_filter_value->deleteValue($value_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['filter_regex'])){
                                $url .= '&filter_regex=' . $this->request->get['filter_regex'];
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

			$this->response->redirect($this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true));
		}

		$this->getList();
	}
        
        /**
         * Change status of values
         */
	public function status() {
		$this->load->language('extension/maza/tf_filter/value');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/maza/tf_filter/value');

		if (isset($this->request->get['status']) && isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $value_id) {
                            if($this->request->get['status']){
                                $this->model_extension_maza_tf_filter_value->enableValue($value_id);
                            } else {
                                $this->model_extension_maza_tf_filter_value->disableValue($value_id);
                            }
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if(isset($this->request->get['filter_name'])){
                                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                        }
                        if(isset($this->request->get['filter_status'])){
                                $url .= '&filter_status=' . $this->request->get['filter_status'];
                        }
                        if(isset($this->request->get['filter_regex'])){
                                $url .= '&filter_regex=' . $this->request->get['filter_regex'];
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

			$this->response->redirect($this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true));
		}

		$this->getList();
	}
        
        /**
         * Export values
         */
	public function export() {
                $this->load->language('extension/maza/tf_filter/value');
                
                $this->document->setTitle($this->language->get('heading_title'));
                
                $this->load->model('extension/maza/tf_filter/value');

		if (isset($this->request->get['filter_id']) && $this->validate()) {
                        header('Content-Type: text/csv');
                        header('Content-Disposition: attachment; filename="filter_value_' . (int)$this->request->get['filter_id'] . '.csv"');

                        $filter_values = $this->model_extension_maza_tf_filter_value->getValues($this->request->get['filter_id']);

                        $csv = fopen('php://output', 'wb');
                        fputcsv($csv, array('name', 'sort_order', 'status', 'image', 'regex', 'value'), ',');
                        foreach ($filter_values as $filter_value) {
                            $line = array(
                                $filter_value['name'],
                                $filter_value['sort_order'],
                                $filter_value['status'],
                                $filter_value['image'],
                                $filter_value['regex'],
                                $filter_value['value'],
                            );

                            fputcsv($csv, $line, ',');
                        }
                        fclose($csv);
                } else {
                    $this->getList();
                }
	}
        
        /**
         * Import CSV file
         */
        public function import(){
                $this->load->language('extension/maza/tf_filter/value');

                $json = array();

                // Check user has permission
                if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter/value')) {
                        $json['error'] = $this->language->get('error_permission');
                } else {
                    if (isset($this->request->files['file']['name'])) {
                            if (substr($this->request->files['file']['name'], -3) != 'csv') {
                                    $json['error'] = $this->language->get('error_filetype');
                            }

                            if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                            }
                    } else {
                            $json['error'] = $this->language->get('error_upload');
                    }
                }

                if (!$json) {
                        $file = DIR_UPLOAD . token(10) . '.tmp';

                        move_uploaded_file($this->request->files['file']['tmp_name'], $file);

                        if (is_file($file)) {
                                $this->load->model('extension/maza/tf_filter/value');

                                $this->model_extension_maza_tf_filter_value->import($this->request->get['filter_id'], $file, $this->request->post['merge']);

                                $json['success'] = $this->language->get('text_success_import');		
                        } else {
                                $json['error'] = $this->language->get('error_file');
                        }
                }

                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
        }
        
        
        /**
         * Get list of value
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
                if (isset($this->request->get['filter_regex'])) {
			$filter_regex = $this->request->get['filter_regex'];
		} else {
			$filter_regex = null;
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
                if(isset($this->request->get['filter_name'])){
                        $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
                }
                if(isset($this->request->get['filter_status'])){
                        $url .= '&filter_status=' . $this->request->get['filter_status'];
                }
                if(isset($this->request->get['filter_regex'])){
                        $url .= '&filter_regex=' . $this->request->get['filter_regex'];
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
                
                // Header
                $header_data = array();
                $header_data['title'] = $this->language->get('text_list');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-tf-general', 'href' => $this->url->link('extension/maza/tf_filter/edit', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true)),
                    array('name' => $this->language->get('tab_key'), 'id' => 'tab-tf-key', 'href' => $this->url->link('extension/maza/tf_filter/edit', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true)),
                    array('name' => $this->language->get('tab_value'), 'id' => 'tab-tf-value', 'href' => false)
                );
                
                
                $header_data['menu_active'] = 'tab-tf-value';
                
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
                    'href' => $this->url->link('extension/maza/tf_filter/value/add', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true),
                    'target' => false,
                    'form_target_id' => false,
                );
                $header_data['buttons'][] = array( // Button enable
                    'id' => 'button-enable',
                    'name' => '',
                    'tooltip' => $this->language->get('button_enable'),
                    'icon' => 'fa-thumbs-up',
                    'class' => 'btn-success',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-tf-value',
                    'formaction' => $this->url->link('extension/maza/tf_filter/value/status', 'user_token=' . $this->session->data['user_token'] . '&status=1&filter_id=' . $this->request->get['filter_id'] . $url, true)
                );
                $header_data['buttons'][] = array( // Button enable
                    'id' => 'button-disable',
                    'name' => '',
                    'tooltip' => $this->language->get('button_disable'),
                    'icon' => 'fa-thumbs-down',
                    'class' => 'btn-danger',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-tf-value',
                    'formaction' => $this->url->link('extension/maza/tf_filter/value/status', 'user_token=' . $this->session->data['user_token'] . '&status=0&filter_id=' . $this->request->get['filter_id'] . $url, true)
                );
                $header_data['buttons'][] = array( // Button delete
                    'id' => 'button-delete',
                    'name' => '',
                    'tooltip' => $this->language->get('button_delete'),
                    'icon' => 'fa-trash',
                    'class' => 'btn-danger',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => 'form-tf-value',
                    'formaction' => $this->url->link('extension/maza/tf_filter/value/delete', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true)
                );
                $header_data['buttons'][] = array( // Button export
                    'id' => 'button-export',
                    'name' => '',
                    'tooltip' => $this->language->get('button_export'),
                    'icon' => 'fa-download',
                    'class' => 'btn-info',
                    'href' => $this->url->link('extension/maza/tf_filter/value/export', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => FALSE,
                );
                $header_data['buttons'][] = array( // Button export
                    'id' => 'button-import',
                    'name' => '',
                    'tooltip' => $this->language->get('button_import'),
                    'icon' => 'fa-upload',
                    'class' => 'btn-info',
                    'href' => FALSE,
                    'target' => FALSE,
                    'form_target_id' => FALSE,
                );
                $header_data['form_target_id'] = 'form-tf-value';
                
                $data['tf_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                $this->load->model('tool/image');
                
                // Value list
                $data['values'] = array();

		$value_data = array(
                        'filter_name' => $filter_name,
                        'filter_status' => $filter_status,
                        'filter_regex' => $filter_regex,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$value_total = $this->model_extension_maza_tf_filter_value->getTotalValues($this->request->get['filter_id'], $value_data);

		$results = $this->model_extension_maza_tf_filter_value->getValues($this->request->get['filter_id'], $value_data);

		foreach ($results as $result) {
                        if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
                        
			$data['values'][] = array(
				'value_id' => $result['value_id'],
				'name'        => $result['name'],
                                'total_product' => $result['total_product'],
                                'image'       => $image,
                                'status'      => $result['status']?$this->language->get('text_enabled'):$this->language->get('text_disabled'),
                                'type'        => $result['regex']?$this->language->get('text_regex'):$this->language->get('text_text'),
				'sort_order'  => $result['sort_order'],
				'edit'        => $this->url->link('extension/maza/tf_filter/value/edit', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . '&value_id=' . $result['value_id'] . $url, true),
			);
		}

		if(isset($this->session->data['warning'])){
                        $data['warning'] = $this->session->data['warning'];
                        unset($this->session->data['warning']);
                } elseif(isset($this->error['warning'])){
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
                if(isset($this->request->get['filter_regex'])){
                        $url .= '&filter_regex=' . $this->request->get['filter_regex'];
                }
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . '&sort=sort_order' . $url, true);
                $data['sort_total_product'] = $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . '&sort=total_product' . $url, true);
                $data['sort_regex'] = $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . '&sort=regex' . $url, true);
                $data['sort_status'] = $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . '&sort=status' . $url, true);
                
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
                if(isset($this->request->get['filter_regex'])){
                        $url .= '&filter_regex=' . $this->request->get['filter_regex'];
                }
                if (isset($this->request->get['sort'])) {
                        $url .= '&sort=' . $this->request->get['sort'];
                }
                if (isset($this->request->get['order'])) {
                        $url .= '&order=' . $this->request->get['order'];
                }

		$pagination = new Pagination();
		$pagination->total = $value_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
                
                $data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
                $data['filter_regex'] = $filter_regex;

		$data['results'] = sprintf($this->language->get('text_pagination'), ($value_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($value_total - $this->config->get('config_limit_admin'))) ? $value_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $value_total, ceil($value_total / $this->config->get('config_limit_admin')));
                
                $data['default_url'] = '&user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'];
                

                $data['user_token'] = $this->session->data['user_token'];
                $data['filter_id'] = $this->request->get['filter_id'];
                
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
		$this->response->setOutput($this->load->view('extension/maza/tf_filter/value_list', $data));
        }
        
        /**
         * Form to add or edit Value
         */
        protected function getForm() {
                $this->load->model('localisation/language');
                $this->load->model('setting/store');
                $this->load->model('tool/image');
//                $this->load->model('catalog/attribute');
                $this->load->model('extension/maza/tf_filter');
//                $this->load->model('catalog/option');
//                $this->load->model('catalog/value');
                
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
                if(isset($this->request->get['filter_regex'])){
                        $url .= '&filter_regex=' . $this->request->get['filter_regex'];
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
                $header_data['title'] = !isset($this->request->get['value_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
                $header_data['theme_select'] = $header_data['skin_select'] = false;
                $header_data['menu'] = array(
                    array('name' => $this->language->get('tab_general'), 'id' => 'tab-tf-general', 'href' => $this->url->link('extension/maza/tf_filter/edit', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true)),
                    array('name' => $this->language->get('tab_key'), 'id' => 'tab-tf-key', 'href' => $this->url->link('extension/maza/tf_filter/edit', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true)),
                    array('name' => $this->language->get('tab_value'), 'id' => 'tab-tf-value', 'href' => false)
                );
                
                $header_data['menu_active'] = 'tab-tf-value';
                $header_data['buttons'][] = array( // Button save
                    'id' => 'button-save',
                    'name' => '',
                    'class' => 'btn-primary',
                    'tooltip' => $this->language->get('button_save'),
                    'icon' => 'fa-save',
                    'href' => false,
                    'target' => false,
                    'form_target_id' => 'form-tf-value',
                );
                $header_data['buttons'][] = array( // Button delete
                    'id' => 'button-cancel',
                    'name' => '',
                    'tooltip' => $this->language->get('button_cancel'),
                    'icon' => 'fa-reply',
                    'class' => 'btn-default',
                    'href' => $this->url->link('extension/maza/tf_filter/value', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true),
                    'target' => FALSE,
                    'form_target_id' => false,
                );
                $header_data['form_target_id'] = 'form-tf-value';
                
                $data['tf_header'] = $this->load->controller('extension/maza/common/header', $header_data);
                
                if (!isset($this->request->get['value_id'])) {
			$data['action'] = $this->url->link('extension/maza/tf_filter/value/add', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/maza/tf_filter/value/edit', 'user_token=' . $this->session->data['user_token'] . '&filter_id=' . $this->request->get['filter_id'] . '&value_id=' . $this->request->get['value_id'] . $url, true);
		}
                
                
                // Default Setting
                $setting = array();
                $setting['value_description'] = array();
                $setting['value'] = '';
                $setting['regex'] = 0;
                $setting['sort_order'] = 0;
                $setting['status'] = true;
                $setting['image'] = '';
                $setting['setting'] = array();
                
                
                if($this->request->server['REQUEST_METHOD'] == 'POST'){
                    $setting = array_merge($setting, $this->request->post);
                } elseif(isset($this->request->get['value_id'])) {
                    $setting = array_merge($setting, $this->model_extension_maza_tf_filter_value->getValue($this->request->get['value_id']));
                    $setting['value_description'] = $this->model_extension_maza_tf_filter_value->getValueDescriptions($this->request->get['value_id']);
                }

                // Data
                $data = array_merge($data, $setting);
                
                $filter_info = $this->model_extension_maza_tf_filter->getFilter($this->request->get['filter_id']);
                
                $data['filter_language'] = $this->model_localisation_language->getLanguage($filter_info['filter_language_id']);
                
                // Image
                if ($setting['image'] && is_file(DIR_IMAGE . $setting['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($setting['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
                
                $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
                
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
		$this->response->setOutput($this->load->view('extension/maza/tf_filter/value_form', $data));
	}
        
        protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter/value')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['value_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 100)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}
                
                if ((utf8_strlen($this->request->post['value']) < 1) || (utf8_strlen($this->request->post['value']) > 1000)) {
                        $this->error['value'] = $this->language->get('error_value');
                }
                
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		
		return !$this->error;
	}
        
//        protected function validateStatus() {
//		if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter/value')) {
//			$this->error['warning'] = $this->language->get('error_permission');
//		}
//
//		return !$this->error;
//	}
        
        protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter/value')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
        
//        protected function validateDelete() {
//		if (!$this->user->hasPermission('modify', 'extension/maza/tf_filter/value')) {
//			$this->error['warning'] = $this->language->get('error_permission');
//		}
//
//		return !$this->error;
//	}
}
