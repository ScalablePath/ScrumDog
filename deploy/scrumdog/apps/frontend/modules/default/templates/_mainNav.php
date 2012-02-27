<ul id="mainnav" class="clearfix main">
	<?php foreach($sf_data->getRaw('nav_array') as $nav_item): ?>
		<li<?php if(isset($nav_item['active']) && $nav_item['active']): ?> class="active"<?php endif; ?>>
			<a href="<?php echo(url_for($nav_item['link'])); ?>" title="<?php echo($nav_item['text']); ?>"><span><?php echo($nav_item['text']); ?></span></a>
		</li>
	<?php endforeach ?> 
	<?php if($sf_user->isAuthenticated()): ?>
		<?php include_component('default', 'memberNav') ?>
	<?php endif; ?>
</ul>