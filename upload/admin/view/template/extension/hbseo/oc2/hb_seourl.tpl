<?php echo $header; ?><?php echo $column_left; ?>
<!--Main Content block start-->

<div id="content">
  <!--Header Start-->
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-latest" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i> SAVE</button>
		<a class="btn btn-danger" onclick="deletelink();" id="button-deletelink"><i class="fa fa-minus"></i> Remove Routes</a>
		<a href="https://www.huntbee.com/documentation/docs/seo-url-generator/" target="_blank" class="btn btn-default"><i class="fa fa-book"></i> Documentation</a>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language" class="form-horizontal">
			<!--Tabs UL Starts-->
			<ul class="nav nav-tabs" id="tabs">
                <li class="active"><a href="#tab-dashboard" data-toggle="tab"><i class="fa fa-tachometer" aria-hidden="true"></i> <?php echo $tab_dashboard; ?></a></li>
				<li><a href="#tab-route" data-toggle="tab"><i class="fa fa-link" aria-hidden="true"></i> <?php echo $tab_routes; ?></a></li>
				<li><a href="#tab-setting" data-toggle="tab"><i class="fa fa-cogs" aria-hidden="true"></i> <?php echo $tab_setting; ?></a></li>
	          </ul>
			  <!--Tabs UL Ends-->
			  <div class="tab-content">
			  	<div class="tab-pane active" id="tab-dashboard">
					<div class="col-sm-9">
						<div id="dashboard-block"></div>
					</div>
					<div class="col-sm-3 well">

						<div class="btn-group" style="margin-bottom:10px; width:100%;">
						  <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" style="width:100%;">
							Generate SEO URL Keyword <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-right" role="menu">
							<li><a onclick="generateurl('product');"><i class="fa fa-play"></i> Product URL Keyword</a></li>
							<li><a onclick="generateurl('category');"><i class="fa fa-play"></i> Category URL Keyword</a></li>
						  	<li><a onclick="generateurl('brand');"><i class="fa fa-play"></i> Brand URL Keyword</a></li>
							<li><a onclick="generateurl('information');"><i class="fa fa-play"></i> Information URL Keyword</a></li>
						  </ul>
						</div>	
						
						<div class="btn-group" style="margin-bottom:10px; width:100%;">
						  <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" style="width:100%;">
							Clear SEO URL Keyword <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-right" role="menu">
							<li><a onclick="clearurl('product_id=');"><i class="fa fa-trash"></i> Product URL Keyword</a></li>
							<li><a onclick="clearurl('category_id=');"><i class="fa fa-trash"></i> Category URL Keyword</a></li>
						  	<li><a onclick="clearurl('manufacturer_id=');"><i class="fa fa-trash"></i> Brand URL Keyword</a></li>
							<li><a onclick="clearurl('information_id=');"><i class="fa fa-trash"></i> Information URL Keyword</a></li>
						  </ul>
						</div>
						
						<div class="btn-group" style="margin-bottom:10px; width:100%;">
						  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" style="width:100%;">
							Additional Tools <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu dropdown-menu-right" role="menu">
							<li><a onclick="preserve();"><i class="fa fa-bookmark"></i> Preserve Exisitng URL Keyword</a></li>
							<li><a onclick="clearpreserve();"><i class="fa fa-trash"></i> Clear Preserve data</a></li>
						  	<li><a onclick="passtoredirectmanager();" title="Set 301 Redirect from Preserved Keyword Data"><i class="fa fa-exchange"></i> Set 301 Redirect</a></li>
						  </ul>
						</div>
										
						<a id="button-batch" data-loading-text="Generating..." class="btn btn-success col-sm-12" onclick="batch_generate('index.php?route=<?php echo $base_route; ?>/hb_seourl/batch_generate&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');"><i class="fa fa-magic"></i> Generate Products in Batches</a>

					</div>
				</div>
				
	  			<!--MISC TAB-->
	  			<div class="tab-pane" id="tab-route">	
						 
					 <div class="form-group">
						<div class="col-sm-12" style="margin-bottom:10px;">
							<div class="col-sm-12">
								<div class="input-group">
									<span class="input-group-addon" id="basic-addon1"><?php echo $store_url; ?>index.php?route=</span>
									<input type="text" id="route" class="form-control" value="" placeholder="Enter route" />
								</div>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="col-sm-5">
								<div class="input-group">
									<span class="input-group-addon" id="basic-addon1"><?php echo $store_url; ?></span>
									<input type="text" id="keyword" class="form-control" value="" placeholder="Enter Keyword" />
								</div>
							</div>
							<div class="col-sm-5">
								<div class="input-group">
									<span class="input-group-addon" id="basic-addon1">Language</span>
									<select id="keyword_language_id" class="form-control">
										<?php foreach ($languages as $language) { ?>
											<option value="<?php echo $language['language_id']; ?>"><?php echo $language['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							
							<div class="col-sm-2">
								<a class="btn btn-success col-sm-12" onclick="addlink();" id="button-addlink"><i class="fa fa-plus"></i> Add Route</a>
							</div>
						</div>
				     </div>
					
					<div id="route-block"></div>
				
				</div>
				
				<!--SETUP-->
				<div class="tab-pane" id="tab-setting">	
					<div class="form-group">
						<label class="col-sm-4 control-label">Keyword Pattern <br />
								<a href="https://www.huntbee.com/documentation/docs/seo-url-generator/short-codes/" target="_blank">Short-codes Reference</a>
						</label>
						
						<div class="col-sm-6">
							<div class="input-group">
								<span class="input-group-addon">Product </span>
								<input type="text" name="hb_seourl_keyword_product" value="<?php echo $hb_seourl_keyword_product; ?>" class="form-control" />
							</div>
						</div>
						<div class="col-sm-2">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_seourl_use_pattern" class="form-control" value="1" <?php echo ($hb_seourl_use_pattern == '1')? 'checked':'' ; ?> />
						</div>
				   	</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Enable Transliterate</label>
						<div class="col-sm-8">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_seourl_trans" class="form-control" value="1" <?php echo ($hb_seourl_trans == '1')? 'checked':'' ; ?> />
						</div>
				   </div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Run script when product/category/brand/information item is added</label>
						<div class="col-sm-8">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_seourl_auto" class="form-control" value="1" <?php echo ($hb_seourl_auto == '1')? 'checked':'' ; ?> />
						</div>
				   </div>
				   <div class="form-group">
						<label class="col-sm-4 control-label">Update New keyword to Preserved List
							<div class="pr_warning">Make sure you have preserved the old keywords by clicking on <b>"Preserve SEO URL Keyword"</b> button</div>
						</label>
						<div class="col-sm-8">
							<input type="checkbox" data-toggle="toggle" data-onstyle="success" name="hb_seourl_preserve" class="form-control" value="1" <?php echo ($hb_seourl_preserve == '1')? 'checked':'' ; ?> />
						</div>
				   </div>
				</div>
				
			  </div><!--tab-content block end-->
        </form>
		
      </div>
    </div>
    <!--Panel Content End-->
    <!--Huntbee copyrights-->
    <div class="container-fluid">
      <center>
        <span class="help"><?php echo $heading_title; ?> - <?php echo $extension_version;?> &copy; <a href="https://www.huntbee.com/">WWW.HUNTBEE.COM</a> | <a href="https://www.huntbee.com/get-support">SUPPORT</a> | <a href="https://www.huntbee.com/documentation/docs/seo-url-generator/" target="_blank">DOCUMENTATION</a></span>
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
	$('#dashboard-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#route-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#dashboard-block').load('index.php?route=extension/hbseo/hb_seourl/dashboard&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
	$('#route-block').load('index.php?route=extension/hbseo/hb_seourl/routes&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
}
</script>

<script type="text/javascript">
function addlink(){
	$('#msgoutput').html('');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=extension/hbseo/hb_seourl/addlink&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {route : $('#route').val(), keyword : $('#keyword').val(), language_id : $('#keyword_language_id').val()},
		  dataType: 'json',		
		  beforeSend: function() {
				$('#button-addlink').button('loading');
		  },
		  complete: function() {
				$('#button-addlink').button('reset');
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
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });
}

function deletelink(){
	$('#msgoutput').html('');
	var arraydata = $('input[name="selected[]"]:checked').map(function(){
        return this.value;
    }).get()
	
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=extension/hbseo/hb_seourl/deletelink&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {selected : arraydata},
		  dataType: 'json',		
		  beforeSend: function() {
				$('#button-deletelink').button('loading');
		  },
		  complete: function() {
				$('#button-deletelink').button('reset');
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
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}

	 });
}

