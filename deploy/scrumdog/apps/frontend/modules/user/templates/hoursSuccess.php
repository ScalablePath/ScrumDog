<?php slot('page_title') ?>My Hours<?php end_slot() ?>
<div id="main">
  	<div class="box">
  		<?php if($rowCount>0): ?><a class="floatright" href="<?php echo($csvLink) ?>"><span class="button">Export</span></a><?php endif; ?>
		<h2>My Hours for <?php echo(date('m/j/Y', strtotime($startDate)).' - '.date('m/j/Y', strtotime($endDate))) ?></h2>
		<?php if($rowCount>0): ?>
		<table>
			<thead>
				<tr>
					<th>Date</th>
					<th>Project</th>
					<th>Hours</th>
					<th>Summary</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php $i=0; foreach($questions as $question): ?>
			<?php $i++; ?>
				<tr<?php if($i%2==0):?> class="alt"<?php endif; ?>>
					<td><?php echo(date('D m/j', strtotime($question->getDate()))) ?></td>
					<td><?php echo($question->Project->getName()) ?></td>
					<td><?php echo($question->getHours()) ?></td>
					<td>
					<?php $taskHours = $question->getTaskHours(); $taskHourCount = count($taskHours); $i=0;?>
					<?php if($taskHourCount>0): ?><b>Tasks</b>: <?php endif; ?>
					<?php foreach($taskHours as $taskHour): ?>
						<?php $i++; ?>
						<?php echo($taskHour->Task->getName()) ?>
						<a href="<?php echo(url_for('@project_task?task_id='.$taskHour->Task->getId())) ?>">#<?php echo($taskHour->Task->getId()) ?></a>
						(<?php echo($taskHour->getHours()) ?> hr<?php if($taskHour->getHours()-1!=0): ?>s<?php endif;?>)<?php if($i!=$taskHourCount):?>,<?php endif; ?>
					<?php endforeach; ?>
					<?php if(trim($question->getWork())!=''): ?>
						<b>Other Work</b>:
						<?php echo($question->getWork()) ?>
					<?php endif; ?>
					<?php if(trim($question->getObstacles())!=''): ?>
						<b>Obstacles</b>:
						<?php echo($question->getObstacles()) ?>
					<?php endif; ?>
					</td>
					<td>
						<a href="<?php echo(url_for('@project_questions?project_id='.$question->Project->getId().'&username='.$question->User->getUsername().'&date='.$question->getDate())) ?>"><span class="edit_button icon edit" title="Edit">Edit</span></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php else: ?>
		There are no hours results for filters you have selected.
		<?php endif; ?>
	</div>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Filters</h2>
	<form action="" method="get">
	<div class="item">
		<label for="work_start_date">Start Date</label>
		<div class="field">
			<input id="work_start_date" class="text date" type="text" value="<?php echo($startDate) ?>" name="start_date"/>
		</div>
	</div>
	<div class="item">
		<label for="work_end_date">End Date</label>
		<div class="field">
			<input id="work_end_date" class="text date" type="text" value="<?php echo($endDate) ?>" name="end_date"/>
		</div>
	</div>
	<div class="item">
		<label for="sprint_scrum_days">Projects</label>
		<br />select: <span id="check_all" class="link">all</span>, <span id="check_none" class="link">none</span>
		<div class="field">
			<ul class="checkbox_list">
			<?php foreach($projects as $project): ?>
				<li>
					<input id="projects_<?php echo($project->getId()); ?>" type="checkbox" class="work-project" value="<?php echo($project->getId()); ?>" name="projects[]" <?php if(in_array($project->getId(), $sf_data->getRaw('projectsArray'))): ?>checked="checked"<?php endif; ?>/>
					<label for="projects_<?php echo($project->getId()); ?>"><?php echo($project->getName()); ?></label>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<input style="margin-top: 5px;" type="submit" value="View Hours" />
	</form>
  </div>
  <div class="box grey">
	<h2>Note</h2>
	<p>Only daily questions with hours greater than zero will appear in this report.</p>
  </div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
	    $('#work_start_date').datepicker({ dateFormat: 'yy-mm-dd', appendText: ' (yyyy-mm-dd)' });
	    $("#work_end_date").datepicker({ dateFormat: 'yy-mm-dd', appendText: ' (yyyy-mm-dd)' });
	    $("#check_all").click(function(){
	    	$("input.work-project").each(function() {
				this.checked = true;
				});
			});
		$("#check_none").click(function(){
	    	$("input.work-project").each(function() {
				this.checked = false;
				});
			});
		});
</script>