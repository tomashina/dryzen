<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9 col-sm-8'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
    <div class="blog blog_post">
    
    <?php if($main_thumb && $blogsetting_post_thumb){ ?>
    <div class="main_thumb">
    <img src="<?php echo $main_thumb; ?>" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" />
    <?php if($post_date_added_status){ ?>
    <div class="date_added">
    <span class="day"><?php echo $date_added_day; ?></span>
    <b class="month"><?php echo $date_added_month; ?></b>
    </div>
    <?php } ?>
    </div>
    <?php } ?>
    
	<h1 id="page-title" class="contrast-font"><?php echo $heading_title; ?></h1>
	
    <div class="blog_stats">
	<?php if($post_author_status){ ?><i><?php echo $text_posted_by; ?>: <?php echo $author; ?></i><?php } ?>
	<?php if($post_page_view_status){ ?><i><?php echo $text_read; ?>: <?php echo $new_read_counter_value; ?></i><?php } ?>
	<?php if($post_comments_count_status){ ?><i><?php echo $text_comments; ?>: <?php echo $comment_total; ?></i><?php } ?>
	</div>
    
    <div class="main_description">
	<?php echo $description; ?>
    </div>
    
    <?php if ($tags) { ?>
	<p class="post_tags">
    <?php echo $text_tags; ?>
	<?php for ($i = 0; $i < count($tags); $i++) { ?>
	<?php if ($i < (count($tags) - 1)) { ?>
	<a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>, 
	<?php } else { ?>
	<a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
	<?php } ?>
	<?php } ?>
	</p>
	<?php } ?>
	
    <?php if($share_status){ ?>
	<div class="lg-share">
    <div class="social-icons round inversed">
    <a class="icon facebook fb_share external" rel="nofollow"><i class="fa fa-facebook"></i></a>
    <a class="icon twitter twitter_share external" rel="nofollow"><i class="fa fa-twitter"></i></a>
    <a class="icon google google_share external" rel="nofollow"><i class="icon-google-plus"></i></a>
    <a class="icon pinterest pinterest_share external" rel="nofollow"><i class="fa fa-pinterest"></i></a>
    <a class="icon vk vk_share external" rel="nofollow"><i class="fa fa-vk"></i></a>
    </div>
    </div>
    <?php } ?>
	
    <!-- Related Products -->
    <?php if ($products) { ?>
      <h3 class="section-title"><b><?php echo $text_related_products; ?></b></h3>
        <div class="grid-holder grid grid<?php echo $rel_prod_per_row; ?>">
        <?php foreach ($products as $product) { ?>
        <?php require('catalog/view/theme/basel/template/product/single_product.tpl'); ?>
        <?php } ?>
      </div>
      <?php } ?>
	 <!-- Related Products End -->
     
     
     
     <?php if ($related_blogs) { ?>
		<h3 class="section-title"><b><?php echo $text_related_blog; ?></b></h3>
        <div class="grid-holder grid<?php echo $rel_per_row; ?>">
            <?php foreach ($related_blogs as $blog) { ?>
            <div class="item single-blog related">
                <?php if(($blog['image']) && ($rel_thumb_status)){ ?>
                <div class="banner_wrap hover-zoom hover-darken">
				<img class="zoom_image" src="<?php echo $blog['image']; ?>" alt="<?php echo $blog['title']; ?>" title="<?php echo $blog['title']; ?>" />
                <a href="<?php echo $blog['href']; ?>" class="effect-holder"></a>
                <?php if($date_added_status){ ?>
                <div class="date_added">
                <span class="day"><?php echo date("d",strtotime($blog['date_added_full']));?></span>
                <b class="month"><?php echo date("M",strtotime($blog['date_added_full']));?></b>
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
				<p class="short-description"><?php echo $blog['short_description']; ?></p>
                <a class="u-lined" href="<?php echo $blog['href']; ?>"><?php echo $text_read_more; ?></a>
                </div>
               </div>
			<?php } ?>            
		</div>
	<?php } ?>
	 <!-- Related Blog End -->
	 
     <!-- Comment Area start -->
  		<?php if($allow_comment){ ?>
        
              <div id="comment"></div>
              <form id="comment_form">
                <h3 class="section-title"><b><?php echo $text_write_comment; ?></b></h3>
                <div id="write_response"></div>
                    <div class="row">
                        <div class="form-group col-sm-6 required">
                        <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                        <input type="text" name="name" value="" id="input-name" class="form-control" />
                        </div>
                        <div class="form-group col-sm-6 required">
                        <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                        <input type="text" name="email" value="" id="input-email" class="form-control" />
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="form-group col-sm-12 required">
                        <label class="control-label" for="input-review"><?php echo $entry_comment; ?></label>
                        <textarea name="comment" rows="5" id="input-comment" class="form-control"></textarea>
                        </div>
                    </div>
                
                
                    <div class="row">
                        <div class="col-sm-12">
                              <div class="form-group required">
                              <label class="control-label" for="input-captcha_comment"><?php echo $entry_captcha; ?></label>
                                <div class="input-group">
                                <span class="input-group-addon captcha_addon"><img src="index.php?route=extension/blog/blog/captcha" alt="" id="captcha_comment" /></span>
                                <input type="text" name="captcha_comment" value="" id="input-captcha_comment" class="form-control" />
                                </div>
                              </div>
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="form-group col-sm-12 text-right">
                        <button type="button" id="button-comment" class="btn btn-primary"><?php echo $button_send; ?></button>
                        </div>
                    </div>
                
                
				</form>
      <?php } ?>
      
      </div>
     
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script><!--
$('#comment').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();
	$("html,body").animate({scrollTop:(($("#comment").offset().top)-50)},500);
    $('#comment').fadeOut(50);

    $('#comment').load(this.href);

    $('#comment').fadeIn(500);
	
});

