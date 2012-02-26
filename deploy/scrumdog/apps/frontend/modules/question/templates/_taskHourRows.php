<?php foreach($hoursCollection as $taskHours): ?>
	
	<?php if ( $isOwner ):?>
			<tr id="task-<?php echo($totalTasks); ?>">
				<td><input style="width:40px;" id="work_task_id-<?php echo($totalTasks); ?>" class="taskid-<?php echo($mode); ?>" type="text" readonly name="questions[<?php echo($mode); ?>][tasks][]" value="<?php echo($taskHours->getTaskId()); ?>" autocomplete="off"/></td>
				<td><input style="width:300px;" id="work_task_name-<?php echo($totalTasks); ?>" type="text" readonly value="<?php echo($taskHours->Task->getName()); ?>" autocomplete="off"/></td>
				<td><input style="width:30px;" id="work_task_hours-<?php echo($totalTasks); ?>"type="text" class="numeric hours" name="questions[<?php echo($mode); ?>][hours][]" value="<?php echo($taskHours->getHours()); ?>" autocomplete="off"/></td>
				<td class="actions"><span class="icon edit" title="Edit">Edit</span>&nbsp;<span class="icon delete" title="Delete">Delete</span></td>
			</tr>
	<?php else:?>
			<tr id="task-<?php echo($totalTasks); ?>">
				<td><?php echo($taskHours->getTaskId()); ?></td>
				<td><?php echo($taskHours->Task->getName()); ?></td>
				<td><?php echo($taskHours->getHours()); ?></td>
				<td class="actions">&nbsp;</td>
			</tr>
	<?php endif; ?>
	
<?php $totalTasks++; endforeach; ?>