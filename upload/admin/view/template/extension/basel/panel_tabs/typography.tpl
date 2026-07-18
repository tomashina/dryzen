<legend>Typography</legend>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use custom fonts instead of the default ones">Override default font styles</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][basel_typo_status]" class="typo-select" value="0" <?php if($basel_typo_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][basel_typo_status]" class="typo-select" value="1" <?php if($basel_typo_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div id="basel_typo_holder"<?php if($basel_typo_status){echo ' style="display:block"';} else {echo ' style="display:none"';} ?>>
<legend class="sub">Google Font List</legend>


<div class="col-sm-offset-2" style="margin-bottom:10px;">
<div class="bs-callout bs-callout-info bs-callout-sm">
When you add new fonts to the list, please save the module before you can start using them in the font settings
</div>
</div>
<div class="row">
<div class="col-sm-2"></div>
<div class="col-sm-10">
 <table id="fonts" class="table table-clean">
    <thead>
      <tr>
        <td width="49%"><span data-toggle="tooltip" title="For example: Roboto:400,600">Font Import</span></td>
        <td width="49%"><span data-toggle="tooltip" title="For example: 'Roboto', sans-serif">Font Name</span></td>
        <td width="2%"></td>
      </tr>
    </thead>
    <tbody>
     <?php $font_row = 1; ?>
     <?php if (isset($basel_fonts)) { ?>
      <?php foreach ($basel_fonts as $basel_font) { ?>
      <tr id="font-row<?php echo $font_row; ?>">
        <td class="first">
        <div style="margin-bottom:5px;">
        <input type="text" class="form-control" name="settings[basel][basel_fonts][<?php echo $font_row; ?>][import]" value="<?php echo isset($basel_font['import']) ? $basel_font['import'] : ''; ?>" />
        </div>
        </td>
        <td class="first">
        <input type="text" class="form-control" name="settings[basel][basel_fonts][<?php echo $font_row; ?>][name]" value="<?php echo isset($basel_font['name']) ? $basel_font['name'] : ''; ?>" />
        </td>
        <td class="text-right">
        <button type="button" onclick="$('#font-row<?php echo $font_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
        </td>
      </tr>
      <?php $font_row++; ?>
    <?php } ?>
   <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2"></td>
        <td class="text-right"><button type="button" onclick="addFontRow();" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
      </tr>
    </tfoot>
  </table>
 </div>
 </div>
 
<legend>Font settings</legend>
<legend class="sub">General</legend>

<div class="form-group">
<label class="col-sm-2 control-label">Body font</label>
<div class="col-sm-10">
    <select name="settings[basel][body_font_fam]" class="form-control">
    <option disabled style="font-weight:bold">System Fonts</option>
    <?php foreach ($system_fonts as $key => $system_font) { ?>
    <option value="<?php echo $key; ?>" 
    <?php echo ($body_font_fam == $key) ? 'selected="selected"' : ''; ?>>
    <?php echo $system_font; ?></option>
    <?php } ?>
    <option disabled style="font-weight:bold">Google Webfonts</option>
    <?php if (isset($basel_fonts)) { ?>
    <?php foreach ($basel_fonts as $basel_font) { ?>
    <option value="<?php echo $basel_font['name']; ?>" <?php if ($body_font_fam == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
    <?php } ?>
    <?php } ?>
    </select>
</div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Disable if your custom font dont support, or looks bad in italic">Keep italics</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][body_font_italic_status]" value="0" <?php if($body_font_italic_status == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][body_font_italic_status]"  value="1" <?php if($body_font_italic_status == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Select default font weight for bold text">Bold font weight</span></label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][body_font_bold_weight]" value="<?php echo isset($body_font_bold_weight) ? $body_font_bold_weight : '600'; ?>" />
    </div>                   
</div>

<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Contrast font is used on item names, in some widgets etc.">Contrast font</span></label>
<div class="col-sm-10">
    <select name="settings[basel][contrast_font_fam]" class="form-control">
    <option disabled style="font-weight:bold">System Fonts</option>
    <?php foreach ($system_fonts as $key => $system_font) { ?>
    <option value="<?php echo $key; ?>" 
    <?php echo ($contrast_font_fam == $key) ? 'selected="selected"' : ''; ?>>
    <?php echo $system_font; ?></option>
    <?php } ?>
    <option disabled style="font-weight:bold">Google Webfonts</option>
    <?php if (isset($basel_fonts)) { ?>
    <?php foreach ($basel_fonts as $basel_font) { ?>
    <option value="<?php echo $basel_font['name']; ?>" <?php if ($contrast_font_fam == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
    <?php } ?>
    <?php } ?>
    </select>
</div>                   
</div>

<legend class="sub">Font size adjustment</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use this to adjust if your font looks bigger or smaller than desirably">Larger font size (16px)</span></label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][body_font_size_16]" value="<?php echo isset($body_font_size_16) ? $body_font_size_16 : '16px'; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use this to adjust if your font looks bigger or smaller than desirably">Large font size (15px)</span></label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][body_font_size_15]" value="<?php echo isset($body_font_size_15) ? $body_font_size_15 : '15px'; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use this to adjust if your font looks bigger or smaller than desirably">Normal font size (14px)</span></label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][body_font_size_14]" value="<?php echo isset($body_font_size_14) ? $body_font_size_14 : '14px'; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use this to adjust if your font looks bigger or smaller than desirably">Small font size (13px)</span></label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][body_font_size_13]" value="<?php echo isset($body_font_size_13) ? $body_font_size_13 : '13px'; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use this to adjust if your font looks bigger or smaller than desirably">Tiny font size (12px)</span></label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][body_font_size_12]" value="<?php echo isset($body_font_size_12) ? $body_font_size_12 : '12px'; ?>" />
    </div>                   
