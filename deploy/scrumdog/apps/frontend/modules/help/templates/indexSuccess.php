<?php slot('page_title') ?>Frequently Asked Questions<?php end_slot() ?>
<?php slot('page_heading') ?>Frequently Asked Questions<?php end_slot() ?>
<div id="main">
<h1>Frequently Asked Questions</h1>

<h2>What is scrum?</h2>
<p>The term <a target="_blank" href="http://en.wikipedia.org/wiki/Scrum_(development)">scrum</a> comes from the sport of rugby
where team members huddle together at the beginning of a play (similar to american football).  In a scrum, the project team convenes quickly
and each team member answers three daily questions:</p>
<ol>
	<li><b>What did you accomplish yesterday?</b></li>
	<li><b>What will you accomplish today?</b></li>
	<li><b>What is getting in your way?</b></li>
</ol>
<p>ScrumDog's daily questions feature saves you time by tracking these daily scrum questions and hours spent on tasks.
</p>

<h2>What is agile development?</h2>
<p>Agile development is a methodology created for building software by constantly adjusting to changing requirements.
There are a lot of great resources on the web.  Read the 
<a target="_blank" href="http://en.wikipedia.org/wiki/Agile_software_development">Wikipedia entry for <b>Agile Software Development</b></a> and the  
<a target="_blank" href="http://agilemanifesto.org/"><b>Agile Manifesto</b></a></p>

<h2>Why should I use ScrumDog for my project?</h2>
<p>ScrumDog will help you to complete your project faster and with less stress.
By forcing you to use the proven scrum process, our online software will help you 
set manageable schedules, prioritize tasks, and get work done on time.</p>

<h2>How much does it cost to use ScrumDog?</h2>
<p>During our beta period, it is free to use ScrumDog.  After the beta period ends, normal pricing will be applied.  Don't worry, it won't cost a lot.</p>
<p>Have questions? Don't hesitate to email us at <?php echo Fluide_Symfony_Util::emailLink(sfConfig::get('app_info_email')) ?>.</p>
<br />
</div>
<div id="sidebar">
<?php if($isAuthenticated): ?>
  <?php include_component('user', 'projects') ?>
  <?php include_component('default', 'inviteMembers') ?>
<?php else: ?>
	<?php include_component('auth', 'register'); ?>
<?php endif; ?>
</div>