 <div class="ctd-innards">
	<form autocomplete="off">
		
		<div class="item">
			<label for="pop-name">Name <span class="required">*</span></label>
			<div class="field">
				<input class="text" style="width: 200px;" name="task[name]" id="pop-name" />
			</div>
		</div>

		<div class="item">
			<label for="pop-estimated_hours"><nobr>Estimated Hours<?php if($sprint_id): ?> <span class="required">*</span><?php endif; ?></nobr></label>
			<div class="field">
				<input class="text numeric" type="text" name="task[estimated_hours]" id="pop-estimated_hours" />
			</div>
		</div>
		
		<div class="item">
			<label for="pop-parent_id">Parent Task</label>
				<div class="field">
					<select id="pop-parent_id" name="task[parent_id]" autocomplete="off">
					<option value="">-none-</option>
					<? foreach($sf_data->getRaw('parentTasks') as $parentTask): ?>
						<option<?php if($selectedTaskId==$parentTask->getId()): ?> selected="selected"<?php endif; ?> value="<?php echo($parentTask->getId()) ?>"><?php echo(substr($parentTask->getName(),0,25)); ?><?php if(strlen($parentTask->getName())>25): ?>&#8230;<?php endif; ?> <?php echo('('.$parentTask->getId().')') ?></option>
					<? endforeach; ?>
				</select>
			</div>
		</div>
		
	<?php if($dialogmode=='backlog'): ?>
	
		<div class="item">
			<label for="pop-business_value">Business Value</label>
				<div class="field">
					<select id="pop-business_value" name="task[business_value]" autocomplete="off">
					<? foreach(SdTaskTable::$businessValueArr as $k => $v): ?>
						<option<?php if($v=='Normal'): ?> selected="selected"<?php endif; ?> value="<?=$k?>"><?=$v?></option>
					<? endforeach; ?>
				</select>
			</div>
		</div>
		
	<?php else: ?>
		
		<div class="item">
			<label for="pop-user_id">Assigned To</label>
				<div class="field">
					<select id="pop-user_id" name="task[user_id]" autocomplete="off">
					<option value="">-unassigned-</option>
					<? foreach($projectUserArray as $k => $v): ?>
						<option value="<?=$k?>"><?=$v?></option>
					<? endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="item">
			<label for="pop-status">Status</label>
				<div class="field">
					<select id="pop-status" name="task[status]" autocomplete="off">
					<? foreach(SdTaskTable::$statusArr as $k => $v): ?>
						<option value="<?=$k;?>"><?=$v;?></option>
					<? endforeach; ?>
				</select>
			</div>
		</div>
		
		<div class="item">
			<label for="pop-priority">Priority</label>
				<div class="field">
					<select id="pop-priority" name="task[priority]" autocomplete="off">
					<? foreach(SdTaskTable::$priorityArr as $k => $v): ?>
						<option<?php if($v=='Normal'): ?> selected="selected"<?php endif; ?> value="<?=$k?>"><?=$v?></option>
					<? endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
	</form>
</div>