<?php
Class ControllerExtensionModuleDigitalElephantFilterPanelManufacturer extends Controller
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
        //manufacturers setting
        $manufacturers_setting = $setting['DEF_settings']['manufacturers'][0];
        $manufacturers_setting_advance = $setting['DEF_settings']['advance']['manufacturers'];

        $section_groups = $this->cache->get('DEF_panel_man_' . $category_id);
        if (($this->helperUrl->isAjaxRequest() || !$section_groups || !isset($setting['DEF_settings']['cache']['isset'])) && !isset($manufacturers_setting['hide'])) {
            $args = array(
                'category_id' => $category_id,
                'filter_sub_category' => true
            );
            $manufacturers = $this->model_extension_module_digitalElephantFilter->getManufacturers($setting['DEF_settings'], $args);
            $section_groups = [];
            $sections = [];
            //if (count($manufacturers) > 1) {
                //conversion of the interface
                $values = [];
                $this->load->model('tool/image');
                $this->load->language('extension/module/digitalElephantFilter');
                foreach ($manufacturers as $manufacturer) {
                    $image = '';
                    if ($manufacturer['image'] && is_file(DIR_IMAGE . $manufacturer['image'])) {
                        $image_width = $manufacturers_setting_advance['image']['width'];
                        $image_height = $manufacturers_setting_advance['image']['height'];
                        $image = $this->model_tool_image->resize($manufacturer['image'], $image_width, $image_height);
                    }

                    $input_label = $manufacturer['name'];
                    $is_enable = true;
                    if (isset($setting['DEF_settings']['is_display_total'])) {
                        $url = $this->helperUrl->getUrlData();
                        $url['category_id'] = $category_id;
                        $url['manufacturers']['manufacturer'] = [];
                        $url['manufacturers']['manufacturer'][] = $manufacturer['manufacturer_id'];
                        $filter_data = $this->getProduct->getFilterDataByUrl($url);
                        $totalProducts = $this->getProduct->getTotalProducts($filter_data);

                        $input_label = $manufacturer['name'] . ' <span>(' . $totalProducts . ')</span>';

                        $is_enable = (isset($totalProducts) && $totalProducts > 0);
                    }

                    $values[] = array(
                        'input_value' => $manufacturer['manufacturer_id'],
                        'input_name' => 'manufacturers',
                        'input_label' => $input_label,
                        'value_id' => $manufacturer['manufacturer_id'],
                        'image' => $image,
                        'is_enable' => $is_enable
                    );
                }

                //type
                $type = 'none';
                if (isset($manufacturers_setting['type']) && $manufacturers_setting['type']) {
                    $type = $manufacturers_setting['type'];
                }

                //open
                $opened = true;
                if (isset($manufacturers_setting['close'])) {
                    $opened = false;
                }

                $section_name = $this->language->get('text_manufacturers');
                $section_id = 'manufacturer';

                $sections[] = array(
                    'id' => $section_id,
                    'name' => $section_name,
                    'values' => $values,
                    'type' => $type,
                    'open' => $opened,
                );
            //}

            $section_groups[] = array(
                'group_name' => '',
                'sections' => $sections,
            );
        }

        return $section_groups;
    }
}