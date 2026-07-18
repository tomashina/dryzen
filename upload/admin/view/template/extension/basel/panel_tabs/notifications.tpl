<legend>Notifications</legend>



<legend class="sub">Top Line Promotion Message</legend>



<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="The top line notification can be used to put a text message to the top of the header">Status</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_top_promo_status]" value="0" <?php if($basel_top_promo_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_top_promo_status]" value="1" <?php if($basel_top_promo_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Width</label>
    <div class="col-sm-10 toggle-btn both-blue">
    <label><input type="radio" name="settings[basel][basel_top_promo_width]" value="" <?php if($basel_top_promo_width == ''){echo ' checked="checked"';} ?> /><span>Boxed</span></label>
    <label><input type="radio" name="settings[basel][basel_top_promo_width]" value="full-width" <?php if($basel_top_promo_width == 'full-width'){echo ' checked="checked"';} ?> /><span>Full Width</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="If enabled, a close button will be added to the message, and if clicked, a cookie will  block the message for that user the next 30 days">Close button</span></label>
    <div class="col-sm-10 toggle-btn both-blue">
    <label><input type="radio" name="settings[basel][basel_top_promo_close]" value="0" <?php if($basel_top_promo_close == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_top_promo_close]" value="1" <?php if($basel_top_promo_close == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Text alignment</label>
    <div class="col-sm-10 toggle-btn both-blue">
    <label><input type="radio" name="settings[basel][basel_top_promo_align]" value="" <?php if($basel_top_promo_align == ''){echo ' checked="checked"';} ?> /><span>Left</span></label>
    <label><input type="radio" name="settings[basel][basel_top_promo_align]" value="text-center" <?php if($basel_top_promo_align == 'text-center'){echo ' checked="checked"';} ?> /><span>Center</span></label>
    </div>                   
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Promo text</label>
    <div class="col-sm-10">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon">
    <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['language_id']; ?>" /></span>
    <input class="form-control" name="settings[basel][basel_top_promo_text][<?php echo $language['language_id']; ?>]" value="<?php echo isset($basel_top_promo_text[$language['language_id']]) ? $basel_top_promo_text[$language['language_id']] : ''; ?>" />
    </div>
    <?php } ?>
    </div>
</div>



<legend class="sub">Cookie Information Bar</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Cookie bar status</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_cookie_bar_status]" value="0" <?php if($basel_cookie_bar_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_cookie_bar_status]" value="1" <?php if($basel_cookie_bar_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="The link target for the Read more link. Leave empty to disable the link">Read more link target</span></label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[basel][basel_cookie_bar_url]" value="<?php echo isset($basel_cookie_bar_url) ? $basel_cookie_bar_url : ''; ?>" />
    </div>                   
</div>


<legend class="sub">Popup Notification</legend>

<div class="form-group">
    <label class="col-sm-2 control-label">Popup notification status</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_popup_note_status]" value="0" <?php if($basel_popup_note_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_popup_note_status]" value="1" <?php if($basel_popup_note_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="If enabled, a cookie will be set for 30 days to prevent the popup for being shown more than one time per visitor">Show only once</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_popup_note_once]" value="0" <?php if($basel_popup_note_once == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_popup_note_once]" value="1" <?php if($basel_popup_note_once == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Apply to home page only</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_popup_note_home]" value="0" <?php if($basel_popup_note_home == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_popup_note_home]" value="1" <?php if($basel_popup_note_home == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Delay in milliseconds until the popup opens. For example, 5000 will open the popup after 5 seconds. Enter 0 to show the popup immediately on page load.">Popup delay</span></label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:120px" name="settings[basel][basel_popup_note_delay]" value="<?php echo isset($basel_popup_note_delay) ? $basel_popup_note_delay : '5000'; ?>" /> ms
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Width * Height">Popup size</span></label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:120px" name="settings[basel][basel_popup_note_w]" value="<?php echo isset($basel_popup_note_w) ? $basel_popup_note_w : '860'; ?>" /> px &nbsp;
    <input class="form-control" style="width:120px" name="settings[basel][basel_popup_note_h]" value="<?php echo isset($basel_popup_note_h) ? $basel_popup_note_h : '500'; ?>" /> px 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable or disable the popup to visitors using a mobile device">Mobile status</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_popup_note_m]" value="767" <?php if($basel_popup_note_m == '767'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_popup_note_m]" value="10" <?php if($basel_popup_note_m == '10'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="If an image is chosen, it will take place at the left side of the popup window">Image</span></label>
    <div class="col-sm-10">
    <a href="" id="thumb-popup-img" data-toggle="image" class="img-thumbnail"><img src="<?php echo $popup_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
    <input type="hidden" name="settings[basel][basel_popup_note_img]" value="<?php echo $basel_popup_note_img; ?>" id="input-popup-img" />
    </div>                   
</div>

<ul class="nav nav-tabs language-tabs">
<?php foreach ($languages as $language) { ?>
<li><a href="#basel_popup_note_block<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
<?php } ?>
</ul>
<div class="tab-content">
<?php foreach ($languages as $language) { ?>
<div class="tab-pane" id="basel_popup_note_block<?php echo $language['language_id']; ?>">

<div class="form-group">
<label class="col-sm-2 control-label">Heading</label>
<div class="col-sm-10">
  <input class="form-control" name="settings[basel][basel_popup_note_title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($basel_popup_note_title[$language['language_id']]) ? $basel_popup_note_title[$language['language_id']] : ''; ?>" />
</div>
</div>

  <div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use shortcode {signup} to add a newsletter signup field">Popup content</span>
    <br /><a class="editor-toggle" id="enable-editor-popup-1" onclick="enable_editor('popup','1')">Enable HTML editor</a></label>
    <div class="col-sm-10">
      <textarea id="editor-textarea-popup-1" name="settings[basel][basel_popup_note_block][<?php echo $language['language_id']; ?>]" class="form-control code"><?php echo isset($basel_popup_note_block[$language['language_id']]) ? $basel_popup_note_block[$language['language_id']] : ''; ?></textarea>
    </div>
  </div>
  
 </div>
 <?php } ?>
</div>