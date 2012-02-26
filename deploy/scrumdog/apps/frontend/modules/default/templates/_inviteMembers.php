<div class="box form">
<h2>Invite people to ScrumDog</h2>
<form action="<? echo url_for('@member_invitemembers') ?>" method="POST">
    <? echo($form); ?>
    
    <?php if ($sf_params->has('project_id')): ?>
    <div class="item">
		<div class="field">
			<input type="checkbox" name="invite-to-project" value="<?php echo $sf_params->get('project_id');?>" checked/>
			<span>Invite to current project</span>
		</div>
	</div>
	<?php endif;?>
	
    <div class="item">
		<div class="field">
			<input type="submit" value="Invite"/>
		</div>
	</div>
</form>
<div></div>
</div>