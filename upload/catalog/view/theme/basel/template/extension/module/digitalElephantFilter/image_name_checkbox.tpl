<div class="filter_type_image digitalElephantFilter_type_checkbox">
    <?php foreach ($inputs_data as $input_data) { ?>
        <?php if ($input_data['image']) { ?>
            <label>
                <?php $checked = ''; ?>
                <?php if (isset($check_data_on_active[$input_data['input_name']][$section['id']])
                    && in_array($input_data['input_value'], $check_data_on_active[$input_data['input_name']][$section['id']])
                ) { ?>
                    <?php $checked = 'checked="checked"'; ?>
                <?php } ?>
                <input style="display: none;"
                       type="checkbox"
                       name="<?= $input_data['input_name']; ?>[<?= $section['id']; ?>][]"
                       value="<?= $input_data['input_value']; ?>"
                    <?= $checked; ?>
                    >
                <img src="<?= $input_data['image']; ?>" title="<?= $input_data['input_label'] ?>"/>
                <span><?= $input_data['input_label'] ?></span>
            </label>
        <?php } ?>
    <?php } ?>
</div>