/**
 * Created by Денис on 06.10.2016.
 */
//Class digitalElephantFilter PRICE
function DigitalElephantFilterSliderPrice() {
    DigitalElephantFilterSliderPrice.selfObj = null;
    this.selector = '#slider-range';
    this.selectorPriceMin = '#digitalElephantFilter_changing_price_min';
    this.selectorPriceMax = '#digitalElephantFilter_changing_price_max';

    this.isset = function() {
        return ($(this.selector).length > 0);
    };

    this.getPeakPriceMin = function () {
        return Number(DEFConfig.peakPrice.min);
    };

    this.getPeakPriceMax = function () {
        return Number(DEFConfig.peakPrice.max);
    };

    this.getCurrentPriceMin = function () {
        return Number(DEFConfig.currentPrice.min);
    };

    this.getCurrentPriceMax = function () {
        return Number(DEFConfig.currentPrice.max);
    };

    this.getChangePriceMin = function () {
        return Number($(this.selectorPriceMin).val());
    };

    this.getChangePriceMax = function () {
        return Number($(this.selectorPriceMax).val());
    };

    this.setChangePriceMin = function (price) {
        $(this.selectorPriceMin).val(price);
		$('#price-min-holder').text(price);
    };

    this.setChangePriceMax = function (price) {
        $(this.selectorPriceMax).val(price);
		$('#price-max-holder').text(price);
    };

    this.initValues = function () {

        var currentMin = this.getPeakPriceMin();
        if (this.getCurrentPriceMin() != '') {
            currentMin = this.getCurrentPriceMin();
        }

        var currentMax = this.getPeakPriceMax();
        if (this.getCurrentPriceMax() != '') {
            currentMax = this.getCurrentPriceMax();
        }

        //console.log(this.getPeakPriceMin());
        //console.log(this.getPeakPriceMax());

        $(this.selector).slider({
            min: this.getPeakPriceMin(),
            max: this.getPeakPriceMax(),
            values: [currentMin, currentMax],
            range: true
        });

        this.setChangePriceMin(currentMin);
        this.setChangePriceMax(currentMax);

        this.changeMinSlideOnKeyword();
        this.changeMaxSlideOnKeyword();
    };

    this.changeMinSlideOnKeyword = function () {
        if (parseInt(this.getChangePriceMin()) > parseInt(this.getChangePriceMax())) {
            this.setChangePriceMin(this.getChangePriceMax());
        }
        $(this.selector).slider("values", 0, this.getChangePriceMin());
    };

    this.changeMaxSlideOnKeyword = function () {

        if (this.getChangePriceMax() > this.getPeakPriceMax()) {
            this.setChangePriceMax(this.getPeakPriceMax());
        }

        if (parseInt(this.getChangePriceMin()) > parseInt(this.getChangePriceMax())) {
            this.setChangePriceMax(this.getChangePriceMin());
        }
        $(this.selector).slider("values", 1, this.getChangePriceMax());
    };

}
//Class digitalElephantFilter PRICE END

/**
 * Singletone
 * @return DigitalElephantFilterSliderPrice
 */
DigitalElephantFilterSliderPrice.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterSliderPrice();
    }

    return this.selfObj;
};