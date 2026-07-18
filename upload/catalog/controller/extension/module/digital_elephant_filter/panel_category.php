<?php
Class ControllerExtensionModuleDigitalElephantFilterPanelCategory extends Controller
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
        $categories_setting = $setting['DEF_settings']['categories'][0];
        $categories_setting_advance = $setting['DEF_settings']['advance']['categories'];

        $section_groups = $this->cache->get('DEF_panel_cat_' . $category_id);

        if (($this->helperUrl->isAjaxRequest() || !$section_groups  || !isset($setting['DEF_settings']['cache']['isset'])) && !isset($categories_setting['hide'])) {

            $categories = $this->model_extension_module_digitalElephantFilter->getSubCategories($category_id, $setting['DEF_settings']);

            $section_groups = [];
            $sections = [];
            //if (count($categories) > 1) {
                //conversion of the interface
                $values = [];
                $this->load->model('tool/image');
                $this->load->language('extension/module/digitalElephantFilter');
                foreach ($categories as $category) {

                    $image = '';
                    if ($category['image'] && is_file(DIR_IMAGE . $category['image'])) {
                        $image_width = $categories_setting_advance['image']['width'];
                        $image_height = $categories_setting_advance['image']['height'];
                        $image = $this->model_tool_image->resize($category['image'], $image_width, $image_height);
                    }

                    $input_label = $category['name'];
                    $is_enable = true;
                    if (isset($setting['DEF_settings']['is_display_total'])) {

                        $url = $this->helperUrl->getUrlData();
                        $url['category_id'] = $category_id;
                        $url['sub_categories'] = [];
                        $url['sub_categories'][] = $category['category_id'];

                        $filter_data = $this->getProduct->getFilterDataByUrl($url);
                        $totalProducts = $this->getProduct->getTotalProducts($filter_data);

                        $input_label = $category['name'] . ' <span>(' . $totalProducts . ')</span>';
                        $is_enable = (isset($totalProducts) && $totalProducts > 0);
                    }

                    $values[] = array(
                        'input_value' => $category['category_id'],
                        'input_name' => 'category',
                        'input_label' => $input_label,
                        'value_id' => $category['category_id'],
                        'image' => $image,
                        'is_enable' => $is_enable
                    );
                }


                //type
                $type = '';
                if (isset($categories_setting['type']) && $categories_setting['type']) {
                    $type = $categories_setting['type'];
                }

                //open
                $opened = true;
                if (isset($categories_setting['close'])) {
                    $opened = false;
                }

                $section_name = $this->language->get('text_categories');
                $section_id = 'categories';

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