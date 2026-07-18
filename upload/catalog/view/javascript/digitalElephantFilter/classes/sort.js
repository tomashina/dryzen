function DigitalElephantFilterSort() {
    DigitalElephantFilterSort.selfObj = null;
    this.selector = DEFConfig.selector.sort;

    this.holdOn = function() {
        $(this.selector).attr('disabled', 'disabled');
    };

    this.holdOff = function() {
        $(this.selector).removeAttr('disabled');
    };
}

/**
 * Singletone
 * @return DigitalElephantFilterSort
 */
DigitalElephantFilterSort.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterSort();
    }

    return this.selfObj;
};