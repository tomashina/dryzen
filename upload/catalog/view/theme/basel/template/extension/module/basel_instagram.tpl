<?php if ($full_width) { ?></div><?php } ?>
<div class="widget"<?php if ($use_margin) echo ' style="margin-bottom:' . $margin . '"'; ?>>
<div class="instagram_wrapper">
<?php if ($block_title) { ?>
<div class="instagram_title<?php if ($title_inline) echo ' floating_title text-center'; ?>">
<div class="table w100 h100"><div class="table-cell">
<div class="widget-title">
    <?php if ($title_preline) { ?><p class="pre-line"><?php echo $title_preline; ?></p><?php } ?>
    <?php if ($title) { ?> 
    <p class="main-title"><span><?php echo $title; ?></span></p>
    <p class="widget-title-separator"><i class="icon-line-cross"></i></p>
    <?php } ?>
    <?php if ($title_subline) { ?>
    <p class="sub-line"><span><?php echo $title_subline; ?></span></p>
    <?php } ?>
</div>
</div></div>
</div>
<?php } ?>
<div class="images_wrap">
<div id="instafeed<?php echo $module; ?>" class="grid-holder lg-grid<?php echo $columns; ?> md-grid<?php echo $columns_md; ?> sm-grid<?php echo $columns_sm; ?>" style="margin:-<?php echo $padding/2; ?>px;"></div>
</div>
</div> <!-- .instagram_wrapper -->      
</div>
<?php if ($full_width) { ?><div class="container"><?php } ?>

<script>
	(function($){
		$(window).on('load', function(){
			$.instagramFeed({
				'username': "<?php echo $username; ?>",
				'container': "#instafeed<?php echo $module; ?>",
				'callback': null,
				'styling': true,
				'items': '<?php echo $limit; ?>',
				'margin': <?php echo $padding; ?>,
				<?php if ($resolution) { ?>
				'image_size': 640,
				<?php } else { ?>
				'image_size': 320,
				<?php } ?>
				'on_error': console.error
			}); 
		});
	})(jQuery);
</script>