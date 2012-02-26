#executed on production on 11/2

CREATE TABLE `sd_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text,
  `is_archived` tinyint(4) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id_idx` (`product_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `sd_message_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `sd_product` (`id`),
  CONSTRAINT `sd_message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sd_message_comment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_id_idx` (`message_id`),
  KEY `user_id_idx` (`user_id`),
  CONSTRAINT `sd_message_comment_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `sd_message` (`id`),
  CONSTRAINT `sd_message_comment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `sd_message_file` (
  `id` bigint(20) NOT NULL auto_increment,
  `message_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `message_id_idx` (`message_id`),
  KEY `file_id_idx` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `sd_message_file`
  ADD CONSTRAINT `sd_message_file_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `sd_file` (`id`),
  ADD CONSTRAINT `sd_message_file_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `sd_message` (`id`);
  
CREATE TABLE IF NOT EXISTS `sd_message_history` (
  `id` bigint(20) NOT NULL auto_increment,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `change_type` varchar(30) NOT NULL,
  `previous_value` text,
  `new_value` text,
  `previous_id` int(11) default NULL,
  `new_id` int(11) default NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `message_id_idx` (`message_id`),
  KEY `user_id_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `sd_message_history`
  ADD CONSTRAINT `sd_message_history_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `sd_message` (`id`),
  ADD CONSTRAINT `sd_message_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`);
  
 ALTER TABLE `sd_task` CHANGE `is_archived` `is_archived` TINYINT( 4 ) NULL DEFAULT '0';

