<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
		<a href="https://www.huntbee.com/documentation/docs/base-plugin-for-huntbee-extensions" target="_blank" class="btn btn-default"><i class="fa fa-book"></i>&nbsp;DOCUMENTATION</a>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-newsletter-module" class="form-horizontal">
          	
		<ul class="nav nav-tabs" id="types">
			<li class="active"><a href="#tab-apps" data-toggle="tab"><i class="fa fa-windows" aria-hidden="true"></i>&nbsp;Productivity Apps</a></li>
			<li><a href="#tab-seo" data-toggle="tab"><i class="fa fa-google" aria-hidden="true"></i>&nbsp;SEO Apps</a></li>
			<li><a href="#tab-php" data-toggle="tab" style="color: rgb(0, 162, 255);"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;PHP Info</a></li>
			
			<li><a href="https://www.huntbee.com/" target="_blank" style="color: rgb(130, 0, 61);"><i class="fa fa-star" aria-hidden="true" ></i>&nbsp;Developer Website</a></li>
			<li><a href="https://www.huntbee.com/get-support" target="_blank" style="color:green;"><i class="fa fa-life-ring" aria-hidden="true"></i>&nbsp;Support & License</a></li>

		</ul>
					  
		<div class="tab-content">	
			<div class="tab-pane active" id="tab-apps">
				<div id="hbapps-block" class="extension"></div>
			</div>
			
			
			<div class="tab-pane" id="tab-seo">
				<div id="hbseo-block"></div>
			</div><!--hbseo-->

			<div class="tab-pane" id="tab-php">
				<div class="well">
					<p><b>Server Date & Time:</b> <?php echo $server_date; ?></p><br>
					<p><b>OpenCart Version:</b> <?php echo $opencart_version; ?></p>
				</div>
				<iframe src="<?php echo $php_info_link; ?>" width="100%" height="1000px"></iframe>
			</div><!--hbseo-->
		</div>

        </form>
      </div>
    </div>
  </div>
    <div class="container-fluid"> <!--Huntbee copyrights-->
    <br />
 <center><span class="help"><?php echo $heading_title; ?>&nbsp;<?php echo $extension_version; ?> &copy; <a href="http://www.huntbee.com/">HUNTBEE.COM</a></span></center>
</div><!--Huntbee copyrights end-->

<style type="text/css">
.pr_error,.pr_info,.pr_infos,pr_default.pr_success,.pr_warning{margin:10px 0;padding:12px}.pr_info{color:#00529B;background-color:#BDE5F8}.pr_success{color:#4F8A10;background-color:#DFF2BF}.pr_warning{color:#9F6000;background-color:#FEEFB3}.pr_error{color:#D8000C;background-color:#FFBABA}.pr_error i,.pr_info i,.pr_success i,.pr_warning i{margin:10px 0;vertical-align:middle}
</style>

<script type="text/javascript">
	$(document).ready(function() {
		$('#hbapps-block').html('<center><div class="loaddiv"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div></center>');
		$('#hbapps-block').load('<?php echo $hbapps; ?>');

		$('#hbseo-block').html('<center><div class="loaddiv"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div></center>');
		$('#hbseo-block').load('<?php echo $hbseo; ?>');
	});
</script>
<script type="text/javascript">
$('#hbapps-block').on('click', '.btn-success', function(e) {
	e.preventDefault();
	
	var node = this;

	$.ajax({
		url: $(node).attr('href'),
		dataType: 'html',
		beforeSend: function() {
			$(node).button('loading');
		},
		complete: function() {
			$(node).button('reset');
		},
		success: function(html) {
			$('#hbapps-block').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#hbseo-block').on('click', '.btn-success', function(e) {
	e.preventDefault();
	
	var node = this;

	$.ajax({
		url: $(node).attr('href'),
		dataType: 'html',
		beforeSend: function() {
			$(node).button('loading');
		},
		complete: function() {
			$(node).button('reset');
		},
		success: function(html) {
			$('#hbseo-block').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#hbapps-block').on('click', '.btn-danger, .btn-warning', function(e) {
	e.preventDefault();
	
	if (confirm('<?php echo $text_confirm; ?>')) {
		var node = this;
	
		$.ajax({
			url: $(node).attr('href'),
			dataType: 'html',
			beforeSend: function() {
				$(node).button('loading');
			},
			complete: function() {
				$(node).button('reset');
			},
			success: function(html) {
				$('#hbapps-block').html(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});


$('#hbseo-block').on('click', '.btn-danger, .btn-warning', function(e) {
	e.preventDefault();
	
	if (confirm('<?php echo $text_confirm; ?>')) {
		var node = this;
	
		$.ajax({
			url: $(node).attr('href'),
			dataType: 'html',
			beforeSend: function() {
				$(node).button('loading');
			},
			complete: function() {
				$(node).button('reset');
			},
			success: function(html) {
				$('#hbseo-block').html(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});
</script>
<?php echo $footer; ?>