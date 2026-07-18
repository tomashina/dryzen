<div class="widget bordered-list">
<b class="bordered-list-title invert"><?php echo $text_account; ?></b>
 <?php if (!$logged) { ?>
  <a href="<?php echo $login; ?>"><?php echo $text_login; ?></a> <a href="<?php echo $register; ?>"><?php echo $text_register; ?></a> <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
  <?php } ?>
  <a href="<?php echo $account; ?>"><?php echo $text_account; ?></a>
  <?php if ($logged) { ?>
  <a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a> <a href="<?php echo $password; ?>"><?php echo $text_password; ?></a>
  <?php } ?>
  <a href="<?php echo $payment; ?>"><?php echo $text_payment; ?></a> <a href="<?php echo $tracking; ?>"><?php echo $text_tracking; ?></a> <a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a>
  <?php if ($logged) { ?>
  <a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a>
  <?php } ?>
</div>