<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
	<tr>
    	<td class="text-left">KEYWORD</td>
		<td class="text-left">REDIRECT URL</td>
		<td class="text-right">DATE ADDED</td>
		<td class="text-center">ACTION</td>
    </tr>
</thead>
<tbody>
    <?php if ($records) { ?>
		<?php foreach ($records as $record){ ?>
			<tr>
			<td class="text-left"><?php echo $record['keyword'] ?></td>
			<td class="text-left"><?php echo $record['redirect_url']; ?></td>
			<td class="text-right"><?php echo $record['date_added']; ?></td>
			<td class="text-center">	
				<button type="button" class="btn btn-warning" onclick="deleteKeyword('<?php echo $record['id']; ?>')"><i class="fa fa-thrash" aria-hidden="true"></i> Delete</button>
			</td>
			</tr>
		<?php } ?>
    <?php }else { ?>
    		<tr>
              <td class="text-center" colspan="4">No Records Found</td>
            </tr> 
    <?php } ?>
</tbody>    

</table>
</div>