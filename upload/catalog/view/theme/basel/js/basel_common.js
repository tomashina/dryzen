function getURLVar(key) {
	var value = [];
	var query = String(document.location).split('?');
	if (query[1]) {
		var part = query[1].split('&');
		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}
		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

function addCookie(name, value, days) {
    var date, expires;
    if (days) {
        date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        expires = "; expires="+date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = name+"="+value+expires+"; path=/";
}

$(document).ready(function() {
	
	// Add body ready class //
	$('body').addClass('document_ready');

	// Set page title position
	if($(".title_in_bc").length ) {
	$('#page-title').appendTo($("#title-holder").empty());
	}

	// Move breadcrumb to header //
	$('ul.breadcrumb').appendTo($('.links-holder span').empty());
	
	// Sticky header
	var sticky_to_top = $('.sticky-header').offset().top;

	console.log(sticky_to_top);
	var stickyheader = function(){
	var window_to_top = $(window).scrollTop();
		if (window_to_top > sticky_to_top ) { 
		$('body').addClass('sticky-active');
		} else {
		$('body').removeClass('sticky-active');
		};
		if (window_to_top > (sticky_to_top + 140)) {
		$('.sticky-header').addClass('short');
		} else {
		$('.sticky-header').removeClass('short');
		}
		if (window_to_top > 250) { 
		$('body').addClass('offset250');
		} else {
		$('body').removeClass('offset250');
		};
	};
	$(window).scroll(function() {
	stickyheader();
	});
	
	// Mobile menu open
	$(".menu-trigger").click(function(){
	$('html').addClass('no-scroll mobile-menu-open');
	$('.body-cover').addClass('active');
	});
	
	// Mobile menu close
	$(".menu-closer").click(function(){
	$('.body-cover').removeClass('active');
	$('html').removeClass('no-scroll mobile-menu-open side-filter-open');
	});
	
	// Mobile menu navigation
	$('.main-menu-wrapper > li.dropdown-wrapper > a .fa').click(function(e) {
	if ($(window).width() < 991) {
	e.preventDefault();
	$(this).parent().parent().siblings().find('>a').removeClass("open");
	$(this).parent().toggleClass("open").parent().find('>.dropdown-content').stop(true, true).slideToggle(350)
	.end().siblings().find('>.dropdown-content').slideUp(350);
	}
	});
	
	$('.main-menu-wrapper ul > li.has-sub > a .fa').click(function(e) {
	if ($(window).width() < 991) {
	e.preventDefault();
	$(this).parent().parent().siblings().find('>a').removeClass("open");
	$(this).parent().toggleClass("open").parent().find('>.sub-holder').stop(true, true).slideToggle(350)
	.end().siblings().find('>.sub-holder').slideUp(350);
	}
	});

	// Click drop down
	$(".dropdown-wrapper-click .clicker").click(function(){
	if ($(this).parent().hasClass('opened')) {
	$(this).parent().removeClass('opened');
	} else {
	$(".dropdown-wrapper-click").removeClass('opened');
	$(this).parent().addClass('opened');
	}
	});
	
	// Open external links in new tab //
	$('a.external').on('click',function(e){
	e.preventDefault();
	window.open($(this).attr('href'));
	});
	
	// Highlight any found errors
	$('.text-danger').each(function() {
		var element = $(this).parent().parent();
		if (element.hasClass('form-group')) {
			element.addClass('has-error');
		}
	});

	/* Search */
	$('.search-trigger').on('click', function() {setTimeout(function(object) {$('.main-search-input').focus();},500);});
	
	$(".main-search-input").focus(function(){$(this).parent().parent().addClass('focus');}).blur(function(){$(this).parent().parent().removeClass('focus');})
	
	$('.do-search').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';
		var value = $('.search-field input[name=\'search\']').val();
		if (value) {url += '&search=' + encodeURIComponent(value);}
		var category_id = $('select[name=\'category_id\']').prop('value');
		if (category_id > 0) {
		url += '&category_id=' + encodeURIComponent(category_id) + '&sub_category=true';
		}
		location = url;
	});
	$('.main-search-input').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('.do-search.main').trigger('click');
		}
	});
	/* Mobile Search */
	$('.search-holder-mobile input[name=\'search-mobile\']').parent().find('.fa-search').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';
		var value = $('.search-holder-mobile input[name=\'search-mobile\']').val();
		if (value) {url += '&search=' + encodeURIComponent(value);}
		location = url;
	});
	$('.search-holder-mobile input[name=\'search-mobile\']').on('keydown', function(e) {
	if (e.keyCode == 13) {$('.search-holder-mobile input[name=\'search-mobile\']').parent().find('.fa-search').trigger('click');}
	});
	
	// Keep Menu In Viewport
	var menu_viewport = function(){
	if ($(window).width() > 992) {
		$('.main-menu .dropdown-content').each(function() {
			var menu = $('.container').offset();
			var dropdown = $(this).parent().offset();
			var dropdown_wrapper = $(this).offset();
			
			// LTR Version
			var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('.container').outerWidth());
			if (i > 0) {
				$(this).css('margin-left', '-' + (i + 15) + 'px');
			} else {
				$(this).css('margin-left', '0px');
			}
			
			// RTL Version		
			var r = (menu.left - dropdown_wrapper.left);
			if (r > 0) {
				//$(this).css('margin-right', '-' + (r + 15) + 'px');
			} else {
				$(this).css('margin-right', '0px');
			}			
		});
	}};
	$(window).on("load resize",function(e){ menu_viewport();});
	
	// Language and currency select
	$('#language-select').on('change', function() {
  	$('#lang-code').attr( 'value', this.value ); $('#form-language').submit();
	});
	
	$('#currency-select').on('change', function() {
  	$('#curr-code').attr( 'value', this.value ); $('#form-currency').submit();
	});
	
	// Tooltip position on product style 2
	$('.product-style2 .single-product .icon').attr('data-placement', 'top');
	
	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
	});
	
	// Banner module 
	$('.cm_content .type-img .cm_item > *').click(function(e) {
	e.stopPropagation();
	});
	$('.banner_wrap').has('.hover-zoom').addClass('hover-zoom');
	$('.banner_wrap').has('.hover-darken').addClass('hover-darken');
	$('.banner_wrap').has('.hover-up').addClass('hover-up');
	$('.banner_wrap').has('.hover-down').addClass('hover-down');
	$('.banner_wrap').has('.hover-border').addClass('hover-border');
	
	// Product List/Grid
	$('#list-view').click(function() {
		$('#product-view').attr('class', 'notransition list');setTimeout(function () {$('#product-view').removeClass('notransition');}, 100);		
		localStorage.setItem('display', 'list');
	});
	$('#grid-view').click(function() {
		$('#product-view').attr('class', 'notransition grid');setTimeout(function () {$('#product-view').removeClass('notransition');}, 100);
		localStorage.setItem('display', 'grid');
	});
	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
	} else {
		$('#grid-view').trigger('click');
	}
	
	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});
	
});

