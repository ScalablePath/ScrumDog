    <tbody id="backlog_body" class="search-table">
    <?php $i=1; foreach($tasks as $task): ?>
      <tr class="task-search-row" id="search_row-<?php echo($task->getId()); ?>"<?php if($i%2==0):?> class="alt"<?php endif;?>>
        <td class="name" id="search_row_name-<?php echo($task->getId()); ?>"><?php echo($task->getName()); ?></td>
		<td><?php echo is_null($task->getUserId()) ? '-unassigned-' : $projectUserArray[$task->getUserId()]; ?></td>
		<td><?=$task->getPriorityText();?></td>
		<td><?=$task->getStatusText();?></td>
<!--		<td><?=$task->getEstimatedHours();?></td> -->
		<td>&nbsp;</td>
      </tr>
    <?php $i++; endforeach; ?>
    </tbody>	