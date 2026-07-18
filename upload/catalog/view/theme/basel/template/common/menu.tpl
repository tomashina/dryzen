<?php if ($categories) { ?>
<?php foreach ($categories as $category) { ?>
    <?php if ($category['children']) { ?>
        <li class="has-sub dropdown-wrapper from-bottom">
        <a href="<?php echo $category['href']; ?>"><span class="top"><?php echo $category['name']; ?></span><i class="fa fa-angle-down"></i></a>
        <div class="sub-holder dropdown-content dropdown-left">
            <div class="dropdown-inner"><div class="menu-item">
                <?php foreach (array_chunk($category['children'], ceil(count($category['children']) / $category['column'])) as $children) { ?>
                  <ul class="default-menu-ul hover-menu">
                    <?php foreach ($children as $child) { ?>
                    	<?php if ($child['grandchildren']) { ?>
					<li class="default-menu-li has-sub dropdown-wrapper from-bottom"><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?><i class="fa fa-angle-right"></i></a>
							<ul class="dropdown-content sub-holder dropdown-left">
                                <?php foreach ($child['grandchildren'] as $grandchild) { ?>
									<li class="default-menu-li"><a href="<?php echo $grandchild['href']; ?>"><?php echo $grandchild['name']; ?></a></li>
								<?php } ?>
							</ul>
					</li>
						<?php } else { ?>
					<li class="default-menu-li"><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a></li>
						<?php } ?>
                    <?php } ?>
                  </ul>
                <?php } ?>
            </div>
        </div>
        </div>
        </li>
    <?php } else { ?>
        <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
    <?php } ?>
<?php } ?>
<?php } ?>