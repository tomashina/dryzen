<legend>Footer</legend>

<legend class="sub">Footer Custom Block (top)</legend>
<ul class="nav nav-tabs language-tabs">
<?php foreach ($languages as $language) { ?>
<li><a href="#footer-block1<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
<?php } ?>
</ul>
<div class="tab-content">
<?php foreach ($languages as $language) { ?>
<div class="tab-pane" id="footer-block1<?php echo $language['language_id']; ?>">
  <div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Custom block which will take place above the footer links">Footer custom block (top)</span><br /><a class="editor-toggle" id="enable-editor-footer1-<?php echo $language['language_id']; ?>" onclick="enable_editor('footer1','<?php echo $language['language_id']; ?>')">Enable HTML editor</a></label>
    <div class="col-sm-10">
      <textarea id="editor-textarea-footer1-<?php echo $language['language_id']; ?>" name="settings[basel][footer_block_1][<?php echo $language['language_id']; ?>]" class="form-control code"><?php echo isset($footer_block_1[$language['language_id']]) ? $footer_block_1[$language['language_id']] : ''; ?></textarea>
    </div>
  </div>
 </div>
 <?php } ?>
</div>

          
<legend class="sub">Footer Links</legend>
<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Enable to use custom footer links instead of the default ones in Opencart">Overwrite default links</span></label>
    <div class="col-sm-10 toggle-btn">
    <label><input type="radio" name="settings[basel][overwrite_footer_links]" class="footer-custom-links-select" value="0" <?php if($overwrite_footer_links == '0'){echo ' checked="checked"';} ?> /><span>Disabled</span></label>
    <label><input type="radio" name="settings[basel][overwrite_footer_links]" class="footer-custom-links-select" value="1" <?php if($overwrite_footer_links == '1'){echo ' checked="checked"';} ?> /><span>Enabled</span></label>
    </div>                   
</div>


<div id="custom_footer_links_holder"<?php if($overwrite_footer_links){echo ' style="display:block"';} else {echo ' style="display:none"';} ?>>
<div class="form-group">
<label class="col-sm-2 control-label"></label>
<div class="col-sm-10">
<ul class="nav content-tabs" id="footer-column-tabs">
    <?php $footer_column_row = 1; ?>    
    <?php foreach ($basel_footer_columns as $basel_footer_column) { ?>
    <li><a href="#footer-column-<?php echo $footer_column_row; ?>" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$('a[href=\'#footer-column-<?php echo $footer_column_row; ?>\']').parent().remove(); $('#footer-column-<?php echo $footer_column_row; ?>').remove(); $('#footer-column-tabs a:first').tab('show');"></i> Column <?php echo $footer_column_row; ?></a></li>
    <?php $footer_column_row++; ?>
    <?php } ?>
    <li id="column-add" style="cursor:pointer;"><a onclick="addFooterColumn();"><i class="fa fa-plus-circle"></i> Add Column</a></li> 
</ul>
<div class="tab-content" id="footer-columns-holder">
<?php $footer_column_row = 1; ?>
<?php if (!empty($basel_footer_columns)) { ?>

<?php foreach ($basel_footer_columns as $basel_footer_column) { ?>
<div class="tab-pane" id="footer-column-<?php echo $footer_column_row; ?>">
<div class="tab-content">
<table class="table table-clean table-footer-column">
<thead>
  <tr>
    <td width="96%"><h4>Column Heading</h4></td>
    <td width="4%">Sort Order</td>
  </tr>
</thead>
<tbody>
  <tr id="column-row<?php echo $footer_column_row; ?>">
    <td class="first">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['language_id']; ?>" /></span>
    <input type="text" class="form-control" name="settings[basel][basel_footer_columns][<?php echo $footer_column_row; ?>][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($basel_footer_column['title'][$language['language_id']]) ? $basel_footer_column['title'][$language['language_id']] : ''; ?>" />
    </div>
    <?php } ?>
    </td>
    <td class="text-right">
    <input type="text" class="form-control" style="width:60px" name="settings[basel][basel_footer_columns][<?php echo $footer_column_row; ?>][sort]" value="<?php echo isset($basel_footer_column['sort']) ? $basel_footer_column['sort'] : '1'; ?>" />
    </td>
  </tr>
</tbody>
</table>
<table id="links-holder-<?php echo $footer_column_row; ?>" class="table table-clean">
    <thead>
      <tr>
        <td width="48%"><h4>Column Links</h4></td>
        <td width="48%"><span data-toggle="tooltip" title="Include http:// when linking to external targets">Link Target</span></td>
        <td width="4%"><span style="white-space:nowrap">Sort Order</span></td>
      </tr>
    </thead>
    <tbody>
    <tr></tr>
    <?php $footer_link_row = 0; ?>
    <?php if (isset($basel_footer_column['links'])) { ?>
	<?php foreach($basel_footer_column['links'] as $link){ ?>
    <tr id="footer-link-row-<?php echo $footer_column_row; ?>-<?php echo $footer_link_row; ?>">
    <td class="first">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['language_id']; ?>" /></span>
    <input class="form-control" name="settings[basel][basel_footer_columns][<?php echo $footer_column_row; ?>][links][<?php echo $footer_link_row; ?>][title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($link['title'][$language['language_id']]) ? $link['title'][$language['language_id']] : ''; ?>" />
    </div>
    <?php } ?>
    </td>
    <td class="first">
    <input class="form-control" name="settings[basel][basel_footer_columns][<?php echo $footer_column_row; ?>][links][<?php echo $footer_link_row; ?>][target]" value="<?php echo isset($link['target']) ? $link['target'] : ''; ?>" />
    </td>
    <td class="first">
    <input type="text" class="form-control" style="width:60px" name="settings[basel][basel_footer_columns][<?php echo $footer_column_row; ?>][links][<?php echo $footer_link_row; ?>][sort]" value="<?php echo isset($link['sort']) ? $link['sort'] : '1'; ?>" />
    </td>
    <td class="text-right">
    <button type="button" onclick="$('#footer-link-row-<?php echo $footer_column_row; ?>-<?php echo $footer_link_row; ?>').remove();" class="btn btn-danger">Remove</button>
    </td>
    </tr>
<?php $footer_link_row++; ?>
<?php } ?> <!-- foreach groups ends -->
<?php } ?>
</tbody>
    <tfoot>
      <tr>
        <td colspan="3"></td>
        <td class="text-right"><button type="button" onclick="addFooterLink(<?php echo $footer_column_row; ?>);" class="btn btn-primary">Add Link</button></td>
      </tr>
    </tfoot>
  </table>
