<div class="container">
<?php echo $position_bottom_half; ?>
</div>
<div class="container">
<?php echo $position_bottom; ?>
</div>
<div id="footer">
<div class="container">
<?php if ($footer_block_1 && $footer_block_1 != '<p><br></p>') { ?>
<div class="footer-top-block">
<?php echo $footer_block_1; ?>
</div>
<?php } ?>
<div class="row links-holder">
<div class="col-xs-12 col-sm-8">
  <div class="row">
  <?php if ($custom_links) { ?>
    <?php foreach($basel_footer_columns as $column) { ?>
    <div class="footer-column col-xs-12 col-sm-6 <?php echo $basel_columns_count; ?> eq_height">
      <?php if ($column['title']){ ?>
        <h5><?php echo $column['title']; ?></h5>
      <?php } ?>
      <?php if(isset($column['links'])){ ?>
      <?php usort($column['links'], function ($a, $b) { return strcmp($a['sort'], $b['sort']); }); ?>
      <ul class="list-unstyled">
      <?php foreach($column['links'] as $key => $link){ ?>
      <li><a href="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a></li>
      <?php } ?>
      </ul>
      <?php } ?>
    </div>
    <?php } ?>
  <?php } else { ?>
      <?php if ($informations) { ?>
      <div class="footer-column col-xs-12 col-sm-4 eq_height">
        <h5><?php echo $text_information; ?></h5>
        <ul class="list-unstyled">
          <?php foreach ($informations as $information) { ?>
          <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
          <?php } ?>
          <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
        </ul>
      </div>
      <?php } ?>
      <div class="footer-column col-xs-12 col-sm-4 eq_height">
        <h5><?php echo $text_extra; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
          <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
          <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
          <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
          <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
        </ul>
      </div>
      <div class="footer-column col-xs-12 col-sm-4 eq_height">
        <h5><?php echo $text_account; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
          <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
          <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
          <li class="is_wishlist"><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
          <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
        </ul>
      </div>
 <?php } ?>
</div><!-- .row ends -->
</div><!-- .col-md-8 ends -->
<div class="footer-column col-xs-12 col-sm-4">
<div class="footer-custom-wrapper">
<?php if (!empty($footer_block_title)) { ?>
<h5><?php echo $footer_block_title; ?></h5>
<?php } ?>
<?php if ($footer_block_2 && $footer_block_2 != '<p><br></p>') { ?>
<div class="custom_block"><?php echo $footer_block_2; ?></div>
<?php } ?>
<?php if (!empty($footer_infoline_1)) { ?>
<p class="infoline"><?php echo $footer_infoline_1; ?></p>
<?php } ?>
<?php if (!empty($footer_infoline_2)) { ?>
<p class="infoline"><?php echo $footer_infoline_2; ?></p>
<?php } ?>
<?php if (!empty($footer_infoline_3)) { ?>
<p class="infoline"><?php echo $footer_infoline_3; ?></p>
<?php } ?>
<?php if ($payment_img) { ?>
<img class="payment_img" src="<?php echo $payment_img; ?>" alt="" />
<?php } ?>
</div>
</div>
</div><!-- .row ends -->
<?php if (isset($basel_copyright)) { ?>
<div class="footer-copyright"><?php echo $basel_copyright; ?></div>
<?php } ?>
</div>
</div>
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<link href="catalog/view/theme/basel/js/lightgallery/css/lightgallery.css" rel="stylesheet" />
<script src="catalog/view/theme/basel/js/jquery.matchHeight.min.js"></script>
<script src="catalog/view/theme/basel/js/countdown.js"></script>
<script src="catalog/view/theme/basel/js/live_search.js"></script>
<script src="catalog/view/theme/basel/js/featherlight.js"></script>
<?php if ($view_popup) { ?>
<!-- Popup -->
<script>
$(document).ready(function() {
if ($(window).width() > <?php echo $popup_width_limit; ?>) {
setTimeout(function() {
$.featherlight({ajax: 'index.php?route=extension/basel/basel_features/basel_popup', variant:'popup-wrapper'});
}, <?php echo $popup_delay; ?>);
}
});
</script>
<?php } ?>
<?php if ($sticky_columns) { ?>
<!-- Sticky columns -->
<script>
if ($(window).width() > 767) {
$('#column-left, #column-right').theiaStickySidebar({containerSelector:$(this).closest('.row'),additionalMarginTop:<?php echo $sticky_columns_offset; ?>});
}
</script>
<?php } ?>
<?php if ($view_cookie_bar) { ?>
<!-- Cookie bar -->
<div class="basel_cookie_bar">
<div class="table">
<div class="table-cell w100"><?php echo $basel_cookie_info; ?></div>
<div class="table-cell button-cell">
<a class="btn btn-tiny btn-light-outline" onclick="$(this).parent().parent().parent().fadeOut(400);"><?php echo $basel_cookie_btn_close; ?></a>
<?php if (!empty($href_more_info)) { ?>
<a class="more-info anim-underline light" href="<?php echo $href_more_info; ?>"><?php echo $basel_cookie_btn_more_info; ?></a>
<?php } ?>
</div>
</div>
</div>
<?php } ?>
<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
BASEL VERSION <?php echo $basel_version; ?> - OPENCART VERSION 2.3 (<?php echo VERSION; ?>)
//-->
</div><!-- .outer-container ends -->
<a class="scroll-to-top primary-bg-color hidden-sm hidden-xs" onclick="$('html, body').animate({scrollTop:0});"><i class="icon-arrow-right"></i></a>
<div id="featherlight-holder"></div>
</body></html>