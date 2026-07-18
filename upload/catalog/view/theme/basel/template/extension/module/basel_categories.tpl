<div class="widget category-widget grid<?php if ($contrast) echo ' contrast-bg'; ?>" <?php if ($use_margin) echo 'style="margin-bottom:' . $margin . '"'; ?>>
    <?php if ($block_title) { ?>
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

    <?php if(isset($categories)){ ?>
        <div class="grid-holder category grid<?php echo $columns; ?> <?php if ($carousel) echo "carousel"; ?> module<?php echo $module; ?>">
        
        <?php if ($view_subs) { ?>
        
              <?php foreach ($categories as $category) { ?>
                <div class="item single-category has-subs">
                <div class="table">
                <div class="table-cell v-top img-cell"><img src="<?php echo $category['thumb']; ?>" alt="<?php echo $category['name']; ?>" /></div>
                
                <div class="table-cell w100 v-top">
                <h5><b><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></b></h5>
                <?php if ($category['children']) { ?>
                <ul class="list-unstyled">
                <?php foreach ($category['children'] as $child) { ?>
                <li><a class="hover_uline" href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a></li>
                <?php } ?>
                </ul>
                <?php } ?>
                </div>
                </div>
                </div><!-- .single-category ends -->
              <?php } ?>
              
        <?php } else { ?>
        	  
              <?php foreach ($categories as $category) { ?>
                <div class="item single-category no-subs">
                <div class="banner_wrap hover-zoom">
                <a href="<?php echo $category['href']; ?>"><img class="zoom_image" src="<?php echo $category['thumb']; ?>" alt="<?php echo $category['name']; ?>" /></a>
                <?php if ($count) { ?>
                <div class="overlay">
                <a class="table w100 h100" href="<?php echo $category['href']; ?>">
                <b class="table-cell text-center"><?php echo $category['products']; ?></b>
                </a>
                </div>
                <?php } ?>
                </div>
                <p class="name contrast-heading"><?php echo $category['name']; ?></p>
                <a class="u-lined" href="<?php echo $category['href']; ?>"><?php echo $basel_text_view_products; ?></a>
                </div><!-- .single-category ends -->
              <?php } ?>
              
        <?php } ?>
          
        </div> <!-- .grid-holder ends -->
    <?php } ?>
    
    <div class="clearfix"></div>
</div>

<?php if ($carousel) { ?>
<script><!--
$('.grid-holder.category.module<?php echo $module; ?>').slick({
<?php if ($carousel_a) { ?>
prevArrow: "<a class=\"arrow-left icon-arrow-left\"></a>",
nextArrow: "<a class=\"arrow-right icon-arrow-right\"></a>",
<?php } else { ?>
arrows: false,
<?php } ?>
<?php if ($direction == 'rtl') { ?>
rtl: true,
<?php } ?>
<?php if ($carousel_b) { ?>
dots:true,
<?php } ?>
respondTo:'min',
rows:<?php echo $rows; ?>,
<?php if ($columns == '6') { ?>
slidesToShow:6,slidesToScroll:6,responsive:[{breakpoint:1100,settings:{slidesToShow:5,slidesToScroll:5}},{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '5') { ?>
slidesToShow:5,slidesToScroll:5,responsive:[{breakpoint:1100,settings:{slidesToShow:4,slidesToScroll:4}},{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '4') { ?>
slidesToShow:4,slidesToScroll:4,responsive:[{breakpoint:960,settings:{slidesToShow:3,slidesToScroll:3}},{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '3') { ?>
slidesToShow:3,slidesToScroll:3,responsive:[{breakpoint:600,settings:{slidesToShow:2,slidesToScroll:2}},
<?php } elseif ($columns == '2') { ?>
slidesToShow:2,slidesToScroll:2,responsive:[
<?php } elseif (($columns == '1' || $columns == 'list')) { ?>
adaptiveHeight:true,slidesToShow:1,slidesToScroll:1,responsive:[
<?php } ?>
{breakpoint:420,settings:{slidesToShow:1,slidesToScroll:1}}
]
});
//--></script>
<?php } ?>