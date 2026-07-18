<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="mp-content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-warning"><i class="fa fa-reply"></i> <?php echo $button_cancel; ?></a>
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
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($success) { ?>
		<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($files) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $text_files_permission; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<button type="button" class="btn btn-primary" id="addfiles"><i class="fa fa-plus-circle" data-class="fa fa-plus-circle"></i> <?php echo $button_files_permission; ?></button>
		</div>
		<script type="text/javascript">
			$('#addfiles').on('click', function() {
				var button = this;
				$.ajax({
					url: 'index.php?route=extension/module/mpimportexport/updatePermissions&<?php echo $get_token; ?>=<?php echo $token; ?>',
					type: 'get',
					data: '',
					dataType: 'json',
					beforeSend: function() {
						$(button).attr('disabled','disabled');
						$(button).find('i').attr('class', 'fa fa-refresh fa-spin');
					},
					complete: function() {
						$(button).removeAttr('disabled');
						$(button).find('i').attr('class', $(button).find('i').attr('data-class'));
					},
					success: function(json) {
						$('.alert-dismissible, .text-danger').remove();

						if (json['redirect']) {
							location = json['redirect'];
						}

						if (json['success']) {
							$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});
		</script>
		<?php } ?>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-mixcloud"></i> <?php echo $text_form; ?></h3>
			</div>
			<div class="panel-body">
				<form id="form-mpimportexport" class="form-horizontal">
					<?php /*
					<table class="table table-bordered">
						<tbody>
							<?php foreach ($block_sections as $block_section) { ?>
							<tr>
								<td><?php echo $block_section['title']; ?></td>
								<td>
									<?php foreach ($block_section['link'] as $action => $link) { ?>
									<a href="<?php echo $link; ?>"><?php echo $block_section['language'][$action]; ?></a>
									<?php } ?>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					*/ ?>
					<style type="text/css">
					.block_section {
						background: linear-gradient(to bottom,#f0f6ff 0,#edf5ff 100%);
						border-radius: 4px;
						text-align: center;
						margin-bottom: 25px;
						position: relative;
						overflow: hidden;
						border-radius: 4px;
						box-shadow:0 1px 17px rgb(167 167 167 / 16%);
						min-height: 170px;
					}
					.block_section > div {
						position: relative;
						z-index: 9;
					}
					.block_section .main_icon {
						position: absolute;opacity: 0.6;transform: translate(0%, 0%);z-index: 1;color: #34b8ff; font-size: 7em;
					}
					.block_section .box_title {
						font-size: 20px;
						font-weight: 500;
						line-height: 1.3;
						margin-bottom: 9px;
						font-family: 'Urbanist', sans-serif;
						background: #34b8ff;
						color: #fff;
						padding: 5px 7px;
					}
					/*danger starts*/
					.block_clr:nth-child(even) .block_section {
						background: #ffeded;
					}
					.block_clr:nth-child(even) .block_section .box_title {
						background: #f98080;
					}
					.block_clr:nth-child(even) .block_section .main_icon {
						color: #f98080;
					}
					/*danger ends*/
					/*orange starts*/
					.block_clr:nth-child(3n+3) .block_section {
						background: #ffede6;
					}
					.block_clr:nth-child(3n+3) .block_section .box_title{
						background: #f9946d;
					}
					.block_clr:nth-child(3n+3) .block_section .main_icon {
						color: #f9946d;
					}
					/*orange ends*/

					/*green starts*/
					.block_clr:nth-child(4n+4) .block_section {
						background: #d3ffe1;
					}
					.block_clr:nth-child(4n+4) .block_section .box_title {
						background: #00ce95;
					}
					.block_clr:nth-child(4n+4) .block_section .main_icon {
						color: #00ce95;
					}

					/*green ends*/
					/*pink starts*/
					.block_clr:nth-child(5n+5) .block_section {
						background: #ffe5f1;
					}
					.block_clr:nth-child(5n+5) .block_section .box_title {
						background: #ff95c6;
					}
					.block_clr:nth-child(5n+5) .block_section .main_icon {
						color: #ff95c6;
					}

					/*pink ends*/
					/*blckcrnt starts*/
					.block_clr:nth-child(6) .block_section {
						background: #ece9ff;
					}
					.block_clr:nth-child(6) .block_section .box_title {
						background: #aba0ff;
					}
					.block_clr:nth-child(6) .block_section .main_icon {
						color: #aba0ff;
					}

					/*blckcrnt ends*/
					/*vanilla starts*/
					.block_clr:nth-child(7) .block_section {
						background: #ffe8be;
					}
					.block_clr:nth-child(7) .block_section .box_title {
						background: #ffb42b;
					}
					.block_clr:nth-child(7) .block_section .main_icon {
						color: #ffb42b;
					}

					/*vanilla ends*/
					/*skyblue starts*/
					.block_clr:nth-child(8) .block_section {
						background: #d3f7ff;
					}
					.block_clr:nth-child(8) .block_section .box_title {
						background: #30d9ff;
					}
					.block_clr:nth-child(8) .block_section .main_icon {
						color: #30d9ff;
					}
					/*skyblue ends*/

					/*.clr_skyblue.block_section{
						background: #f0f6ff;
					}
					.clr_skyblue.box_title {
						background: #34b8ff;
					}
					.clr_danger.block_section{
						background: #ffeded;
					}
					.clr_danger.box_title {
						background: #f98080;
					}
					.clr_orange.block_section{
						background: #ffede6;
					}
					.clr_orange.box_title {
						background: #f9946d;
					}
					.clr_green.block_section{
						background: #d3ffe1;
					}
					.clr_green.box_title {
						background: #00ce95;
					}
					.clr_green.block_section{
						background: #ffe5f1;
					}
					.clr_green.box_title {
						background: #ff95c6;
					}
					.clr_blckcrrent.block_section{
						background: #ece9ff;
					}
					.clr_blckcrrent.box_title {
						background: #aba0ff;
					}
					.clr_vanilla.block_section{
						background: #ffe8be;
					}
					.clr_vanilla.box_title {
						background: #ffb42b;
					}*/

					</style>
					<div class="row blocks">
						<?php foreach ($block_sections as $block_section) { ?>
	          <div class="col-sm-4 block_clr">
	          	<div class="block_section">
	              <div class="box_title"><?php echo $block_section['title']; ?></div>
	              <i class="fa fa-shopping-cart main_icon"></i>
	              <div class="box_btns">
	                <?php foreach ($block_section['link'] as $action => $link) { ?>
									<a href="<?php echo $link; ?>" class="btn <?php echo $block_section['class'][$action]; ?>"><i class="<?php echo $block_section['icon'][$action]; ?>"></i> <?php echo $block_section['language'][$action]; ?></a>
									<?php } ?>
	              </div>
	            </div>
	          </div>
	          <?php } ?>
	        </div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>