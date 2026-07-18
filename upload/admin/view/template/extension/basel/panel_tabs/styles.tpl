<legend>Styles & Colors</legend>

<legend class="sub">General</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Shopping cart icon</label>
    <div class="col-sm-10 image-btn">
    <label><input type="radio" name="settings[basel][basel_cart_icon]" value="global-cart-bag" <?php if($basel_cart_icon == 'global-cart-bag'){echo ' checked="checked"';} ?> /><span><i class="icon-bag"></i></span></label>
    
    <label><input type="radio" name="settings[basel][basel_cart_icon]" value="global-cart-basket" <?php if($basel_cart_icon == 'global-cart-basket'){echo ' checked="checked"';} ?> /><span><i class="icon-basket"></i></span></label>
    
    <label><input type="radio" name="settings[basel][basel_cart_icon]" value="global-cart-handbag" <?php if($basel_cart_icon == 'global-cart-handbag'){echo ' checked="checked"';} ?> /><span><i class="icon-handbag"></i></span></label>
    
    <label><input type="radio" name="settings[basel][basel_cart_icon]" value="global-cart-briefcase" <?php if($basel_cart_icon == 'global-cart-briefcase'){echo ' checked="checked"';} ?> /><span><i class="icon-briefcase"></i></span></label>
    
    <label><input type="radio" name="settings[basel][basel_cart_icon]" value="global-cart-shoppingbag" <?php if($basel_cart_icon == 'global-cart-shoppingbag'){echo ' checked="checked"';} ?> /><span><i class="icon-shopping-bag"></i></span></label>
    
    <label><input type="radio" name="settings[basel][basel_cart_icon]" value="global-cart-shoppingbasket" <?php if($basel_cart_icon == 'global-cart-shoppingbasket'){echo ' checked="checked"';} ?> /><span><i class="icon-shopping-basket"></i></span></label>
    </div>                   
</div>

<legend class="sub">Layout</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Layout Style</label>
    <div class="col-sm-10 toggle-btn both-blue">
    <label><input type="radio" name="settings[basel][basel_main_layout]" value="0" <?php if($basel_main_layout == '0'){echo ' checked="checked"';} ?> /><span>Full width</span></label>
    <label><input type="radio" name="settings[basel][basel_main_layout]" value="1" <?php if($basel_main_layout == '1'){echo ' checked="checked"';} ?> /><span>Boxed width</span></label>
    </div>                   
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Content width</label>
<div class="col-sm-10">
    <select name="settings[basel][basel_content_width]" class="form-control">
        <option value="narrow_container"<?php if($basel_content_width == 'narrow_container'){echo ' selected="selected"';} ?>>Narrow (1060px)</option>
        <option value=""<?php if($basel_content_width == ''){echo ' selected="selected"';} ?>>Normal (1170px)</option>
        <option value="wide_container"<?php if($basel_content_width == 'wide_container'){echo ' selected="selected"';} ?>>Wide (1280px)</option>
    </select>
</div>                   
</div>

<legend class="sub">Sticky Columns</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Sticky columns stays within the viewport when scrolling down">Sticky Columns</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_sticky_columns]" value="0" <?php if($basel_sticky_columns == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_sticky_columns]" value="1" <?php if($basel_sticky_columns == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use offset if sticky header is enabled to avoid overlapping columns/header. Enter a value in pixels, for example 100">Sticky Offset Top</span></label>
    <div class="col-sm-10 toggle-btn">
    <input class="form-control" style="width:120px" name="settings[basel][basel_sticky_columns_offset]" value="<?php echo isset($basel_sticky_columns_offset) ? $basel_sticky_columns_offset : '100'; ?>" />
    </div>                   
</div>

