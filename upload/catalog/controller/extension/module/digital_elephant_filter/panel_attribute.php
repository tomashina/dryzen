<?php
Class ControllerExtensionModuleDigitalElephantFilterPanelAttribute extends Controller
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
        $section_groups = $this->cache->get('DEF_panel_attr_' . $category_id);
        if (($this->helperUrl->isAjaxRequest() || !$section_groups || !isset($setting['DEF_settings']['cache']['isset']))) {
            $args = array(
                'category_id' => $category_id,
                'filter_sub_category' => true
            );
            $attributes_groups = $this->model_extension_module_digitalElephantFilter->getAttributes($setting['DEF_settings'], $args);
			
			$attributes_type_by_setting = '';
			if (isset($setting['DEF_settings']['attributes'])) {
            $attributes_type_by_setting = $setting['DEF_settings']['attributes'];
			}

            $section_groups = [];
            foreach ($attributes_groups as $attribute_groups_id => $attribute_group) {

                $sections = [];
                foreach ($attribute_group['attribute_values'] as $attribute_value_id => $attribute_value) {

                    if (!isset($attributes_type_by_setting[$attribute_value_id]['hide'])) {

                        $values = [];
                        foreach ($attribute_value['values'] as $value) {
                            if (!empty($value) || $value === '0') {

                                $input_label = $value;
                                $is_enable = true;
                                if (isset($setting['DEF_settings']['is_display_total'])) {
                                    $url = $this->helperUrl->getUrlData();
                                    $url['category_id'] = $category_id;
                                    $url['attribute'][$attribute_value_id] = [];
                                    $url['attribute'][$attribute_value_id][] = $value;
                                    $filter_data = $this->getProduct->getFilterDataByUrl($url);
                                    $totalProducts = $this->getProduct->getTotalProducts($filter_data);

                                    $input_label = $value . ' <span>(' . $totalProducts . ')</span>';
                                    $is_enable = (isset($totalProducts) && $totalProducts > 0);
                                }

                                $values[] = array(
                                    'input_value' => $value,
                                    'input_name' => 'attribute',
                                    'input_label' => $input_label,
                                    'value_id' => $value,
                                    'image' => '',
                                    'is_enable' => $is_enable
                                );
                            }
                        }

                        if ($values) {
                            //type
                            $type = '';
                            if (isset($attributes_type_by_setting[$attribute_value_id]['type'])) {
                                $type = $attributes_type_by_setting[$attribute_value_id]['type'];
                            }

                            //open
                            $opened = true;
                            if (isset($attributes_type_by_setting[$attribute_value_id]['close'])) {
                                $opened = false;
                            }

                            $sections[] = array(
                                'id' => $attribute_value_id,
                                'name' => $attribute_value['name'],
                                'values' => $values,
                                'type' => $type,
                                'open' => $opened,
                            );
                        }
                    }
                }

                if ($sections) {
                    $section_groups[] = array(
                        'group_name' => $attribute_group['name'],
                        'sections' => $sections,
                    );
                }
            }
        }

        return $section_groups;
    }
}