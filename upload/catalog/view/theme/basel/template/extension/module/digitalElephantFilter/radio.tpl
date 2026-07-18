<div class="digitalElephantFilter_type_radio">
    <!--
    <label class="radio-inline">
        <input
            type="radio"
            name="<?= $inputs_data[0]['input_name'] ?>[<?= $section['id'] ?>][]"
            value="0"
            >
        <?= $text_all; ?>
    </label>
    -->
    <?php foreach ($inputs_data as $input_data) { ?>
        <label class="radio-inline">

            <?php $attr_checked = ''; ?>
            <?php if (isset($check_data_on_active[$input_data['input_name']][$section['id']])
                && in_array($input_data['input_value'], $check_data_on_active[$input_data['input_name']][$section['id']])) { ?>
                <?php $attr_checked = 'checked="checked"'; ?>
            <?php } ?>

            <?php $attr_disabled = (!$input_data['is_enable']) ? 'disabled="disabled"' : ''; ?>
            <input
                type="radio"
                name="<?= $input_data['input_name'] ?>[<?= $section['id'] ?>][]"
                value="<?= $input_data['input_value'] ?>"
                <?= $attr_checked; ?>
                <?= $attr_disabled ?>
                >
            <span><?= $input_data['input_label'] ?></span>
        </label>
    <?php } ?>
</div>