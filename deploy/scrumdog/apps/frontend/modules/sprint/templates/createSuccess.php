<?php slot('page_title') ?>Create Sprint<?php end_slot() ?>
<?php slot('project_title') ?><? echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
<h1>Create Sprint</h1>
<div class="box form">
<p class="required">Fields marked with * are required.</p>
<form action="<? echo url_for('@project_createsprint?project_id='.$project_id) ?>" method="POST">
    <? echo($form); ?>
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
			<li>A typical sprint is from two to four weeks long.</li>
			<li>Sprint work days are the days you expect the project team members to be working on tasks.</li>
		</ul>
  </div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
    $("#sprint_start_date").datepicker({ dateFormat: 'yy-mm-dd', appendText: ' (yyyy-mm-dd)' });
    $("#sprint_end_date").datepicker({ dateFormat: 'yy-mm-dd', appendText: ' (yyyy-mm-dd)' });
});
</script>
