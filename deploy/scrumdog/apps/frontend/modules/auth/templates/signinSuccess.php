<?php slot('page_title') ?>Login<?php end_slot() ?>
<h1>Login</h1>
<div id="main">
	<div class="box form">
		<?php if($form->hasErrors()): ?>
		<h2>Something has gone awry.</h2>
		<?php elseif(isset($_GET['redirect'])): ?>
		<h2>Sign in and we'll send you on your way.</h2>
		<?php else: ?>
		<h2>Come on in!  The water's nice.</h2>
		<?php endif; ?>
		<form id="login-form" action="<?php echo url_for('@user_signin') ?>" method="POST">
		<?php echo($form); ?>
            <div class="item">
				<div class="field">
					<?php if($renderRedirectInput): ?>
						<input type="hidden" name="redirect" value="<?=$redirectUrl?>"/>
					<?php endif; ?>
					<input type="submit" value="Login"/>
				</div>
			</div>
			<div class="item">
                <a href="<?php echo url_for('@user_password') ?>" title="forgot login info">forgot login info</a>
            </div>
		</form>
	</div>
</div>
<?php if(!$isAuthenticated): ?>
<div id="sidebar">
	<div class="box grey">
		<h2>Not a member yet?</h2>
		<p>What's the matter with you!<br />
		 <a href="<?php echo url_for('@user_register') ?>">Click here to register</a>.
	</div>
</div>
<?php endif; ?>