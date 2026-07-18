<?php

class ControllerExtensionModuleDigitalElephantFilter extends Controller
{
    const STATUS_ACTIVE = '1';

    private $error = array();

    public function index()
    {

		if ((float)VERSION >= 3.0) {
		$token_prefix = 'user_token';
		$modules_url = 'marketplace/extension';
		} else {
		$token_prefix = 'token';
		$modules_url = 'extension/extension';
		}
		
		$this->document->addStyle('view/javascript/basel/basel_panel.css');

        $this->loadModel();
        $this->loadLanguage();

        $this->document->setTitle($this->language->get('heading_title'));

        $this->edit();

        $data = $this->getText();

        $data += $this->getError();

        $data += $this->getLinks();

        $data += $this->getSettingData();

        $data['packages'] = $this->getFilterData();
        $data['packages_advance'] = $this->getPackagesSort();
        $data['preloaders'] = $this->getPreloaders();

        $data['list_types_input'] = $this->getTemplatesListType();
        $data['list_types_sort'] = $this->getListTypesSort();

        $data['languages'] = $this->getLanguages();

        $data += $this->getLayouts();

        $data['breadcrumbs'] = $this->getBreadcrumbs();

        $this->response->setOutput($this->load->view('extension/module/digitalElephantFilter', $data));

    }


    protected function getFilterData()
    {
        $packages = array();

        $categories = $this->getCategories();
        $manufacturers = $this->getManufacturers();

        $options = $this->getOptions();
        $attributes = $this->getAttributes();

        //sort output data
        $packages[] = $manufacturers;
        $packages[] = $categories;
		$packages[] = $options;
        $packages[] = $attributes;
		
		return $packages;
    }

    private function getPackagesSort()
    {
        $packages_advance = array(
            'manufacturers',
            'categories',
            'options',
            'attributes'
        );

        return $packages_advance;
    }

    private function loadModel()
    {
        $this->load->model('setting/setting');
        $this->load->model('catalog/option');
        $this->load->model('catalog/attribute');
    }

    private function loadLanguage()
    {
        $this->load->language('extension/module/digitalElephantFilter');
    }

