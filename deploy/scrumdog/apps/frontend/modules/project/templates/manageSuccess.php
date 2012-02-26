<?php slot('page_title') ?><? echo($project->getName()); ?> : Manage Project<?php end_slot() ?>
<?php slot('project_title') ?><? echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Manage Project</h1>
	<div class="box">
	<h2>Sprints</h2>
		<form action="<? echo url_for('@project_manage?project_id='.$project->getId()) ?>" method="POST">
			<table id="sprint_table-<?=$project_id?>" class="sprint_table">
				<thead>
				  <tr><th>Name</th><th>Active</th><th>Current</th>
				</thead>
				<?php include_component('project', 'sprintTableBody', array('project' => $project)); ?>
			</table>
			<div>
				<input type="hidden" name="sprint[project_id]" value="<?php echo $project->getId(); ?>"/>
				<input type="submit" value="Update"/>
			</div>
		</form>
	</div>
</div>
<div id="sidebar">
  <div class="box form">
	<h2>Change Project Name</h2>
	<form action="<? echo url_for('@project_manage?project_id='.$project->getId()) ?>" method="POST">
		<? echo($form); ?>
		<div class="item">
			<div class="field">
				<input type="hidden" name="project[id]" value="<?php echo $project->getId(); ?>"/>
				<input type="submit" value="Change"/>
			</div>
		</div>
	</form>
  </div>
</div>