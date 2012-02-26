#executed on production on 8/30

 ALTER TABLE `sd_user` CHANGE `time_zone` `time_zone` VARCHAR( 50 ) NOT NULL ;

 ALTER TABLE `sd_user` DROP INDEX `time_zone`;

ALTER TABLE `sd_user` ADD `time_zone_offset` INT NOT NULL AFTER `time_zone` ;

 ALTER TABLE `sd_user` ADD INDEX ( `time_zone_offset` );