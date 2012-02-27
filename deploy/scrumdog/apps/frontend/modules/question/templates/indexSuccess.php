<?php slot('page_title') ?><?php echo($project->getName()); ?> : Project Questions<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1><a href="<?php echo(url_for('@member_profile?username='.$questionUser->getUsername())); ?>"><?php echo($questionUser->getFullName()); ?></a>'s Daily Questions for <?php echo(date("l M jS", $questionTime)); ?></h1>
	<div id="question-browse">
		<a href="<?php echo(url_for('@project_questions?project_id='.$project_id.'&username='.$userName.'&date='.date("Y-m-d", $prevTime))); ?>"><< Previous Day</a>
		<?php if(!$isCurrentDay): ?>
			<a class="right" href="<?php echo(url_for('@project_questions?project_id='.$project_id.'&username='.$userName.'&date='.date("Y-m-d", $nextTime))); ?>">Next Day >></a>
		<?php endif; ?>
	</div>
	
	<?php if($isOwner):?>
		<form id="question_form" enctype="multipart/form-data" method="post" autocomplete="off" action="<?php echo(url_for('@project_questionsave?project_id='.$project_id.'&username='.$userName.'&date='.$dateString)); ?>">
	<?php endif;?>
	
<?php if($isCurrentDay): ?>
  	<div class="box question">
		<h2>What did you accomplish yesterday (<?php echo date("l", $prevTime); ?>)?</h2>
		<h3>Select the tasks you worked on.</h3>
		<p>Enter the number of hours you spent on each task.</p>
		<table>
			<thead>
				<tr><th>Task ID</th><th>Name</th><th>Hours</th><th>&nbsp;</th></tr>
			</thead>
			<tbody id="yester_body">
			<?php include_partial('taskHourRows', array('hoursCollection' => $yesterHoursCollection, 'totalTasks' => 1000, 'mode' => 'yester', 'isOwner' => $isOwner)); ?>
			</tbody>
		</table>
		
		<?php if($isOwner):?>
			<input id="yester_add" class="add" type="button" value="Add Task" />
		<?php endif;?>
		
		<h3>Describe what you worked on yesterday.</h3>
		<p>Include some details that are not explained in the tasks above.</p>
		
		<?php if($isOwner):?>
			<textarea autocomplete="off" id="yester-work" name="questions[yester-work]" rows="5" cols="35" ><?php if(!is_null($yesterQuestion)):?><?php echo($yesterQuestion->getWork()); ?><?php endif; ?></textarea>
		<?php else:?>
			<div class="show_textarea" >
				<?php if(!is_null($yesterQuestion)) echo $yesterQuestion->getWork(); ?>
			</div>
		<?php endif;?>
		
		<h3>What is the total number of hours you worked on this project yesterday?</h3>
		
		<?php if($isOwner):?>
			<input type="text" id="yester-total" class="numeric total text" name="questions[yester-hours]" autocomplete="off" value="<?php if(!is_null($yesterQuestion)):?><?php echo($yesterQuestion->getHours()); ?><?php endif; ?>" /> hours <span class="required">*</span>
		<?php else:?>
			<div class="show_textarea" >
				<?php if(!is_null($yesterQuestion)) echo $yesterQuestion->getHours(); ?> hours <span class="required">*</span>
			</div>	
		<?php endif;?>
		
		<br />
<!--
		<span id="show_question_history-1" class="show_link">Show question history</span>
-->
	</div>
