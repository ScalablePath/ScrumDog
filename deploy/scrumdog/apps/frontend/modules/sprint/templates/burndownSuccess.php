<?php slot('page_title') ?>Burndown Chart<?php end_slot() ?>
<?php slot('project_title') ?><?php echo($project->getName()); ?><?php end_slot() ?>

<div id="main">
	<h2>Burndown Chart</h2>
	<!-- amline script-->
	  <script type="text/javascript" src="/js/swfobject.js"></script>
		<div id="flashcontent">
			<strong>You need to upgrade your Flash Player to view this content.</strong>
		</div>	
		<script type="text/javascript">
			// <![CDATA[		
			var so = new SWFObject("/amcharts/amline.swf", "amline", "100%", "400", "8", "#FFFFFF");
			so.addVariable("path", "/amcharts/");
			so.addVariable("settings_file", encodeURIComponent("/amcharts/burndown_settings.xml"));
			so.addVariable("data_file", encodeURIComponent("<?php echo(url_for('@sprint_burndown-data?sprint_id='.$sprint_id)); ?>"));
			so.write("flashcontent");
			// ]]>
		</script>
	<!-- end of amline script -->
</div>
<div id="sidebar">
  <div class="box grey">
	<h2>Tip</h2>
	<p>Tasks are only deducted from the total hours remaining when their status has been changed to <b>accepted</b>.
  </div>
</div>

