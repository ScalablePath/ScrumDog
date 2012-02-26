<?php slot('page_title') ?><? echo($project->getName()); ?> : Messages<?php end_slot() ?>
<?php slot('project_title') ?><? echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Message Board</h1>
	<?php if(count($messages)>0): ?>
		<?php foreach($messages as $message): ?>
	  	<div class="box grey">
			<h2><a href="<?php echo url_for('@project_message_view?message_id='.$message['id']) ?>"><?php echo $message['title'] ?></a>
			<?php $commentCount = count($message->Comments); ?>
			<?php if($commentCount==1): ?>
			(1 comment)
			<?php else: ?>
			(<?php echo($commentCount) ?> comments)
			<?php endif; ?>
			</h2>
			<p class="metadata"><i>Created on <?php echo(date("D M, j \a\\t g:i a", strtotime($message->created_at)).' '.sfConfig::get('app_server_timezone')); ?> by <a href="<?php echo(url_for('@member_profile?username='.$message->User->getUsername())); ?>"><?php echo($message->User->getFullName()); ?></a></i></p>
			<p><?php echo nl2br($message->getSummary()) ?></p>
			<?php if(count($message->Files)>0): ?>
			<div class="files">
			<?php include_component('task', 'FileList', array('mode' => 'message_index', 'class' => '', 'id' => $message['id'], 'files' => $message->Files)); ?>
			</div>
			<?php endif; ?>
			<!--
			<p><a href="<?php echo url_for('@project_message_view?message_id='.$message['id']) ?>">View message detail >></a></p>
			-->
		</div>
		<?php endforeach; ?>
	<?php else: ?>
		<p>There are no messages in this project yet.</p>
	<?php endif; ?>
</div>
<div id="sidebar">
	<div class="box grey">
		<h2>Post a message</h2>
		<p><a href="<?php echo url_for('@project_message_create?project_id='.$project->getId()) ?>">Click here</a> to create a new message.</p>
	</div>
</div>

 <script type="text/javascript">
	function dialog(mes, s) {
		s = s || {bgColor: 'transparent'};
		
		$.fn.nyroModalManual({
			bgColor: s.bgColor,
			content: mes
		});
	}

	$(document).ready(function() {
		$('.files a.nyroModal').nyroModal({title:'ThIS a TEst'});											   
		$('.files a').tooltip();
		$('.files a img').hover(function(e) {
			this.t = this.title;
			this.title = "";
		},
		function(){
			this.title = this.t;		
		});
	});
</script>