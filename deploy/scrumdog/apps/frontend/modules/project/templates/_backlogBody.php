    <tbody id="backlog_body">
    <? $i=1; foreach($tasks as $task): ?>
      <tr<?php if($i%2==0):?> class="alt"<?php endif;?>>
        <td><a href="<? echo(url_for('@project_task?task_id='.$task->getId())); ?>"><? echo($task->getName()); ?></a></td>
		<td><?=$task->getBusinessValueText();?></td>
		<td><?=$task->getEstimatedHours();?></td>
        <td class="actions">
			<span id="backlog_edit-<?=$task->getId()?>" class="backlog_edit edit_button icon edit" title="Edit">Edit</span>
			<span id="backlog_assign-<?=$task->getId()?>" class="backlog_assign assign_button icon move" title="Move">Move</span>
			<span id="backlog_archive-<?=$task->getId()?>" class="backlog_archive archive_button icon delete" title="Archive">Archive</span>
		</td>
      </tr>

	<!-- hidden form -->
	<tr id="form_row-<?=$task->getId();?>" class="hide<?php if($i%2==0):?> alt<?php endif;?>">
        <td><input id="name-<?=$task->getId();?>" name="task[name]" class="text" type="text" autocomplete="off" style="width: 100%;" value="<? echo($task->getName()); ?>"/></td>
        <td>
			<select id="bv-<?php echo $task->getId();?>" name="task[business_value]" autocomplete="off">
			<? foreach(SdTaskTable::$businessValueArr as $k => $v):	?>
				<option <?php if($task->getBusinessValue()== $k): ?> selected="selected"; <? endif; ?>value="<?=$k;?>"><?=$v;?></option>
			<? endforeach; ?>
			</select>
		</td>
		 <td><input id="eh-<?=$task->getId();?>" name="task[estimated_hours]" autocomplete="off" class="numeric text" type="text" style="width: 35px;" value="<? echo($task->getEstimatedHours()); ?>"/></td>
		<td class="actions"><span id="backlog_save-<?=$task->getId()?>" class="button backlog_save save_button">save</span> <span id="backlog_cancel-<?=$task->getId()?>" class="button secondary backlog_cancel cancel_button">cancel</span></td>
	</tr>
    <? $i++; endforeach; ?>
    </tbody>