// Quickview
var quickview = function(product_id) {
	$.ajax({
		url: 'index.php?route=extension/basel/quickview&product_id=' + product_id,
		type: 'post',
		dataType: 'html',
		beforeSend: function() {
              $('body').append('<span class="basel-spinner ajax-call"></span>');
           },
		success: function(html) {
			$('.basel-spinner.ajax-call').remove();
			$('[data-toggle=\'tooltip\']').tooltip('hide');
			$.featherlight(html);
		}
	});
}

// Newsletter Subscribe
var subscribe = function(module) {
	var $email = $('input[id=\'subscribe-module' + module + '\']');
	var $response = $('#subscribe-respond' + module + '');
	var $consent = $('#subscribe-consent-' + module);

	if ($consent.length && !$consent.is(':checked')) {
		$response.html('<span>' + ($consent.data('error') || '') + '</span>');
		setTimeout(function() {$response.find('span').fadeOut(500);}, 3000);
		return;
	}

	$.ajax({
		url: 'index.php?route=extension/basel/basel_features/subscribe&module=' + module,
		type: 'post',
		dataType: 'json',
		data: 'email=' + encodeURIComponent($email.val()) + ($consent.length ? '&consent=' + ($consent.is(':checked') ? '1' : '0') : ''),
		success: function(json) {
			if (json['error']) {
				$response.html('<span>' + json['error'] + '</span>');
				setTimeout(function() {$response.find('span').fadeOut(500);}, 3000);
			}
			if (json['success']) {
				$response.html('<span>' + json['success'] + '</span>');
				setTimeout(function() {$response.find('span').fadeOut(500);}, 5000);
				$email.val('');
				if ($consent.length) {
					$consent.prop('checked', false);
				}
			}}
	});
}
// Newsletter Unsubscribe
var unsubscribe = function(module) {
	$.ajax({
		url: 'index.php?route=extension/basel/basel_features/unsubscribe&module=' + module,
		type: 'post',
		dataType: 'json',
		data: 'email=' + encodeURIComponent($('input[id=\'subscribe-module' + module + '\']').val()),
		success: function(json) {
			if (json['error']) {
				$('#subscribe-respond' + module + '').html('<span>' + json['error'] + '</span>');
				setTimeout(function() {$('#subscribe-respond' + module + ' span').fadeOut(500);}, 3000);
			}
			if (json['success']) {
				$('#subscribe-respond' + module + '').html('<span>' + json['success'] + '</span>');
				setTimeout(function() {$('#subscribe-respond' + module + ' span').fadeOut(500);}, 5000);
				$('input[id=\'subscribe-module' + module + '\']').val('');
			}}
	});
}

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity, source) {
		$.ajax({
			url: 'index.php?route=extension/basel/basel_features/add_to_cart',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			
			beforeSend: function(json) {
			$('body').append('<span class="basel-spinner ajax-call"></span>');
			},
			
			success: function(json) {
			$('[data-toggle=\'tooltip\']').tooltip('hide');

				if (json['redirect']) {
					location = json['redirect'];
				}
				
				if (json['success_redirect']) {
					
					location = json['success_redirect'];
				
				} else if (json['success']) {
										
					$('.alert, .popup-note, .basel-spinner.ajax-call, .text-danger').remove();
					html = '<div class="popup-note">';
					html += '<div class="inner">';
					html += '<a class="popup-note-close" onclick="$(this).parent().parent().remove()">&times;</a>';
					html += '<div class="table">';
					html += '<div class="table-cell v-top img"><img src="' + json['image'] + '" /></div>';
					html += '<div class="table-cell v-top">' + json['success'] + '</div>';
					html += '</div>';
					html += '</div>';
					html += '</div>';
					$('body').append(html);
					setTimeout(function() {$('.popup-note').hide();}, 8100);
					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
					$('.cart-total-items').html( json['total_items'] );
					$('.cart-total-amount').html( json['total_amount'] );
					}, 100);

					$('#cart-content').load('index.php?route=common/cart/info #cart-content > *');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',

			success: function(json) {
					location = 'index.php?route=checkout/cart';
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=extension/basel/basel_features/remove_from_cart',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
				$('.cart-total-items').html( json['total_items'] );
				$('.cart-total-amount').html( json['total_amount'] );
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = 'index.php?route=checkout/cart';
				} else {
					$('#cart-content').load('index.php?route=common/cart/info #cart-content > *');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var voucher = {
	'add': function() {
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			
			success: function(json) {
				location = 'index.php?route=checkout/cart';
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=extension/basel/basel_features/add_to_wishlist',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
			$('[data-toggle=\'tooltip\']').tooltip('hide');	
				if (json['success_redirect']) {
					location = json['success_redirect'];
				} else if (json['success']) {
					$('.alert, .popup-note, .basel-spinner.ajax-call, .text-danger').remove();
					html = '<div class="popup-note">';
					html += '<div class="inner">';
					html += '<a class="popup-note-close" onclick="$(this).parent().parent().remove()">&times;</a>';
					html += '<div class="table">';
					html += '<div class="table-cell v-top img"><img src="' + json['image'] + '" /></div>';
					html += '<div class="table-cell v-top">' + json['success'] + '</div>';
					html += '</div>';
					html += '</div>';
					html += '</div>';
					$('body').append(html);
					setTimeout(function() {$('.popup-note').hide();}, 8100);
				}
				$('.wishlist-total span').html(json['total']);
				$('.wishlist-counter').html(json['total_counter']);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=extension/basel/basel_features/add_to_compare',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
			$('[data-toggle=\'tooltip\']').tooltip('hide');
				if (json['success_redirect']) {
					location = json['success_redirect'];
				} else if (json['success']) {
					$('.alert, .popup-note, .basel-spinner.ajax-call, .text-danger').remove();
					html = '<div class="popup-note">';
					html += '<div class="inner">';
					html += '<a class="popup-note-close" onclick="$(this).parent().parent().remove()">&times;</a>';
					html += '<div class="table">';
					html += '<div class="table-cell v-top img"><img src="' + json['image'] + '" /></div>';
					html += '<div class="table-cell v-top">' + json['success'] + '</div>';
					html += '</div>';
					html += '</div>';
					html += '</div>';
					$('body').append(html);
					setTimeout(function() {$('.popup-note').hide();}, 8100);
					$('#compare-total').html(json['total']);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {
	}
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function(e) {
	e.preventDefault();
	$('#modal-agree').remove();
	var element = this;
	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div';
			html += '  </div>';
			html += '</div>';
			$('body').append(html);
			$('#modal-agree').modal('show');
		}
	});
});
// Contact Form
var contact_form_send = function(form_id) {
	$.ajax({
		url: 'index.php?route=extension/basel/basel_features/basel_send_message',
		type: 'post',
		dataType: 'json',
		data: $('#mail_form' + form_id + '').serialize(),
		complete: function() {
			$('#mail_form' + form_id + ' .captchaimg').attr('src', 'index.php?route=extension/basel/basel_features/basel_captcha#'+new Date().getTime());
			$('#mail_form' + form_id + ' input[name=\'captcha\']').val('');
		},
		success: function(json) {
			$('#mail_form' + form_id + ' .respond').html('');
			if (json['error']) {
				$('#mail_form' + form_id + ' .respond').html('<p class="alert alert-danger">' + json['error'] + '</p>');
			}
			if (json['success']) {
				$('#mail_form' + form_id + ' .respond').html('<p class="alert alert-success">' + json['success'] + '</p>');
				$('#mail_form' + form_id + ' input').val('');
				$('#mail_form' + form_id + ' textarea').val('');
			}
		}
	});
};
// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();
			$.extend(this, option);
			$(this).attr('autocomplete', 'off');
			// Focus
			$(this).on('focus', function() {
				this.request();
			});
			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});
			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});
			// Click
			this.click = function(event) {
				event.preventDefault();
				value = $(event.target).parent().attr('data-value');
				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}
			// Show
			this.show = function() {
				var pos = $(this).position();
				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});
				$(this).siblings('ul.dropdown-menu').show();
			}
			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}
			// Request
			this.request = function() {
				clearTimeout(this.timer);
				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}
			// Response
			this.response = function(json) {
				html = '';
				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}
					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}
					// Get all the ones with a categories
					var category = new Array();
					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}
							category[json[i]['category']]['item'].push(json[i]);
						}
					}
					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}
				if (html) {
					this.show();
				} else {
					this.hide();
				}
				$(this).siblings('ul.dropdown-menu').html(html);
			}
			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));
		});
	}
})(window.jQuery);
