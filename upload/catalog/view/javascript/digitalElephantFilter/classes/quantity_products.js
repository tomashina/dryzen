function DigitalElephantFilterQuantityProducts() {
    DigitalElephantFilterQuantityProducts.selfObj = null;

    this.config = DEFConfig;
    this.selector = DEFConfig.selector.quantityProducts;
    this.sync   = DigitalElephantFilterSync.instance();

    this.render = function($_GET_string, isClickToShowMore, count_click_show_more) {

        if (typeof isClickToShowMore === 'undefined') {
            isClickToShowMore = false;
        }

        if (typeof isClickToShowMore === 'undefined' || isClickToShowMore === false || typeof count_click_show_more === 'undefined') {
            count_click_show_more = 0;
        }

        var $this = this;
        $(this.selector).load(
            this.config.action.ajaxRenderQuantityProducts + $_GET_string + '' +
            '&path=' + this.config.categoryPath + '' +
            '&count_click_show_more=' + count_click_show_more,
            function(result, status)
            {
                if (status === 'success') {
                    $this.off();
                    $this.sync.addLoadedElements($this.selector);
                }
            }
        );
    };

    this.isset = function() {
        return this.config.state.isQuantityProducts;
    };

    this.off = function() {
        $(this.selector).css('display', 'none');
    };
}

/**
 * Singletone
 * @return DigitalElephantFilterQuantityProducts
 */
DigitalElephantFilterQuantityProducts.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterQuantityProducts();
    }

    return this.selfObj;
};