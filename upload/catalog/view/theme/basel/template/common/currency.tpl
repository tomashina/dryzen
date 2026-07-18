<?php if (count($currencies) > 1) { ?>
<div class="option">
<h4><?php echo $text_currency; ?></h4>
<?php foreach ($currencies as $currency) { ?>
<?php if ($currency['code'] == $code) { ?>
<p><span class="anim-underline active"><?php echo $currency['title']; ?></span></p>
<?php } else { ?>
<p><a class="anim-underline" onclick="$('input[name=\'code\']').attr('value', '<?php echo $currency['code']; ?>'); $('#form-currency').submit();">
<?php echo $currency['title']; ?></a></p>
<?php } ?>
<?php } ?>
<select name="currency-select" id="currency-select">
<?php foreach ($currencies as $currency) { ?>
<?php if ($currency['code'] == $code) { ?>
<option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['code']; ?></option>
<?php } else { ?>
<option value="<?php echo $currency['code']; ?>"><?php echo $currency['code']; ?></option>
<?php } ?>
<?php } ?>
</select>
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-currency">  
<input type="hidden" name="code" id="curr-code" value="" />
<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
</form>
<script>
$(document).ready(function() {
$('.mobile-lang-curr').addClass('has-c');
$('#currency-select').appendTo('.mobile-lang-curr');
});
</script>
</div>
<?php } ?>