</div>

<legend class="sub">Headings</legend>

<div class="form-group">
<label class="col-sm-2 control-label">Headings font</label>
<div class="col-sm-10">
    <select name="settings[basel][headings_fam]" class="form-control">
    <option disabled style="font-weight:bold">System Fonts</option>
    <?php foreach ($system_fonts as $key => $system_font) { ?>
    <option value="<?php echo $key; ?>" 
    <?php echo ($headings_fam == $key) ? 'selected="selected"' : ''; ?>>
    <?php echo $system_font; ?></option>
    <?php } ?>
    <option disabled style="font-weight:bold">Google Webfonts</option>
    <?php if (isset($basel_fonts)) { ?>
    <?php foreach ($basel_fonts as $basel_font) { ?>
    <option value="<?php echo $basel_font['name']; ?>" <?php if ($headings_fam == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
    <?php } ?>
    <?php } ?>
    </select>
</div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Headings font weight</label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][headings_weight]" value="<?php echo isset($headings_weight) ? $headings_weight : '600'; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Smaller headings size</label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][headings_size_sm]" value="<?php echo isset($headings_size_sm) ? $headings_size_sm : '20px'; ?>" />
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label">Largeer headings size</label>
    <div class="col-sm-10 form-inline">
    <input class="form-control" style="width:160px" name="settings[basel][headings_size_lg]" value="<?php echo isset($headings_size_lg) ? $headings_size_lg : '28px'; ?>" />
    </div>                   
</div>

