<?php echo $header; ?><?php echo $column_left; ?>
<?php echo $tf_column_left ?>
<?php echo $tf_header ?>
<div id="content" class="mz-content">
  <!-- Alert message -->
  <?php if(isset($success)){ ?>
    <div class="alert alert-success alert-dismissible"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <?php echo $success ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
  <?php } ?>
  <?php if(isset($warning)){ ?>
    <div class="alert alert-warning alert-dismissible"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo $warning ?> <a href="#" class="close" data-dismiss="alert" aria-label="close"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
  <?php } ?>
    
  <div class="panel-group mz-panel-action-bar">
    <div class="panel panel-default">
      <div class="panel-heading clearfix" data-toggle="collapse" data-target="#filter-tf-filter">
        <h4 class="panel-title pull-left"><?php echo $text_filter ?></h4>
        <span class="pull-right"><i class="fa fa-chevron-circle-down"></i></span>
      </div>
      <div id="filter-tf-filter" class="panel-collapse collapse">
        <div class="panel-body">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name ?>" placeholder="<?php echo $entry_name ?>" id="input-name" class="form-control large" />
              </div>
            </div>
            <div class="col-sm-6">
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
  
  <div class="panel mz-panel-default">
    <div class="panel-body">
      <form action="<?php echo $delete ?>" method="post" enctype="multipart/form-data" id="form-tf-filter">
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
                <td class="text-right"><?php if($sort == 'date_sync'){ ?>
                  <a href="<?php echo $sort_date_sync ?>" class="<?php echo strtolower($order) ?>"><?php echo $column_date_sync ?></a>
                  <?php } else { ?>
                  <a href="<?php echo $sort_date_sync ?>"><?php echo $column_date_sync ?></a>
                  <?php } ?></td>
                <td class="text-right"><?php echo $column_action ?></td>
              </tr>
            </thead>
            <tbody>
              <?php if($filters){ ?>
              <?php foreach($filters as $filter){ ?>
              <tr>
                <td class="text-center"><?php if(in_array($filter['filter_id'], $selected)){ ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $filter['filter_id'] ?>" checked="checked" />
                  <?php } else { ?>
                  <input type="checkbox" name="selected[]" value="<?php echo $filter['filter_id'] ?>" />
                  <?php } ?></td>
                <td class="text-left"><?php echo $filter['name'] ?></td>
                <td class="text-center"><?php echo $filter['sort_order'] ?></td>
                <td class="text-center"><?php echo $filter['status'] ?></td>
                <td class="text-right"><?php echo $filter['date_sync'] ?></td>
                <td class="text-right">
                  <a href="<?php echo $filter['value_edit'] ?>" data-toggle="tooltip" title="<?php echo $button_value ?>" class="btn btn-info"><i class="fa fa-tint"></i></a>
                  <a href="<?php echo $filter['edit'] ?>" data-toggle="tooltip" title="<?php echo $button_edit ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                </td>
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
        
	location = 'index.php?route=extension/maza/tf_filter<?php echo $default_url ?>' + url;
});
</script>
<script>
$('#button-sync').click(function(){
    if(!confirm('<?php echo $text_confirm ?>')) return false; // Confirm action
    
    var button_sync = $(this);
    button_sync.data('loading-text', '<i class="fa fa-refresh fa-spin"></i>');
    
    $.ajax({
            url: "index.php?route=extension/maza/tf_filter/sync&user_token=<?php echo $user_token ?>",
            type: 'post',
            data: $('#form-tf-filter').serialize(),
            dataType: 'json',
            beforeSend: function () {
                button_sync.button('loading');
            },
            success: function(json) {
                    $('.alert-dismissible').remove();

                    if (json['success']) {
                            $('#content').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
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
//{#Confirm before delete module#}
$('#button-delete, #button-copy').on('click', function(){
    if(confirm("<?php echo $text_confirm ?>")){
        return true;
    } else {
        return false;
    }
});

</script>
</div>
<?php echo $tf_footer ?>