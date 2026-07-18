<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_on_seo_keywords ?></label>
    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['seo']['is_keywords'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               name="DEF_settings[seo][is_keywords]"
            <?= $checked ?>
            >
    </label>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_state_show_more ?></label>
    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['state']['is_button_show_more'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               name="DEF_settings[state][is_button_show_more]"
            <?= $checked ?>
            >
    </label>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_state_pagination ?></label>
    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['state']['is_pagination'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               name="DEF_settings[state][is_pagination]"
            <?= $checked ?>
            >
    </label>
</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_state_quantity_products ?></label>
    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['state']['is_quantity_products'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               name="DEF_settings[state][is_quantity_products]"
            <?= $checked ?>
            >
    </label>
</div>

<input type="hidden" name="DEF_settings[preloader_type]" value="spinner_fast" checked="checked">