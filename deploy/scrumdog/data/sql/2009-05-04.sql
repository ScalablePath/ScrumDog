#executed on production on 5/11

ALTER TABLE `sd_task` ADD `is_archived` TINYINT( 1 ) NULL DEFAULT '0' AFTER `invested_hours` ;

ALTER TABLE `sd_task` ADD INDEX ( `is_archived` ) ;