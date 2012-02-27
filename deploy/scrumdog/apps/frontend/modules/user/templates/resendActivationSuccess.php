<?php slot('page_title') ?>Resend Activation<?php end_slot() ?>
<h1>Resend Activation</h1>
<div class="box form">
<h2>Did your activation email get lost in the ether? It happens.</h2>
<p>Fill out this form with your username and we'll resend your activation email.</p>
<form action="<?php echo url_for('@user_resendactivation') ?>" method="POST">
    <?php echo($form); ?>
    <div class="item">
		<div class="field">
			<input type="submit" value="Submit"/>
		</div>
	</div>
</form>
</div>