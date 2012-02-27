<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php if (has_slot('page_title')): ?>
      <title>ScrumDog : <?php include_slot('page_title') ?></title>
    <?php else: ?>
      <?php include_title() ?>
    <?php endif; ?>
	
	<?php echo stylesheet_tag('/css/reset.css') ?>
	<?php echo stylesheet_tag('/css/main.css') ?>
	<?php echo stylesheet_tag('/js/jquery/theme/ui.core.css') ?>
	<?php echo stylesheet_tag('/js/jquery/theme/ui.theme.css') ?>
	<?php echo stylesheet_tag('/js/jquery/theme/ui.datepicker.css') ?>
	<?php echo stylesheet_tag('/js/jquery/theme/ui.dialog.css') ?>
	<?php echo stylesheet_tag('/js/jquery/plugins/autocomplete/css/jquery.autocomplete.css') ?> <!-- Autocomplete plugin css -->
	
	<?php echo javascript_include_tag('/js/jquery/jquery-1.3.1.js') ?>
	<?php echo javascript_include_tag('/js/jquery/jquery-ui-personalized-1.6rc6.js') ?>
	<?php echo javascript_include_tag('/js/jquery/plugins/nyroModal/jquery.nyroModal-1.5.0.js') ?> <!-- jquery/plugins/lightbox/jquery.lightbox.js -->
	<?php echo javascript_include_tag('/js/jquery/plugins/json/jquery.json-1.3.js') ?>
	<?php echo javascript_include_tag('/js/jquery/plugins/numeric/jquery.numeric.js') ?>
	<?php echo javascript_include_tag('/js/jquery/plugins/tooltip/jquery.tooltip.js') ?>
	<?php echo javascript_include_tag('/js/jquery/plugins/autoResize/jquery.autoResize.js') ?>
	<?php echo javascript_include_tag('/js/jquery/plugins/parseclass/jquery.parseclass.js') ?>
	<?php echo javascript_include_tag('/js/jquery/plugins/loader/jquery.loader.js') ?>
	<?php echo javascript_include_tag('/js/jquery/plugins/autocomplete/jquery.autocomplete.js') ?> 		<!-- Autocomplete plugin js -->
	<?php echo javascript_include_tag('/js/webtoolkit.aim.js') ?>
	<?php echo javascript_include_tag('/js/Task.js') ?>
	<?php echo javascript_include_tag('/js/Archive.js') ?>
	<?php echo javascript_include_tag('/js/Backlog.js') ?>
	<?php echo javascript_include_tag('/js/Questions.js') ?>
	<?php echo javascript_include_tag('/js/BacklogSprint.js') ?>
	<?php echo javascript_include_tag('/js/BacklogUser.js') ?>
	<?php echo javascript_include_tag('/js/Message.js') ?>
	<?php echo javascript_include_tag('Terms') ;?>
	
    <link rel="shortcut icon" href="/favicon.ico" />
  </head>
  <body>
<?php $navScope = sfContext::getInstance()->getRequest()->getParameter('nav_scope'); ?>
    <div id="container" class="<?php echo($navScope); ?>-scope">
      <div id="header">
		<p class="metanav">
			<?php include_component('default', 'metaNavLeft') ?>
			<?php include_component('default', 'metaNavRight') ?>
		</p>
<!--
        <div id="logo">
          <a href="<?php echo url_for('@homepage') ?>"><img id="logo" src="/images/logo.gif" alt="ScrumDog" /></a>
        </div>
-->
        <?php if (has_slot('project_title')): ?>
          <span id="project_title"><?php include_slot('project_title') ?></span>
        <?php else: ?>
          <span id="project_title">ScrumDog <sup>beta</sup></span>
        <?php endif; ?>
        <?php if($navScope=='project'): ?>
          <?php include_component('default', 'projectNav') ?>
        <?php else: ?>
          <?php include_component('default', 'mainNav') ?>          
        <?php endif; ?>
        <?php if(!$sf_user->isAuthenticated()): ?>
          <?php include_component('user', 'headerSignin') ?>
        <?php endif; ?>
		<?php include_component('default', 'subNav') ?>
      </div>
      <div id="page">
		<?php if ($sf_user->hasFlash('success')): ?>
          <div class="flash_success"><?php echo $sf_user->getFlash('success', ESC_RAW) ?></div>
        <?php endif; ?>

        <?php if ($sf_user->hasFlash('notice')): ?>
          <div class="flash_notice"><?php echo $sf_user->getFlash('notice', ESC_RAW) ?></div>
        <?php endif; ?>

        <?php if ($sf_user->hasFlash('error')): ?>
          <div class="flash_error"><?php echo $sf_user->getFlash('error', ESC_RAW) ?></div>
        <?php endif; ?>
        <div id="content">
          <?php echo $sf_content ?>
        </div>
      </div>
      <?php include_component('default', 'footer') ?>
    </div>
    <script type="text/javascript">
        var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
    </script>
    <script type="text/javascript">
        try 
        {
            var pageTracker = _gat._getTracker("UA-16194332-1");
            pageTracker._trackPageview();
        } 
        catch(err)
        {}
    </script>
  </body>
</html>
