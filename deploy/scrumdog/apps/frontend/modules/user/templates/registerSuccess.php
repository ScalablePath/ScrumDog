<?php slot('page_title') ?>Register<?php end_slot() ?>

<h1>Register</h1>
<div class="box form">
	<h2>Not a member yet? Sign up now!</h2>
	<form action="<? echo url_for('@user_register') ?>" method="POST">
		
		<div class="item">
			<label></label>
			<div class="field"><?php echo $form->renderGlobalErrors();?></div>
		</div>
			
		<?php
			foreach($form as $widget)
			{
				$name = $widget->getName();
				
				if ($name != 'terms' && $name != '_csrf_token')
					echo $widget->renderRow();
				elseif($name == 'terms')
				{
					?>
					<div class="item">
						<label for="user_<?php echo $name?>"><?php echo $widget->renderLabel()?></label>
						<div class="field"> 
							<?php echo $widget->renderError()?>
							<?php echo $widget->render()?>
							<span class="help"><?php echo $widget->renderHelp()?></span>
						</div>
					</div>
					<?php
				}else{
					echo $widget->render();
				}//endif
			}//endfor
			
			if ($email_to_registered) {
				?>
				<script>
				$(document).ready(function(){
					$('#user_email').attr("value",function() {return '<?php echo $email_to_registered;?>';});
				});
				</script>
				<?php
			} 
		?>
		
		<?php if ($sf_params->has('key')):?>
			<input type="hidden" id="user_key" name="key" value="<?php echo $sf_params->get('key')?>"/>
		<?php endif;?>
		
		<div class="item">
			<div class="field">
				<input type="submit" value="Sign Up"/>
			</div>
		</div>
	</form>
	
	<div class="hide form" id="terms-popup">
		<?php include_partial('terms/Terms', array()); ?>
	</div>
	
</div>