/**
 *
 * DigitalElephantFilterContainerProducts
 *
 * the class is responsible for ajax upload products
 */
function DigitalElephantFilterContainerProducts() {
    DigitalElephantFilterContainerProducts.selfObj = null;

    this.helper             = DigitalElephantFilterHelper.instance();
    this.url                = DigitalElephantFilterUrl.instance();
    this.pagination         = DigitalElephantFilterPagination.instance();
    this.buttonShowMore     = DigitalElephantFilterShowMore.instance();
    this.quantityProducts   = DigitalElephantFilterQuantityProducts.instance();
    this.filterPanel        = DigitalElephantFilterPanel.instance();
    this.sync               = DigitalElephantFilterSync.instance();
    this.limit              = DigitalElephantFilterLimit.instance();
    this.sort               = DigitalElephantFilterSort.instance();
    this.config             = DEFConfig;
    this.selector           = DEFConfig.selector.containerProducts;

    this.preloaderId = 'preloader-container-products';
    this.holdId      = 'hold-container-products';

    this.ajaxGetProducts = function ($_GET_string, isClickToShowMore) {

        this.url.changeUrlAddress($_GET_string);
        this.beforeProductsUpload($_GET_string, isClickToShowMore);

        var filterAction = $(this.filterPanel.selectorForm).attr('action');

        var $this = this;
        $.ajax({
            url: filterAction + $_GET_string + '&path=' + this.config.categoryPath,
            dataType: 'HTML',
            success: function(html) {

                var productsContent = $(html).find($this.selector).html();

                if (productsContent) {
                    if (isClickToShowMore) {
                        $($this.selector).append(productsContent);
                    } else {
                        $($this.selector).html(productsContent);
						// Basel
						$this.slideToTop();
                    }
                } else {
                    $($this.selector).html('<p>' + $this.config.text.productNotFound + '</p>');
                }
                $this.afterProductsUpload($_GET_string, isClickToShowMore);
            }
        });
    };

    this.beforeProductsUpload = function($_GET_string, isClickToShowMore) {
        this.quantityProducts.off();
        this.pagination.off();
        this.buttonShowMore.remove();

        if (isClickToShowMore) {
            this.buttonShowMore.addCountClick();
        } else {
            this.buttonShowMore.clearCountClick();
            this.preloaderOn();
            this.holdOn();
        }

        this.filterPanel.holdOn();
        this.limit.holdOn();
        this.sort.holdOn();


        //synchronized
        this.sync.preloaderOn();
    };

    this.afterProductsUpload = function($_GET_string, isClickToShowMore) {
        this.helper.setTypeView();

        this.filterPanel.render($_GET_string);

        if (this.buttonShowMore.isset()) {
            this.buttonShowMore.render($_GET_string);
        }

        if (this.pagination.isset()) {
            this.pagination.render($_GET_string);
        }

        if (this.quantityProducts.isset()) {
            this.quantityProducts.render($_GET_string, isClickToShowMore, this.buttonShowMore.getCountClick());
        }
		
        //synchronized
        this.sync.reset();
        this.syncAfterProductsUpload(isClickToShowMore);
		
		// Basel
		$('.product-style2 .single-product .icon').attr('data-placement', 'top');
		
		
    };

    this.syncAfterProductsUpload = function(isClickToShowMore) {

        var $this = this;
        var intervalId = setInterval(function() {
            if ($this.sync.isSyncCompleted(isClickToShowMore)) {
                clearInterval(intervalId);
                console.log('sync complete');

                //$this.preloaderOff();
                $this.sync.preloaderOff();
                $this.filterPanel.holdOff();
                $this.limit.holdOff();
                $this.sort.holdOff();
				
                $this.helper.smoothAppearance($this.buttonShowMore.selector);

                if ($this.pagination.isset()) {
                    $this.helper.smoothAppearance($this.pagination.selector);
                }

                if ($this.quantityProducts.isset()) {
                    $this.helper.smoothAppearance($this.quantityProducts.selector);
                }
            }
        }, 200);
		
		// Basel
		$('.basel-spinner.ajax-call').remove();
		$('[data-toggle=\'tooltip\']').tooltip('hide');
		
    };

    this.preloaderOn = function () {
        // Basel
		$('body').append('<span class="basel-spinner ajax-call"></span>');
		
		if ( ! $('div').is('#'+this.preloaderId)) {
            $(this.selector).append('<div class="row" id="' + this.preloaderId + '">' +
                    '<div><i class="' + this.config.preloaderClass + '"></i></div>' +
                '</div>');
        }

    };

    this.preloaderOff = function () {
        $('#' + this.preloaderId).remove();
    };

    this.holdOn = function() {
        $(this.selector).append('<div id="' + this.holdId + '" class="hold-on"></div>');
        $('#' + this.holdId).css('height', $(this.selector).height() + 'px');
    };

    this.holdOff = function() {
        $('#' + this.holdId).remove();
    };

    this.slideToTop = function() {
        var topProductWrap = $(this.selector).offset().top;
        $('html, body').animate({scrollTop: topProductWrap - 130}, '1200');
    };
}

/**
 * Singletone
 * @return DigitalElephantFilterContainerProducts
 */
DigitalElephantFilterContainerProducts.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterContainerProducts();
    }

    return this.selfObj;
};