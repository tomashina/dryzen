$(function(){
    //instantiation
    var DEFController = new DigitalElephantFilterController();
    initSliderPrice(DEFController);
    //instantiation END

    //CONTAINER PRODUCT REQUIRE STYLE
    $(DEFController.containerProducts.selector).css('position', 'relative');

    //toggle Filter
    $(document).on('click', DEFController.filterPanel.selectorElementTitle, function(){
        var $this = this;
        $(this).next('div').slideToggle(500, function(){
            DEFController.filterPanel.setStateSection($this);
        });
    });
    //toggle Filter End

    //ajax get products BEGIN
    if (DEFController.config.isButtonApply) {
        $(document).on('click', DEFController.filterPanel.selectorButtonApply, function(){
            DEFController.changeFilterData();
        });

    } else {
        $(document).on('change', DEFController.filterPanel.selectorControlElements, function(){
            DEFController.changeFilterData();
        });

    }

    //load products after change pagination
    $(document).on('click', DEFController.pagination.selectorLink, function(){
        var linkWidthNewPage = $(this).attr('href');
        DEFController.changePagination(linkWidthNewPage);
        return false;
    });

    //page reload
    if (DEFController.url.isFilterParamsInUrl()) {
        //page reloaded
        DEFController.pageReloaded();
    }

    //change limit;
    $(DEFController.limit.selector).attr('onchange', '');
    $(DEFController.limit.selector).change(function() {
        DEFController.changeLimit($(this).val());
    });

    //change sort
    $(DEFController.sort.selector).attr('onchange', '');
    $(DEFController.sort.selector).change(function(){
        DEFController.changeSort($(this).val());
    });

    //reset
    $(document).on('click', DEFController.filterPanel.selectorButtonClear, function() {
        DEFController.reset();
    });

    //Show more
    if (DEFController.buttonShowMore.isset()) {
        if (!DEFController.url.isFilterParamsInUrl()) {
            DEFController.buttonShowMore.render(DEFController.$_GET_string(), true);
        }

        $(document).on('click', DEFController.buttonShowMore.selector, function(){
            DEFController.clickOnButtonShowMore(this);
        })
    }
    //ajax get products END
});


function initSliderPrice(DEFController)
{
    if (DEFController.sliderPrice.isset()) {

        DEFController.sliderPrice.initValues();

        //price slider BEGIN
        $(DEFController.sliderPrice.selector).slider({
            slide: function (event, ui) {
                DEFController.sliderPrice.setChangePriceMin($(DEFController.sliderPrice.selector).slider("values", 0));
                DEFController.sliderPrice.setChangePriceMax($(DEFController.sliderPrice.selector).slider("values", 1));
            }
        });

        $(DEFController.sliderPrice.selector).slider({
            stop: function (event, ui) {
                DEFController.sliderPrice.setChangePriceMin($(DEFController.sliderPrice.selector).slider("values", 0));
                DEFController.sliderPrice.setChangePriceMax($(DEFController.sliderPrice.selector).slider("values", 1));

                if (!DEFController.config.isButtonApply) {
                    DEFController.changeFilterData();
                }
            }
        });

        //input keyword min
        $(document).on('change', DEFController.sliderPrice.selectorPriceMin, function () {
            DEFController.sliderPrice.changeMinSlideOnKeyword();
            //DEFController.ajaxGetProducts();
        });

        //input keyword max
        $(document).on('change', DEFController.sliderPrice.selectorPriceMax, function () {
            DEFController.sliderPrice.changeMaxSlideOnKeyword();
            //DEFController.ajaxGetProducts();
        });
        //price slider END
    }
}