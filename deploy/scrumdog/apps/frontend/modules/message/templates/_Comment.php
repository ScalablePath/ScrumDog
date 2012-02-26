<div id="outer_comment_paragraph"<?php if($class=='hide'): ?> style="opacity: 0;"<?php endif;?>>
	<?php foreach($comments as $comment): ?>
	<p>
		<?php 
			$imageFile = $comment->User->ProfileImage;
			if(is_null($imageFile))
			{
				if($comment->User->getGender()=='female')
					$avatarSrc = "/images/avatar/female_50x50.gif";
				else
					$avatarSrc = "/images/avatar/male_50x50.gif";
			}
			else
				$avatarSrc = $imageFile->getThumbnailSrc(50, 50, 'scale');
		?>
		<a href="<?php echo url_for('@member_profile?username='.$comment->User->username) ?>"><img src="<?php echo $avatarSrc ?>" /></a>
		<a href="<?php echo url_for('@member_profile?username='.$comment->User->username) ?>"><?php echo $comment->User->full_name; ?></a><br/>
		&quot;<?php echo str_replace("\n", '<br/>', $comment->comment); ?>&quot;<br />
		<i><?php echo(date("D M, j \a\\t g:i a", strtotime($comment->created_at)).' '.sfConfig::get('app_server_timezone')); ?></i><br />
	</p>
	<?php endforeach;  ?>
</div>