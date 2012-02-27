<?php slot('page_title') ?>Edit Profile<?php end_slot() ?>
<div id="main">
	<h1>Edit Profile</h1>
	<div class="box form">
	<!-- This image part should be cleaned up -->
	   <?php if($profileImageFile): ?>
		<?php echo image_tag($profileImageFile->getThumbnailSrc(100, 100, 'scale'), array('alt'=>$user->getFullName(),'title'=>$user->getFullName())); ?>
		<br />
		<?php echo link_to('Delete profile image', '@member_delete_profileimage?id='.$profileImageFile->getId()); ?>
		<?php endif ?>	
	<p class="required">Fields marked with * are required.</p>
	<form action="<?php echo url_for('@member_editprofile') ?>" method="POST" enctype="multipart/form-data">
		<?php echo($form); ?>
		<div class="item">
			<div class="field">
				<input type="submit" value="Submit"/>
			</div>
		</div>
	</form>
	</div>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Tips</h2>
	<ul>
		<li>Select the proper time zone so that you get your reminder emails in the morning (your time).</li>
		<li>View your <a href="<?php echo url_for('@member_profile?username='.$user['username']) ?>">public profile</a> to see what others see.</li>
	</ul>
  </div>
</div>
