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
            <!--Name-->
            <div class="form-group required">
              <label class="col-xs-3 control-label" for="input-name"><?php echo $entry_name ?></label>
              <div class="col-xs-9">
              <?php foreach($languages as $language){ ?>
                <div class="input-group inline"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
                  <input class="form-control" type="text" name="value_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($value_description[$language['language_id']]['name'])?$value_description[$language['language_id']]['name']:''; ?>" placeholder="<?php echo $entry_name ?>" >
                  <?php if(isset($err_name[$language['language_id']])){ ?>
                    <div class="text-danger"><?php echo $err_name[$language['language_id']] ?></div>
                  <?php } ?>
                </div>
              <?php } ?>
              </div>
            </div>
            
            <!--status-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-status"><?php echo $entry_status ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($status){ ?>
                  <input type="radio" name="status" value="1" id="input-status-enabled" checked>
                  <label for="input-status-enabled" ><?php echo $text_enabled; ?></label>
                  <input type="radio" name="status" value="0" id="input-status-disabled">
                  <label for="input-status-disabled"><?php echo $text_disabled; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="status" value="1" id="input-status-enabled" >
                  <label for="input-status-enabled"><?php echo $text_enabled; ?></label>
                  <input type="radio" name="status" value="0" id="input-status-disabled" checked>
                  <label for="input-status-disabled" ><?php echo $text_disabled; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--sort order-->
            <div class="form-group required">
              <label class="col-xs-3 control-label" for="input-sort-order"><?php echo $entry_sort_order ?></label>
              <div class="col-xs-9">
                <input class="form-control" id="input-sort-order" placeholder="<?php echo $entry_sort_order ?>" type="text" name="sort_order" value="<?php echo $sort_order; ?>" >
              </div>
            </div>
            
            <!--Image-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_image ?></label>
              <div class="col-xs-9"><a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb ?>" alt="" title="" data-placeholder="<?php echo $placeholder ?>" /></a>
                <input type="hidden" name="image" value="<?php echo $image ?>" id="input-image" />
              </div>
            </div>
            
            <!--Value type-->
            <div class="form-group">
              <label class="col-xs-3 control-label" for="input-regex"><span data-toggle="tooltip" title="<?php echo $help_value_type ?>"><?php echo $entry_value_type ?></span></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <?php if($regex){ ?>
                  <input type="radio" name="regex" value="1" id="input-regex-enabled" checked>
                  <label for="input-regex-enabled" ><?php echo $text_regex; ?></label>
                  <input type="radio" name="regex" value="0" id="input-regex-disabled">
                  <label for="input-regex-disabled"><?php echo $text_text; ?></label>
                  <?php } else { ?>
                  <input type="radio" name="regex" value="1" id="input-regex-enabled" >
                  <label for="input-regex-enabled"><?php echo $text_regex; ?></label>
                  <input type="radio" name="regex" value="0" id="input-regex-disabled" checked>
                  <label for="input-regex-disabled" ><?php echo $text_text; ?></label>
                  <?php } ?>
                </div>
              </div>
            </div>
            
            <!--Value-->
            <div class="form-group required">
              <label class="col-xs-3 control-label" for="input-value"><span data-toggle="tooltip" title="<?php echo $help_value ?>"><?php echo $entry_value ?></span></label>
              <div class="col-xs-9">
                <div class="input-group inline">
                  <span class="input-group-addon"><img src="language/<?php echo $filter_language['code']; ?>/<?php echo $filter_language['code']; ?>.png" title="<?php echo $filter_language['name']; ?>" /></span>
                  <input class="form-control" type="text" name="value" value="<?php echo $value; ?>" id="input-value" placeholder="<?php echo $entry_value ?>" >
                  <?php if(isset($err_value)){ ?>
                    <div class="text-danger"><?php echo $err_value ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </form>
<!--  <script>
    $('#value-languages a').on('click', function(e){
        e.preventDefault();
        var value = $(this).data('value');
        $('input[name="value_language_id"]').val(value);
        
        $(this).parents('.dropdown-menu').prev('.dropdown-toggle').html($(this).html() + ' <span class="caret"></span>');
    });
    
    $('#input-value-language-<?php // echo $value_language_id ?>').click();
  </script>-->
</div>
<?php echo $tf_footer ?>