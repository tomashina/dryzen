<div style="width:<?php echo $popup_width; ?>px;max-width:100%;">
<div class="table popup-window dark_field text-center" style="height:<?php echo $popup_height; ?>px;">

<?php if ($img) { ?>
<div class="table-cell img-cell">
<img src="<?php echo $img; ?>" alt="" />
</div>
<?php } ?>

<div class="table-cell content-cell w100">
<?php if (!empty($popup_title)) { ?>
<h2><?php echo $popup_title; ?></h2>
<?php } ?>
<?php if ($popup_content_block && $popup_content_block != '<p><br></p>') { ?>
<?php echo $popup_content_block; ?>
<?php } ?>
</div>

</div>
</div>