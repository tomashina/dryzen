<?php if (count($languages) > 1) { ?>
<div class="option">
<h4><?php echo $text_language; ?></h4>
<?php foreach ($languages as $language) { ?>
<?php if ($language['code'] == $code) { ?>
<p><span class="anim-underline active"><?php echo $language['name']; ?></span></p>
<?php } else { ?>
<p><a class="anim-underline" onclick="$('input[name=\'code\']').attr('value', '<?php echo $language['code']; ?>'); $('#form-language').submit();">
<?php echo $language['name']; ?></a></p>
<?php } ?>
<?php } ?>
<select name="language-select" id="language-select" class="-hidden-md -hidden-lg">
<?php foreach ($languages as $language) { ?>
<?php if ($language['code'] == $code) { ?>
<option value="<?php echo $language['code']; ?>" selected="selected"><?php echo $language['name']; ?></option>
<?php } else { ?>
<option value="<?php echo $language['code']; ?>"><?php echo $language['name']; ?></option>
<?php } ?>
<?php } ?>
</select>
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language">
<input type="hidden" name="code" id="lang-code" value="" />
<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
</form>
<script>
$(document).ready(function() {
$('.mobile-lang-curr').addClass('has-l');
$('#language-select').appendTo('.mobile-lang-curr');
});
</script>
</div>
<?php } ?>