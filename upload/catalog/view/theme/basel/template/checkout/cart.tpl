<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  
  <?php if ($attention) { ?>
  <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $attention; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  
  <div class="row">
  
  <?php echo $column_left; ?>
    
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9 col-sm-8'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    
    <div id="content" class="<?php echo $class; ?>">
    <?php echo $content_top; ?>
      <h1 id="page-title"><?php echo $heading_title; ?><?php if ($weight) { ?> (<?php echo $weight; ?>)<?php } ?></h1>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
        
        
          <table class="table table-bordered border-bottom">
            <thead>
              <tr>
                <td class="hidden-xs hidden-sm"></td>
                <td colspan="2"><?php echo $column_name; ?></td>
                <td class="hidden-xs hidden-sm"><?php echo $column_model; ?></td>
                <td><?php echo $column_quantity; ?></td>
                <td class="text-right hidden-xs hidden-sm"><?php echo $column_price; ?></td>
                <td class="text-right"><?php echo $column_total; ?></td>
              </tr>
            </thead>
            
            <tbody>
              <?php foreach ($products as $product) { ?>
              <tr>
              <td class="remove-cell hidden-xs hidden-sm text-center">
              <a onclick="cart.remove('<?php echo $product['cart_id']; ?>');" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="product-remove"><i class="fa fa-times"></i></a>
              </td>
                <td class="image">
                <?php if ($product['thumb']) { ?>
                  <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
                  <?php } ?>
                  </td>
                <td class="name"><a class="hover_uline" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                  <?php if (!$product['stock']) { ?>
                  <span class="text-danger">***</span>
                  <?php } ?>
                  <?php if ($product['option']) { ?>
                  <?php foreach ($product['option'] as $option) { ?>
                  <br />
                  <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                  <?php } ?>
                  <?php } ?>
                  <?php if ($product['reward']) { ?>
                  <br />
                  <small><?php echo $product['reward']; ?></small>
                  <?php } ?>
                  <?php if ($product['recurring']) { ?>
                  <br />
                  <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
                  <?php } ?>
                  <small class="hidden-md hidden-lg"><br /><?php echo $column_model; ?>: <?php echo $product['model']; ?></small>
                  <small class="hidden-md hidden-lg"><br /><?php echo $column_price; ?>: <?php echo $product['price']; ?></small><br />
				  <a class="btn btn-default btn-tiny hidden-md hidden-lg" style="margin-top:5px;" onclick="cart.remove('<?php echo $product['cart_id']; ?>');"><?php echo $button_remove; ?></a>
                  </td>
                <td class="hidden-xs hidden-sm"><?php echo $product['model']; ?></td>
                <td>
                
                <input type="number" min="1" step="1" name="quantity[<?php echo $product['cart_id']; ?>]" value="<?php echo $product['quantity']; ?>" class="form-control qty-form" />
                    </td>
                <td class="text-right price-cell hidden-xs hidden-sm"><?php echo $product['price']; ?></td>
                <td class="text-right total-cell"><?php echo $product['total']; ?></td>
              </tr>
              <?php } ?>
              <?php foreach ($vouchers as $voucher) { ?>
              <tr>
                <td class="text-center hidden-xs hidden-sm">
				<a onclick="voucher.remove('<?php echo $voucher['key']; ?>');" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="product-remove"><i class="fa fa-times"></i></a>
				</td>
                <td colspan="2" class="text-left">
                <?php echo $voucher['description']; ?><br>
				<a class="btn btn-default btn-tiny hidden-md hidden-lg" style="margin-top:5px;" onclick="voucher.remove('<?php echo $voucher['key']; ?>');"><?php echo $button_remove; ?></a>
                </td>
                <td class="hidden-xs hidden-sm"></td>
                <td class="text-left"><div class="input-group btn-block" style="max-width: 200px;">
					<input type="number" value="1" disabled="disabled" class="form-control qty-form" />
                    </td>
				<td class="hidden-xs hidden-sm"></td>
                <td class="text-right"><?php echo $voucher['amount']; ?></td>
              </tr>
              <?php } ?>
              
            </tbody>
            
          </table>

          <div class="row margin-b30">
          <div class="col-xs-12 text-right"><button type="submit" class="btn btn-primary"><?php echo $button_update; ?></button></div>
          </div>
          
          
        </form>
        
        <div class="row">
        
        <div class="col-sm-7">
            <div class="row cart-modules">
                <?php if ($modules) { ?>
                    <?php foreach ($modules as $module) { ?>
                        <div class="col-sm-6 margin-b30">
                        	<?php echo $module; ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        
        <div class="col-sm-5">
            <div class="totals-slip">
            	<div class="table-holder margin-b25">
                <table class="table table-bordered total-list margin-b0">
                <?php foreach ($totals as $total) { ?>
                <tr>
                <td><b><?php echo $total['title']; ?>:</b></td>
                <td class="text-right"><?php echo $total['text']; ?></td>
                </tr>
                <?php } ?>
                </table>
                </div>
                <a href="<?php echo $checkout; ?>" class="btn btn-lg btn-contrast btn-block"><?php echo $button_checkout; ?></a>
            </div>
        </div>
        
        </div>
      
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?> 