<?php slot('page_title') ?><? echo($project->getName()); ?> : Add Project Team Members<?php end_slot() ?>
<?php slot('project_title') ?><? echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Add Members</h1>
  <div class="box form">
    <form action="<? echo url_for('@project_addmembers?project_id='.$project->getId()) ?>" method="POST">
        <? echo($form); ?>
        <div class="item">
			<div class="field">
				<input type="submit" value="Add Members"/>
			</div>
		</div>
    </form>
  </div>
</div>