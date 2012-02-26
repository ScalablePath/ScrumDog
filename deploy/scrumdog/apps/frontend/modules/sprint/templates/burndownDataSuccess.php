<?php decorate_with(false); ?>
<chart>
	<series>
	<?php foreach($sprintDays as $i => $day): ?>
		<value xid="<?php echo($i+1); ?>"><?php echo($day->date); ?></value>
	<?php endforeach; ?>
	</series>
	<graphs>
		<graph gid="1">
			<?php $remainingHours = $totalSprintHours; foreach($sprintDays as $i => $day): ?>
				<?php if(!$day->isFutureDay): ?>
			<value xid="<?php echo($i+1); ?>"><?php $remainingHours -= $day->confirmedHours; echo($remainingHours); ?></value>
				<?php endif ?>
			<?php endforeach; ?>
		</graph>
		<graph gid="2">
		<?php foreach($sprintDays as $i => $day): ?>
			<value xid="<?php echo($i+1); ?>"><?php echo($totalSprintHours - ($i * $idealDailyHours)); ?></value>
		<?php endforeach; ?>
		</graph>
	</graphs>
	<guides>	                                   
	</guides>	
</chart>