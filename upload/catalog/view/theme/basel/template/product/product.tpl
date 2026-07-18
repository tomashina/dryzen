<?php echo $header; ?>

<?php if ($product_layout != "full-width") { ?>
<style>
.product-page .image-area {
	<?php if (($product_layout == "images-left") && $images) { ?> 
		width: <?php echo ($img_w + $img_a_w + 20); ?>px;
	<?php } else { ?>
		width: <?php echo $img_w; ?>px;
	<?php } ?>
}
.product-page .main-image {
	width:<?php echo $img_w; ?>px;	
}
.product-page .image-additional {
	<?php if ($product_layout == "images-left") { ?> 
		width: <?php echo $img_a_w; ?>px;
		height: <?php echo $img_h; ?>px;
	<?php } else { ?>
		width: <?php echo $img_w; ?>px;
	<?php } ?>
}
.product-page .image-additional.has-arrows {
	<?php if ($product_layout == "images-left") { ?> 
		height: <?php echo ($img_h - 40); ?>px;
	<?php } ?>
}
@media (min-width: 992px) and (max-width: 1199px) {
.product-page .image-area {
	<?php if ($product_layout == "images-left") { ?> 
		width: <?php echo (($img_w + $img_a_w)/1.25 + 20); ?>px;
	<?php } else { ?>
		width: <?php echo $img_w/1.25; ?>px;
	<?php } ?>
}
.product-page .main-image {
	width:<?php echo $img_w/1.25; ?>px;	
}
.product-page .image-additional {
	<?php if ($product_layout == "images-left") { ?> 
		width: <?php echo $img_a_w/1.25; ?>px;
		height: <?php echo $img_h/1.25; ?>px;
	<?php } else { ?>
		width: <?php echo $img_w/1.25; ?>px;
	<?php } ?>
}
}
</style>
<?php } ?>

<ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>

