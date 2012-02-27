<?php foreach($sf_data->getRaw('nav_array') as $nav_item): ?>
  <li id="mainnav-account" <?php if(isset($nav_item['active']) && $nav_item['active']): ?> class="active"<?php endif; ?>>
    <a href="<?php echo(url_for($nav_item['link'])); ?>" title="<?php echo($nav_item['text']); ?>"><span><?php echo($nav_item['text']); ?></span></a>
  </li>
<?php endforeach ?> 