<legend class="sub">Widget Titles</legend>
<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Select heading style on modules">Widget heading style</span></label>
<div class="col-sm-10">
    <select name="settings[basel][basel_widget_title_style]" class="form-control">
        <option value="0"<?php if($basel_widget_title_style == '0'){echo ' selected="selected"';} ?>>Style 1 - (Cross Separator)</option>
        <option value="2"<?php if($basel_widget_title_style == '2'){echo ' selected="selected"';} ?>>Style 2 - (Line Separator)</option>
        <option value="3"<?php if($basel_widget_title_style == '3'){echo ' selected="selected"';} ?>>Style 3 - (Bordered Title, No Separator)</option>
    </select>
</div>                   
</div>

<legend class="sub">Product Listing Style</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Select how to show products in product listings, like category pages, related products etc.">Product listing style</span></label>
    <div class="col-sm-10">
    <select name="settings[basel][basel_list_style]" class="form-control">
        <option value="1"<?php if($basel_list_style == '1'){echo ' selected="selected"';} ?>>Style 1 - Default style</option>
        <option value="2"<?php if($basel_list_style == '2'){echo ' selected="selected"';} ?>>Style 2 - Action buttons slide up on hover</option>
        <option value="3"<?php if($basel_list_style == '3'){echo ' selected="selected"';} ?>>Style 3 - Dark image overlay on hover</option>
        <option value="4"<?php if($basel_list_style == '4'){echo ' selected="selected"';} ?>>Style 4 - Dark image overlay on hover alt 2</option>
        <option value="1 names-c"<?php if($basel_list_style == '1 names-c'){echo ' selected="selected"';} ?>>Style 5 - Center aligned</option>
        <option value="6"<?php if($basel_list_style == '6'){echo ' selected="selected"';} ?>>Style 6 - Center aligned with button</option>
    </select>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Show the first additional image when hovering a product">Swap image on hover</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_thumb_swap]" value="0" <?php if($basel_thumb_swap == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_thumb_swap]" value="1" <?php if($basel_thumb_swap == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Keep the product names in listings within one line (using only CSS)">Cut product names</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_cut_names]" value="0" <?php if($basel_cut_names == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_cut_names]" value="1" <?php if($basel_cut_names == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="On smaller mobile devices, select to view 1 or 2 items per row">Minimum items per row</span></label>
    <div class="col-sm-10 toggle-btn both-blue">
    <label><input type="radio" name="settings[basel][items_mobile_fw]" value="1" <?php if($items_mobile_fw == '1'){echo ' checked="checked"';} ?> /><span>1 item</span></label>
    <label><input type="radio" name="settings[basel][items_mobile_fw]" value="0" <?php if($items_mobile_fw == '0'){echo ' checked="checked"';} ?> /><span>2 items</span></label>
    </div>                   
</div>

<legend>Custom Color Scheme</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Override default colors</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_design_status]" class="design-select" value="0" <?php if($basel_design_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_design_status]" class="design-select" value="1" <?php if($basel_design_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div id="custom_design_holder"<?php if($basel_design_status){echo ' style="display:block"';} else {echo ' style="display:none"';} ?>>

<legend class="third">Body Background (when using boxed layout)</legend>

<div class="form-group">
    <label class="col-sm-2 control-label">Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_body_bg_color) ? $basel_body_bg_color : '#ececec'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_body_bg_color]" value="<?php echo isset($basel_body_bg_color) ? $basel_body_bg_color : '#ececec'; ?>" />
    </div> 
    </div>                  
</div>


