<?php slot('page_title') ?>My Dashboard<?php end_slot() ?>
<div id="main">
	<div class="box">
		<h2>My Tasks</h2>
		<form autocomplete="off" onSubmit="return false">
			<table id="project_backlog_table-<?php echo $user->getId();?>" class="backlog_table">
				<thead>
				  <tr>
					<th class="sortable<?php if(isset($sort['name'])) echo(' '.$sort['name']); ?>">
						<span id="sort-name">Name</span><br />
						<input id="filter-name" name="filter[name]" class="text" type="text" autocomplete="off" style="width: 99%;" value="<?php echo $filters['name']; ?>"/>		
					</th>
					<th class="sortable<?php if(isset($sort['project_id'])) echo(' '.$sort['project_id']); ?>">
						<span id="sort-proj">Project</span><br />
						<select id="filter-proj" name="filter[project_id]" autocomplete="off">
							<option value="">Any</option>
							<? foreach($userProjects as $project): ?>
								<option <?php if($filters['project_id']==(string)$project->getId()):?>selected="selected" <?php endif; ?>value="<?php echo $project->getId();?>"><?php echo $project->getName();?></option>
							<? endforeach; ?>
						</select>
					</th>
					<th class="sortable<?php if(isset($sort['priority'])) echo(' '.$sort['priority']); ?>">
						<span id="sort-pri">Priority</span><br />
						<select id="filter-pri" name="filter[priority]" autocomplete="off">
							<option value="">Any</option>
							<? foreach(SdTaskTable::$priorityArr as $k => $v): ?>
								<option <? if($filters['priority']==(string)$k):?>selected="selected" <? endif; ?>value="<?=$k;?>"><?=$v;?></option>
							<? endforeach; ?>
						</select>
					</th>
					<th class="sortable<?php if(isset($sort['status'])) echo(' '.$sort['status']); ?>">
						<span id="sort-status">Status</span><br />
						<select id="filter-status" name="filter[status]" autocomplete="off">
							<option value="">Any</option>
							<option <? if($filters['status']=='not-completed'):?>selected="selected" <? endif; ?>value="not-completed">Not Completed</option>
							<option <? if($filters['status']=='not-accepted'):?>selected="selected" <? endif; ?>value="not-accepted">Not Accepted</option>
							<? foreach(SdTaskTable::$statusArr as $k => $v): ?>
								<option <? if($filters['status']==(string)$k):?>selected="selected" <? endif; ?>value="<?=$k;?>"><?=$v;?></option>
							<? endforeach; ?>
						</select>
					</th>
					<th class="sortable<?php if(isset($sort['estimated_hours'])) echo(' '.$sort['estimated_hours']); ?>">
						<span id="sort-eh">Estimated Hours</span><br />
						<select id="filter-eh" name="filter[estimated_hours]" autocomplete="off">
							<option value="">Any</option>
							<? foreach(SdTaskTable::$hoursRangeArr as $k => $v):	?>
								<option <?php if($filters['estimated_hours']==(string)$v):?>selected="selected" <?php endif;?>value="<?=$v;?>"><?=$v;?></option>
							<? endforeach; ?>
						</select>
					</th>
					<th class="actions">
						<br />
						<span class="clear_button icon reset" title="Reset">Reset</span>
						<span class="refresh_button icon refresh" title="Refresh">Refresh</span>
					</th>
				</tr>
				</thead>
				<?php include_component('user', 'backlogBody', array('filters' => $filters, 'sort' => $sort)); ?>
			</table>
		</form>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			var filters = <?php echo(json_encode($sf_data->getRaw('filters'))); ?>;
			var sort = <?php echo(json_encode($sf_data->getRaw('sort'))); ?>;
			user_backlog = new UserBacklog(filters, sort);
		});
	</script>
</div>
<div id="sidebar">
    <?php include_component('user', 'projects') ?>
</div>