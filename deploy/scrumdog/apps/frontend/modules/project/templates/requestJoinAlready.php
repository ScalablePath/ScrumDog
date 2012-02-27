<?php slot('page_title') ?><?php echo($project->getName()); ?> : Already Requested to Join<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Join Project</h1>
  <div class="box grey">
    <h2>You have already requested to join the <?php echo($project->getName()); ?> project team.</h2>
    <p>You will be notified if the project owner accepts or rejects your request.</p>
  </div>
</div>