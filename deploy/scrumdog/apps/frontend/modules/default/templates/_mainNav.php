<ul id="mainnav" class="clearfix main">
	<?php foreach($sf_data->getRaw('nav_array') as $nav_item): ?>
		<li<? if(isset($nav_item['active']) && $nav_item['active']): ?> class="active"<? endif; ?>>
			<a href="<? echo(url_for($nav_item['link'])); ?>" title="<? echo($nav_item['text']); ?>"><span><? echo($nav_item['text']); ?></span></a>
		</li>
	<?php endforeach ?> 
	<? if($sf_user->isAuthenticated()): ?>
		<?php include_component('default', 'memberNav') ?>
	<? endif; ?>
</ul>