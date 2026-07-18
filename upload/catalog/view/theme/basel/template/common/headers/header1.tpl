<div class="header-wrapper header1 fixed-header-possible">
<?php if ($top_line_style) { ?>
<div class="top_line">
  <div class="container <?php echo $top_line_width; ?>">
  	<div class="table">
        <div class="table-cell left sm-text-center xs-text-center">
            <div class="promo-message"><?php echo $promo_message; ?></div>
        </div>
        <div class="table-cell text-right hidden-xs hidden-sm">
            <div class="links">
            <ul>
            <?php require('catalog/view/theme/basel/template/common/static_links.tpl'); ?>
            </ul>
            <?php if ($lang_curr_title) { ?>
            <div class="setting-ul">
            <div class="setting-li dropdown-wrapper from-left lang-curr-trigger nowrap"><a>
            <span><?php echo $lang_curr_title; ?></span>
            </a>
            <div class="dropdown-content dropdown-right lang-curr-wrapper">
            <?php echo $language; ?>
            <?php echo $currency; ?>
            </div>
            </div>
            </div>
            <?php } ?>
            </div>
        </div>
    </div> <!-- .table ends -->
  </div> <!-- .container ends -->
</div> <!-- .top_line ends -->
<?php } ?>
<span class="table header-main sticky-header-placeholder">&nbsp;</span>
<div class="sticky-header outer-container header-style">
<div class="container <?php echo $main_header_width; ?>">
<div class="table header-main">
<div class="table-cell w40 menu-cell hidden-xs hidden-sm">
<?php if($primary_menu) { ?>
<div class="main-menu menu-stay-left">
	<ul class="categories">
		<?php if($primary_menu == 'oc') { ?>
        <!-- Default menu -->
        <?php echo $default_menu; ?>
      <?php } else if (isset($primary_menu)) { ?>
        <!-- Mega menu -->
        <?php foreach($primary_menu_desktop as $key=> $row) { ?>
        <?php require('catalog/view/theme/basel/template/common/menus/mega_menu.tpl'); ?>
        <?php } ?>
      <?php } ?>
    </ul>
</div>
<?php } ?>
</div>
<div class="table-cell w20 logo text-center">
    <?php if ($logo) { ?>
    <div id="logo">
    <a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a>
    </div>
    <?php } ?>
</div>
<div class="table-cell w40 shortcuts text-right"> 
   <div class="font-zero">
    <?php if ($header_login) { ?>
        <div class="shortcut-wrapper sign-in hidden-sx hidden-sm hidden-xs">
        <?php if ($logged) { ?>
        <a class="anim-underline" href="<?php echo $account; ?>"><?php echo $text_account; ?></a> &nbsp;/&nbsp; 
        <a class="anim-underline" href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a>
        <?php } else { ?>
        <a class="anim-underline" href="<?php echo $login; ?>"><?php echo $text_login; ?> / <?php echo $text_register; ?></a>
        <?php } ?>
        </div>
    <?php } ?>
    <?php if ($header_search) { ?>
    <div class="icon-element">
    <div class="dropdown-wrapper-click from-top hidden-sx hidden-sm hidden-xs">
    <a class="shortcut-wrapper search-trigger from-top clicker">
    <i class="icon-magnifier icon"></i>
    </a>
    <div class="dropdown-content dropdown-right">
    <div class="search-dropdown-holder">
    <div class="search-holder">
    <?php echo $basel_search; ?>
    </div>
    </div>
    </div>
    </div>
    </div>
    <?php } ?>
    <div class="icon-element is_wishlist">
    <a class="shortcut-wrapper wishlist" href="<?php echo $wishlist; ?>">
    <div class="wishlist-hover"><i class="icon-heart icon"></i><span class="counter wishlist-counter"><?php echo $wishlist_counter; ?></span></div>
    </a>
    </div>
    <div class="icon-element catalog_hide">
    <div id="cart" class="dropdown-wrapper from-top">
    <a href="<?php echo $shopping_cart; ?>" class="shortcut-wrapper cart">
    <i id="cart-icon" class="global-cart icon"></i> <span id="cart-total" class="nowrap">
    <span class="counter cart-total-items"><?php echo $cart_items; ?></span> <span class="slash hidden-md hidden-sm hidden-xs">/</span>&nbsp;<b class="cart-total-amount hidden-sm hidden-xs"><?php echo $cart_amount; ?></b>
    </span>
    </a>
    <div class="dropdown-content dropdown-right hidden-sm hidden-xs">
    <?php echo $cart; ?>
    </div>
    </div>
    </div>
    <div class="icon-element">
    <a class="shortcut-wrapper menu-trigger hidden-md hidden-lg">
    <i class="icon-line-menu icon"></i>
    </a>
    </div>
   </div>
</div>
</div> <!-- .table.header_main ends -->
</div> <!-- .container ends -->
</div> <!-- .sticky ends -->
</div> <!-- .header_wrapper ends -->