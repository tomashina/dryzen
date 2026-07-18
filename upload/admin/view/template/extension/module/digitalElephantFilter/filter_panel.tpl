<?php
/**
 * filter_data(manufacturers[], categories[], options[], attributes[])
 */
?>
<div class="form-group">
    <label class="col-sm-2 control-label"></label>
    <label class="col-sm-2 control-label"><?= $text_type; ?></label>
    <label class="col-sm-1 control-label">
        <?= $text_hide; ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               id="hide_all"
            >
    </label>
    <label class="col-sm-1 control-label">
        <?= $text_close; ?>
        <input type="checkbox"
               class="form-control checkbox-inline"
               id="close_all"
            >
    </label>

</div>

<div class="form-group">
    <label class="col-sm-2 control-label"><?= $text_filter_price ?></label>
    <label class="col-sm-offset-2 col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['filter_price']['hide'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
            <input type="checkbox"
                   class="form-control checkbox-inline hide_all"
                   name="DEF_settings[filter_price][hide]"
                <?= $checked ?>
                >
        </label>

    <label class="col-sm-1 control-label">
        <?php $checked = ''; ?>
        <?php if (isset($DEF_settings['filter_price']['close'])) { ?>
            <?php $checked = ' checked="checked"'; ?>
        <?php } ?>
            <input type="checkbox"
                   class="form-control checkbox-inline close_all"
                   name="DEF_settings[filter_price][close]"
                <?= $checked ?>
                >
        </label>
</div>

<?php foreach ($packages as $package) { ?>
    <div class="form-group">
        <?php foreach ($package as $section) { ?>
            <?php
            $input_name = $section['input_name'];
            $input_label = $section['input_label'];
            $section_id = $section['section_id'];
            $group_name = $section['group_name'];
            ?>
            <div class="row <?= $input_name ?>" style="padding-bottom:10px;">
                <label class="col-sm-2 control-label"><?= $input_label; ?> <?= $group_name; ?></label>

                <div class="col-sm-2">
                    <select
                        name="DEF_settings[<?= $input_name ?>][<?= $section_id ?>][type]"
                        class="form-control <?= $input_name ?>"
                        >
                        <?php foreach ($list_types_input as $type_input) { ?>
                            <?php $selected = '' ?>
                            <?php if (isset($DEF_settings[$input_name][$section_id]['type'])) { ?>
                                <?php if ($type_input == $DEF_settings[$input_name][$section_id]['type']) { ?>
                                    <?php $selected = '  selected="selected"'; ?>
                                <?php } ?>
                            <?php } ?>

                            <option <?= $selected; ?>><?= $type_input ?></option>
                        <?php } ?>
                    </select>
                </div>
                <label class="col-sm-1 control-label">
                    <?php $checked = ''; ?>
                    <?php if (isset($DEF_settings[$input_name][$section_id]['hide'])) { ?>
                        <?php $checked = ' checked="checked"'; ?>
                    <?php } ?>

                        <input type="checkbox"
                               class="form-control checkbox-inline hide_all"
                               name="DEF_settings[<?= $input_name ?>][<?= $section_id ?>][hide]"
                            <?= $checked ?>
                            >
                    </label>

                <label class="col-sm-1 control-label">
                    <?php $checked = ''; ?>
                    <?php if (isset($DEF_settings[$input_name][$section_id]['close'])) { ?>
                        <?php $checked = ' checked="checked"'; ?>
                    <?php } ?>

                        <input type="checkbox"
                               class="form-control checkbox-inline close_all"
                               name="DEF_settings[<?= $input_name ?>][<?= $section_id ?>][close]"
                            <?= $checked ?>
                            >
                    </label>

                <input
                    type="hidden"
                    name="DEF_settings[<?= $input_name ?>][<?= $section_id ?>][input_label]"
                    value="<?= $input_label ?>"
                    >

                <input
                    type="hidden"
                    name="DEF_settings[<?= $input_name ?>][<?= $section_id ?>][element_id]"
                    value="<?= $section_id ?>"
                    >
            </div>

        <?php } ?>
    </div>
<?php } ?>
<script>
$('select[class*="attributes"] option[value*="image"]').remove();
</script>