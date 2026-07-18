<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9 col-sm-8'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?> no-min-height">
    <?php echo $content_top; ?>
      <h1 id="page-title"><?php echo $heading_title; ?></h1>

      <div class="login-area">
      <div class="row">
        <div class="col-sm-6 left">
            <h2><i class="icon-login"></i><?php echo $text_returning_affiliate; ?></h2>
            
            <?php if ($success) { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
            <?php } ?>
            <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
            <?php } ?>
  
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-vertical">
              <div class="form-group required">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              </div>
              <div class="form-group required">
                <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
                <a href="<?php echo $forgotten; ?>" class="label-link pull-right"><?php echo $text_forgotten; ?></a>
                <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" />
              </div>
              <input type="submit" value="<?php echo $button_login; ?>" class="btn btn-outline btn-block" />
              <?php if ($redirect) { ?>
              <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
              <?php } ?>
            </form>
          </div>
          
          <div class="col-sm-6 right">
            <h2><?php echo $text_new_affiliate; ?></h2>
            <div class="margin-b20"><?php echo $text_description; ?></div>
            <a class="btn btn-primary" href="<?php echo $register; ?>"><?php echo $button_continue; ?></a>
        </div>
       </div>
      </div>
     <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>