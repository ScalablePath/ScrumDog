<div class="box grey">
	<h2><? if($for_current_user): ?>My <? endif; ?>Projects
	<? if($for_current_user): ?>
	 (<a href="<? echo url_for('@member_createproject'); ?>">create new</a>)
	<? endif; ?>
	</h2>
	<ul>
	<? foreach($projects as $project): ?>
	  <li><a href="<? echo(url_for('@project_dashboard?project_id='.$project->getId())); ?>"><? echo($project->getName()); ?></a></li>
	<? endforeach; ?>
	</ul>
</div>