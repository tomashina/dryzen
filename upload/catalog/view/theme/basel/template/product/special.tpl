<?php echo $header; ?>
<div class="container">
  
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  
  <div class="row">
  
  <?php echo $column_left; ?>
    
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
<?php echo $footer; ?>