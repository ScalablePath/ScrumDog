#executed on production on 8/30

ALTER TABLE `sd_task_hours` ADD `product_id` INT( 4 ) NOT NULL AFTER `task_id` ;

ALTER TABLE `sd_task_hours` ADD INDEX ( `product_id` );