</div>
</div> <!-- #footer-columns--->
<?php $footer_column_row++; ?>
<?php } ?>
<?php } ?>
</div>  <!-- #footer-columns-holder-->
</div>                
</div>
</div>

<legend class="sub">Footer Custom Block (right)</legend>
<ul class="nav nav-tabs language-tabs">
<?php foreach ($languages as $language) { ?>
<li><a href="#footer-block2-language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
<?php } ?>
</ul>
<div class="tab-content">
<?php foreach ($languages as $language) { ?>
<div class="tab-pane" id="footer-block2-language<?php echo $language['language_id']; ?>">

<div class="form-group">
<label class="col-sm-2 control-label">Heading</label>
<div class="col-sm-10">
  <input class="form-control" name="settings[basel][footer_block_title][<?php echo $language['language_id']; ?>]" value="<?php echo isset($footer_block_title[$language['language_id']]) ? $footer_block_title[$language['language_id']] : ''; ?>" />
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Custom block which will take place as a column right by the footer links">Footer custom block (top)</span><br /><a class="editor-toggle" id="enable-editor-footer2-<?php echo $language['language_id']; ?>" onclick="enable_editor('footer2','<?php echo $language['language_id']; ?>')">Enable HTML editor</a></label>
<div class="col-sm-10">
  <textarea id="editor-textarea-footer2-<?php echo $language['language_id']; ?>" name="settings[basel][footer_block_2][<?php echo $language['language_id']; ?>]" class="form-control code"><?php echo isset($footer_block_2[$language['language_id']]) ? $footer_block_2[$language['language_id']] : ''; ?></textarea>
</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Contact detail lines will take place below the custom block">Contact details</span></label>
<div class="col-sm-10">
  <input class="form-control" name="settings[basel][footer_infoline_1][<?php echo $language['language_id']; ?>]" value="<?php echo isset($footer_infoline_1[$language['language_id']]) ? $footer_infoline_1[$language['language_id']] : ''; ?>" />
  <input class="form-control" name="settings[basel][footer_infoline_2][<?php echo $language['language_id']; ?>]" value="<?php echo isset($footer_infoline_2[$language['language_id']]) ? $footer_infoline_2[$language['language_id']] : ''; ?>" />
  <input class="form-control" name="settings[basel][footer_infoline_3][<?php echo $language['language_id']; ?>]" value="<?php echo isset($footer_infoline_3[$language['language_id']]) ? $footer_infoline_3[$language['language_id']] : ''; ?>" />
</div>
</div>
  
 </div>
 <?php } ?>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><span data-toggle="tooltip" title="If an image is uploaded, it will take place below the contact details">Payment icons</span></label>
    <div class="col-sm-10">
    <a href="" id="thumb-payment-img" data-toggle="image" class="img-thumbnail"><img src="<?php echo $payment_thumb; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a>
    <input type="hidden" name="settings[basel][basel_payment_img]" value="<?php echo $basel_payment_img; ?>" id="input-payment-img" />
    </div>                   
</div>


