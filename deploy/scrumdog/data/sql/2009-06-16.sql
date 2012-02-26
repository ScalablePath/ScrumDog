#executed on production on 8/30

ALTER TABLE `sd_product_user` ADD `send_email` INT NOT NULL DEFAULT '1' AFTER `role` ;

ALTER TABLE `sd_product_user` ADD INDEX ( `send_email` ) ;

ALTER TABLE `sd_user` ADD `time_zone` INT NOT NULL AFTER `phone` ;

ALTER TABLE `sd_user` ADD INDEX ( `time_zone` ) ;