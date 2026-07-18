 <div class="table-responsive">
	<table class="table table-bordered table-hover">
	<thead>
		<tr>
		<td class="text-center">ID</td>
		<td class="text-left">OLD PATH</td>
		<td class="text-left">NEW PATH</td>
		<td class="text-center">STATUS</td>
		<td class="text-right">DATE ADDED</td>
	  </tr>
	</thead>
	<tbody>
		<?php if ($records) { ?>
		<?php foreach ($records as $record) { ?>
		<tr>
		<td class="text-center"><?php echo $record['id']; ?></td>
		<td class="text-left"><?php echo $record['old_path']; ?></td>
		<td class="text-left"><?php echo $record['new_path']; ?></td>
		<td class="text-center"><?php echo $record['status']; ?></td>
		<td class="text-right"><?php echo $record['date_added']; ?></td>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr><td class="text-center" colspan="5">No Records Found</td></tr>
		<?php } ?>				
	</tbody>
	</table>
</div>

<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>

