/**
 * Class DigitalElephantFilterSync
 * This is class synchronization show elements -
 * pagination, quantityProducts, buttonShowMore and contentProducts
 * after ajax uploaded
 */
function DigitalElephantFilterSync() {
    DigitalElephantFilterSync.selfObj = null;

    this.config = DEFConfig;
    this.storageLoadedElements = [];
    this.preloaderId = 'preloader-sync';
    //this.storageExpectedElements = null;

    this.isSyncCompleted = function(isClickToShowMore) {
        var countLoadedElements = this.getLoadedElements().length;
        return (this.getCountExpectedElements(isClickToShowMore) === countLoadedElements);
    };

    this.getCountExpectedElements = function () {

        var elements = [];

        elements.push('panel');

        if (this.config.state.isButtonShowMore) {
            elements.push('button_show_more');
        }

        if (this.config.state.isPagination) {
            elements.push('pagination');
        }

        if (this.config.state.isQuantityProducts) {
            elements.push('quantity_products');
        }

        return elements.length;
    };

    this.addLoadedElements = function(selector) {
        this.storageLoadedElements.push(selector);
    };

    this.getLoadedElements = function() {
        return this.storageLoadedElements;
    };

    this.reset = function() {
        this.storageLoadedElements = [];
    };

    this.preloaderOn = function() {
        if ($('#' + this.preloaderId).length == 0) {
            $(this.config.selector.containerProducts).after('' +
                '<div class="col-sm-12" id="' + this.preloaderId + '">' +
                '<i ' +
                'class="' + this.config.preloaderClass + '"' +
                'style="margin: auto; margin-top: 10px; display: block">' +
                '</div>');
        }
    };

    this.preloaderOff = function() {
        $('#' + this.preloaderId).remove();
    };
}

/**
 * Singletone
 * @return DigitalElephantFilterSync
 */
DigitalElephantFilterSync.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterSync();
    }

    return this.selfObj;
};