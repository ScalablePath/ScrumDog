    <tbody id="backlog_body" class="search-table">
    <? $i=1; foreach($tasks as $task): ?>
      <tr class="task-search-row" id="search_row-<? echo($task->getId()); ?>"<?php if($i%2==0):?> class="alt"<?php endif;?>>
        <td class="name" id="search_row_name-<? echo($task->getId()); ?>"><? echo($task->getName()); ?></td>
		<td><?php echo is_null($task->getUserId()) ? '-unassigned-' : $projectUserArray[$task->getUserId()]; ?></td>
		<td><?=$task->getPriorityText();?></td>
		<td><?=$task->getStatusText();?></td>
<!--		<td><?=$task->getEstimatedHours();?></td> -->
		<td>&nbsp;</td>
      </tr>
    <? $i++; endforeach; ?>
    </tbody>	