function DigitalElephantFilterLimit() {
    DigitalElephantFilterLimit.selfObj = null;
    this.selector = DEFConfig.selector.limit;

    this.holdOn = function() {
        $(this.selector).attr('disabled', 'disabled');
    };

    this.holdOff = function() {
        $(this.selector).removeAttr('disabled');
    };
}

/**
 * Singletone
 * @return DigitalElephantFilterLimit
 */
DigitalElephantFilterLimit.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterLimit();
    }

    return this.selfObj;
};