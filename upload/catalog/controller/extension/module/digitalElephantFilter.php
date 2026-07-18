<?php

/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 9/23/2016
 * Time: 2:35 PM
 *
 * INTERFACE BY MANUFACTURER, CATEGORY, OPTIONS, ATTRIBUTES:
 * $input_name
 * $input_value
 * $input_label
 * $element_id(options, attributes, manufacturer, category)
 * $image
 */

/**
 * Render filter panel
 * Class ControllerExtensionModuleDigitalElephantFilter
 */
class ControllerExtensionModuleDigitalElephantFilter extends Controller
{
    /**
     * @var ControllerExtensionModuleDigitalElephantFilterSeo
     */
    private $seo = null;

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterPanel
     */
    private $panel = null;


    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->seo = $this->load->controller('extension/module/digital_elephant_filter/seo/prototype');
        $this->panel = $this->load->controller('extension/module/digital_elephant_filter/panel/prototype');
    }

    public function index()
    {

		if ((float)VERSION >= 3.0) {
			$setting = $this->config->get('module_digitalElephantFilter_settings');
		} else {
		 	$setting = $this->config->get('digitalElephantFilter_settings');       
		}
        
        if ($rendered_panel = $this->panel->render($setting)) {
            $this->setStyle();
            $this->setScript();
            $this->runSEO($setting);
            return $rendered_panel;
        }
    }
    protected function setScript()
    {
		
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}	
		
        if ($this->language->get('direction') == 'rtl') {
		$this->document->addScript($server . '/catalog/view/javascript/jquery/ui/jquery-ui-slider-rtl.min.js');	
        } else { 
		$this->document->addScript($server . '/catalog/view/javascript/jquery/ui/jquery-ui-slider.min.js');
		}
		
		$this->document->addScript($server . '/catalog/view/theme/basel/js/jquery.ui.touch-punch.min.js');
		$this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/main.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/controller.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/helper.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/slider_price.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/url_private.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/url.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/pagination.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/show_more.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/quantity_products.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/sync.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/container_products.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/panel.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/limit.js');
        $this->document->addScript($server . '/catalog/view/javascript/digitalElephantFilter/classes/sort.js');
    }

    protected function setStyle()
    {
		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}
		
        $this->document->addStyle($server . '/catalog/view/javascript/jquery/ui/jquery-ui.min.css');
    }

    private function runSEO($setting) {
        if (isset($setting['DEF_settings']['seo']['is_keywords']) && isset($this->request->get['ajax_digitalElephantFilter'])) {
            $this->seo->setMetaKeywords();
        }
    }
}