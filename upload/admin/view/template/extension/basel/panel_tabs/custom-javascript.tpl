<legend>Custom Javascript</legend>

<div class="form-group">
    <label class="col-sm-2 control-label">Custom Javascript Status</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_custom_js_status]" class="custom-js-select" value="0" <?php if($basel_custom_js_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_custom_js_status]" class="custom-js-select" value="1" <?php if($basel_custom_js_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div id="custom_js_holder"<?php if($basel_custom_js_status){echo ' style="display:block"';} else {echo ' style="display:none"';} ?>>
<div class="form-group">
    <label class="col-sm-2 control-label">Javascript</label>
    <div class="col-sm-10">
    <textarea name="settings[basel][basel_custom_js]" class="form-control code"><?php echo isset($basel_custom_js) ? $basel_custom_js : ''; ?></textarea>
    </div>                   
</div>
</div>
