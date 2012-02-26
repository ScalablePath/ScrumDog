<?php slot('page_title') ?>Create Task<?php end_slot() ?>
<?php slot('project_title') ?><? echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
<h1>Create Task</h1>
<div class="box form">
<p class="required">Fields marked with * are required.</p>
<form action="<? echo url_for($createRoute) ?>" method="POST" enctype="multipart/form-data">
    <? echo($form); ?>
	
	<div class="item">
		<label for="task_file">Files</label>
		<div class="field">
			<div id="divFileUpload">
				<div>
					<input autocomplete="off" id="file-input0" type="file" name="Filedata" />&nbsp;
					<span id="backlog_add" class="backlog_add add_button icon add" title="Add file" onclick="addFileToUpload('divFileUpload')">Add file</span>
				</div>
			</div>
		</div>
	</div>

    <div class="item">
		<div class="field">
			<input type="submit" value="Create"/>
		</div>
	</div>
</form>
</div>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Notes</h2>
	<p>
		<ul>
			<li><b>Business value</b> helps to determine which tasks should be moved from the project backlog into a sprint first.</li>
			<li><b>Priority</b> lets team members know which tasks they should work on first.</li>
		</ul>
  </div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
	    $("input.numeric").numeric(null);
	});
</script>