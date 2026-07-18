<?= $header; ?><?= $column_left; ?>
    <div id="content" class="digitalElephantFilter">
        <div class="page-header">
            <div class="container-fluid">
                <div class="pull-right">
                    <button type="submit" form="form-latest" data-toggle="tooltip" title="<?= $button_save; ?>"
                            class="btn btn-primary"><i class="fa fa-save"></i></button>
                    <button type="submit" form="form-popup_coupon" id="button-save-out" data-toggle="tooltip"
                            title="<?= $button_save_and_cancel ?>" class="btn btn-primary" value="save-out"><i class="fa fa-save"></i> <i class="fa fa-reply"></i>
                    </button>
                    <a href="<?= $cancel; ?>" data-toggle="tooltip" title="<?= $button_cancel; ?>"
                       class="btn btn-default"><i class="fa fa-reply"></i></a></div>
                <h1><?= $heading_title; ?></h1>
                <ul class="breadcrumb">
                    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                        <li><a href="<?= $breadcrumb['href']; ?>"><?= $breadcrumb['text']; ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="container-fluid">
            <?php if ($error_warning) { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?= $error_warning; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>
            <?php if ($success) { ?>
            <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> <?= $text_edit; ?></h3>
                </div>
                <div class="panel-body">
                    <form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form-latest"
                          class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="input-status"><?= $entry_status; ?></label>

                            <div class="col-sm-10">
                                <select name="status" id="input-status" class="form-control">
                                    <?php if ($status) { ?>
                                        <option value="1" selected="selected"><?= $text_enabled; ?></option>
                                        <option value="0"><?= $text_disabled; ?></option>
                                    <?php } else { ?>
                                        <option value="1"><?= $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?= $text_disabled; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab-setting-filter-panel-advanced" data-toggle="tab">
                                    <?= $text_tab_filter_panel_advanced; ?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-setting-filter-panel" data-toggle="tab">
                                    <?= $text_tab_filter_panel; ?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-setting-selector" data-toggle="tab">
                                    <?= $text_tab_selector; ?>
                                </a>
                            </li>
                            <li>
                                <a href="#tab-setting-sort" data-toggle="tab">
                                    <?= $text_tab_sort; ?>
                                </a>
                            </li>

                            <li>
                                <a href="#tab-setting-cache" data-toggle="tab">
                                    <?= $text_tab_cache ?>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="tab-setting-filter-panel-advanced">
                                <?php require_once 'digitalElephantFilter/filter_panel_advanced.tpl' ?>
                                <?php require_once __DIR__ . '/digitalElephantFilter/other.tpl' ?>
                            </div>
                            <div class="tab-pane" id="tab-setting-filter-panel">
                                <?php require_once 'digitalElephantFilter/filter_panel.tpl' ?>
                            </div>
                            <div class="tab-pane" id="tab-setting-selector">
                                <?php require_once __DIR__ . '/digitalElephantFilter/selector.tpl' ?>
                            </div>
                            <div class="tab-pane" id="tab-setting-sort">
                                <?php require_once __DIR__ . '/digitalElephantFilter/sort.tpl' ?>
                            </div>
                            <div class="tab-pane" id="tab-setting-cache">
                                <?php require_once __DIR__ . '/digitalElephantFilter/cache.tpl' ?>
                            </div>
                        </div>

                        <!-- HIDDEN -->
                        <input type="hidden" name="save_out" id="field-save-out" value="0">
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
$(function(){

    //multiple choose
    $('#hide_all, #close_all').click(function(){
        var input_class = '.' + $(this).attr('id');
        if ($(this).is(':checked')) {
            $(input_class).each(function() {
                if (!$(this).is(':checked')) {
                    $(this).click();
                }
            });
        } else {
            $(input_class).each(function() {
                if ($(this).is(':checked')) {
                    $(this).click();
                }
            });
        }
    });

    checkShowAll();
    checkOpenedAll();
    //multiple choose END

});

    function checkShowAll() {
        if ($('.hide_all').length == $('.hide_all:checked').length) {
            $('#hide_all').attr('checked', 'checked');
            return true;
        }
        return false;
    }

    function checkOpenedAll() {
        if ($('.close_all').length == $('.close_all:checked').length) {
            $('#close_all').attr('checked', 'checked');
            return true;
        }
        return false;
    }

$('#button-save-out').click(function(){
    $('#field-save-out').val('1');
});

</script>
<?= $footer; ?>