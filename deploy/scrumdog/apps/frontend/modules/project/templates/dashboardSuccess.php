<?php slot('page_title') ?><?php echo($project->getName()); ?> : Project Dashboard<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
  	<div class="box">
  	<span id="create-task-button" class="button floatright">Create Task</span>
	<h2>Project Backlog</h2>
	<form autocomplete="off" onSubmit="return false">
	<table id="project_backlog_table-<?=$project_id?>" class="backlog_table">
		<thead>
		  <tr>
			<th class="sortable<?php if(isset($sort['name'])) echo(' '.$sort['name']); ?>">
				<span id="sort-name">Name</span><br />
				<input id="filter-name" name="filter[name]" class="text" type="text" autocomplete="off" style="width: 99%;" value="<?php echo $filters['name']; ?>"/>		
			</th>
			<th class="sortable<?php if(isset($sort['business_value'])) echo(' '.$sort['business_value']); ?>">
				<span id="sort-bv">Business Value</span><br />
			<select id="filter-bv" name="filter[business_value]" autocomplete="off">
					<option value="">Any</option>
					<?php foreach(SdTaskTable::$businessValueArr as $k => $v):	?>
						<option <?php if($filters['business_value']==(string)$k):?>selected="selected" <?php endif;?>value="<?=$k;?>"><?=$v;?></option>
					<?php endforeach; ?>
				</select>
			</th>
			<th class="sortable<?php if(isset($sort['estimated_hours'])) echo(' '.$sort['estimated_hours']); ?>">
				<span id="sort-eh">Estimated Hours</span><br />
				<select id="filter-eh" name="filter[estimated_hours]" autocomplete="off">
					<option value="">Any</option>
					<?php foreach(SdTaskTable::$hoursRangeArr as $k => $v):	?>
						<option <?php if($filters['estimated_hours']==(string)$v):?>selected="selected" <?php endif;?>value="<?=$v;?>"><?=$v;?></option>
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
		<?php include_component('project', 'backlogBody', array('project' => $project, 'filters' => $filters, 'sort' => $sort)) ?>
	</table>
	</form>
	<!-- Start Assign Dialog -->
	<div class="hide" id="assign_dialog">
		<?php if($hasActiveSprints): ?>
		<form autocomplete="off">
			<select id="assign_select">
				<?php foreach($activeSprints as $sprint): ?>
					<option value="<?=$sprint->getId()?>"<?php if($sprint->current==1): ?> selected="selected"<?php endif; ?>><?=$sprint->getName()?></option>
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
	<!-- End Assign Dialog -->
	
	<!-- Start Create Dialog -->
	<div class="hide form" id="create-task-dialog">
	<?php include_component('task', 'createDialog'); ?>
	</div>
	<!-- End Create Dialog -->
	
	<script type="text/javascript">
		$(document).ready(function () {
			var filters = <?php echo(json_encode($sf_data->getRaw('filters'))); ?>;
			var sort = <?php echo(json_encode($sf_data->getRaw('sort'))); ?>;
			backlog = new Backlog(filters, sort);
			backlog.mode = 'backlog';
		});
	</script>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Tip</h2>
	<p>The <b>project backlog</b> is where you queue up tasks that you would like to be completed at some point in the future.
The highest priority tasks in the project backlog will be assigned to a sprint in order to be worked on.</p>
  </div>
</div>