<?php endif; ?>

  	<div class="box question">
		<?php if($isCurrentDay): ?>
			<h2>What will you accomplish today (<?php echo date("l", $questionTime); ?>)?</h2>
		<?php else: ?>
			<h2>What did you accomplish?</h2>
		<?php endif; ?>
		<?php if($isCurrentDay): ?>
			<h3>Select the tasks you plan to work on.</h3>
		<?php else: ?>
			<h3>Select the tasks you worked on.</h3>
		<?php endif; ?>
		<?php if($isCurrentDay): ?>
			<p>Enter the number of hours you intend to spend on each task.</p>
		<?php else: ?>
			<p>Enter the number of hours you spent on each task.</p>
		<?php endif; ?>
		<table>
			<thead>
				<tr><th>Task ID</th><th>Name</th><th>Hours</th><th>&nbsp;</th></tr>
			</thead>
			<tbody id="today_body">
				<?php include_partial('taskHourRows', array('hoursCollection' => $todayHoursCollection, 'totalTasks' => 2000, 'mode' => 'today', 'isOwner' => $isOwner)); ?>
			</tbody>
		</table>
		
		<?php if($isOwner):?>
			<input id="today_add" class="add" type="button" value="Add Task" />
		<?php endif;?>
		
		<?php if($isCurrentDay): ?>
			<h3>Describe what you will work on today.</h3>
		<?php else: ?>
			<h3>Describe what you worked on.</h3>
		<?php endif; ?>
		<p>Include some details that are not explained in the tasks above.</p>
		
		<?php if($isOwner):?>
			<textarea autocomplete="off" id="today-work" name="questions[today-work]" rows="5" cols="35" ><?php if(!is_null($todayQuestion)):?><?php echo($todayQuestion->getWork()); ?><?php endif; ?></textarea>
		<?php else:?>
			<div class="show_textarea">
				<?php if(!is_null($todayQuestion)) echo $todayQuestion->getWork();?>
			</div>
		<?php endif;?>
		
		<?php if($isCurrentDay): ?>
			<h3>What obstacles are in your way?</h3>
		<?php else: ?>
			<h3>What obstacles were in your way?</h3>
		<?php endif; ?>
		
		<?php if($isOwner):?>
			<textarea autocomplete="off" name="questions[today-obstacles]" rows="5" cols="35" ><?php if(!is_null($todayQuestion)):?><?php echo($todayQuestion->getObstacles()); ?><?php endif; ?></textarea>
		<?php else:?>
			<div class="show_textarea">
				<?php if(!is_null($todayQuestion)) echo $todayQuestion->getObstacles(); ?>
			</div>
		<?php endif;?>
		
		<br />
		<?php if($isCurrentDay): ?>
			<h3>What is the total number of hours you will work on this project today?</h3>
		<?php else: ?>
			<h3>What is the total number of hours you spent on the project?</h3>
		<?php endif; ?>
		
		<?php if($isOwner):?>
			<input type="text" id="today-total" class="numeric total text" name="questions[today-hours]" autocomplete="off" value="<?php if(!is_null($todayQuestion)):?><?php echo($todayQuestion->getHours()); ?><?php endif; ?>" /> hours <span class="required">*</span>
		<?php else:?>
			<div class="show_textarea">
				<?php if(!is_null($todayQuestion)) echo $todayQuestion->getHours(); else echo "0";?> hours <span class="required">*</span>
			</div>
		<?php endif;?>
		
<!--
		<span id="show_question_history-1" class="show_link">Show question history</span>
