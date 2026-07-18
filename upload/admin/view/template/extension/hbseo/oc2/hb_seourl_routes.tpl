<ul class="nav nav-tabs" id="r-languages">
	<?php foreach ($languages as $language) { ?>
		<li><a href="#r-language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php echo $language['name']; ?></a></li>
	<?php } ?>
</ul>

<div class="tab-content"> <!-- language tab content -->
	<?php foreach ($languages as $language) { ?>
		<div class="tab-pane" id="r-language<?php echo $language['language_id']; ?>">	
		<table class="table table-bordered table-hover">
		<thead>
			<tr>
			<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
			<td class="text-left">Routes</td>
			<td class="text-left">Keyword</td>
			<td class="text-right">Date Added</td>
		  </tr>
		</thead>
		<tbody>
			<?php if ($records) { ?>
			<?php foreach ($records[$language['language_id']] as $record) { ?>
			<tr>
			<td style="text-align: center;"><input type="checkbox" name="selected[]" value="<?php echo $record['id']; ?>" /></td>
			<td class="text-left"><?php echo $record['route']; ?> </td>
			<td class="text-left"><?php echo $record['keyword']; ?> </td>
			<td class="text-right"><?php echo $record['date_added']; ?></td>
			</tr>
			<?php } ?><!--endfor-->
			<?php } else { ?>
			<tr><td class="text-center" colspan="4">No Records Found</td></tr>
			<?php } ?>	<!--endif-->
		</tbody>
		</table>
		</div>
	<?php } ?>	<!--endfor-->
	</div> <!-- language tab content end-->	
<script type="text/javascript"><!--
$('#r-languages a:first').tab('show');
//--></script>

