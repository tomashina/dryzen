<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <!--Header Start-->
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-canonical" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
		<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-add" title="Add Custom Canonical"><i class="fa fa-plus" aria-hidden="true"></i></button>
		<button type="button" class="btn btn-danger" id="delete"  data-toggle="tooltip" title="DELETE"><i class="fa fa-trash-o"></i></button>
		<a href="https://www.huntbee.com/documentation/docs/seo-canonical-links-for-opencart/" target="_blank" class="btn btn-default"  data-toggle="tooltip" title="DOCUMENTATION"><i class="fa fa-book"></i></a>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a> </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <!--Header End-->
  <div class="container-fluid">
    <!--Start - Error / Success Message if any -->
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <!--End - Error / Success Message if any -->
	<div id="msgoutput"></div>
	
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-files-o" aria-hidden="true"></i> <?php echo $heading_title; ?></h3>
		<?php if ($stores) { ?>
		<div class="pull-right">
		<select id="store">
			<option value="0" <?php echo ($store_id == 0)?'selected':''; ?>>Default Store</option>
			<?php foreach ($stores as $store) { ?>
				<option value="<?php echo $store['store_id']; ?>" <?php echo ($store_id == $store['store_id'])?'selected':''; ?>><?php echo $store['name']; ?></option>
			<?php } ?>
		</select>
		</div>
		<?php } ?>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-canonical" class="form-horizontal">
			<!--Tabs UL Starts-->
			<ul class="nav nav-tabs" id="tabs">
                <li class="active"><a href="#tab-setting" data-toggle="tab"><i class="fa fa-gears" aria-hidden="true"></i> Settings</a></li>
				<li><a href="#tab-custom" onclick="loadCustom();" data-toggle="tab"><i class="fa fa-magic" aria-hidden="true"></i> Canonical Path Replacer</a></li>
	          </ul>
			  <!--Tabs UL Ends-->
			  <div class="tab-content">

			  	<div class="tab-pane active" id="tab-setting">
					<div class="form-group">
						<label class="col-sm-2 control-label">Status</label>
						<div class="col-sm-10">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_canonical_status" class="form-control" value="1" <?php echo ($hb_canonical_status == '1')? 'checked':'' ; ?> />
						</div>
				   	 </div>
					 
					  <div class="form-group">
						<label class="col-sm-2 control-label">Product Canonical Type</label>
						<div class="col-sm-10">
						  <select name="hb_canonical_type" class="form-control">
							<option value="1" <?php echo ($hb_canonical_type == '1')? 'selected':''; ?> ><?php echo $text_type_long; ?></option>
							<option value="0" <?php echo ($hb_canonical_type == '0')? 'selected':''; ?> ><?php echo $text_type_short; ?></option>
							<option value="2" <?php echo ($hb_canonical_type == '2')? 'selected':''; ?> >Level Selection</option>
						  </select>
						</div>
					  </div>
					  
					  <div class="form-group">
						<label class="col-sm-2 control-label">Product Category Levels</label>
						<div class="col-sm-10">
						  <input type="number" name="hb_canonical_level" value="<?php echo $hb_canonical_level; ?>" class="form-control" >
						</div>
					  </div>
					  
					  <div class="form-group">
						<label class="col-sm-2 control-label">Category Canonical Type</label>
						<div class="col-sm-10">
						  <select name="hb_canonical_type_c" class="form-control">
							<option value="1" <?php echo ($hb_canonical_type_c == '1')? 'selected':''; ?> ><?php echo $text_type_long; ?></option>
							<option value="0" <?php echo ($hb_canonical_type_c == '0')? 'selected':''; ?> ><?php echo $text_type_short; ?></option>
						  </select>
						</div>
					  </div>

				</div>
				
				<div class="tab-pane" id="tab-custom">
					<div class="input-group" style="margin-bottom:10px;">
						<input type="text" id="search-value" onkeyup="searchURL();" class="form-control" placeholder="Search by URL/Canonical">
						<span class="input-group-addon btn" id="search-button" onclick="searchURL();"><i class="fa fa-search"></i></span>
					</div>
					<div id="custom-block"></div>
				</div>
				
			</div>	
        </form>
		
		<!--KEYWORD BASED URL START-->
		<div class="modal fade" id="modal-add" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Custom Canonical Links</h4>
			  </div>
			  <div class="modal-body">
				  <div class="form-group">
					<label class="control-label">URL</label>
					<input type="text" class="form-control" id="browser-url">
				  </div>
				  <div class="form-group">
					<label class="control-label">Canonical URL</label>
					<input type="text" class="form-control" id="canonical">
				  </div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="add-canonical">ADD</button>
			  </div>
			  <div id="add-result" style="text-align:center;"></div>
			</div>
		  </div>
		</div>
		<!--KEYWORD BASED URL END-->
		
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <!--Huntbee copyrights-->
    <center>
      <span class="help"><?php echo $heading_title; ?> - <?php echo $extension_version; ?> &copy; <a href="https://www.huntbee.com/">WWW.HUNTBEE.COM</a> | <a href="https://www.huntbee.com/get-support">SUPPORT</a></span>
    </center>
  </div>
  <!--Huntbee copyrights end-->
</div>

<style type="text/css">
.loaddiv{margin:100px;color:#0099CC;}
body{font-family: 'PT Sans', sans-serif; font-size: 13px;}
</style>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>	

<script type="text/javascript">
function loadCustom(){
	$('#custom-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#custom-block').load('index.php?route=<?php echo $base_route; ?>/hb_canonical/custom&token=<?php echo $token; ?>');
}

$('#custom-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();

	$('#custom-block').load(this.href);
});

function searchURL() {
	var searchvalue = $('#search-value').val();
	$('#custom-block').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$('#custom-block').load('index.php?route=<?php echo $base_route; ?>/hb_canonical/custom&token=<?php echo $token; ?>&search='+ encodeURIComponent(searchvalue));
};

$('#add-canonical').on('click', function() {
	$('#add-result').html('');

	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_canonical/add_canonical&token=<?php echo $token; ?>',
		  data: {browser_url : $('#browser-url').val(), canonical : $('#canonical').val()},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#add-result').html('<div class="alert alert-success">'+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					  loadCustom();
				}
				if (json['warning']) {
					  $('#add-result').html('<div class="alert alert-danger">'+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
		  },			
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });	
});

$('#delete').on('click', function() {
	$('#msgoutput').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	var arraydata = $('input[name="selected[]"]:checked').map(function(){
        return this.value;
    }).get();
	
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_canonical/delete&token=<?php echo $token; ?>',
		  data: {selected : arraydata},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="alert alert-success">'+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					  loadCustom();
				}
				if (json['warning']) {
					  $('#msgoutput').html('<div class="alert alert-danger">'+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
		  },			
			error: function(xhr, ajaxOptions, thrownError) {
				$('#msgoutput').html('');
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });	
});
</script>
<script type="text/javascript">
$('#store').on('change', function() {
	window.location.href = 'index.php?route=<?php echo $base_route; ?>/hb_canonical&token=<?php echo $token; ?>&store_id='+$('#store').val();
});
</script>
<?php echo $footer; ?>