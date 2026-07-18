<?php echo $header; ?>
<div class="container">
  
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  
  <div class="row"><?php echo $column_left; ?>
    
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9 col-sm-8'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    
    <div id="content" class="<?php echo $class; ?>">
    
    <?php echo $content_top; ?>
      
      <h1 id="page-title"><?php echo $heading_title; ?></h1>
      
      <legend><?php echo $entry_search; ?></legend>
          
      <div class="row">
        <div class="col-sm-6 margin-b10">
          <input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo $text_keyword; ?>" id="input-search" class="form-control" />
        </div>
        <div class="col-sm-6 margin-b10">
          <select name="category_id" class="form-control">
            <option value="0"><?php echo $text_category; ?></option>
            <?php foreach ($categories as $category_1) { ?>
            <?php if ($category_1['category_id'] == $category_id) { ?>
            <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
            <?php } ?>
            <?php foreach ($category_1['children'] as $category_2) { ?>
            <?php if ($category_2['category_id'] == $category_id) { ?>
            <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
            <?php } ?>
            <?php foreach ($category_2['children'] as $category_3) { ?>
            <?php if ($category_3['category_id'] == $category_id) { ?>
            <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
            <?php } ?>
            <?php } ?>
            <?php } ?>
            <?php } ?>
          </select>
        </div>
        </div>
        <div class="form-group">
          
        <label class="checkbox-inline">
          <?php if ($description) { ?>
          <input type="checkbox" name="description" value="1" id="description" checked="checked" />
          <?php } else { ?>
          <input type="checkbox" name="description" value="1" id="description" />
          <?php } ?>
          <?php echo $entry_description; ?></label>
       <label class="checkbox-inline">
            <?php if ($sub_category) { ?>
            <input type="checkbox" name="sub_category" value="1" checked="checked" />
            <?php } else { ?>
            <input type="checkbox" name="sub_category" value="1" />
            <?php } ?>
            <?php echo $text_sub_category; ?></label>
      </div>
      
      <input type="button" value="<?php echo $button_search; ?>" id="button-search" class="btn btn-primary margin-b30" />
      
      <?php echo $position_category_top; ?>
      
      <?php if ($products) { ?>
      <div id="product-view" class="grid">
      
      <div class="table filter">
      
      	<div class="table-cell nowrap hidden-sm hidden-md hidden-lg"><a class="filter-trigger-btn"></a></div>
          
          <div class="table-cell nowrap hidden-xs">
          <a id="grid-view" class="view-icon grid" data-toggle="tooltip" data-title="<?php echo $button_grid; ?>"><i class="fa fa-th"></i></a>
          <a id="list-view" class="view-icon list" data-toggle="tooltip" data-title="<?php echo $button_list; ?>"><i class="fa fa-th-list"></i></a>
          </div>
          
          <div class="table-cell w100">
          <a href="<?php echo $compare; ?>" id="compare-total" class="hidden-xs"><?php echo $text_compare; ?></a>
          </div>
          
          <div class="table-cell nowrap text-right">
          <div class="sort-select">
          <span class="hidden-xs"><?php echo $text_sort; ?></span>
          <select id="input-sort" class="form-control input-sm inline" onchange="location = this.value;">
            <?php foreach ($sorts as $sorts) { ?>
            <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
            <option value="<?php echo $sorts['href']; ?>" selected="selected"> <?php echo $sorts['text']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $sorts['href']; ?>" ><?php echo $sorts['text']; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
          </div>
          </div>
          
          <div class="table-cell nowrap text-right hidden-xs hidden-sm">
          <span><?php echo $text_limit; ?></span>
          <select id="input-limit" class="form-control input-sm inline" onchange="location = this.value;">
            <?php foreach ($limits as $limits) { ?>
            <?php if ($limits['value'] == $limit) { ?>
            <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
          </div>
      
      </div>
      
      <div class="grid-holder product-holder grid<?php echo $basel_prod_grid; ?>">
        <?php foreach ($products as $product) { ?>
        <?php require('catalog/view/theme/basel/template/product/single_product.tpl'); ?>
        <?php } ?>
      </div>
      </div> <!-- #product-view ends -->
      
      <div class="row pagination-holder">
        <div class="col-sm-6 xs-text-center"><?php echo str_replace(array("&gt;|","|&lt;"),array("&gt;&gt", "&lt;&lt"),$pagination); ?></div>
        <div class="col-sm-6 text-right xs-text-center"><span class="pagination-text"><?php echo $results; ?></span></div>
      </div>
      
      <?php } else { ?>
      	<p><?php echo $text_empty; ?></p>
      <?php } ?>
      
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script><!--
$('#button-search').bind('click', function() {
	url = 'index.php?route=product/search';

	var search = $('#content input[name=\'search\']').prop('value');

	if (search) {
		url += '&search=' + encodeURIComponent(search);
	}

	var category_id = $('#content select[name=\'category_id\']').prop('value');

	if (category_id > 0) {
		url += '&category_id=' + encodeURIComponent(category_id);
	}

	var sub_category = $('#content input[name=\'sub_category\']:checked').prop('value');

	if (sub_category) {
		url += '&sub_category=true';
	}

	var filter_description = $('#content input[name=\'description\']:checked').prop('value');

	if (filter_description) {
		url += '&description=true';
	}

	location = url;
});

$('#content input[name=\'search\']').bind('keydown', function(e) {
	if (e.keyCode == 13) {
		$('#button-search').trigger('click');
	}
});

$('select[name=\'category_id\']').on('change', function() {
	if (this.value == '0') {
		$('input[name=\'sub_category\']').prop('disabled', true);
	} else {
		$('input[name=\'sub_category\']').prop('disabled', false);
	}
});

$('select[name=\'category_id\']').trigger('change');
--></script>
<?php echo $footer; ?>