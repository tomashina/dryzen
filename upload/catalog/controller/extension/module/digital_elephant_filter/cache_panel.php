<?php
class ControllerExtensionModuleDigitalElephantFilterCachePanel extends Controller
{
    const CACHE_PREFIX  = 'DEF_panel_';
    
	// -------------- //
	// TOKEN PASSWORD //
	const TOKEN = 'qwertrt43245terwte';

    /**
     * @var Cache
     */
    private $cacheDEF = null;

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

    public function __construct($registry) {
        parent::__construct($registry);

        $long_time = 30000000;

		if (VERSION >= '3.0.0.0') {
            $cache_type = $this->config->get('cache_engine');
        } else {
            $cache_type = $this->config->get('cache_type');
        }

        $this->cacheDEF = new Cache($cache_type, $long_time);
        $this->panelCategory = $this->load->controller('extension/module/digital_elephant_filter/panel_category/prototype');
        $this->panelManufacturer = $this->load->controller('extension/module/digital_elephant_filter/panel_manufacturer/prototype');
        $this->panelOption = $this->load->controller('extension/module/digital_elephant_filter/panel_option/prototype');
        $this->panelAttribute = $this->load->controller('extension/module/digital_elephant_filter/panel_attribute/prototype');
    }

    public function caching()
    {
        if (empty($this->request->post['token']) || $this->request->post['token'] != self::TOKEN) {
            $this->response->setOutput('Permission denied');
            return;
        }

        if ($this->request->post['token']) {
            $this->load->model('extension/module/digitalElephantFilter');

            //post $settings
            $this->clear();

			if ((float)VERSION >= 3.0) {
			$settings = $this->config->get('module_digitalElephantFilter_settings');
			} else {
			 $settings = $this->config->get('digitalElephantFilter_settings');       
			}
			
            $category_ids = $this->model_extension_module_digitalElephantFilter->getAllCategoryIds();
            if ($category_ids) {
                foreach ($category_ids as $category_id) {
                    $category_data = $this->panelCategory->getSectionGroups($settings, $category_id);
                    $this->cacheDEF->set(self::CACHE_PREFIX . 'cat_' . $category_id, $category_data);

                    $manufacturer_data = $this->panelManufacturer->getSectionGroups($settings, $category_id);
                    $this->cacheDEF->set(self::CACHE_PREFIX . 'man_' . $category_id, $manufacturer_data);

                    $option_data = $this->panelOption->getSectionGroups($settings, $category_id);
                    $this->cacheDEF->set(self::CACHE_PREFIX . 'opt_' . $category_id, $option_data);

                    $attribute_data = $this->panelAttribute->getSectionGroups($settings, $category_id);
                    $this->cacheDEF->set(self::CACHE_PREFIX . 'attr_' . $category_id, $attribute_data);
                }
            }

            $this->response->setOutput('Cache completed');
        }
    }

    public function clear()
    {
        if (empty($this->request->post['token']) || $this->request->post['token'] != self::TOKEN) {
            $this->response->setOutput('Permission denied');
            return;
        }

        $path = [DIR_CACHE . 'cache.' . self::CACHE_PREFIX . '*'];
        while (count($path) != 0) {
            $next = array_shift($path);
            foreach (glob($next) as $file) {
                unlink($file);
            }
        }

        $this->response->setOutput('Clear completed');
    }
}