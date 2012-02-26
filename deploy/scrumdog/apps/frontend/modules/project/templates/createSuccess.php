<?php slot('page_title') ?>Create Project<?php end_slot() ?>
<div id="main">
	<h1>Create Project</h1>
	<div class="box form">
	<p class="required">Fields marked with * are required.</p>
	<form action="<? echo url_for('@member_createproject') ?>" method="POST">
		<? echo($form); ?>
		<div class="item">
			<div class="field">
				<input type="submit" value="Create"/>
			</div>
		</div>
	</form>
	</div>
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Note</h2>
	<p>Creating a project is so easy.  All you need to do is choose a name!</p>
  </div>
</div>
