<div class="header-wrapper header6">

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

<span class="table header-main sticky-header-placeholder slidedown">&nbsp;</span>
<div class="sticky-header slidedown outer-container header-style">
  <div class="container <?php echo $main_header_width; ?>">
    <div class="table header-main <?php echo $main_menu_align; ?>">
    
    <div class="table-cell text-left w20 logo">
    	<?php if ($logo) { ?>
        <div id="logo">
    	<a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a>
        </div>
    	<?php } ?>
    </div>
    
    <?php if($primary_menu) { ?>
    <div class="table-cell text-center w60 menu-cell hidden-xs hidden-sm">
        <div class="main-menu">
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
    </div>
    <?php } ?>
    
    <div class="table-cell w20 shortcuts text-right"> 
       
       <div class="font-zero">
        
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

<div class="outer-container header-bottom hidden-xs hidden-sm">
<div class="container <?php echo $main_header_width; ?>">
	<div class="table">
    	<?php if($secondary_menu) { ?>
    	<div class="table-cell w275">
        <div class="main-menu vertical vertical-menu-bg dropdown-wrapper from-bottom">
        <h4 class="menu-heading"><b><?php echo $text_category; ?><i class="fa fa-angle-down"></i></b></h4>
            <ul class="categories vertical-menu-bg dropdown-content">
              <?php if($secondary_menu == 'oc') { ?>
                <!-- Default menu -->
                <?php echo $default_menu; ?>
              <?php } else if (isset($secondary_menu)) { ?> 
                <!-- Mega menu -->
                <?php foreach($secondary_menu_desktop as $key=> $row) { ?>
                <?php require('catalog/view/theme/basel/template/common/menus/mega_menu.tpl'); ?>
                <?php } ?>
              <?php } ?>
            </ul>
        </div>
        </div>
        <?php } ?>
        
        <div class="table-cell w100 search-cell <?php echo $basel_search_scheme; ?>">
         <?php echo $basel_search; ?>
        </div>
    </div>
</div><!-- .container ends -->
</div>

</div> <!-- .header_wrapper ends -->