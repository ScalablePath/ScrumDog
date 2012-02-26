<? if(count($nav_array)>0): ?>
  <ul id="subnav">
  <? foreach($sf_data->getRaw('nav_array') as $nav_item): ?>
    <li<? if(isset($nav_item['active']) && $nav_item['active']): ?> class="active"<? endif; ?>>
      <a href="<? echo(url_for($nav_item['link'])); ?>" title="<? echo($nav_item['text']); ?>"><? echo($nav_item['text']); ?></a>
    </li>
  <? endforeach; ?>
  </ul>
<? endif; ?>