<div class="widget module<?php echo $module; if ($columns != 'list') echo ' grid'; if ($contrast) echo ' contrast-bg'; if ($carousel && $rows > 1) echo ' multiple-rows'; ?>" <?php if ($use_margin) echo 'style="margin-bottom:' . $margin . '"'; ?>>
<?php if ($block_title) { ?>
<!-- Block Title -->
<div class="widget-title">
<?php if ($title_preline) { ?><p class="pre-line"><?php echo $title_preline; ?></p><?php } ?>
<?php if ($title) { ?> 
<p class="main-title"><span><?php echo $title; ?></span></p>
<p class="widget-title-separator"><i class="icon-line-cross"></i></p>
<?php } ?>
<?php if ($title_subline) { ?>
<p class="sub-line"><span><?php echo $title_subline; ?></span></p>
<?php } ?>
</div>
<?php } ?>
<?php if (count($tabs) >= 2) { ?>
<!-- Tabs -->
<ul id="tabs-<?php echo $module; ?>" class="nav nav-tabs <?php echo $tabstyle; ?>" data-tabs="tabs" style="">
    <?php foreach ($tabs as $keyTab => $tab) { ?>
        <?php if($keyTab == 0) { ?>
        <li class="active"><a href="#tab<?php echo $module; ?><?php echo $keyTab; ?>" data-toggle="tab"><?php echo $tab['title']; ?></a></li>
        <?php } else { ?>
        <li><a href="#tab<?php echo $module; ?><?php echo $keyTab; ?>" data-toggle="tab"><?php echo $tab['title']; ?></a></li>
        <?php } ?>
    <?php } ?>
</ul>
<?php } ?>
<div class="tab-content has-carousel <?php if (!$carousel) echo "overflow-hidden"; ?>">
<!-- Product Group(s) -->
<?php foreach ($tabs as $key => $tab) { ?>
<div class="tab-pane <?php echo (empty($key) ? 'active in' : ''); ?> fade" id="tab<?php echo $module; ?><?php echo $key; ?>">
    <div class="grid-holder grid<?php echo $columns; ?> prod_module<?php echo $module; ?> <?php if ($carousel) echo "carousel"; ?> <?php if ($carousel_a && $rows > 1) echo "sticky-arrows"; ?>">
        <?php foreach ($tab['products'] as $product) { ?>
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
				$(".module<?php echo $module; ?> .sale-counter.id<?php echo $product['product_id']; ?>").countdown("<?php echo $product['sale_end_date']; ?>").on('update.countdown', function(event) {
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
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php if ($use_button) { ?>
<!-- Button -->
<div class="widget_bottom_btn <?php if ($carousel && $carousel_b) echo 'has-dots'; ?>">
<a class="btn btn-contrast" href="<?php echo isset($link_href) ? $link_href : ''; ?>"><?php echo $link_title; ?></a>
</div>
<?php } ?>
</div>
<div class="clearfix"></div>
</div>
<?php if ($carousel) { ?>
<script><!--
$('.grid-holder.prod_module<?php echo $module; ?>').slick({
<?php if ($carousel_a) { ?>
prevArrow: "<a class=\"arrow-left icon-arrow-left\"></a>",
nextArrow: "<a class=\"arrow-right icon-arrow-right\"></a>",
<?php } else { ?>
arrows: false,
<?php } ?>
<?php if ($direction == 'rtl') { ?>
rtl:true,
<?php } ?>
<?php if ($carousel_b) { ?>
dots:true,
<?php } ?>
respondTo:'min',
rows:<?php echo $rows; ?>,
<?php if ($columns == '5') { ?>
slidesToShow:5,slidesToScroll:5,responsive:[{breakpoint:1100,settings:{slidesToShow:4,slidesToScroll:4}},{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '4') { ?>
slidesToShow:4,slidesToScroll:4,responsive:[{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '3') { ?>
slidesToShow:3,slidesToScroll:3,responsive:[{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '2') { ?>
slidesToShow:2,slidesToScroll:2,responsive:[
<?php } elseif (($columns == '1' || $columns == 'list')) { ?>
adaptiveHeight:true,slidesToShow:1,slidesToScroll:1,responsive:[
<?php } ?>
<?php if ($items_mobile_fw) { ?>
{breakpoint:420,settings:{slidesToShow:1,slidesToScroll:1}}
<?php } ?>
]
});
$('.product-style2 .single-product .icon').attr('data-placement', 'top');
$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
<?php if ($carousel_a && $rows > 1) { ?>
$(window).load(function() {
var p_c_o = $('.prod_module<?php echo $module; ?>').offset().top;
var p_c_o_b = $('.prod_module<?php echo $module; ?>').offset().top + $('.prod_module<?php echo $module; ?>').outerHeight(true) - 100;
var p_sticky_arrows = function(){
var p_m_o = $(window).scrollTop() + ($(window).height()/2);
if (p_m_o > p_c_o && p_m_o < p_c_o_b) {
$('.prod_module<?php echo $module; ?> .slick-arrow').addClass('visible').css('top', p_m_o - p_c_o + 'px');
} else {
$('.prod_module<?php echo $module; ?> .slick-arrow').removeClass('visible');
}
};
$(window).scroll(function() {p_sticky_arrows();});
});
<?php } ?>
//--></script>
<?php } ?>