<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
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
      
      <?php if ($products) { ?>
        <table class="table table-bordered">
          <thead>
            <tr>
              <td class="remove-cell"></td>
              <td></td>
              <td><?php echo $column_name; ?></td>
              <td class="hidden-xs hidden-sm"><?php echo $column_model; ?></td>
              <td class="text-right hidden-xs hidden-sm"><?php echo $column_stock; ?></td>
              <td class="text-right price-cell hidden-xs hidden-sm"><?php echo $column_price; ?></td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product) { ?>
            <tr>
            <td class="remove-cell text-center"><a href="<?php echo $product['remove']; ?>" title="<?php echo $button_remove; ?>" class="product-remove"><i class="fa fa-times"></i></a></td>
              <td class="image">
              <?php if ($product['thumb']) { ?>
                <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" /></a>
                <?php } ?>
                </td>
              <td>
              <a class="hover_uline" href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
              <span class="display-block hidden-md hidden-lg">
                <?php if (!$product['special']) { ?>
                  <?php echo $product['price']; ?>
                  <?php } else { ?>
                  <span class="price"><?php echo $product['special']; ?> </span><span class="price-old"><?php echo $product['price']; ?></span>
                  <?php } ?>
                  </span>
                  <span class="display-block hidden-md hidden-lg"><?php echo $product['stock']; ?></span>
                </span>
                </td>
              <td class="hidden-xs hidden-sm"><?php echo $product['model']; ?></td>
              <td class="text-right hidden-xs hidden-sm"><?php echo $product['stock']; ?></td>
              <td class="text-right price-cell hidden-xs hidden-sm"><?php if ($product['price']) { ?>
                
                  <?php if (!$product['special']) { ?>
                  <?php echo $product['price']; ?>
                  <?php } else { ?>
                  <span class="price"><?php echo $product['special']; ?></span><span class="price-old"><?php echo $product['price']; ?></span>
                  <?php } ?>
                
                <?php } ?></td>
              <td class="text-right"><button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');" class="btn btn-sm btn-primary"><?php echo $button_cart; ?></button>
                </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>

      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>