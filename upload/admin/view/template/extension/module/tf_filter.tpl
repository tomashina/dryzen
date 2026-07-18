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
            <!--module name-->
            <div class="form-group required">
              <label class="col-xs-3 control-label" for="input-name"><?php echo $entry_module_name ?></label>
              <div class="col-xs-9">
                <input class="form-control" id="input-name" placeholder="<?php echo $entry_module_name ?>" type="text" name="name" value="<?php echo $name; ?>" >
                <?php if (isset($err_name)) { ?>
                <div class="text-danger"><?php echo $err_name; ?></div>
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
            
            <!--module title-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-title"><?php echo $entry_title ?></label>
              <div class="col-xs-9">
              <?php foreach($languages as $language){ ?>
                <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                  <input class="form-control" type="text" name="title[<?php echo $language['language_id']; ?>]" value="<?php echo isset($title[$language['language_id']])?$title[$language['language_id']]:''; ?>" placeholder="<?php echo $entry_title ?>" >
                </div>
              <?php } ?>
              </div>
            </div>
            
            <!--count product-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-count-product"><?php echo $entry_count_product ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($count_product){ ?>
                  <input type="radio" name="count_product" value="1" id="input-count-product-enabled" checked>
                  <label for="input-count-product-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="count_product" value="0" id="input-count-product-disabled">
                  <label for="input-count-product-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="count_product" value="1" id="input-count-product-enabled" >
                  <label for="input-count-product-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="count_product" value="0" id="input-count-product-disabled" checked>
                  <label for="input-count-product-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--sub category-->
<!--            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-sub-category"><span data-toggle="tooltip" title="<?php // echo $help_sub_category ?>"><?php // echo $entry_sub_category ?></span></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php // if($sub_category){ ?>
                  <input type="radio" name="sub_category" value="1" id="input-sub-category-enabled" checked>
                  <label for="input-sub-category-enabled" ><?php // echo $text_enabled; ?></label>
                  <input type="radio" name="sub_category" value="0" id="input-sub-category-disabled">
                  <label for="input-sub-category-disabled"><?php // echo $text_disabled; ?></label>
                  <?php // } else { ?>
                  <input type="radio" name="sub_category" value="1" id="input-sub-category-enabled" >
                  <label for="input-sub-category-enabled"><?php // echo $text_enabled; ?></label>
                  <input type="radio" name="sub_category" value="0" id="input-sub-category-disabled" checked>
                  <label for="input-sub-category-disabled" ><?php // echo $text_disabled; ?></label>
                  <?php // } ?>
                </div>
              </div>
            </div>-->
            
            <!--Cache-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_cache ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($cache){ ?>
                  <input type="radio" name="cache" value="1" id="input-cache-enabled" checked>
                  <label for="input-cache-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="cache" value="0" id="input-cache-disabled">
                  <label for="input-cache-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="cache" value="1" id="input-cache-enabled" >
                  <label for="input-cache-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="cache" value="0" id="input-cache-disabled" checked>
                  <label for="input-cache-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            <!--Delay-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_delay ?>"><?php echo $entry_delay ?></span></label>
              <div class="col-xs-9">
                <input class="form-control" id="input-delay" placeholder="<?php echo $entry_delay ?>" type="number" name="delay" value="<?php echo $delay; ?>" >
              </div>
            </div>
          </div>
        </div>
        
      </div> <!-- // General -->
      
      <!--Filter-->
      <div id="tab-tf-data" class="tab-pane fade">
        <div class="panel mz-panel-vertical-nav">
          <div class="panel-body clearfix">
            <ul id="tab-tf-filter" class="nav nav-tabs mz-panel-nav-vertical-tabs col-xs-2">
              <li class="active"><a href="#tab-tf-filter-price" data-toggle="tab"><?php echo $entry_price ?></a></li>
              <li><a href="#tab-tf-filter-sub-category" data-toggle="tab"><?php echo $entry_sub_category ?></a></li>
              <li><a href="#tab-tf-filter-manufacturer" data-toggle="tab"><?php echo $entry_manufacturer ?></a></li>
              <li><a href="#tab-tf-filter-search" data-toggle="tab"><?php echo $entry_search ?></a></li>
              <li><a href="#tab-tf-filter-availability" data-toggle="tab"><?php echo $entry_availability ?></a></li>
              <li><a href="#tab-tf-filter-discount" data-toggle="tab"><?php echo $entry_discount ?></a></li>
              <li><a href="#tab-tf-filter-rating" data-toggle="tab"><?php echo $entry_rating ?></a></li>
              <li><a href="#tab-tf-filter-filter" data-toggle="tab"><?php echo $entry_filter ?></a></li>
              <li><a href="#tab-tf-filter-custom" data-toggle="tab"><?php echo $entry_custom ?></a></li>
            </ul>
            <div id="tab-tf-filter-content" class="tab-content col-xs-10">
              <!--Price-->
              <div id="tab-tf-filter-price" class="tab-pane fade in active">
                <!--Status-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['price']['status']){ ?>
                      <input type="radio" name="filter[price][status]" value="1" id="input-tf-filter-price-enabled" checked>
                      <label for="input-tf-filter-price-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[price][status]" value="0" id="input-tf-filter-price-disabled">
                      <label for="input-tf-filter-price-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[price][status]" value="1" id="input-tf-filter-price-enabled" >
                      <label for="input-tf-filter-price-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[price][status]" value="0" id="input-tf-filter-price-disabled" checked>
                      <label for="input-tf-filter-price-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--Filter title-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_title ?></label>
                  <div class="col-xs-9">
                  <?php foreach($languages as $language){ ?>
                    <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                      <input class="form-control" type="text" name="filter[price][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($filter['price']['title'][$language['language_id']])?$filter['price']['title'][$language['language_id']]:''; ?>" placeholder="<?php echo $entry_title ?>" >
                    </div>
                  <?php } ?>
                  </div>
                </div>
                <!--sort order-->
                <div class="form-group required">
                  <label class="col-xs-3 control-label" for="input-tf-filter-price-sort-order"><?php echo $entry_sort_order ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-price-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="number" name="filter[price][sort_order]" value="<?php echo $filter['price']['sort_order']; ?>" >
                  </div>
                </div>
                <!--collapse by default-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['price']['collapse']){ ?>
                      <input type="radio" name="filter[price][collapse]" value="1" id="input-tf-filter-price-collapse-enabled" checked>
                      <label for="input-tf-filter-price-collapse-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[price][collapse]" value="0" id="input-tf-filter-price-collapse-disabled">
                      <label for="input-tf-filter-price-collapse-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[price][collapse]" value="1" id="input-tf-filter-price-collapse-enabled" >
                      <label for="input-tf-filter-price-collapse-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[price][collapse]" value="0" id="input-tf-filter-price-collapse-disabled" checked>
                      <label for="input-tf-filter-price-collapse-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <!--Sub category-->
              <div id="tab-tf-filter-sub-category" class="tab-pane fade">
                
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['sub_category']['status']){ ?>
                      <input type="radio" name="filter[sub_category][status]" value="1" id="input-tf-filter-sub-category-enabled" checked>
                      <label for="input-tf-filter-sub-category-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[sub_category][status]" value="0" id="input-tf-filter-sub-category-disabled">
                      <label for="input-tf-filter-sub-category-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[sub_category][status]" value="1" id="input-tf-filter-sub-category-enabled" >
                      <label for="input-tf-filter-sub-category-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[sub_category][status]" value="0" id="input-tf-filter-sub-category-disabled" checked>
                      <label for="input-tf-filter-sub-category-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_title ?></label>
                  <div class="col-xs-9">
                  <?php foreach($languages as $language){ ?>
                    <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                      <input class="form-control" type="text" name="filter[sub_category][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($filter['sub_category']['title'][$language['language_id']])?$filter['sub_category']['title'][$language['language_id']]:''; ?>" placeholder="<?php echo $entry_title ?>" >
                    </div>
                  <?php } ?>
                  </div>
                </div>
                
                <div class="form-group required">
                  <label class="col-xs-3 control-label" for="input-tf-filter-sub-category-sort-order"><?php echo $entry_sort_order ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-sub-category-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="number" name="filter[sub_category][sort_order]" value="<?php echo $filter['sub_category']['sort_order']; ?>" >
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['sub_category']['collapse']){ ?>
                      <input type="radio" name="filter[sub_category][collapse]" value="1" id="input-tf-filter-sub-category-collapse-enabled" checked>
                      <label for="input-tf-filter-sub-category-collapse-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[sub_category][collapse]" value="0" id="input-tf-filter-sub-category-collapse-disabled">
                      <label for="input-tf-filter-sub-category-collapse-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[sub_category][collapse]" value="1" id="input-tf-filter-sub-category-collapse-enabled" >
                      <label for="input-tf-filter-sub-category-collapse-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[sub_category][collapse]" value="0" id="input-tf-filter-sub-category-collapse-disabled" checked>
                      <label for="input-tf-filter-sub-category-collapse-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-sub-category-input-type"><?php echo $entry_input_type ?></label>
                  <div class="col-xs-9">
                    <select name="filter[sub_category][input_type]" id="input-tf-filter-sub-category-input-type" class="form-control">
                      <?php foreach($input_types as $input_type){ ?>
                        <?php if($input_type['code'] == $filter['sub_category']['input_type']){ ?>
                        <option value="<?php echo $input_type['code'] ?>" selected="selected"><?php echo $input_type['text'] ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $input_type['code'] ?>"><?php echo $input_type['text'] ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="list-tf-filter-sub-category-list-type"><?php echo $entry_list_type ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php foreach($list_types as $list_type){ ?>
                        <?php if($list_type['code'] == $filter['sub_category']['list_type']){ ?>
                        <input type="radio" name="filter[sub_category][list_type]" value="<?php echo $list_type['code'] ?>" id="input-tf-filter-sub-category-list-type-<?php echo $list_type['code'] ?>" checked>
                        <label for="input-tf-filter-sub-category-list-type-<?php echo $list_type['code'] ?>" ><?php echo $list_type['text']; ?></label>
                        <?php } else { ?>
                        <input type="radio" name="filter[sub_category][list_type]" value="<?php echo $list_type['code'] ?>" id="input-tf-filter-sub-category-list-type-<?php echo $list_type['code'] ?>">
                        <label for="input-tf-filter-sub-category-list-type-<?php echo $list_type['code'] ?>" ><?php echo $list_type['text']; ?></label>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-sub-category-image-width"><?php echo $entry_image_width ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-sub-category-image-width" placeholder="<?php echo $entry_image_width ?>" type="number" name="filter[sub_category][image_width]" value="<?php echo $filter['sub_category']['image_width']; ?>" >
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-sub-category-image-height"><?php echo $entry_image_height ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-sub-category-image-height" placeholder="<?php echo $entry_image_height ?>" type="number" name="filter[sub_category][image_height]" value="<?php echo $filter['sub_category']['image_height']; ?>" >
                  </div>
                </div>
                  
                <!-- show search-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="list-tf-filter-sub-category-list-type"><?php echo $entry_show_search ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php foreach($list_search_status as $search_status){ ?>
                        <?php if($search_status['code'] == $filter['sub_category']['search']){ ?>
                        <input type="radio" name="filter[sub_category][search]" value="<?php echo $search_status['code'] ?>" id="input-tf-filter-sub-category-list-type-<?php echo $search_status['code'] ?>" checked>
                        <label for="input-tf-filter-sub-category-list-type-<?php echo $search_status['code'] ?>" ><?php echo $search_status['text']; ?></label>
                        <?php } else { ?>
                        <input type="radio" name="filter[sub_category][search]" value="<?php echo $search_status['code'] ?>" id="input-tf-filter-sub-category-list-type-<?php echo $search_status['code'] ?>">
                        <label for="input-tf-filter-sub-category-list-type-<?php echo $search_status['code'] ?>" ><?php echo $search_status['text']; ?></label>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <!--Manufacturer-->
              <div id="tab-tf-filter-manufacturer" class="tab-pane fade">
                <!--Status-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['manufacturer']['status']){ ?>
                      <input type="radio" name="filter[manufacturer][status]" value="1" id="input-tf-filter-manufacturer-enabled" checked>
                      <label for="input-tf-filter-manufacturer-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[manufacturer][status]" value="0" id="input-tf-filter-manufacturer-disabled">
                      <label for="input-tf-filter-manufacturer-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[manufacturer][status]" value="1" id="input-tf-filter-manufacturer-enabled" >
                      <label for="input-tf-filter-manufacturer-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[manufacturer][status]" value="0" id="input-tf-filter-manufacturer-disabled" checked>
                      <label for="input-tf-filter-manufacturer-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--Filter title-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_title ?></label>
                  <div class="col-xs-9">
                  <?php foreach($languages as $language){ ?>
                    <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                      <input class="form-control" type="text" name="filter[manufacturer][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($filter['manufacturer']['title'][$language['language_id']])?$filter['manufacturer']['title'][$language['language_id']]:''; ?>" placeholder="<?php echo $entry_title ?>" >
                    </div>
                  <?php } ?>
                  </div>
                </div>
                <!--sort order-->
                <div class="form-group required">
                  <label class="col-xs-3 control-label" for="input-tf-filter-manufacturer-sort-order"><?php echo $entry_sort_order ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-manufacturer-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="number" name="filter[manufacturer][sort_order]" value="<?php echo $filter['manufacturer']['sort_order']; ?>" >
                  </div>
                </div>
                <!--collapse-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['manufacturer']['collapse']){ ?>
                      <input type="radio" name="filter[manufacturer][collapse]" value="1" id="input-tf-filter-manufacturer-collapse-enabled" checked>
                      <label for="input-tf-filter-manufacturer-collapse-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[manufacturer][collapse]" value="0" id="input-tf-filter-manufacturer-collapse-disabled">
                      <label for="input-tf-filter-manufacturer-collapse-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[manufacturer][collapse]" value="1" id="input-tf-filter-manufacturer-collapse-enabled" >
                      <label for="input-tf-filter-manufacturer-collapse-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[manufacturer][collapse]" value="0" id="input-tf-filter-manufacturer-collapse-disabled" checked>
                      <label for="input-tf-filter-manufacturer-collapse-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--Input types-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-manufacturer-input-type"><?php echo $entry_input_type ?></label>
                  <div class="col-xs-9">
                    <select name="filter[manufacturer][input_type]" id="input-tf-filter-manufacturer-input-type" class="form-control">
                      <?php foreach($input_types as $input_type){ ?>
                        <?php if($input_type['code'] == $filter['manufacturer']['input_type']){ ?>
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
                  <label class="col-xs-3 control-label" for="list-tf-filter-manufacturer-list-type"><?php echo $entry_list_type ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php foreach($list_types as $list_type){ ?>
                        <?php if($list_type['code'] == $filter['manufacturer']['list_type']){ ?>
                        <input type="radio" name="filter[manufacturer][list_type]" value="<?php echo $list_type['code'] ?>" id="input-tf-filter-manufacturer-list-type-<?php echo $list_type['code'] ?>" checked>
                        <label for="input-tf-filter-manufacturer-list-type-<?php echo $list_type['code'] ?>" ><?php echo $list_type['text']; ?></label>
                        <?php } else { ?>
                        <input type="radio" name="filter[manufacturer][list_type]" value="<?php echo $list_type['code'] ?>" id="input-tf-filter-manufacturer-list-type-<?php echo $list_type['code'] ?>">
                        <label for="input-tf-filter-manufacturer-list-type-<?php echo $list_type['code'] ?>" ><?php echo $list_type['text']; ?></label>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!-- show search-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="list-tf-filter-manufacturer-list-type"><?php echo $entry_show_search ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php foreach($list_search_status as $search_status){ ?>
                        <?php if($search_status['code'] == $filter['manufacturer']['search']){ ?>
                        <input type="radio" name="filter[manufacturer][search]" value="<?php echo $search_status['code'] ?>" id="input-tf-filter-manufacturer-list-type-<?php echo $search_status['code'] ?>" checked>
                        <label for="input-tf-filter-manufacturer-list-type-<?php echo $search_status['code'] ?>" ><?php echo $search_status['text']; ?></label>
                        <?php } else { ?>
                        <input type="radio" name="filter[manufacturer][search]" value="<?php echo $search_status['code'] ?>" id="input-tf-filter-manufacturer-list-type-<?php echo $search_status['code'] ?>">
                        <label for="input-tf-filter-manufacturer-list-type-<?php echo $search_status['code'] ?>" ><?php echo $search_status['text']; ?></label>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--Image width-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-manufacturer-image-width"><?php echo $entry_image_width ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-manufacturer-image-width" placeholder="<?php echo $entry_image_width ?>" type="number" name="filter[manufacturer][image_width]" value="<?php echo $filter['manufacturer']['image_width']; ?>" >
                  </div>
                </div>
                
                <!--Image height-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-manufacturer-image-height"><?php echo $entry_image_height ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-manufacturer-image-height" placeholder="<?php echo $entry_image_height ?>" type="number" name="filter[manufacturer][image_height]" value="<?php echo $filter['manufacturer']['image_height']; ?>" >
                  </div>
                </div>
              </div>
              
              <!--Search-->
              <div id="tab-tf-filter-search" class="tab-pane fade">
                <!--Status-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['search']['status']){ ?>
                      <input type="radio" name="filter[search][status]" value="1" id="input-tf-filter-search-enabled" checked>
                      <label for="input-tf-filter-search-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[search][status]" value="0" id="input-tf-filter-search-disabled">
                      <label for="input-tf-filter-search-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[search][status]" value="1" id="input-tf-filter-search-enabled" >
                      <label for="input-tf-filter-search-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[search][status]" value="0" id="input-tf-filter-search-disabled" checked>
                      <label for="input-tf-filter-search-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--Filter title-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_title ?></label>
                  <div class="col-xs-9">
                  <?php foreach($languages as $language){ ?>
                    <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                      <input class="form-control" type="text" name="filter[search][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($filter['search']['title'][$language['language_id']])?$filter['search']['title'][$language['language_id']]:''; ?>" placeholder="<?php echo $entry_title ?>" >
                    </div>
                  <?php } ?>
                  </div>
                </div>
                <!--Filter placeholder-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_placeholder ?></label>
                  <div class="col-xs-9">
                  <?php foreach($languages as $language){ ?>
                    <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" placeholder="<?php echo $language['name']; ?>" /></span>
                      <input class="form-control" type="text" name="filter[search][placeholder][<?php echo $language['language_id']; ?>]" value="<?php echo isset($filter['search']['placeholder'][$language['language_id']])?$filter['search']['placeholder'][$language['language_id']]:''; ?>" placeholder="<?php echo $entry_placeholder ?>" >
                    </div>
                  <?php } ?>
                  </div>
                </div>
                <!-- description -->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_description ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['search']['description']){ ?>
                      <input type="radio" name="filter[search][description]" value="1" id="input-tf-filter-search-description-enabled" checked>
                      <label for="input-tf-filter-search-description-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[search][description]" value="0" id="input-tf-filter-search-description-disabled">
                      <label for="input-tf-filter-search-description-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[search][description]" value="1" id="input-tf-filter-search-description-enabled" >
                      <label for="input-tf-filter-search-description-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[search][description]" value="0" id="input-tf-filter-search-description-disabled" checked>
                      <label for="input-tf-filter-search-description-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!-- collapse -->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['search']['collapse']){ ?>
                      <input type="radio" name="filter[search][collapse]" value="1" id="input-tf-filter-search-collapse-enabled" checked>
                      <label for="input-tf-filter-search-collapse-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[search][collapse]" value="0" id="input-tf-filter-search-collapse-disabled">
                      <label for="input-tf-filter-search-collapse-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[search][collapse]" value="1" id="input-tf-filter-search-collapse-enabled" >
                      <label for="input-tf-filter-search-collapse-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[search][collapse]" value="0" id="input-tf-filter-search-collapse-disabled" checked>
                      <label for="input-tf-filter-search-collapse-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--sort order-->
                <div class="form-group required">
                  <label class="col-xs-3 control-label" for="input-tf-filter-search-sort-order"><?php echo $entry_sort_order ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-search-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="number" name="filter[search][sort_order]" value="<?php echo $filter['search']['sort_order']; ?>" >
                  </div>
                </div>
              </div>
              
              <!--Stock status-->
              <div id="tab-tf-filter-availability" class="tab-pane fade">
                <!--Status-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['availability']['status']){ ?>
                      <input type="radio" name="filter[availability][status]" value="1" id="input-tf-filter-availability-enabled" checked>
                      <label for="input-tf-filter-availability-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[availability][status]" value="0" id="input-tf-filter-availability-disabled">
                      <label for="input-tf-filter-availability-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[availability][status]" value="1" id="input-tf-filter-availability-enabled" >
                      <label for="input-tf-filter-availability-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[availability][status]" value="0" id="input-tf-filter-availability-disabled" checked>
                      <label for="input-tf-filter-availability-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--Filter title-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_title ?></label>
                  <div class="col-xs-9">
                  <?php foreach($languages as $language){ ?>
                    <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                      <input class="form-control" type="text" name="filter[availability][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($filter['availability']['title'][$language['language_id']])?$filter['availability']['title'][$language['language_id']]:''; ?>" placeholder="<?php echo $entry_title ?>" >
                    </div>
                  <?php } ?>
                  </div>
                </div>
                <!--sort order-->
                <div class="form-group required">
                  <label class="col-xs-3 control-label" for="input-tf-filter-availability-sort-order"><?php echo $entry_sort_order ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-availability-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="number" name="filter[availability][sort_order]" value="<?php echo $filter['availability']['sort_order']; ?>" >
                  </div>
                </div>
                <!-- collapse -->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['availability']['collapse']){ ?>
                      <input type="radio" name="filter[availability][collapse]" value="1" id="input-tf-filter-availability-collapse-enabled" checked>
                      <label for="input-tf-filter-availability-collapse-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[availability][collapse]" value="0" id="input-tf-filter-availability-collapse-disabled">
                      <label for="input-tf-filter-availability-collapse-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[availability][collapse]" value="1" id="input-tf-filter-availability-collapse-enabled" >
                      <label for="input-tf-filter-availability-collapse-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[availability][collapse]" value="0" id="input-tf-filter-availability-collapse-disabled" checked>
                      <label for="input-tf-filter-availability-collapse-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!-- stock_status -->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_stock_status ?>"><?php echo $entry_stock_status ?></span></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['availability']['stock_status']){ ?> 
                      <input type="radio" name="filter[availability][stock_status]" value="1" id="input-tf-filter-availability-stock-status-enabled" checked>
                      <label for="input-tf-filter-availability-stock-status-enabled" ><?php echo $text_enabled ?></label>
                      <input type="radio" name="filter[availability][stock_status]" value="0" id="input-tf-filter-availability-stock-status-disabled">
                      <label for="input-tf-filter-availability-stock-status-disabled"><?php echo $text_disabled ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[availability][stock_status]" value="1" id="input-tf-filter-availability-stock-status-enabled" >
                      <label for="input-tf-filter-availability-stock-status-enabled"><?php echo $text_enabled ?></label>
                      <input type="radio" name="filter[availability][stock_status]" value="0" id="input-tf-filter-availability-stock-status-disabled" checked>
                      <label for="input-tf-filter-availability-stock-status-disabled" ><?php echo $text_disabled ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="form-group stock_status">
									<label class="col-xs-3 control-label"><?php echo $entry_input_type ?></label>
									<div class="col-xs-9">
										<select name="filter[availability][input_type]" id="input-tf-filter-availability-input-type" class="form-control">
                      <?php foreach($input_types as $input_type){ ?>
												<?php if ($input_type['code'] == $filter['availability']['input_type']){ ?>
												<option value="<?php echo $input_type['code'] ?>" selected="selected"><?php echo $input_type['text'] ?></option>
												<?php } else { ?>
												<option value="<?php echo $input_type['code'] ?>"><?php echo $input_type['text'] ?></option>
												<?php } ?>
											<?php } ?>
										</select>
									</div>
								</div>
              </div>
              
              <!--Discount-->
              <div id="tab-tf-filter-discount" class="tab-pane fade">
                <div class="alert alert-info"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $help_discount ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
                <!--Status-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['discount']['status']){ ?>
                      <input type="radio" name="filter[discount][status]" value="1" id="input-tf-filter-discount-enabled" checked>
                      <label for="input-tf-filter-discount-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[discount][status]" value="0" id="input-tf-filter-discount-disabled">
                      <label for="input-tf-filter-discount-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[discount][status]" value="1" id="input-tf-filter-discount-enabled" >
                      <label for="input-tf-filter-discount-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[discount][status]" value="0" id="input-tf-filter-discount-disabled" checked>
                      <label for="input-tf-filter-discount-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--Filter title-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_title ?></label>
                  <div class="col-xs-9">
                  <?php foreach($languages as $language){ ?>
                    <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                      <input class="form-control" type="text" name="filter[discount][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($filter['discount']['title'][$language['language_id']])?$filter['discount']['title'][$language['language_id']]:''; ?>" placeholder="<?php echo $entry_title ?>" >
                    </div>
                  <?php } ?>
                  </div>
                </div>
                <!--sort order-->
                <div class="form-group required">
                  <label class="col-xs-3 control-label" for="input-tf-filter-discount-sort-order"><?php echo $entry_sort_order ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-discount-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="number" name="filter[discount][sort_order]" value="<?php echo $filter['discount']['sort_order']; ?>" >
                  </div>
                </div>
                <!-- collapse -->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['discount']['collapse']){ ?>
                      <input type="radio" name="filter[discount][collapse]" value="1" id="input-tf-filter-discount-collapse-enabled" checked>
                      <label for="input-tf-filter-discount-collapse-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[discount][collapse]" value="0" id="input-tf-filter-discount-collapse-disabled">
                      <label for="input-tf-filter-discount-collapse-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[discount][collapse]" value="1" id="input-tf-filter-discount-collapse-enabled" >
                      <label for="input-tf-filter-discount-collapse-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[discount][collapse]" value="0" id="input-tf-filter-discount-collapse-disabled" checked>
                      <label for="input-tf-filter-discount-collapse-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <!--Rating-->
              <div id="tab-tf-filter-rating" class="tab-pane fade">
                <!--Status-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-rating-status"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['rating']['status']){ ?>
                      <input type="radio" name="filter[rating][status]" value="1" id="input-tf-filter-rating-enabled" checked>
                      <label for="input-tf-filter-rating-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[rating][status]" value="0" id="input-tf-filter-rating-disabled">
                      <label for="input-tf-filter-rating-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[rating][status]" value="1" id="input-tf-filter-rating-enabled" >
                      <label for="input-tf-filter-rating-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[rating][status]" value="0" id="input-tf-filter-rating-disabled" checked>
                      <label for="input-tf-filter-rating-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <!--Filter title-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_title ?></label>
                  <div class="col-xs-9">
                  <?php foreach($languages as $language){ ?>
                    <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                      <input class="form-control" type="text" name="filter[rating][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($filter['rating']['title'][$language['language_id']])?$filter['rating']['title'][$language['language_id']]:''; ?>" placeholder="<?php echo $entry_title ?>" >
                    </div>
                  <?php } ?>
                  </div>
                </div>
                <!--sort order-->
                <div class="form-group required">
                  <label class="col-xs-3 control-label" for="input-tf-filter-rating-sort-order"><?php echo $entry_sort_order ?></label>
                  <div class="col-xs-9">
                    <input class="form-control" id="input-tf-filter-rating-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="number" name="filter[rating][sort_order]" value="<?php echo $filter['rating']['sort_order']; ?>" >
                  </div>
                </div>
                <!--collapse-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['rating']['collapse']){ ?>
                      <input type="radio" name="filter[rating][collapse]" value="1" id="input-tf-filter-rating-collapse-enabled" checked>
                      <label for="input-tf-filter-rating-collapse-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[rating][collapse]" value="0" id="input-tf-filter-rating-collapse-disabled">
                      <label for="input-tf-filter-rating-collapse-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[rating][collapse]" value="1" id="input-tf-filter-rating-collapse-enabled" >
                      <label for="input-tf-filter-rating-collapse-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[rating][collapse]" value="0" id="input-tf-filter-rating-collapse-disabled" checked>
                      <label for="input-tf-filter-rating-collapse-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <!--Filter-->
              <div id="tab-tf-filter-filter" class="tab-pane fade">
                <div class="alert alert-info"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $help_filter ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
                <!--Status-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-filter-status"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['filter']['status']){ ?>
                      <input type="radio" name="filter[filter][status]" value="1" id="input-tf-filter-filter-enabled" checked>
                      <label for="input-tf-filter-filter-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[filter][status]" value="0" id="input-tf-filter-filter-disabled">
                      <label for="input-tf-filter-filter-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[filter][status]" value="1" id="input-tf-filter-filter-enabled" >
                      <label for="input-tf-filter-filter-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[filter][status]" value="0" id="input-tf-filter-filter-disabled" checked>
                      <label for="input-tf-filter-filter-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <!-- show search-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="list-tf-filter-filter-list-type"><?php echo $entry_show_search ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php foreach($list_search_status as $search_status){ ?>
                        <?php if($search_status['code'] == $filter['filter']['search']){ ?>
                        <input type="radio" name="filter[filter][search]" value="<?php echo $search_status['code'] ?>" id="input-tf-filter-filter-list-type-<?php echo $search_status['code'] ?>" checked>
                        <label for="input-tf-filter-filter-list-type-<?php echo $search_status['code'] ?>" ><?php echo $search_status['text']; ?></label>
                        <?php } else { ?>
                        <input type="radio" name="filter[filter][search]" value="<?php echo $search_status['code'] ?>" id="input-tf-filter-filter-list-type-<?php echo $search_status['code'] ?>">
                        <label for="input-tf-filter-filter-list-type-<?php echo $search_status['code'] ?>" ><?php echo $search_status['text']; ?></label>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <!--Require category-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_require_category ?>"><?php echo $entry_require_category ?></span></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['filter']['require_category']){ ?>
                      <input type="radio" name="filter[filter][require_category]" value="1" id="input-tf-filter-filter-require-category-enabled" checked>
                      <label for="input-tf-filter-filter-require-category-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[filter][require_category]" value="0" id="input-tf-filter-filter-require-category-disabled">
                      <label for="input-tf-filter-filter-require-category-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[filter][require_category]" value="1" id="input-tf-filter-filter-require-category-enabled" >
                      <label for="input-tf-filter-filter-require-category-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[filter][require_category]" value="0" id="input-tf-filter-filter-require-category-disabled" checked>
                      <label for="input-tf-filter-filter-require-category-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <!--collapse by default-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['filter']['collapse']){ ?>
                      <input type="radio" name="filter[filter][collapse]" value="1" id="input-tf-filter-filter-collapse-enabled" checked>
                      <label for="input-tf-filter-filter-collapse-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[filter][collapse]" value="0" id="input-tf-filter-filter-collapse-disabled">
                      <label for="input-tf-filter-filter-collapse-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[filter][collapse]" value="1" id="input-tf-filter-filter-collapse-enabled" >
                      <label for="input-tf-filter-filter-collapse-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[filter][collapse]" value="0" id="input-tf-filter-filter-collapse-disabled" checked>
                      <label for="input-tf-filter-filter-collapse-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <!--Custom-->
              <div id="tab-tf-filter-custom" class="tab-pane fade">
                <div class="alert alert-info"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo $help_custom ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
                <!--Status-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="input-tf-filter-custom-status"><?php echo $entry_status ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['custom']['status']){ ?>
                      <input type="radio" name="filter[custom][status]" value="1" id="input-tf-filter-custom-enabled" checked>
                      <label for="input-tf-filter-custom-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[custom][status]" value="0" id="input-tf-filter-custom-disabled">
                      <label for="input-tf-filter-custom-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[custom][status]" value="1" id="input-tf-filter-custom-enabled" >
                      <label for="input-tf-filter-custom-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[custom][status]" value="0" id="input-tf-filter-custom-disabled" checked>
                      <label for="input-tf-filter-custom-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <!-- show search-->
                <div class="form-group">
                  <label class="col-xs-3 control-label" for="list-tf-filter-custom-list-type"><?php echo $entry_show_search ?></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php foreach($list_search_status as $search_status){ ?>
                        <?php if($search_status['code'] == $filter['custom']['search']){ ?>
                        <input type="radio" name="filter[custom][search]" value="<?php echo $search_status['code'] ?>" id="input-tf-filter-custom-list-type-<?php echo $search_status['code'] ?>" checked>
                        <label for="input-tf-filter-custom-list-type-<?php echo $search_status['code'] ?>" ><?php echo $search_status['text']; ?></label>
                        <?php } else { ?>
                        <input type="radio" name="filter[custom][search]" value="<?php echo $search_status['code'] ?>" id="input-tf-filter-custom-list-type-<?php echo $search_status['code'] ?>">
                        <label for="input-tf-filter-custom-list-type-<?php echo $search_status['code'] ?>" ><?php echo $search_status['text']; ?></label>
                        <?php } ?>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                
                <!--Require category-->
                <div class="form-group">
                  <label class="col-xs-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_require_category ?>"><?php echo $entry_require_category ?></span></label>
                  <div class="col-xs-9">
                    <div class="mz-switch-field">
                      <?php if($filter['custom']['require_category']){ ?>
                      <input type="radio" name="filter[custom][require_category]" value="1" id="input-tf-filter-custom-require-category-enabled" checked>
                      <label for="input-tf-filter-custom-require-category-enabled" ><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[custom][require_category]" value="0" id="input-tf-filter-custom-require-category-disabled">
                      <label for="input-tf-filter-custom-require-category-disabled"><?php echo $text_disabled; ?></label>
                      <?php } else { ?>
                      <input type="radio" name="filter[custom][require_category]" value="1" id="input-tf-filter-custom-require-category-enabled" >
                      <label for="input-tf-filter-custom-require-category-enabled"><?php echo $text_enabled; ?></label>
                      <input type="radio" name="filter[custom][require_category]" value="0" id="input-tf-filter-custom-require-category-disabled" checked>
                      <label for="input-tf-filter-custom-require-category-disabled" ><?php echo $text_disabled; ?></label>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
                
            </div>
          </div>
        </div>
      </div> <!-- // Filter -->
      
      <!--Layout-->
      <div id="tab-tf-layout" class="tab-pane fade">
        <div class="panel mz-panel-default">
          <div class="panel-heading"><?php echo $text_general ?></div>
          <div class="panel-body">
            <!--collapse by default-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_collapse ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($collapsed){ ?>
                  <input type="radio" name="collapsed" value="1" id="input-collapsed-enabled" checked>
                  <label for="input-collapsed-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="collapsed" value="0" id="input-collapsed-disabled">
                  <label for="input-collapsed-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="collapsed" value="1" id="input-collapsed-enabled" >
                  <label for="input-collapsed-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="collapsed" value="0" id="input-collapsed-disabled" checked>
                  <label for="input-collapsed-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Ajax-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_ajax ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($ajax){ ?>
                  <input type="radio" name="ajax" value="1" id="input-ajax-enabled" checked>
                  <label for="input-ajax-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="ajax" value="0" id="input-ajax-disabled">
                  <label for="input-ajax-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="ajax" value="1" id="input-ajax-enabled" >
                  <label for="input-ajax-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="ajax" value="0" id="input-ajax-disabled" checked>
                  <label for="input-ajax-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Reset all-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-reset-all"><?php echo $entry_reset_all ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($reset_all){ ?>
                  <input type="radio" name="reset_all" value="1" id="input-reset-all-enabled" checked>
                  <label for="input-reset-all-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="reset_all" value="0" id="input-reset-all-disabled">
                  <label for="input-reset-all-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="reset_all" value="1" id="input-reset-all-enabled" >
                  <label for="input-reset-all-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="reset_all" value="0" id="input-reset-all-disabled" checked>
                  <label for="input-reset-all-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Reset per group-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-reset-group"><?php echo $entry_reset_group ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($reset_group){ ?>
                  <input type="radio" name="reset_group" value="1" id="input-reset-group-enabled" checked>
                  <label for="input-reset-group-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="reset_group" value="0" id="input-reset-group-disabled">
                  <label for="input-reset-group-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="reset_group" value="1" id="input-reset-group-enabled" >
                  <label for="input-reset-group-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="reset_group" value="0" id="input-reset-group-disabled" checked>
                  <label for="input-reset-group-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
          
        <div class="panel mz-panel-default">
          <div class="panel-heading"><?php echo $text_filter_value ?></div>
          <div class="panel-body">
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_overflow ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php foreach($overflow_types as $overflow_type) { ?>
                    <?php if($overflow_type['code'] == $overflow){ ?>
                    <input type="radio" name="overflow" value="<?php echo $overflow_type['code'] ?>" id="input-overflow-<?php echo $overflow_type['code'] ?>" checked>
                    <label for="input-overflow-<?php echo $overflow_type['code'] ?>" ><?php echo $overflow_type['text']; ?></label>
                    <?php } else { ?>
                    <input type="radio" name="overflow" value="<?php echo $overflow_type['code'] ?>" id="input-overflow-<?php echo $overflow_type['code'] ?>" >
                    <label for="input-overflow-<?php echo $overflow_type['code'] ?>"><?php echo $overflow_type['text']; ?></label>
                    <?php } ?>
                  <?php } ?>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-xs-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_hide_zero_filter; ?>"><?php echo $entry_hide_zero_filter; ?></span></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if ($hide_zero_filter) { ?>
                  <input type="radio" name="hide_zero_filter" value="1" id="input-zero-filter-hide" checked>
                  <label for="input-zero-filter-hide" ><?php echo $text_hide; ?></label>
                  <input type="radio" name="hide_zero_filter" value="0" id="input-zero-filter-disabled">
                  <label for="input-zero-filter-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="hide_zero_filter" value="1" id="input-zero-filter-hide" >
                  <label for="input-zero-filter-hide"><?php echo $text_hide; ?></label>
                  <input type="radio" name="hide_zero_filter" value="0" id="input-zero-filter-disabled" checked>
                  <label for="input-zero-filter-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
          
        <div class="panel mz-panel-default">
          <div class="panel-heading"><?php echo $text_filter_column ?></div>
          <div class="panel-body">
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-column-lg"><?php echo $entry_column_lg ?></label>
              <div class="col-xs-5">
                <div class="input-group">
                  <input type="range" name="column_lg" min="1" max="6" value="<?php echo $column_lg ?>" id="input-column-lg" class="form-control" />
                  <span class="input-group-addon"><?php echo $column_lg ?></span>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-column-md"><?php echo $entry_column_md ?></label>
              <div class="col-xs-5">
                <div class="input-group">
                  <input type="range" name="column_md" min="1" max="6" value="<?php echo $column_md ?>" id="input-column-md" class="form-control" />
                  <span class="input-group-addon"><?php echo $column_md ?></span>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-column-sm"><?php echo $entry_column_sm ?></label>
              <div class="col-xs-5">
                <div class="input-group">
                  <input type="range" name="column_sm" min="1" max="6" value="<?php echo $column_sm ?>" id="input-column-sm" class="form-control" />
                  <span class="input-group-addon"><?php echo $column_sm ?></span>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-column-xs"><?php echo $entry_column_xs ?></label>
              <div class="col-xs-5">
                <div class="input-group">
                  <input type="range" name="column_xs" min="1" max="6" value="<?php echo $column_xs ?>" id="input-column-xs" class="form-control" />
                  <span class="input-group-addon"><?php echo $column_xs ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
  <script>
$('input[name="filter[availability][stock_status]"]').on('change', function(){
	if($(this).val() == 1){
		$('.stock_status').removeClass('hide');
	} else {
    $('.stock_status').addClass('hide');
  }
});
$('input[name="filter[availability][stock_status]"]:checked').trigger('change');

$('input[type="range"]').on('input change', function(){
    $(this).siblings('.input-group-addon').text($(this).val());
});
//{#Confirm before delete module#}
$('#button-delete').on('click', function(){
    if(confirm("<?php echo $text_confirm ?>")){
        return true;
    } else {
        return false;
    }
});

</script>
</div>
<?php echo $tf_footer ?>