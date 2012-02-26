<?php slot('page_title') ?>Invite<?php end_slot() ?>
<?php slot('page_heading') ?>Invite<?php end_slot() ?>
<div id="main">
	<h1>Invite people to join ScrumDog.</h1>
	<div class="box form">
	<form action="<? echo url_for('@member_invitemembers') ?>" method="POST">
		<? echo($form); ?>
		<div class="item">
			<div class="field">
				<input type="submit" value="Invite"/>
			</div>
		</div>
	</form>
	</div>
</div>