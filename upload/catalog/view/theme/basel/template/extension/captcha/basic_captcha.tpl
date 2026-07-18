<?php if (substr($route, 0, 9) == 'checkout/') { ?>
    
    <div class="form-group required margin-b10">
    <label class="control-label" for="input-payment-captcha"><?php echo $entry_captcha; ?></label>
    <input type="text" name="captcha" id="input-payment-captcha" class="form-control margin-b10" />
    <img src="index.php?route=extension/captcha/basic_captcha/captcha" alt="" class="margin-b10" />
    </div>
    
<?php } else if (substr($route, 0, 8) == 'product/') { ?>

    <label class="control-label" for="input-payment-captcha"><?php echo $entry_captcha; ?></label>
    <input type="text" name="captcha" id="input-payment-captcha" class="form-control margin-b10 grey" />
    <img src="index.php?route=extension/captcha/basic_captcha/captcha" alt="" class="margin-b10" />
    
<?php } else if (substr($route, 0, 12) == 'information/') { ?>

    <div class="form-group required margin-b10">
    <label class="control-label" for="input-payment-captcha"><?php echo $entry_captcha; ?></label>
    <input type="text" name="captcha" id="input-payment-captcha" class="form-control margin-b10" />
    <img src="index.php?route=extension/captcha/basic_captcha/captcha" alt="" class="margin-b10" />
    <?php if ($error_captcha) { ?>
    <div class="text-danger"><?php echo $error_captcha; ?></div>
    <?php } ?>
    </div>
    
<?php } else { ?>

    <div class="form-group required margin-b10">
    <label class="col-sm-2 control-label" for="input-captcha"><?php echo $entry_captcha; ?></label>
    <div class="col-sm-10">
    <input type="text" name="captcha" id="input-captcha" class="form-control margin-b10" />
    <img src="index.php?route=extension/captcha/basic_captcha/captcha" alt="" />
    <?php if ($error_captcha) { ?>
    <div class="text-danger"><?php echo $error_captcha; ?></div>
    <?php } ?>
    </div>
    </div>
    
<?php } ?>