<div class="container product-layout <?php echo $product_layout; ?>">
  
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9 col-sm-8'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="product-main no-min-height <?php echo $class; ?>">
    <?php echo $content_top; ?>
    
    <div class="table product-info product-page">
     
     <div class="table-cell left">
     
     <?php if ($thumb || $images) { ?>
     <div class="image-area <?php if (!$hover_zoom) { echo "hover-zoom-disabled"; } ?>" id="gallery">
            
        <?php if ($thumb) { ?>
        <div class="main-image">
        
        <?php if (($price) && ($special) && ($sale_badge)) { ?>
        <span class="badge sale_badge"><i><?php echo $sale_badge; ?></i></span>
        <?php } ?>
        
        <?php if (isset($is_new)) { ?>
        <span class="badge new_badge"><i><?php echo $basel_text_new; ?></i></span>
        <?php } ?>
        
        <?php if (($qty < 1) && ($stock_badge_status)) { ?>
        <span class="badge out_of_stock_badge"><i><?php echo $basel_text_out_of_stock; ?></i></span>
        <?php } ?>

        <a class="<?php if (!$images) { echo "link cloud-zoom"; } else if (($product_layout == 'full-width')) { echo "link"; } else { echo "cloud-zoom"; } ?>" id="main-image" href="<?php echo $popup; ?>" rel="position:'inside', showTitle: false"><img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
        </div>
        <?php } ?>
       
        <?php if ($images) { ?>
        <ul class="image-additional">
        <?php foreach ($images as $image) { ?>
        <li>
        <a class="link <?php if ($product_layout != 'full-width') { echo "cloud-zoom-gallery locked"; } ?>" href="<?php echo $image['popup']; ?>" rel="useZoom: 'main-image', smallImage: '<?php echo $image['thumb_lg']; ?>'"><img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a>
        </li>
        <?php } ?>
            <?php if ($thumb && ($product_layout != "full-width")) { ?>
            <li><a class="link cloud-zoom-gallery locked active" href="<?php echo $popup; ?>" rel="useZoom: 'main-image', smallImage: '<?php echo $thumb; ?>'"><img src="<?php echo $thumb_sm; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" /></a></li>
            <?php } ?>
        </ul>

        <?php } ?>
            
     </div> <!-- .table-cell.left ends -->
     <?php } ?>
      
     </div> <!-- .image-area ends -->
     
     
    <div class="table-cell w100 right">
	<div class="inner">
    
    <div class="product-h1">
    <h1 id="page-title"><?php echo $heading_title; ?></h1>
    </div>
    
    <?php if ($review_status && ($review_qty > 0)) { ?>
    <div class="rating">
    <span class="rating_stars rating r<?php echo $rating; ?>">
    <i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
    </span>
    </div>
    <span class="review_link">(<a class="hover_uline to_tabs" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $reviews; ?></a>)</span>
	<?php } ?>

    <?php if ($price) { ?>
      <ul class="list-unstyled price">
        <?php if (!$special) { ?>
        <li><span class="live-price"><?php echo $price; ?><span></li>
        <?php } else { ?>
        <li><span class="price-old"><?php echo $price; ?></span><span class="live-price-new"><?php echo $special; ?><span></li>
        <span id="special_countdown"></span>
        <?php } ?>
      </ul>
        
        <?php if ($discounts) { ?>
        <p class="discount">
        <?php foreach ($discounts as $discount) { ?>
        <span><?php echo $discount['quantity']; ?><?php echo $text_discount; ?><i class="price"><?php echo $discount['price']; ?></i></span>
        <?php } ?>
        </p>
        <?php } ?>
      
      <?php } ?> <!-- if price ends -->
      
      
      <?php if ($meta_description_status && $meta_description) { ?>
      <p class="meta_description"><?php echo $meta_description; ?></p>
      <?php } ?>
      
      
      
      
      <div id="product">
            
            <?php if ($options) { ?>
            <div class="options">
            <?php foreach ($options as $option) { ?>
            
            <?php if ($option['type'] == 'select') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell name">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell">
              <select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
                <option value=""><?php echo $text_select; ?></option>
                <?php foreach ($option['product_option_value'] as $option_value) { ?>
                <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                <?php if ($option_value['price']) { ?>
                (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                <?php } ?>
                </option>
                <?php } ?>
              </select>
              </div>
            </div>
            <?php } ?>
            
            <?php if ($option['type'] == 'radio') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell radio-cell name">
              <label class="control-label"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell radio-cell">
              <div id="input-option<?php echo $option['product_option_id']; ?>">
                <?php foreach ($option['product_option_value'] as $option_value) { ?>
                <div class="radio<?php if ($option_value['image']) echo ' has-image'; ?>">
                  <label>
                    <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                    <?php if ($option_value['image']) { ?>
                    <img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" data-toggle="tooltip" data-title="<?php echo $option_value['name']; ?><?php if ($option_value['price']) { ?> (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)<?php } ?>" />
                    <?php } ?>
                    <span class="name">
                    <?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                    </span>
                  </label>
                </div>
                <?php } ?>
              </div>
              </div>
            </div>
            <?php } ?>
            
            <?php if ($option['type'] == 'checkbox') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell checkbox-cell name">
              <label class="control-label"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell checkbox-cell">
              <div id="input-option<?php echo $option['product_option_id']; ?>">
                <?php foreach ($option['product_option_value'] as $option_value) { ?>
                <div class="checkbox<?php if ($option_value['image']) echo ' has-image'; ?>">
                  <label>
                    <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                    <?php if ($option_value['image']) { ?>
                    <img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" data-toggle="tooltip" data-title="<?php echo $option_value['name']; ?><?php if ($option_value['price']) { ?> (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)<?php } ?>" /> 
                    <?php } ?>
                    <span class="name">
                    <?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                    </span>
                  </label>
                </div>
                <?php } ?>
              </div>
              </div>
            </div>
            <?php } ?>
            
            
            <?php if ($option['type'] == 'text') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell name">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell">
              <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
              </div>
            </div>
            <?php } ?>
            
            <?php if ($option['type'] == 'textarea') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell name">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell">
              <textarea name="option[<?php echo $option['product_option_id']; ?>]" rows="5" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control"><?php echo $option['value']; ?></textarea>
              </div>
            </div>
            <?php } ?>
            
            <?php if ($option['type'] == 'file') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell name">
              <label class="control-label"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell">
              <button type="button" id="button-upload<?php echo $option['product_option_id']; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
              <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" id="input-option<?php echo $option['product_option_id']; ?>" />
              </div>
            </div>
            <?php } ?>
            
            <?php if ($option['type'] == 'date') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell name">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell">
              <div class="input-group date">
                <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                </span></div>
                </div>
            </div>
            <?php } ?>
            
            <?php if ($option['type'] == 'datetime') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell name">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell">
              <div class="input-group datetime">
                <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
                </div>
            </div>
            <?php } ?>
            
            <?php if ($option['type'] == 'time') { ?>
            <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> table-row">
              <div class="table-cell name">
              <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
              </div>
              <div class="table-cell">
              <div class="input-group time">
                <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                <span class="input-group-btn">
                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                </span></div>
                </div>
            </div>
            <?php } ?>
            
            <?php } ?> <!-- foreach option -->
            </div>
            <?php } ?>
            
            <?php if ($recurrings) { ?>
            <hr>
            <h3><?php echo $text_payment_recurring ?></h3>
            <div class="form-group required">
              <select name="recurring_id" class="form-control">
                <option value=""><?php echo $text_select; ?></option>
                <?php foreach ($recurrings as $recurring) { ?>
                <option value="<?php echo $recurring['recurring_id'] ?>"><?php echo $recurring['name'] ?></option>
                <?php } ?>
              </select>
              <div class="help-block" id="recurring-description"></div>
            </div>
            <?php } ?>
            
            <div class="form-group buy catalog_hide">

            <input type="number" step="1" min="<?php echo $minimum; ?>" name="quantity" value="<?php echo $minimum; ?>" id="input-quantity" class="form-control input-quantity" />
              <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
              <button type="button" id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php if (($qty < 1) && ($stock_badge_status)) { ?><?php echo $basel_text_out_of_stock; ?><?php } else { ?><?php echo $button_cart; ?><?php } ?></button>
            </div>
            <?php if ($minimum > 1) { ?>
            <div class="alert alert-sm alert-info"><i class="fa fa-info-circle"></i> <?php echo $text_minimum; ?></div>
            <?php } ?>
          
          </div><!-- #product ends -->


	<p class="info is_wishlist"><a onclick="wishlist.add('<?php echo $product_id; ?>');"><i class="icon-heart"></i> <?php echo $button_wishlist; ?></a></p>
	<p class="info is_compare"><a onclick="compare.add('<?php echo $product_id; ?>');"><i class="icon-refresh"></i> <?php echo $button_compare; ?></a></p>
    <?php if (!empty($question_status)) { ?>
    <p class="info is_ask"><a class="to_tabs" onclick="$('a[href=\'#tab-questions\']').trigger('click'); return false;"><i class="icon-question"></i> <?php echo $basel_button_ask; ?></a></p>
    <?php } ?>
    
    <div class="clearfix"></div>
    
    <div class="info-holder">
    
      <?php if ($price && $tax) { ?>
      <p class="info p-tax"><b><?php echo $text_tax; ?></b> <span class="live-price-tax"><?php echo $tax; ?></span></p>
      <?php } ?>
      
      <?php if ($price && $points) { ?>
      <p class="info"><b><?php echo $text_points; ?></b> <?php echo $points; ?></p>
      <?php } ?>
      
      <p class="info <?php if ($qty > 0) { echo "in_stock"; } ?>"><b><?php echo $text_stock; ?></b> <?php echo $stock; ?></p>
      
      <?php if ($manufacturer) { ?>
      <p class="info"><b><?php echo $text_manufacturer; ?></b> <a class="hover_uline" href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></p>
      <?php } ?>
      
      <p class="info"><b><?php echo $text_model; ?></b> <?php echo $model; ?></p>
      
      <?php if ($reward) { ?>
      <p class="info"><b><?php echo $text_reward; ?></b> <?php echo $reward; ?></p>
      <?php } ?>
      
      <?php if ($tags) { ?>
      <p class="info tags"><b><?php echo $text_tags; ?></b> &nbsp;<span><?php foreach ($tags as $tag) { ?><a class="hover_uline" href="<?php echo $tag['href']; ?>"><?php echo $tag['tag']; ?></a><?php } ?></span></p>
      <?php } ?>
      
      <?php if ($basel_share_btn) { ?>
        <?php if ($basel_sharing_style == 'large') { ?>
            <div class="lg-share">
            <div class="social-icons round inversed">
            <a class="icon facebook fb_share external" rel="nofollow"><i class="fa fa-facebook"></i></a>
            <a class="icon twitter twitter_share external" rel="nofollow"><i class="fa fa-twitter"></i></a>
            <a class="icon google google_share external" rel="nofollow"><i class="icon-google-plus"></i></a>
            <a class="icon pinterest pinterest_share external" rel="nofollow"><i class="fa fa-pinterest"></i></a>
            <a class="icon vk vk_share external" rel="nofollow"><i class="fa fa-vk"></i></a>
            </div>
            </div>
        <?php } else { ?>
            <p class="info share"><b><?php echo $basel_text_share; ?>:</b> 
            <a class="single_share fb_share external" rel="nofollow"><i class="fa fa-facebook"></i></a>
            <a class="single_share twitter_share external" rel="nofollow"><i class="fa fa-twitter"></i></a>
            <a class="single_share google_share external" rel="nofollow"><i class="icon-google-plus"></i></a>
            <a class="single_share pinterest_share external" rel="nofollow"><i class="fa fa-pinterest"></i></a>
            <a class="single_share vk_share external" rel="nofollow"><i class="fa fa-vk"></i></a>
            </p>
        <?php } ?>      
      <?php } ?>
     
     </div> <!-- .info-holder ends -->
     
	 </div> <!-- .inner ends -->
     </div> <!-- .table-cell.right ends -->
    
    </div> <!-- .product-info ends -->
    
