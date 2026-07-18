<?php foreach ($packages_advance as $package_name) { ?>
    <div class="form-group">
        <label class="col-sm-2 control-label"><?= $package_name ?></label>

        <label class="col-sm-1 control-label"><?= $text_sort ?></label>

        <div class="col-sm-1">
            <select
                name="DEF_settings[advance][<?= $package_name?>][sort]"
                class="form-control"
                >
                <?php foreach ($list_types_sort as $type_sort) { ?>
                    <?php $selected = '' ?>
                    <?php if (isset($DEF_settings['advance'][$package_name]['sort'])) { ?>
                        <?php if ($type_sort['value'] == $DEF_settings['advance'][$package_name]['sort']) { ?>
                            <?php $selected = '  selected="selected"'; ?>
                        <?php } ?>
                    <?php } ?>

                    <option
                        value="<?= $type_sort['value'] ?>"
                        <?= $selected; ?>
                        >
                        <?= $type_sort['name'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm-8 image-<?= $package_name ?>">
            <label class="control-label col-sm-2"><?= $entry_image; ?></label>

            <div class="col-sm-1 image-<?= $package_name ?>">
                <input
                    type="text"
                    class="form-control"
                    name="DEF_settings[advance][<?= $package_name?>][image][width]"
                    value="<?= $DEF_settings['advance'][$package_name]['image']['width'] ?>"
                    placeholder="<?= $entry_width; ?>"
                    size="3"
                    />
            </div>
            <div class="col-sm-1 image-<?= $package_name ?>">
                <input
                    type="text"
                    class="form-control"
                    name="DEF_settings[advance][<?= $package_name?>][image][height]"
                    value="<?= $DEF_settings['advance'][$package_name]['image']['height'] ?>"
                    placeholder="<?= $entry_height; ?>"
                    size="3"
                    />
            </div>
        </div>
    </div>
<?php } ?>
<style>
.image-attributes {visibility:hidden;}
</style>