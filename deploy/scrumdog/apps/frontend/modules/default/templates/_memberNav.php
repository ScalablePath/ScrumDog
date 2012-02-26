<?php foreach($sf_data->getRaw('nav_array') as $nav_item): ?>
  <li id="mainnav-account" <? if(isset($nav_item['active']) && $nav_item['active']): ?> class="active"<? endif; ?>>
    <a href="<? echo(url_for($nav_item['link'])); ?>" title="<? echo($nav_item['text']); ?>"><span><? echo($nav_item['text']); ?></span></a>
  </li>
<?php endforeach ?> 
