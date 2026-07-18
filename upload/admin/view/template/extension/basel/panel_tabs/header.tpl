<legend>Header Styling</legend>

<div class="form-group">
<label class="col-sm-2 control-label">Header Alternative</label>
<div class="col-sm-10">
    <select id="header-select" name="settings[basel][basel_header]" class="form-control">
        <option value="header1"<?php if($basel_header == 'header1'){echo ' selected="selected"';} ?>>Header 1</option>
        <option value="header2"<?php if($basel_header == 'header2'){echo ' selected="selected"';} ?>>Header 2</option>
        <option value="header3"<?php if($basel_header == 'header3'){echo ' selected="selected"';} ?>>Header 3</option>
        <option value="header4"<?php if($basel_header == 'header4'){echo ' selected="selected"';} ?>>Header 4</option>
        <option value="header5"<?php if($basel_header == 'header5'){echo ' selected="selected"';} ?>>Header 5</option>
        <option value="header6"<?php if($basel_header == 'header6'){echo ' selected="selected"';} ?>>Header 6</option>
    </select>
    <div id="header-preview" class="preview-holder">
    <img src="view/javascript/basel/img/theme-panel/headers/<?php echo $basel_header; ?>.png" />
    </div>
</div>
</div>

<legend class="sub">Top Line</legend>

<div class="form-group">
    <label class="col-sm-2 control-label">Top Line Status</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][top_line_style]" value="0" <?php if($top_line_style == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][top_line_style]" value="1" <?php if($top_line_style == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
<label class="col-sm-2 control-label">Top Line Width</label>
<div class="col-sm-10">
    <select name="settings[basel][top_line_width]" class="form-control">
        <option value="full-width"<?php if($top_line_width == 'full-width'){echo ' selected="selected"';} ?>>Full Width</option>
        <option value="boxed"<?php if($top_line_width == 'boxed'){echo ' selected="selected"';} ?>>Boxed</option>
    </select>
</div>                   
</div>

<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enter value in pixels, eg. 41">Top Line Height</span></label>
<div class="col-sm-10">
    <input class="form-control" name="settings[basel][top_line_height]" value="<?php echo isset($top_line_height) ? $top_line_height : '41'; ?>" />
</div>                   
</div>

<legend class="sub">Main Header Area</legend>
<div class="form-group">
<label class="col-sm-2 control-label">Main Area Width</label>
<div class="col-sm-10">
    <select name="settings[basel][main_header_width]" class="form-control">
        <option value="full-width"<?php if($main_header_width == 'full-width'){echo ' selected="selected"';} ?>>Full Width</option>
        <option value="boxed"<?php if($main_header_width == 'boxed'){echo ' selected="selected"';} ?>>Boxed</option>
    </select>
</div>                   
</div>
<div class="form-group">
    <label class="col-sm-2 control-label"><br /><span data-toggle="tooltip" title="Enter values in pixels, eg. 100">Main Header Area Height</span></label>
    <div class="col-sm-10">
    
    <div class="row">
    <div class="col-sm-4">
    <label>Desktop View</label>
    <input class="form-control" name="settings[basel][main_header_height]" value="<?php echo isset($main_header_height) ? $main_header_height : '104'; ?>" />
    </div>
    
    <div class="col-sm-4">
    <label>Mobile View</label>
    <input class="form-control" name="settings[basel][main_header_height_mobile]" value="<?php echo isset($main_header_height_mobile) ? $main_header_height_mobile : '70'; ?>" />
    </div>
    
    <div class="col-sm-4">
    <label>Sticky Header</label>
    <input class="form-control" name="settings[basel][main_header_height_sticky]" value="<?php echo isset($main_header_height_sticky) ? $main_header_height_sticky : '70'; ?>" />
    </div>
        
    </div>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Keep the header fixed on top of the page when scrolling">Sticky Header</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_sticky_header]" value="0" <?php if($basel_sticky_header == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_sticky_header]" value="1" <?php if($basel_sticky_header == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<legend class="sub">Logo</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enter value in pixels, eg. 100">Logo Max Width</span></label>
    <div class="col-sm-10">
    <input class="form-control" name="settings[basel][logo_maxwidth]" value="<?php echo isset($logo_maxwidth) ? $logo_maxwidth : '250'; ?>" />        
    </div>
</div>

<legend class="sub">Menu Area</legend>
<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Only available on header alternative 2 & 6">Menu Alignment</span></label>
<div class="col-sm-10">
    <select name="settings[basel][main_menu_align]" class="form-control">
    	<option value="menu-aligned-left"<?php if($main_menu_align == 'menu-aligned-left'){echo ' selected="selected"';} ?>>Left</option>
        <option value="menu-aligned-center"<?php if($main_menu_align == 'menu-aligned-center'){echo ' selected="selected"';} ?>>Center</option>
        <option value="menu-aligned-right"<?php if($main_menu_align == 'menu-aligned-right'){echo ' selected="selected"';} ?>>Right</option>
    </select>
</div>                   
</div>
<div class="form-group">
    <label class="col-sm-2 control-label"><br /><span data-toggle="tooltip" title="Enter values in pixels, eg. 100">Menu Area Height</span></label>
    <div class="col-sm-10">
    
    <div class="row">
    <div class="col-sm-4">
    <label>Default</label>
    <input class="form-control" name="settings[basel][menu_height_normal]" value="<?php echo isset($menu_height_normal) ? $menu_height_normal : '50'; ?>" />
    </div>
    
    <div class="col-sm-4">
    <label>Sticky Header</label>
    <input class="form-control" name="settings[basel][menu_height_sticky]" value="<?php echo isset($menu_height_sticky) ? $menu_height_sticky : '70'; ?>" />
    </div>
        
    </div>
    </div>                   
</div>


<legend class="sub">Header Promo Messages</legend>

<div class="form-group">
<label class="col-sm-2 control-label">Primary Promo Message</label>
    <div class="col-sm-10">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon">
    <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['language_id']; ?>" /></span>
    <input class="form-control" name="settings[basel][basel_promo][<?php echo $language['language_id']; ?>]" value="<?php echo isset($basel_promo[$language['language_id']]) ? $basel_promo[$language['language_id']] : ''; ?>" />
    </div>
    <?php } ?>
    </div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="If available in your header alternative">Secondary Promo Message</span></label>
    <div class="col-sm-10">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon">
    <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['language_id']; ?>" /></span>
    <input class="form-control" name="settings[basel][basel_promo2][<?php echo $language['language_id']; ?>]" value="<?php echo isset($basel_promo2[$language['language_id']]) ? $basel_promo2[$language['language_id']] : ''; ?>" />
    </div>
    <?php } ?>
    </div>
