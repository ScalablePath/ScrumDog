#executed on production on 4/21

ALTER TABLE `sd_user` CHANGE `profile_image` `profile_image` INT NULL DEFAULT NULL;

ALTER TABLE `sd_user` CHANGE `sponsor_image` `sponsor_image` INT NULL DEFAULT NULL;

-- the following line may throw an error
ALTER TABLE `sd_file` DROP FOREIGN KEY sd_file_FK_1;

ALTER TABLE `sd_file` DROP `task_id`;

CREATE TABLE IF NOT EXISTS `sd_task_file` (
  `task_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `task_id_idx` (`task_id`),
  KEY `file_id_idx` (`file_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `sd_task_file`
  ADD CONSTRAINT `sd_task_file_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `sd_file` (`id`),
  ADD CONSTRAINT `sd_task_file_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `sd_task` (`id`);