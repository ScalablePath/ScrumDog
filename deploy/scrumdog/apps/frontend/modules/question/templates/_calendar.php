	<table id="question-calendar">
		<thead>
			<tr><th id="cal_prev" class="<?php echo($calendar->prevYear.'-'.$calendar->prevMonth); ?>"><<</th><th class="center" colspan="5"><?php echo($calendar->monthText.' '.$calendar->year); ?></th><th id="cal_next" class="<?php echo($calendar->nextYear.'-'.$calendar->nextMonth); ?>">>></th></tr>
			<tr><th>Sun</th><th>Mon</th><th>Tues</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>
		</thead>
		<tbody>
			<tr>
			<?php $i=1; foreach($calendar->days as $day): ?>
				<td class="<?php echo($day->getClass()); ?>" title="<?php echo($day->getInfo()); ?>">
					<a href="<?php echo(url_for('@project_questions?project_id='.$project_id.'&username='.$questionUser->getUsername().'&date='.date('Y-m-d', $day->timestamp))); ?>">
						<?php echo(date('j', $day->timestamp)); ?>
					</a>
				</td>
				<?php if($i%7==0 && $i<$calendar->dayCount): ?>
				</tr><tr>
				<?php endif; ?>
			<?php $i++; endforeach; ?>
			</tr>
		</tbody>
	</table>