#executed on production on 8/30

ALTER TABLE `sd_sprint` CHANGE `scrum_start_time` `scrum_start_time` TIME NULL;

ALTER TABLE `sd_sprint` CHANGE `scrum_time_zone_name` `scrum_time_zone_name` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE `sd_task` ADD `date_confirmed` DATE NULL ;