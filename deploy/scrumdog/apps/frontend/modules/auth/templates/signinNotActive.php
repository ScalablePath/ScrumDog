<?php slot('page_title') ?>Login<?php end_slot() ?>
<h1>Login</h1>
<div id="main">
	<div class="box grey">
		<h2>A ScrumDog account with that username exists, but it has not been activated yet.</h2>
		<p>Please check your email inbox for your original account activation email or <a href="<?php echo url_for('@user_resendactivation?resend[username]='.$username)?>">Click here</a> resend the activation email.</p>
	</div>
</div>