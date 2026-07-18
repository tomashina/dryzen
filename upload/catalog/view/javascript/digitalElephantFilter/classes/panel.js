function DigitalElephantFilterPanel() {
    DigitalElephantFilterPanel.selfObj = null;

    this.selector = '#digitalElephantFilter_box';
    this.selectorForm = '#digitalElephantFilter_form';
    this.selectorButtonApply = '#digitalElephantFilter_button_apply';
    this.selectorButtonClear = 'input[type="reset"]';
    this.selectorElementTitle = '.filter_element_title';
    this.selectorControlElements = '#digitalElephantFilter_box input, #digitalElephantFilter_box select';
    this.sync   = DigitalElephantFilterSync.instance();
    this.config = DEFConfig;

    this.holdId = 'hold-filter-panel';

    this.render = function($_GET_string) {
        var $this = this;

        $.ajax({
            url: $this.config.action.ajaxRenderPanel + $_GET_string + '&path=' + this.config.categoryPath,
            dataType: 'HTML',
            success: function(html) {
                $($this.selector).replaceWith(html);
                $this.sync.addLoadedElements($this.selector);
            }
        });
    };

    this.holdOn = function() {
        $(this.selector).append('<div id="' + this.holdId + '" class="hold-on"></div>');
        $('#' + this.holdId).css('height', $(this.selector).height() + 'px');
        $('#' + this.holdId).css('top', '0');
    };

    this.holdOff = function() {
        $('#' + this.holdId).remove();
    };

    this.setStateSection = function(selectorSectionTitle) {
        var name = $(selectorSectionTitle).attr('data-section-name');
        var id = $(selectorSectionTitle).attr('data-section-id');
        var state = 1;

        if ($(selectorSectionTitle).next('div').is(':hidden')) {
            $(selectorSectionTitle).next('div').removeClass('is_opened');
			$(selectorSectionTitle).addClass('is_closed');
            state = 0;
        } else {
            $(selectorSectionTitle).next('div').addClass('is_opened');
			$(selectorSectionTitle).removeClass('is_closed');
            state = 1;
        }

        var $this = this;

        var data = {
            section: {
                state: state,
                name: name,
                id: id
            }
        };

        $.ajax({
            url: $this.config.action.ajaxSetStateSection,
            data: data,
            dataType: 'html',
            method: 'post',
            success: function(html) {
                console.log(html);
            }
        });
    };
}

/**
 * Singletone
 * @return DigitalElephantFilterPanel
 */
DigitalElephantFilterPanel.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterPanel();
    }

    return this.selfObj;
};