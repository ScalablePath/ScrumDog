<?php slot('page_title') ?><?php echo($project->getName()); ?> : Already a Member<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Join Project</h1>
  <div class="box grey">
    <h2>What are you doing here?</h2>
    <p>You are already a member of the <?php echo($project->getName()); ?> project team. <a href="<?php echo url_for('@project_dashboard?project_id='.$project->getId()); ?>">Click here</a> to go to the project dashboard.</p>
  </div>
</div>