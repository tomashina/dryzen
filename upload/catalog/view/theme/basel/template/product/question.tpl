<div class="row">
<div class="col-sm-6">
	<h4><b><?php echo $basel_text_recent_questions; ?></b></h4>
	<div id="question"></div>
</div>
<div class="col-sm-6 right">
<form class="form-horizontal" id="form-question">
    
    <h4 id="ask_heading"><b><?php echo $basel_heading_ask; ?></b></h4>
    
    <div class="form-group required">
      <div class="col-sm-12">
        <label class="control-label" for="input-name"><?php echo $basel_entry_name; ?></label>
        <input type="text" name="q_name" value="" id="input-name" class="form-control grey" />
      </div>
    </div>
    
    <div class="form-group required">
      <div class="col-sm-12">
        <label class="control-label" for="input-email"><?php echo $basel_entry_email; ?></label>
        <input type="text" name="q_email" value="" id="input-email" class="form-control grey" />
      </div>
    </div>
    
    <div class="form-group required">
      <div class="col-sm-12">
        <label class="control-label" for="input-question"><?php echo $basel_entry_question; ?></label>
        <textarea name="q_text" rows="5" id="input-question" class="form-control grey"></textarea>
      </div>
    </div>
    
    <div class="form-group required margin-b10">
      <div class="col-sm-12">
      <label class="control-label"><?php echo $basel_entry_captcha; ?></label>
        <div class="input-group">
        <span class="input-group-addon captcha_addon grey"><img src="index.php?route=extension/basel/question/question_captcha" id="captcha_product_questions" alt="" class="captchaimg" /></span>
        <input type="text" name="captcha_product_questions" value="" id="input-captcha_product_questions" class="form-control grey" />
        </div>
      </div>
    </div>
    
     <div class="buttons clearfix">
      <div class="text-right">
        <button type="button" id="button-question" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-outline"><?php echo $basel_button_send; ?></button>
      </div>
    </div>

  </form>
</div>
</div>
              
<script><!--
$('#question').delegate('.pagination a', 'click', function(e) {
  e.preventDefault();
	$("html,body").animate({scrollTop:(($("#question").offset().top)-50)},500);
    $('#question').fadeOut(50);

    $('#question').load(this.href);

    $('#question').fadeIn(500);
	
});

$('#question').load('index.php?route=extension/basel/question/question_list&product_id=<?php echo $product_id; ?>');

$('#button-question').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/basel/question/ask_question&product_id=<?php echo $product_id; ?>',
		type: 'post',
		dataType: 'json',
		data: 'name=' + encodeURIComponent($('input[name=\'q_name\']').val()) + 
			  '&email=' + encodeURIComponent($('input[name=\'q_email\']').val()) + 
			  '&text=' + encodeURIComponent($('textarea[name=\'q_text\']').val()) + 
			  '&captcha_product_questions=' + encodeURIComponent($('input[name=\'captcha_product_questions\']').val()),
		
		beforeSend: function() {
			$('#button-question').button('loading');
		},
		complete: function() {
			$('#button-question').button('reset');
			$('#captcha_product_questions').attr('src', 'index.php?route=extension/basel/question/question_captcha#'+new Date().getTime());
			$('input[name=\'captcha_product_questions\']').val('');
		},
		success: function(json) {
			$('.alert-success, .alert-danger').remove();
			
			if (json['error']) {
				$('#ask_heading').after('<div class="alert alert-sm alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}
			
			if (json['success']) {
				$('#ask_heading').after('<div class="alert alert-sm alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
				
				$('input[name=\'q_name\']').val('');
				$('input[name=\'q_email\']').val('');
				$('textarea[name=\'q_text\']').val('');
				$('input[name=\'captcha_product_questions\']').val('');
			}
		}
	});
});
//--></script> 
