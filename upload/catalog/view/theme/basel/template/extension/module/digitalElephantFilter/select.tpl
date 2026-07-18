<?php
/**
 * SELECT:
 * $input_name
 * $option_value
 * $option_name
 * $option_id
 */
?>
<div class="digitalElephantFilter_type_select">

    <select class="form-control" name="<?= $inputs_data[0]['input_name'] ?>[<?= $section['id'] ?>][]">
        <option value=""><?= $text_all ?></option>
        <?php foreach ($inputs_data as $input_data) { ?>

            <?php $attr_selected = ''; ?>
            <?php if (isset($check_data_on_active[$input_data['input_name']][$section['id']])
                && in_array($input_data['input_value'], $check_data_on_active[$input_data['input_name']][$section['id']])) { ?>
                <?php $attr_selected = 'selected="selected"'; ?>
            <?php } ?>

            <?php $attr_disabled = (!$input_data['is_enable']) ? 'disabled="disabled"' : ''; ?>
            <option
                value="<?= $input_data['value_id'] ?>"
                <?= $attr_selected; ?>
                <?= $attr_disabled; ?>

                >
                <?= $input_data['input_label'] ?>
            </option>
        <?php } ?>
    </select>
</div>