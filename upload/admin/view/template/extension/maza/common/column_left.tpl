<nav id="mz-column-left">
  <div id="mazatheme-logo">
    <a href="http://flexi-filter.templatemaza.com/docs" target="_blank"><img src="view/image/maza/flexi-filter.png" alt="Flexi filter" title="Flexi filter" class="img-responsive"/></a>
    <span>3.6</span>
  </div>
   <ul id="mz-menu" class="mz-menu-center">
    <?php if(isset($menus)){ ?>
      <?php foreach ($menus as $menu) { ?>
        <?php if($menu['active']){ ?>
          <li id="<?php echo $menu['id']; ?>" class="active">
            <a href="<?php echo $menu['href']; ?>"><i class="fa <?php echo $menu['icon']; ?> fw"></i> <span><?php echo $menu['name']; ?></span></a>
          </li>
        <?php } else { ?>
          <li id="<?php echo $menu['id']; ?>">
            <a href="<?php echo $menu['href']; ?>"><i class="fa <?php echo $menu['icon']; ?> fw"></i> <span><?php echo $menu['name']; ?></span></a>
          </li>
        <?php } ?>
      <?php } ?>
      <li class="text-center"><?php echo $text_module ?></li>
    <?php } ?>
    <?php foreach ($modules as $module) { ?>
      <?php if($module['active']){ ?>
        <li id="<?php echo $module['id']; ?>" class="active">
          <a href="<?php echo $module['href']; ?>"><i class="fa <?php echo $module['icon']; ?> fw"></i> <span><?php echo $module['name']; ?></span></a>
        </li>
      <?php } else { ?>
        <li id="<?php echo $module['id']; ?>">
          <a href="<?php echo $module['href']; ?>"><i class="fa <?php echo $module['icon']; ?> fw"></i> <span><?php echo $module['name']; ?></span></a>
        </li>
      <?php } ?>
    <?php } ?>
  </ul>
</nav>
