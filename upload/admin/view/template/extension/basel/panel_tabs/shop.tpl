<legend>Shop</legend>

<legend class="sub">Catalog Mode</legend>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Disable the product purchase options globally">Catlog Mode</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][catalog_mode]" value="0" <?php if($catalog_mode == '0'){echo ' checked="checked"';} ?> /><span>Off</span></label>
    <label><input type="radio" name="settings[basel][catalog_mode]" value="1" <?php if($catalog_mode == '1'){echo ' checked="checked"';} ?> /><span>On</span></label>
    </div>                   
</div>




<legend class="sub">Product Quickview</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable/Disable the product quickview feature globally">Product quickview Status</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][quickview_status]" value="0" <?php if($quickview_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][quickview_status]" value="1" <?php if($quickview_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<legend class="sub">Product Labels</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Sale label</label>
    <div class="col-sm-10">
    <select name="settings[basel][salebadge_status]" class="form-control">
        <option value="0"<?php if($salebadge_status == '0'){echo ' selected="selected"';} ?>>Disabled</option>
        <option value="1"<?php if($salebadge_status == '1'){echo ' selected="selected"';} ?>>Enabled - Sale Text</option>
		<option value="2"<?php if($salebadge_status == '2'){echo ' selected="selected"';} ?>>Enabled - Discount Percentage</option>
    </select>
    </div>                   
</div>


<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Set how many days to show the new-label on new products. Set it as 0 to disable">New label period</span></label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[basel][newlabel_status]" value="<?php echo isset($newlabel_status) ? $newlabel_status : '0'; ?>" />
    </div>                   
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Out of stock label</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][stock_badge_status]" value="0" <?php if($stock_badge_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][stock_badge_status]" value="1" <?php if($stock_badge_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                     
</div>


<legend class="sub">Specials Countdown</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="View time left on (time limited) special prices. Please note tat the Product groups module has its own setting for this.">Specials countdown status</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][countdown_status]" value="0" <?php if($countdown_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][countdown_status]" value="1" <?php if($countdown_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<legend class="sub">Add To Cart</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Action when an item is successfully added to cart">Add to cart action</span></label>
    <div class="col-sm-10 toggle-btn">
    <select name="settings[basel][basel_cart_action]" class="form-control">
        <option value="0"<?php if($basel_cart_action == '0'){echo ' selected="selected"';} ?>>Stay - Notification message</option>
        <option value="redirect_cart"<?php if($basel_cart_action == 'redirect_cart'){echo ' selected="selected"';} ?>>Redirect - Shopping cart</option>
        <option value="redirect_checkout"<?php if($basel_cart_action == 'redirect_checkout'){echo ' selected="selected"';} ?>>Redirect - Checkout</option>
    </select>
    </div>                   
</div>


<legend class="sub">Wish List</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable/Disable the wishlist feature globally">Wish List Status</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][wishlist_status]" value="0" <?php if($wishlist_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][wishlist_status]" value="1" <?php if($wishlist_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Action when an item is successfully added to wish list">Add to wish list action</span></label>
    <div class="col-sm-10 toggle-btn">
    <select name="settings[basel][basel_wishlist_action]" class="form-control">
        <option value="0"<?php if($basel_wishlist_action == '0'){echo ' selected="selected"';} ?>>Stay - Notification message</option>
        <option value="redirect"<?php if($basel_wishlist_action == 'redirect'){echo ' selected="selected"';} ?>>Redirect - Wish List Page</option>
    </select>
    </div>                   
</div>

<legend class="sub">Product Comparison</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable/Disable the product comparison feature globally">Comparison Status</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][compare_status]" value="0" <?php if($compare_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][compare_status]" value="1" <?php if($compare_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Action when an item is successfully added to wish list">Add to compare action</span></label>
    <div class="col-sm-10 toggle-btn">
    <select name="settings[basel][basel_compare_action]" class="form-control">
        <option value="0"<?php if($basel_compare_action == '0'){echo ' selected="selected"';} ?>>Stay - Notification message</option>
        <option value="redirect"<?php if($basel_compare_action == 'redirect'){echo ' selected="selected"';} ?>>Redirect - Product Comparison Page</option>
    </select>
    </div>                   
</div>




