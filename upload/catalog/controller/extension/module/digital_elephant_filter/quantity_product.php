<?php
class ControllerExtensionModuleDigitalElephantFilterQuantityProduct extends Controller
{
    /**
     * @var ControllerExtensionModuleDigitalElephantFilterHelperUrl
     */
    private $helperUrl = null;

    /**
     * @var ControllerExtensionModuleDigitalElephantFilterGetProduct
     */
    private $getProduct = null;


    public function prototype()
    {
        return $this;
    }

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->helperUrl = $this->load->controller('extension/module/digital_elephant_filter/helper_url/prototype');
        $this->getProduct = $this->load->controller('extension/module/digital_elephant_filter/get_product/prototype');
    }

    public function render($count_click_show_more = 0)
    {
        $url = $this->helperUrl->getUrlData();
        $data_filter = $this->getProduct->getFilterDataByUrl($url);
        $product_total = $this->getProduct->getTotalProducts($data_filter);

        if ($count_click_show_more > 0) {
            $count_pages = $count_click_show_more + 1;
            $results = sprintf(
                $this->language->get('text_pagination'),
                ($product_total) ? (($url['page'] - $count_pages) * $url['limit']) + 1 : 0,
                ((($url['page'] - 1) * $url['limit']) > ($product_total - $url['limit'])) ? $product_total : ((($url['page'] - 1) * $url['limit']) + $url['limit']),
                $product_total,
                ceil($product_total / $url['limit'])
            );
        } else {
            $results = sprintf(
                $this->language->get('text_pagination'),
                ($product_total) ? (($url['page'] - 1) * $url['limit']) + 1 : 0,
                ((($url['page'] - 1) * $url['limit']) > ($product_total - $url['limit'])) ? $product_total : ((($url['page'] - 1) * $url['limit']) + $url['limit']),
                $product_total,
                ceil($product_total / $url['limit'])
            );
        }



        return $results;
    }

    public function ajaxRender()
    {
        $count_click_show_more = $this->request->get['count_click_show_more'];
        echo $this->render($count_click_show_more);
    }
}