<legend class="sub">Page titles / H1</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><br /><span data-toggle="tooltip" title="Inline titles are page titles that are not moved to the breadcrumb">Inline titles</span></label>
    <div class="col-sm-10">
    <div class="row">
        <div class="col-sm-4">
        <label>Font family</label>
        <select name="settings[basel][h1_inline_fam]" class="form-control">
        <option disabled style="font-weight:bold">System Fonts</option>
        <?php foreach ($system_fonts as $key => $system_font) { ?>
        <option value="<?php echo $key; ?>" 
        <?php echo ($h1_inline_fam == $key) ? 'selected="selected"' : ''; ?>>
        <?php echo $system_font; ?></option>
        <?php } ?>
        <option disabled style="font-weight:bold">Google Webfonts</option>
        <?php if (isset($basel_fonts)) { ?>
        <?php foreach ($basel_fonts as $basel_font) { ?>
        <option value="<?php echo $basel_font['name']; ?>" <?php if ($h1_inline_fam == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
        <?php } ?>
        <?php } ?>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Font size</label>
        <input class="form-control" name="settings[basel][h1_inline_size]" value="<?php echo isset($h1_inline_size) ? $h1_inline_size : '34px'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Font weight</label>
        <input class="form-control" name="settings[basel][h1_inline_weight]" value="<?php echo isset($h1_inline_weight) ? $h1_inline_weight : '600'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Text transform</label>
        <select name="settings[basel][h1_inline_trans]" class="form-control">
        <option value="none"<?php if($h1_inline_trans == 'none'){echo ' selected="selected"';} ?>>None</option>
        <option value="uppercase"<?php if($h1_inline_trans == 'uppercase'){echo ' selected="selected"';} ?>>Uppercase</option>
        <option value="lowercase"<?php if($h1_inline_trans == 'lowercase'){echo ' selected="selected"';} ?>>Lowercase</option>
        <option value="capitalize"<?php if($h1_inline_trans == 'capitalize'){echo ' selected="selected"';} ?>>Capitalize</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Letter spcaing</label>
        <input class="form-control" name="settings[basel][h1_inline_ls]" value="<?php echo isset($h1_inline_ls) ? $h1_inline_ls : '0px'; ?>" />
        </div>
    </div>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><br />Titles inside breadcrumb</label>
    <div class="col-sm-10">
    <div class="row">
        <div class="col-sm-4">
        <label>Font family</label>
        <select name="settings[basel][h1_breadcrumb_fam]" class="form-control">
        <option disabled style="font-weight:bold">System Fonts</option>
        <?php foreach ($system_fonts as $key => $system_font) { ?>
        <option value="<?php echo $key; ?>" 
        <?php echo ($h1_breadcrumb_fam == $key) ? 'selected="selected"' : ''; ?>>
        <?php echo $system_font; ?></option>
        <?php } ?>
        <option disabled style="font-weight:bold">Google Webfonts</option>
        <?php if (isset($basel_fonts)) { ?>
        <?php foreach ($basel_fonts as $basel_font) { ?>
        <option value="<?php echo $basel_font['name']; ?>" <?php if ($h1_breadcrumb_fam == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
        <?php } ?>
        <?php } ?>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Font size</label>
        <input class="form-control" name="settings[basel][h1_breadcrumb_size]" value="<?php echo isset($h1_breadcrumb_size) ? $h1_breadcrumb_size : '34px'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Font weight</label>
        <input class="form-control" name="settings[basel][h1_breadcrumb_weight]" value="<?php echo isset($h1_breadcrumb_weight) ? $h1_breadcrumb_weight : '600'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Text transform</label>
        <select name="settings[basel][h1_breadcrumb_trans]" class="form-control">
        <option value="none"<?php if($h1_breadcrumb_trans == 'none'){echo ' selected="selected"';} ?>>None</option>
        <option value="uppercase"<?php if($h1_breadcrumb_trans == 'uppercase'){echo ' selected="selected"';} ?>>Uppercase</option>
        <option value="lowercase"<?php if($h1_breadcrumb_trans == 'lowercase'){echo ' selected="selected"';} ?>>Lowercase</option>
        <option value="capitalize"<?php if($h1_breadcrumb_trans == 'capitalize'){echo ' selected="selected"';} ?>>Capitalize</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Letter spcaing</label>
        <input class="form-control" name="settings[basel][h1_breadcrumb_ls]" value="<?php echo isset($h1_breadcrumb_ls) ? $h1_breadcrumb_ls : '0px'; ?>" />
        </div>
    </div>
    </div>                   
</div>

<legend class="sub">Widgets Titles / Module Titles</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><br /><span data-toggle="tooltip" title="Widgets not positioned in column areas">Widgets in content</span></label>
    <div class="col-sm-10">
    <div class="row">
        <div class="col-sm-4">
        <label>Font family</label>
        <select name="settings[basel][widget_lg_fam]" class="form-control">
        <option disabled style="font-weight:bold">System Fonts</option>
        <?php foreach ($system_fonts as $key => $system_font) { ?>
        <option value="<?php echo $key; ?>" 
        <?php echo ($widget_lg_fam == $key) ? 'selected="selected"' : ''; ?>>
        <?php echo $system_font; ?></option>
        <?php } ?>
        <option disabled style="font-weight:bold">Google Webfonts</option>
        <?php if (isset($basel_fonts)) { ?>
        <?php foreach ($basel_fonts as $basel_font) { ?>
        <option value="<?php echo $basel_font['name']; ?>" <?php if ($widget_lg_fam == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
        <?php } ?>
        <?php } ?>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Font size</label>
        <input class="form-control" name="settings[basel][widget_lg_size]" value="<?php echo isset($widget_lg_size) ? $widget_lg_size : '26px'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Font weight</label>
        <input class="form-control" name="settings[basel][widget_lg_weight]" value="<?php echo isset($widget_lg_weight) ? $widget_lg_weight : '600'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Text transform</label>
        <select name="settings[basel][widget_lg_trans]" class="form-control">
        <option value="none"<?php if($widget_lg_trans == 'none'){echo ' selected="selected"';} ?>>None</option>
        <option value="uppercase"<?php if($widget_lg_trans == 'uppercase'){echo ' selected="selected"';} ?>>Uppercase</option>
        <option value="lowercase"<?php if($widget_lg_trans == 'lowercase'){echo ' selected="selected"';} ?>>Lowercase</option>
        <option value="capitalize"<?php if($widget_lg_trans == 'capitalize'){echo ' selected="selected"';} ?>>Capitalize</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Letter spcaing</label>
        <input class="form-control" name="settings[basel][widget_lg_ls]" value="<?php echo isset($widget_lg_ls) ? $widget_lg_ls : '0px'; ?>" />
        </div>
    </div>
    </div>                   
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><br />Widgets in column</label>
    <div class="col-sm-10">
    <div class="row">
        <div class="col-sm-4">
        <label>Font family</label>
        <select name="settings[basel][widget_sm_fam]" class="form-control">
        <option disabled style="font-weight:bold">System Fonts</option>
        <?php foreach ($system_fonts as $key => $system_font) { ?>
        <option value="<?php echo $key; ?>" 
        <?php echo ($widget_sm_fam == $key) ? 'selected="selected"' : ''; ?>>
        <?php echo $system_font; ?></option>
        <?php } ?>
        <option disabled style="font-weight:bold">Google Webfonts</option>
        <?php if (isset($basel_fonts)) { ?>
        <?php foreach ($basel_fonts as $basel_font) { ?>
        <option value="<?php echo $basel_font['name']; ?>" <?php if ($widget_sm_fam == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
        <?php } ?>
        <?php } ?>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Font size</label>
        <input class="form-control" name="settings[basel][widget_sm_size]" value="<?php echo isset($widget_sm_size) ? $widget_sm_size : '16px'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Font weight</label>
        <input class="form-control" name="settings[basel][widget_sm_weight]" value="<?php echo isset($widget_sm_weight) ? $widget_sm_weight : '600'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Text transform</label>
        <select name="settings[basel][widget_sm_trans]" class="form-control">
        <option value="none"<?php if($widget_sm_trans == 'none'){echo ' selected="selected"';} ?>>None</option>
        <option value="uppercase"<?php if($widget_sm_trans == 'uppercase'){echo ' selected="selected"';} ?>>Uppercase</option>
        <option value="lowercase"<?php if($widget_sm_trans == 'lowercase'){echo ' selected="selected"';} ?>>Lowercase</option>
        <option value="capitalize"<?php if($widget_sm_trans == 'capitalize'){echo ' selected="selected"';} ?>>Capitalize</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Letter spcaing</label>
        <input class="form-control" name="settings[basel][widget_sm_ls]" value="<?php echo isset($widget_sm_ls) ? $widget_sm_ls : '0.75px'; ?>" />
        </div>
    </div>
    </div>                   
</div>

<legend class="sub">Main Menu In Header</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><br />Main menu</label>
    <div class="col-sm-10">
    <div class="row">
        <div class="col-sm-4">
        <label>Font family</label>
        <select name="settings[basel][menu_font_fam]" class="form-control">
        <option disabled style="font-weight:bold">System Fonts</option>
        <?php foreach ($system_fonts as $key => $system_font) { ?>
        <option value="<?php echo $key; ?>" 
        <?php echo ($menu_font_fam == $key) ? 'selected="selected"' : ''; ?>>
        <?php echo $system_font; ?></option>
        <?php } ?>
        <option disabled style="font-weight:bold">Google Webfonts</option>
        <?php if (isset($basel_fonts)) { ?>
        <?php foreach ($basel_fonts as $basel_font) { ?>
        <option value="<?php echo $basel_font['name']; ?>" <?php if ($menu_font_fam == $basel_font['name']) { echo 'selected="selected"'; } ?>><?php echo $basel_font['name']; ?></option>
        <?php } ?>
        <?php } ?>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Font size</label>
        <input class="form-control" name="settings[basel][menu_font_size]" value="<?php echo isset($menu_font_size) ? $menu_font_size : '14px'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Font weight</label>
        <input class="form-control" name="settings[basel][menu_font_weight]" value="<?php echo isset($menu_font_weight) ? $menu_font_weight : '400'; ?>" />
        </div>
        <div class="col-sm-2">
        <label>Text transform</label>
        <select name="settings[basel][menu_font_trans]" class="form-control">
        <option value="none"<?php if($menu_font_trans == 'none'){echo ' selected="selected"';} ?>>None</option>
        <option value="uppercase"<?php if($menu_font_trans == 'uppercase'){echo ' selected="selected"';} ?>>Uppercase</option>
        <option value="lowercase"<?php if($menu_font_trans == 'lowercase'){echo ' selected="selected"';} ?>>Lowercase</option>
        <option value="capitalize"<?php if($menu_font_trans == 'capitalize'){echo ' selected="selected"';} ?>>Capitalize</option>
        </select>
        </div>
        <div class="col-sm-2">
        <label>Letter spcaing</label>
        <input class="form-control" name="settings[basel][menu_font_ls]" value="<?php echo isset($menu_font_ls) ? $menu_font_ls : '0.5px'; ?>" />
        </div>
    </div>
    </div>                   
</div> 

</div><!-- #basel_typo_holder ends -->


<script type="text/javascript"><!--
var font_row = <?php echo $font_row; ?>;
function addFontRow() {
	html  = '<tr id="font-row' + font_row + '">';
	html += '<td class="first">';
	html += '<input type="text" class="form-control" name="settings[basel][basel_fonts][' + font_row + '][import]" />';
	html += '</td>';
	html += '<td class="first">';
	html += '<input type="text" class="form-control" name="settings[basel][basel_fonts][' + font_row + '][name]" />';
	html += '</td>';
	html += '<td class="text-right"><button type="button" onclick="$(\'#font-row' + font_row  + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';
	$('#fonts tbody').append(html);
	font_row++;
}
//--></script>