<?php if ($full_width_tabs) { ?>
</div> <!-- main column ends -->
<?php echo $column_right; ?>
</div> <!-- .row ends -->
</div> <!-- .container ends -->    
<?php } ?>


<?php if ($full_width_tabs) { ?>
<div class="outer-container product-tabs-wrapper">
<div class="container">   
<?php } else { ?>
<div class="inline-tabs"> 
<?php } ?>

<!-- Tabs area start -->
<div class="row">
<div class="col-sm-12">
  
  <ul class="nav nav-tabs <?php echo $product_tabs_style; ?> main_tabs">
    <li class="active"><a href="#tab-description" data-toggle="tab"><?php echo $tab_description; ?></a></li>
    <?php if ($product_tabs) { ?>
        <?php foreach ($product_tabs as $tab) { ?>
        <li><a href="#custom-tab-<?php echo $tab['tab_id']; ?>" data-toggle="tab"><?php echo $tab['name']; ?></a></li>
        <?php } ?>
    <?php } ?>
    <?php if ($attribute_groups) { ?>
    <li><a href="#tab-specification" data-toggle="tab"><?php echo $tab_attribute; ?></a></li>
    <?php } ?>
    <?php if ($review_status) { ?>
    <li><a href="#tab-review" data-toggle="tab"><?php echo $tab_review; ?></a></li>
    <?php } ?>
    <?php if (!empty($question_status)) { ?>
    <li><a href="#tab-questions" data-toggle="tab"><?php echo $basel_tab_questions; ?> (<?php echo $questions_total; ?>)</a></li>
    <?php } ?>
  </ul>
  
  <div class="tab-content">
    
    <div class="tab-pane active" id="tab-description">
    <?php echo $description; ?>
    </div>
    
    <?php if ($product_tabs) { ?>
    <?php foreach ($product_tabs as $tab) { ?>
    <div class="tab-pane" id="custom-tab-<?php echo $tab['tab_id']; ?>">
    <?php echo $tab['description']; ?>
    </div>
    <?php } ?>
    <?php } ?>
    
    <?php if ($attribute_groups) { ?>
    <div class="tab-pane" id="tab-specification">
      <table class="table specification">
        <?php foreach ($attribute_groups as $attribute_group) { ?>
        <thead>
          <tr>
            <td colspan="2"><?php echo $attribute_group['name']; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
          <tr>
            <td class="text-left"><b><?php echo $attribute['name']; ?></b></td>
            <td class="text-right"><?php echo $attribute['text']; ?></td>
          </tr>
          <?php } ?>
        </tbody>
        <?php } ?>
      </table>
    </div>
    <?php } ?>
    
    <?php if (!empty($question_status)) { ?>
    <div class="tab-pane" id="tab-questions">
    <?php echo $product_questions; ?>
    </div>
    <?php } ?>
    
    <?php if ($review_status) { ?>
    <div class="tab-pane" id="tab-review">
    <div class="row">
    <div class="col-sm-6">
    <h4><b><?php echo $button_reviews; ?></b></h4>

        <div id="review">
		<?php if ($seo_reviews) { ?>
		
        <?php foreach ($seo_reviews as $review) { ?>
		<div class="table">
		<div class="table-cell"><i class="fa fa-user"></i></div>
		<div class="table-cell right">
		<p class="author"><b><?php echo $review['author']; ?></b>  -  <?php echo $review['date_added']; ?>
		<span class="rating">
		<span class="rating_stars rating r<?php echo $review['rating']; ?>">
		<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
		</span>
		</span>
		</p>
		<?php echo $review['text']; ?>
		</div>
		</div>
		<?php } ?>
		
        <?php if ($pagination) { ?>
		<div class="pagination-holder"><?php echo $pagination; ?></div>
		<?php } ?>
        
        <?php } else { ?>
		<p><?php echo $text_no_reviews; ?></p>
        <?php } ?>
		</div>
        
    </div>
    <div class="col-sm-6 right">
      <form class="form-horizontal" id="form-review">
        
        <h4 id="review-notification"><b><?php echo $text_write; ?></b></h4>
        <?php if ($review_guest) { ?>
        
        <div class="form-group required">
          <div class="col-sm-12 rating-stars">
            <label class="control-label"><?php echo $entry_rating; ?></label>
            
            <input type="radio" value="1" name="rating" id="rating1" />
        	<label for="rating1"><i class="fa fa-star-o"></i></label>
            
            <input type="radio" value="2" name="rating" id="rating2" />
        	<label for="rating2"><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i></label>
            
            <input type="radio" value="3" name="rating" id="rating3" />
        	<label for="rating3"><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i></label>
            
            <input type="radio" value="4" name="rating" id="rating4" />
        	<label for="rating4"><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i></label>
            
            <input type="radio" value="5" name="rating" id="rating5" />
        	<label for="rating5"><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i></label>
            </div>
        </div>
        
        <div class="form-group required">
          <div class="col-sm-12">
            <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
            <input type="text" name="name" value="<?php echo $customer_name; ?>" id="input-name" class="form-control grey" />
          </div>
        </div>
        <div class="form-group required">
          <div class="col-sm-12">
            <label class="control-label" for="input-review"><?php echo $entry_review; ?></label>
            <textarea name="text" rows="5" id="input-review" class="form-control grey"></textarea>
            <small><?php echo $text_note; ?></small>
          </div>
        </div>
        
        <div class="form-group required">
          <div class="col-sm-12">
            <?php echo $captcha; ?>
          </div>
        </div>
        
        <div class="buttons clearfix">
          <div class="text-right">
          <button type="button" id="button-review" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-outline"><?php echo $button_continue; ?></button>
          </div>
        </div>
        <?php } else { ?>
        <?php echo $text_login; ?>
        <?php } ?>
      </form>
       </div>
      </div>
    </div>
    <?php } ?> <!-- if review-status ends -->
    
  </div> <!-- .tab-content ends -->
