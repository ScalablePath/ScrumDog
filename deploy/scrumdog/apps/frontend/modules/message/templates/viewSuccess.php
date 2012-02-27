<?php slot('page_title') ?>Message : <?php echo $message->getTitle()?><?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
<div class="box message-detail">
<form id="message_form" autocomplete="off" action="">
	<input id="id_input" name="message[id]" type="hidden" value="<?php echo $message->getId();?>" />
	<?php if($sf_user->getId()==$message->User->getId() || $sf_user->getId()==1): ?>
	<div class="message-buttons">
		<input class="message-edit display" type="button" value="Edit" />
		<input class="message-save field hide" type="button" value="Save" />
		<input class="message-cancel field hide" type="button" value="Cancel" />
	</div>
	<?php endif; ?>
	<div class="item nolabel title">
		<div class="display">
			<h1 id="title-header"><?php echo($message->getTitle()); ?></h1>
		</div>
		<div class="field hide">
			<input autocomplete="off" id="title_input" name="message[title]" class="text" style="width: 100%;" value="<?php echo $message->getTitle()?>" />
		</div>
	</div>
	<p class="metadata">Created on <?php echo(date("D M, j \a\\t g:i a", strtotime($message->created_at)).' '.sfConfig::get('app_server_timezone')); ?> by <a href="<?php echo(url_for('@member_profile?username='.$message->User->getUsername())); ?>"><?php echo($message->User->getFullName()); ?></a></p>
	<div class="item nolabel content">
		<div class="display">
			<p id="content_paragraph"><?php echo(nl2br($message->getContent())); ?></p>
		</div>
		<div class="field hide">
			<textarea autocomplete="off" id="content_textarea" class="full" name="message[content]"><?php echo $message->getContent()?></textarea>
		</div>
	</div>
</form>
</div>

<div class="box files" id="files">
    <h2>Files</h2>
	<?php include_component('task', 'FileList', array('mode' => 'message', 'class' => '')); ?>
	<a href="#" id="addFiles">Add File</a>	
    <div id="addFilesForm" style="display: none;">
	    <form action="<?php echo url_for('@message_file_upload?message_id='.$message->getId()) ?>" method="post" enctype="multipart/form-data" onsubmit="return AIM.submit(this, {'onStart' : startCallback, 'onComplete' : completeCallback})">
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

<div class="box" id="history">
    <h2>Message History</h2>
	<a href="#" id="showHistory">Show history</a>
</div>
</div>
<div id="sidebar">
  <div class="box grey message-comments">
	<h2>Comments</h2>
	<?php include_component('message', 'Comment', array('class' => '')); ?>
	<div>
		<textarea autocomplete="off" id="comment_textarea" name="message[comment]" rows="5" cols="35" ></textarea>
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
		myObj = new Message();
	});
</script>