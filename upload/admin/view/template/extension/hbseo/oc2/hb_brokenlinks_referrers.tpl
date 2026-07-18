<div class="table-responsive">
<table class="table table-bordered table-hover">
<thead>
	<tr>
    	<td class="text-left"><?php echo $column_referrer; ?></td>
		<td class="text-left"><?php echo $column_useragent; ?></td>
		<td class="text-right"><?php echo $column_ip; ?></td>
        <td class="text-right"><?php echo $column_datetime; ?></td>
		
    </tr>
</thead>
<tbody>
    <?php if ($records) { ?>
		<?php foreach ($referrers as $referrer){ ?>
			<tr>
			<td class="text-left" style="word-break:break-all;"><?php echo $referrer['referrer'] ?></td>
			<td class="text-left" style="word-break:break-all;"><?php echo $referrer['user_agent']; ?></td>
			<td class="text-right"><?php echo $referrer['ip']; ?></td>
			<td class="text-right"><?php echo $referrer['datetime']; ?></td>
			</tr>
		<?php } ?>
    <?php }else { ?>
    		<tr>
              <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
            </tr> 
    <?php } ?>
</tbody>    

</table>
</div>