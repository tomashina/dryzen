function liveopencartInitRO($container, ro_params, ro_data) {
	
	let initRO = function(){
		
		let ro_instance = $container.liveopencart_RelatedOptions(ro_params);
		
		ro_instance.common_fn = ro_getCommonFunctions(ro_instance);
		ro_instance.initCommonFunctions();
		if ( typeof(ro_setThemeCommonFunctions) == 'function' ) {
			ro_setThemeCommonFunctions(ro_instance);
		}
		ro_instance.common_fn.initBasic();
		
		var spec_fn = ro_getSpecificFunctions(ro_instance);
		
		ro_instance.spec_fn = spec_fn;
		ro_instance.initSpecFunctions();
		if ( typeof(ro_setThemeSpecificFunctions) == 'function' ) {
			ro_setThemeSpecificFunctions(ro_instance);
		}
			
		// << the part when the product has related options
		if ( ro_data ) {
			// to custom
			ro_instance.use_block_options = ($('a[id^=block-option][option-value]').length || $('a[id^=block-image-option][option-value]').length || $('a[id^=color-][optval]').length);
			
			ro_instance.bind('init_after.ro', ro_instance.spec_fn.event_init_after);
			ro_instance.bind('setAccessibleOptionValues_select_after.ro', ro_instance.spec_fn.event_setAccessibleOptionValues_select_after);
			ro_instance.bind('setAccessibleOptionValues_radioUncheck_after.ro', ro_instance.spec_fn.event_setAccessibleOptionValues_radioUncheck_after);
			ro_instance.bind('setAccessibleOptionValues_radioToggle_after.ro', ro_instance.spec_fn.event_setAccessibleOptionValues_radioToggle_after);
			ro_instance.bind('setAccessibleOptionValues_radioEnableDisable_after.ro', ro_instance.spec_fn.event_setAccessibleOptionValues_radioEnableDisable_after);
			ro_instance.bind('setSelectedCombination_withAccessControl_after.ro', ro_instance.spec_fn.event_setSelectedCombination_withAccessControl_after);
			ro_instance.bind('controlAccessToValuesOfAllOptions_after.ro', ro_instance.spec_fn.event_controlAccessToValuesOfAllOptions_after);
			
		}
		// >> the part when the product has related options
		
		ro_instance.bind('setOptionValue_after.ro', ro_instance.spec_fn.event_setOptionValue_after);
		
		//ro_instance.spec_fn = spec_fn;
		//ro_instance.initSpecFunctions();
		//if ( typeof(ro_setThemeSpecificFunctions) == 'function' ) {
		//	ro_setThemeSpecificFunctions(ro_instance);
		//}
		
		if ( ro_data ) {
			ro_instance.custom_radioToggle = spec_fn.custom_radioToggle;
			ro_instance.custom_radioEnableDisable = spec_fn.custom_radioEnableDisable;
			ro_instance.sstore_setOptionsStyles = spec_fn.sstore_setOptionsStyles;
		}
		
		ro_instance.initRO();
	};
	
	if ( $container.liveopencart_RelatedOptions ) {
		initRO();
	} else {
		$().ready(function(){
			initRO();
		});
	}
	
}