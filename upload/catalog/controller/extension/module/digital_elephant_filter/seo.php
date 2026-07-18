<?php

class ControllerExtensionModuleDigitalElephantFilterSeo extends Controller
{
    /**
     * @var ControllerExtensionModuleDigitalElephantFilterHelperUrl
     */
    private $helperUrl = null;


    public function prototype()
    {
        return $this;
    }

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->helperUrl = $this->load->controller('extension/module/digital_elephant_filter/helper_url/prototype');
    }

    public function setMetaKeywords()
    {
        $this->load->model('extension/module/digitalElephantFilter');
        $this->load->model('catalog/category');
        $this->load->model('catalog/manufacturer');

        $delimiter = ', ';
        $url_data = $this->helperUrl->getUrlData();

        $keywords = '';

        if (isset($this->request->get['path'])) {
            $path = explode('_', $this->request->get['path']);
            $category_id = array_pop($path);
            $model_category = $this->model_catalog_category->getCategory($category_id);
            $keywords .= $model_category['name'] . $delimiter;
        }

        if (!empty($url_data['manufacturers']['manufacturers'])) {
            foreach ($url_data['manufacturers']['manufacturers'] as $manufacturer_id) {
                $model_manufacturer = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
                $keywords .= $model_manufacturer['name'] . $delimiter;
            }
        }

        if (!empty($this->request->get['category']['categories'])) {
            foreach ($this->request->get['category']['categories'] as $category_id) {
                $model_category = $this->model_catalog_category->getCategory($category_id);
                $keywords .= $model_category['name'] . $delimiter;
            }
        }

        if (!empty($this->request->get['option'])) {
            foreach ($this->request->get['option'] as $option_ids) {
                foreach ($option_ids as $option_id) {
                    $model_option = $this->model_extension_module_digitalElephantFilter->getOptionValueDescriptions($option_id);
					if (isset($model_option['name'])) {
                    $keywords .= $model_option['name'] . $delimiter;
					}
                }
            }
        }

        if (!empty($this->request->get['attribute'])) {
            foreach ($this->request->get['attribute'] as $attribute_values) {
                foreach ($attribute_values as $attribute_value) {
                    $keywords .= $attribute_value . $delimiter;
                }
            }
        }

        $keywords = substr($keywords, 0, -strlen($delimiter));
        $this->document->setKeywords($keywords);
        return;
    }
}