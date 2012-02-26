#executed on production on 4/21

ALTER TABLE `sd_task_comment` ADD `updated_at` DATETIME NOT NULL ;
ALTER TABLE `sd_task_comment` CHANGE `created_at` `created_at` DATETIME NOT NULL;  

ALTER TABLE `sd_confirmation` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_confirmation` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_file` CHANGE `path` `filename` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `sd_file` ADD `created_at` DATETIME NOT NULL ;

ALTER TABLE `sd_invitation` ADD `created_at` DATETIME NOT NULL ;

#executed to here

ALTER TABLE `sd_product` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_product` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_product_user` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_product_user` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_question` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_question` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_question_task` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_question_task` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_sponsorship_history` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_sponsorship_history` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_sponsorship_request` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_sponsorship_request` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_sponsorship_request_history` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_sponsorship_request_history` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_sprint` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_sprint` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_task` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_task` CHANGE `updated_at` `updated_at` DATETIME NOT NULL; 

ALTER TABLE `sd_task_history` CHANGE `created_at` `created_at` DATETIME NOT NULL;

ALTER TABLE `sd_user` CHANGE `created_at` `created_at` DATETIME NOT NULL;
ALTER TABLE `sd_user` CHANGE `updated_at` `updated_at` DATETIME NOT NULL;