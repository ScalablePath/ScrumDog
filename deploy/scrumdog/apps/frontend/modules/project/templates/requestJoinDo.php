<?php slot('page_title') ?><? echo($project->getName()); ?> : Request to Join<?php end_slot() ?>
<?php slot('project_title') ?><? echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Join Project</h1>
  <div class="box form">
    <h2>Would you like to join the <? echo($project->getName()); ?> project team?</h2>
    <p>Click the button below if you know the owner of this project and you would like to join the team.</p>
    <form action="<? echo url_for('@project_requestjoin?project_id='.$project->getId()) ?>" method="POST">
		<div class="item">
			<div class="field">
				<input type="submit" value="Request to Join"/>
			</div>
		</div>
    </form>
  </div>
</div>