$('#comment').load('index.php?route=extension/blog/blog/comment&blog_id=<?php echo $blog_id; ?>');
//--></script>

<script><!--

$('#button-comment').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/blog/blog/write&blog_id=<?php echo $blog_id; ?>',
		type: 'post',
		dataType: 'json',
		data: $("#comment_form").serialize(),
		
		complete: function() {
			$('#button-comment').button('reset');
			$('#captcha_comment').attr('src', 'index.php?route=extension/blog/blog/captcha#'+new Date().getTime());
			$('input[name=\'captcha_comment\']').val('');
		},
		success: function(json) {
			$('.alert-success, .alert-danger').remove();
			
			if (json['error']) {
				$('#write_response').html('<div class="alert alert-sm alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
				$('#write_response').html('<div class="alert alert-sm alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				
				$('input[name=\'name\']').val('');
				$('input[name=\'email\']').val('');
				$('textarea[name=\'comment\']').val('');
				$('input[name=\'captcha_comment\']').val('');
			}
		}
	});
});    
// Sharing buttons
var share_url = encodeURIComponent(window.location.href);
var page_title = '<?php echo $heading_title ?>';
<?php if ($main_thumb) { ?>
var thumb = '<?php echo $main_thumb ?>';
<?php } ?>
$('.fb_share').attr("href", 'https://www.facebook.com/sharer/sharer.php?u=' + share_url + '');
$('.twitter_share').attr("href", 'https://twitter.com/intent/tweet?source=' + share_url + '&text=' + page_title + ': ' + share_url + '');
$('.google_share').attr("href", 'https://plus.google.com/share?url=' + share_url + '');
$('.pinterest_share').attr("href", 'http://pinterest.com/pin/create/button/?url=' + share_url + '&media=' + thumb + '&description=' + page_title + '');
$('.vk_share').attr("href", 'http://vkontakte.ru/share.php?url=' + share_url + '');
</script>

<script type="application/ld+json">
{
"@context": "http://schema.org",
"@type": "NewsArticle",
"mainEntityOfPage": {
"@type": "WebPage",
"@id": "https://google.com/article"
},
"headline": "<?php echo $heading_title ?>",
<?php if($main_thumb){ ?>
"image": {
"@type": "ImageObject",
"url": "<?php echo $main_thumb ?>",
"height": <?php echo $img_height ?>,
"width": <?php echo $img_width ?>
},
<?php } ?>
"datePublished": "<?php echo $date_added_full ?>",
"dateModified": "<?php echo $date_added_full ?>",
"author": {
"@type": "Person",
"name": "<?php echo $author ?>"
},
"publisher": {
"@type": "Organization",
"name": "<?php echo $store ?>",
<?php if($logo){ ?>
"logo": {
"@type": "ImageObject",
"url": "<?php echo $logo ?>"
}
<?php } ?>
},
"description": "<?php echo $short_description ?>"
}
</script>
<?php echo $footer; ?> 