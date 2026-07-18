/**
 * Created by Денис on 06.10.2016.
 */
//Class digitalElephantFilter HELPER
function DigitalElephantFilterHelper() {
    DigitalElephantFilterHelper.selfObj = null;

    this.checkOnKeyArray = function (array, key) {
        return !(typeof array[key] == 'function' || typeof array[key] == 'undefined');
    };

    this.setTypeView = function () {
        if (localStorage.getItem('display') == 'list') {

            //$('#content .product-list').attr('class', 'product-layout col-xs-12');

            $('.product-layout').removeClass('product-grid');
            $('.product-layout').addClass('product-list');
        } else {

            var cols = $('#column-right, #column-left').length;

            if (cols == 2) {
                $('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
            } else if (cols == 1) {
                $('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
            } else {
                $('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
            }

            $('.product-layout').removeClass('product-list');
            $('.product-layout').addClass('product-grid');
        }
    };

    this.smoothAppearance = function(selector, delay) {
        if (typeof delay === 'undefined' || delay === '') {
            delay = 500;
        }
        $(selector).css('display', 'none');
        $(selector).fadeIn(delay);
        $(selector).css('display', 'block');
    }
}
//Class digitalElephantFilter HELPER END

/**
 * Singletone
 * @return DigitalElephantFilterHelper
 */
DigitalElephantFilterHelper.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterHelper();
    }

    return this.selfObj;
};