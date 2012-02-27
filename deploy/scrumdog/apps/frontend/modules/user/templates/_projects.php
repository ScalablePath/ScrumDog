<div class="box grey">
	<h2><?php if($for_current_user): ?>My <?php endif; ?>Projects
	<?php if($for_current_user): ?>
	 (<a href="<?php echo url_for('@member_createproject'); ?>">create new</a>)
	<?php endif; ?>
	</h2>
	<ul>
	<?php foreach($projects as $project): ?>
	  <li><a href="<?php echo(url_for('@project_dashboard?project_id='.$project->getId())); ?>"><?php echo($project->getName()); ?></a></li>
	<?php endforeach; ?>
	</ul>
</div>