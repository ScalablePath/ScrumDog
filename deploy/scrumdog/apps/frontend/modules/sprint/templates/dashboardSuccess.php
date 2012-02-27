<?php slot('page_title') ?>Sprint Dashboard<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<div class="box">
	<span id="create-task-button" class="button floatright">Create Task</span>
	<h2>Sprint Backlog</h2>
	<form autocomplete="off" onSubmit="return false">
	<table id="project_backlog_table-<?php echo $sprint_id?>" class="backlog_table">
		<thead>
		  <tr>
			<th class="sortable<?php if(isset($sort['name'])) echo(' '.$sort['name']); ?>">
				<span id="sort-name">Name</span><br />
				<input id="filter-name" name="filter[name]" class="text" type="text" autocomplete="off" style="width: 99%;" value="<?php echo $filters['name']; ?>"/>		
			</th>
			<th class="sortable<?php if(isset($sort['user_id'])) echo(' '.$sort['user_id']); ?>">
				<span id="sort-user">Assigned To</span><br />
			<select id="filter-user" name="filter[user_id]" autocomplete="off">
					<option value="">Anyone</option>
					<option <?php if($filters['user_id']=='null'):?>selected="selected" <?php endif;?>value="null">-unassigned-</option>
					<?php foreach($projectUserArray as $k => $v):	?>
						<option <?php if($filters['user_id']==(string)$k):?>selected="selected" <?php endif;?>value="<?php echo $k;?>"><?php echo $v;?></option>
					<?php endforeach; ?>
				</select>
			</th>
			<th class="sortable<?php if(isset($sort['priority'])) echo(' '.$sort['priority']); ?>">
				<span id="sort-pri">Priority</span><br />
				<select id="filter-pri" name="filter[priority]" autocomplete="off">
					<option value="">Any</option>
					<?php foreach(SdTaskTable::$priorityArr as $k => $v): ?>
						<option <?php if($filters['priority']==(string)$k):?>selected="selected" <?php endif; ?>value="<?php echo $k;?>"><?php echo $v;?></option>
					<?php endforeach; ?>
				</select>
			</th>
			<th class="sortable<?php if(isset($sort['status'])) echo(' '.$sort['status']); ?>">
				<span id="sort-status">Status</span><br />
				<select id="filter-status" name="filter[status]" autocomplete="off">
					<option value="">Any</option>
					<option <?php if($filters['status']=='not-completed'):?>selected="selected" <?php endif; ?>value="not-completed">Not Completed</option>
					<option <?php if($filters['status']=='not-accepted'):?>selected="selected" <?php endif; ?>value="not-accepted">Not Accepted</option>
					<?php foreach(SdTaskTable::$statusArr as $k => $v): ?>
						<option <?php if($filters['status']==(string)$k):?>selected="selected" <?php endif; ?>value="<?php echo $k;?>"><?php echo $v;?></option>
					<?php endforeach; ?>
				</select>
			</th>
			<th class="sortable<?php if(isset($sort['estimated_hours'])) echo(' '.$sort['estimated_hours']); ?>">
				<span id="sort-eh">Estimated Hours</span><br />
				<select id="filter-eh" name="filter[estimated_hours]" autocomplete="off">
					<option value="">Any</option>
					<?php foreach(SdTaskTable::$hoursRangeArr as $k => $v):	?>
						<option <?php if($filters['estimated_hours']==(string)$v):?>selected="selected" <?php endif;?>value="<?php echo $v;?>"><?php echo $v;?></option>
					<?php endforeach; ?>
				</select>
			</th>
			<th class="actions">
				<br />
				<span class="clear_button icon reset" title="Reset">Reset</span>
				<span class="refresh_button icon refresh" title="Refresh">Refresh</span>
			</th>
		</tr>
		</thead>
		<?php include_component('sprint', 'backlogBody', array('project' => $project, 'sprint' => $sprint, 'projectUserArray' => $projectUserArray, 'filters' => $filters, 'sort' => $sort)); ?>
	</table>
	</form>
	<div class="hide" id="assign_dialog">
		<?php if($hasActiveSprints): ?>
		<form autocomplete="off">
			<select id="assign_select">
				<option value="">Project Backlog</option>
				<?php foreach($activeSprints as $mySprint): ?>
					<?php if($sprint->getId()!=$mySprint->getId()): ?>
					<option value="<?php echo $mySprint->getId()?>"<?php if($mySprint->current==1): ?> selected="selected"<?php endif; ?>><?php echo $mySprint->getName()?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
		</form>
		<p>Note: Any subtasks will also be moved along with this task.</p>
		<?php else: ?>
		<p>You must <a href="<?php echo(url_for('@project_createsprint?project_id='.$project_id)); ?>">create a sprint</a> before you can move a task.</p>
		<?php endif; ?>
	</div>
	<div class="hide" id="estimate_dialog"><p>You must first estimate the hours.</p></div>
	</div>
	
	<!-- Start Create Dialog -->
	<div class="hide form" id="create-task-dialog">
	<?php include_component('task', 'createDialog'); ?>
	</div>
	<!-- End Create Dialog -->
	
	<script type="text/javascript">
		$(document).ready(function () {
			var filters = <?php echo(json_encode($sf_data->getRaw('filters'))); ?>;
			var sort = <?php echo(json_encode($sf_data->getRaw('sort'))); ?>;
			sprint_backlog = new SprintBacklog(filters, sort);
			taskSaveSuccessFunc = 'SBL_taskSaveSuccess';
		});
	</script>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Tip</h2>
	<p>The <b>sprint backlog</b> is where tasks are queued to be completed during the timeframe of the current sprint.
If a person has completed all of the tasks assigned to them, they can come here to find a suitable task to work on based on the task priority.
  </div>
</div>
