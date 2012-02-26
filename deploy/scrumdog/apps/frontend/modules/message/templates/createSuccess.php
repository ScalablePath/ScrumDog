<?php slot('page_title') ?>Create Message<?php end_slot() ?>
<?php slot('project_title') ?><? echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
<h1>Create Message</h1>
<div class="box form">
<p class="required">Fields marked with * are required.</p>
<form action="<? echo url_for('@project_message_create?project_id='.$project->getId()) ?>" method="POST" enctype="multipart/form-data">
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
			<input type="submit" value="Post"/>
		</div>
	</div>
</form>
</div>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Notes</h2>
	<ul>
		<li>An email notification of your message posting will be sent to the project members who are currently set to receive emails.</li>
		<li>After you create the message, you will be able to attach files to it.</li>
	</ul>
  </div>
</div>