<div class="form-group">
    <label class="col-sm-2 control-label">Background Image</label>
    <div class="col-sm-10">
    <div class="row">
        <div class="col-sm-2">
        <a href="" id="thumb-bc-img" data-toggle="image" class="img-thumbnail"><img src="<?php echo $body_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
    <input type="hidden" name="settings[basel][basel_body_bg_img]" value="<?php echo $basel_body_bg_img; ?>" id="input-body-img" />
        </div>
        <div class="col-sm-2">
        <label>Background Position</label>
        <select name="settings[basel][basel_body_bg_img_pos]" class="form-control">
        <option value="top left"<?php if($basel_body_bg_img_pos == 'top left'){echo ' selected="selected"';} ?>>top left</option>
        <option value="top center"<?php if($basel_body_bg_img_pos == 'top center'){echo ' selected="selected"';} ?>>top center</option>
        <option value="top right"<?php if($basel_body_bg_img_pos == 'top right'){echo ' selected="selected"';} ?>>top right</option>
        <option value="center left"<?php if($basel_body_bg_img_pos == 'center left'){echo ' selected="selected"';} ?>>middle left</option>
        <option value="center center"<?php if($basel_body_bg_img_pos == 'center center'){echo ' selected="selected"';} ?>>middle center</option>
        <option value="center right"<?php if($basel_body_bg_img_pos == 'center right'){echo ' selected="selected"';} ?>>middle right</option>
        <option value="bottom left"<?php if($basel_body_bg_img_pos == 'bottom left'){echo ' selected="selected"';} ?>>bottom left</option>
        <option value="bottom center"<?php if($basel_body_bg_img_pos == 'bottom center'){echo ' selected="selected"';} ?>>bottom center</option>
        <option value="bottom right"<?php if($basel_body_bg_img_pos == 'bottom right'){echo ' selected="selected"';} ?>>bottom right</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Background Size</label>
        <select name="settings[basel][basel_body_bg_img_size]" class="form-control">
        <option value="auto"<?php if($basel_body_bg_img_size == 'auto'){echo ' selected="selected"';} ?>>auto</option>
        <option value="contain"<?php if($basel_body_bg_img_size == 'contain'){echo ' selected="selected"';} ?>>contain</option>
        <option value="cover"<?php if($basel_body_bg_img_size == 'cover'){echo ' selected="selected"';} ?>>cover</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Background Size</label>
        <select name="settings[basel][basel_body_bg_img_repeat]" class="form-control">
        <option value="no-repeat"<?php if($basel_body_bg_img_repeat == 'no-repeat'){echo ' selected="selected"';} ?>>no-repeat</option>
        <option value="repeat-x"<?php if($basel_body_bg_img_repeat == 'repeat-x'){echo ' selected="selected"';} ?>>repeat-x (-)</option>
        <option value="repeat-y"<?php if($basel_body_bg_img_repeat == 'repeat-y'){echo ' selected="selected"';} ?>>repeat-y (|)</option>
        <option value="repeat"<?php if($basel_body_bg_img_repeat == 'repeat'){echo ' selected="selected"';} ?>>repeat</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Background Attachment</label>
        <select name="settings[basel][basel_body_bg_img_att]" class="form-control">
        <option value="scroll"<?php if($basel_body_bg_img_att == 'scroll'){echo ' selected="selected"';} ?>>scroll</option>
        <option value="local"<?php if($basel_body_bg_img_att == 'local'){echo ' selected="selected"';} ?>>local</option>
        <option value="fixed"<?php if($basel_body_bg_img_att == 'fixed'){echo ' selected="selected"';} ?>>fixed</option>
        </select>
        </div>        
            
    </div>
    </div>                   
</div>


<legend class="third">Top Line Promo Message</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_top_note_bg) ? $basel_top_note_bg : '#000000'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_top_note_bg]" value="<?php echo isset($basel_top_note_bg) ? $basel_top_note_bg : '#000000'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_top_note_color) ? $basel_top_note_color : '#eeeeee'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_top_note_color]" value="<?php echo isset($basel_top_note_color) ? $basel_top_note_color : '#eeeeee'; ?>" />
    </div> 
    </div>                 
</div>

<legend class="third">Top Line</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_top_line_bg) ? $basel_top_line_bg : '#1daaa3'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_top_line_bg]" value="<?php echo isset($basel_top_line_bg) ? $basel_top_line_bg : '#1daaa3'; ?>" />
    </div> 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_top_line_color) ? $basel_top_line_color : '#ffffff'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_top_line_color]" value="<?php echo isset($basel_top_line_color) ? $basel_top_line_color : '#ffffff'; ?>" />
    </div> 
    </div>                   
