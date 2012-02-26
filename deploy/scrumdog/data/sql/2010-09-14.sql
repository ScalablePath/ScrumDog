ALTER TABLE `sd_task_hours` ADD `time` VARCHAR( 10 ) NOT NULL AFTER `date`;
ALTER TABLE `sd_task_hours` ADD `notes` TEXT NOT NULL AFTER `hours`;