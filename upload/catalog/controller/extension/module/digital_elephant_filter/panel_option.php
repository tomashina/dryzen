<?php
Class ControllerExtensionModuleDigitalElephantFilterPanelOption extends Controller
{
    /**
     * @var ControllerExtensionModuleDigitalElephantFilterGetProduct
     */
    private $getProduct = null;

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterHelperUrl
     */
    private $helperUrl = null;

    public function prototype() {
        return $this;
    }

    public function __construct($registry) {
        parent::__construct($registry);
        $this->getProduct = $this->load->controller('extension/module/digital_elephant_filter/get_product/prototype');
        $this->helperUrl = $this->load->controller('extension/module/digital_elephant_filter/helper_url/prototype');
    }

    public function getSectionGroups($setting, $category_id)
    {
        //categories setting
        $section_groups = $this->cache->get('DEF_panel_opt_' . $category_id);
        if ($this->helperUrl->isAjaxRequest() || !$section_groups || !isset($setting['DEF_settings']['cache']['isset'])) {
            $args = array(
                'category_id' => $category_id,
                'filter_sub_category' => true
            );
            $options = $this->model_extension_module_digitalElephantFilter->getOptions($setting['DEF_settings'], $args);
			
			$options_type_by_setting = '';
            if (isset($setting['DEF_settings']['options'])) {
			$options_type_by_setting = $setting['DEF_settings']['options'];
            $options_setting_advance = $setting['DEF_settings']['advance']['options'];
			}

            $section_groups = [];
            $sections = [];
            $this->load->model('tool/image');
            foreach ($options as $option) {

                $option_id = $option['option_id'];

                $values = array();
				if (!isset($options_type_by_setting[$option_id]['hide'])) {

                    //conversion of the interface
                    foreach ($option['option_values'] as $option_value) {

                        $image = '';
                        if ($option_value['image'] && is_file(DIR_IMAGE . $option_value['image'])) {
                            $image_width = $options_setting_advance['image']['width'];
                            $image_height = $options_setting_advance['image']['height'];
                            $image = $this->model_tool_image->resize($option_value['image'], $image_width, $image_height);
                        }

                        $input_label = $option_value['name'];
                        $is_enable = true;
                        if (isset($setting['DEF_settings']['is_display_total'])) {
                            $url = $this->helperUrl->getUrlData();
                            $url['category_id'] = $category_id;
                            $url['option'][$option_id] = [];
                            $url['option'][$option_id][] = $option_value['option_value_id'];
                            $filter_data = $this->getProduct->getFilterDataByUrl($url);
                            $totalProducts = $this->getProduct->getTotalProducts($filter_data);
							
                            $input_label = $option_value['name'] . ' <span>(' . $totalProducts . ')</span>';
                            
							$is_enable = (isset($totalProducts) && $totalProducts > 0);
                        }

                        $values[] = array(
                            'input_value' => $option_value['option_value_id'],
                            'input_name' => 'option',
                            'input_label' => $input_label,
                            'value_id' => $option_value['option_value_id'],
                            'image' => $image,
                            'is_enable' => $is_enable
                        );
                    }

                    //type
                    $type = '';
                    if (isset($options_type_by_setting[$option_id]['type'])) {
                        $type = $options_type_by_setting[$option_id]['type'];
                    }

                    //open
                    $opened = true;
                    if (isset($options_type_by_setting[$option_id]['close'])) {
                        $opened = false;
                    }

                    $sections[] = array(
                        'id' => $option_id,
                        'name' => $option['name'],
                        'values' => $values,
                        'type' => $type,
                        'open' => $opened,
                    );
                }
            }

            $section_groups[] = array(
                'group_name' => '',
                'sections' => $sections,
            );
        }

        return $section_groups;
    }
}