-->
	</div>

	<?php if($isOwner):?>
		<input id="question_save" type="button" value="Save" />
	</form>
	<?php endif;?>
	
	<div class="hide" id="search-dialog">
	<!--
		<span id="create-task-button" class="button floatright">Create Task</span>
	-->
		<form autocomplete="off" onSubmit="return false">
		<table id="backlog_table-<?php echo($project_id); ?>" class="search-table">
		<thead>
		  <tr>
			<th class="sortable<?php if(isset($searchSort['name'])) echo(' '.$searchSort['name']); ?>">
				<span id="sort-name">Name</span><br />
				<input id="filter-name" name="filter[name]" class="text" type="text" autocomplete="off" style="width: 99%;" value="<?php echo $filters['name']; ?>"/>		
			</th>
			<th class="sortable<?php if(isset($searchSort['user_id'])) echo(' '.$searchSort['user_id']); ?>">
				<span id="sort-user">Assigned To</span><br />
			<select id="filter-user" name="filter[user_id]" autocomplete="off">
					<option value="">Anyone</option>
					<option <?php if($searchFilters['user_id']=='null'):?>selected="selected" <?php endif;?>value="null">-unassigned-</option>
					<?php foreach($projectUserArray as $k => $v):	?>
						<option <?php if($searchFilters['user_id']==(string)$k):?>selected="selected" <?php endif;?>value="<?=$k;?>"><?=$v;?></option>
					<?php endforeach; ?>
				</select>
			</th>

			<th class="sortable<?php if(isset($searchSort['priority'])) echo(' '.$searchSort['priority']); ?>">
				<span id="sort-pri">Priority</span><br />
				<select id="filter-pri" name="filter[priority]" autocomplete="off">
					<option value="">Any</option>
					<?php foreach(SdTaskTable::$priorityArr as $k => $v): ?>
						<option <?php if($searchFilters['priority']==(string)$k):?>selected="selected" <?php endif; ?>value="<?=$k;?>"><?=$v;?></option>
					<?php endforeach; ?>
				</select>
			</th>

			<th class="sortable<?php if(isset($searchSort['status'])) echo(' '.$searchSort['status']); ?>">
				<span id="sort-status">Status</span><br />
				<select id="filter-status" name="filter[status]" autocomplete="off">
					<option value="">Any</option>
					<option <?php if($searchFilters['status']=='not-completed'):?>selected="selected" <?php endif;?>value="not-completed">Not Completed</option>
					<option <?php if($searchFilters['status']=='not-accepted'):?>selected="selected" <?php endif;?>value="not-accepted">Not Accepted</option>
					<?php foreach(SdTaskTable::$statusArr as $k => $v): ?>
						<option <?php if($searchFilters['status']==(string)$k):?>selected="selected" <?php endif; ?>value="<?=$k;?>"><?=$v;?></option>
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
		</table>
		<div style="overflow:auto; height:400px;">
			<table class="backlog_table">
			<?php include_component('question', 'searchTableBody', array('project' => $project, 'projectUserArray' => $projectUserArray, 'filters' => $searchFilters, 'sort' => $searchSort)); ?>
			</table>
		</div>
	</div>
	
	<!-- Start Create Dialog
	<div class="hide form" id="create-task-dialog">
	<?php //include_component('task', 'createDialog'); ?>
	</div>
	 End Create Dialog -->
	
	<script type="text/javascript">
		$(document).ready(function () {
			qi = new Questions(<?php echo($isCurrentDay); ?>);
			<?php if(isset($yesterHoursCollection) && count($yesterHoursCollection)>0): ?>
				<?php foreach($yesterHoursCollection as $taskHours): ?>
				qi.yesterTasks[<?php echo($taskHours->getTaskId()); ?>] = true;
				qi.yesterTasksCount++;
				<?php endforeach; ?>
			<?php endif; ?>
			<?php foreach($todayHoursCollection as $taskHours): ?>
			qi.todayTasks[<?php echo($taskHours->getTaskId()); ?>] = true;
			qi.todayTasksCount++;
			<?php endforeach; ?>
			var filters = <?php echo(json_encode($sf_data->getRaw('searchFilters'))); ?>;
			var sort = <?php echo(json_encode($sf_data->getRaw('searchSort'))); ?>;
			question_backlog = new QuestionBacklog(filters, sort);
			question_backlog.questionUserId = <?php echo($questionUser->getId()); ?>;
		});
	</script>
</div>
<div id="sidebar">
	<div class="box calendar">
		<?php include_component('question', 'calendar', array('project_id' => $project_id, 'questionUser' => $questionUser, 'dateString' => $dateString)); ?>
		<script type="text/javascript">
			$(document).ready(function () {
				qc = new QuestionCalendar();
				qc.username = '<?php echo($questionUser->getUsername()); ?>';
				qc.selectedDay = '<?php echo($dateString); ?>';
			});
		</script>
	</div>
  <div class="box grey">
	<h2>Project Members</h2>
	<p>Select another member to see their questions.</p>
	<ul>
	<?php foreach($projectUsers as $projectUser): ?>
		<li>
		<?php if($userName == $projectUser->User->getUsername()): ?>
			<b><?php echo($projectUser->User->getFullName()); ?></b>
		<?php else: ?>
			<a href="<?php echo(url_for('@project_questions?project_id='.$project_id.'&username='.$projectUser->User->getUsername().'&date='.$dateString)); ?>"><?php echo($projectUser->User->getFullName()); ?></a>
		<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
  </div>
</div>