</div>

<legend class="third">Header Area</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_header_bg) ? $basel_header_bg : '#ffffff'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_header_bg]" value="<?php echo isset($basel_header_bg) ? $basel_header_bg : '#ffffff'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_header_color) ? $basel_header_color : '#000000'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_header_color]" value="<?php echo isset($basel_header_color) ? $basel_header_color : '#000000'; ?>" />
    </div> 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Color on details in header, for example product counters">Header accent color</span></label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_header_accent) ? $basel_header_accent : '#1daaa3'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_header_accent]" value="<?php echo isset($basel_header_accent) ? $basel_header_accent : '#1daaa3'; ?>" />
    </div> 
    </div>                  
</div>

<legend class="third">Menu</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_header_menu_bg) ? $basel_header_menu_bg : '#111111'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_header_menu_bg]" value="<?php echo isset($basel_header_menu_bg) ? $basel_header_menu_bg : '#111111'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_header_menu_color) ? $basel_header_menu_color : '#eeeeee'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_header_menu_color]" value="<?php echo isset($basel_header_menu_color) ? $basel_header_menu_color : '#eeeeee'; ?>" />
    </div> 
    </div>                 
</div>

<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="(When using header 6)">Search field color scheme</span></label>
<div class="col-sm-10">
    <select name="settings[basel][basel_search_scheme]" class="form-control">
        <option value="dark-search"<?php if($basel_search_scheme == 'dark-search'){echo ' selected="selected"';} ?>>Dark</option>
        <option value="light-search"<?php if($basel_search_scheme == 'light-search'){echo ' selected="selected"';} ?>>Light</option>
    </select>
</div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Menu sale label</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_menutag_sale_bg) ? $basel_menutag_sale_bg : '#D41212'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_menutag_sale_bg]" value="<?php echo isset($basel_menutag_sale_bg) ? $basel_menutag_sale_bg : '#D41212'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Menu new label</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_menutag_new_bg) ? $basel_menutag_new_bg : '#1daaa3'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_menutag_new_bg]" value="<?php echo isset($basel_menutag_new_bg) ? $basel_menutag_new_bg : '#1daaa3'; ?>" />
    </div> 
    </div>                  
</div>

<legend class="third">Breadcrumbs (When Holding Titles)</legend>

