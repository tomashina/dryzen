<?php echo $header; ?><?php echo $column_left; ?>
<?php echo $tf_column_left ?>
<?php echo $tf_header ?>
<div id="content" class="mz-content">
  
  <!-- Alert message -->
  <?php if(isset($success)){ ?>
    <div class="alert alert-success"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $success ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
  <?php } ?>
  <?php if(isset($warning)){ ?>
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $warning ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
  <?php } ?>
  
  <!-- Form -->
  <form action="<?php echo $action ?>" class="form-horizontal" enctype="multipart/form-data" id="form-tf-filter" method="post">
    <div class="tab-content">
        
      <!--General-->
      <div id="tab-tf-general" class="tab-pane fade in active">
        <div class="panel mz-panel-default">
          <div class="panel-body">
            <!--Name-->
            <div class="form-group required">
              <label class="col-xs-3 control-label" for="input-name"><?php echo $entry_name ?></label>
              <div class="col-xs-9">
              <?php foreach($languages as $language){ ?>
                <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                  <input class="form-control" type="text" name="filter_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($filter_description[$language['language_id']]['name'])?$filter_description[$language['language_id']]['name']:''; ?>" placeholder="<?php echo $entry_name ?>" >
                  <?php if(isset($err_name[$language['language_id']])){ ?>
                    <div class="text-danger"><?php echo $err_name[$language['language_id']] ?></div>
                  <?php } ?>
                </div>
              <?php } ?>
              </div>
            </div>
            
            <!--status-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-status"><?php echo $entry_status ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($status){ ?>
                  <input type="radio" name="status" value="1" id="input-status-enabled" checked>
                  <label for="input-status-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="status" value="0" id="input-status-disabled">
                  <label for="input-status-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="status" value="1" id="input-status-enabled" >
                  <label for="input-status-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="status" value="0" id="input-status-disabled" checked>
                  <label for="input-status-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--sort order-->
            <div class="form-group required">
              <label class="col-xs-3 control-label" for="input-sort-order"><?php echo $entry_sort_order ?></label>
              <div class="col-xs-9">
                <input class="form-control" id="input-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="number" min="0" name="sort_order" value="<?php echo $sort_order; ?>" >
              </div>
            </div>
            
            <!--Filter language id-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-tf-filter-language-id"><span data-toggle='tooltip' title='<?php echo $help_filter_language ?>'><?php echo $entry_filter_language ?></span></label>
              <div class="col-xs-9">
                <select name="filter_language_id" id="input-tf-filter-language-id" class="form-control">
                  <?php foreach($languages as $language){ ?>
                    <?php if($filter_language_id == $language['language_id']){ ?>
                    <option value="<?php echo $language['language_id'] ?>" selected="selected"><?php echo $language['name'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $language['language_id'] ?>"><?php echo $language['name'] ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            
            <!--Value format-->
<!--            <div class="form-group">
                <label class="col-xs-3 control-label" for="input-tf-setting-value-format"><span data-toggle="tooltip" title="<?php // echo $help_value_format ?>"><?php // echo $entry_value_format ?></span></label>
              <div class="col-xs-9">
              <?php foreach($languages as $language){ ?>
                <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php // echo $language['code']; ?>/<?php // echo $language['code']; ?>.png" title="<?php // echo $language['name']; ?>" /></span>
                  <input class="form-control" type="text" name="setting[value_format][<?php // echo $language['language_id']; ?>]" value="<?php // echo isset($setting['value_format'][$language['language_id']])?$setting['value_format'][$language['language_id']]:''; ?>" placeholder="<?php // echo $entry_value_format ?>" >
                </div>
              <?php } ?>
              </div>
            </div>-->
            
            <!-- Show in category -->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?php echo $entry_category; ?></span></label>
              <div class="col-xs-9">
                <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
                <div id="filter-category" class="well well-sm" style="height: 150px; overflow: auto;">
                  <?php foreach ($filter_categories as $filter_category) { ?>
                  <div id="filter-category-<?php echo $filter_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $filter_category['name']; ?>
                    <input type="hidden" name="filter_category[]" value="<?php echo $filter_category['category_id']; ?>" />
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Sync value status-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-sync-value-status"><span data-toggle="tooltip" title="<?php echo $help_sync_value ?>"><?php echo $entry_sync_value ?></span></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($setting['value_sync_status']){ ?>
                  <input type="radio" name="setting[value_sync_status]" value="1" id="input-sync-value-status-enabled" checked>
                  <label for="input-sync-value-status-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[value_sync_status]" value="0" id="input-sync-value-status-disabled">
                  <label for="input-sync-value-status-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="setting[value_sync_status]" value="1" id="input-sync-value-status-enabled" >
                  <label for="input-sync-value-status-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[value_sync_status]" value="0" id="input-sync-value-status-disabled" checked>
                  <label for="input-sync-value-status-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!--Layout-->
      <div id="tab-tf-layout" class="tab-pane fade">
        <div class="panel mz-panel-default">
          <div class="panel-body">
            <!--collapse-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($setting['collapse']){ ?>
                  <input type="radio" name="setting[collapse]" value="1" id="input-tf-setting-collapse-enabled" checked>
                  <label for="input-tf-setting-collapse-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[collapse]" value="0" id="input-tf-setting-collapse-disabled">
                  <label for="input-tf-setting-collapse-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="setting[collapse]" value="1" id="input-tf-setting-collapse-enabled" >
                  <label for="input-tf-setting-collapse-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[collapse]" value="0" id="input-tf-setting-collapse-disabled" checked>
                  <label for="input-tf-setting-collapse-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Input types-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-tf-setting-input-type"><?php echo $entry_input_type ?></label>
              <div class="col-xs-9">
                <select name="setting[input_type]" id="input-tf-setting-input-type" class="form-control">
                  <?php foreach($input_types as $input_type){ ?>
                    <?php if($input_type['code'] == $setting['input_type']){ ?>
                    <option value="<?php echo $input_type['code'] ?>" selected="selected"><?php echo $input_type['text'] ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $input_type['code'] ?>"><?php echo $input_type['text'] ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            
            <!--List types-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="list-tf-setting-list-type"><?php echo $entry_list_type ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php foreach($list_types as $list_type){ ?>
                    <?php if($list_type['code'] == $setting['list_type']){ ?>
                    <input type="radio" name="setting[list_type]" value="<?php echo $list_type['code'] ?>" id="input-tf-setting-list-type-<?php echo $list_type['code'] ?>" checked>
                    <label for="input-tf-setting-list-type-<?php echo $list_type['code'] ?>" ><?php echo $list_type['text']; ?></label>
                    <?php } else { ?>
                    <input type="radio" name="setting[list_type]" value="<?php echo $list_type['code'] ?>" id="input-tf-setting-list-type-<?php echo $list_type['code'] ?>">
                    <label for="input-tf-setting-list-type-<?php echo $list_type['code'] ?>" ><?php echo $list_type['text']; ?></label>
                    <?php } ?>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Value image width-->
            <div class="form-group required">
              <label class="col-xs-3 control-label" for="input-value-image-width"><?php echo $entry_image_width ?></label>
              <div class="col-xs-9">
                <input class="form-control" id="input-value-image-width" placeholder="<?php echo $entry_image_width ?>" type="number" min="0" name="setting[value_image_width]" value="<?php echo $setting['value_image_width']; ?>" >
              </div>
            </div>
            
            <!--Value image height-->
            <div class="form-group required">
              <label class="col-xs-3 control-label" for="input-value-image-height"><?php echo $entry_image_height ?></label>
              <div class="col-xs-9">
                <input class="form-control" id="input-value-image-height" placeholder="<?php echo $entry_image_height ?>" type="number" min="0" name="setting[value_image_height]" value="<?php echo $setting['value_image_height']; ?>" >
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!--Keys-->
      <div id="tab-tf-key" class="tab-pane fade">
        <div class="panel mz-panel-default">
          <div class="panel-body">
            <!-- Key attribute-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-key-attribute"><span data-toggle="tooltip" title="<?php echo $help_attribute; ?>"><?php echo $entry_attribute; ?></span></label>
              <div class="col-xs-9">
                <input type="text" name="key_attribute" value="" placeholder="<?php echo $entry_attribute; ?>" id="input-key-attribute" class="form-control" />
                <div id="key-attribute" class="well well-sm" style="height: 150px; overflow: auto;">
                  <?php foreach ($key_attributes as $key_attribute) { ?>
                  <div id="key-attribute-<?php echo $key_attribute['attribute_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $key_attribute['name']; ?>
                    <input type="hidden" name="setting[key_attribute][]" value="<?php echo $key_attribute['attribute_id']; ?>" />
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!-- Key option-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-key-option"><span data-toggle="tooltip" title="<?php echo $help_option; ?>"><?php echo $entry_option; ?></span></label>
              <div class="col-xs-9">
                <input type="text" name="key_option" value="" placeholder="<?php echo $entry_option; ?>" id="input-key-option" class="form-control" />
                <div id="key-option" class="well well-sm" style="height: 150px; overflow: auto;">
                  <?php foreach ($key_options as $key_option) { ?>
                  <div id="key-option-<?php echo $key_option['option_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $key_option['name']; ?>
                    <input type="hidden" name="setting[key_option][]" value="<?php echo $key_option['option_id']; ?>" />
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!-- Key filter_group-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-key-filter-group"><span data-toggle="tooltip" title="<?php echo $help_filter_group; ?>"><?php echo $entry_filter_group; ?></span></label>
              <div class="col-xs-9">
                <input type="text" name="key_filter_group" value="" placeholder="<?php echo $entry_filter_group; ?>" id="input-key-filter-group" class="form-control" />
                <div id="key-filter-group" class="well well-sm" style="height: 150px; overflow: auto;">
                  <?php foreach ($key_filter_groups as $key_filter_group) { ?>
                  <div id="key-filter-group-<?php echo $key_filter_group['filter_group_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $key_filter_group['name']; ?>
                    <input type="hidden" name="setting[key_filter_group][]" value="<?php echo $key_filter_group['filter_group_id']; ?>" />
                  </div>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Product name-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_product_name ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($setting['key_product_name']){ ?>
                  <input type="radio" name="setting[key_product_name]" value="1" id="input-tf-setting-key-product-name-enabled" checked>
                  <label for="input-tf-setting-key-product-name-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[key_product_name]" value="0" id="input-tf-setting-key-product-name-disabled">
                  <label for="input-tf-setting-key-product-name-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="setting[key_product_name]" value="1" id="input-tf-setting-key-product-name-enabled" >
                  <label for="input-tf-setting-key-product-name-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[key_product_name]" value="0" id="input-tf-setting-key-product-name-disabled" checked>
                  <label for="input-tf-setting-key-product-name-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Product description-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_product_description ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($setting['key_product_description']){ ?>
                  <input type="radio" name="setting[key_product_description]" value="1" id="input-tf-setting-key-product-description-enabled" checked>
                  <label for="input-tf-setting-key-product-description-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[key_product_description]" value="0" id="input-tf-setting-key-product-description-disabled">
                  <label for="input-tf-setting-key-product-description-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="setting[key_product_description]" value="1" id="input-tf-setting-key-product-description-enabled" >
                  <label for="input-tf-setting-key-product-description-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[key_product_description]" value="0" id="input-tf-setting-key-product-description-disabled" checked>
                  <label for="input-tf-setting-key-product-description-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Product tags-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_product_tags ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($setting['key_product_tags']){ ?>
                  <input type="radio" name="setting[key_product_tags]" value="1" id="input-tf-setting-key-product-tags-enabled" checked>
                  <label for="input-tf-setting-key-product-tags-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[key_product_tags]" value="0" id="input-tf-setting-key-product-tags-disabled">
                  <label for="input-tf-setting-key-product-tags-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="setting[key_product_tags]" value="1" id="input-tf-setting-key-product-tags-enabled" >
                  <label for="input-tf-setting-key-product-tags-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="setting[key_product_tags]" value="0" id="input-tf-setting-key-product-tags-disabled" checked>
                  <label for="input-tf-setting-key-product-tags-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <script>
// Category
$('input[name=\'category\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&user_token=<?php echo $user_token ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					};
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'category\']').val('');

		$('#filter-category-' + item['value']).remove();

		$('#filter-category').append('<div id="filter-category-' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="filter_category[]" value="' + item['value'] + '" /></div>');
	}
});

$('#filter-category').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// key Attribute
$('input[name=\'key_attribute\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=extension/maza/catalog/autocomplete/attribute&user_token=<?php echo $user_token ?>&limit=100&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'] + ' [' + item['attribute_group'] + ']',
						value: item['attribute_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'key_attribute\']').val('');

		$('#key-attribute-' + item['value']).remove();

		$('#key-attribute').append('<div id="key-attribute-' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="setting[key_attribute][]" value="' + item['value'] + '" /></div>');
	}
});

$('#key-attribute').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// key Option
$('input[name=\'key_option\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=extension/maza/catalog/autocomplete/option&user_token=<?php echo $user_token ?>&limit=100&filter_category=choose&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
                                        return {
						label: item['name'],
						value: item['option_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'key_option\']').val('');

		$('#key-option-' + item['value']).remove();

		$('#key-option').append('<div id="key-option-' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="setting[key_option][]" value="' + item['value'] + '" /></div>');
	}
});

$('#key-option').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});

// key Attribute
$('input[name=\'key_filter_group\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=extension/maza/catalog/autocomplete/filter_group&user_token=<?php echo $user_token ?>&limit=100&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['filter_group_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'key_filter_group\']').val('');

		$('#key-filter-group-' + item['value']).remove();

		$('#key-filter-group').append('<div id="key-filter-group-' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="setting[key_filter_group][]" value="' + item['value'] + '" /></div>');
	}
});

$('#key-filter-group').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
  </script>
</div>
<?php echo $tf_footer ?>