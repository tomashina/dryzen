function getURLQuery(url){
        var value = {};

        var query = String(url).split('?');

        if (query[1]) {
            var part = query[1].split('&');

            for (i = 0; i < part.length; i++) {
                var data = part[i].split('=');

                if (data[0] && data[1]) {
                    value[data[0]] = data[1];
                }
            }
        }
        
        return value;
}

function modifyURLQuery(url, param){
        var value = getURLQuery(url);
        
        $.extend(value, param);
        
        // Generate query parameter string
        var query_param = '';
        for (i in value){
            if(value[i]){
                query_param += '&' + i + '=' + value[i];
            }
        }
        
        var query = String(url).split('?');
        
        // Return url with modified parameter
        if(query_param){
            return query[0] + '?' + query_param.substring(1);
        } else {
            return query[0];
        }
}

$.fn.tf_filter = function(setting){
    // Default setting
    var default_setting = {
        delay: 2, // Second
        hideZeroFilter: false,
        requestURL: null,
        searchEl: null,
        ajax: true,
        search_in_description: true,
        no_result: '', // HTML for empty product result
        countProduct: null,
        sortBy: 'product',
        onParamChange: function(){},
        onInputChange: function(){},
        onReset: function(){},
        onResult: function(){}, // Result function for json
        onBeforeSend: function(){},
        onComplete: function(){},
        status: {
            price: 1,
            sub_category: 1,
            manufacturer: 1,
            search: 1,
            rating: 1,
            discount: 1,
            filter: 1,
            custom: 1,
            availability: 1
        }
    };
    
    setting = $.extend({}, default_setting, setting);
    
    var tf_filter = this;
    
    // Filter inputs
    this.inputs = $(this).find('input:not([type="search"])');
    
    // Start filter
    this.start = function(param){
        if(!setting.ajax){
            // Reload page with filter parameter
            window.location.href = modifyURLQuery(window.location.href, $.extend({}, param, {page: null}));
            return;
        }
        
        // Send request to filter products
        $.ajax({
            url: setting.requestURL,
            data: param,
            dataType: 'json',
            beforeSend: function(){
                $('body').addClass('tf-filter-loading');
                tf_filter.inputs.prop('disabled', 1);
                
                setting.onBeforeSend(); // Trigger event
            },
            success: function(json){
                tf_filter.inputs.prop('disabled', 0);
                
                setting.onResult(json); // Trigger event
                
                tf_filter.updateFilter(json['filter']); // Update filter
            },
            complete: function(){
                $('body').removeClass('tf-filter-loading');
                $(setting.loaderEl).hide();
                
                setting.onComplete(); // Trigger event
            }
        });
    };
    
    // Update filters values
    this.updateFilter = function(data){
            var inputs = tf_filter.find(':checkbox,:radio');
            
            inputs.filter(':not(:checked)').prop("disabled", 1); // Disable all unchecked input
            tf_filter.find('.tf-product-total').text('0').addClass('label-danger'); // Reset product count to zero
            
            // Manufacturer
            if(data['manufacturer']){
                var input_manufacturer = inputs.filter('[name="tf_fm"]');

                if (setting.hideZeroFilter) {
                    input_manufacturer.parents('.tf-filter-value').addClass('hide');
                }
                
                for(var i in data['manufacturer']){
                    input_manufacturer.filter('[value="' + data['manufacturer'][i]['manufacturer_id'] + '"]')
                            .prop('disabled', 0).parents('.tf-filter-value').removeClass('hide').find('.tf-product-total').text(data['manufacturer'][i]['total']).removeClass('label-danger');
                }

                if (data['manufacturer']) {
                    input_manufacturer.parents('.tf-filter-group').removeClass('hide');
                } else {
                    input_manufacturer.parents('.tf-filter-group').addClass('hide');
                }
                
                this.sort(input_manufacturer);
            }
            
            // Sub category
            if(data['sub_category']){
                var input_sub_category = inputs.filter('[name="tf_fsc"]');

                if (setting.hideZeroFilter) {
                    input_sub_category.parents('.tf-filter-value').addClass('hide');
                }
                
                for(var i in data['sub_category']){
                    input_sub_category.filter('[value="' + data['sub_category'][i]['category_id'] + '"]')
                            .prop('disabled', 0).parents('.tf-filter-value').removeClass('hide').find('.tf-product-total').text(data['sub_category'][i]['total']).removeClass('label-danger');
                }

                if (data['sub_category']) {
                    input_sub_category.parents('.tf-filter-group').removeClass('hide');
                } else {
                    input_sub_category.parents('.tf-filter-group').addClass('hide');
                }
                
                this.sort(input_sub_category);
            }
            
            // Availability
            if(data['availability']){
                var input_availability = inputs.filter('[name="tf_fs"]');
                
                if(data['availability']['in_stock'] > 0){
                    input_availability.filter('[value="1"]')
                            .prop('disabled', 0).parents('.tf-filter-value').find('.tf-product-total').text(data['availability']['in_stock']).removeClass('label-danger');
                }
                
                if(data['availability']['out_of_stock'] > 0){
                    input_availability.filter('[value="0"]')
                            .prop('disabled', 0).parents('.tf-filter-value').find('.tf-product-total').text(data['availability']['out_of_stock']).removeClass('label-danger');
                }
                    
            }

            // stock_status
            if(data['stock_status']){
                var input_stock_status = inputs.filter('[name="tf_fss"]');

                if (setting.hideZeroFilter) {
                    input_stock_status.parents('.tf-filter-value').addClass('hide');
                }
                
                for(var i in data['stock_status']){
                    input_stock_status.filter('[value="' + data['stock_status'][i]['stock_status_id'] + '"]')
                            .prop('disabled', 0).parents('.tf-filter-value').removeClass('hide').find('.tf-product-total').text(data['stock_status'][i]['total']).removeClass('label-danger');
                }

                if (data['stock_status']) {
                    input_stock_status.parents('.tf-filter-group').removeClass('hide');
                } else {
                    input_stock_status.parents('.tf-filter-group').addClass('hide');
                }
                
                this.sort(input_stock_status);
            }
            
            // Rating
            if(data['rating']){
                var input_rating = inputs.filter('[name="tf_fr"]');
                
                for(var i in data['rating']){
                    input_rating.filter('[value="' + data['rating'][i]['rating'] + '"]')
                            .prop('disabled', 0).parents('.tf-filter-value').find('.tf-product-total').text(data['rating'][i]['total']).removeClass('label-danger');
                }
            }
            
            // discount
            if(data['discount']){
                var input_discount = inputs.filter('[name="tf_fd"]');
                
                for(var i in data['discount']){
                    input_discount.filter('[value="' + data['discount'][i]['value'] + '"]')
                            .prop('disabled', 0).parents('.tf-filter-value').find('.tf-product-total').text(data['discount'][i]['total']).removeClass('label-danger');
                }
            }
            
            // Filter
            if(data['filter']){
                var input_filter = inputs.filter('[name="tf_ff"]');

                if (setting.hideZeroFilter) {
                    input_filter.parents('.tf-filter-value').addClass('hide');
                }
                
                for(var i in data['filter']){
                    input_filter.filter('[value="' + data['filter'][i]['filter_id'] + '"]')
                            .prop('disabled', 0).parents('.tf-filter-value').removeClass('hide').find('.tf-product-total').text(data['filter'][i]['total']).removeClass('label-danger');
                }

                $('.tf-filter-group.filter').each(function(){
                    if ($(this).find('.tf-filter-value:not(.hide)').length > 0) {
                        $(this).removeClass('hide');
                    } else {
                        $(this).addClass('hide');
                    }
                });
            }
            
            // Custom
            if(data['custom']){
                var input_custom = inputs.filter('[name^="tf_fc"]');

                if (setting.hideZeroFilter) {
                    input_custom.parents('.tf-filter-value').addClass('hide');
                }
                
                for(var i in data['custom']){
                    input_custom.filter('[value="' + data['custom'][i]['value_id'] + '"]')
                            .prop('disabled', 0).parents('.tf-filter-value').removeClass('hide').find('.tf-product-total').text(data['custom'][i]['total']).removeClass('label-danger');
                }
                
                $('.tf-filter-group.custom').each(function(){
                    if ($(this).find('.tf-filter-value:not(.hide)').length > 0) {
                        $(this).removeClass('hide');
                    } else {
                        $(this).addClass('hide');
                    }
                });
                
                $('[data-custom-filter]').each(function(){
                    tf_filter.sort(inputs.filter('[name="tf_fc' + $(this).data('custom-filter') + '"]'));
                });
            }
    };
    
    // Sort filter value
    this.sort = function(values){
        if(setting.countProduct && setting.sortBy === 'product'){
            var $wrapper = values.parents('.tf-filter-group-content');
            var sortByproduct = function(a, b){
                return Number($(a).parents('.tf-filter-value').find('.tf-product-total').text()) < Number($(b).parents('.tf-filter-value').find('.tf-product-total').text());
            };
            var sortByInput = function(a, b){
                return $(a).prop('disabled') > $(b).prop('disabled');
            };

            if(setting.countProduct){
                var callBack = sortByproduct;
            } else {
                var callBack = sortByInput;
            }

            values.sort(callBack).each(function(){
                $wrapper.append($(this).parents('.tf-filter-value'));
            });

            $wrapper.append($wrapper.find('[data-toggle="tf-seemore"]'));
        }
    };
    
    this.getParam = function(){
        var param = {};
        
        // price
        if(setting.status.price){ 
            var price = '';
            var min_price_input = tf_filter.inputs.filter('[name="tf_fp[min]"]');
            var max_price_input = tf_filter.inputs.filter('[name="tf_fp[max]"]');
            
            if(min_price_input.attr('min') !== min_price_input.val()){ // When minimum price change
                price +=  min_price_input.val();
            }
            
            if(max_price_input.attr('max') !== max_price_input.val()){ // When maximum price change
                price += 'p' + max_price_input.val();
            }
            
            if(price){
                param.tf_fp = price;
            }
        }
        
        // Search
        if(setting.status.search){
            var keyword = tf_filter.inputs.filter('[name="tf_fq"]').val();
            
            if(keyword){
                param.tf_fq = keyword;
                
                if(setting.search_in_description){
                    param.description = 1;
                }
            }
        }
        
        // Rating
        if(setting.status.rating){
            var min_rating = tf_filter.inputs.filter('[name="tf_fr"]:checked').val();
            
            if(min_rating){
                param.tf_fr = min_rating;
            }
        }
        
        // Discount
        if(setting.status.discount){
            var min_discount = tf_filter.inputs.filter('[name="tf_fd"]:checked').val();
            
            if(min_discount){
                param.tf_fd = min_discount;
            }
        }
        
        // Availability
        if(setting.status.availability){
            var in_stock = tf_filter.inputs.filter('[name="tf_fs"]:checked').val();
            
            if(in_stock !== undefined){
                param.tf_fs = in_stock;
            }
        }

        // stock_status
        if(setting.status.availability){
            var stock_status_ids = tf_filter.inputs.filter('[name="tf_fss"]:checked').map(function(){
                return $(this).val();
            }).get().join('.');
            
            if(stock_status_ids){
                param.tf_fss = stock_status_ids;
            }
        }
        
        // Manufacturer
        if(setting.status.manufacturer){
            var manufacturer_ids = tf_filter.inputs.filter('[name="tf_fm"]:checked').map(function(){
                return $(this).val();
            }).get().join('.');
            
            if(manufacturer_ids){
                param.tf_fm = manufacturer_ids;
            }
        }
        
        // Sub category
        if(setting.status.sub_category){
            var sub_category_ids = tf_filter.inputs.filter('[name="tf_fsc"]:checked').map(function(){
                return $(this).val();
            }).get().join('.');
            
            if(sub_category_ids){
                param.tf_fsc = sub_category_ids;
            }
        }
        
        // Filter
        if(setting.status.filter){
            var filter_ids = tf_filter.inputs.filter('[name="tf_ff"]:checked').map(function(){
                return $(this).val();
            }).get().join('.');
            
            if(filter_ids){
                param.tf_ff = filter_ids;
            }
        }
        
        // Custom
        if(setting.status.custom){
            var tf_fc = '';
            
            $('[data-custom-filter]').each(function(){
                var value_ids =  tf_filter.inputs.filter('[name="tf_fc' + $(this).data('custom-filter') + '"]:checked').map(function(){
                    return $(this).val();
                }).get().join('.');

                if(value_ids){
                    tf_fc += 'c' + value_ids;
                }
            });
            
            if(tf_fc){
                param.tf_fc = tf_fc.substring(1);
            }
        }
        
        return $.extend({
            tf_fp: null,
            tf_fq: null,
            tf_fr: null,
            tf_fd: null,
            tf_fs: null,
            tf_fss: null,
            tf_fm: null,
            tf_fsc: null,
            tf_ff: null,
            tf_fc: null
        }, param);
    };
    
    // Run task after user change filter
    tf_filter.on('change', function(){
        // Clear past timeout
        if(tf_filter.timeoutId !== undefined){
            clearTimeout(tf_filter.timeoutId);
        }
        
        // Get filter param
        var param = tf_filter.getParam();
        
        // Delay before to start filter
        tf_filter.timeoutId = setTimeout(function(){
            tf_filter.start(param);
        }, setting.delay * 1000);
        
        // Update page URL
        history.pushState(null, null, modifyURLQuery(window.location.href, $.extend({}, param, {page: null})));
        
        // Trigger param change event
        setting.onParamChange(param);
        
        
    });
    
    // Input change event
    this.inputs.on('change', function(e){
        setting.onInputChange(e);
    });
    
    // Search
    $(setting.searchEl).on('keyup', function(){
        var value = $(this).val().trim().toLowerCase();
        
        $(this).parents('.tf-filter-group').find('.form-check').filter(function(){
            var text = $(this).text().trim().toLowerCase();
            if(!text){ // Check in image
                text = $(this).find('img').attr('alt').trim().toLowerCase();
            }
            
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
    
    // See more
    $("[data-toggle='tf-seemore']").click(function(e){
        e.preventDefault();
        var wrapper = $(this).parent();
        if(wrapper.hasClass('show')){
            wrapper.removeClass('show');
            $(this).text($(this).data('show'));
        } else {
            wrapper.addClass('show');
            $(this).text($(this).data('hide'));
        }
    });
    
    // # Reset #
    // Radio and checkbox
    $('[data-tf-reset="check"]').on('click', function(e){
        e.stopImmediatePropagation();
        $(this).parents('.tf-filter-group').find('input').prop('checked', false);
        
        setting.onReset(this);
        tf_filter.change();
    });

    // Price
    $('[data-tf-reset="price"]').on('click', function(e){
        e.stopImmediatePropagation();
        
        $('[name="tf_fp[min]"]').val($('[name="tf_fp[min]"]').attr('min'));
        $('[name="tf_fp[max]"]').val($('[name="tf_fp[max]"]').attr('max'));
        
        setting.onReset(this);
        tf_filter.change();
    });

    // Text
    $('[data-tf-reset="text"]').on('click', function(e){
        e.stopImmediatePropagation();
        $(this).parents('.tf-filter-group').find('input').val('');
        
        setting.onReset(this);
        tf_filter.change();
    });

    // Reset all
    $(this).find('[data-tf-reset="all"]').on('click', function(e){
        e.stopImmediatePropagation();

        // Radio and checkbox
        $('.tf-filter-group :checkbox, .tf-filter-group :radio').prop('checked', false);
        
        // Text
        $('.tf-filter-group input[type="text"]').val('');

        // Price
        $('[name="tf_fp[min]"]').val($('[name="tf_fp[min]"]').attr('min'));
        $('[name="tf_fp[max]"]').val($('[name="tf_fp[max]"]').attr('max'));
        
        // Trigger events
        setting.onReset(this);
        tf_filter.change();
    });
};