<div class="form-group">
    <label class="col-sm-2 control-label">Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_bc_bg_color) ? $basel_bc_bg_color : '#000000'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_bc_bg_color]" value="<?php echo isset($basel_bc_bg_color) ? $basel_bc_bg_color : '#000000'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><br /><span data-toggle="tooltip" title="Inline titles helper">Background image</span></label>
    <div class="col-sm-10">
    <div class="row">
        <div class="col-sm-2">
        <a href="" id="thumb-bc-img" data-toggle="image" class="img-thumbnail"><img src="<?php echo $bc_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
    <input type="hidden" name="settings[basel][basel_bc_bg_img]" value="<?php echo $basel_bc_bg_img; ?>" id="input-bc-img" />
        </div>
        <div class="col-sm-2">
        <label>Background Position</label>
        <select name="settings[basel][basel_bc_bg_img_pos]" class="form-control">
        <option value="top left"<?php if($basel_bc_bg_img_pos == 'top left'){echo ' selected="selected"';} ?>>top left</option>
        <option value="top center"<?php if($basel_bc_bg_img_pos == 'top center'){echo ' selected="selected"';} ?>>top center</option>
        <option value="top right"<?php if($basel_bc_bg_img_pos == 'top right'){echo ' selected="selected"';} ?>>top right</option>
        <option value="center left"<?php if($basel_bc_bg_img_pos == 'center left'){echo ' selected="selected"';} ?>>middle left</option>
        <option value="center center"<?php if($basel_bc_bg_img_pos == 'center center'){echo ' selected="selected"';} ?>>middle center</option>
        <option value="center right"<?php if($basel_bc_bg_img_pos == 'center right'){echo ' selected="selected"';} ?>>middle right</option>
        <option value="bottom left"<?php if($basel_bc_bg_img_pos == 'bottom left'){echo ' selected="selected"';} ?>>bottom left</option>
        <option value="bottom center"<?php if($basel_bc_bg_img_pos == 'bottom center'){echo ' selected="selected"';} ?>>bottom center</option>
        <option value="bottom right"<?php if($basel_bc_bg_img_pos == 'bottom right'){echo ' selected="selected"';} ?>>bottom right</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Background Size</label>
        <select name="settings[basel][basel_bc_bg_img_size]" class="form-control">
        <option value="auto"<?php if($basel_bc_bg_img_size == 'auto'){echo ' selected="selected"';} ?>>auto</option>
        <option value="contain"<?php if($basel_bc_bg_img_size == 'contain'){echo ' selected="selected"';} ?>>contain</option>
        <option value="cover"<?php if($basel_bc_bg_img_size == 'cover'){echo ' selected="selected"';} ?>>cover</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Background Size</label>
        <select name="settings[basel][basel_bc_bg_img_repeat]" class="form-control">
        <option value="no-repeat"<?php if($basel_bc_bg_img_repeat == 'no-repeat'){echo ' selected="selected"';} ?>>no-repeat</option>
        <option value="repeat-x"<?php if($basel_bc_bg_img_repeat == 'repeat-x'){echo ' selected="selected"';} ?>>repeat-x (-)</option>
        <option value="repeat-y"<?php if($basel_bc_bg_img_repeat == 'repeat-y'){echo ' selected="selected"';} ?>>repeat-y (|)</option>
        <option value="repeat"<?php if($basel_bc_bg_img_repeat == 'repeat'){echo ' selected="selected"';} ?>>repeat</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Background Attachment</label>
        <select name="settings[basel][basel_bc_bg_img_att]" class="form-control">
        <option value="scroll"<?php if($basel_bc_bg_img_att == 'scroll'){echo ' selected="selected"';} ?>>scroll</option>
        <option value="local"<?php if($basel_bc_bg_img_att == 'local'){echo ' selected="selected"';} ?>>local</option>
        <option value="fixed"<?php if($basel_bc_bg_img_att == 'fixed'){echo ' selected="selected"';} ?>>fixed</option>
        </select>
        </div>        
            
    </div>
    </div>                   
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">Color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_bc_color) ? $basel_bc_color : '#ffffff'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_bc_color]" value="<?php echo isset($basel_bc_color) ? $basel_bc_color : '#ffffff'; ?>" />
    </div> 
    </div>                  
</div>



