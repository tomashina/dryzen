<?php
class ControllerExtensionModuleBaselMegamenu extends Controller {
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
		
		$data[$token_prefix] = $this->session->data[$token_prefix];
		if (isset($_GET['edit'])) $data['edit'] = $_GET['edit']; else $data['edit']	= '';
		if (isset($_GET['module_id'])) $data['module_id'] = $_GET['module_id']; else $data['module_id']	= '';
		
        //Load the language file for this module
        $this->load->language('extension/module/basel_megamenu');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('extension/basel/basel_megamenu');
        $this->load->model('tool/image');
        $this->load->model($model_module_load);
        $this->document->addStyle('view/javascript/basel_megamenu/basel_megamenu.css');
        $this->document->addScript('view/javascript/basel_megamenu/jquery.nestable.js');
        $this->load->model('localisation/language');
        $data['languages'] = $this->model_localisation_language->getLanguages();
        $data['language_id'] = 0;
        foreach($data['languages'] as $value) {
            if($value['code'] == $this->config->get('config_language')) {
                $data['language_id'] = $value['language_id'];
            }
        }
		
		//Languages
		$this->load->language('extension/module/basel_megamenu');
		$data['heading_title'] 		= $this->language->get('heading_title');
		$data['heading_title_so'] 	= $this->language->get('heading_title_so');
		$data['text_edit'] 			= $this->language->get('text_edit');
		$data['text_enabled'] 		= $this->language->get('text_enabled');
		$data['text_disabled'] 		= $this->language->get('text_disabled');
		$data['text_yes'] 			= $this->language->get('text_yes');
		$data['text_no'] 			= $this->language->get('text_no');
		$data['entry_name'] 		= $this->language->get('entry_name');
		$data['entry_description_name'] 		= $this->language->get('entry_description_name');	
		$data['text_creat_new_item'] 			= $this->language->get('text_creat_new_item');	
		$data['text_expand_all'] 				= $this->language->get('text_expand_all');
		$data['text_collapse_all'] 				= $this->language->get('text_collapse_all');
		$data['text_edit_item'] 				= $this->language->get('text_edit_item');
		$data['text_name'] = $this->language->get('text_name');
		$data['text_label'] = $this->language->get('text_label');
		$data['text_icon_font'] = $this->language->get('text_icon_font');
		$data['text_class_menu'] = $this->language->get('text_class_menu');
		$data['text_link_in_new_window'] = $this->language->get('text_link_in_new_window');	
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_enabled'] = $this->language->get('text_enabled');		
		$data['text_status'] = $this->language->get('text_status');		
		$data['text_position'] = $this->language->get('text_position');
		$data['text_submenu_width'] = $this->language->get('text_submenu_width');
		$data['text_example'] = $this->language->get('text_example');
		$data['text_content_item'] = $this->language->get('text_content_item');
		$data['text_content_config'] = $this->language->get('text_content_config');
		$data['text_parent_config'] = $this->language->get('text_parent_config');	
		$data['text_parent_item'] = $this->language->get('text_parent_item');	
		$data['text_content_width'] = $this->language->get('text_content_width');	
		$data['text_content_type'] = $this->language->get('text_content_type');	
		$data['text_name'] 						= $this->language->get('text_name');	
		$data['text_basic_configuration'] 		= $this->language->get('text_basic_configuration');	
		$data['text_design_configuration'] 		= $this->language->get('text_design_configuration');
		$data['text_orientation'] 				= $this->language->get('text_orientation');	
		$data['text_number_load_vertical'] 		= $this->language->get('text_number_load_vertical');	
		$data['text_home_item'] 				= $this->language->get('text_home_item');
		$data['text_home_text'] 				= $this->language->get('text_home_text');	
		$data['entry_head_name'] 				= $this->language->get('entry_head_name');
		$data['entry_display_mobile_module'] 	= $this->language->get('entry_display_mobile_module');
		
		
		if(isset($_GET['delete'])) {
            if($this->validate()){
                if($this->model_extension_basel_basel_megamenu->deleteMenu(intval($_GET['delete']))) {
                    $this->session->data['success'] = $this->language->get('text_success_menu_item_delete');
					$this->remove_cache();
                } else {
                    $this->session->data['error_warning'] = $this->language->get('text_error_menu_item_delete');
                }
            } else {
                $this->session->data['error_warning'] = $this->language->get('error_permission');
            }
            $this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix].'&module_id='.$this->request->get['module_id']);
        }
		
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            if(isset($_POST['button-create'])) {
                if($this->validate()) {
                    $error = false;
                    $lang_id = $data['language_id'];
                    if(empty($this->request->post['name'][$lang_id])) {$this->request->post['name'][$lang_id] = 'Unnamed Item';}
                    if($error == true) {
                        $this->session->data['error_warning'] = $this->language->get('text_warning');
                    } else {
						
						if(isset($this->request->post['content']['subcategory']['category']) && ($this->request->post['content']['subcategory']['category']))
						$this->request->post['content']['subcategory']['category'] = json_encode($this->request->post['content']['subcategory']['category'],true);	
                        $this->model_extension_basel_basel_megamenu->addMenu($this->request->post);
                        $this->session->data['success'] = $this->language->get('text_success');
						$this->remove_cache();
						$this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix].'&module_id='.$this->request->get['module_id']);
                    }
                } else {
                    $this->session->data['error_warning'] = $this->language->get('error_permission');
					$this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix].'&module_id='.$this->request->get['module_id']);
                }
                
            }
        }
			
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if(isset($_POST['button-back'])) {
				$this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix].'&module_id='.$this->request->get['module_id']);
			}	
			
            if(isset($_POST['button-edit'])) {
                if($this->validate()) {
                    $error = false;
                    $lang_id = $data['language_id'];
                    if($this->request->post['name'][$lang_id] == '') $error = true;
                    if($error == true) {
                        $this->session->data['error_warning'] = $this->language->get('text_warning');
                    } else {
						
						if(isset($this->request->post['content']['subcategory']['category']) && ($this->request->post['content']['subcategory']['category']))
						$this->request->post['content']['subcategory']['category'] = json_encode($this->request->post['content']['subcategory']['category'],true);						
                        $this->model_extension_basel_basel_megamenu->saveMenu($this->request->post);
						
                        $this->session->data['success'] = $this->language->get('text_success');
						$this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix].'&module_id='.$this->request->get['module_id']);
                    }
                } else {
                    $this->session->data['error_warning'] = $this->language->get('error_permission');
					$this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix].'&module_id='.$this->request->get['module_id']);
                }
            }
        }
		
        $data['nestable_list'] = $this->model_extension_basel_basel_megamenu->generate_nestable_list($data['language_id']);
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if(isset($_POST['button-save'])){
                $megamenu = array();

                if(!isset($this->request->post['status'])) 
					$this->request->post['status'] = 1;
                if(!isset($this->request->post['sort_order'])) 
					$this->request->post['sort_order'] = 0;
                if(!isset($this->request->post['orientation'])) 
					$this->request->post['orientation'] = 0;
                if(!isset($this->request->post['home_text'])) 
					$this->request->post['home_text'] = 0;
                if(!isset($this->request->post['home_item'])) 
					$this->request->post['home_item'] = 0;
                if(empty($this->request->post['name'])) 
					$this->request->post['name'] = 'New Mega Menu';
                if(!isset($this->request->post['icon_font'])) 
					$this->request->post['icon_font'] = '';
                if(!isset($this->request->post['class_menu'])) 
					$this->request->post['class_menu'] = '';					
                if(!isset($this->request->post['show_itemver']))
					$this->request->post['show_itemver'] = 10;
				if (!isset($this->request->post['head_name'])) {
					$this->request->post['head_name'] = array();
				}	
				if (!isset($this->request->post['disp_mobile_module'])) {
					$this->request->post['disp_mobile_module'] = array();
				}
				
				$moduleid_new= $this->model_extension_basel_basel_megamenu->getModuleId(); // Get module id
				$module_id = '';
				if (!isset($this->request->get['module_id'])) {
					$this->request->post['moduleid'] = $moduleid_new[0]['Auto_increment'];
					$module_id = $moduleid_new[0]['Auto_increment'];
					$this->$model_module_path->addModule('basel_megamenu', $this->request->post);	
				} else {
					$module_id = $this->request->get['module_id'];
					$this->request->post['moduleid'] = $this->request->get['module_id'];
					$this->$model_module_path->editModule($this->request->get['module_id'], $this->request->post);
				}	
				if (isset($this->request->post['import_module']) && $this->request->post['import_module']) {
					$import_module = $this->request->post['import_module'];
					$this->model_extension_basel_basel_megamenu->duplicateModule($module_id,$import_module);
				}					
                $this->session->data['success'] = $this->language->get('text_success');
				$this->remove_cache();
				$this->response->redirect($this->url->link('extension/module/basel_megamenu', 'module_id='.$module_id.'&'.$token_prefix.'=' . $this->session->data[$token_prefix], true));
			}
        }

        if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $module_info = $this->$model_module_path->getModule($this->request->get['module_id']);
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
				'href' => $this->url->link('extension/module/basel_megamenu', $token_prefix . '=' . $this->session->data[$token_prefix], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/basel_megamenu', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}	

		$this->load->model($model_module_load);
		$data['modules'] = $this->$model_module_path->getModulesByCode('basel_megamenu');
		if (isset($this->request->post['head_name'])) {
			$data['head_name'] = $this->request->post['head_name'];
		} elseif (!empty($module_info)) {
			$data['head_name'] = (isset($module_info['head_name'])) ? $module_info['head_name'] : array();
		} else {
			$data['head_name'] = array();
		}
		if (isset($this->request->get['module_id'])) {
			$data['moduleid'] = $this->request->get['module_id'];
		} elseif (!empty($module_info) && isset($module_info['moduleid'])) {
			$data['moduleid'] = $module_info['moduleid'];
		} else {
			$data['moduleid'] = '';
		}		
        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($module_info)) {
            $data['name'] = $module_info['name'];
        } else {
            $data['name'] = '';
        }
        if (isset($this->request->post['show_itemver'])) {
            $data['show_itemver'] = $this->request->post['show_itemver'];
        } elseif (!empty($module_info)) {
            $data['show_itemver'] = $module_info['show_itemver'];
        } else {
            $data['show_itemver'] = '';
        }
        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = '';
        }
        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($module_info)) {
            $data['sort_order'] = $module_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }
        if (isset($this->request->post['orientation'])) {
            $data['orientation'] = $this->request->post['orientation'];
        } elseif (!empty($module_info)) {
            $data['orientation'] = $module_info['orientation'];
        } else {
            $data['orientation'] = '';
        }
        if (isset($this->request->post['home_text'])) {
            $data['home_text'] = $this->request->post['home_text'];
        } elseif (!empty($module_info)) {
            $data['home_text'] = $module_info['home_text'];
        } else {
            $data['home_text'] = '';
        }
		
        if (isset($this->request->post['home_item'])) {
            $data['home_item'] = $this->request->post['home_item'];
        } elseif (!empty($module_info)) {
            $data['home_item'] = $module_info['home_item'];
        } else {
            $data['home_item'] = '';
        }
		

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/basel_megamenu', $token_prefix . '=' . $this->session->data[$token_prefix], true);
			$data['selectedid'] = 0;       
	   } else {
            $data['action'] = $this->url->link('extension/module/basel_megamenu', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true);			
			$data['selectedid'] = $this->request->get['module_id'];
		}
		
        if (isset($_GET['jsonstring'])) {
            if($this->validate()){
                $jsonstring = $_GET['jsonstring'];
                $jsonDecoded = json_decode(html_entity_decode($jsonstring));
                function parseJsonArray($jsonArray, $parentID = 0) {
                    $return = array();
                    foreach ($jsonArray as $subArray) {
                        $returnSubSubArray = array();
                        if (isset($subArray->children)){
                            $returnSubSubArray = parseJsonArray($subArray->children, $subArray->id);
                        }
                        $return[] = array('id' => $subArray->id, 'parentID' => $parentID);
                        $return = array_merge($return, $returnSubSubArray);
                    }
                    return $return;
                }
                $readbleArray = parseJsonArray($jsonDecoded);
                foreach ($readbleArray as $key => $value) {
                    if (is_array($value)) {
                        $this->model_extension_basel_basel_megamenu->save_rang($value['parentID'], $value['id'], $key);
                    }
                }
				$this->remove_cache();
                die("The menu was last saved ".date("y-m-d H:i:s"));
				
            } else {
                die($this->language->get('error_permission'));
            }
        }


        $data['action_type'] = 'basic';
        if(isset($_GET['action'])) {				
			$_['error_width']      = 'Width required!';
            if($_GET['action'] == 'create') {
                $data['action_type'] = 'create';
                $data['name'] = '';
                $data['description'] = '';
                $data['icon'] = '';
                $data['link'] = '';
                $data['new_window'] = '';
                $data['icon_font'] = '';
				$data['class_menu'] = '';
				$data['disp_mobile_item'] = 1;
                $data['status'] = '';
                $data['position'] = '';
                $data['submenu_width'] = '600';
                $data['content_width'] = '4';
                $data['content_type'] = '0';
				$data['item_type'] = '1';
				$data['show_title'] = '0';
                $data['content'] = array(
                    'html' => array(
                        'text' => array()
                    ),
                    'product' => array(
                        'id' => '',
                        'name' => ''
                    ),
					'categories' => array(
                        'categories' => array(),
                        'columns' => '',
                        'submenu' => '',
                        'submenu_columns' => '',
						'limit' => ''
                    ),										
					'image' => ''
                );
                $data['list_categories'] = false;
            }
        }
        // Edycja menu
        if(isset($_GET['edit'])) {
            $data['action_type'] = 'edit';						
            $dane = $this->model_extension_basel_basel_megamenu->getMenu(intval($_GET['edit']));		
			$this->load->model('tool/image');
			if (isset($dane['content']['image']['link']) && is_file(DIR_IMAGE . $dane['content']['image']['link'])) {
				$dane['content']['image']['image_link'] = $this->model_tool_image->resize($dane['content']['image']['link'], 100, 100);
			} elseif (!empty($dane) && is_file(DIR_IMAGE . $dane['icon'])) {
				$dane['content']['image']['image_link'] = $this->model_tool_image->resize($dane['content']['image']['link'], 100, 100);
			} else {
				$dane['content']['image']['image_link'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			}	
		
			$dane['content']['subcategory']['category'] = (isset($dane['content']['subcategory']['category']) && $dane['content']['subcategory']['category']) ? json_decode($dane['content']['subcategory']['category']) :  array();
       
			if($dane) {
                $data['name'] = $dane['name'];
                $data['description'] = $dane['description'];
                $data['icon'] 		= 	$dane['icon'];
                $data['link'] 		= $dane['link'];
                $data['icon_font'] = $dane['icon_font'];
				$data['class_menu'] = $dane['class_menu'];
				$data['disp_mobile_item'] = $dane['disp_mobile_item'];
				$data['item_type'] = $dane['item_type'];
				$data['show_title'] = $dane['show_title'];
                $data['new_window'] = $dane['new_window'];
                $data['status'] = $dane['status'];
                $data['position'] = $dane['position'];
				$data['disp_mobile_item'] = $dane['disp_mobile_item'];
                $data['submenu_width'] = $dane['submenu_width'];
                $data['content_width'] = $dane['content_width'];
                $data['content_type'] = $dane['content_type'];
                $data['content'] = $dane['content'];
                $data['list_categories'] = $this->model_extension_basel_basel_megamenu->getCategories($dane['content']['categories']['categories']);
            } else {
                $this->session->data['error_warning'] = 'This menu does not exist!';
                $this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix]);
            }
        }
        elseif (isset($_GET['duplicate']))
        {
            $this->model_extension_basel_basel_megamenu->duplicateMenu($_GET['duplicate']);
            $this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix].'&module_id='.$this->request->get['module_id']);
        }
        elseif (isset($_GET['changestatus']))
        {
            $dane = $this->model_extension_basel_basel_megamenu->getMenu(intval($_GET['changestatus']));
            if ($dane['status']==1)
                $status['status'] =0;
            else
                $status['status'] =1;
            $status['id'] = intval($_GET['changestatus']);
            $this->model_extension_basel_basel_megamenu->UpdatePosition($status);
			$this->remove_cache();
            $this->response->redirect(HTTPS_SERVER . 'index.php?route=extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix].'&module_id='.$this->request->get['module_id']);
        }
        $this->load->model('tool/image');
        if (isset($this->request->post['icon']) && is_file(DIR_IMAGE . $this->request->post['icon'])) {
            $data['icon'] = $this->request->post['icon'];
        } elseif (!empty($dane) && is_file(DIR_IMAGE . $dane['icon'])) {
            $data['icon'] = $dane['icon'];
        } else {
            $data['icon'] = 'no_image.png';
        }
		$data['src_icon'] = $this->model_tool_image->resize($data['icon'],100,100);
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['image_default'] = 'no_image.png';
		$data['src_image_default'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        
        if (isset($this->session->data['error_warning'])) {
            $data['error_warning'] = $this->session->data['error_warning'];
            unset($this->session->data['error_warning']);
        } else {
            $data['error_warning'] = '';
        }
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        $data['linkremove'] = $this->url->link('extension/module/basel_megamenu&'.$token_prefix.'=' . $this->session->data[$token_prefix]);
        $data['cancel'] = $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['success_remove'] = $this->language->get('text_success_remove');
        $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        if($is_ajax){
            self::remove_cache();
        }
        $this->response->setOutput($this->load->view('extension/module/basel_megamenu', $data));
    }
    
	public function remove_cache() {
        $this->cache->delete('megamenu');
    }
	
	public function uninstall() {
        $this->load->model('setting/setting');
        $this->load->model('extension/basel/basel_megamenu');
        $this->model_extension_basel_basel_megamenu->uninstall();
    }

    public function install() {
		if ((float)VERSION >= 3.0) {$model_module_load = 'setting/module';} else {$model_module_load = 'extension/module';}
        $this->load->model('setting/setting');
        $this->load->model($model_module_load);
        $this->load->model('extension/basel/basel_megamenu');
		$this->model_extension_basel_basel_megamenu->install();
    }
	
	protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/basel_megamenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }		
		return !$this->error;
    }

}