</div>


<legend class="sub">General</legend>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable to keep the header floating on your home page, with a transparent background">Homepage Overlay Header</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_home_overlay_header]" value="0" <?php if($basel_home_overlay_header == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_home_overlay_header]" value="1" <?php if($basel_home_overlay_header == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Login / Register Status</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][header_login]"  value="0" <?php if($header_login == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][header_login]"  value="1" <?php if($header_login == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Header Search Status</label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][header_search]"  value="0" <?php if($header_search == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][header_search]"  value="1" <?php if($header_search == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>
</div>

<legend>Menu Management</legend>
<div class="col-sm-offset-2">
<div class="bs-callout bs-callout-info bs-callout-sm">
<h4>How to manage menus?</h4>
<p>Please see the sketch at the header selection to see where menus are located.</p>
<p>As <b>Primary Menu</b> and <b>Secondary Menu</b> you can attach either the <i>Default menu from Opencart</i>, or a menu created using the <i>Basel Megamenu Module.</i></p>
<p>Default menu from Opencart = <i>All categories from Catalog > Categories where the "Top" checkbox is checked</i><br />
Mega Menu Module = <i>Menus created from the module Basel Megamenu under Extensions > Extensions -> Modules.</i></p>
<p><b>On mobile devices</b>, links from <i>Primary Menu</i>, <i>Secondary Menu</i> and <i>Static links</i> will be listed</p>
</div>
</div>
<legend class="sub">Main Menus</legend>
<!-- PRIMARY MENU -->
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Select which menu to be used as the primary menu">Primary Menu</span></label>
    <div class="col-sm-10">
        <select name="settings[basel][primary_menu]" class="form-control">
        	<?php if ($primary_menu == '0') { ?>
            <option value="0" selected="selected">None</option>
            <?php } else { ?>
            <option value="0">None</option>
            <?php } ?>
            <?php if ($primary_menu == 'oc') { ?>
            <option value="oc" selected="selected">Default Opencart Category Menu</option>
            <?php } else { ?>
            <option value="oc">Default Opencart Category Menu</option>
            <?php } ?>
        <?php foreach ($menu_modules as $menu_module) { ?>
            <?php if ($primary_menu == $menu_module['module_id']) { ?>
            <option value="<?php echo $menu_module['module_id']; ?>" selected="selected">Basel Megamenu: <?php echo $menu_module['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $menu_module['module_id']; ?>">Basel Megamenu: <?php echo $menu_module['name']; ?></option>
            <?php } ?>
        <?php } ?>
        </select>
    </div>                   
</div>

<!-- SECONDARY MENU -->
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Select which menu to be used as the secondary menu. (Only available on some header alternatives)">Seconday Menu</span></label>
    <div class="col-sm-10">
        <select name="settings[basel][secondary_menu]" class="form-control">
        	<?php if ($secondary_menu == '0') { ?>
            <option value="0" selected="selected">None</option>
            <?php } else { ?>
            <option value="0">None</option>
            <?php } ?>
            <?php if ($secondary_menu == 'oc') { ?>
            <option value="oc" selected="selected">Default Opencart Category Menu</option>
            <?php } else { ?>
            <option value="oc">Default Opencart Category Menu</option>
            <?php } ?>
        <?php foreach ($menu_modules as $menu_module) { ?>
            <?php if ($secondary_menu == $menu_module['module_id']) { ?>
            <option value="<?php echo $menu_module['module_id']; ?>" selected="selected">Basel Megamenu: <?php echo $menu_module['name']; ?></option>
            <?php } else { ?>
            <option value="<?php echo $menu_module['module_id']; ?>">Basel Megamenu: <?php echo $menu_module['name']; ?></option>
            <?php } ?>
        <?php } ?>
        </select>
    </div>                   
</div>

<legend class="sub">Header Static Links</legend>
<!-- STATIC LINKS -->
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="If disabled, the default links from opencart will be used">Override Static Links</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][use_custom_links]" class="links-select" value="0" <?php if($use_custom_links == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][use_custom_links]" class="links-select" value="1" <?php if($use_custom_links == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="row" id="custom_links_holder"<?php if($use_custom_links){echo ' style="display:block"';} else {echo ' style="display:none"';} ?>>
<div class="col-sm-2"></div>
<div class="col-sm-10">
 <table id="links" class="table table-clean">
    <thead>
      <tr>
        <td width="48%">Text</td>
        <td width="48%"><span data-toggle="tooltip" title="Include http:// when linking to external targets">Link Target</span></td>
        <td width="4%">Sort Order</td>
      </tr>
    </thead>
    <tbody>
     <?php $link_row = 1; ?>
     <?php if (!empty($basel_links)) { ?>
      
      <?php foreach ($basel_links as $basel_link) { ?>
      <tr id="link-row<?php echo $link_row; ?>">
        <td class="first">
        <?php foreach ($languages as $language) { ?>
        <div class="input-group">
        <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['language_id']; ?>" /></span>
        <input type="text" class="form-control" name="settings[basel][basel_links][<?php echo $link_row; ?>][text][<?php echo $language['language_id']; ?>]" value="<?php echo isset($basel_link['text'][$language['language_id']]) ? $basel_link['text'][$language['language_id']] : ''; ?>" size="40" />
        </div>
        <?php } ?>
        </td>
        <td class="first">
        <input type="text" class="form-control" name="settings[basel][basel_links][<?php echo $link_row; ?>][target]" value="<?php echo isset($basel_link['target']) ? $basel_link['target'] : ''; ?>" />
        </td>
        <td class="first">
        <input type="text" class="form-control" style="width:60px" name="settings[basel][basel_links][<?php echo $link_row; ?>][sort]" value="<?php echo isset($basel_link['sort']) ? $basel_link['sort'] : '0'; ?>" />
        </td>
        <td class="text-right">
        <button type="button" onclick="$('#link-row<?php echo $link_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
        </td>
      </tr>
      <?php $link_row++; ?>
    <?php } ?>
    <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3"></td>
        <td class="text-right"><button type="button" onclick="addLinkRow();" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
      </tr>
    </tfoot>
  </table>
 </div>
</div> <!-- row ends -->
          

<script type="text/javascript"><!--
var link_row = <?php echo $link_row; ?>;
function addLinkRow() {
	html  = '<tr id="link-row' + link_row + '">';
	html += '<td class="first">';
	<?php foreach ($languages as $language) { ?>
	html += '<div class="input-group">';
	html += '<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>';
	html += '<input type="text" class="form-control" name="settings[basel][basel_links][' + link_row + '][text][<?php echo $language['language_id']; ?>]" />';
	html += '</div>';
	<?php } ?>
	html += '</td>';
	html += '<td class="first">';
	html += '<input type="text" class="form-control" name="settings[basel][basel_links][' + link_row + '][target]" />';
	html += '</td>';
	html += '<td class="first">';
	html += '<input type="text" class="form-control" style="width:60px" value="0" name="settings[basel][basel_links][' + link_row + '][sort]" />';
	html += '</td>';
	html += '<td class="text-right"><button type="button" onclick="$(\'#link-row' + link_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	$('#links tbody').append(html);
	link_row++;
}
//--></script>