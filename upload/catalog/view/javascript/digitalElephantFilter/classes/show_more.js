function DigitalElephantFilterShowMore() {
    DigitalElephantFilterShowMore.selfObj = null;

    this.helper = DigitalElephantFilterHelper.instance();
    this.url    = DigitalElephantFilterUrl.instance();
    this.sync   = DigitalElephantFilterSync.instance();
    this.config = DEFConfig;

    this.id = 'button-show-more';
    this.selector = '#button-show-more';
    this.preloaderId = 'preloader-show-more';
    this.storageCountClick = 0;

    this.render = function($_GET_string, isNativePage) {
        var $_GET_string = $_GET_string + '&path=' + this.config.categoryPath;
        var $_GET_array = this.url.stringToArray($_GET_string);

        var page = 1;
        if (typeof $_GET_array['page'] !== 'undefined' && $_GET_array['page'] != '') {
            page = $_GET_array['page'];
        }

        var $this = this;
        $.ajax({
            url: $this.config.action.ajaxCheckToRenderShowMore,
            dataType: 'JSON',
            data: $_GET_string,
            success: function(json) {
                if (json.success) {
                    if ($($this.selector).length > 0) {
                        $this.remove();
                    }
                    $($this.config.selector.containerProducts).after('<div class="col-sm-12 text-center" style="padding:10px 0;">' +
                        '<button ' +
                        'id="' + $this.id + '"' +
                        'class="btn btn-outline btn-block" ' +
                        'data-page="' + page + '"' +
                        '>' +
                        '' + $this.config.text.buttonShowMore + '' +
                        '</button>' +
                        '</div>');
                }

                $this.off();
                if (isNativePage) {
                    $this.helper.smoothAppearance($this.selector);
                } else {
                    $this.sync.addLoadedElements($this.selector);
                }
            }
        });
    };

    this.preloaderOn = function() {
        $(this.selector).html('' +
            '<div class="col-sm-12" id="' + this.preloaderId + '">' +
            '<i ' +
            'class="' + this.config.preloaderClass + '"' +
            '</div>');
    };

    this.preloaderOff = function() {
        $('#' + this.preloaderId).remove();
    };

    this.isset = function() {
        return this.config.state.isButtonShowMore;
    };

    this.remove = function() {
        $(this.selector).parent('div').remove();
    };

    this.addCountClick = function() {
        this.storageCountClick++;
    };

    this.getCountClick = function() {
        return this.storageCountClick;
    };

    this.clearCountClick = function() {
        this.storageCountClick = 0;
    };

    this.off = function() {
        $(this.selector).css('display', 'none');
    };
}

/**
 * Singletone
 * @return DigitalElephantFilterShowMore
 */
DigitalElephantFilterShowMore.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterShowMore();
    }

    return this.selfObj;
};