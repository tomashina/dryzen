<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
	<tr>
		<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
    	<td class="text-left">KEYWORD</td>
		<td class="text-right">DATE ADDED</td>
    </tr>
</thead>
<tbody>
    <?php if ($records) { ?>
		<?php foreach ($records as $record){ ?>
			<tr>
			<td style="text-align: center;"><input type="checkbox" name="selected[]" value="<?php echo $record['id']; ?>" /></td>
			<td class="text-left"><?php echo $record['keyword'] ?></td>
			<td class="text-right"><?php echo $record['date_added']; ?></td>
			</tr>
		<?php } ?>
    <?php }else { ?>
    		<tr>
              <td class="text-center" colspan="3">No Records Found</td>
            </tr> 
    <?php } ?>
</tbody>    

</table>
</div>

<div class="row">
  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>