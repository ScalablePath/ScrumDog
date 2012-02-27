<?php slot('page_title') ?>Task : <?php echo $task->getName()?><?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
<div class="box task-detail">
<form id="task_form" autocomplete="off" action="">
	<input id="id_input" name="task[id]" type="hidden" value="<?php echo $task->getId();?>" />
	<div class="task-buttons">
		<input class="task-edit display" type="button" value="Edit" />
		<input class="task-save field hide pri-task" type="button" value="Save" />
		<input class="task-cancel field hide pri-task" type="button" value="Cancel" />
	</div>
	<div class="item nolabel title">
		<div class="display">
			<h1 id="name-header"><?php echo($task->getName()); ?></h1>
		</div>
		<div class="field hide pri-task">
			<input autocomplete="off" id="name_input" name="task[name]" class="text" style="width: 100%;" value="<?php echo $task->getName()?>" />
		</div>
	</div>

	<p class="metadata">Created on <?php echo(date("D M, j \a\\t g:i a", strtotime($task->created_at)).' '.sfConfig::get('app_server_timezone')); ?> by <a href="<?php echo(url_for('@member_profile?username='.$task->Creator->getUsername())); ?>"><?php echo($task->Creator->getFullName()); ?></a></p>
	<div class="item nolabel description">
		<div class="display">
			<p id="description_paragraph"><?php echo(nl2br($task->getDescription())); ?></p>
		</div>
		<div class="field hide pri-task">
			<textarea autocomplete="off" id="description_textarea" class="full" name="task[description]"><?php echo $task->getDescription()?></textarea>
		</div>
	</div>
	<h2>Metadata</h2>
	<div class="metadata clearfix">
		<div class="item">
			<label for="bv_select">Business Value</label>
			<div class="display">
				<p id="bv_paragraph"><?php echo $task->getBusinessValueText()?></p>
			</div>
			<div class="field hide pri-task">
				<select id="bv_select" name="task[business_value]">
				<?php foreach(SdTaskTable::$businessValueArr as $k => $v): ?>
					<option <?php if($task->getBusinessValue()==$k):?> selected="selected" <?php endif; ?>value="<?php echo $k;?>"><?php echo $v;?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="item">
			<label for="pri_select">Priority</label>
			<div class="display">
				<p id="pri_paragraph"><?php echo $task->getPriorityText()?></p>
			</div>
			<div class="field hide pri-task">
				<select id="pri_select" name="task[priority]">
				<?php foreach(SdTaskTable::$priorityArr as $k => $v): ?>
					<option <?php if($task->getPriority()==$k):?>selected="selected" <?php endif; ?>value="<?php echo $k;?>"><?php echo $v;?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="item">
			<label for="eh_input">Estimated Hours</label>
			<div class="display">
				<p id="eh_paragraph"><?php echo nl2br($task->getEstimatedHours())?></p>
			</div>
			<div class="field hide pri-task">
				<input autocomplete="off" id="eh_input" name="task[estimated_hours]" class="numeric text" style="width: 35px;" value="<?php echo $task->getEstimatedHours()?>" />
			</div>
		</div>
		<div class="item">
			<label for="user_select">Assigned To</label>
			<div class="display">
				<p id="user_paragraph">
					<?php if(!is_null($task->getUserId())): ?>
						<a href="<?php echo url_for('@member_profile?username='.$projectUsers[$task->getUserId()]->getUsername()) ?>"><?php echo($task->getUserText()) ?></a>
					<?php else: ?>
						<?php echo('-unassigned-') ?>
					<?php endif; ?>
				</p>
			</div>
			<div class="field hide pri-task">
				<select id="user_select" name="task[user_id]">
					<option value="">-unassigned-</option>
				<?php foreach($projectUsers as $k => $v): ?>
					<option <?php if($task->getUserId()==$k):?>selected="selected" <?php endif; ?>value="<?php echo $k?>"><?php echo $v->getFullName()?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="item">
			<label for="status_select">Status</label>
			<div class="display">
				<p id="status_paragraph"><?php echo $task->getStatusText()?></p>
			</div>
			<div class="field hide pri-task">
				<select id="status_select" name="task[status]">
				<?php foreach(SdTaskTable::$statusArr as $k => $v): ?>
					<option <?php if($task->getStatus()==$k):?>selected="selected" <?php endif; ?>value="<?php echo $k;?>"><?php echo $v;?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div style="width: 100%; margin-top: 5px;" class="item">
			<label for="pt_select">Parent Task</label>
			<div class="display">
				<p id="pt_paragraph">
					<?php if(!is_null($task->getParentId())): ?>
						<a href="<?php echo(url_for('@project_task?task_id='.$parentTask->getId())); ?>"><?php echo($parentTask->getName().' ('.$parentTask->getId().')'); ?></a>
					<?php else: ?>
						-none-
					<?php endif; ?>
				</p>
			</div>
			<div class="field hide pri-task">
				<select id="pt_select" name="task[parent_id]">
					<option value="">-none-</option>
					<?php foreach($sf_data->getRaw('parentTasks') as $parentTask): ?>
						<option <?php if($parentTask->getId()==$task->getParentId()): ?> selected="selected"<?php endif; ?>value="<?php echo($parentTask->getId()) ?>"><?php echo($parentTask->getName().' ('.$parentTask->getId().')') ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
