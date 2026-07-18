<legend>Category Pages</legend>

<legend class="sub">Category Description</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable/Disable to include the category thumb in the category description.">Category thumb</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][category_thumb_status]" value="0" <?php if($category_thumb_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][category_thumb_status]" value="1" <?php if($category_thumb_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<legend class="sub">Sub Categories On Category Pages</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Show sub categories</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][category_subs_status]" value="0" <?php if($category_subs_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][category_subs_status]" value="1" <?php if($category_subs_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Sub categories per row</label>
<div class="col-sm-10">
    <select name="settings[basel][basel_subs_grid]" class="form-control">
        <option value="2"<?php if($basel_subs_grid == '2'){echo ' selected="selected"';} ?>>2</option>
        <option value="3"<?php if($basel_subs_grid == '3'){echo ' selected="selected"';} ?>>3</option>
        <option value="4"<?php if($basel_subs_grid == '4'){echo ' selected="selected"';} ?>>4</option>
        <option value="5"<?php if($basel_subs_grid == '5'){echo ' selected="selected"';} ?>>5</option>
        <option value="6"<?php if($basel_subs_grid == '6'){echo ' selected="selected"';} ?>>6</option>
    </select>
</div>
</div>

<legend class="sub">Product Listings</legend>
<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Number of products per row will also apply on search result pages, specials page and manufacturer pages">Products per row</span></label>
<div class="col-sm-10">
    <select name="settings[basel][basel_prod_grid]" class="form-control">
        <option value="2"<?php if($basel_prod_grid == '2'){echo ' selected="selected"';} ?>>2</option>
        <option value="3"<?php if($basel_prod_grid == '3'){echo ' selected="selected"';} ?>>3</option>
        <option value="4"<?php if($basel_prod_grid == '4'){echo ' selected="selected"';} ?>>4</option>
        <option value="5"<?php if($basel_prod_grid == '5'){echo ' selected="selected"';} ?>>5</option>
    </select>
</div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Products per page</label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[theme_default][theme_default_product_limit]" value="<?php echo isset($theme_default_product_limit) ? $theme_default_product_limit : '12'; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="In the list view, short description character limit (categories, special etc)">List Description Limit</span></label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[theme_default][theme_default_product_description_length]" value="<?php echo isset($theme_default_product_description_length) ? $theme_default_product_description_length : '190'; ?>" />
    </div>                   
</div>

