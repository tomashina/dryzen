<legend>One Click Installer</legend>
<?php if ($theme_default_directory == 'basel' && count($stores) <= 1) { ?>
    <?php if (isset($demos)) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label">Select Store</label>
        <div class="col-sm-10">
            <select class="form-control" id="demo-store-selector">
            <?php foreach ($demos as $demo) { ?>
                <option value="<?php echo $demo['demo_id']; ?>"><?php echo $demo['name']; ?></option>
            <?php } ?>
            </select>
                        
            <div class="demo-preview"><br />
            <img id="demo-preview-img" src="model/extension/basel/demo_stores/1/preview.jpg" /><br /><br />
            
<div class="one-click-info" style="margin-bottom:20px;">
<p>By using the One click installer tool, you can easily configure your modules and layouts as our demo stores.</p>
<p>Please note that your existing layouts and module configurations will be lost when importing a demo store.</p>
<p>Sample products, categories and blog posts will be added. Existing products, categories and blog posts will not be affected.</p>
<p>* Stores in the list marked with * will add rules to the Thame Panel's Custom CSS</p>
</div>
			<a id="demo-preview-btn" class="btn btn-primary" onclick="importDemo(1);">Install this demo store</a>
            </div>
        </div>                   
    </div>
    <?php } ?>
<?php } ?>
<?php if ($theme_default_directory != 'basel') { ?>
<p>Please enable the theme before using the one click installer</p>
<?php } ?>
<?php if (count($stores) <= 1) { ?>
<p>Since the One click installer changes layouts and modules, its not multi-store ready</p>
<?php } ?>

<script type="text/javascript">
$('select[id=\'demo-store-selector\']').on('change', function () {
    $('#demo-preview-img').attr('src','model/extension/basel/demo_stores/' + $(this).val() + '/preview.jpg');
	$('#demo-preview-btn').attr('onclick','importDemo(' + $(this).val() + ')');
});
var importDemo = function(store_id) {
	importurl = '<?php echo $demo_import_url; ?>'.replace(/\&amp;/g,'&');
	confirm('<?php echo $text_confirm; ?>') ? location.href=importurl + store_id : false;
}
</script>