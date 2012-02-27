<?php slot('page_title') ?>Terms of Service<?php end_slot() ?>

<div id="main">
	<?php include_partial('Terms', array()); ?>
</div>

<div id="sidebar">
<?php if($isAuthenticated): ?>
  <?php include_component('user', 'projects') ?>
  <?php include_component('default', 'inviteMembers') ?>
<?php else: ?>
	<?php include_component('auth', 'register'); ?>
<?php endif; ?>
</div>