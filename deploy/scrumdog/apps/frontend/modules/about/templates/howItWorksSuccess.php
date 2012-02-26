<?php slot('page_title') ?>How it works<?php end_slot() ?>
<div id="main">
	<h1>How ScrumDog works</h1>
	<p>Once you register, there are four steps to get up and running.</p>
	<h2>1. Create a Project</h2>
	<p>All you need for this step is a project name like "My Widget".  This takes about ten seconds. In typical scrumspeak they use the word "product", but we thought "project" was more flexible.</p>
	<h2>2. Invite your team</h2>
	<p>For this step all you need is the email addresses of the people on your project team.  Once you invite them, they can signup for ScrumDog and join your project.</p>
	<h2>3. Create a Sprint</h2>
	<p>A sprint is period of time (typically 2-4 weeks) when the project team focuses on completing a limited number of tasks.
	Over the life of a typical project, there are many sprints.</p>
	<h2>4. Create and Assign Tasks</h2>
	<p>Tasks are where most of the activity happens.
	A task is assigned to one member of the team at a time, but the entire team can collaborate on a task by sharing files, 
	making comments, and forwarding the task to other team members.</p>
	<? if(!$isAuthenticated): ?>
	<h2>So what are you waiting for? <a href="<?php echo url_for('@user_register'); ?>">Sign up now!</a></h2>
	<? endif; ?>
<p>Have questions? Don't hesitate to email us at <?php echo Fluide_Symfony_Util::emailLink(sfConfig::get('app_info_email')) ?>.</p>
<br />
</div>
<div id="sidebar">
<? if($isAuthenticated): ?>
  <?php include_component('user', 'projects') ?>
  <?php include_component('default', 'inviteMembers') ?>
<? else: ?>
	<?php include_component('auth', 'register'); ?>
<? endif;  ?>
</div>