
var ro_vueAppOptions = {
	delimiters: ['${', '}'],
	el: '#form-related-options',
	data: {
		use_variants: false,
		edit_separately: false, // edit in list (by default) or edit separately (optional)

		edit_single: false,
		//saving_status: false,
		
		url_edit_variant: '',
		
		options: [],
		variants: [],
		
		num_variants_per_page: 50,
		current_page: 1,
	},
	
	methods: {
		
		sprintf: function(){
			let str = '';
			let parts = arguments[0].split('%s');
			str+= parts[0];
			for (part_i=1;part_i<parts.length;part_i++) {
				str+= (arguments[part_i] || '%s');
				str+= parts[part_i];
			}
			return str;
		},
		
		showVariantFields: function(variant_i){
			//this.variants[variant_i].show_fields = true;
			this.$set(this.variants[variant_i], 'show_fields', true);
		},
		
		setUseVariantsStatus: function(use_variants){
			this.use_variants = use_variants;
		},
		
		//updateUseVariantsStatus: function(){
		//	let use_variants = $('#ro_use_variants').is(':checked');
		//	if (use_variants != this.use_variants) {
		//		this.use_variants = use_variants;
		//	}
		//},
		
		loadOptions: function(options) {
			let this_app = this;
			$.each(options, function(option_i, option){
				this_app.options.push(option);
			});
		},
		
		loadVariants: function(variants) {
			let this_app = this;
			$.each(variants, function(v_i, variant){
				this_app.addVariant(variant, true);
			});
		},
		
		getMaxSortOrder: function(){
			let sort_order = 0;
			this.variants.forEach(function(variant){
				sort_order = Math.max(sort_order, parseInt(variant.sort_order) || 0);
			});
			return sort_order;
		},
		
		addVariant: function(p_variant, no_page_change) {
			
			if (!p_variant && this.edit_separately) {
				editVariantInPopup();
			} else {
			
				let variant = $.extend(true, {
					options: [],
					fields: [],
					sort_order: this.getMaxSortOrder()+1,
				}, p_variant || {});
				
				this.variants.push(variant);
				let variant_i = this.variants.length-1;
				if ( !no_page_change && !this.isVariantOnCurrentPage(variant_i) ) {
					this.setCurrentPage(this.getVariantPage(variant_i));
				}
			}
		},
		
		addOrUpdateVariant: function(variant) {
			let index = this.getVariantIndexByROVId(variant.rov_id);
			if (index == -1) {
				this.addVariant(variant);
			} else {
				this.variants.splice(index, 1, $.extend(true, {}, variant)); // [index]= is not reactive
			}
		},
		
		removeVariant: function(variant_i) {
			
			this.variants.splice(variant_i, 1);
			this.updateCurrentPage();
		},
		
		getVariantIndexByROVId: function(rov_id) {
			let index = -1;
			if (rov_id) {
				let variants_indexes_to_replace = [];
				this.variants.forEach(function(variant, variant_i){
					if (variant.rov_id == rov_id) {
						variants_indexes_to_replace.push(variant_i);
					}
				});
				if (variants_indexes_to_replace.length) { // supposed to be only one
					index = variants_indexes_to_replace[0];
				}
			}
			return index;
		},
		
		removeVariantByROVId: function(rov_id) {
			
			let index = this.getVariantIndexByROVId(rov_id);
			if (index != -1) {
				this.removeVariant(index);
			}
		},
		
		removeVariantConfirm: function(variant_i, event) {
			
			let variant = this.variants[variant_i];
			if ( variant.rov_id ) {
				if ( !confirm( this.sprintf(ro_texts.text_confirm_variant_removing, (parseInt(variant.ro_combs_cnt) ? parseInt(variant.ro_combs_cnt) : ro_texts.text_if_any) ) ) ) {
					return;
				}
			}
			if (this.edit_separately && variant && variant.rov_id) {
				let $button = (event && event.target ? $(event.target) : false);
				removeVariantDirectly( variant.rov_id, $button );
				
			} else {
				this.removeVariant(variant_i);
			}
		},
		
		addVariantOption: function(variant_i) {
			this.variants[variant_i].options.push({option_id: ''});
		},
		
		removeVariantOption: function(variant_i, option_i) {
			this.variants[variant_i].options.splice(option_i, 1);
		},
		
		getVariantFieldMaxSortOrder: function(variant_i){
			let sort_order = 0;
			this.variants[variant_i].fields.forEach(function(field){
				sort_order = Math.max(sort_order, parseInt(field.sort_order) || 0);
			});
			return sort_order;
		},
		
		addVariantField: function(variant_i, p_field){
			let field = $.extend(true, {
				name: '',
				type: 'text',
				key: '',
				sort_order: this.getVariantFieldMaxSortOrder(variant_i)+1,
			}, p_field || {});
			this.variants[variant_i].fields.push( field );
		},
		
		removeVariantField: function(variant_i, field_i){
			if ( this.variants[variant_i].fields[field_i].relatedoptions_field_id ) {
				if ( !confirm(ro_texts.confirm_ro_variant_field_remove) ) {
					return;
				}
			}
			this.variants[variant_i].fields.splice(field_i, 1);
		},
		
		isVariantOnCurrentPage: function(variant_i){
			return (variant_i >= (this.current_page-1)*this.num_variants_per_page && variant_i < this.current_page*this.num_variants_per_page);
		},
		
		getNumOfPages: function() {
			return this.getVariantPage(this.variants.length-1);
		},
		
		getVariantPage: function(variant_i) {
			let num_of_page = (variant_i+1) / this.num_variants_per_page;
			if (Math.floor(num_of_page) != num_of_page) {
				num_of_page = Math.floor(num_of_page)+1;
			}
			return num_of_page || 1;
		},
		
		setCurrentPage: function(page){
			let this_app = this;
			
			$(this_app.$el).find('.variants_list').css('opacity', 0.1);
			setTimeout(function(){
				this_app.current_page = page;
			}, 10);
			
			
		},
		
		updateCurrentPage: function() {
			if (this.current_page > this.getNumOfPages()) {
				this.current_page = this.getNumOfPages();
			}
		},
		
		editVariant: function(variant_i) {
			let rov_id = this.variants[variant_i].rov_id;
			editVariantInPopup(rov_id);
		},
		
		beginSaving: function(){
			let this_app = this;
			
			this_app.saving_status = true;
			
			clearVariantSaveAlerts();
			
			$(this_app.el).find('button[data-url-save]').each(function(){
				$(this).button('loading');
			});
		},
		
		endSaving: function(){
			let this_app = this;
			
			this_app.saving_status = false;
			
			clearVariantSaveAlerts();
			
			$(this_app.el).find('button[data-url-save]').each(function(){
				$(this).button('reset');
			});
		},
		
		
		
	},
	mounted: function () {
		
		//this.$nextTick(function () {
		//	this.updateUseVariantsStatus();
		//});
	},
	
	updated: function(){
		let this_app = this;
		
		this.$nextTick(function () {
			if (this_app.$el) {
				$(this_app.$el).find('.variants_list').css('opacity', 1);
			}
		});
	},
};

