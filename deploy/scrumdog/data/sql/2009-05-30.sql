#executed on production on 8/30

#reworking the question table

ALTER TABLE `sd_question` DROP FOREIGN KEY sd_question_FK_1;

ALTER TABLE `sd_question` CHANGE `sprint_id` `product_id` INT( 11 ) NOT NULL ;

ALTER TABLE `sd_question` ADD FOREIGN KEY ( `product_id` ) REFERENCES `sd_product` ( `id` ); 

ALTER TABLE `sd_question` DROP `type`;

ALTER TABLE `sd_question` CHANGE `answer` `work` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE `sd_question` ADD `obstacles` TEXT NULL AFTER `work` ;

#question history table

CREATE TABLE IF NOT EXISTS `sd_question_history` (
  `id` int(11) NOT NULL auto_increment,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `change_type` varchar(30) NOT NULL,
  `previous_value` text,
  `new_value` text,
  `previous_id` int(11) default NULL,
  `new_id` int(11) default NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `sd_question_history_FI_1` (`question_id`),
  KEY `sd_question_history_FI_2` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Constraints for table `sd_question_history`
--
ALTER TABLE `sd_question_history`
  ADD CONSTRAINT `sd_question_history_FK_1` FOREIGN KEY (`question_id`) REFERENCES `sd_question` (`id`),
  ADD CONSTRAINT `sd_question_history_FK_2` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`);
