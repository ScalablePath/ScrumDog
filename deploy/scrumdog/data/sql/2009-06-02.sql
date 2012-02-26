#executed on production on 8/30

DROP TABLE `sd_question_task`;

CREATE TABLE IF NOT EXISTS `sd_task_hours` (
  `date` date default NULL,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `hours` float default NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `sd_task_hours_FI_1` (`user_id`),
  KEY `sd_task_hours_FI_2` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `sd_task_hours`
  ADD CONSTRAINT `sd_task_hours_FK_1` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`),
  ADD CONSTRAINT `sd_task_hours_FK_2` FOREIGN KEY (`task_id`) REFERENCES `sd_task` (`id`);