<?php
class ControllerExtensionModuleDigitalElephantFilterPagination extends Controller
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

    public function render()
    {
        $url_string = $this->helperUrl->getUrlStringWithoutRoute();

        $url = $this->helperUrl->getUrlData();
        $data_filter = $this->getProduct->getFilterDataByUrl($url);
        $product_total = $this->getProduct->getTotalProducts($data_filter);

        $pagination = new Pagination();
        $pagination->total = $product_total;
        $pagination->page = $url['page'];
        $pagination->limit = $url['limit'];

        $url_string_without_page = $this->helperUrl->deleteParamFromUrlString($url_string, 'page');

        $pagination->url = $this->url->link('product/category', $url_string_without_page . '&page={page}');

        // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
        if ($url['page'] == 1) {
            $this->document->addLink($this->url->link('product/category', 'path=' . $url['category_id'], true), 'canonical');
        } elseif ($url['page'] == 2) {
            $this->document->addLink($this->url->link('product/category', 'path=' . $url['category_id'], true), 'prev');
        } else {
            $this->document->addLink($this->url->link('product/category', 'path=' . $url['category_id'] . '&page='. ($url['page'] - 1), true), 'prev');
        }

        if ($url['limit'] && ceil($product_total / $url['limit']) > $url['page']) {
            $this->document->addLink($this->url->link('product/category', 'path=' . $url['category_id'] . '&page='. ($url['page'] + 1), true), 'next');
        }

        return $pagination->render();
    }

    public function ajaxRender()
    {
        echo $this->render();
    }
}