function clearurl(query){
	$('#msgoutput').html('');
	$.ajax({
		  type: 'post',
		  url: 'index.php?route=extension/hbseo/hb_seourl/clearseourl&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {query : query},
		  dataType: 'json',		
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				loadreports();
		  },			
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}

	 });
}

function preserve(){
	$('#msgoutput').html('');
	$.ajax({
		  url: 'index.php?route=extension/hbseo/hb_seourl/preserveseourl&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  dataType: 'json',		
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				if (json['warning']) {
					  $('#msgoutput').html('<div class="alert alert-danger"><i class="fa fa-warning"></i> '+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
		  },			
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}

	 });
}

function clearpreserve(){
	$('#msgoutput').html('');
	$.ajax({
		  url: 'index.php?route=extension/hbseo/hb_seourl/clearpreserveseourl&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  dataType: 'json',		
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
		  },			
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}

	 });
}

function passtoredirectmanager(){
	$('#msgoutput').html('');
	$.ajax({
		  url: 'index.php?route=extension/hbseo/hb_seourl/seourltobrokenlinks&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  dataType: 'json',		
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				if (json['warning']) {
					  $('#msgoutput').html('<div class="alert alert-danger"><i class="fa fa-warning"></i> '+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
		  },			
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}

	 });
}

function generateurl(type){
	$('#msgoutput').html('');
	$.ajax({
		  type: 'get',
		  url: 'index.php?route=extension/hbseo/hb_seourl/generatekeyword&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		  data: {type : type},
		  dataType: 'json',		
		  success: function(json) {
				if (json['success']) {
					  $('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				}
				loadreports();
		  },			
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}

	 });
}


function batch_generate(url) {
	$.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('#button-batch').button('loading');
		},
		complete: function() {
			//$('#button-batch').button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();

			if (json['next']) {
				if (json['success']) {
					$('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i>  ' + json['success'] + '</div>');
					//loadreports();
					
					batch_generate(json['next']);
					
				}
			} else {
				if (json['success']) {
					$('#msgoutput').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					loadreports();
					$('#button-batch').button('reset');
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			$('#button-batch').button('reset');
		}
	});
}

</script>

<script type="text/javascript">
$('#store').on('change', function() {
	window.location.href = 'index.php?route=<?php echo $base_route; ?>/hb_seourl&token=<?php echo $token; ?>&store_id='+$('#store').val();
});
</script>
<?php echo $footer; ?>