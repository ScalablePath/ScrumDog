<?php slot('page_title') ?><?php echo($project->getName()); ?> : Members<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>
<div id="main">
	<h1>Members</h1>
  <div class="box">
  <h2>Active Members</h2>
  <form action="<?php echo url_for('@project_members?project_id='.$project->getId()) ?>" method="POST">
  <table>
    <thead>
      <tr><th>Name</th><th>Role</th><th>Send Email</th><th>&nbsp;</th></tr>
    </thead>
    <tbody>
    <?php $i=1; foreach($projectUsers as $projectUser): ?>
      <?php $member = Doctrine::getTable('SdUser')->find($projectUser->getUserId());?>
      <tr<?php if($i%2==0):?> class="alt"<?php endif;?>>
        <td><a href="<?php echo(url_for('@member_profile?username='.$member->getUsername())); ?>"><?php echo($member->getFullName().' ('.$member->getUsername().')'); ?></a></td>
        <td><?php switch($projectUser->getRole()){case 1: echo('owner'); break; case 2: echo('member'); break;} ?></td>
			<td>
			<?php if($isProjectOwner || $member->getId()==$user->getId()): ?>
			<input type="hidden" name="project_user[id][]" value="<?php echo $projectUser->getId(); ?>" /><input type="checkbox" name="project_user[send_email][]" value="<?php echo $projectUser->getId(); ?>" <?php if($projectUser->getSendEmail()):?>checked="checked"<?php endif; ?> />
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
			
			</td>
			<td>
			<?php if($isProjectOwner): ?>
				<?php if($member->getId()!=$user->getId()): ?>
					<a class="remove_button button" href="javascript:if(confirm('Are you sure you want to remove <?php echo($member->getUsername()); ?> from the project?')){window.location='<?php echo(url_for('@project_removemember?project_id='.$project->getId().'&project_user_id='.$projectUser->getId())); ?>';}">remove</a>
				<?php else: ?>				
					&nbsp;
				<?php endif; ?>
			<?php elseif($member->getId()==$user->getId()): ?>
				<a class="remove_button button" href="javascript:if(confirm('Are you sure you want to remove yourself from the project?')){window.location='<?php echo(url_for('@project_removemember?project_id='.$project->getId().'&project_user_id='.$projectUser->getId())); ?>';}">remove</a>
			<?php else: ?>				
				&nbsp;
			<?php endif; ?>
			</td>
     </tr>
    <?php $i++; endforeach; ?>
    </tbody>
  </table>
	<div>
		<input type="hidden" name="project_user[project_id]" value="<?php echo $project->getId(); ?>"/>
		<input type="submit" value="Update"/>
	</div>
  </form>
  <br />
<?php if($pendingUserCount>0): ?>
  <h2>Pending Members</h2>
  <table>
    <thead>
      <tr><th>Name</th><th>Status</th><?php if($isProjectOwner): ?><th>&nbsp;</th><?php endif; ?></tr>
    </thead>
    <tbody>
    <?php foreach($pendingUsers as $projectUser): ?>
      <?php $member = Doctrine::getTable('SdUser')->find($projectUser->getInviteeUserId()); ?>
      <tr>
        <td><a href="<?php echo(url_for('@member_profile?username='.$member->getUsername())); ?>"><?php echo($member->getFullName().' ('.$member->getUsername().')'); ?></a></td>
        <td><?php echo ( (int)$projectUser->getStatus() === SdInvitationTable::SEND )? 'invited':'requested'; ?></td>
        <?php if($isProjectOwner): ?>
        <td>
        <?php if( $projectUser->getStatus() == 0): ?>
          <a class="accept_button button" href="<?php echo(url_for('@project_acceptmember?project_id='.$project->getId().'&invitation_id='.$projectUser->getId())); ?>">accept</a> 
		<?php endif; ?>  
          <a class="remove_button button" href="javascript:if(confirm('Are you sure you want to remove <?php echo($member->getUsername()); ?> from the project?')){window.location='<?php echo(url_for('@project_removeinvitation?project_id='.$project->getId().'&invitation_id='.$projectUser->getId())); ?>';}">remove</a>
        </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<br />

<?php if($pendingUsersNotRegisteredCount>0): ?>
  <h2>Pending Members (not registered)</h2>
  <table>
    <thead>
      <tr><th>Email</th><th>Status</th><?php if($isProjectOwner): ?><th>&nbsp;</th><?php endif; ?></tr>
    </thead>
    <tbody>
    <?php foreach($pendingUsersNotRegistered as $projectUser): ?>
      
      <tr>
        <td><?php echo $projectUser->getInviteeEmail(); ?></td>
        <td>invited</td>
        <?php if($isProjectOwner): ?>
        <td>
          <a class="remove_button button" href="javascript:if(confirm('Are you sure you want to remove <?php echo($projectUser->getInviteeEmail()); ?> invitation from the project?')){window.location='<?php echo(url_for('@project_removeinvitation?project_id='.$project->getId().'&invitation_id='.$projectUser->getId())); ?>';}">remove</a>
        </td>
        <?php endif; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
<br />

</div>
</div>
<div id="sidebar">
    <?php include_component('project', 'addMembers') ?>
    <?php include_component('default', 'inviteMembers') ?>
</div>