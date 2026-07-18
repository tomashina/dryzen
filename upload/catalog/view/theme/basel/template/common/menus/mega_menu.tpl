<?php $class = ''; $icon_font = ''; $class_link = ''; $title = false; $target = ''; $submenu_bg_img = ''; $caret = '';
if(!empty($row['icon_font'])) $icon_font .= "<i class='icon ".$row['icon_font']."'></i>";
if($row['description'] != '') $class_link .= ' description';
if($row['class_menu']) $class .= $row['class_menu'];
if(is_array($row['submenu']) && !empty($row['submenu'])) {
$caret = "<i class='fa fa-angle-down'></i>";
  if($row['submenu_width'] == 'full') {
	$class .= ' has-sub dropdown-wrapper has-full-width';
    $is_full_width = true;
  } else {
  	$class .= ' has-sub dropdown-wrapper from-bottom';
    $is_full_width = false;
  }
}
if($row['new_window']) $target = ' external';
if(!isset($row['name'][$lang_id])) $row['name'][$lang_id] = '---';
if ($row['icon'] && (strpos($row['icon'], 'no_image') === false)) $submenu_bg_img = 'background-image:url(\'' . $row['icon'] . '\');background-position:'. $row['position'] .';'; 
?>
<!-- Top level items -->
<li class="<?php echo $class; ?>">
<a <?php if ($row['link']) { ?>href="<?php echo $row['link']; ?>"<?php } ?> class="<?php echo $class_link; ?> <?php echo $target; ?>">
<?php echo $icon_font . '<span class="top">' . $row['name'][$lang_id] . '</span>' . $row['description'] ?><?php echo $caret; ?>
</a>
<?php if(is_array($row['submenu']) && !empty($row['submenu'])) { ?>
<!-- Sub Menu items -->
<!-- if full width -->
<?php if($row['submenu_width'] == 'full') { ?>
<div class="full-width-dropdown dropdown-content sub-holder" style="<?php echo $submenu_bg_img; ?>">
<?php } else { ?>
<div class="dropdown-content sub-holder dropdown-left <?php if ($row['submenu_width'] < '231') echo 'narrow'; ?>" style="<?php if ($row['submenu_width']) echo 'width:' . $row['submenu_width'] . 'px;'; ?><?php echo $submenu_bg_img; ?>">
<?php } ?>

<?php if($row['submenu_width'] == 'full') { ?>
<div class="container">
<?php } ?>

<div class="dropdown-inner">
<div class="row">
<?php $row_fluid = 0; ?>
<?php foreach($row['submenu'] as $submenu) {
$target = ''; if($submenu['new_window']) $target = 'external';
$icon_font = ''; if($submenu['icon_font']) $icon_font .= "<i class='".$submenu['icon_font']."'></i>";
if(($row_fluid+$submenu['content_width']) > 12) {
$row_fluid = $submenu['content_width'];
echo '</div><span class="border"></span><div class="row">';
} else {
$row_fluid = $row_fluid+$submenu['content_width'];
}
?>
<div class="col-xs-12 col-sm-<?php echo $submenu['content_width']; ?> <?php echo $submenu['class_menu']; ?>">
<div class="menu-item">
<?php if($submenu['show_title']) { ?>
<?php if($submenu['link']) { ?>
<h4 class="column-title"><?php if ($icon_font) { ?><span class="icon"><?php echo $icon_font; ?></span><?php } ?><b><a href="<?php echo $submenu['link']; ?>" class="<?php echo $target; ?>"><?php echo $submenu['name'][$lang_id] . $submenu['description']; ?></a></b></h4>
<?php } else { ?>
<h4 class="column-title"><?php if ($icon_font) { ?><span class="icon"><?php echo $icon_font; ?></span><?php } ?><b><?php echo $submenu['name'][$lang_id] . $submenu['description']; ?></b></h4>
<?php } ?>
<?php } ?>
<?php if($submenu['content_type'] == '0') { ?>
<!-- HTML CONTENT -->
<div class="html hover-menu">
<?php echo $submenu['html']; ?>
</div>
<?php } ?>
<?php if($submenu['content_type'] == '1') { ?>
<!-- SINGLE PRODUCT CONTENT -->
<?php if(is_array($submenu['product'])) { ?>
<div class="grid">
<div class="single-product menu-product">
<div class="image">
<a href="<?php echo $submenu['product']['link']; ?>">
<img src="<?php echo $submenu['product']['image']; ?>" alt="<?php echo $submenu['product']['name']; ?>" title="<?php echo $submenu['product']['name']; ?>" />
<?php if ($submenu['product']['image2']) { ?>
<img class="thumb2" src="<?php echo $submenu['product']['image2']; ?>" alt="<?php echo $submenu['product']['name']; ?>" title="<?php echo $submenu['product']['name']; ?>" />
<?php } ?>
</a>
<?php if ($submenu['product']['price'] && $submenu['product']['special'] && ($salebadge_status)) { ?>
<div class="sale-counter mm id<?php echo $submenu['product']['id']; ?>"></div>
<span class="badge sale_badge"><i><?php echo $basel_text_sale; ?></i></span>
<?php } ?>
<?php if ($submenu['product']['new_label']) { ?>
<span class="badge new_badge"><i><?php echo $basel_text_new; ?></i></span>
<?php } ?>
<a class="img-overlay" href="<?php echo $submenu['product']['link']; ?>"></a>
<div class="btn-center"><a class="btn btn-light-outline btn-thin btn-tiny" onclick="cart.add('<?php echo $submenu['product']['id']; ?>', '<?php echo $submenu['product']['minimum']; ?>');"><?php echo $button_cart; ?></a></div>
<div class="icons-wrapper">
<a class="icon is-cart" data-toggle="tooltip" data-placement="left" data-title="<?php echo $button_cart; ?>" onclick="cart.add('<?php echo $submenu['product']['id']; ?>', '<?php echo $submenu['product']['minimum']; ?>');"><span class="global-cart"></span></a>
<a class="icon is_wishlist" data-toggle="tooltip" data-placement="left" data-title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $submenu['product']['id']; ?>');"><span class="icon-heart"></span></a>
<a class="icon is_compare" onclick="compare.add('<?php echo $submenu['product']['id']; ?>');" data-toggle="tooltip" data-placement="left" data-title="<?php echo $button_compare; ?>"><span class="icon-refresh"></span></a>
<a class="icon is_quickview hidden-xs" onclick="quickview('<?php echo $submenu['product']['id']; ?>');" data-toggle="tooltip" data-placement="left" data-title="<?php echo $basel_button_quickview; ?>"><span class="icon-magnifier-add"></span></a>
</div>
</div>
<div class="caption">
<a class="product-name" href="<?php echo $submenu['product']['link']; ?>"><?php echo $submenu['product']['name']; ?></a>
<?php if ($submenu['product']['rating']) { ?>      
<div class="rating">
<span class="rating_stars rating r<?php echo $submenu['product']['rating']; ?>">
<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
</span>
</div>
<?php } ?>
<div class="price-wrapper">
<?php if ($submenu['product']['price']) { ?>
<div class="price">
<?php if (!$submenu['product']['special']) { ?>
<?php echo $submenu['product']['price']; ?>
<?php } else { ?>
<span class="price-old">
<?php echo $submenu['product']['price']; ?></span><span class="price-new"><?php echo $submenu['product']['special']; ?>
</span>
<?php } ?>
</div>
<?php } ?>
<a class="btn btn-outline btn-tiny catalog_hide" onclick="cart.add('<?php echo $submenu['product']['id']; ?>', '<?php echo $submenu['product']['minimum']; ?>');"><span class="global-cart"></span><?php echo $button_cart; ?></a>
</div>
</div>
<?php if ($submenu['product']['sale_end_date'] && $countdown_status) { ?>
<script>
$(function() {
$(".sale-counter.mm.id<?php echo $submenu['product']['id']; ?>").countdown({
date: "<?php echo $submenu['product']['sale_end_date']; ?>",
render: function(data) {
$(this.el).html(
"<div>" + 
data.days + "<i><?php echo $basel_text_days; ?></i></div><div>" + 
data.hours + " <i><?php echo $basel_text_hours; ?></i></div><div>" + 
data.min + " <i><?php echo $basel_text_mins; ?></i></div><div>" + 
data.sec + " <i><?php echo $basel_text_secs; ?></i></div>");
} 
}); 
});
</script>
<?php } ?>
</div>
</div>
<?php } ?>
<?php } ?>
<?php if($submenu['content_type'] == '2') { ?>
<!-- CATEGORIES CONTENT -->
<div class="categories">
<?php echo $submenu['categories']; ?>
</div>
<?php } ?>
<?php if($submenu['content_type'] == '4') { ?>
<!-- BANNER CONTENT -->
<div class="link">
<?php echo $submenu['images']['link']; ?>
</div>
<?php } ?>
</div>
</div>
<?php } ?>
</div> <!-- row -->
</div>
<?php if($row['submenu_width'] == 'full') { ?>
</div>
<?php } ?>
</div>
<?php } ?>
</li>