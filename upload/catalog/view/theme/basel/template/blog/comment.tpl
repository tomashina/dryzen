<?php if ($comments) { ?>
<h3 class="section-title"><b><?php echo $text_comments; ?></b></h3>
<?php foreach ($comments as $comment) { ?>
<div class="table blog_comment">
    <div class="table-cell v-top avatar">
        <i class="icon-user"></i>
    </div>
    <div class="table-cell w100">
        <p><b><?php echo $comment['name']; ?></b> - <?php echo $comment['date_added']; ?></p>
        <p><?php echo $comment['comment']; ?></p>
    </div>
</div>
<?php } ?>
<?php echo str_replace(array("&gt;|","|&lt;"),array("&gt;&gt", "&lt;&lt"),$pagination); ?>
<?php } ?>