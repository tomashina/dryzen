<?php
Class ControllerExtensionModuleDigitalElephantFilterPanel extends Controller
{
    protected $urlData = array();
    private $isPackageShow = [];
    private $storageFilterPrice = [];

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterHelperUrl
     */
    private $helperUrl = null;

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterGetProduct
     */
    private $getProduct = null;

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterPanelCategory
     */
    private $panelCategory = null;

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterPanelManufacturer
     */
    private $panelManufacturer = null;

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterPanelOption
     */
    private $panelOption = null;

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterPanelAttribute
     */
    private $panelAttribute = null;


    public function prototype()
    {
        return $this;
    }

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->helperUrl = $this->load->controller('extension/module/digital_elephant_filter/helper_url/prototype');
        $this->seo = $this->load->controller('extension/module/digital_elephant_filter/seo/prototype');
        $this->getProduct = $this->load->controller('extension/module/digital_elephant_filter/get_product/prototype');
        $this->panelCategory = $this->load->controller('extension/module/digital_elephant_filter/panel_category/prototype');
        $this->panelManufacturer = $this->load->controller('extension/module/digital_elephant_filter/panel_manufacturer/prototype');
        $this->panelOption = $this->load->controller('extension/module/digital_elephant_filter/panel_option/prototype');
        $this->panelAttribute = $this->load->controller('extension/module/digital_elephant_filter/panel_attribute/prototype');
    }

    public function render($setting, $is_ajax_render = false)
    {
        $this->loadModel();
        $this->loadLanguage();

        $this->urlData = $this->helperUrl->getUrlData();

        $data = $this->getText();

        $data['filter_data'] = $this->getFilterData($setting, $this->urlData['category_id']);
        $data['is_filter_show'] = $this->isFilterShow();
        $data['check_data_on_active'] = $this->urlData;

        $data['JS_config'] = $this->getJSConfig($setting);

        $data['is_button_apply'] = $this->isButtonApply($setting);
        $data['is_button_clear'] = $this->isButtonClear($setting);
        $data['is_show_group_attributes'] = $this->isShowGroupAttributes($setting);
        $data['is_ajax_render'] = $is_ajax_render;
		
		if ($this->config->get('theme_default_directory') == 'basel')
        return $this->load->view('extension/module/digitalElephantFilter', $data);
    }

    public function ajaxRender() {
        
	if ((float)VERSION >= 3.0) {
	$setting = $this->config->get('module_digitalElephantFilter_settings');
	} else {
	$setting = $this->config->get('digitalElephantFilter_settings');       
	}
        echo $this->render($setting, true);
    }

    protected function getJSConfig($setting)
    {
        $filterPrice = $this->getFilterPrice($setting);

        $text_no_result = $this->language->get('text_no_results');
       	
		if (!isset($this->request->get['path']))
		$this->request->get['path'] = '';

        return [
            'peakPrice' => [
                'min' => (isset($filterPrice['min'])) ? $filterPrice['min'] : '',
                'max' => (isset($filterPrice['max'])) ? $filterPrice['max'] : ''
            ],
            'currentPrice' => [
                'min' => $this->urlData['price']['min'],
                'max' => $this->urlData['price']['max'],
            ],
            'selector' => [
                'containerProducts' => html_entity_decode($setting['DEF_settings']['selector_container_products']),
                'pagination' => $setting['DEF_settings']['selector_pagination'],
                'quantityProducts' => $setting['DEF_settings']['selector_quantity_products'],
                'limit' => $setting['DEF_settings']['selector_limit'],
                'sort' => $setting['DEF_settings']['selector_sort'],
            ],
            'action' => [
                'category' => str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'])),
                'categoryProduct' => $this->url->link('product/category'),
                'ajaxRenderPagination' => $this->url->link('extension/module/digital_elephant_filter/pagination/ajaxRender'),
                'ajaxRenderQuantityProducts' => $this->url->link('extension/module/digital_elephant_filter/quantity_product/ajaxRender'),
                'getProduct' => $this->url->link('extension/module/digital_elephant_filter/get_product'),
                'ajaxCheckToRenderShowMore' => $this->url->link('extension/module/digital_elephant_filter/show_more/ajaxCheckToRender'),
                'ajaxRenderPanel' => $this->url->link('extension/module/digital_elephant_filter/panel/ajaxRender'),
                'ajaxSetStateSection' => $this->url->link('extension/module/digital_elephant_filter/panel/ajaxSetStateSection')
            ],
            'text' => [
                'productNotFound' => $text_no_result,
                'buttonShowMore' => $this->language->get('text_show_more'),
            ],
            'categoryPath' => $this->request->get['path'],
            'state' => [
                'isButtonShowMore' => (isset($setting['DEF_settings']['state']['is_button_show_more'])),
                'isPagination' => (isset($setting['DEF_settings']['state']['is_pagination'])),
                'isQuantityProducts' => (isset($setting['DEF_settings']['state']['is_quantity_products'])),
            ],
            'isButtonApply' => $this->isButtonApply($setting),
            'preloaderClass' => $this->getPreloaderClass($setting),
        ];
    }

    protected function isFilterShow()
    {
        return (
            (
                $this->isPackageShow['price']
                || $this->isPackageShow['category']
                || $this->isPackageShow['manufacturer']
                || $this->isPackageShow['option']
                || $this->isPackageShow['attribute']
            )
            && $this->hasCategoryProducts($this->urlData['category_id']) 
			&& (isset($this->request->get['path'])) 
        );
    }

    protected function getFilterData($setting, $category_id)
    {
        $data = [];

        //for cycle
        $packages = [
            'category',
            'manufacturer',
            'option',
            'attribute'
        ];

        $data['packages'] = [];
        foreach ($packages as $package) {
            if ($result_package = $this->getPackage($package, $setting, $category_id)) {
                $data['packages'][] = $result_package;
            }
        }

        $data['price'] = $this->getFilterPrice($setting);

        return $data;
    }

    /**
     * @param $setting
     * @return array
     */
    protected function getFilterPrice($setting)
    {
        if (!$this->storageFilterPrice) {
            $price = $this->model_extension_module_digitalElephantFilter->getMinMaxPrice($this->urlData['category_id']);

            $show = true;
            if (isset($setting['DEF_settings']['filter_price']['hide'])
                || $price['min'] == $price['max']) {
                $show = false;
            }

            $this->isPackageShow['price'] = $show;

            $section_states = $this->getStateSections();

            $data = [];
            if ($show) {
                $opened = true;
                if (isset($setting['DEF_settings']['filter_price']['close'])) {
                    $opened = false;
                }

                $section_name = 'price';
                $section_id = 'price';

                if (isset($section_states[$section_name][$section_id]) && $section_states[$section_name][$section_id]) {
                    $opened = true;
                } else if (isset($section_states[$section_name][$section_id]) && !$section_states[$section_name][$section_id]) {
                    $opened = false;
                }

                $data = array(
                    'min' => (int)floor($this->currency->getValue($this->session->data['currency']) * $price['min']),
                    'max' => (int)ceil($this->currency->getValue($this->session->data['currency']) * $price['max']),
                    'show' => (bool)$show,
                    'open' => (bool)$opened,
                );
            }

            $this->storageFilterPrice = $data;
        }

        return $this->storageFilterPrice;
    }

    protected function isButtonApply($setting)
    {
        return (isset($setting['DEF_settings']['is_button_apply']));
    }

    protected function isButtonClear($setting)
    {
        return (isset($setting['DEF_settings']['is_button_clear']));
    }

    protected function isShowGroupAttributes($setting)
    {
        return (isset($setting['DEF_settings']['is_group_attributes']));
    }

    protected function loadModel()
    {
        $this->load->model('catalog/manufacturer');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('extension/module/digitalElephantFilter');
        $this->load->model('tool/image');
        $this->load->model('localisation/currency');

    }

    protected function loadLanguage()
    {
        $this->load->language('product/category');
        $this->load->language('extension/module/digitalElephantFilter');
    }

    public function getPackage($packageName, $setting, $category_id)
    {
        $panelPackage = 'panel' . ucfirst($packageName);
        $section_groups = $this->{$panelPackage}->getSectionGroups($setting, $category_id);
        $this->isPackageShow[$packageName] = false;

        if ($section_groups) {
            $section_states = $this->getStateSections();

            foreach ($section_groups as &$section_group) {
                if ($section_group['sections']) {
                    $this->isPackageShow[$packageName] = true;

                    foreach ($section_group['sections'] as &$section) {
                        $opened = $section['open'];
                        if (isset($section_states[$section['name']][$section['id']]) && $section_states[$section['name']][$section['id']]) {
                            $opened = true;
                        } else if (isset($section_states[$section['name']][$section['id']]) && !$section_states[$section['name']][$section['id']]) {
                            $opened = false;
                        }

                        $section['open'] = $opened;
                    }
                }
            }
        }

        return $section_groups;
    }

    protected function getText()
    {
        $data = array();

        $data['text_price_range'] = $this->language->get('text_price_range');
        $data['text_all'] = $this->language->get('text_all');
        $data['text_clear'] = $this->language->get('text_clear');
        $data['text_ok'] = $this->language->get('text_ok');

        $data['heading_title'] = $this->language->get('heading_title');

        $data['symbol_left'] = $this->currency->getSymbolLeft($this->session->data['currency']);
        $data['symbol_right'] = $this->currency->getSymbolRight($this->session->data['currency']);

        return $data;
    }

    private function hasCategoryProducts($category_id)
    {
        $this->load->model('catalog/product');

        $args = array(
            'filter_category_id' => $category_id,
            'filter_sub_category' => true,
        );

        $result = $this->model_catalog_product->getTotalProducts($args);
        if ($result > 1) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * @param array $setting
     * @return string
     */
    private function getPreloaderClass($setting) {
        $preloaders = [
            'spinner_fast'  => 'fa fa-spinner fa-pulse fa-3x fa-fw',
            'circle'        => 'fa fa-circle-o-notch fa-spin fa-3x fa-fw',
            'refresh'       => 'fa fa-refresh fa-spin fa-3x fa-fw',
            'config'        => 'fa fa-cog fa-spin fa-3x fa-fw',
            'spinner_slow'  => 'fa fa-spinner fa-spin fa-3x fa-fw',
        ];

        return $preloaders[$setting['DEF_settings']['preloader_type']];
    }

    public function ajaxSetStateSection() {
        if (
            isset($this->request->post['section']['name'])
            && isset($this->request->post['section']['id'])
            && isset($this->request->post['section']['state'])
        ) {

            $name   = $this->request->post['section']['name'];
            $id     = $this->request->post['section']['id'];
            $state  = $this->request->post['section']['state'];

            $this->session->start();

            if (!isset($this->session->data['DEF_section'])) {
                $this->session->data['DEF_section'] = [];
            }

            if (!isset($this->session->data['DEF_section'][$name])) {
                $this->session->data['DEF_section'][$name] = [];
            }

            $this->session->data['DEF_section'][$name][$id] = $state;

            $this->response->setOutput('state saved');
        }
    }

    private function getStateSections() {
        $output = [];
        if (isset($this->session->data['DEF_section'])) {
            $output = $this->session->data['DEF_section'];
        }
        return $output;

    }
}