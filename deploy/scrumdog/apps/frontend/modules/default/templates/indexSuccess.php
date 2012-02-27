<?php slot('page_title') ?>Welcome<?php end_slot() ?>
<div id="main">
	<img src="/images/ScrumDog_Mascot.gif" style="float: right" />
	<h1>Welcome to ScrumDog</h1>
	<h2>Scrum made simple.<!--&#0153;--></h2>
	<p>ScrumDog is a project management web application that is based on the scrum methodology.
	Our task-focused flavor of scrum and simple user interface make sure that you and your team will get more things done faster.
	ScrumDog works great for teams who are spread across multiple locations and encourages you and your team to use scrum development practices.
	You create a <b>project</b>, <b>sprint</b>, and <b>tasks</b> for your team to work on.
	Daily questions and dashboards help you manage the scrum process with tools such as the the <b>burn-down chart</b>, <b>backlog</b>, and <b>daily questions</b>.
	ScrumDog lets you spend less time managing and more time actually working on your project.
	</p>
<h3><a href="<?php echo url_for('@how_it_works'); ?>">Learn more about how ScrumDog works >></a></h3><br />
<!--
  <h2>How much does it cost?</h2>
  <p>During our beta period, ScrumDog is free.
After the beta period, ScrumDog will charge a monthly fee based on the number of projects and team members you need.
Don't worry, we realize the importance of providing value to our customers and will always be priced competitively.</p>
-->
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