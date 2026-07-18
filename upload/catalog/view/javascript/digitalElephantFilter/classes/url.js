/**
 * 06.10.2016.
 */
//Class digitalElephantFilter URL
function DigitalElephantFilterUrl() {
    DigitalElephantFilterUrl.selfObj = null;

    this.urlPrivate = new DigitalElephantFilterUrlPrivate();
    this.config = DEFConfig;

    this.stringToArray = function (url) {
        return this.urlPrivate.stringToArray(url);
    };

    this.cutDomainInLink = function(link) {
        return link.replace(window.location.protocol, '');
    };

    this.cutFileInLink = function(link) {
        return link.replace(/(.)*?\.php/g, '');
    };

    this.cutGetParam = function(link, name) {
        var pattern = new RegExp('(&|\\?)' + name + '.*?(?=&|$)', 'g');
        return link.replace(pattern, '');
    };

    this.getGetParam = function(link, name) {
        var pattern = new RegExp('(&|\\?)' + name + '.*?(?=&|$)', 'g');
        var result = link.match(pattern);

        //console.log(result);

        if (result === null) {
            return '';
        } else {
            var output = '';

            result.forEach(function(item, i, arr) {
                output += item.replace(/\?/, '&');
            });
            return output;
        }
    };

    this.getGetParamValue = function(link, name) {
        var $_GET_array = this.stringToArray(link);

        var output = '';
        if (typeof $_GET_array[name] !== 'undefined') {
            output = $_GET_array[name];
        }

        return output;
    };

    this.getNativeParams = function() {
        var link = window.location.href;

        var page    = this.getGetParam(link, 'page');
        var limit   = this.getGetParam(link, 'limit');
        var sort    = this.getGetParam(link, 'sort');
        var order   = this.getGetParam(link, 'order');

        return page + limit + sort + order;
    };

    this.getFilterParamsByLink = function() {
        var link = window.location.href;

        var price           = this.getGetParam(link, 'price');
        var category        = this.getGetParam(link, 'category');
        var manufacturers   = this.getGetParam(link, 'manufacturers');
        var option          = this.getGetParam(link, 'option');
        var attribute       = this.getGetParam(link, 'attribute');
        var ajaxDEF         = this.getGetParam(link, 'ajax_digitalElephantFilter');

        return price + category + manufacturers + option + attribute + ajaxDEF;
    };

    this.setGetParam = function(link, name, value) {
        return link + '&' + name + '=' + value;
    };

    this.removeEmptyParams = function(link) {
        this.urlPrivate.storageRemoveEmptyParams = null;
        return this.urlPrivate.removeEmptyParams(link);
    };

    this.isFilterParamsInUrl = function() {
        var $_GET = window.location.href;
        return (this.getGetParam($_GET, 'ajax_digitalElephantFilter') != '');
    };

    this.changeUrlAddress = function ($_GET_string) {
        window.history.pushState('', '', this.config.action.category + $_GET_string);
    };
}
//Class digitalElephantFilter URL END

/**
 * Singletone
 * @return DigitalElephantFilterUrl
 */
DigitalElephantFilterUrl.instance = function() {
    if (this.selfObj == null) {
        this.selfObj = new DigitalElephantFilterUrl();
    }

    return this.selfObj;
};