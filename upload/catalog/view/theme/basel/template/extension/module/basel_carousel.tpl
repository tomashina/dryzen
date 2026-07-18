<div class="widget carousel-widget grid<?php if ($contrast) echo ' contrast-bg'; ?>" <?php if ($use_margin) echo 'style="margin-bottom:' . $margin . '"'; ?>>
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
<div class="grid-holder carousel carousel_module module<?php echo $module; ?>">
  <?php foreach ($banners as $banner) { ?>
    <div class="item text-center">
    <?php if ($banner['link']) { ?>
        <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="display-inline-block" /></a>
    <?php } else { ?>
        <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="display-inline-block" />
    <?php } ?>
    </div>
  <?php } ?>
</div> <!-- .grid-holder ends -->
<div class="clearfix"></div>
</div>
<script><!--
$('.grid-holder.carousel_module.module<?php echo $module; ?>').slick({
<?php if ($carousel_a) { ?>
prevArrow: "<a class=\"arrow-left icon-arrow-left\"></a>",
nextArrow: "<a class=\"arrow-right icon-arrow-right\"></a>",
<?php } else { ?>
arrows: false,
<?php } ?>
<?php if ($carousel_b) { ?>
dots:true,
<?php } ?>
<?php if ($direction == 'rtl') { ?>
rtl: true,
<?php } ?>
<?php if ($autoplay) { ?>
autoplay:true,
autoplaySpeed:<?php echo $autoplay; ?>,
<?php } ?>
respondTo:'min',
slidesToScroll:1,
rows:<?php echo $rows; ?>,
<?php if ($columns == '6') { ?>
slidesToShow:6,responsive:[{breakpoint:1100,settings:{slidesToShow:5,slidesToScroll:5}},{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '5') { ?>
slidesToShow:5,responsive:[{breakpoint:1100,settings:{slidesToShow:4,slidesToScroll:4}},{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '4') { ?>
slidesToShow:4,responsive:[{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '3') { ?>
slidesToShow:3,responsive:[{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '2') { ?>
slidesToShow:2,responsive:[
<?php } elseif (($columns == '1' || $columns == 'list')) { ?>
adaptiveHeight:true,slidesToShow:1,slidesToScroll:1,responsive:[
<?php } ?>
{breakpoint:420,settings:{slidesToShow:2}}
]
});
//--></script>