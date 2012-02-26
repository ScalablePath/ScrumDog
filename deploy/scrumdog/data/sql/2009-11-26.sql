#executed on production on 11/29;

ALTER TABLE `sd_task` ADD `parent_id` INT NULL AFTER `creator_user_id` ;

#ALTER TABLE `sd_task` ADD INDEX ( `parent_id_idx` ) ;
 
ALTER TABLE `sd_task` ADD CONSTRAINT `sd_task_FK_5` FOREIGN KEY (`parent_id`) REFERENCES `sd_task` (`id`);