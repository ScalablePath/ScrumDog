<?php slot('page_title') ?>Forgot Username or Password<?php end_slot() ?>
<h1>Forgot Username or Password</h1>
<div id="main">
	<div class="box form">
	<h2>Forgot your login info? No worries.</h2>
	<p>Fill out this form with your username or your email address (whichever you can remember) and we'll email your login info to you.</p>
	<form action="<? echo url_for('@user_password') ?>" method="POST">
		<? echo($form); ?>
		<div class="item">
			<div class="field">
				<input type="submit" value="Submit"/>
			</div>
		</div>
	</form>
	</div>
</div>