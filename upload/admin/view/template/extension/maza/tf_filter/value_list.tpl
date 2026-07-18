<?php echo $header; ?><?php echo $column_left; ?>
<?php echo $tf_column_left ?>
<?php echo $tf_header ?>
<div id="content" class="mz-content">
  <div class="panel-group mz-panel-action-bar">
    <div class="panel panel-default">
      <div class="panel-heading clearfix" data-toggle="collapse" data-target="#filter-tf-value">
        <h4 class="panel-title pull-left"><?php echo $text_filter ?></h4>
        <span class="pull-right"><i class="fa fa-chevron-circle-down"></i></span>
      </div>
      <div id="filter-tf-value" class="panel-collapse collapse">
        <div class="panel-body">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name ?>" placeholder="<?php echo $entry_name ?>" id="input-name" class="form-control large" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-type"><?php echo $entry_type ?></label>
                <select name="filter_regex" id="input-type" class="form-control large">
                  <option value=""></option>
                  <?php if($filter_regex == '1'){ ?>
                  <option value="1" selected="selected"><?php echo $text_regex ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_regex ?></option>
                  <?php } ?>
                  <?php if($filter_regex == '0'){ ?>
                  <option value="0" selected="selected"><?php echo $text_text ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_text ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status ?></label>
                <select name="filter_status" id="input-status" class="form-control large">
                  <option value=""></option>
                  <?php if($filter_status == '1'){ ?>
                  <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled ?></option>
                  <?php } ?>
                  <?php if($filter_status == '0'){ ?>
                  <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="panel-footer text-right"><button type="button" id="button-filter" class="btn btn-primary"><i class="fa fa-filter"></i> <?php echo $button_filter ?></button></div>
      </div>
    </div>
  </div>
    
  <!-- Alert message -->
  <?php if(isset($success)){ ?>
    <div class="alert alert-success alert-dismissible"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $success ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
  <?php } ?>
  <?php if(isset($warning)){ ?>
    <div class="alert alert-warning alert-dismissible"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $warning ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
  <?php } ?>
  
  <div class="panel mz-panel-default">
    <div class="panel-body">
      <form method="post" enctype="multipart/form-data" id="form-tf-value">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                <td class="text-left"><?php if($sort == 'name'){ ?>
                  <a href="<?php echo $sort_name ?>" class="<?php echo strtolower($order) ?>"><?php echo $column_name ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_name ?>"><?php echo $column_name ?></a>
                  <?php } ?></td>
                <td class="text-center"><?php echo $column_image ?></td>
                <td class="text-center"><?php if($sort == 'total_product'){ ?>
                  <a href="<?php echo $sort_total_product ?>" class="<?php echo strtolower($order) ?>"><?php echo $column_product ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_total_product ?>"><?php echo $column_product ?></a>
                  <?php } ?></td>
                <td class="text-center"><?php if($sort == 'regex'){ ?>
                  <a href="<?php echo $sort_regex ?>" class="<?php echo strtolower($order) ?>"><?php echo $column_type ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_regex ?>"><?php echo $column_type ?></a>
                  <?php } ?></td>
                <td class="text-center"><?php if($sort == 'sort_order'){ ?>
                  <a href="<?php echo $sort_sort_order ?>" class="<?php echo strtolower($order) ?>"><?php echo $column_sort_order ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_sort_order ?>"><?php echo $column_sort_order ?></a>
                  <?php } ?></td>
                <td class="text-center"><?php if($sort == 'status'){ ?>
                  <a href="<?php echo $sort_status ?>" class="<?php echo strtolower($order) ?>"><?php echo $column_status ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_status ?>"><?php echo $column_status ?></a>
                  <?php } ?></td>
                <td class="text-right"><?php echo $column_action ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if($values){ ?>
              <?php foreach($values as $value){ ?>
              <tr>
                <td class="text-center"><?php if(in_array($value['value_id'], $selected)){ ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $value['value_id'] ?>" checked="checked" />
                  <?php } else { ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $value['value_id'] ?>" />
                  <?php } ?></td>
                <td class="text-left"><?php echo $value['name'] ?></td>
                <td class="text-center"><?php if($value['image']){ ?> <img src="<?php echo $value['image'] ?>" alt="<?php echo $value['name'] ?>" class="img-thumbnail" /> <?php } else { ?> <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span> <?php } ?></td>
                <td class="text-center"><?php echo $value['total_product'] ?></td>
                <td class="text-center"><?php echo $value['type'] ?></td>
                <td class="text-center"><?php echo $value['sort_order'] ?></td>
                <td class="text-center"><?php echo $value['status'] ?></td>
                <td class="text-right"><a href="<?php echo $value['edit'] ?>" data-toggle="tooltip" title="<?php echo $button_edit ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="6"><?php echo $text_no_results ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </form>
      <div class="row">
        <div class="col-sm-6 text-left"><?php echo $pagination ?></div>
        <div class="col-sm-6 text-right"><?php echo $results ?></div>
      </div>
    </div>
  </div>
    
<div id="modal-import" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-upload"></i> <?php echo $text_import_values ?></h4>
      </div>
      <div class="modal-body">
        <form id="form-import" method="post" class="form-horizontal" enctype="multipart/form-data">
            <!--Data-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_data ?></label>
              <div class="col-xs-9">
                <div class="mz-switch-field">
                  <input type="radio" name="merge" value="1" id="input-merge-enabled" checked>
                  <label for="input-merge-enabled"><?php echo $text_merge; ?></label>
                  <input type="radio" name="merge" value="0" id="input-merge-disabled">
                  <label for="input-merge-disabled" ><?php echo $text_replace; ?></label>
                </div>
              </div>
            </div>
            
            <!--File CSV-->
            <div class="form-group">
              <label class="col-xs-3 control-label"><?php echo $entry_csv ?></label>
              <div class="col-xs-9">
                <label class="btn btn-default"><i class="fa fa-upload"></i> <?php echo $text_browse ?> <input type="file" name="file" class="hide" accept=".csv" required /></label>
              </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="button-submit-import" class="btn btn-info"><?php echo $button_import ?></button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $('#button-filter').on('click', function() {
	var url = '';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status !== '') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}
        
        var filter_regex = $('select[name=\'filter_regex\']').val();

	if (filter_regex !== '') {
		url += '&filter_regex=' + encodeURIComponent(filter_regex);
	}
        
	location = 'index.php?route=extension/maza/tf_filter/value<?php echo $default_url ?>' + url;
});
</script>
<script>
$('#button-sync').click(function(){
    if(!confirm('<?php echo $text_confirm ?>')) return false; // Confirm action
    
    var button_sync = $(this);
    button_sync.data('loading-text', '<i class="fa fa-refresh fa-spin"></i>');
    
    $('.alert-dismissible').remove();
    
    $.ajax({
            url: "index.php?route=extension/maza/tf_filter/sync&user_token=<?php echo $user_token ?>",
            type: 'post',
            data: {filter_id: <?php echo $filter_id ?>},
            dataType: 'json',
            beforeSend: function () {
                button_sync.button('loading');
            },
            success: function(json) {
                    if (json['success']) {
                            $('#content').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                            setTimeout(function(){ location.reload(); }, 1000);
                    }
                    if (json['error']) {
                            $('#content').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
            },
            complete: function () {
                button_sync.button('reset');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
    });
});
</script>
<script>
$('#button-import').click(function(){
    $('#modal-import').modal('show');
});
$('#button-submit-import').click(function(){
    $('.alert-dismissible').remove();
    
    $.ajax({
        url: 'index.php?route=extension/maza/tf_filter/value/import<?php echo $default_url ?>',
        type: 'post',
        dataType: 'json',
        data: new FormData($('#form-import')[0]),
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
                $('#button-submit-import').button('loading');
        },
        complete: function() {
                $('#button-submit-import').button('reset');
        },
        success: function(json) {
                if (json['error']) {
                        $('#form-import').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }

                if (json['success']) {
                        $('#modal-import').modal('hide');
                        $('#content').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                        setTimeout(function(){ location.reload(); }, 1000);
                }
        },
        error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});
</script>
<script>
// Confirm Actions 
$('#button-delete,#button-enable,#button-disable').on('click', function(){
    if(confirm("<?php echo $text_confirm ?>")){
        return true;
    } else {
        return false;
    }
});

</script>
</div>
<?php echo $tf_footer ?>