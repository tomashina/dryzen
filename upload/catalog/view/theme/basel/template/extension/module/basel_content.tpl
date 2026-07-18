<?php if ($block_full_width) { ?>
</div>
<?php } ?>
<div class="widget cm_module" style="<?php echo $module_margins; ?>">
<?php if ($block_title) { ?>
<!-- Block Title -->
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
<?php } ?>
<div class="cm_block_wrapper">
<?php if ($bg_video) { ?><div class="vid-holder"></div><?php } ?>
<div class="cm_block <?php echo $module; ?>" style="<?php echo $block_style; ?>;">
<div class="<?php if (!$content_full_width && $block_full_width) { echo "container"; } ?>">
<?php if (isset($columns)) { ?>
<div class="cm_content" style="<?php echo $content_style; ?>;">
<div class="row <?php if ($content_no_margin) { echo "marginless"; } ?>">
<?php foreach($columns as $column){ ?>
<div class="<?php echo $column['column_class']; ?> type-<?php echo $column['type']; ?> cm_column <?php if ($equal_height) { echo "eq_height"; } ?>">    
<?php if ($column['type'] == "img") { ?>
<!-- Banner type -->
<?php if ($column['data2']) { ?>
<div class="banner_wrap">
<div class="zoom_image_wrap"><img class="zoom_image" src="<?php echo $column['data2']; ?>" alt="" /></div>
<div class="effect-holder"></div>
<div class="banner_overlay">
    <div class="cm_item_wrapper <?php if ($column['data5']) { ?>pointer" onclick="location.href='<?php echo $column['data5']; ?>';<?php } ?>">
        <div class="cm_item <?php echo $column['data7']; ?>">
            <?php echo $column['data1']; ?>
        </div>
    </div>
</div>
</div>
<?php } ?>
<?php if ($column['data4']) { ?>
<div class="banner_wrap">
<div class="zoom_image_wrap"><img class="zoom_image" src="<?php echo $column['data4']; ?>" alt="" /></div>
<div class="effect-holder"></div>
<div class="banner_overlay">
    <div class="cm_item_wrapper <?php if ($column['data6']) { ?>pointer" onclick="location.href='<?php echo $column['data6']; ?>';<?php } ?>">
        <div class="cm_item <?php echo $column['data8']; ?>">
            <?php echo $column['data3']; ?>
        </div>
    </div>
</div>
</div>
<?php } ?>
<?php } else if ($column['type'] == "tm") { ?>
<!-- Testimonial type -->
<?php if (isset($testimonials)) { ?>
<div class="grid-holder <?php echo $column['data8']; ?> tm_module grid<?php echo $column['data7']; ?>">
<?php foreach ($testimonials as $testimonial) { ?>
<div class="item single-testimonial">
    <div class="testimonial-wrap">
    <?php if ($testimonial['image']) { ?>
    <p><img src="<?php echo $testimonial['image']; ?>" alt=""/></p>
    <?php } ?>
    <p><?php echo $testimonial['description'] ; ?></p>
    <p><b><?php echo $testimonial['name']; ?></b><?php if ($testimonial['org']) echo ' - ' . $testimonial['org']; ?></p> 
    </div>    
</div>
<?php } ?>
</div>
<?php } else { echo 'No testimonials found'; } ?>
<?php } else { ?>
<!-- HTML/Newsletter type -->
<div class="cm_item_wrapper">
    <div class="cm_item <?php echo $column['data7']; ?>">
        <?php echo $column['data1']; ?>
    </div>
</div>
<?php } ?>
</div>    
<?php } ?>
</div>
</div>
<?php } ?>
</div>
</div>
</div>
</div>
<?php if ($block_full_width) { ?>
<div class="container">
<?php } ?>
<?php if ($para_status && $bg_img) { ?>
<script>
$(window).load(function(){
$('.cm_block.<?php echo $module; ?>').parallax("50%", 0.<?php echo $para_status; ?>);
})
</script>
<?php } ?>
<?php if ($bg_video) { ?>
<script>
$('.vid-holder').tuber({
videoId: '<?php echo $video_id; ?>',
isBackground: false
});
</script>
<?php } ?>
<?php if (isset($columns)) { ?>
<?php foreach($columns as $column){ ?>
<?php if ($column['type'] == "tm") { ?>
<script><!--
$('.grid-holder.tm_module').slick({
adaptiveHeight:true,
<?php if ($direction == 'rtl') { ?>
rtl: true,
<?php } ?>
dots:true,
arrows: false,
respondTo:'min',
<?php if ($column['data7'] == '3') { ?>
slidesToShow:3,slidesToScroll:3,responsive:[{breakpoint:960,settings:{slidesToShow:1,slidesToScroll:1}}],
<?php } elseif ($column['data7'] == '2') { ?>
slidesToShow:2,slidesToScroll:2,responsive:[{breakpoint:960,settings:{slidesToShow:1,slidesToScroll:1}}]
<?php } elseif (($column['data7'] == '1')) { ?>
slidesToShow:1,slidesToScroll:1,responsive:[]
<?php } ?>
}).parents('.cm_block_wrapper').addClass('has-testimonials');
//--></script>
<?php } ?>
<?php } ?>
<?php } ?>