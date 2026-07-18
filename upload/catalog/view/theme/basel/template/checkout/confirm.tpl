<?php if (!isset($redirect)) { ?>
<div class="table-responsive">
  <table class="table table-bordered margin-b0">
    <thead>
      <tr>
        <td class="text-left"><?php echo $column_name; ?></td>
        <td class="text-left hidden-xs hidden-sm"><?php echo $column_model; ?></td>
        <td class="text-right hidden-xs hidden-sm"><?php echo $column_quantity; ?></td>
        <td class="text-right hidden-xs hidden-sm"><?php echo $column_price; ?></td>
        <td class="text-right"><?php echo $column_total; ?></td>
   
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
        <td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?>
          <?php if($product['recurring']) { ?>
          <br />
          <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
          <?php } ?></td>
        <td class="text-left hidden-xs hidden-sm"><?php echo $product['model']; ?></td>
        <td class="text-right hidden-xs hidden-sm"><?php echo $product['quantity']; ?></td>
        <td class="text-right hidden-xs hidden-sm"><?php echo $product['price']; ?></td>
        <td class="text-right"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td class="text-left"><?php echo $voucher['description']; ?></td>
        <td class="text-left"></td>
        <td class="text-right">1</td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    </table>
    
    <table class="table totals table-bordered">
      <tbody>
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td><b><?php echo $total['title']; ?></b></td>
        <td class="text-right"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
      </tbody>
  </table>
  
</div>
<?php echo $payment; ?>
<?php } else { ?>
<script><!--
location = '<?php echo $redirect; ?>';
//--></script>
<?php } ?>
