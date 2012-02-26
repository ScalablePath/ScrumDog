<?php slot('page_title') ?>About<?php end_slot() ?>
<div id="main">
	<h1>About ScrumDog</h1>
	<p>ScrumDog is the creation of <a href="http://www.linkedin.com/in/damienf">Damien Filiatrault</a> and a handful of colleagues.
As the leader of team of software developers and a firm believer in the value of the scrum process, he wanted a better online tool
for managing projects.  ScrumDog is that tool.</p>
	<h2>Why is ScrumDog different?</h2>
<p>Most online scrum software is overly complicated.  By focusing directly on tasks without the intermediate step of stories, 
ScrumDog allows you and your team to get to work faster.  Once you try our intuitive user interface, you will see how fast and easy it is 
to take control of your project using ScrumDog.</p>
<p>The most unique feature of ScrumDog is likely the <b>Daily Questions</b> page which guides the project manager throught the standup meeting and records team members' responses for tracking purposes.  ScrumDog sends reminder emails each morning to keep the entire team focused on the right tasks following the scrum process.</p>
	<h2>ScrumDog Features</h2>
	<ul>
		<li>Team Management</li>
		<li>Member Profiles</li>
		<li>Personal Task Dashboard</li>
		<li>Project Backlog</li>
		<li>Sprint Backlog</li>
		<li>Sprint Burndown Chart</li>
		<li>Fast Inline Editing, Sorting and Filtering of Tasks</li>
		<li>Task Archive</li>
		<li>Task Comments</li>
		<li>Task Attachments (Files)</li>
		<li>Task History</li>
		<li>Subtasks</li>
		<li>Daily Reminder Emails and Questions</li>
		<li>Time Tracking</li>
		<li>Work Summary Reporting</li>
		<li>Project Messages and File Sharing</li>
	</ul>
<br />
<p>Have questions? Don't hesitate to email us at <?php echo Fluide_Symfony_Util::emailLink(sfConfig::get('app_info_email')) ?>.</p>
<br />
</div>
<div id="sidebar">
<? if($isAuthenticated): ?>
  <?php include_component('user', 'projects') ?>
  <?php include_component('default', 'inviteMembers') ?>
<? else: ?>
	<?php include_component('auth', 'register'); ?>
<? endif; ?>
</div>