<ul class="nav nav-tabs" id="languages">
	<?php foreach ($languages as $language) { ?>
		<li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php echo $language['name']; ?></a></li>
	<?php } ?>
</ul>

<div class="tab-content"> <!-- language tab content -->
	<?php foreach ($languages as $language) { ?>
		<div class="tab-pane" id="language<?php echo $language['language_id']; ?>">	
			<table class="table table-hover">
				<thead>
					<tr>
						<td>Type</td>
						<td class="col-sm-6">Status</td>
						<td class="text-right">Total Items</td>
						<td class="text-right">Total Available Keywords</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Product URL Keyword</td>
						<td>
							<div class="progress">
							  <div class="progress-bar progress-bar-success col-sm-12" role="progressbar" aria-valuenow="<?php echo $percent_product_count[$language['language_id']]; ?>"
							  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percent_product_count[$language['language_id']]; ?>%">
								<?php echo $percent_product_count[$language['language_id']]; ?>%
							  </div>
							</div>
						</td>
						<td class="text-right"><?php echo $total_product_count[$language['language_id']]; ?></td>
						<td class="text-right"><?php echo $available_product_count[$language['language_id']]; ?></td>
					</tr>
					<tr>
						<td>Category URL Keyword</td>
						<td>
							<div class="progress">
							  <div class="progress-bar progress-bar-success col-sm-12" role="progressbar" aria-valuenow="<?php echo $percent_category_count[$language['language_id']]; ?>"
							  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percent_category_count[$language['language_id']]; ?>%">
								<?php echo $percent_category_count[$language['language_id']]; ?>%
							  </div>
							</div>
						</td>
						<td class="text-right"><?php echo $total_category_count[$language['language_id']]; ?></td>
						<td class="text-right"><?php echo $available_category_count[$language['language_id']]; ?></td>
					</tr>
					<tr>
						<td>Manufacturer URL Keyword</td>
						<td>
							<div class="progress">
							  <div class="progress-bar progress-bar-success col-sm-12" role="progressbar" aria-valuenow="<?php echo $percent_brand_count[$language['language_id']]; ?>"
							  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percent_brand_count[$language['language_id']]; ?>%">
								<?php echo $percent_brand_count[$language['language_id']]; ?>%
							  </div>
							</div>
						</td>
						<td class="text-right"><?php echo $total_brand_count[$language['language_id']]; ?></td>
						<td class="text-right"><?php echo $available_brand_count[$language['language_id']]; ?></td>
					</tr>
					<tr>
						<td>Information URL Keyword</td>
						<td>
							<div class="progress">
							  <div class="progress-bar progress-bar-success col-sm-12" role="progressbar" aria-valuenow="<?php echo $percent_information_count[$language['language_id']]; ?>"
							  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percent_information_count[$language['language_id']]; ?>%">
								<?php echo $percent_information_count[$language['language_id']]; ?>%
							  </div>
							</div>
						</td>
						<td class="text-right"><?php echo $total_information_count[$language['language_id']]; ?></td>
						<td class="text-right"><?php echo $available_information_count[$language['language_id']]; ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>
</div> <!-- language tab content end-->	
<script type="text/javascript"><!--
$('#languages a:first').tab('show');
//--></script>
