/**
 * structure $_GET_string:
    * native_params + filter_params
 *
 * structure native_params:
     * &limit=param
     * &page=param
     * &order=param
     * &sort=param
 *
 * structure filter_params:
     * &price[min]=param
     * &price[max]=param
     * &category[categories][] = param
     * &manufacturers[manufacturers][] = param
     * &option[option_id][] = param
     * &attribute[attribute_id][] = param
 */

function DigitalElephantFilterController() {
    this.sliderPrice        = DigitalElephantFilterSliderPrice.instance();
    this.url                = DigitalElephantFilterUrl.instance();
    this.pagination         = DigitalElephantFilterPagination.instance();
    this.buttonShowMore     = DigitalElephantFilterShowMore.instance();
    this.quantityProducts   = DigitalElephantFilterQuantityProducts.instance();
    this.containerProducts  = DigitalElephantFilterContainerProducts.instance();
    this.filterPanel        = DigitalElephantFilterPanel.instance();
    this.sync               = DigitalElephantFilterSync.instance();
    this.limit              = DigitalElephantFilterLimit.instance();
    this.sort               = DigitalElephantFilterSort.instance();
    this.config             = DEFConfig;

    this.$_GET_string = function() {
        if (this.url.isFilterParamsInUrl) {
            var filterParams = this.url.removeEmptyParams(this.getFilterParams());
            return '&' + filterParams + this.url.getNativeParams();
        } else {
            return this.url.getNativeParams();
        }
    };

    this.getFilterParams = function() {
        return $(this.filterPanel.selectorForm).serialize();
    };

    this.changeFilterData = function() {
        var $_GET_string = this.$_GET_string();
        $_GET_string = this.url.cutGetParam($_GET_string, 'page');

        this.containerProducts.ajaxGetProducts($_GET_string);
    };

    this.changeLimit = function (linkWithLimit) {
        var $_GET_string = this.$_GET_string();

        $_GET_string = this.url.cutGetParam($_GET_string, 'page');
        $_GET_string = this.url.cutGetParam($_GET_string, 'limit');

        var limit = this.url.getGetParam(linkWithLimit, 'limit');

        $_GET_string = $_GET_string + limit;

        this.containerProducts.ajaxGetProducts($_GET_string);
    };

    this.changeSort = function (linkWithSort) {
        var $_GET_string = this.$_GET_string();

        $_GET_string = this.url.cutGetParam($_GET_string, 'page');
        $_GET_string = this.url.cutGetParam($_GET_string, 'sort');
        $_GET_string = this.url.cutGetParam($_GET_string, 'order');

        var sort = this.url.getGetParam(linkWithSort, 'sort');
        var order = this.url.getGetParam(linkWithSort, 'order');

        $_GET_string = $_GET_string + sort + order;

        this.containerProducts.ajaxGetProducts($_GET_string);
    };

    this.clickOnButtonShowMore = function (currentButton) {
        var $_GET_string = this.$_GET_string();

        var currentPage = $(currentButton).attr('data-page');
        var nextPage = Number(currentPage) + 1;

        $_GET_string = this.url.cutGetParam($_GET_string, 'page');
        $_GET_string = this.url.setGetParam($_GET_string, 'page', nextPage);

        this.containerProducts.ajaxGetProducts($_GET_string, true);
    };

    this.changePagination = function(linkWithNewPage) {
        var $_GET_string = this.$_GET_string();

        $_GET_string = this.url.cutGetParam($_GET_string, 'page');

        var page = this.url.getGetParam(linkWithNewPage, 'page');

        $_GET_string = $_GET_string + page;

        this.containerProducts.slideToTop();
        this.containerProducts.ajaxGetProducts($_GET_string);
    };

    this.pageReloaded = function() {
        var $_GET_string = this.url.getFilterParamsByLink() + this.url.getNativeParams();

        this.containerProducts.ajaxGetProducts($_GET_string);
    };

    this.reset = function () {
        $(this.filterPanel.selectorForm + ' input').removeAttr('checked');
        $(this.filterPanel.selectorForm + ' select option').removeAttr('selected');

        this.sliderPrice.setChangePriceMin(this.sliderPrice.getPeakPriceMin());
        this.sliderPrice.setChangePriceMax(this.sliderPrice.getPeakPriceMax());

        this.sliderPrice.changeMinSlideOnKeyword();
        this.sliderPrice.changeMaxSlideOnKeyword();

        var $_GET_string = this.$_GET_string();
        $_GET_string = this.url.cutGetParam($_GET_string, 'page');

        this.containerProducts.ajaxGetProducts($_GET_string);
    };
}
//ajax get products END