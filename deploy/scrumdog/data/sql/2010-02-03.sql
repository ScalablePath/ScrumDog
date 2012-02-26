ALTER TABLE `sd_invitation` CHANGE `products` `projects` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `sd_user` DROP `allow_product_view`;

#alter table names
RENAME TABLE `sd_product`  TO `sd_project` ;
RENAME TABLE `sd_product_user`  TO `sd_project_user` ;



#drop constraints
ALTER TABLE `sd_message` DROP FOREIGN KEY `sd_message_ibfk_2`;
ALTER TABLE `sd_project_user` DROP FOREIGN KEY `sd_product_user_FK_1`;
ALTER TABLE `sd_question` DROP FOREIGN KEY `sd_question_ibfk_1`;
ALTER TABLE `sd_sprint` DROP FOREIGN KEY `sd_sprint_FK_1`;
ALTER TABLE `sd_task` DROP FOREIGN KEY `sd_task_FK_1`;
 
#alter fields
ALTER TABLE `sd_message` CHANGE `product_id` `project_id` INT( 11 ) NOT NULL;
ALTER TABLE `sd_project_user` CHANGE `product_id` `project_id` INT( 11 ) NOT NULL;
ALTER TABLE `sd_question` CHANGE `product_id` `project_id` INT( 11 ) NOT NULL;
ALTER TABLE `sd_sprint` CHANGE `product_id` `project_id` INT( 11 ) NOT NULL;
ALTER TABLE `sd_task` CHANGE `product_id` `project_id` INT( 11 ) NOT NULL;
ALTER TABLE `sd_task_hours` CHANGE `product_id` `project_id` INT( 11 ) NOT NULL;

#Add constraints again
ALTER TABLE `sd_message` ADD CONSTRAINT `sd_message_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `sd_project` (`id`);
ALTER TABLE `sd_project_user` ADD CONSTRAINT `sd_product_user_FK_1` FOREIGN KEY (`project_id`) REFERENCES `sd_project` (`id`);
ALTER TABLE `sd_question` ADD CONSTRAINT `sd_question_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `sd_project` (`id`);
ALTER TABLE `sd_sprint` ADD CONSTRAINT `sd_sprint_FK_1` FOREIGN KEY (`project_id`) REFERENCES `sd_project` (`id`);
ALTER TABLE `sd_task` ADD CONSTRAINT `sd_task_FK_1` FOREIGN KEY (`project_id`) REFERENCES `sd_project` (`id`);
#new
ALTER TABLE `sd_task_hours` ADD CONSTRAINT `sd_task_hours_FK_3` FOREIGN KEY (`project_id`) REFERENCES `sd_project` (`id`);
