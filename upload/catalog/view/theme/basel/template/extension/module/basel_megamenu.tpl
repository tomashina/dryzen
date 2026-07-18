<div class="widget">
    <div class="main-menu vertical vertical-menu-bg">
    <h4 class="menu-heading"><b><?php echo $heading_title; ?></b></h4>
        <ul class="categories vertical-menu-bg">
            <?php foreach($menu as $key=> $row) { ?>
            <?php require('catalog/view/theme/basel/template/common/menus/mega_menu.tpl'); ?>
            <?php } ?>
        </ul>
    </div>
</div>