//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

var ro_common = (function($){
	let ro_common_methods = {	
		
		events_suffixes : {
			before 		: '.before',
			instead 	: '.instead',
			after 		: '.after',
			alterResult : '.alterResult', // after with result as the first parameter and with returning a new result
		},
		
		proxyObjectMethods : function(obj_to_proxy, debug, custom_methods) {
			
			if ( !obj_to_proxy.proxied ) {
				
				for ( let _method_name in obj_to_proxy ) {
					if ( !obj_to_proxy.hasOwnProperty(_method_name) ) continue;
					let _method = obj_to_proxy[_method_name];
					if ( typeof(_method) == 'function' ) {
						
						ro_common_methods.proxyObjectMethod(obj_to_proxy, _method_name, _method, debug, custom_methods);
						
					}
				}
				obj_to_proxy.proxied = true;
			}
			return obj_to_proxy;
		},
		
		debugInfo : function(data, debug) {
			if ( debug ) {
				console.debug(data);
			}
		},
		
		hasSomethingToProxy : function(_method_name, custom_methods) {
			
			let has_something_to_proxy = false;
			ro_common_methods.each(ro_common_methods.events_suffixes, function(suffix){
				if ( typeof(custom_methods[_method_name + suffix]) == 'function' ) {
					has_something_to_proxy = true;
					return false;//break
				}
			});
			return has_something_to_proxy;
		},
		
		proxyObjectMethod : function(obj_to_proxy, _method_name, _method, debug, custom_methods) {
			
			if ( ro_common_methods.hasSomethingToProxy(_method_name, custom_methods) ) {
				obj_to_proxy[_method_name] = function(){
					
					ro_common_methods.debugInfo('call proxied method: '+_method_name, debug);
					ro_common_methods.debugInfo(arguments, debug);
					
					if ( typeof(custom_methods[_method_name + ro_common_methods.events_suffixes.before]) == 'function' ) {
						ro_common_methods.debugInfo('call '+ro_common_methods.events_suffixes.before, debug);
						custom_methods[_method_name + ro_common_methods.events_suffixes.before].apply(this, arguments);
					}
					
					if ( typeof(custom_methods[_method_name + ro_common_methods.events_suffixes.instead]) == 'function' ) {
						ro_common_methods.debugInfo('call '+ro_common_methods.events_suffixes.instead, debug);
						return custom_methods[_method_name + ro_common_methods.events_suffixes.instead].apply(this, arguments);
					}
					
					ro_common_methods.debugInfo('call original', debug);
					
					var result = _method.apply(this, arguments);
					
					if ( typeof(custom_methods[_method_name + ro_common_methods.events_suffixes.after]) == 'function' ) {
						ro_common_methods.debugInfo('call '+ro_common_methods.events_suffixes.after, debug);
						custom_methods[_method_name + ro_common_methods.events_suffixes.after].apply(this, arguments);
					}
					
					if ( typeof(custom_methods[_method_name + ro_common_methods.events_suffixes.alterResult]) == 'function' ) {
						ro_common_methods.debugInfo('call '+ro_common_methods.events_suffixes.alterResult, debug);
						let args = ro_common_methods.argumentsToArray(arguments);
						args.unshift(result);
						result = custom_methods[_method_name + ro_common_methods.events_suffixes.alterResult].apply(this, args);
					}
					
					return result;
					
				};
			}
		},
		
		argumentsToArray : function(args) {
			let args_array = [];
			for (let i=0;i<args.length;i++) {
				args_array.push(args[i]);
			}
			return args_array;
		},
		
		addToArrayIfNotExists : function(val, arr) { // addToArrayIfNotExists
			if ( $.inArray(val, arr) == -1 ) {
				arr.push(val);
			}
		},
		
		getIntersectionOfArrays : function(arr1, arr2) { // uses order of the first array
			var match = [];
			$.each(arr1, function (i, val1) {
				if ($.inArray(val1, arr2) != -1 && $.inArray(val1, match) == -1) {
					match.push(val1);
				}
			});
			return match;
		},
		
		existsIntersectionOfArrays : function(arr1, arr2) {
			var result = false;
			ro_common_methods.each(arr1, function(item){
				if ( $.inArray(item, arr2) != -1 ) {
					result = true;
					return false;
				}
			});
			return result;
		},
		
		getConcatenationOfArraysUnique : function(arr1, arr2) {
			var arr = arr1.slice();
			ro_common_methods.each(arr2, function(value){
				if ( $.inArray(value, arr) == -1 ) {
					arr.push(value);
				}
			});
			return arr;
		},
		
		excludeItemsFromArray : function(arr, arr_to_exclude) {
			let new_arr = [];
			ro_common_methods.each(arr, function(value){
				if ( $.inArray(value, arr_to_exclude) == -1 ) {
					new_arr.push(value);
				}
			});
			return new_arr;
		},
		
		getOuterHTML : function($elem) {
			var str = $('<div>').append($elem.clone()).html();
			return str;
		},
		
		each : function(collection, fn){
			for ( var i_item in collection ) {
				if ( !collection.hasOwnProperty(i_item) ) continue;
				if ( fn(collection[i_item], i_item) === false ) {
					return;
				}
			}
		},
		
		getPoipInstaceByElement : function($elem) {
			while ( $elem.length ) {
				if ( $elem.data('poip_product') ) {
					return $elem.data('poip_product');
				}
				$elem = $elem.parent();
			}
		},
		
		externalOptionChangeEvent : function($elem){
			let poip_instance = ro_common_methods.getPoipInstaceByElement($elem);
			if ( poip_instance ) {
				poip_instance.externalOptionChange();
			}
		},
		
	};
	return ro_common_methods;
})(jQuery);