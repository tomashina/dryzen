<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9 col-sm-8'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      
      <h1 id="page-title"><?php echo $text_my_account; ?></h1>
      <legend><?php echo $text_my_account; ?></legend>
      <ul class="list-unstyled margin-b30">
        <li><a href="<?php echo $edit; ?>"><?php echo $text_edit; ?></a></li>
        <li><a href="<?php echo $password; ?>"><?php echo $text_password; ?></a></li>
        <li><a href="<?php echo $payment; ?>"><?php echo $text_payment; ?></a></li>
      </ul>
      <legend><?php echo $text_my_tracking; ?></legend>
      <ul class="list-unstyled margin-b30">
        <li><a href="<?php echo $tracking; ?>"><?php echo $text_tracking; ?></a></li>
      </ul>
      <legend><?php echo $text_my_transactions; ?></legend>
      <ul class="list-unstyled margin-b30">
        <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
      </ul>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>