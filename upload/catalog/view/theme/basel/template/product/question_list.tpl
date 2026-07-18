<?php if ($questions) { ?>
    <?php foreach ($questions as $question) { ?>
    <div class="table">
    <div class="table-cell"><i class="fa fa-comments-o"></i></div>
    <div class="table-cell right">
    <p class="author"><b><?php echo $basel_text_question_from; ?> <?php echo $question['author']; ?></b> - <span><?php echo $question['date_added']; ?></span></p>
    <p class="question">- <?php echo $question['text']; ?></p>
    <?php if ($question['answer']) { ?>
    <p class="our-answer"><b><?php echo $basel_text_our_answer; ?></b></p>
    <p>- <?php echo $question['answer']; ?></p>
    <?php } else { ?>
    <p class="no-answer"><i>(<?php echo $basel_text_no_answer; ?>)</i></p>
    <?php } ?>
    </div>
    </div>
    <?php } ?>
    <?php if ($pagination) { ?>
    <div class="pagination-holder"><?php echo str_replace(array("&gt;|","|&lt;"),array("&gt;&gt", "&lt;&lt"),$pagination); ?></div>
    <?php } ?>
<?php } else { ?>
	<p><?php echo $basel_text_no_questions; ?></p>
<?php } ?>