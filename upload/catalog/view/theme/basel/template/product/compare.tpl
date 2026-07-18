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
      <div class="table-responsive compare-table">
      <table class="table table-bordered">
        <thead>
          <tr>
            <td colspan="<?php echo count($products) + 1; ?>"><strong><?php echo $text_product; ?></strong></td>
          </tr>
        </thead>
        <tbody class="text-center">
          <tr>
            <td><?php echo $button_remove; ?></td>
            <?php foreach ($products as $product) { ?>
            <td class="remove">
            <a href="<?php echo $product['remove']; ?>" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="product-remove"></a>
            </td>
            <?php } ?>
          </tr>
          <tr>
            <td><?php echo $text_name; ?></td>
            <?php foreach ($products as $product) { ?>
            <td class="text-center product">
            <?php if ($product['thumb']) { ?>
              <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="margin-b15" /></br>
              <?php } ?>
              
              <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
              
              <?php if ($product['price']) { ?>
              <br /><span class="compare-price">
              <?php if (!$product['special']) { ?>
              <?php echo $product['price']; ?>
              <?php } else { ?>
              <span class="price-old"><?php echo $product['price']; ?></span> <span class="price"><?php echo $product['special']; ?></span>
              <?php } ?>
              </span>
              <?php } ?>
              
              <input type="button" value="<?php echo $button_cart; ?>" class="btn btn-primary btn-sm catalog_hide" onclick="cart.add('<?php echo $product['product_id']; ?>', '<?php echo $product['minimum']; ?>');" />
              
              </td>
            <?php } ?>
          </tr>
          
          <tr>
            <td><?php echo $text_model; ?></td>
            <?php foreach ($products as $product) { ?>
            <td><?php echo $product['model']; ?></td>
            <?php } ?>
          </tr>
          <tr>
            <td><?php echo $text_manufacturer; ?></td>
            <?php foreach ($products as $product) { ?>
            <td><?php echo $product['manufacturer']; ?></td>
            <?php } ?>
          </tr>
          <tr>
            <td><?php echo $text_availability; ?></td>
            <?php foreach ($products as $product) { ?>
            <td><?php echo $product['availability']; ?></td>
            <?php } ?>
          </tr>
          <?php if ($review_status) { ?>
          <tr>
            <td><?php echo $text_rating; ?></td>
            <?php foreach ($products as $product) { ?>
            <td>
             <div class="rating">
             <span class="rating_stars rating r<?php echo $product['rating']; ?>">
             <i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
             </span>
             </div>
              <?php echo $product['reviews']; ?>
              </td>
            <?php } ?>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_summary; ?></td>
            <?php foreach ($products as $product) { ?>
            <td class="description"><?php echo $product['description']; ?></td>
            <?php } ?>
          </tr>
          <tr>
            <td><?php echo $text_weight; ?></td>
            <?php foreach ($products as $product) { ?>
            <td><?php echo $product['weight']; ?></td>
            <?php } ?>
          </tr>
          <tr>
            <td><?php echo $text_dimension; ?></td>
            <?php foreach ($products as $product) { ?>
            <td><?php echo $product['length']; ?> x <?php echo $product['width']; ?> x <?php echo $product['height']; ?></td>
            <?php } ?>
          </tr>
        </tbody>
        <?php foreach ($attribute_groups as $attribute_group) { ?>
        <thead>
          <tr>
            <td colspan="<?php echo count($products) + 1; ?>"><strong><?php echo $attribute_group['name']; ?></strong></td>
          </tr>
        </thead>
        <?php foreach ($attribute_group['attribute'] as $key => $attribute) { ?>
        <tbody class="text-center">
          <tr>
            <td><?php echo $attribute['name']; ?></td>
            <?php foreach ($products as $product) { ?>
            <?php if (isset($product['attribute'][$key])) { ?>
            <td><?php echo $product['attribute'][$key]; ?></td>
            <?php } else { ?>
            <td></td>
            <?php } ?>
            <?php } ?>
          </tr>
        </tbody>
        <?php } ?>
        <?php } ?>
       </table>
      </div>
      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>
