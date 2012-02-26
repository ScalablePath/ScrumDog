<ul <?php if($mode!='message_index'): ?>id="file-list" <?php endif; ?>class="clearfix" <?php if($class=='hide'): ?>style="opacity: 0;"<?php endif;?>>
<?php foreach($files as $file): ?>
	<li id="file_item-<?php echo $file->getId() ?>" style="padding: 0; background: none;">
		<a id="file_link-<?php echo $file->getId() ?>" href="<?php echo $file->File->getSrc(); ?>" target="_blank" class="<?=$file->File->getModal();?>" title="<?=$file->File->getInfo();?>" alt="<?=$file->File->getTruncatedFileName();?>"><img src="<?php echo $file->File->getIconSrc(); ?>" alt="<?php echo $file->File->filename; ?>" title="<?php echo $file->File->filename; ?>" /></a>
		<a href="<?php echo $file->File->getSrc(); ?>" target="_blank" class="<?=$file->File->getModal();?>" title="<?=$file->File->getInfo();?>" alt="<?=$file->File->getTruncatedFileName();?>"><?php echo $file->File->getTruncatedFileName(); ?></a>
		
		<span id="file_delete-<?php echo $file->getId() ?>" class="file icon delete" title="Delete">Delete</span>
	</li>
<?php endforeach ?>
</ul>