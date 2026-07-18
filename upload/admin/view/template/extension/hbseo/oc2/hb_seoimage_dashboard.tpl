<table class="table table-hover table-bordered">
	<thead>
	<tr>
		<td class="text-left">Image Table</td>
		<td class="text-center" style="color:green;">Organized</td>
		<td class="text-center" style="color:red;">Unorganized</td>
		<td class="text-center">Action</td>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>Product Main Images</td>
		<td class="text-center"><a class="btn" onclick="show_image_list('product','o');"><span id="count_o_product"><?php echo $org_products_main_total; ?></span></a></td>
		<td class="text-center"><a class="btn" onclick="show_image_list('product','u');"><span id="count_u_product"><?php echo $unorg_products_main_total; ?></span></a></td>
		<td rowspan="2"><a class="btn btn-success col-sm-12" id="button-rename" onclick="startprocess('index.php?route=<?php echo $base_route; ?>/hb_seoimage/generate&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');" data-loading-text="Running..." <?php echo ($hb_seoimage_status)? '':'disabled="disabled"'; ?> ><i class="fa fa-play" aria-hidden="true"></i> START</a></td>
	</tr>
	
	<tr>
		<td>Product Additional Images</td>
		<td class="text-center"><a class="btn" onclick="show_image_list('product_image','o');"><span id="count_o_product_image"><?php echo $org_products_additional_total; ?></span></a></td>
		<td class="text-center"><a class="btn" onclick="show_image_list('product_image','u');"><span id="count_u_product_image"><?php echo $unorg_products_additional_total; ?></span></a></td>
	</tr>
	
	<tr>
		<td>Category Images</td>
		<td class="text-center"><a class="btn" onclick="show_image_list('category','o');"><span id="count_o_category"><?php echo $org_category_total; ?></span></a></td>
		<td class="text-center"><a class="btn" onclick="show_image_list('category','u');"><span id="count_u_category"><?php echo $unorg_category_total; ?></span></a></td>
		<td class="text-center"><a class="btn btn-success col-sm-12" id="button-category-rename" onclick="rename_images('category');" <?php echo ($hb_seoimage_status)? '':'disabled="disabled"'; ?>><i class="fa fa-play" aria-hidden="true"></i> START</a></td>
	</tr>
	
	<tr>
		<td>Brand Images</td>
		<td class="text-center"><a class="btn" onclick="show_image_list('manufacturer','o');"><span id="count_o_manufacturer"><?php echo $org_brand_total; ?></span></a></td>
		<td class="text-center"><a class="btn" onclick="show_image_list('manufacturer','u');"><span id="count_u_manufacturer"><?php echo $unorg_brand_total; ?></span></a></td>
		<td class="text-center"><a class="btn btn-success col-sm-12" id="button-brand-rename" onclick="rename_images('brand');" <?php echo ($hb_seoimage_status)? '':'disabled="disabled"'; ?>><i class="fa fa-play" aria-hidden="true"></i> START</a></td>
	</tr>
	
	<tr>
		<td>Product Option Images</td>
		<td class="text-center"><a class="btn" onclick="show_image_list('option_value','o');"><span id="count_o_option_value"><?php echo $org_option_total; ?></span></a></td>
		<td class="text-center"><a class="btn" onclick="show_image_list('option_value','u');"><span id="count_u_option_value"><?php echo $unorg_option_total; ?></span></a></td>
		<td class="text-center"><a class="btn btn-success col-sm-12" id="button-option-rename" onclick="rename_images('option');" <?php echo ($hb_seoimage_status)? '':'disabled="disabled"'; ?>><i class="fa fa-play" aria-hidden="true"></i> START</a></td>
	</tr>
	</tbody>
</table>
