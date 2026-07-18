<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">  
    
      <div class="pull-right">
	<button type="submit" form="form-basel" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary">Save</button>
    <a class="btn btn-default" href="http://basel.openthemer.com/help" target="_blank" data-toggle="tooltip" title="View Theme Documentation">Documentation</a>
    </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  
  <div class="container-fluid">    
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_permission; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
  
   <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-basel" class="form-horizontal">
   
   <div class="panel-wrapper">
   
   <div class="panel-left">
   <div class="setting-header">v 1.3.1.0</div>
   <ul class="menu list-unstyled">
   <li class="active"><a href="#tab-status" data-toggle="tab"><i class="fa fa-toggle-on"></i> Status</a></li>
   <li><a href="#tab-shop" data-toggle="tab"><i class="fa fa-shopping-cart"></i> Shop Settings</a></li>
   <li><a href="#tab-header" data-toggle="tab"><i class="fa fa-columns"></i> Header</a></li>
   <li><a href="#tab-page-titles" data-toggle="tab"><i class="fa fa-chain"></i> Breadcrumbs</a></li>
   <li><a href="#tab-product-pages" data-toggle="tab"><i class="fa fa-clone"></i> Product Pages</a></li>
   <li><a href="#tab-category-pages" data-toggle="tab"><i class="fa fa-th"></i> Category Pages</a></li>
   <li><a href="#tab-contact-page" data-toggle="tab"><i class="fa fa-envelope-o"></i> Contact Page</a></li>
   <li><a href="#tab-footer" data-toggle="tab"><i class="fa fa-gears"></i> Footer</a></li>
   <li><a href="#tab-notifications" data-toggle="tab"><i class="fa fa-comment-o"></i> Notifications</a></li>
   <li><a href="#tab-styles" data-toggle="tab"><i class="fa fa-paint-brush"></i> Design & Colors</a></li>
   <li><a href="#tab-typography" data-toggle="tab"><i class="fa fa-font"></i> Typography</a></li>
   <li><a href="#tab-image-sizes" data-toggle="tab"><i class="fa fa-image"></i> Image Sizes</a></li>
   <li><a href="#tab-custom-css" data-toggle="tab"><i class="fa fa-css3"></i> Custom CSS</a></li>
   <li><a href="#tab-custom-javascript" data-toggle="tab"><i class="fa fa-code"></i> Custom Javascript</a></li>
   <li><a href="#tab-one-click-installer" data-toggle="tab"><i class="fa fa-magic"></i> One Click Installer</a></li>
   
   </ul>
   </div><!-- .panel-left ends -->
   
   <div class="panel-right">
   <div class="store-header">
   <div class="form-group">
    <label class="col-sm-9 control-label">Store to edit:</label>
        <div class="col-sm-3">
            <select name="store_id" class="form-control">
                <?php foreach($stores as $result) { ?>
                    <?php if($result['store_id'] == $store_id) { ?>
                        <option value="<?php echo $result['store_id']; ?>" selected="selected">
                            <?php echo $result['name']; ?>
                        </option>
                    <?php } else { ?>
                        <option value="<?php echo $result['store_id']; ?>">
                            <?php echo $result['name']; ?>
                        </option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>
    </div>
   </div>
   
   
   
   <div class="main-content">
   
   <div class="tab-content">
   
       <div class="tab-pane active" id="tab-status">
       <?php require_once('panel_tabs/status.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-shop">
       <?php require_once('panel_tabs/shop.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-header">
       <?php require_once('panel_tabs/header.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-page-titles">
       <?php require_once('panel_tabs/page-titles.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-product-pages">
       <?php require_once('panel_tabs/product-pages.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-category-pages">
       <?php require_once('panel_tabs/category-pages.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-contact-page">
       <?php require_once('panel_tabs/contact-page.tpl'); ?>
       </div>

       <div class="tab-pane" id="tab-footer">
       <?php require_once('panel_tabs/footer.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-notifications">
       <?php require_once('panel_tabs/notifications.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-styles">
       <?php require_once('panel_tabs/styles.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-typography">
       <?php require_once('panel_tabs/typography.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-image-sizes">
       <?php require_once('panel_tabs/image-sizes.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-custom-css">
       <?php require_once('panel_tabs/custom-css.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-custom-javascript">
       <?php require_once('panel_tabs/custom-javascript.tpl'); ?>
       </div>
       
       <div class="tab-pane" id="tab-one-click-installer">
       <?php require_once('panel_tabs/one-click-installer.tpl'); ?>
       </div>
       
    </div>
   
   </div> <!-- .main-content ends -->
    
  </div> <!-- panel-right ends -->
     
 </div>        
</form>
</div> <!-- content ends -->


<script type="text/javascript"><!--
// Store selector
$('select[name=\'store_id\']').on('change', function () {
    location = 'index.php?route=extension/basel/basel&token=<?php echo $token; ?>&store_id=' +  encodeURIComponent($(this).val());
});
//--></script>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<script type="text/javascript">
// Header preview image
$('#header-select').on('change', function() {
$('#header-preview img').attr('src', 'view/javascript/basel/img/theme-panel/headers/' + $(this).val() + '.png');
});
// Always open first tab
$('.nav-tabs').each(function() {
	$(this).find('li:first a').tab('show');
});
// Static links on/off
$('.links-select').on('change', function() {
if ($(this).val() == '1') {
	$('#custom_links_holder').css('display', 'block');
} else {
	$('#custom_links_holder').css('display', 'none');
}
});
// Custom design on/off
$('.design-select').on('change', function() {
if ($(this).val() == '1') {
	$('#custom_design_holder').css('display', 'block');
} else {
	$('#custom_design_holder').css('display', 'none');
}
});
// Custom fonts on/off
$('.typo-select').on('change', function() {
if ($(this).val() == '1') {
	$('#basel_typo_holder').css('display', 'block');
} else {
	$('#basel_typo_holder').css('display', 'none');
}
});
// Custom footer links on/off
$('.footer-custom-links-select').on('change', function() {
if ($(this).val() == '1') {
	$('#custom_footer_links_holder').css('display', 'block');
} else {
	$('#custom_footer_links_holder').css('display', 'none');
}
});
// Custom CSS on/off
$('.custom-css-select').on('change', function() {
if ($(this).val() == '1') {
	$('#custom_css_holder').css('display', 'block');
} else {
	$('#custom_css_holder').css('display', 'none');
}
});
// Custom Javascript on/off
$('.custom-js-select').on('change', function() {
if ($(this).val() == '1') {
	$('#custom_js_holder').css('display', 'block');
} else {
	$('#custom_js_holder').css('display', 'none');
}
});
// Colorpicker 
$('.colorfield input').colorpicker({
sliders: {
saturation: {maxLeft: 150, maxTop: 150},hue: { maxTop: 150},alpha: { maxTop: 150}}
}).on('changeColor.colorpicker', function(){
$(this).parent().find('.input-group-addon i').css("background-color", $(this).val());
});
var enable_editor = function(area, lang_id) {
	if ( $('#enable-editor-' + area + '-' + lang_id + '').hasClass('active') ) {
		$('#enable-editor-' + area + '-' + lang_id + '').text('Enable HTML editor').removeClass('active');
		$('#editor-textarea-' + area + '-' + lang_id + '').summernote('destroy');
	} else {
		$('#enable-editor-' + area + '-' + lang_id + '').text('Disable HTML editor').addClass('active');
		$('#editor-textarea-' + area + '-' + lang_id + '').summernote({
			disableDragAndDrop: true,
			styleWithSpan: false,
			height: 260,
			fontSizes: ['12', '13', '14', '16', '18', '20', '22', '24', '26','28','30','32', '34', '48' , '64', '72'],
			toolbar: [
				['style', ['style']],
				['font', [ "underline","italic", "bold", "clear"]],
				['fontsize', ['fontsize']],
				['color', ['color']],
				['para', ['paragraph']],
				['insert', ['link', 'image']],
				['view', ['fullscreen', 'codeview']]
			],
			buttons: {
    			image: function() {
					var ui = $.summernote.ui;
					// create button
					var button = ui.button({
						contents: '<i class="fa fa-image" />',
						tooltip: $.summernote.lang[$.summernote.options.lang].image.image,
						click: function () {
							$('#modal-image').remove();
							$.ajax({
								url: 'index.php?route=common/filemanager&token=' + getURLVar('token'),
								dataType: 'html',
								beforeSend: function() {
									$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
									$('#button-image').prop('disabled', true);
								},
								complete: function() {
									$('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
									$('#button-image').prop('disabled', false);
								},
								success: function(html) {
									$('body').append('<div id="modal-image" class="modal">' + html + '</div>');
									
									$('#modal-image').modal('show');
									
									$('#modal-image').delegate('a.thumbnail', 'click', function(e) {
										e.preventDefault();
										
										$('#editor-textarea-' + area + '-' + lang_id + '').summernote('insertImage', $(this).attr('href'));
																	
										$('#modal-image').modal('hide');
									});
								}});}});
				return button.render();
		}}});
	}
}
</script>
</div>
<?php echo $footer; ?>