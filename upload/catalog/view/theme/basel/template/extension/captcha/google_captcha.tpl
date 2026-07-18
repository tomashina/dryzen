<script src="//www.google.com/recaptcha/api.js"></script>

<?php if (substr($route, 0, 9) == 'checkout/') { ?>

<fieldset>
<legend><?php echo $text_captcha; ?></legend>
<div class="form-group required">
<label class="control-label" for="input-payment-captcha"><?php echo $entry_captcha; ?></label>
<div id="input-payment-captcha" class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
<?php if ($error_captcha) { ?>
<div class="text-danger"><?php echo $error_captcha; ?></div>
<?php } ?>
</div>
</fieldset>

<?php } else if ((substr($route, 0, 12) == 'information/') || (substr($route, 0, 8) == 'product/')) { ?>

<div class="required">
<label class="control-label"><?php echo $entry_captcha; ?></label>
<div class="g-recaptcha margin-b10" data-sitekey="<?php echo $site_key; ?>"></div>
<?php if ($error_captcha) { ?>
<div class="text-danger margin-b10"><?php echo $error_captcha; ?></div>
<?php } ?>
</div>

<?php } else { ?>

<fieldset>
<legend><?php echo $text_captcha; ?></legend>
<div class="form-group required">
<label class="col-sm-2 control-label"><?php echo $entry_captcha; ?></label>
<div class="col-sm-10">
<div class="g-recaptcha" data-sitekey="<?php echo $site_key; ?>"></div>
<?php if ($error_captcha) { ?>
<div class="text-danger"><?php echo $error_captcha; ?></div>
<?php } ?>
</div>
</div>
</fieldset>

<?php } ?>