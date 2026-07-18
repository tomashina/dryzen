<?php if ($reviews) { ?>
<?php foreach ($reviews as $review) { ?>
<div class="table">
<div class="table-cell"><i class="fa fa-user"></i></div>
<div class="table-cell right">
<p class="author"><b><?php echo $review['author']; ?></b> &nbsp;-&nbsp; <?php echo $review['date_added']; ?>
<span class="rating">
<span class="rating_stars rating r<?php echo $review['rating']; ?>">
<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
</span>
</span>
</p>
<?php echo $review['text']; ?>
</div>
</div>
<?php } ?>
<?php if ($pagination) { ?>
<div class="pagination-holder"><?php echo str_replace(array("&gt;|","|&lt;"),array("&gt;&gt", "&lt;&lt"),$pagination); ?></div>
<?php } ?>
<?php } else { ?>
<p><?php echo $text_no_reviews; ?></p>
<?php } ?>