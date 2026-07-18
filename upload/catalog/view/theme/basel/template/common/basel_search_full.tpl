<div class="full-search-wrapper">
<div class="search-field search-main">
<input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo $basel_text_search; ?>" class="form-control main-search-input" />
</div>
<div class="search-category">
<select name="category_id" class="form-control">
<option value="0"><?php echo $basel_text_category; ?></option>
<?php foreach ($categories as $category_1) { ?>
    <?php if ($category_1['category_id'] == $category_id) { ?>
    <option value="<?php echo $category_1['category_id']; ?>" selected="selected"><?php echo $category_1['name']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $category_1['category_id']; ?>"><?php echo $category_1['name']; ?></option>
    <?php } ?>
    <?php foreach ($category_1['children'] as $category_2) { ?>
    <?php if ($category_2['category_id'] == $category_id) { ?>
    <option value="<?php echo $category_2['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $category_2['category_id']; ?>">&nbsp;&nbsp;&nbsp;<?php echo $category_2['name']; ?></option>
    <?php } ?>
    <?php foreach ($category_2['children'] as $category_3) { ?>
    <?php if ($category_3['category_id'] == $category_id) { ?>
    <option value="<?php echo $category_3['category_id']; ?>" selected="selected">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
    <?php } else { ?>
    <option value="<?php echo $category_3['category_id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $category_3['name']; ?></option>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    <?php } ?>
</select>
</div>
<div class="search-button do-search main">
<a class="icon-magnifier icon"></a>
</div>
</div>
<script>
$(document).ready(function() {
$('.search-holder-mobile input[name=\'search-mobile\']').attr("placeholder", "<?php echo $basel_text_search; ?>").attr("value", "<?php echo $search; ?>");
});
</script>