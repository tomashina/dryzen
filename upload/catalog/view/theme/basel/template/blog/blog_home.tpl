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
      <div class="blog">
      
      <h1 id="page-title"><?php echo $heading_title; ?></h1>
      
      <?php if($description){ ?>
      <div class="main_description">
      <?php echo $description; ?>
      </div>
      <?php } ?>
  	
    <?php if($blogs){ ?>
		<div class="grid-holder grid<?php echo $list_columns; ?>">
			
            <?php foreach ($blogs as $blog) { ?>
				<div class="item single-blog">
                
                <?php if($blog['image']){ ?>
                <div class="banner_wrap hover-zoom hover-darken">
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
                
				<p class="short-description"><?php echo $blog['short_description']; ?></p>
                <a class="u-lined" href="<?php echo $blog['href']; ?>"><?php echo $text_read_more; ?></a>
                </div>
               </div>
			<?php } ?>
          </div>
		<div class="row pagination-holder">
        <div class="col-sm-6 xs-text-center"><?php echo str_replace(array("&gt;|","|&lt;"),array("&gt;&gt", "&lt;&lt"),$pagination); ?></div>
        <div class="col-sm-6 text-right xs-text-center"><span class="pagination-text"><?php echo $results; ?></span></div>
      </div>
	<?php }else{ ?>
		<div><?php echo $text_no_blog_posts; ?></div>
	<?php } ?>
    </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?> 