	<table>
		<tr>
			<th width="15%">Time</th>
			<th width="15%">User</th>
			<th width="15%">Type</th>
			<th width="35%">Old Value</th>
			<th width="35%">New Value</th>
		</tr>
		<?php $i=1; foreach($histories as $history): ?>
		<tr<?php if($i%2==0):?> class="alt"<?php endif;?>>
			<td width="15%"><?php echo date('n/j g:ia', strtotime($history->getCreatedAt())); ?></td>
			<td width="15%"><?php echo $history->User->full_name; ?></td>
			<td width="15%"><?php echo str_replace('_', ' ', $history->change_type); ?></td>
			<td width="35%">
				<?php if(!strstr($history->change_type, 'file')): ?>
					<?php echo $history->previous_value; ?>
				<?php else: ?>
					<?php echo html_entity_decode($history->previous_value); ?>
				<?php endif; ?>
			</td>
			<td width="35%">
				<?php if(!strstr($history->change_type, 'file')): ?>
					<?php echo $history->new_value; ?>
				<?php else: ?>
					<?php echo html_entity_decode($history->new_value); ?>
				<?php endif; ?>
			</td>
		</tr>
		<?php $i++; ?>	
		<?php endforeach ?>
	</table>