    private function edit()
    {
		
		if ((float)VERSION >= 3.0) {
		$token_prefix = 'user_token';
		$modules_url = 'marketplace/extension';
		$module_key_name = 'module_digitalElephantFilter';
		} else {
		$token_prefix = 'token';
		$modules_url = 'extension/extension';
		$module_key_name = 'digitalElephantFilter';
		}
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $data_to_setting = [
                $module_key_name . '_settings' => $this->request->post,
                $module_key_name . '_status' => $this->request->post['status'],
            ];

            $this->model_setting_setting->editSetting($module_key_name, $data_to_setting);

            $this->session->data['success'] = $this->language->get('text_success');

            if (!empty($this->request->post['save_out'])) {
                
                    $this->response->redirect($this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true));
               
            }
        }
    }

    private function getCategories()
    {
        $category_id = 0;
        $category['input_label'] = $this->language->get('text_categories');
        $category['section_id'] = $category_id;
        $category['input_name'] = 'categories';
        $category['group_name'] = '';
        $categories[] = $category;
        return $categories;
    }

    private function getManufacturers()
    {
        $manufacturer_id = 0;
        $manufacturer['input_label'] = $this->language->get('text_manufacturer');
        $manufacturer['section_id'] = $manufacturer_id;
        $manufacturer['input_name'] = 'manufacturers';
        $manufacturer['group_name'] = '';
        $manufacturers[] = $manufacturer;
        return $manufacturers;
    }

    private function getOptions()
    {

        $total_options = $this->model_catalog_option->getTotalOptions();

        $option_args = array(
            'start' => 0,
            'limit' => $total_options,
            'sort' => 'sort'
        );

        $options = $this->model_catalog_option->getOptions($option_args);

        foreach ($options as $key => $option) {
            if (!in_array($option['type'], array('radio', 'checkbox', 'select', 'image'))) {
                unset($options[$key]);
            }
        }

        $data = array();
        foreach ($options as $option) {
            $data[] = array(
                'section_id' => $option['option_id'],
                'input_label' => $option['name'],
                'input_name' => "options",
                'group_name' => '',
            );
        }

        return $data;
    }

    private function getAttributes()
    {
        $total_attributes = $this->model_catalog_attribute->getTotalAttributes();

        $attr_args = array(
            'start' => 0,
            'limit' => $total_attributes,
            'sort' => 'sort'
        );
        $attributes = $this->model_catalog_attribute->getAttributes($attr_args);

        $data = array();
        foreach ($attributes as $attribute) {
            $data[] = array(
                'section_id' => $attribute['attribute_id'],
                'input_label' => $attribute['name'],
                'input_name' => "attributes",
                'group_name' => '<i>(' . $attribute['attribute_group'] . ')</i>',
            );
        }

//        var_dump($attributes);

        return $data;
    }


    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/digitalElephantFilter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['DEF_settings']['selector_container_products'])) {
            $this->error['selector_container_products'] = $this->language->get('error_selector_container_products');
        }

        return !$this->error;
    }

    protected function getText()
    {
        $data = array();
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_setting_name'] = $this->language->get('text_setting_name');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_limit'] = $this->language->get('entry_limit');
        $data['entry_width'] = $this->language->get('entry_width');
        $data['entry_height'] = $this->language->get('entry_height');
        $data['entry_status'] = $this->language->get('entry_status');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_cancel'] = $this->language->get('button_save_and_cancel');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');

        $data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $data['text_categories'] = $this->language->get('text_categories');
        $data['text_checked'] = $this->language->get('text_checked');
        $data['text_visible'] = $this->language->get('text_visible');
        $data['text_selector_container_products'] = $this->language->get('text_selector_container_products');
        $data['text_selector_pagination'] = $this->language->get('text_selector_pagination');
        $data['text_selector_quantity_products'] = $this->language->get('text_selector_quantity_products');
        $data['text_selector_limit'] = $this->language->get('text_selector_limit');
        $data['text_selector_sort'] = $this->language->get('text_selector_sort');
        $data['text_filter_price'] = $this->language->get('text_filter_price');

        $data['text_tab_filter_panel'] = $this->language->get('text_tab_filter_panel');
        $data['text_tab_filter_panel_advanced'] = $this->language->get('text_tab_filter_panel_advanced');
        $data['text_tab_selector'] = $this->language->get('text_tab_selector');
        $data['text_tab_sort'] = $this->language->get('text_tab_sort');
        $data['text_tab_other'] = $this->language->get('text_tab_other');
        $data['text_tab_label'] = $this->language->get('text_tab_label');
        $data['text_tab_cache'] = $this->language->get('text_tab_cache');

        $data['text_state_pagination'] = $this->language->get('text_state_pagination');
        $data['text_state_show_more'] = $this->language->get('text_state_show_more');
        $data['text_state_quantity_products'] = $this->language->get('text_state_quantity_products');

        $data['text_choose_preloader'] = $this->language->get('text_choose_preloader');

        $data['text_type'] = $this->language->get('text_type');
        $data['text_hide'] = $this->language->get('text_hide');
        $data['text_close'] = $this->language->get('text_close');
        $data['text_sort'] = $this->language->get('text_sort');

        $data['text_on_button_apply'] = $this->language->get('text_on_button_apply');
        $data['text_on_button_clear'] = $this->language->get('text_on_button_clear');
        $data['text_on_group_attributes'] = $this->language->get('text_on_group_attributes');
        $data['text_on_seo_keywords'] = $this->language->get('text_on_seo_keywords');
        $data['text_on_display_totals'] = $this->language->get('text_on_display_totals');

        $data['text_cache_isset'] = $this->language->get('text_cache_isset');
        $data['text_cache_token'] = $this->language->get('text_cache_token');
        $data['text_cache_update'] = $this->language->get('text_cache_update');
        $data['text_cache_clear'] = $this->language->get('text_cache_clear');

        $data['text_section_sort'] = $this->language->get('text_section_sort');

        return $data;
    }

    private function getListTypesSort()
    {
        $list_types_sort[] = array('value' => 'sort', 'name' => $this->language->get('text_type_sort_sort'));
        $list_types_sort[] = array('value' => 'name', 'name' => $this->language->get('text_type_sort_name'));

        return $list_types_sort;
    }

    private function getBreadcrumbs()
    {
		
		if ((float)VERSION >= 3.0) {
		$token_prefix = 'user_token';
		$modules_url = 'marketplace/extension';
		} else {
		$token_prefix = 'token';
		$modules_url = 'extension/extension';
		}
	
        $data = array();

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
                'href' => $this->url->link('extension/module/digitalElephantFilter', $token_prefix . '=' . $this->session->data[$token_prefix], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/digitalElephantFilter', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        return $data['breadcrumbs'];
    }

    private function getError()
    {

        $data = array();

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

        if (isset($this->error['selector_container_products'])) {
            $data['error_selector_container_products'] = $this->error['selector_container_products'];
        } else {
            $data['error_selector_container_products'] = '';
        }

        return $data;
    }

    private function getLinks()
    {

		if ((float)VERSION >= 3.0) {
		$token_prefix = 'user_token';
		$modules_url = 'marketplace/extension';
		} else {
		$token_prefix = 'token';
		$modules_url = 'extension/extension';
		}

        $data = array();

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/digitalElephantFilter', $token_prefix . '=' . $this->session->data[$token_prefix], true);
        } else {
            $data['action'] = $this->url->link('extension/module/digitalElephantFilter', $token_prefix . '=' . $this->session->data[$token_prefix] . '&module_id=' . $this->request->get['module_id'], true);
        }


        $data['cancel'] = $this->url->link($modules_url, $token_prefix . '=' . $this->session->data[$token_prefix] . '&type=module', true);

        $data['action_cache_update'] = HTTP_CATALOG . 'index.php?route=extension/module/digital_elephant_filter/cache_panel/caching';
        $data['action_cache_clear'] = HTTP_CATALOG . 'index.php?route=extension/module/digital_elephant_filter/cache_panel/clear';

        return $data;
    }

    private function getSettingData()
    {
	
		if ((float)VERSION >= 3.0) {
		$module_key_name = 'module_digitalElephantFilter';
		} else {
		$module_key_name = 'digitalElephantFilter';
		}
	
        $data = [];

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $module_info = $this->request->post;
        } else {
            $module_info = $this->config->get($module_key_name . '_settings');
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($module_info)) {
            $data['status'] = $module_info['status'];
        } else {
            $data['status'] = self::STATUS_ACTIVE;
        }

        $default_settings = $this->getDefaultSettings();

        $data['DEF_settings'] = [];
        if (isset($module_info['DEF_settings'])) {
            $data['DEF_settings'] = $module_info['DEF_settings'];
        }

        //FILTER PANEL
        if (!isset($module_info['DEF_settings']) && $default_settings['is_display_total']) {
            $data['DEF_settings']['is_display_total'] = $default_settings['is_display_total'];
        }
		if (!isset($module_info['DEF_settings']) && $default_settings['is_button_apply']) {
            $data['DEF_settings']['is_button_apply'] = $default_settings['is_button_apply'];
        }
        if (!isset($module_info['DEF_settings']) && $default_settings['is_button_clear']) {
            $data['DEF_settings']['is_button_clear'] = $default_settings['is_button_clear'];
        }
        if (!isset($module_info['DEF_settings']) && $default_settings['is_group_attributes']) {
            $data['DEF_settings']['is_group_attributes'] = $default_settings['is_group_attributes'];
        }

        //SORT/IMAGE
        $packages_advance = $this->getPackagesSort();
        foreach ($packages_advance as $package_name) {
            if (!isset($module_info['DEF_settings']['advance'][$package_name]['image']['width'])) {
                $data['DEF_settings']['advance'][$package_name]['image']['width'] = $default_settings['image']['width'];
            }
            if (!isset($module_info['DEF_settings']['advance'][$package_name]['image']['height'])) {
                $data['DEF_settings']['advance'][$package_name]['image']['height'] = $default_settings['image']['height'];
            }
            if (!isset($module_info['DEF_settings']['advance'][$package_name]['sort'])) {
                $data['DEF_settings']['advance'][$package_name]['sort'] = $default_settings['sort'];
            }
        }

        //SELECTOR
        if (!isset($data['DEF_settings']['selector_container_products'])) {
            $data['DEF_settings']['selector_container_products'] = $default_settings['selector']['container_products'];
        }

        if (!isset($data['DEF_settings']['selector_pagination'])) {
            $data['DEF_settings']['selector_pagination'] = $default_settings['selector']['pagination'];
        }

        if (!isset($data['DEF_settings']['selector_quantity_products'])) {
            $data['DEF_settings']['selector_quantity_products'] = $default_settings['selector']['quantity_products'];
        }

        if (!isset($data['DEF_settings']['selector_limit'])) {
            $data['DEF_settings']['selector_limit'] = $default_settings['selector']['limit'];
        }

        if (!isset($data['DEF_settings']['selector_sort'])) {
            $data['DEF_settings']['selector_sort'] = $default_settings['selector']['sort'];
        }

        //OTHER
        if (!isset($module_info['DEF_settings']) && $default_settings['state']['is_button_show_more']) {
            $data['DEF_settings']['state']['is_button_show_more'] = $default_settings['state']['is_button_show_more'];
        }
        if (!isset($module_info['DEF_settings']) && $default_settings['state']['is_pagination']) {
            $data['DEF_settings']['state']['is_pagination'] = $default_settings['state']['is_pagination'];
        }
        if (!isset($module_info['DEF_settings']) && $default_settings['state']['is_quantity_products']) {
            $data['DEF_settings']['state']['is_quantity_products'] = $default_settings['state']['is_quantity_products'];
        }
        if (!isset($module_info['DEF_settings']) && !isset($data['DEF_settings']['preloader_type'])) {
            $data['DEF_settings']['preloader_type'] = $default_settings['preloader_type'];
        }
        if (!isset($module_info['DEF_settings']) && $default_settings['seo']['is_keywords']) {
            $data['DEF_settings']['seo']['is_keywords'] = $default_settings['seo']['is_keywords'];
        }

        //CACHE
        if (!isset($module_info['DEF_settings']['cache']['token'])) {
            $data['DEF_settings']['cache']['token'] = 'sdfs23dsf54d2';
        }

        return $data;
    }

    private function getTemplatesListType()
    {
        // Make path into an array
        $path = array(DIR_CATALOG . 'view/theme/basel/template/extension/module/digitalElephantFilter/*');
        $templates = [];
        // While the path array is still populated keep looping through
        while (count($path) != 0) {
            $next = array_shift($path);

            foreach (glob($next) as $file) {
                // Add the file to the files to be deleted array
                if (is_file($file)) {
					
					if ((float)VERSION >= 3.0) {
						preg_match('/\/module\/digitalElephantFilter\/(.*)\.twig$/', $file, $matches);
					
					} else {
					  	preg_match('/\/module\/digitalElephantFilter\/(.*)\.tpl$/', $file, $matches);      
					}
                    
                    if (isset($matches[1]))
                        $templates[] = $matches[1];
                }
            }
        }
        sort($templates);
        return $templates;
    }

    private function getLayouts()
    {
        $data = array();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        return $data;
    }

    /**
     * Font Awesome preloader
     * @return array
     */
    private function getPreloaders()
    {
        return [
            'spinner_fast' => 'fa fa-spinner fa-pulse fa-3x fa-fw',
            'circle' => 'fa fa-circle-o-notch fa-spin fa-3x fa-fw',
            'refresh' => 'fa fa-refresh fa-spin fa-3x fa-fw',
            'config' => 'fa fa-cog fa-spin fa-3x fa-fw',
            'spinner_slow' => 'fa fa-spinner fa-spin fa-3x fa-fw',
        ];
    }

    private function getLanguages()
    {
        $this->load->model('localisation/language');
        return $this->model_localisation_language->getLanguages();
    }

    private function getDefaultSettings()
    {

       
        $preloader_type = 'spinner_fast';
      

        return [
            'sort' => 'Sort',
            'image' => [
                'width' => '25',
                'height' => '25'
            ],
            'selector' => [
                'container_products' => '.product-holder',
                'pagination' => '.pagination-navigation',
                'quantity_products' => '.pagination-text',
                'limit' => '#input-limit',
                'sort' => '#input-sort',
            ],
            'is_display_total' => true,
			'is_button_apply' => false,
            'is_button_clear' => true,
            'is_group_attributes' => true,
            'state' => [
                'is_button_show_more' => false,
                'is_pagination' => true,
                'is_quantity_products' => true,
            ],
            'preloader_type' => $preloader_type,
            'seo' => [
                'is_keywords' => true
            ],
        ];
    }
}