
function ro_button_cart_control($button_add_to_cart) {
	if (!$button_add_to_cart.attr('allow_add_to_cart')) {
		let ro_instance = ro_getInstanceByElement( $button_add_to_cart );
		if ( ro_instance ) {
			ro_instance.spec_fn.stockControl(1, $button_add_to_cart);
			return false;
		}

	}
	$button_add_to_cart.attr('allow_add_to_cart','');
	return true;
}

function ro_getInstanceByElement($elem) {
	let $current_elem = $elem;
	while ( $current_elem.length && !$current_elem.data('liveopencart_relatedOptions') ) {
		$current_elem = $current_elem.parent();
	}
	return $current_elem.data('liveopencart_relatedOptions');
}

function ro_getSpecificFunctions(ro_instance) {
	return (function($){
		var ro_functions = {
			
			displayStockWarning : function(alert_message) {
				
				if ( typeof(ro_instance.spec_fn.custom_displayStockWarning) == 'function' ) {
					return ro_instance.spec_fn.custom_displayStockWarning(alert_message);
				}
				
				if ( !alert_message ) {
					$('#ro_stock_alert, [data-ro="stock-alert"]').remove();
				} else {
					if ( ro_instance.ro_theme_name == 'journal2' ) {
						$('#input-quantity').closest('.form-group').after('<div class="form-group" data-ro="stock-alert"><div class="text-danger">'+alert_message+'</div></div>');
					} else {
						if ( $('#input-quantity').length ) { // standard
							$('#input-quantity').parent().after('<div class="alert alert-warning" data-ro="stock-alert">' + alert_message + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						} else if ( $('#quantity_wanted').length ) { // themes like fastor
							$('#quantity_wanted').parent().parent().append('<div class="text-danger" data-ro="stock-alert">' + alert_message + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						}
					}
				}
			},
			
			displayStockWarningsQPO: function(pov_warnings) {
				
				let qpo_data_id = 'liveopencart_qpo';
				let qpo_instance = ro_instance.getElement('input[name^="quantity_per_option["]').first().parents().filter(function(){ return $(this).data(qpo_data_id); }).first().data(qpo_data_id);
				
				if ( qpo_instance && qpo_instance.displayQuantityWarnings ) {
					qpo_instance.displayQuantityWarnings(pov_warnings);
				}
				
			},
			
			stockControl : function(add_to_cart, $button_add_to_cart, start_now) { // ro_stock_control
			
				if ( ro_instance.timer_ro_stock_control ) {
					clearTimeout(ro_instance.timer_ro_stock_control);
				}
			
				if ( !ro_instance.ro_settings.stock_control ) {
					if (add_to_cart) {
						$button_add_to_cart.attr('allow_add_to_cart','allow_add_to_cart');
						$button_add_to_cart.click();
					}
					return;
				}
				
				if ( !start_now ) {
					ro_instance.timer_ro_stock_control = setTimeout(function(){ // to avoid multiple calls
						ro_functions.stockControl(add_to_cart, $button_add_to_cart, true);
					}, 50);
					return;
				}
			
				var erros_msg = ro_instance.ro_texts.entry_stock_control_error;
				
				ro_functions.displayStockWarning('');
				if ( ro_instance.ro_theme_name == 'journal2' ) {
					$('#ro_stock_alert, [data-ro="stock-alert"]').remove();
				}
				if ( $('#quantity_wanted').length ) { // themes like fastor
					$('#quantity_wanted').parent().parent().find('.text-danger').remove();
				}
				
				if ( ro_instance.ro_data && ro_instance.ro_data != [] && ro_instance.ro_data != {}) {
				
					ro_stock_control_last_call = (new Date()).getTime();
					let current_call = ro_stock_control_last_call;
				
					$.ajax({
							url: 'index.php?route=extension/liveopencart/related_options/getROFreeQuantities&ro_product_id='+ro_instance.product_id+'&call='+ro_stock_control_last_call,
							type: 'post',
							dataType: 'json',  
							data: $('select, input:radio:checked, input[type="text"]').filter('[name^="'+ro_instance.option_prefix+'"], [name^="quantity_per_option["]'),
							cache: false,      
							success: function (json) {
							
								if ( current_call != ro_stock_control_last_call ) {
									return;
								}
								
								if ( ro_instance.input_quantity_per_options.length ) {
									ro_functions.displayStockWarning('');
								}
							
								var allow_add_to_cart = true;
								if ( json && json.quantity !== false ) {
									let ro_quantity = json.quantity;
									let quantity = ro_instance.getQuantityInput().val();
									
									if (parseInt(ro_quantity) < parseInt(quantity) ) {
										allow_add_to_cart = false;
										let alert_message = erros_msg.replace('%s',parseInt(ro_quantity));
										
										ro_functions.displayStockWarning(alert_message);
									}
								}
								if ( json && typeof(json.quantity_per_option_value) != 'undefined' && json.quantity_per_option_value && Object.keys(json.quantity_per_option_value).length ) {
									ro_functions.displayStockWarning('');
									// specific notification for quantity input/select per option value
									let pov_warnings = [];
									ro_instance.each(json.quantity_per_option_value, function(max_quantity_per_value, product_option_value_id){
										pov_warnings.push({
											pov_id: product_option_value_id,
											alert_message: erros_msg.replace('%s',parseInt(max_quantity_per_value)),
										});
										allow_add_to_cart = false;
									});
									ro_functions.displayStockWarningsQPO(pov_warnings);

								} 
																			
								if (add_to_cart && allow_add_to_cart) {
									$button_add_to_cart.attr('allow_add_to_cart','allow_add_to_cart');
									$button_add_to_cart.click();
								} else {
									ro_instance.first_stock_control_only_on_add_to_cart = false; //control stock always after the first error/warning
								}
								
							},
							error: function(error) {
								console.log(error);
							}
					});
				} else { // if there's no selected related options combination - use standard algorithm
					if (add_to_cart) {
						$button_add_to_cart.attr('allow_add_to_cart','allow_add_to_cart');
						$button_add_to_cart.click();
					}
				}
			},
			
			updateModel : function(){ // ro_set_model
			
				var options_values = ro_instance.getOptionValues([]);
				var ro_ids = ro_instance.getSelectedROCombsByOptions(options_values);
				var product_model = ro_instance.ro_texts.product_model;
				var model = "";
				
				if (ro_ids.length) {
					ro_instance.each(ro_instance.ro_data, function(ro_dt){
						ro_instance.each(ro_ids, function(ro_id){
							if (ro_dt.ro[ro_id] && ro_dt.ro[ro_id].model) {
								if (ro_instance.ro_settings.spec_model == 1) {
									model = ro_dt.ro[ro_id].model;
								} else if (ro_instance.ro_settings.spec_model == 2 || ro_instance.ro_settings.spec_model == 3) {
									if ( ro_instance.ro_settings.spec_model_delimiter_ro && model ) {
										model+= ro_instance.ro_settings.spec_model_delimiter_ro;
									}
									model+= ro_dt.ro[ro_id].model;
								}
							}
						});
					});
				}
				
				if (model) {
					if (ro_instance.ro_settings.spec_model == 3) {
						if ( ro_instance.ro_settings.spec_model_delimiter_product ) {
							model = product_model + ro_instance.ro_settings.spec_model_delimiter_product + model;
						} else {
							model = product_model + model;
						}
					}
				} else {
					model = product_model;
				}
				ro_instance.getElement('[data-ro="product-model"], [data-ro="product_model"], #product_model').html(model);
			},
			
			updateUPC : function(){ // ro_set_model
			
				let $upc_elem = ro_instance.getElement('[data-ro="product-upc"], [data-ro="product_upc"]').first();
				let product_upc = typeof($upc_elem.attr('data-ro-original-html')) != 'undefined' ? $upc_elem.attr('data-ro-original-html') : $upc_elem.html();
				if ( typeof($upc_elem.attr('data-ro-original-html')) == 'undefined' ) {
					$upc_elem.attr('data-ro-original-html', $upc_elem.html());
				}
				let upc = product_upc;
				
				let ro_combs = ro_instance.getCurrentROCombs();
				
				ro_instance.each(ro_combs, function(ro_comb){
					if ( ro_comb.upc ) {
						upc = ro_comb.upc;
						return false;
					}
				});
				
				$upc_elem.html(upc);
			},
			
			updateStockInfo : function() { // ro_set_stock
				
				var stock = ro_instance.ro_texts.stock;
				var stock_status_color = '';
				let journal2_stock_status = ro_instance.ro_theme_name == 'journal2' ? ro_instance.ro_texts.stock_status : '';
				
				var options_values = ro_instance.getOptionValues([]);
				var ro_ids = ro_instance.getSelectedROCombsByOptions(options_values);
				let in_stock = true;
				
				let ro_comb_stock_found = false;
				
				if (ro_ids.length) {
					ro_instance.each(ro_instance.ro_data, function(ro_dt){
						
						ro_instance.each(ro_ids, function(ro_id){
						
							if (ro_dt.ro[ro_id] && ro_dt.ro[ro_id].stock) {
								
								stock = ro_dt.ro[ro_id].stock;
								
								in_stock = ro_dt.ro[ro_id].in_stock;
								
								if ( ro_dt.ro[ro_id].stock_status_color ) {
									stock_status_color = ro_dt.ro[ro_id].stock_status_color;
								}
								
								if (ro_instance.ro_theme_name == 'journal2') {
									journal2_stock_status = ro_dt.ro[ro_id].in_stock ? 'instock' : 'outofstock';
								}
								
								ro_comb_stock_found = true;
								return false;
							
							}
						});
						if ( ro_comb_stock_found ) {
							return false;
						}
					});
				}
				
				if (ro_instance.ro_theme_name == 'journal2' && journal2_stock_status) {
					//journal2 uses specific price and stock update, but it's slow and doesn't swith block class (style)
					$('#product .p-stock .journal-stock').removeClass('instock, outofstock').addClass(journal2_stock_status);
					$('#product .p-stock .journal-stock').html(stock);
				} else {
					ro_instance.getElement('[data-ro="product-stock"], #product_stock').html(stock);
				}
				
				if ( typeof(liveopencart_stock_status_color) != 'undefined' ) {
					liveopencart_stock_status_color.updateColor(stock_status_color);
				}
				
				if ( ro_instance.spec_fn && typeof(ro_instance.spec_fn.additional_updateStockInfoAfter) == 'function' && ro_comb_stock_found ) {
					// currently this function should exist only for one instance (product page)
					ro_instance.spec_fn.additional_updateStockInfoAfter(stock, in_stock);
				}
				
			},
			
			// Block Option & journal2 compatibility
			// show/hide enable/disable options block
			updateAvailabilityOfBlockAndJournalPushButtons : function() { // ro_check_visibility_of_block_options
			
				if (ro_instance.use_block_options || ro_instance.ro_theme_name == 'journal2') {
					
					var available_values = [];
					
					// block options use SELECTs for select & radio
					ro_instance.getOptionElement('select[name^="'+ro_instance.option_prefix+'["]').find('option').each( function () {
						
						if ($(this).val()) {
							if (ro_instance.hide_inaccessible) {
								available_values.push( $(this).val() );
							} else {
								if (! $(this).attr('disabled')) {
									available_values.push( $(this).val() );
								}
							}
						}
						
					});
					
					// block options use RADIOs for images
					ro_instance.getOptionElement('input[type=radio][name^="'+ro_instance.option_prefix+'["]').each( function () {
						
						if (ro_instance.hide_inaccessible) {
							if ($(this)[0].style.display != 'none') {
								available_values.push( $(this).val() );
							}
						} else {
							if (!$(this).attr('disabled')) {
								available_values.push( $(this).val() );
							}
						}
						
					});
					
					// Product Block Option Module
					if ( ro_instance.use_block_options) {
						ro_instance.getOptionElement('a[id^=block-option],a[id^=block-image-option]').each( function () {
							if ($.inArray($(this).attr('option-value'), available_values) == -1) {
								$(this).removeClass('block-active');
								if (ro_instance.hide_inaccessible) {
									$(this).hide();
								} else {
									if (!$(this).attr('disabled')) {
										$(this).attr('disabled', true);
										$(this).fadeTo("fast", 0.2);
									}
								}
							} else {
								if (ro_instance.hide_inaccessible) {
									$(this).show();
								} else {
									if ($(this).attr('disabled')) {
										$(this).attr('disabled', false);
										$(this).fadeTo("fast", 1);
									}
								}
							}
							
						} );
						ro_instance.getOptionElement('a[id^=color-option]').each( function () {
							if ($.inArray($(this).attr('optval'), available_values) == -1) {
								$(this).removeClass('color-active');
								if (ro_instance.hide_inaccessible) {
									$(this).hide();
								} else {
									if (!$(this).attr('disabled')) {
										$(this).attr('disabled', true);
										$(this).fadeTo("fast", 0.2);
									}
								}
							} else {
								if (ro_instance.hide_inaccessible) {
									$(this).show();
								} else {
									if ($(this).attr('disabled')) {
										$(this).attr('disabled', false);
										$(this).fadeTo("fast", 1);
									}
								}
							}
						} );
					}
					
					// Journal2
					if ( ro_instance.ro_theme_name == 'journal2' ) {
					
						$('#product').find('li[data-value]').each(function() {
							if ($.inArray($(this).attr('data-value'), available_values) == -1) {
								$(this).removeClass('selected');
								if (ro_instance.hide_inaccessible) {
									$(this).hide();
								} else {
									if (!$(this).attr('disabled')) {
										$(this).attr('disabled', true);
										$(this).fadeTo("fast", 0.2);
									}
								}
							} else {
								if (ro_instance.hide_inaccessible) {
									$(this).show();
								} else {
									if ($(this).attr('disabled')) {
										$(this).attr('disabled', false);
										$(this).fadeTo("fast", 1);
									}
								}
							}
							
							// change standart Journal2 function
							$(this).unbind('click');
							
							
							$(this).click(function () {
								if ($(this).attr('disabled')) {
									return;
								}
								var product_option_value_id = $(this).attr('data-value');
								
								$(this).siblings().removeClass('selected');
								$(this).addClass('selected');
								$(this).parent().siblings('select').find('option[value="' + product_option_value_id + '"]').attr('selected', 'selected');
								$(this).parent().siblings('select').trigger('change');
								
								$(this).parent().parent().find('.radio input[type=radio][name^="'+ro_instance.option_prefix+'"]').attr('checked', false);
								$(this).parent().parent().find('.radio input[type=radio][name^="'+ro_instance.option_prefix+'"][value="'+product_option_value_id+'"]').attr('checked', true).trigger('change');
								
								if (Journal.updatePrice) {
									Journal.updateProductPrice();
								}
								// compatibility with Product Option Image extension (by another developer)
								if ( typeof(selectvalue) == 'function' ) {
									selectvalue(product_option_value_id);
								}
								
							});
						});
					}
				}
			},
			
			// << EVENT/TRIGGER FUNCTIONS
			event_setOptionValue_after : function(event, product_option_id, product_option_value_id, $touched_elems) { // ro_event_setOptionValue_after
				
				if ( typeof(ro_instance.spec_fn.custom_event_setOptionValue_after) == 'function' && ro_instance.spec_fn.custom_event_setOptionValue_after(event, product_option_id, product_option_value_id, $touched_elems) ) {
					return;
				}
				
				ro_instance.common_fn.piodd_setValue(product_option_id, product_option_value_id);
				ro_instance.common_fn.journal2_makeOptionValueSelected(product_option_value_id);
				
				ro_functions.refresh_select_nicer(product_option_id);
			},
			
			event_init_after : function(event) { // ro_event_init_after
				
				ro_instance.getContainer().on('change', '[name^="quantity_per_option["]', function(){
					ro_functions.stockControl(0);
				});
				
				ro_instance.getQuantityInput().change(function(){
					if ( ro_instance.getQuantityInput().is(':visible') ) { // only if no QPO
						ro_functions.stockControl(0);
					}
				});
		  
				if ( ro_instance.spec_fn && typeof(ro_instance.spec_fn.additional_eventInitAfter) == 'function' ) {
					// currently this function should exist only for one instance (product page)
					ro_instance.spec_fn.additional_eventInitAfter(event);
				}
				
			},
			
			event_setAccessibleOptionValues_select_after : function (event, product_option_id) {
				ro_functions.refresh_select_nicer(product_option_id);
			},
			
			refresh_select_nicer : function(product_option_id) {
				let $select_element = ro_instance.getOptionElement('select[name="'+ro_instance.getOptionNameByPOId(product_option_id)+'"]');
				if ( $select_element.data('_styler') ) {
					$select_element.trigger('refresh');
				} else if ( $.fn.customSelect ) {
					setTimeout(function(){
						$select_element.trigger('render');
						$select_element.trigger('update'); // older customSelect version
					}, 1);
				}
			},
			
			event_setAccessibleOptionValues_radioUncheck_after : function(event, elem_to_uncheck) {
				if ( elem_to_uncheck.length ) {
					if ( ro_instance.ro_theme_name == 'fastor' && elem_to_uncheck.siblings('span').length && ( !elem_to_uncheck.is(':visible') || elem_to_uncheck.css('visibility') == 'hidden' ) ) {
					// specific selectors - button-style
						elem_to_uncheck.siblings('span').removeClass('active');
					}
					if ( elem_to_uncheck.data('iCheck') ) { // radio and checkboxes nicer
						elem_to_uncheck.iCheck();
					}
				}
				// << Product Image Option DropDown compatibility
				ro_instance.common_fn.piodd_setValue(option_id, '');
				// >> Product Image Option DropDown compatibility
			},
			
			event_setAccessibleOptionValues_radioToggle_after : function(event, option_id, $radio) {
				
				// << Product Image Option DropDown compatibility
				var piodd_option_div = $('#image-option-'+option_id);
				var piodd_value = piodd_option_div.find('ul.dd-options input.dd-option-value[value='+$radio.val()+']');
				if (piodd_value.length) {
					piodd_value.parent().toggle(!option_value_disabled);
				}
				// >> Product Image Option DropDown compatibility
				
			},
			
			event_setAccessibleOptionValues_radioEnableDisable_after : function(event, option_id, $radio) {
				
				// << Product Image Option DropDown compatibility
				// make copies of unavailable elements, originals hide in hidden div, when element became available again - place it back
				var piodd_option_div = $('#image-option-'+option_id);
				
				if ( piodd_option_div.find('ul.dd-options').length ) {
					
					var ro_hidden_div_id = piodd_option_div.attr('id')+'-ro-hidden';
					
					if ( !$('#'+ro_hidden_div_id).length ) {
						piodd_option_div.after('<div id="'+ro_hidden_div_id+'" style="display: none;"></div>');
					}
					var ro_hidden_div = $('#'+ro_hidden_div_id);
					
					var clone_id = 'clone_'+$radio.val();
					if (option_value_disabled) {
					
						var piodd_value = piodd_option_div.find('ul.dd-options input.dd-option-value[value='+$radio.val()+']');
						
						if (piodd_value.length) {
					
							if ( !piodd_option_div.find('[clone_id='+clone_id+']').length ) {
								var ro_clone = piodd_value.parent().clone(true, true).appendTo(ro_hidden_div);
								ro_clone.clone().insertAfter(piodd_value.parent()).attr('clone_id', clone_id).fadeTo('fast', 0.2);
								piodd_value.parent().remove();
							}
						}
						
					} else {
						if (ro_hidden_div.find('[value='+$radio.val()+']').length) {
							ro_hidden_div.find('[value='+$radio.val()+']').parent().clone(true, true).insertAfter(piodd_option_div.find('[clone_id='+clone_id+']'));
							ro_hidden_div.find('[value='+$radio.val()+']').parent().remove();
							piodd_option_div.find('[clone_id='+clone_id+']').remove();
						}
					}
					
				}
				// >> Product Image Option DropDown compatibility
				
			},
			
			event_setSelectedCombination_withAccessControl_after : function(event) {
				ro_instance.common_fn.updateViewOfBlockOptions();
				ro_instance.common_fn.updateViewOfJournalPushButtons();
			},
			
			event_controlAccessToValuesOfAllOptions_after : function(event) {
			
				if ( ro_instance.ro_settings.spec_model ) {
					ro_functions.updateModel();
				}
				
				if ( ro_instance.ro_settings.spec_upc ) {
					ro_functions.updateUPC();
				}
				
				if ( ro_instance.ro_settings.spec_ofs || ro_instance.ro_settings.spec_inss ) {
					ro_functions.updateStockInfo();
				}
				
				if ( !ro_instance.first_stock_control_only_on_add_to_cart ) {
					ro_functions.stockControl(0);
				}
				ro_functions.updateAvailabilityOfBlockAndJournalPushButtons();
			},
			
			// >> EVENT/TRIGGER FUNCTIONS
			
			// << CUSTOM FUNCTIONS
			custom_radioToggle : function( $radio, option_value_disabled ) {
				
			},
			
			custom_radioEnableDisable : function( $radio, option_value_disabled ) {
			
			},
			
			sstore_setOptionsStyles : function($radio){
				var $radio_checked = $('input:radio[name="'+$radio.attr('name')+'"]:checked');
				if ($radio.next().is('img') ) { // image
					var sstore_label_img = $radio.closest('label');
					if (sstore_label_img.length) {
						sstore_label_img.closest('.radio').parent().find('label.selected-img').removeClass('selected-img').addClass('not-selected-img');
						$radio_checked.closest('label.not-selected-img').removeClass('not-selected-img').addClass('selected-img');
					}
				} else { // radio
					$radio.siblings('label.selected').removeClass('selected').addClass('not-selected');
					$radio_checked.siblings('label[for="'+$radio_checked.attr('id')+'"].not-selected').removeClass('not-selected').addClass('selected');
				}
			},
	
			// >> CUSTOM FUNCTIONS
		};
		return ro_functions;
	})(jQuery);
}

