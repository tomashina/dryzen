<div class="search-field">
<input type="text" name="search" value="<?php echo $search; ?>" placeholder="<?php echo $basel_text_search; ?>" class="form-control main-search-input" />
<span class="btn btn-contrast do-search main"><?php echo $basel_text_search; ?></span>
</div>
<script>
$(document).ready(function() {
$('.search-holder-mobile input[name=\'search-mobile\']').attr("placeholder", "<?php echo $basel_text_search; ?>").attr("value", "<?php echo $search; ?>");
});
</script>