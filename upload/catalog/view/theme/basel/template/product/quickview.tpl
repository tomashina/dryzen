<div class="table product-info quickview-info hidden-xs" style="height:<?php echo $img_h; ?>px; width:980px;">
     
     <div class="table-cell left" style="padding-bottom:0;">
     
     <?php if ($thumb || $images) { ?>
     <div class="image-area" id="gallery" style="position:relative;">
            
        <?php if ($thumb) { ?>
        <div class="main-image qv_image carousel" style="width:<?php echo $img_w; ?>px;">
        <img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
        
        <?php if ($images) { ?>
        <?php foreach ($images as $image) { ?>
        <img src="<?php echo $image['thumb']; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" />
        <?php } ?>
        <?php } ?>
        
        </div>
        <?php } ?>
       
            
     </div> <!-- .table-cell.left ends -->
     <?php } ?>
      
     </div> <!-- .image-area ends -->

    <div class="table-cell w100 right">
	<div class="inner">
    
    <div class="product-h1">
    <h1><?php echo $heading_title; ?></h1>
    </div>
    
    <?php if ($review_status && ($review_qty > 0)) { ?>
    <div class="rating">
    <span class="rating_stars rating r<?php echo $rating; ?>">
    <i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
    </span>
    </div>
    <span class="review_link">(<a class="hover_uline to_reviews" href="<?php echo $product_href; ?>"><?php echo $reviews; ?></a>)</span>
	<?php } ?>

    <?php if ($price) { ?>
      <ul class="list-unstyled price">
        <?php if (!$special) { ?>
        <li><span class="live-price"><?php echo $price; ?><span></li>
        <?php } else { ?>
        <li><span class="price-old"><?php echo $price; ?></span><span class="live-price-new"><?php echo $special; ?><span></li>
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
		
          <input step="1" min="<?php echo $minimum; ?>" type="number" name="quantity" value="<?php echo $minimum; ?>" class="input-quantity" id="input-quantity" class="form-control" />
          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
          <button type="button" id="button-cart-quickview" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php if (($qty < 1) && ($stock_badge_status)) { ?><?php echo $basel_text_out_of_stock; ?><?php } else { ?><?php echo $button_cart; ?><?php } ?></button>
          
          
          <a href="<?php echo $product_href; ?>" class="btn btn-outline details"><?php echo $basel_text_view_details; ?></a>
          
            </div>
            <?php if ($minimum > 1) { ?>
            <div class="alert alert-sm alert-info"><i class="fa fa-info-circle"></i> <?php echo $text_minimum; ?></div>
            <?php } ?>
          
          </div><!-- #product ends -->

    
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
      
      
      <?php if ($basel_share_btn) { ?>
      <p class="info share"><b>Share:</b> 
        <a class="single_share fb_share external" rel="nofollow"><i class="fa fa-facebook"></i></a>
        <a class="single_share twitter_share external" rel="nofollow"><i class="fa fa-twitter"></i></a>
        <a class="single_share google_share external" rel="nofollow"><i class="icon-google-plus"></i></a>
        <a class="single_share pinterest_share external" rel="nofollow"><i class="fa fa-pinterest"></i></a>
        <a class="single_share vk_share external" rel="nofollow"><i class="fa fa-vk"></i></a>
      </p>
      <?php } ?>
     
     </div> <!-- .info-holder ends -->
     
	 </div> <!-- .inner ends -->
     </div> <!-- .table-cell.right ends -->
    
<link href="catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/jquery/datetimepicker/moment.js"></script>
<script src="catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js"></script>


<?php if ($basel_price_update) { ?>
<script src="index.php?route=extension/basel/live_options/js&product_id=<?php echo $product_id; ?>"></script>
<?php } ?>
<script>
$('.qv_image').slick({
<?php if ($direction == 'rtl') { ?>
rtl: true,
<?php } ?>
prevArrow: "<a class=\"arrow-left within icon-arrow-left\"></a>",
nextArrow: "<a class=\"arrow-right within icon-arrow-right\"></a>",
arrows:true
});
// Sharing buttons
var share_url = encodeURIComponent('<?php echo $product_href; ?>');
var page_title = '<?php echo $heading_title ?>';
<?php if ($thumb) { ?>
var thumb = '<?php echo $thumb ?>';
<?php } ?>
$('.fb_share').attr("href", 'https://www.facebook.com/sharer/sharer.php?u=' + share_url + '');
$('.twitter_share').attr("href", 'https://twitter.com/intent/tweet?source=' + share_url + '&text=' + page_title + ': ' + share_url + '');
$('.google_share').attr("href", 'https://plus.google.com/share?url=' + share_url + '');
$('.pinterest_share').attr("href", 'http://pinterest.com/pin/create/button/?url=' + share_url + '&media=' + thumb + '&description=' + page_title + '');
$('.vk_share').attr("href", 'http://vkontakte.ru/share.php?url=' + share_url + '');

// Open external links in new tab //
$('a.external').on('click',function(e){
e.preventDefault();
window.open($(this).attr('href'));
});
</script>

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
$('#button-cart-quickview').on('click', function() {
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

</div> <!-- .product-info ends -->