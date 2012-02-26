<div class="box form">

<?php use_javascript('/js/jquery/plugins/autocomplete/autocomplete.js')?>

<h2>Add Members</h2>
<form action="<? echo url_for('@project_addmembers?project_id='.$project_id) ?>" method="POST">
    <? echo($form); ?>
    <div class="item">
		<div class="field">
			<input type="submit" value="Add Members"/>
		</div>
	</div>
</form>
</div>