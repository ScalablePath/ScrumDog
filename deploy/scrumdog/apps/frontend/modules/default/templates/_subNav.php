<?php if(count($nav_array)>0): ?>
  <ul id="subnav">
  <?php foreach($sf_data->getRaw('nav_array') as $nav_item): ?>
    <li<?php if(isset($nav_item['active']) && $nav_item['active']): ?> class="active"<?php endif; ?>>
      <a href="<?php echo(url_for($nav_item['link'])); ?>" title="<?php echo($nav_item['text']); ?>"><?php echo($nav_item['text']); ?></a>
    </li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>