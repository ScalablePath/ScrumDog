    <tbody id="backlog_body">
    <?php $i=1; foreach($subTasks as $task): ?>
      <tr<?php if($i%2==0):?> class="alt"<?php endif;?>>
        <td><a href="<?php echo(url_for('@project_task?task_id='.$task->getId())); ?>"><?php echo($task->getName()); ?></a></td>
		<td><?php echo is_null($task->getUserId()) ? '-unassigned-' : $projectUserArray[$task->getUserId()]; ?></td>
		<td><?=$task->getPriorityText();?></td>
		<td><?=$task->getStatusText();?></td>
		<td><?=$task->getEstimatedHours();?></td>
		<td class="actions">
			<span id="backlog_edit-<?=$task->getId()?>" class="backlog_edit edit_button icon edit" title="Edit">Edit</span>
			<span id="backlog_assign-<?=$task->getId()?>" class="backlog_assign assign_button icon move" title="Move">Move</span>
			<?php if($task->getIsArchived()==0): ?>
			<span id="backlog_archive-<?=$task->getId()?>" class="backlog_archive archive_button icon delete" title="Archive">Archive</span>
			<?php endif; ?>
		</td>
      </tr>

	<!-- hidden form -->

	<tr id="form_row-<?=$task->getId()?>" class="hide<?php if($i%2==0):?> alt<?php endif;?>">
        <td><input id="name-<?=$task->getId()?>" autocomplete="off" name="task[name]" class="text" type="text" style="width: 100%;" value="<?php echo($task->getName()); ?>" autocomplete="off" /></td>
        <td>
			<select id="user-<?=$task->getId()?>" name="task[user_id]" autocomplete="off">
				<option value="">-unassigned-</option>
				<?php foreach($projectUserArray as $k => $v): ?>
					<option <?php if($task->getUserId()==$k):?>selected="selected" <?php endif; ?>value="<?=$k?>"><?=$v?></option>
				<?php endforeach; ?>
			</select>
		</td>
		<td>
			<select id="pri-<?=$task->getId()?>" name="task[priority]" autocomplete="off">
			<?php foreach(SdTaskTable::$priorityArr as $k => $v): ?>
				<option <?php if($task->getPriority()==$k):?>selected="selected" <?php endif; ?>value="<?=$k;?>"><?=$v;?></option>
			<?php endforeach; ?>
			</select>
		</td>
		<td>
			<select id="status-<?=$task->getId()?>" name="task[status]" autocomplete="off">
			<?php foreach(SdTaskTable::$statusArr as $k => $v): ?>
				<option <?php if($task->getStatus()==$k):?>selected="selected" <?php endif; ?>value="<?=$k;?>"><?=$v;?></option>
			<?php endforeach; ?>
			</select>
		</td>
		 <td><input id="eh-<?=$task->getId()?>" name="task[estimated_hours]" class="eh numeric text" type="text"  autocomplete="off" style="width: 35px;" value="<?php echo($task->getEstimatedHours()); ?>"/></td>
		<td class="actions"><span id="backlog_save-<?=$task->getId()?>" class="button backlog_save save_button">save</span> <span id="backlog_cancel-<?=$task->getId()?>" class="button secondary backlog_cancel cancel_button">cancel</span></td>
	</tr>
    <?php $i++; endforeach; ?>
    </tbody>	