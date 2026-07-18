
    <h4><b><?php echo $heading_title; ?></b></h4>
 
<div class="form-vertical">
 <label class="control-label" for="input-coupon"><?php echo $entry_voucher; ?></label>
        <input type="text" name="voucher" value="<?php echo $voucher; ?>" placeholder="<?php echo $entry_voucher; ?>" id="input-voucher" class="form-control margin-b10" />
        
        <input type="submit" value="<?php echo $button_voucher; ?>" id="button-voucher" data-loading-text="<?php echo $text_loading; ?>"  class="btn btn-primary" />
</div>
      
<script><!--
$('#button-voucher').on('click', function() {
  $.ajax({
    url: 'index.php?route=extension/total/voucher/voucher',
    type: 'post',
    data: 'voucher=' + encodeURIComponent($('input[name=\'voucher\']').val()),
    dataType: 'json',
    beforeSend: function() {
      $('#button-voucher').button('loading');
    },
    complete: function() {
      $('#button-voucher').button('reset');
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