</div> <!-- .col-sm-12 ends -->
</div> <!-- .row ends -->
<!-- Tabs area ends -->

<?php if ($full_width_tabs) { ?>
</div>
<?php } ?>
</div>
      
      <!-- Related Products -->
      
    <?php if ($full_width_tabs) { ?>
    <div class="container">  
    <?php } ?>
      
        <?php if ($products) { ?>
        <div class="widget widget-related">
        
        <div class="widget-title">
        <p class="main-title"><span><?php echo $text_related; ?></span></p>
        <p class="widget-title-separator"><i class="icon-line-cross"></i></p>
        </div>
        
        <div class="grid grid-holder related carousel grid<?php echo $basel_rel_prod_grid; ?>">
            <?php foreach ($products as $product) { ?>
              <?php require('catalog/view/theme/basel/template/product/single_product.tpl'); ?>
            <?php } ?>
        </div>
        </div>
        <?php } ?>
      
      
      <?php echo $content_bottom; ?>
      
    <?php if ($full_width_tabs) { ?>
    </div>  
    <?php } ?>


<?php if (!$full_width_tabs) { ?>
</div> <!-- main column ends -->
<?php echo $column_right; ?>
</div> <!-- .row ends -->
</div> <!-- .container ends -->
<?php } ?>

<script src="catalog/view/theme/basel/js/lightgallery/js/lightgallery.min.js"></script>
<script src="catalog/view/theme/basel/js/lightgallery/js/lg-zoom.min.js"></script>
<script src="catalog/view/theme/basel/js/cloudzoom/cloud-zoom.1.0.2.min.js"></script>
<?php if ($basel_price_update) { ?>
<script src="index.php?route=extension/basel/live_options/js&product_id=<?php echo $product_id; ?>"></script>
<?php } ?>

