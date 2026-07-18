<div class="main-menu-wrapper hidden-md hidden-lg">
<ul class="mobile-top">
    <li class="mobile-lang-curr"></li>
    <?php if($header_search) { ?>
    <li class="search">
        <div class="search-holder-mobile">
        <input type="text" name="search-mobile" value="" placeholder="" class="form-control" /><a class="fa fa-search"></a>
        </div>
    </li>
    <?php } ?>
</ul>
<?php if($primary_menu) { ?>
<ul class="categories">
<?php if($primary_menu == 'oc') { ?>
<!-- Default menu -->
<?php echo $default_menu; ?>
<?php } else if (isset($primary_menu)) { ?>
<!-- Mega menu -->
<?php foreach($primary_menu_mobile as $key=> $row) { ?>
<?php require('catalog/view/theme/basel/template/common/menus/mega_menu.tpl'); ?>
<?php } ?>
<?php } ?>
</ul>
<?php } ?>
<?php if($secondary_menu) { ?>
<ul class="categories">
    <?php if($secondary_menu == 'oc') { ?>
        <!-- Default menu -->
        <?php echo $default_menu; ?>
    <?php } else if (isset($secondary_menu)) { ?> 
        <!-- Mega menu -->
        <?php foreach($secondary_menu_mobile as $key=> $row) { ?>
        	<?php require('catalog/view/theme/basel/template/common/menus/mega_menu.tpl'); ?>
        <?php } ?>
    <?php } ?>
</ul>
<?php } ?>
<ul class="categories">
    <?php require('catalog/view/theme/basel/template/common/static_links.tpl'); ?>
</ul>
</div>
<span class="body-cover menu-closer"></span>