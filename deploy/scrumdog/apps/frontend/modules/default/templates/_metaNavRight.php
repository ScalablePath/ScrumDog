<?php $first = true; foreach($sf_data->getRaw('nav_array') as $nav_item): ?>
	<?php if($first==false): ?> &bull; <?php endif; $first = false; ?>
	<a href="<?php echo(url_for($nav_item['link'])); ?>"><?php echo($nav_item['text']); ?></a>
<?php endforeach ?>