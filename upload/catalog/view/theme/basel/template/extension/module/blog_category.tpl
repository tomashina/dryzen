<div class="widget">
<div class="widget-title">
    <p class="main-title"><span><?php echo $heading_title_category; ?></span></p>
    <p class="widget-title-separator"><i class="icon-line-cross"></i></p>
</div>
<?php if (!empty($categories)) { ?>
<ul class="plain-menu blog-menu">
<?php foreach ($categories as $category_1) { ?>
   <?php if ($category_1['category_1_id'] == $category_1_id) { ?>
     <li class="open active"><a href="<?php echo $category_1['href']; ?>" ><?php echo $category_1['name']; ?></a>
      <?php } else { ?>
      <li><a href="<?php echo $category_1['href']; ?>" ><?php echo $category_1['name']; ?></a> 
      <?php } ?>
      <?php if ($category_1['children']) { ?>
      <div class="sign"><i class="icon-arrow-right"></i></div>
      <ul>
      <?php foreach ($category_1['children'] as $category_2) { ?>
      <li class="open active"><a href="<?php echo $category_2['href']; ?>"><?php echo $category_2['name']; ?></a></li>
      <?php } ?>
      </ul>
      <?php } ?>
    </li>
    <?php } ?>
</ul>
<?php } ?>
</div>
<script>        
	$('.plain-menu.blog-menu li').bind().click(function(e) {
	$(this).toggleClass("open").find('>ul').stop(true, true).slideToggle(350)
	.end().siblings().find('>ul').slideUp().parent().removeClass("open");
	e.stopPropagation();
	});
	$('.plain-menu.blog-menu li a').click(function(e) {
	e.stopPropagation();
	});
</script>