<legend class="sub">Footer Copyright Text</legend>
<div class="form-group">
<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="Use {year} to add the current year">Footer Copyright Text</span></label>
    <div class="col-sm-10">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
    <span class="input-group-addon">
    <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['language_id']; ?>" /></span>
    <input class="form-control" name="settings[basel][basel_copyright][<?php echo $language['language_id']; ?>]" value="<?php echo isset($basel_copyright[$language['language_id']]) ? $basel_copyright[$language['language_id']] : ''; ?>" />
    </div>
    <?php } ?>
    </div>
</div>

<script type="text/javascript"><!--
$('#footer-column-tabs a:first').tab('show');
var footer_column_row = <?php echo $footer_column_row; ?>;
function addFooterColumn() {
	html = '<div class="tab-pane" id="footer-column-' + footer_column_row + '">';
	html += '<div class="tab-content">';
	html += '<table id="footer_column-' + footer_column_row + '" class="table table-clean table-footer-column">';
	html += '<thead>';
	html += '<tr>';
	html += '<td width="96%"><h4>Column Heading</h4></td>';
	html += '<td width="4%">Sort Order</td>';
	html += '</tr>';
	html += '</thead>';
	html += '<tbody>';
   	html += '<tr id="link-row' + footer_column_row + '">';
	html += '<td class="first">';
	<?php foreach ($languages as $language) { ?>
	html += '<div class="input-group">';
	html += '<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>';
	html += '<input type="text" class="form-control" name="settings[basel][basel_footer_columns][' + footer_column_row + '][title][<?php echo $language['language_id']; ?>]" />';
	html += '</div>';
	<?php } ?>
	html += '</td>';
	html += '<td class="text-right">';
	html += '<input type="text" class="form-control" style="width:60px" value="' + footer_column_row + '" name="settings[basel][basel_footer_columns][' + footer_column_row + '][sort]" />';
	html += '</td>';
	html += '</tr>';
	html += '</tbody>';
	html += '</table>';
	html += '<table id="links-holder-' + footer_column_row  + '" class="table table-clean">';
    html += '<thead>';
	html += '<tr><td width="48%"><h4>Column Links</h4></td>';
	html += '<td width="48%"><span data-toggle="tooltip" title="Include http:// when linking to external targets">Link Target</span></td>';
	html += '<td width="4%"><span style="white-space:nowrap">Sort Order</span></td></tr>';
	html += '</thead>';
	html += '<tbody>';
	html += '<tr></tr>';
	html += '</tbody>';
	html += '<tfoot>';
	html += '<tr>';
	html += '<td colspan="3"></td>';
	html += '<td class="text-right"><button type="button" onclick="addFooterLink(' + footer_column_row  + ');" class="btn btn-primary">Add Link</button></td>';
	html += '</tr>';
	html += '</tfoot>';
	html += '</table>';
	html += '</div>';
	html += '</div>';
	$('#footer-columns-holder').append(html);
	$('#column-add').before('<li><a href="#footer-column-' + footer_column_row + '" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$(\'a[href=\\\'#footer-column-' + footer_column_row + '\\\']\').parent().remove(); $(\'#footer-column-' + footer_column_row + '\').remove(); $(\'#footer-column-tabs a:first\').tab(\'show\');"></i> Column ' + footer_column_row + '</a></li>');
	$('#footer-column-tabs a[href=\'#footer-column-' + footer_column_row + '\']').tab('show');
	footer_column_row++;
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
}
//--></script>

<script type="text/javascript"><!--
function addFooterLink(footer_column_row) {
	link_row = $('#links-holder-' + footer_column_row + ' tbody tr').length;
	html = '<tr id="footer-link-row-' + footer_column_row + '-' + link_row + '">';
	html += '<td class="first">';
	<?php foreach ($languages as $language) { ?>
	html += '<div class="input-group">';
	html += '<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>';
	html += '<input type="text" class="form-control" name="settings[basel][basel_footer_columns][' + footer_column_row + '][links][' + link_row + '][title][<?php echo $language['language_id']; ?>]" />';
	html += '</div>';
	<?php } ?>
	html += '</td>';
	html += '<td class="first">';
	html += '<input type="text" class="form-control" name="settings[basel][basel_footer_columns][' + footer_column_row + '][links][' + link_row + '][target]" />';
	html += '</td>';
	html += '<td class="first">';
	html += '<input type="text" style="width:60px" class="form-control" name="settings[basel][basel_footer_columns][' + footer_column_row + '][links][' + link_row + '][sort]" value="' + link_row + '" />';

	html += '</td>';
	html += '<td class="text-right">';
	html += '<button type="button" onclick="$(\'#footer-link-row-' + footer_column_row  + '-' + link_row + '\').remove();" class="btn btn-danger">Remove</button>';
	html += '</td>';
   	html += '</tr>';
	$('#links-holder-' + footer_column_row + ' tbody').append(html);
	link_row++;
}
//--></script>