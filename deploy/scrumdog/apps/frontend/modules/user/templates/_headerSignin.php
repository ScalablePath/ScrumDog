<?php if($renderForm): ?>
<div id="header_signin">
<form action="<?php echo url_for('@user_signin') ?>" method="post">
	<div class="item">
		<div class="field">
			<?php echo $form['remember']->render() ?>
			<label for="user_remember">remember me</label>
			<a href="<?php echo url_for('@user_password') ?>">forgot login info</a>
		</div>
	</div>
	<div class="item">
		<div class="field">
			<?php echo $form['username']->render() ?>
			<?php echo $form['password']->render() ?>
			<?php echo $form['_csrf_token']->render() ?>
			<input type="submit" value="Sign In"/>
		</div>
	</div> 
</form>
</div>
<?php endif; ?>