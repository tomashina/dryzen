<legend>Product Pages</legend>

<legend class="sub">Product Images</legend>

<div class="form-group">
<label class="col-sm-2 control-label">Images layout</label>
<div class="col-sm-10">
    <select name="settings[basel][product_layout]" class="form-control">
        <option value="images-left"<?php if($product_layout == 'images-left'){echo ' selected="selected"';} ?>>Additional images left of main image</option>
        <option value="images-bottom"<?php if($product_layout == 'images-bottom'){echo ' selected="selected"';} ?>>Additional images below main image</option>
        <option value="full-width"<?php if($product_layout == 'full-width'){echo ' selected="selected"';} ?>>Stacked images - (Full width layout)</option>
    </select>
</div>                   
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Zoom main image on hover</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_hover_zoom]" value="0" <?php if($basel_hover_zoom == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_hover_zoom]" value="1" <?php if($basel_hover_zoom == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<legend class="sub">Product Details</legend>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Show the product meta description among the product details">View meta description</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][meta_description_status]" value="0" <?php if($meta_description_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][meta_description_status]" value="1" <?php if($meta_description_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Show a countdown until special prices expires. Please note that there is a separate setting to show countdown on product listings in the Shop tab">Specials countdown</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][product_page_countdown]" value="0" <?php if($product_page_countdown == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][product_page_countdown]" value="1" <?php if($product_page_countdown == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Share buttons</label>
<div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_share_btn]" value="0" <?php if($basel_share_btn == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_share_btn]" value="1" <?php if($basel_share_btn == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                    
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Sharing buttons style</label>
<div class="col-sm-10">
    <select name="settings[basel][basel_sharing_style]" class="form-control">
        <option value="small"<?php if($basel_sharing_style == 'small'){echo ' selected="selected"';} ?>>Default (small)</option>
        <option value="large"<?php if($basel_sharing_style == 'large'){echo ' selected="selected"';} ?>>Large</option>
    </select>
</div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable/Disable price excluding tax on product pages and in list view">View ex tax price</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][ex_tax_status]" value="0" <?php if($ex_tax_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][ex_tax_status]" value="1" <?php if($ex_tax_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable/Disable the price to be updated after selecting options and quantities">Live price update</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_price_update]" value="0" <?php if($basel_price_update == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_price_update]" value="1" <?php if($basel_price_update == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>


<legend class="sub">Product Information Tabs</legend>
<div class="form-group">
<label class="col-sm-2 control-label">Tabs layout</label>
<div class="col-sm-10">
    <select name="settings[basel][full_width_tabs]" class="form-control">
        <option value="1"<?php if($full_width_tabs == '1'){echo ' selected="selected"';} ?>>Full width with contrast background</option>
        <option value="0"<?php if($full_width_tabs == '0'){echo ' selected="selected"';} ?>>Inline</option>
    </select>
</div>                   
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Tabs Style</label>
<div class="col-sm-10">
    <select name="settings[basel][product_tabs_style]" class="form-control">
    	<option value=""<?php if($product_tabs_style == ''){echo ' selected="selected"';} ?>>Default Style</option>
        <option value="nav-tabs-lg text-center"<?php if($product_tabs_style == 'nav-tabs-lg text-center'){echo ' selected="selected"';} ?>>Centered (Large)</option>
        <option value="nav-tabs-sm text-center"<?php if($product_tabs_style == 'nav-tabs-sm text-center'){echo ' selected="selected"';} ?>>Centered (Small)</option>
    </select>
</div>                   
</div>




<legend class="sub">Product Questions &amp; Answers</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Allow customers to ask questions/see previously asked questions about products directly on product pages">Product Questions Status</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][product_question_status]" value="0" <?php if($product_question_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][product_question_status]" value="1" <?php if($product_question_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Questions Per Page</label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[basel][questions_per_page]" value="<?php echo isset($questions_per_page) ? $questions_per_page : '5'; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Set the status on new questions asked. If set to published, not yet answered questions will be visible">Status On New Questions</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][questions_new_status]" value="0" <?php if($questions_new_status == '0'){echo ' checked="checked"';} ?> /><span>Not Published</span></label>
    <label><input type="radio" name="settings[basel][questions_new_status]" value="1" <?php if($questions_new_status == '1'){echo ' checked="checked"';} ?> /><span>Published</span></label>
    </div>                  
</div>

<legend class="sub">Related Products</legend>
<div class="form-group">
<label class="col-sm-2 control-label">Products per row</label>
<div class="col-sm-10">
    <select name="settings[basel][basel_rel_prod_grid]" class="form-control">
        <option value="2"<?php if($basel_rel_prod_grid == '2'){echo ' selected="selected"';} ?>>2</option>
        <option value="3"<?php if($basel_rel_prod_grid == '3'){echo ' selected="selected"';} ?>>3</option>
        <option value="4"<?php if($basel_rel_prod_grid == '4'){echo ' selected="selected"';} ?>>4</option>
        <option value="5"<?php if($basel_rel_prod_grid == '5'){echo ' selected="selected"';} ?>>5</option>
    </select>
</div>                   
</div>