<?php slot('page_title') ?>Manage Sprint<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Manage Sprint</h1>
	<div class="box form">
	<p class="required">Fields marked with * are required.</p>
	<form action="<?php echo url_for('@sprint_manage?sprint_id='.$sprint_id) ?>" method="POST">
	  <?php echo($form); ?>
		<div class="item">
			<div class="field">
				<input type="hidden" name="redirect" value="<?php echo $redirectUrl?>"/>
				<input type="submit" value="Update"/>
			</div>
		</div>
	</form>
	</div>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Note</h2>
	<p>Reminder emails are sent to team members on sprint work days by default.  The project owner can control who receives reminder emails on the project <a href="<?php echo url_for('@project_members?project_id='.$project_id) ?>">team members</a> page.</p>
  </div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
    $("#sprint_start_date").datepicker({ dateFormat: 'yy-mm-dd', appendText: ' (yyyy-mm-dd)' });
    $("#sprint_end_date").datepicker({ dateFormat: 'yy-mm-dd', appendText: ' (yyyy-mm-dd)' });
});
</script>
