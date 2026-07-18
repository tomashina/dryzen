<div class="widget blog-widget<?php if ($columns != 'list') echo ' grid'; if ($contrast) echo ' contrast-bg'; ?>" <?php if ($use_margin) echo 'style="margin-bottom:' . $margin . '"'; ?>>
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
    <?php if(!empty($posts)){ ?>
        <div class="grid-holder blog grid<?php echo $columns; ?> <?php if ($carousel) echo "carousel"; ?> module<?php echo $module; ?> <?php if ($carousel_a && $rows > 1) echo "sticky-arrows"; ?>">
            <?php foreach ($posts as $blog) { ?>
            <div class="item single-blog">
            <?php if($blog['image'] && $thumb){ ?>
            <div class="banner_wrap hover-zoom hover-darken"<?php if ($columns == 'list') echo ' style="width:' . $img_width . 'px"'; ?>>
            <img class="zoom_image" src="<?php echo $blog['image']; ?>" alt="<?php echo $blog['title']; ?>" title="<?php echo $blog['title']; ?>" />
            <a href="<?php echo $blog['href']; ?>" class="effect-holder"></a>
            <?php if($date_added_status){ ?>
            <div class="date_added">
            <span class="day"><?php echo $blog['date_added_day']; ?></span>
            <b class="month"><?php echo $blog['date_added_month']; ?></b>
            </div>
            <?php } ?>
            <?php if ($blog['tags']) { ?>
            <div class="tags-wrapper">
            <div class="tags primary-bg-color">
            <?php $i = 0; foreach ($blog['tags'] as $tag) { ?><a href="index.php?route=extension/blog/home&tag=<?php echo trim($tag); ?>"><?php echo trim($tag); ?></a><?php if (++$i == 2) break; } ?>
            </div>
            </div>
            <?php } ?>
            </div>
            <?php } ?>
            <div class="summary">
            <h3 class="blog-title"><a href="<?php echo $blog['href']; ?>"><?php echo $blog['title']; ?></a></h3>
            <div class="blog_stats">
            <?php if($author_status){ ?><i><?php echo $text_posted_by; ?>: <?php echo $blog['author']; ?></i><?php } ?>
            <?php if($comments_count_status){ ?><i><?php echo $text_comments; ?>: <?php echo $blog['comment_total']; ?></i><?php } ?>
            <?php if($page_view_status){ ?><i><?php echo $text_read; ?>: <?php echo $blog['count_read']; ?></i><?php } ?>
            </div>
            <?php if($blog['short_description']){ ?>
            <p class="short-description"><?php echo $blog['short_description']; ?></p>
            <?php } ?>
            </div>
            </div>
            <?php } ?>
        </div> <!-- .grid-holder ends -->
        <?php if ($use_button) { ?>
        <div class="widget_bottom_btn <?php if ($carousel && $carousel_b) echo 'has-dots'; ?>">
        <a class="btn btn-outline" href="<?php echo $blog_show_all; ?>"><?php echo $text_show_all; ?></a>
        </div>
        <?php } ?>
    <?php } ?>
    <div class="clearfix"></div>
</div>

<?php if ($carousel) { ?>
<script><!--
$('.grid-holder.blog.module<?php echo $module; ?>').slick({
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
<?php if ($columns == '4') { ?>
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
$("[data-toggle='tooltip']").tooltip();
<?php if ($carousel_a && $rows > 1) { ?>
$(document).ready(function() {
var c_o = $('.module<?php echo $module; ?>').offset().top;
var c_o_b = $('.module<?php echo $module; ?>').offset().top + $('.module<?php echo $module; ?>').outerHeight(true) - 100;
var sticky_arrows = function(){
var m_o = $(window).scrollTop() + ($(window).height()/2);
if (m_o > c_o && m_o < c_o_b) {
$('.grid-holder.blog.module<?php echo $module; ?> .slick-arrow').addClass('visible').css('top', m_o - c_o + 'px');
} else {
$('.grid-holder.blog.module<?php echo $module; ?> .slick-arrow').removeClass('visible');
}
};
$(window).scroll(function() {sticky_arrows();});
});
<?php } ?>
//--></script>
<?php } ?>