<?php if ($fullwidth) { ?>
</div>
<?php } ?>
<div class="widget" style="margin-bottom:<?php echo $margin_bottom; ?>;">
<div id="masterslider<?php echo $module; ?>" class="master-slider widget <?php echo $nav_buttons; ?>" style="min-height:<?php echo $height; ?>px">
<?php foreach($sections as $section) { ?>
<?php if($section['sort_order'] != '-1'){ ?>
<div class="ms-slide <?php if (!empty($section['slide_kenburn'])) echo 'kenburn ' . $section['slide_kenburn']; ?>" data-delay="<?php echo $section['duration']; ?>" <?php if (!empty($section['bg_color'])) { ?>style="background-color:<?php echo $section['bg_color']; ?>"<?php } ?>>
<?php if (!empty($section['is_bg'])) { ?>
<img src="<?php echo $section['thumb_image']; ?>" data-src="<?php echo $section['thumb_image']; ?>" class="slide-bg" alt="">
<?php } ?>
<?php foreach($section['groups'] as $key => $group){ ?>
<?php if($group['type'] == 'text'){ ?>
<!-- Text layer -->
    <div class="ms-layer ms-caption" style="font-family:<?php echo $group['font']; ?>;font-weight:<?php echo $group['fontweight']; ?>;font-size:<?php echo $group['fontsize']; ?>;color:<?php echo $group['color']; ?>;<?php if ($group['bg']) echo 'background-color:' . $group['bg']; ?>;padding:<?php echo $group['padding']; ?>;border-radius:<?php echo $group['radius']; ?>;white-space:nowrap; <?php echo $group['customcss']; ?>;"
    data-origin = "ml" data-type = "text" data-offset-x = "<?php echo $group['left']; ?>" data-offset-y = "<?php echo $group['top']; ?>" data-parallax = "<?php echo $group['p_index']; ?>" data-duration = "<?php echo $group['durationin']; ?>" data-delay = "<?php echo $group['start']; ?>" data-effect = "<?php echo $group['transitionin']; ?>" data-ease = "<?php echo $group['easingin']; ?>" data-hide-effect = "<?php echo $group['transitionout']; ?>" data-hide-duration = "<?php echo $group['durationout']; ?>" data-hide-ease = "<?php echo $group['easingout']; ?>" data-hide-time = "<?php echo $group['end']; ?>" > <?php echo $group['description']; ?>
    </div>
<?php } ?>
<?php if($group['type'] == 'image'){ ?>
<!-- Image layer -->
    <img src="<?php echo $group['image']; ?>" alt="" data-src="<?php echo $group['image']; ?>" class="ms-layer image" 
    <?php if($group['minheight']){ ?> style="min-height:<?php echo $group['minheight']; ?>px" <?php } ?>
    data-origin = "ml" data-type = "image" data-offset-x = "<?php echo $group['left']; ?>" data-offset-y = "<?php echo $group['top']; ?>" data-parallax = "<?php echo $group['p_index']; ?>" data-duration = "<?php echo $group['durationin']; ?>" data-delay = "<?php echo $group['start']; ?>" data-effect = "<?php echo $group['transitionin']; ?>" data-ease = "<?php echo $group['easingin']; ?>" data-hide-effect = "<?php echo $group['transitionout']; ?>" data-hide-duration = "<?php echo $group['durationout']; ?>" data-hide-ease = "<?php echo $group['easingout']; ?>" data-hide-time = "<?php echo $group['end']; ?>" />
<?php } ?>
<?php if($group['type'] == 'button'){ ?>
<!-- Button layer -->
    <a class="ms-layer ms-btn <?php echo $group['button_class']; ?> <?php if ($group['button_target']) { echo 'external'; } ?>" href="<?php echo $group['button_href']; ?>" style="white-space:nowrap;" 
    data-origin = "ml" data-type = "button" data-offset-x = "<?php echo $group['left']; ?>" data-offset-y = "<?php echo $group['top']; ?>" data-parallax = "<?php echo $group['p_index']; ?>" data-duration = "<?php echo $group['durationin']; ?>" data-delay = "<?php echo $group['start']; ?>" data-effect = "<?php echo $group['transitionin']; ?>" data-ease = "<?php echo $group['easingin']; ?>" data-hide-effect = "<?php echo $group['transitionout']; ?>" data-hide-duration = "<?php echo $group['durationout']; ?>" data-hide-ease = "<?php echo $group['easingout']; ?>" data-hide-time = "<?php echo $group['end']; ?>" >
    <span><?php echo $group['description']; ?></span></a>
<?php } ?>
<?php } ?><!-- foreach groups -->
<?php if ($section['link']) { ?>
<a href="<?php echo $section['link']; ?>" class="ls-link <?php if ($section['link_new_window']) echo ' external'; ?>"></a>
<?php } ?>
</div><!-- .ls-slide ends -->
<?php } ?>
<?php } ?><!-- foreach sections ends -->
</div><!-- id masterslider ends -->
</div><!-- .widget -->
<script>
$(document).ready(function(){
var slider = new MasterSlider();
slider.setup('masterslider<?php echo $module; ?>' , {
	width:<?php echo $width; ?>,
	height:<?php echo $height; ?>,
	minHeight:<?php echo $minheight; ?>,
	layout:"fillwidth",
	grabCursor:false,
	centerControls:false,
	autoplay:true,
	hideLayers:true,
	instantStartLayers:true,
	overPause:false,
	<?php if($loop){ ?>loop:true,<?php } ?>
	speed:<?php echo $speed; ?>,
	view:"<?php echo $slide_transition; ?>"
});
<?php if($nav_buttons){ ?>slider.control('arrows' , {autohide:false});<?php } ?>
<?php if($nav_bullets){ ?>slider.control('bullets' , {autohide:false});<?php } ?>
<?php if($nav_timer_bar){ ?>slider.control('timebar' , {width:5});<?php } ?>
setTimeout("$('body').addClass('ms_ready');", 400);
});
</script>
<?php if ($fullwidth) { ?>
<div class="container">
<?php } ?>