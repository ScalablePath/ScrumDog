<?php slot('page_title') ?><?php echo($project->getName()); ?> : Project Archive<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<div class="box">
	<h1>Archived Tasks</h1>
	<form autocomplete="off" onSubmit="return false">
	<table id="project_backlog_table-<?php echo $project_id?>" class="backlog_table">
		<thead>
		  <tr>
			<th class="sortable<?php if(isset($sort['name'])) echo(' '.$sort['name']); ?>">
				<span id="sort-name">Name</span><br />
				<input id="filter-name" name="filter[name]" class="text" type="text" autocomplete="off" style="width: 100%;" value="<?php echo $filters['name']; ?>"/>		
			</th>
			<th class="sortable<?php if(isset($sort['business_value'])) echo(' '.$sort['business_value']); ?>">
				<span id="sort-bv">Business Value</span><br />
			<select id="filter-bv" name="filter[business_value]" autocomplete="off">
					<option value="">any</option>
					<?php foreach(SdTaskTable::$businessValueArr as $k => $v):	?>
						<option <?php if($filters['business_value']==(string)$k):?>selected="selected" <?php endif;?>value="<?php echo $k;?>"><?php echo $v;?></option>
					<?php endforeach; ?>
				</select>
			</th>
			<th class="sortable<?php if(isset($sort['estimated_hours'])) echo(' '.$sort['estimated_hours']); ?>">
				<span id="sort-eh">Estimated Hours</span><br />
				<select id="filter-eh" name="filter[estimated_hours]" autocomplete="off">
					<option value="">any</option>
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
		<?php include_component('project', 'archiveBody', array('project' => $project, 'filters' => $filters, 'sort' => $sort)) ?>
	</table>
	</form>
	<div class="hide" id="assign_dialog">
		<?php if($hasActiveSprints): ?>
		<form autocomplete="off">
			<select id="assign_select">
				<option value="">Project Backlog</option>
				<?php foreach($activeSprints as $sprint): ?>
					<option value="<?php echo $sprint->getId()?>"<?php if($sprint->current==1): ?> selected="selected"<?php endif; ?>><?php echo $sprint->getName()?></option>
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
	<script type="text/javascript">
		$(document).ready(function () {
			var filters = <?php echo(json_encode($sf_data->getRaw('filters'))); ?>;
			var sort = <?php echo(json_encode($sf_data->getRaw('sort'))); ?>;
			archive_backlog = new ArchiveBacklog(filters, sort);
		});
	</script>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Note</h2>
	<p>Tasks are never completely deleted.  Instead they are moved to the <b>archive</b> so that they are always available to be reviewed or re-activated.
  </div>
</div>