-- Altering task_history table 

ALTER TABLE `sd_task_history` CHANGE `type` `change_type` VARCHAR( 30 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `sd_task_history` CHANGE `description` `previous_value` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `sd_task_history` DROP `old_id`;

ALTER TABLE `sd_task_history` CHANGE `new_id` `new_value` TEXT NULL DEFAULT NULL;

ALTER TABLE `sd_task_history` ADD `previous_id` INT NULL AFTER `new_value` ,
ADD `new_id` INT NULL AFTER `previous_id` ;