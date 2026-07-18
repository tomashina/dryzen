<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_on_display_totals ?></label>
    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['is_display_total'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               name="DEF_settings[is_display_total]"
            <?= $checked ?>
            >
    </label>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_on_group_attributes ?></label>

    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['is_group_attributes'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               name="DEF_settings[is_group_attributes]"
            <?= $checked ?>
            >
    </label>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_on_button_apply ?></label>

    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['is_button_apply'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               name="DEF_settings[is_button_apply]"
            <?= $checked ?>
            >
    </label>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_on_button_clear ?></label>

    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['is_button_clear'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               name="DEF_settings[is_button_clear]"
            <?= $checked ?>
            >
    </label>
</div>