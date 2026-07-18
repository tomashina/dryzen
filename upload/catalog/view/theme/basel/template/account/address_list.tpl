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
  <?php if ($error_warning) { ?>
  <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
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
      <h1 id="page-title"><?php echo $heading_title; ?></h1>
      <legend><?php echo $text_address_book; ?></legend>
      
      <?php if ($addresses) { ?>
      
      
          <?php foreach ($addresses as $result) { ?>
          <div class="single-address">
          <i class="display-block margin-b15"><?php echo $result['address']; ?></i>
           <small><a href="<?php echo $result['update']; ?>" class="inline-link"><?php echo $button_edit; ?></a></small>
           <small><a href="<?php echo $result['delete']; ?>" class="inline-link"><?php echo $button_delete; ?></a></small>
           </div>
          <?php } ?>
       
      
      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <div class="buttons clearfix text-right">
        <a href="<?php echo $add; ?>" class="btn btn-contrast"><?php echo $button_new_address; ?></a>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>