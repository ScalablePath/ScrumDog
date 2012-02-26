<?php slot('page_title') ?><? echo($project->getName()); ?> : Confirm membership<?php end_slot() ?>
<?php slot('project_title') ?><? echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Join Project</h1>
  <div class="box form">
    <h2>You've been invited to join this team!&nbsp;&nbsp;Would you like to join the <? echo($project->getName()); ?> project team?</h2>
    <p>You have already been invited to join this project.  Click the button below to confirm that you would like to join the project.</p>
    <form action="<? echo url_for('@project_confirmjoin?project_id='.$project->getId()) ?>" method="POST">
		<div class="item">
			<div class="field">
				<input type="submit" value="Join Project"/>
			</div>
		</div>
    </form>
  </div>
</div>