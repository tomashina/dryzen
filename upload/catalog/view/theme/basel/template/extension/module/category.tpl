<div class="widget">
<div class="widget-title">
    <p class="main-title"><span><?php echo $heading_title; ?></span></p>
    <p class="widget-title-separator"><i class="icon-line-cross"></i></p>
</div>
<ul class="plain-menu cat">
<?php foreach ($categories as $category) { ?>
   <?php if ($category['category_id'] == $category_id) { ?>
     <li class="open active"><a href="<?php echo $category['href']; ?>" ><?php echo $category['name']; ?></a>
      <?php } else { ?>
      <li><a href="<?php echo $category['href']; ?>" ><?php echo $category['name']; ?></a> 
      <?php } ?>
      <?php if ($category['children']) { ?>
      <div class="sign"><i class="icon-arrow-right"></i></div>
      <ul>
      
      <?php foreach ($category['children'] as $child) { ?>

      <?php if ($child['grandchildren']) { ?>
                <?php if ($child['category_id'] == $child_id) { ?>
                <li class="open active"><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a>
                <?php } else { ?>
                <li><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a>
                <?php } ?>
                
        <div class="sign"><i class="icon-arrow-right"></i></div>
                <ul>
                    <?php foreach ($child['grandchildren'] as $grandchild) { ?>
                        <li><a href="<?php echo $grandchild['href']; ?>"><?php echo $grandchild['name']; ?></a></li>
                    <?php } ?>
                </ul>
        </li>
            <?php } else { ?>
        <li class=""><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a></li>
            <?php } ?>
      
      <?php } ?>
      </ul>
      <?php } ?>
    </li>
    <?php } ?>
</ul>
</div>
<script>        
	$('.plain-menu.cat > li').bind().click(function(e) {
	$(this).toggleClass("open").find('>ul').stop(true, true).slideToggle(350)
	.end().siblings().find('>ul').slideUp().parent().removeClass("open");
	e.stopPropagation();
	});
	$('.plain-menu.cat li a').click(function(e) {
	e.stopPropagation();
	});
	
	$('.plain-menu.cat > li > ul > li').bind().click(function(e) {
	$(this).toggleClass("open").find('>ul').stop(true, true).slideToggle(350)
	.end().siblings().find('>ul').slideUp().parent().removeClass("open");
	e.stopPropagation();
	});
	
	$('.plain-menu.cat li ul li a').click(function(e) {
	e.stopPropagation();
	});
</script>