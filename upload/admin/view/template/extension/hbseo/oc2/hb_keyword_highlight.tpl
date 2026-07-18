<?php echo $header; ?><?php echo $column_left; ?>
<!--Main Content block start-->

<div id="content">
  <!--Header Start-->
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#generate-modal"><i class="fa fa-angle-double-right" aria-hidden="true"></i> Generate</button>
		<a class="btn btn-danger" onclick="deleteKeywords();" id="button-deletekeyword"><i class="fa fa-trash"></i> Delete Keywords</a>
		<a href="https://www.huntbee.com/documentation/docs/seo-keyword-highlight-generator/" target="_blank" class="btn btn-default"><i class="fa fa-book"></i> Documentation</a>
		<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
	  </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <!--Header End-->
  <!--Container 1 start -->
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
	<!--Panel Content Start-->
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language" class="form-horizontal">
			<div class="form-group">
				<div class="col-sm-12" style="border: #333 1px solid; padding: 20px; background-color: #515151; color: white;">
					<label class="col-sm-3 control-label">Enter Keyword / Phrase</label>
					<div class="col-sm-6">
						<input type="text" name="keyword" id="keyword" value="" class="form-control" placeholder="Enter keyword here. For Example: iPhone" />
					</div>
					<div class="col-sm-3">
						<a class="btn btn-success col-sm-12" style="background-color:#4f9814;" onclick="addKeyword();" id="button-addkeyword"><i class="fa fa-plus"></i> Add Keyword</a>
					</div>
				</div>
			</div>
			
			<div class="input-group" style="margin-bottom:10px;">
				<input type="text" id="search-value" onkeyup="searchKeyword();" class="form-control" placeholder="Search Keyword">
				<span class="input-group-addon btn" id="search-button" onclick="searchKeyword();"><i class="fa fa-search"></i></span>
			</div>
			<div id="keywords-block"></div>
        </form>
		<!--GENERATOR START-->
		<div class="modal fade" id="generate-modal" tabindex="-1" role="dialog">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">CHOOSE TABLES</h4>
			  </div>
			  <div class="modal-body">
			  	<div class="pr_warning text-center"><i class="fa fa-exclamation-circle"></i> DATABASE BACKUP IS STRICTLY RECOMMENDED</div>	
				  <div class="form-group">
				  	<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-code"></i> Tag to be used :</span>
						<input type="text" class="form-control" id="tag" value="b" />
					</div>
				  </div>
				  <div class="form-group">
					<label class="control-label"><input type="checkbox" name="tables[]" value="product" /> Product</label>
				  </div>
				  <div class="form-group">
					<label class="control-label"><input type="checkbox" name="tables[]" value="category" /> Category</label>
				  </div>
				  <div class="form-group">
					<label class="control-label"><input type="checkbox" name="tables[]" value="manufacturer" /> Manufacturer / Brand</label>
				  </div>
				  <div class="form-group">
					<label class="control-label"><input type="checkbox" name="tables[]" value="information" /> Information</label>
				  </div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="generate"><i class="fa fa-play-circle"></i> GENERATE</button>
			  </div>
			  <div id="generate-result" style="text-align:center;"></div>
			</div>
		  </div>
		</div>
		<!--GENERATOR END-->
		
      </div>
    </div>
    <!--Panel Content End-->
    <!--Huntbee copyrights-->
    <div class="container-fluid">
      <center>
        <span class="help"><?php echo $heading_title; ?> - <?php echo $extension_version;?> &copy; <a href="https://www.huntbee.com/">WWW.HUNTBEE.COM</a> | <a href="https://www.huntbee.com/get-support">SUPPORT</a></span>
      </center>
    </div>
    <!--Huntbee copyrights end-->
  </div>
  <!--Container 1 start -->
</div>
<!--Main Content block end-->
<style type="text/css"> <!--addtional css-->
.pr_error,.pr_info,.pr_infos,.pr_success,.pr_warning{margin:10px 0;padding:12px}.pr_info{color:#00529B;background-color:#BDE5F8}.pr_success{color:#4F8A10;background-color:#DFF2BF}.pr_warning{color:#9F6000;background-color:#FEEFB3}.pr_error{color:#D8000C;background-color:#FFBABA}.pr_error i,.pr_info i,.pr_success i,.pr_warning i{margin:10px 0;vertical-align:middle}

a {cursor:pointer;}
</style>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>	   

<script type="text/javascript">
$(document).ready(function() {
	loadreports();
});
</script>
<script type="text/javascript">
function loadreports(){
	$('#keywords-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#keywords-block').load('index.php?route=extension/hbseo/hb_keyword_highlight/keywords&token=<?php echo $token; ?>');
}
</script>

<script type="text/javascript">
function addKeyword(){
	$('#msgoutput').html('');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=extension/hbseo/hb_keyword_highlight/addkeyword&token=<?php echo $token; ?>',
		  data: {keyword : $('#keyword').val()},
		  dataType: 'json',		
		  beforeSend: function() {
				$('#button-addkeyword').button('loading');
		  },
		  complete: function() {
				$('#button-addkeyword').button('reset');
		  },
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				if (json['error']) {
					  $('#msgoutput').html('<div class="alert alert-danger"><i class="fa fa-warning"></i> '+json['error']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				loadreports();
		  },			
		  error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}

function deleteKeywords(){
	$('#msgoutput').html('');
	var arraydata = $('input[name="selected[]"]:checked').map(function(){
        return this.value;
    }).get()
	
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=extension/hbseo/hb_keyword_highlight/deletekeywords&token=<?php echo $token; ?>',
		  data: {selected : arraydata},
		  dataType: 'json',		
		  beforeSend: function() {
				$('#button-deletekewords').button('loading');
		  },
		  complete: function() {
				$('#button-deletekewords').button('reset');
		  },
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				if (json['warning']) {
					  $('#msgoutput').html('<div class="alert alert-danger"><i class="fa fa-warning"></i> '+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				loadreports();
		  },			
		  error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}

function searchKeyword() {
	var searchvalue = $('#search-value').val();
	$('#keywords-block').html('<center><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></center>');
	$('#keywords-block').load('index.php?route=<?php echo $base_route; ?>/hb_keyword_highlight/keywords&token=<?php echo $token; ?>&search='+searchvalue);
};

$('#generate').on('click', function() {
	$('#generate-result').html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
	
	var arraydata = $('input[name="tables[]"]:checked').map(function(){
        return this.value;
    }).get()
	
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=<?php echo $base_route; ?>/hb_keyword_highlight/generate&token=<?php echo $token; ?>',
		  data: {tables : arraydata, tag: $('#tag').val()},
		  dataType: 'json',
		  success: function(json) {
				if (json['success']) {
					  $('#generate-result').html('<div class="pr_success">'+json['success']+'</div>');
					  loadreports();
				}
				if (json['error']) {
					  $('#generate-result').html('<div class="pr_error">'+json['warning']+'</div>');
				}
		  },			
		  error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });	
});
</script>
<?php echo $footer; ?>