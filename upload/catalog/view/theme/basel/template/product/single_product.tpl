<div class="item single-product">
<div class="image" <?php if ((isset($columns)) && ($columns == 'list')) echo 'style="width:' . $img_width . 'px"'; ?>>
    <a href="<?php echo $product['href']; ?>">
    <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" />
    <?php if ($product['thumb2']) { ?>
    <img class="thumb2" src="<?php echo $product['thumb2']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" />
    <?php } ?>
    </a>
<?php if (($product['price']) && ($product['special']) && ($salebadge_status)) { ?>
    <div class="sale-counter id<?php echo $product['product_id']; ?>"></div>
    <span class="badge sale_badge"><i><?php echo $product['sale_badge']; ?></i></span>
<?php } ?>
<?php if ($product['new_label']) { ?>
    <span class="badge new_badge"><i><?php echo $basel_text_new; ?></i></span>
<?php } ?>

<?php if (($product['quantity'] < 1) && ($stock_badge_status)) { ?>
	<span class="badge out_of_stock_badge"><i><?php echo $basel_text_out_of_stock; ?></i></span>
	<?php $button_cart = $basel_text_out_of_stock; ?>
<?php } else { ?>
	<?php $button_cart = $default_button_cart; ?>
<?php } ?>
<a class="img-overlay" href="<?php echo $product['href']; ?>"></a>
<div class="btn-center catalog_hide"><a class="btn btn-light-outline btn-thin" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><?php echo $button_cart; ?></a></div>
<div class="icons-wrapper">
<a class="icon is-cart catalog_hide" data-toggle="tooltip" data-placement="<?php echo $tooltip_align; ?>" data-title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><span class="global-cart"></span></a>
<a class="icon is_wishlist" data-toggle="tooltip" data-placement="<?php echo $tooltip_align; ?>"  data-title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><span class="icon-heart"></span></a>
<a class="icon is_compare" onclick="compare.add('<?php echo $product['product_id']; ?>');" data-toggle="tooltip" data-placement="<?php echo $tooltip_align; ?>" data-title="<?php echo $button_compare; ?>"><span class="icon-refresh"></span></a>
<a class="icon is_quickview hidden-xs" onclick="quickview('<?php echo $product['product_id']; ?>');" data-toggle="tooltip" data-placement="<?php echo $tooltip_align; ?>" data-title="<?php echo $basel_button_quickview; ?>"><span class="icon-magnifier-add"></span></a>
</div> <!-- .icons-wrapper -->
</div><!-- .image ends -->
<div class="caption">
<a class="product-name" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
<?php if ($product['rating']) { ?>      
    <div class="rating">
    <span class="rating_stars rating r<?php echo $product['rating']; ?>">
    <i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
    </span>
    </div>
<?php } ?>
<div class="price-wrapper">
<?php if ($product['price']) { ?>
<div class="price">
    <?php if (!$product['special']) { ?>
    <span><?php echo $product['price']; ?></span>
    <?php } else { ?>
    <span class="price-old"><?php echo $product['price']; ?></span><span class="price-new"><?php echo $product['special']; ?></span>
    <?php } ?>
    <?php if ($product['tax']) { ?>
    <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
    <?php } ?>
</div><!-- .price -->
<?php } ?>
<p class="description"><?php if (isset($product['description'])) echo $product['description']; ?></p>
<a class="btn catalog_hide <?php if ($basel_list_style == '6') { echo 'btn-contrast'; } else { echo 'btn-outline';} ?>" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');"><span class="global-cart"></span><?php echo $button_cart; ?></a>
</div><!-- .price-wrapper -->
<div class="plain-links">
<a class="icon is_wishlist link-hover-color" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><span class="icon-heart"></span> <?php echo $button_wishlist; ?></a>
<a class="icon is_compare link-hover-color" onclick="compare.add('<?php echo $product['product_id']; ?>');"><span class="icon-refresh"></span> <?php echo $button_compare; ?></a>
<a class="icon is_quickview link-hover-color" onclick="quickview('<?php echo $product['product_id']; ?>');"><span class="icon-magnifier-add"></span> <?php echo $basel_button_quickview; ?></a>
</div><!-- .plain-links-->
</div><!-- .caption-->
<?php if ($product['sale_end_date'] && $countdown_status) { ?>
<script>
  $(function() {
	$(".sale-counter.id<?php echo $product['product_id']; ?>").countdown("<?php echo $product['sale_end_date']; ?>").on('update.countdown', function(event) {
  var $this = $(this).html(event.strftime(''
    + '<div>'
    + '%D<i><?php echo $basel_text_days; ?></i></div><div>'
    + '%H <i><?php echo $basel_text_hours; ?></i></div><div>'
    + '%M <i><?php echo $basel_text_mins; ?></i></div><div>'
    + '%S <i><?php echo $basel_text_secs; ?></i></div></div>'));
});
  });
</script>
<?php } ?>
</div><!-- .single-product ends -->