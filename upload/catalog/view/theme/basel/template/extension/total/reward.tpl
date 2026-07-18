
    <h4><b><?php echo $heading_title; ?></b></h4>
  
  <div class="form-vertical">
      <label class="control-label" for="input-reward"><?php echo $entry_reward; ?></label>

        <input type="text" name="reward" value="<?php echo $reward; ?>" placeholder="<?php echo $entry_reward; ?>" id="input-reward" class="form-control margin-b10" />

        <input type="submit" value="<?php echo $button_reward; ?>" id="button-reward" data-loading-text="<?php echo $text_loading; ?>"  class="btn btn-primary" />

       </div>
       
      <script><!--
$('#button-reward').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/total/reward/reward',
		type: 'post',
		data: 'reward=' + encodeURIComponent($('input[name=\'reward\']').val()),
		dataType: 'json',
		beforeSend: function() {
			$('#button-reward').button('loading');
		},
		complete: function() {
			$('#button-reward').button('reset');
		},
		success: function(json) {
			$('.alert').remove();

			if (json['error']) {
				$('.breadcrumb').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}

			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});
//--></script>