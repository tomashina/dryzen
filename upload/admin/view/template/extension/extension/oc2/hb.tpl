<fieldset>
  <legend><?php echo $heading_title; ?>
  
  <div class="pull-right">
        <a href="https://www.huntbee.com/documentation/" target="_blank" data-toggle="tooltip" title="Knowledge Base" class="btn btn-default btn-sm"><i class="fa fa-book"></i> User Guides</a>
		<a href="https://www.huntbee.com/get-support/" target="_blank" data-toggle="tooltip" title="Need Help? Get our Support" class="btn btn-primary btn-sm"><i class="fa fa-life-ring"></i> Get Support</a>
		<a href="https://www.huntbee.com/" target="_blank" data-toggle="tooltip" title="More useful extensions for you store" class="btn btn-primary btn-sm"><i class="fa fa-cart-plus"></i> Extension Store</a>
  </div>
  </legend>
  
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
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr style="background-color:#fcfcfc">
          <td class="text-left"><i class="fa fa-wrench"></i> <?php echo $column_name; ?></td>
          <td class="text-center"><?php echo $column_action; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php if ($extensions) { ?>
        <?php foreach ($extensions as $extension) { ?>
        <tr>
          <td class="text-left"><b><?php echo $extension['name']; ?></b></td>
          <td class="text-center">
		  	<?php if ($extension['installed']) { ?>
				<?php if ($store_count == 1) { ?>
					<?php foreach ($extension['store'] as $store) { ?>
						<a class="btn btn-secondary btn-primary" href="<?php echo $store['edit']; ?>"><i class="fa fa-gear"></i> <?php echo $button_edit; ?></a>
					<?php } ?>
				<?php } else { ?>
					<span class="dropdown">
					  <button class="btn btn-secondary btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-gear"></i> <?php echo $button_edit; ?>
					  </button>
					  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<?php foreach ($extension['store'] as $store) { ?>
						<li><a href="<?php echo $store['edit']; ?>"><?php echo $store['name']; ?></a></li>
						<?php } ?>
					  </ul>
					</span>
				<?php } ?>
		  <?php } ?>
		      <?php if (!$extension['installed']) { ?>
            <a href="<?php echo $extension['install']; ?>" data-toggle="tooltip" title="<?php echo $button_install; ?>" class="btn btn-success"><i class="fa fa-plus-circle"></i></a>
            <?php } else { ?>
            <a href="<?php echo $extension['uninstall']; ?>" data-toggle="tooltip" title="<?php echo $button_uninstall; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a>
            <a href="<?php echo $extension['update']; ?>" data-toggle="tooltip" title="<?php echo $button_update; ?>" class="btn btn-warning"><i class="fa fa-refresh"></i></a>
          <?php } ?>
          

          </td>
        </tr>
        <?php } ?>
        <?php } else { ?>
        <tr>
          <td class="text-center" colspan="2"><?php echo $text_no_results; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</fieldset>