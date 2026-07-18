/**
 * 06.10.2016.
 */
//Class digitalElephantFilter URL private methods
function DigitalElephantFilterUrlPrivate() {
    this.storageData = [];
    this.storageKeys = [];

    this.storageRemoveEmptyParams = null;

    this.stringToArray = function (url) {

        if (typeof url === 'string') {
            if (url.indexOf('&') == 0 || url.indexOf('?') == 0) {
                url = url.substr(1);
            }

            this.parse(url);
            var dataArray = this.storageData;
            this.reset();
            return dataArray;
        } else {
            throw new TypeError('Error type input format. DigitalElephantFilterUrl.stringToArray');
        }
    };

    this.parse = function (url) {
        var urlPart = '';
        var urlCut = '';
        if (url !== '') {
            if (url.indexOf('&') !== -1) {

                urlPart = url.substr(0, url.indexOf('&'));
                urlCut = url.substr(url.indexOf('&') + 1);

            } else {
                urlPart = url;
                urlCut = '';
            }

            var key = urlPart.substr(0, urlPart.indexOf('='));
            var value = urlPart.substr(urlPart.indexOf('=') + 1);

            this.setValueToStorageData(key, value);
            this.parse(urlCut);
        }
    };

    this.reset = function () {
        this.storageData = [];
    };

    this.setValueToStorageData = function(key, value) {
        if (key.search(/\[/) != -1) {
            //array
            this.setValueToArray(key, value)
        } else {
            //string
            this.storageData[key] = value;
        }
    };

    this.setValueToArray = function (key, value) {
            this.parseKeys(key);

            var mainKey = key.substr(0, key.indexOf('['));
            var result = this.storageKeys;

            if (typeof result[2] !== 'undefined') {
                if (typeof this.storageData[mainKey] === 'undefined') {
                    this.storageData[mainKey] = [];
                }
                if (typeof this.storageData[mainKey][result[0]] === 'undefined') {
                    this.storageData[mainKey][result[0]] = [];
                }
                if (typeof this.storageData[mainKey][result[0]][result[1]] === 'undefined') {
                    this.storageData[mainKey][result[0]][result[1]] = [value];
                } else {
                    this.storageData[mainKey][result[0]][result[1]].push(value);
                }
            } else if (typeof result[1] !== 'undefined') {
                if (typeof this.storageData[mainKey] === 'undefined') {
                    this.storageData[mainKey] = [];
                }
                if (typeof this.storageData[mainKey][result[0]] === 'undefined') {
                    this.storageData[mainKey][result[0]] = [value];
                } else {
                    this.storageData[mainKey][result[0]].push(value);
                }
            } else if (typeof result[0] !== 'undefined') {
                if (typeof this.storageData[mainKey] === 'undefined') {
                    this.storageData[mainKey] = [value];
                } else {
                    this.storageData[mainKey].push(value);
                }
            }
            this.storageKeys = [];
        };

    this.parseKeys = function(keys) {
        if (keys !== '') {

            if (keys.indexOf('[') !== -1) {

                var leftBreak = keys.indexOf('[');
                var rightBreak = keys.indexOf(']');

                var key = keys.substring(leftBreak + 1, rightBreak);

                this.storageKeys.push(key);

                this.parseKeys(keys.substr(keys.indexOf(']') + 1));
            }
        }
    };


    this.removeEmptyParams = function(link) {
        if (typeof link === 'string') {
            var result = link.match(/(?:(?:.*&)|^)(.*?=)(?:(?=&)|$)/);

            //if group match
            if (result !== null && typeof result[1] !== 'undefined') {
                link = link.replace(result[1], '');
                link = link.replace(/&&/, '&');
                link = link.replace(/&(?=$)/, '');
            }

            var searchMatch = link.match(/(?:(?:.*&)|^)(.*?=)(?:(?=&)|$)/);
            if (searchMatch !== null ) {
                this.removeEmptyParams(link);
            } else {
                this.storageRemoveEmptyParams = link;
            }
        } else {
            this.storageRemoveEmptyParams = '';
        }

        return this.storageRemoveEmptyParams;
    };
}
//Class digitalElephantFilter URL END