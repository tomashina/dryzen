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
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h1 id="page-title"><?php echo $heading_title; ?></h1>
      <?php if ($orders) { ?>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <td><?php echo $column_order_id; ?></td>
              <td><?php echo $column_customer; ?></td>
              <td class="hidden-xs hidden-sm"><?php echo $column_product; ?></td>
              <td class="hidden-xs hidden-sm"><?php echo $column_status; ?></td>
              <td class="text-right hidden-xs hidden-sm"><?php echo $column_total; ?></td>
              <td class="text-right hidden-xs hidden-sm"><?php echo $column_date_added; ?></td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order) { ?>
            <tr>
              <td>#<?php echo $order['order_id']; ?></td>
              <td><?php echo $order['name']; ?></td>
              <td class="hidden-xs hidden-sm"><?php echo $order['products']; ?></td>
              <td class="hidden-xs hidden-sm"><?php echo $order['status']; ?></td>
              <td class="text-right hidden-xs hidden-sm"><?php echo $order['total']; ?></td>
              <td class="text-right hidden-xs hidden-sm"><?php echo $order['date_added']; ?></td>
              <td class="text-right">
              <a href="<?php echo $order['view']; ?>" class="btn btn-primary btn-sm"><?php echo $button_view; ?></a>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="text-right"><?php echo $pagination; ?></div>
      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
     
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>