<legend class="third">Content Area</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Color when hovering links etc">Primary accent color</span></label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_primary_accent_color) ? $basel_primary_accent_color : '#1daaa3'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_primary_accent_color]" value="<?php echo isset($basel_primary_accent_color) ? $basel_primary_accent_color : '#1daaa3'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="On produts in product listings and product pages">Sale badge background</span></label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_salebadge_bg) ? $basel_salebadge_bg : '#1daaa3'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_salebadge_bg]" value="<?php echo isset($basel_salebadge_bg) ? $basel_salebadge_bg : '#1daaa3'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="On produts in product listings and product pages">Sale badge color</span></label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_salebadge_color) ? $basel_salebadge_color : '#ffffff'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_salebadge_color]" value="<?php echo isset($basel_salebadge_color) ? $basel_salebadge_color : '#ffffff'; ?>" />
    </div> 
    </div>                 
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="On produts in product listings and product pages">New badge background</span></label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_newbadge_bg) ? $basel_newbadge_bg : '#ffffff'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_newbadge_bg]" value="<?php echo isset($basel_newbadge_bg) ? $basel_newbadge_bg : '#ffffff'; ?>" />
    </div> 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="On produts in product listings and product pages">New badge color</span></label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_newbadge_color) ? $basel_newbadge_color : '#111111'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_newbadge_color]" value="<?php echo isset($basel_newbadge_color) ? $basel_newbadge_color : '#111111'; ?>" />
    </div> 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Price color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_price_color) ? $basel_price_color : '#1daaa3'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_price_color]" value="<?php echo isset($basel_price_color) ? $basel_price_color : '#1daaa3'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="When using vertical mega menu modules, or when using header alternative 5">Vertical menu background</span></label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_vertical_menu_bg) ? $basel_vertical_menu_bg : '#212121'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_vertical_menu_bg]" value="<?php echo isset($basel_vertical_menu_bg) ? $basel_vertical_menu_bg : '#212121'; ?>" />
    </div> 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="When using vertical mega menu modules, or when using header alternative 5">Vertical menu background on hover</span></label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_vertical_menu_bg_hover) ? $basel_vertical_menu_bg_hover : '#fbbc34'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_vertical_menu_bg_hover]" value="<?php echo isset($basel_vertical_menu_bg_hover) ? $basel_vertical_menu_bg_hover : '#fbbc34'; ?>" />
    </div> 
    </div>                  
</div>


<legend class="third">Buttons</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Default buttons: Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_default_btn_bg) ? $basel_default_btn_bg : '#000000'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_default_btn_bg]" value="<?php echo isset($basel_default_btn_bg) ? $basel_default_btn_bg : '#000000'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Default buttons: Color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_default_btn_color) ? $basel_default_btn_color : '#ffffff'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_default_btn_color]" value="<?php echo isset($basel_default_btn_color) ? $basel_default_btn_color : '#ffffff'; ?>" />
    </div> 
    </div>                  
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Default buttons: Hover background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_default_btn_bg_hover) ? $basel_default_btn_bg_hover : '#3e3e3e'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_default_btn_bg_hover]" value="<?php echo isset($basel_default_btn_bg_hover) ? $basel_default_btn_bg_hover : '#3e3e3e'; ?>" />
    </div> 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Default buttons: Hover color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_default_btn_color_hover) ? $basel_default_btn_color_hover : '#ffffff'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_default_btn_color_hover]" value="<?php echo isset($basel_default_btn_color_hover) ? $basel_default_btn_color_hover : '#ffffff'; ?>" />
    </div> 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Action buttons: Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_contrast_btn_bg) ? $basel_contrast_btn_bg : '#1daaa3'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_contrast_btn_bg]" value="<?php echo isset($basel_contrast_btn_bg) ? $basel_contrast_btn_bg : '#1daaa3'; ?>" />
    </div> 
    </div>                   
</div>


<legend class="third">Footer</legend>
<div class="form-group">
    <label class="col-sm-2 control-label">Background</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_footer_bg) ? $basel_footer_bg : '#000000'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_footer_bg]" value="<?php echo isset($basel_footer_bg) ? $basel_footer_bg : '#000000'; ?>" />
    </div> 
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_footer_color) ? $basel_footer_color : '#ffffff'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_footer_color]" value="<?php echo isset($basel_footer_color) ? $basel_footer_color : '#ffffff'; ?>" />
    </div> 
    </div>               
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Heading / Links separator color</label>
    <div class="col-sm-10">
    <div class="input-group form-inline colorfield">
    <span class="input-group-addon"><i style="background:<?php echo isset($basel_footer_h5_sep) ? $basel_footer_h5_sep : '#cccccc'; ?>"></i></span>
    <input class="form-control" name="settings[basel][basel_footer_h5_sep]" value="<?php echo isset($basel_footer_h5_sep) ? $basel_footer_h5_sep : '#cccccc'; ?>" />
    </div> 
    </div>                
</div>
</div><!-- #custom_design_holder -->
