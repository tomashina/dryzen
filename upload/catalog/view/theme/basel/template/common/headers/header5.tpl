<div class="header-wrapper header5">
<span class="table header-main sticky-header-placeholder hidden-md hidden-lg">&nbsp;</span>

<div class="sticky-header slidedown mobile-only outer-container header-style">
  <div class="container <?php echo $main_header_width; ?>">
    <div class="table header-main">
    
    <div class="table-cell w50 vertical-bottom logo">
     <?php if ($logo) { ?>
        <div id="logo">
    		<a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" /></a>
        </div>
     <?php } ?>
     <div class="promo-message hidden-xs hidden-sm"><?php echo $promo_message; ?></div>
    </div>
    
    
    <div class="table-cell w50 vertical-bottom text-right hidden-xs hidden-sm"> 
       
        <div class="links first-row">
		
        <?php if ($lang_curr_title) { ?>
        <div class="setting-ul">
        <div class="setting-li dropdown-wrapper from-bottom lang-curr-trigger nowrap"><a>
        <span><?php echo $lang_curr_title; ?></span>
        </a>
        <div class="dropdown-content dropdown-right lang-curr-wrapper">
        <?php echo $language; ?>
        <?php echo $currency; ?>
        </div>
        </div>
        </div>
        <?php } ?>
        
        <ul>
        
        <li class="wishlist-hover is_wishlist">
        <a href="<?php echo $wishlist; ?>" id="wishlist-total"><i class="icon-heart icon"></i><span><?php echo $text_wishlist; ?></span></a>
        </li>
        
        
        <li id="cart" class="dropdown-wrapper from-bottom catalog_hide">
        <a href="<?php echo $shopping_cart; ?>"><i class="global-cart icon"></i><span class="cart-total-items"><?php echo $cart_items; ?></span> / <b class="cart-total-amount"><?php echo $cart_amount; ?></b></a>
        <div class="dropdown-content dropdown-right hidden-sm hidden-xs">
        <?php echo $cart; ?>
        </div>
        </li>
        
        </ul>
        </div>
        
        <div class="links">
		<ul>
        <?php require('catalog/view/theme/basel/template/common/static_links.tpl'); ?>
        </ul>
        </div>
            
    </div>
    
    <div class="table-cell shortcuts text-right hidden-md hidden-lg">
    <div class="font-zero">
    <div class="icon-element is_wishlist">
    <a class="shortcut-wrapper wishlist" href="<?php echo $wishlist; ?>">
    <div class="wishlist-hover"><i class="icon-heart icon"></i><span class="counter wishlist-counter"><?php echo $wishlist_counter; ?></span></div>
    </a>
    </div>
    
    <div class="icon-element catalog_hide">
    <a href="<?php echo $shopping_cart; ?>" class="shortcut-wrapper cart">
    <i id="cart-icon" class="global-cart icon"></i> <span id="cart-total" class="nowrap">
    <span class="counter cart-total-items"><?php echo $cart_items; ?></span>&nbsp;
    </span>
    </a>
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
</div> <!-- .mobile-sticky-header ends -->



<div class="sticky-header slidedown outer-container menu-style menu-style-contrast hidden-xs hidden-sm">


<div class="container <?php echo $main_header_width; ?>">

        <div class="main-menu menu-stay-left">
        
        <?php if($primary_menu) { ?>
        <ul class="categories block">
          <?php if($primary_menu == 'oc') { ?>
            <!-- Default menu -->
            <?php echo $default_menu; ?>
          <?php } else if (isset($primary_menu)) { ?> 
            <!-- Mega menu -->
            <?php foreach($primary_menu_desktop as $key=> $row) { ?>
            <?php require('catalog/view/theme/basel/template/common/menus/mega_menu.tpl'); ?>
            <?php } ?>
          <?php } ?>
        
        <li class="dropdown-wrapper-click from-left pull-right has-icon hidden-sx hidden-sm hidden-xs">
        <a class="search-trigger clicker">
        <i class="icon-magnifier icon"></i>
        </a>
        <div class="dropdown-content dropdown-right">
        <div class="search-dropdown-holder">
        <div class="search-holder">
        <?php echo $basel_search; ?>
        </div>
        </div>
        </div>
        </li>
        
        </ul>
        <?php } ?>

        </div>
        
</div>
</div>

</div> <!-- .header_wrapper ends -->