<?php if ($products) { ?>
<script><!--
$('.grid-holder.related').slick({
prevArrow: "<a class=\"arrow-left icon-arrow-left\"></a>",
nextArrow: "<a class=\"arrow-right icon-arrow-right\"></a>",
dots:true,
<?php if ($direction == 'rtl') { ?>
rtl: true,
<?php } ?>
respondTo:'min',
<?php if ($basel_rel_prod_grid == '5') { ?>
slidesToShow:5,slidesToScroll:5,responsive:[{breakpoint:1100,settings:{slidesToShow:4,slidesToScroll:4}},{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($basel_rel_prod_grid == '4') { ?>
slidesToShow:4,slidesToScroll:4,responsive:[{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($basel_rel_prod_grid == '3') { ?>
slidesToShow:3,slidesToScroll:3,responsive:[{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($basel_rel_prod_grid == '2') { ?>
slidesToShow:2,slidesToScroll:2,responsive:[
<?php } ?>
<?php if ($items_mobile_fw) { ?>
{breakpoint:320,settings:{slidesToShow:1,slidesToScroll:1}}
<?php } ?>
]
});
$('.product-style2 .single-product .icon').attr('data-placement', 'top');
$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
//--></script>
<?php } ?>

<?php if (isset($sale_end_date) && $product_page_countdown) { ?>
 <script>
  $(function() {
	$('#special_countdown').countdown('<?php echo $sale_end_date; ?>').on('update.countdown', function(event) {
  var $this = $(this).html(event.strftime(''
    + '<div class=\"special_countdown\"></span><p><span class=\"icon-clock\"></span> <?php echo $basel_text_offer_ends; ?></p><div>'
    + '%D<i><?php echo $basel_text_days; ?></i></div><div>'
    + '%H <i><?php echo $basel_text_hours; ?></i></div><div>'
    + '%M <i><?php echo $basel_text_mins; ?></i></div><div>'
    + '%S <i><?php echo $basel_text_secs; ?></i></div></div>'));
});
  });
</script>
<?php } ?>

<script><!--
$('select[name=\'recurring_id\'], input[name="quantity"]').change(function(){
	$.ajax({
		url: 'index.php?route=product/product/getRecurringDescription',
		type: 'post',
		data: $('input[name=\'product_id\'], input[name=\'quantity\'], select[name=\'recurring_id\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#recurring-description').html('');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();

			if (json['success']) {
				$('#recurring-description').html(json['success']);
			}
		}
	});
});
//--></script>

<script><!--
$('#button-cart').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/basel/basel_features/add_to_cart',
		type: 'post',
		data: $('#product input[type=\'text\'], #product input[type=\'number\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
		dataType: 'json',
		beforeSend: function(json) {
			$('body').append('<span class="basel-spinner ajax-call"></span>');
		},

		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.table-cell').removeClass('has-error');

			if (json['error']) {
				$('.basel-spinner.ajax-call').remove();
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}

				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}

				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}

				if (json['success_redirect']) {
					
					location = json['success_redirect'];
				
				} else if (json['success']) {
					
					$('.table-cell').removeClass('has-error');
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
});
//--></script>
<script><!--
$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});

$('button[id^=\'button-upload\']').on('click', function() {
	var node = this;

	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input').attr('value', json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
//--></script>
<script><!--
$('#review').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();
	$("html,body").animate({scrollTop:(($("#review").offset().top)-50)},500);
    $('#review').fadeOut(50);

    $('#review').load(this.href);

    $('#review').fadeIn(500);
	
});


$('#button-review').on('click', function() {
	$.ajax({
		url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: $("#form-review").serialize(),
		beforeSend: function() {
			$('#button-review').button('loading');
		},
		complete: function() {
			$('#button-review').button('reset');
		},
		success: function(json) {
			$('.alert-success, .alert-danger').remove();

			if (json['error']) {
				$('#review-notification').after('<div class="alert alert-sm alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}

			if (json['success']) {
				$('#review-notification').after('<div class="alert alert-sm alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

				$('input[name=\'name\']').val('');
				$('textarea[name=\'text\']').val('');
				$('input[name=\'rating\']:checked').prop('checked', false);
			}
		}
	});
});

$(document).ready(function() {
<?php if ($product_layout == "full-width") { ?>
// Sticky information
$('.table-cell.right .inner').theiaStickySidebar({containerSelector:'.product-info'});
<?php } ?>

// Reviews/Question scroll link
$(".to_tabs").click(function() {
    $('html, body').animate({
        scrollTop: ($(".main_tabs").offset().top - 100)
    }, 1000);
});

// Sharing buttons
<?php if ($basel_share_btn) { ?>
var share_url = encodeURIComponent(window.location.href);
var page_title = '<?php echo $heading_title ?>';
<?php if ($thumb) { ?>
var thumb = '<?php echo $thumb ?>';
<?php } ?>
$('.fb_share').attr("href", 'https://www.facebook.com/sharer/sharer.php?u=' + share_url + '');
$('.twitter_share').attr("href", 'https://twitter.com/intent/tweet?source=' + share_url + '&text=' + page_title + ': ' + share_url + '');
$('.google_share').attr("href", 'https://plus.google.com/share?url=' + share_url + '');
$('.pinterest_share').attr("href", 'http://pinterest.com/pin/create/button/?url=' + share_url + '&media=' + thumb + '&description=' + page_title + '');
$('.vk_share').attr("href", 'http://vkontakte.ru/share.php?url=' + share_url + '');
<?php } ?>
});
//--></script>

<?php if ($product_layout != "full-width") { ?>
<script>
$(document).ready(function() {
$('.image-additional a.link').click(function (e) {
	if ($(this).hasClass("locked")) {
  		e.stopImmediatePropagation();
	}
	$('.image-additional a.link.active').removeClass('active');
	$(this).addClass('active')
});

<?php if ($images) { ?>
$('.cloud-zoom-wrap').click(function (e) {
	e.preventDefault();
	$('.image-additional a.link.active').removeClass('locked').trigger('click').addClass('locked');
});
<?php } else { ?>
$('.cloud-zoom-wrap').click(function (e) {
	e.preventDefault();
	$('#main-image').trigger('click');
});
<?php } ?>

$('.image-additional').slick({
prevArrow: "<a class=\"icon-arrow-left\"></a>",
nextArrow: "<a class=\"icon-arrow-right\"></a>",
appendArrows: '.image-additional .slick-list',
arrows:true,
<?php if ($direction == 'rtl') { ?>
rtl: true,
<?php } ?>
infinite:false,
<?php if ($product_layout == "images-left") { ?>
slidesToShow: <?php echo floor(($img_h)/$img_a_h) ;?>,
vertical:true,
verticalSwiping:true,
<?php } else { ?>
slidesToShow: <?php echo round($img_w/$img_a_w) ;?>,
<?php } ?>
responsive: [
{
breakpoint: 992,
settings: {
vertical:false,
verticalSwiping:false
}
}]
});	

});
//--></script>
<?php } ?>

<script>
$(document).ready(function() {
// Image Gallery
$("#gallery").lightGallery({
	selector: '.link',
	download:false,
	hideBarsDelay:99999
});
});
//--></script>
<script type="application/ld+json">
{
"@context": "http://schema.org",
"@type": "Product",
"image": [
<?php if ($thumb) { ?>
"<?php echo $thumb; ?>"
<?php } ?>
],
"description": "<?php echo $meta_description; ?>",
<?php if ($review_qty) { ?>
"aggregateRating": {
"@type": "AggregateRating",
"ratingValue": "<?php echo $rating; ?>",
"reviewCount": "<?php echo $review_qty; ?>"},
<?php } ?>
"name": "<?php echo $heading_title; ?>",
"sku": "<?php echo $model; ?>",
<?php if ($manufacturer) { ?>
"brand": "<?php echo $manufacturer; ?>",
<?php } ?>
"offers": {
"@type": "Offer",
<?php if ($qty > 0) { ?>
"availability": "http://schema.org/InStock",
<?php } else { ?>
"availability": "http://schema.org/OutOfStock",
<?php } ?>
<?php if ($price) { ?>
<?php if ($special) { ?>
"price": "<?php echo $special_snippet; ?>",
<?php } else { ?>
"price": "<?php echo $price_snippet; ?>",
<?php } ?>
<?php } ?>
"priceCurrency": "<?php echo $currency_code; ?>"
}
}
</script>
<?php echo $footer; ?>