</form>
</div>
<div class="box" id="files">
    <h2>Files</h2>
	<?php include_component('task', 'FileList', array('mode' => 'task', 'class' => '')); ?>
	<a href="#" id="addFiles">Add File</a>
    <div id="addFilesForm" style="display: none;">
	    <form action="<?php echo url_for('@task_file_upload?task_id='.$task->getId()) ?>" method="post" enctype="multipart/form-data" onsubmit="return AIM.submit(this, {'onStart' : startCallback, 'onComplete' : completeCallback})">
			<div>
				<input autocomplete="off" id="file-input0" type="file" name="Filedata" />
			</div>
			<br />
			<div>
				<input type="submit" value="upload" />
				<br /><br />
				<div id="file-uploading" style="display: none">
					<img src="/images/loader.gif" />uploading...
				</div>
			</div>
		</form>

	</div>
</div>

<div class="box" id="subtasks">
	<?php if($task->getIsArchived() == 0): ?>
	<span id="create-task-button" class="button floatright">Create Subtask</span>
	<?php endif; ?>
    <h2>Subtasks</h2>
    <form autocomplete="off">
	<table class="subtask_table<?php if(count($subTasks)==0): ?> hide<?php endif; ?>">
		<thead>
		  <tr>
			<th>
				<span>Name</span>
			</th>
			<th>
				<span>Assigned To</span>
			</th>
			<th>
				<span>Priority</span>
			</th>
			<th>
				<span>Status</span>
			</th>
			<th>
				<span id="sort-eh">Estimated Hours</span>
			</th>
			<th class="actions">
				<span class="refresh_button icon refresh" title="Refresh">Refresh</span>
			</th>
		</tr>
		</thead>
		<?php include_component('task', 'subtaskBody', array('task' => $task, 'project' => $project, 'subTasks' => $subTasks)); ?>
	</table>
	</form>
</div>

<!-- Start Assign Dialog -->
	<div class="hide" id="assign_dialog">
		<?php if($hasActiveSprints): ?>
		<form autocomplete="off">
			<select id="assign_select">
				<?php if(!is_null($task->getSprintId()) || $task->getIsArchived()==1): ?>): ?>
					<option value="">Project Backlog</option>
				<?php endif; ?>
				<?php foreach($activeSprints as $mySprint): ?>
					<?php if($task->getSprintId()!=$mySprint->getId()): ?>
					<option value="<?php echo $mySprint->getId()?>"><?php echo $mySprint->getName()?></option>
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

	<!-- End Assign Dialog -->

<!-- Start Create Dialog -->
<div class="hide form" id="create-task-dialog">
<?php include_component('task', 'createDialog'); ?>
</div>
<!-- End Create Dialog -->

<div class="box" id="history">
    <h2>Task History</h2>
	<a href="#" id="showHistory">Show history</a>
</div>
</div>
<div id="sidebar">
  <div class="box grey task-comments">
	<h2>Comments</h2>
	<?php include_component('task', 'Comment', array('class' => '')); ?>
	<div>
		<textarea autocomplete="off" id="comment_textarea" name="task[comment]" rows="5" cols="35" ></textarea>
		<span id='error_field' class='hide error_field'>Please enter a comment.<br /></span>
		<input id="comment_save" type="button" value="Submit Comment" />
	</div>
  </div>
</div>
<script type="text/javascript">   
    $(document).ready(prepareFileList);
    
	$("#addFiles").click(function() {
		$("#addFilesForm").toggle('blind',{},500);
		$(this).hide();
		return false;
	});
		
	$('#showHistory').click(showHistory);

	$(document).ready(function () {
		myObj = new Task();
		myObj.project_id = <?php echo $task->getProjectId(); ?>;
		<?php if(!is_null($task->getSprintId())): ?>
		myObj.sprint_id = <?php echo($task->getSprintId()); ?>;
		<?php endif; ?>
		<?php if($task->getIsArchived()==1): ?>
		myObj.is_archived = 1;
		<?php endif; ?>
		taskSaveSuccessFunc = 'ST_taskSaveSuccess';
		st_backlog = new SubtaskBacklog();
	});
</script>