		 <div class="table-responsive">
			<table class="table table-bordered table-hover">
			<thead>
				<tr>
				<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected200\']').prop('checked', this.checked);" /></td>
				<td class="text-left"><?php echo $column_error_url; ?> <span class="label label-success">200</span> & <?php echo $column_redirect_url; ?></td>
				<td class="text-center"><?php echo $column_hits; ?></td>
				<td class="text-center"><?php echo $column_redirect_hits; ?></td>
				<td class="text-right"><?php echo $column_date; ?></td>
			  </tr>
			</thead>
			<tbody>
				<?php if ($records) { ?>
				<?php foreach ($records as $record) { ?>
				<tr>
				<td style="text-align: center;"><?php if ($record['selected200']) { ?>
					<input type="checkbox" name="selected200[]" value="<?php echo $record['id']; ?>" checked="checked" />
					<?php } else { ?>
					<input type="checkbox" name="selected200[]" value="<?php echo $record['id']; ?>" />
					<?php } ?>
				</td>
								
				<td class="text-left col-sm-6" style="word-break:break-all;">
					<?php echo $record['error']; ?> 
					<div class="input-group">
						<input class="form-control col-sm-3" name="redirect" id="redirect" value="<?php echo $record['redirect']; ?>" onblur="updateredirect(this.value,'<?php echo $record['id']; ?>')">
						<div class="input-group-addon"><?php echo $record['type']; ?></div>
					</div>
					<div id="msg<?php echo $record['id']; ?>"></div>
				</td>
				<td class="text-center"><?php echo $record['hits']; ?></td>
				<td class="text-center"><?php echo $record['redirect_hits']; ?></td>
				<td class="text-right">
					<span class="label label-primary">First</span> <?php echo $record['date_added']; ?><br /><br />
					<span class="label label-primary">Last</span> <?php echo $record['date_modified']; ?>
				</td>
				</tr>
				<?php } ?>
				<?php } else { ?>
				<tr><td class="text-center" colspan="7">No Records Found</td></tr>
				<?php } ?>				
			</tbody>
			</table>
		</div>
		
		<div class="row">
		  <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
		  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
		</div>

