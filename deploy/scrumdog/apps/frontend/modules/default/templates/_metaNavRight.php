<?php $first = true; foreach($sf_data->getRaw('nav_array') as $nav_item): ?>
	<? if($first==false): ?> &bull; <? endif; $first = false; ?>
	<a href="<? echo(url_for($nav_item['link'])); ?>"><? echo($nav_item['text']); ?></a>
<?php endforeach ?>