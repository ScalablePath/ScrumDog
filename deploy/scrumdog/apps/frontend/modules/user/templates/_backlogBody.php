    <tbody id="backlog_body">
    <?php $i=1; foreach($tasks as $task): ?>
      <tr<?php if($i%2==0):?> class="alt"<?php endif;?>>
        <td><a href="<?php echo(url_for('@project_task?task_id='.$task->getId())); ?>"><?php echo($task->getName()); ?></a></td>
		<td><a href="<?php echo(url_for('@project_dashboard?project_id='.$task->getProjectId())); ?>"><?php echo($task->Project->getName()); ?></a></td>
		<td><?php echo $task->getPriorityText();?></td>
		<td><?php echo $task->getStatusText();?></td>
		<td><?php echo $task->getEstimatedHours();?></td>
		<td class="actions">
			<span id="backlog_edit-<?php echo $task->getId()?>" class="backlog_edit edit_button icon edit">edit</span>
		</td>
      </tr>

	<!-- hidden form -->

	<tr id="form_row-<?php echo $task->getId()?>" class="hide<?php if($i%2==0):?> alt<?php endif;?>">
        <td><input id="name-<?php echo $task->getId()?>" autocomplete="off" name="task[name]" class="text" type="text" style="width: 100%;" value="<?php echo($task->getName()); ?>" /></td>
		<td><input readonly="readonly" autocomplete="off" name="task[name]" class="text" type="text" style="width: 100%;" value="<?php echo($task->Project->getName()); ?>" /></td>		
		<td>
			<select id="pri-<?php echo $task->getId()?>" name="task[priority]" autocomplete="off">
			<?php foreach(SdTaskTable::$priorityArr as $k => $v): ?>
				<option <?php if($task->getPriority()==$k):?>selected="selected" <?php endif; ?>value="<?php echo $k;?>"><?php echo $v;?></option>
			<?php endforeach; ?>
			</select>
		</td>
		<td>
			<select id="status-<?php echo $task->getId()?>" name="task[status]" autocomplete="off">
			<?php foreach(SdTaskTable::$statusArr as $k => $v): ?>
				<option <?php if($task->getStatus()==$k):?>selected="selected" <?php endif; ?>value="<?php echo $k;?>"><?php echo $v;?></option>
			<?php endforeach; ?>
			</select>
		</td>
		 <td><input id="eh-<?php echo $task->getId()?>" name="task[estimated_hours]" class="eh numeric text" type="text"  autocomplete="off" style="width: 35px;" value="<?php echo($task->getEstimatedHours()); ?>"/></td>
		<td class="actions"><span id="backlog_save-<?php echo $task->getId()?>" class="button backlog_save save_button">save</span> <span id="backlog_cancel-<?php echo $task->getId()?>" class="button secondary backlog_cancel cancel_button">cancel</span></td>
	</tr>
    <?php $i++; endforeach; ?>
    </tbody>	