<div class="table-responsive">
<table class="table table-bordered table-hover table-condensed">
<?php if ($records) { ?>
	<?php foreach ($records as $record) { ?>
		<tr>
			<td class="text-center"><a href="<?php echo $record['edit']; ?>" target="_blank"><?php echo $record['id']; ?></a></td>
			<td class="text-left"><?php echo $record['image']; ?></td>
		</tr>
	<?php } ?>
<?php } else { ?>
	<tr>
		<td>No Record Found</td>
	</tr>
<?php } ?>
</table>
</div>

<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
