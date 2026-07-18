function DigitalElephantFilterPagination() {
    DigitalElephantFilterPagination.selfObj = null;

    this.config = DEFConfig;
    this.selector = DEFConfig.selector.pagination;
    this.selectorLink = DEFConfig.selector.pagination + ' a';
    this.sync   = DigitalElephantFilterSync.instance();

    this.off = function() {
        $(this.selector).css('display', 'none');
    };

    this.render = function($_GET_string) {
        var $this = this;
        $(this.selector).load(this.config.action.ajaxRenderPagination + $_GET_string + '&path=' + this.config.categoryPath, function(result, status){
            if (status === 'success') {
                $this.off();
                $this.sync.addLoadedElements($this.selector);
            }
        });
    };

    this.preloaderOn = function() {
        $(this.selector).html('' +
            '<i ' +
            'class="' + this.config.preloaderClass + '"' +
            'style="margin: auto; margin-top: 10px; display: block;">');
    };

    this.isset = function() {
        return this.config.state.isPagination;
    }
}

/**
 * Singletone
 * @return DigitalElephantFilterPagination
 */
DigitalElephantFilterPagination.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterPagination();
    }

    return this.selfObj;
};