<?php echo $header; ?><?php echo $column_left; ?>
<?php echo $tf_column_left ?>
<?php echo $tf_header ?>
<div id="content" class="mz-content">
  
  <!-- Alert message -->
  <?php if(isset($success)){ ?>
    <div class="alert alert-success"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $success ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
  <?php } ?>
  <?php if(isset($warning)){ ?>
    <div class="alert alert-warning"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $warning ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
  <?php } ?>
  
  <!-- Form -->
  <form action="<?php echo $action ?>" class="form-horizontal" enctype="multipart/form-data" id="form-tf-value" method="post">
    <div class="tab-content">
        
      <!--General-->
      <div id="tab-tf-general" class="tab-pane fade in active">
        <div class="panel mz-panel-default">
          <div class="panel-body">
            <!--Sub category-->
            <div class="form-group">
              <label class="col-xs-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_sub_category ?>"><?php echo $entry_sub_category ?></span></label>
              <div class="col-xs-10">
                <div class="mz-switch-field">
                  <?php if($module_tf_filter_sub_category){ ?>
                  <input type="radio" name="module_tf_filter_sub_category" value="1" id="input-sub-category-enabled" checked>
                  <label for="input-sub-category-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="module_tf_filter_sub_category" value="0" id="input-sub-category-disabled">
                  <label for="input-sub-category-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="module_tf_filter_sub_category" value="1" id="input-sub-category-enabled" >
                  <label for="input-sub-category-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="module_tf_filter_sub_category" value="0" id="input-sub-category-disabled" checked>
                  <label for="input-sub-category-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
        
      <!--cron-->
      <div id="tab-tf-cron" class="tab-pane fade">
        <div class="panel mz-panel-default">
          <div class="panel-body">
            <!--cron status-->
            <div class="form-group">
              <label class="col-xs-2 control-label"><?php echo $entry_status ?></label>
              <div class="col-xs-10">
                <div class="mz-switch-field">
                  <?php if($module_tf_filter_cron_status){ ?>
                  <input type="radio" name="module_tf_filter_cron_status" value="1" id="input-cron-status-enabled" checked>
                  <label for="input-cron-status-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="module_tf_filter_cron_status" value="0" id="input-cron-status-disabled">
                  <label for="input-cron-status-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="module_tf_filter_cron_status" value="1" id="input-cron-status-enabled" >
                  <label for="input-cron-status-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="module_tf_filter_cron_status" value="0" id="input-cron-status-disabled" checked>
                  <label for="input-cron-status-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!-- cron url-->
            <div class="form-group">
              <label class="col-xs-2 control-label" for="input-cron-url"><span data-toggle="tooltip" title="<?php echo $help_url ?>"><?php echo $entry_url ?></span></label>
              <div class="col-xs-10">
                <input class="form-control large" id="input-cron-url" type="text"  value="<?php echo $cron_url; ?>" readonly="">
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </form>
</div>
<?php echo $tf_footer ?>