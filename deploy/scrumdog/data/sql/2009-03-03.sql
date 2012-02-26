-- phpMyAdmin SQL Dump
-- version 2.11.9.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 03, 2009 at 12:42 PM
-- Server version: 5.0.67
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `sd_confirmation`
--

DROP TABLE IF EXISTS `sd_confirmation`;
CREATE TABLE `sd_confirmation` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(32) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `attributes` text,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `sd_confirmation_I_1` (`type`),
  KEY `sd_confirmation_I_2` (`hash`),
  KEY `type_hash` (`type`,`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_file`
--

DROP TABLE IF EXISTS `sd_file`;
CREATE TABLE `sd_file` (
  `id` int(11) NOT NULL auto_increment,
  `task_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `sd_file_FI_1` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_invitation`
--

DROP TABLE IF EXISTS `sd_invitation`;
CREATE TABLE `sd_invitation` (
  `id` int(11) NOT NULL auto_increment,
  `inviter_user_id` int(11) NOT NULL,
  `invitee_user_id` int(11) default NULL,
  `invitee_email` varchar(255) default NULL,
  `products` text,
  `sprints` text,
  `hash` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `sd_invitation_I_1` (`hash`),
  KEY `user_hash` (`hash`),
  KEY `sd_invitation_FI_1` (`inviter_user_id`),
  KEY `sd_invitation_FI_2` (`invitee_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_product`
--

DROP TABLE IF EXISTS `sd_product`;
CREATE TABLE `sd_product` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_product_user`
--

DROP TABLE IF EXISTS `sd_product_user`;
CREATE TABLE `sd_product_user` (
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` int(11) NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `sd_product_user_FI_1` (`product_id`),
  KEY `sd_product_user_FI_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_question`
--

DROP TABLE IF EXISTS `sd_question`;
CREATE TABLE `sd_question` (
  `id` int(11) NOT NULL auto_increment,
  `sprint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `date` date default NULL,
  `answer` text NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `sd_question_FI_1` (`sprint_id`),
  KEY `sd_question_FI_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_question_task`
--

DROP TABLE IF EXISTS `sd_question_task`;
CREATE TABLE `sd_question_task` (
  `question_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `hours` float default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `sd_question_task_FI_1` (`question_id`),
  KEY `sd_question_task_FI_2` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_sponsorship_history`
--

DROP TABLE IF EXISTS `sd_sponsorship_history`;
CREATE TABLE `sd_sponsorship_history` (
  `user_id` int(11) NOT NULL,
  `date` date default NULL,
  `max_sponsorships` int(11) default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `sd_sponsorship_history_FI_1` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_sponsorship_request`
--

DROP TABLE IF EXISTS `sd_sponsorship_request`;
CREATE TABLE `sd_sponsorship_request` (
  `requester_user_id` int(11) NOT NULL,
  `sponsor_user_id` int(11) NOT NULL,
  `sponsored_user_id` int(11) NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `sd_sponsorship_request_FI_1` (`requester_user_id`),
  KEY `sd_sponsorship_request_FI_2` (`sponsor_user_id`),
  KEY `sd_sponsorship_request_FI_3` (`sponsored_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_sponsorship_request_history`
--

DROP TABLE IF EXISTS `sd_sponsorship_request_history`;
CREATE TABLE `sd_sponsorship_request_history` (
  `requester_user_id` int(11) NOT NULL,
  `sponsor_user_id` int(11) NOT NULL,
  `sponsored_user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`),
  KEY `sd_sponsorship_request_history_FI_1` (`requester_user_id`),
  KEY `sd_sponsorship_request_history_FI_2` (`sponsor_user_id`),
  KEY `sd_sponsorship_request_history_FI_3` (`sponsored_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_sprint`
--

DROP TABLE IF EXISTS `sd_sprint`;
CREATE TABLE `sd_sprint` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `scrum_start_time` time NOT NULL,
  `scrum_time_zone_name` varchar(255) NOT NULL,
  `scrum_days` varchar(13) NOT NULL default '1,2,3,4,5',
  `scrum_day_0` tinyint(4) default NULL,
  `scrum_day_1` tinyint(4) default NULL,
  `scrum_day_2` tinyint(4) default NULL,
  `scrum_day_3` tinyint(4) default NULL,
  `scrum_day_4` tinyint(4) default NULL,
  `scrum_day_5` tinyint(4) default NULL,
  `scrum_day_6` tinyint(4) default NULL,
  `active` tinyint(4) default '1',
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `sd_sprint_FI_1` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_task`
--

DROP TABLE IF EXISTS `sd_task`;
CREATE TABLE `sd_task` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `sprint_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `creator_user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `status` int(11) NOT NULL default '0',
  `business_value` int(11) NOT NULL default '1',
  `development_effort` int(11) NOT NULL default '1',
  `priority` int(11) NOT NULL default '1',
  `estimated_hours` varchar(10) default NULL,
  `invested_hours` float default '0',
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `sd_task_FI_1` (`product_id`),
  KEY `sd_task_FI_2` (`sprint_id`),
  KEY `sd_task_FI_3` (`user_id`),
  KEY `sd_task_FI_4` (`creator_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_task_history`
--

DROP TABLE IF EXISTS `sd_task_history`;
CREATE TABLE `sd_task_history` (
  `id` int(11) NOT NULL auto_increment,
  `task_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `description` text,
  `user_id` int(11) NOT NULL,
  `old_id` int(11) default NULL,
  `new_id` int(11) default NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `sd_task_history_FI_1` (`task_id`),
  KEY `sd_task_history_FI_2` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sd_user`
--

DROP TABLE IF EXISTS `sd_user`;
CREATE TABLE `sd_user` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `remember_key` varchar(10) default NULL,
  `is_active` tinyint(4) default '0',
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `city` varchar(50) default NULL,
  `state` varchar(50) default NULL,
  `country` varchar(50) default NULL,
  `phone` varchar(50) default NULL,
  `gender` varchar(6) default NULL,
  `is_public` tinyint(4) default NULL,
  `allow_product_view` tinyint(4) default NULL,
  `force_sponsee_image` tinyint(4) default NULL,
  `profile_image` varchar(255) default NULL,
  `sponsor_image` varchar(255) default NULL,
  `current_sponsor_user_id` int(11) default NULL,
  `last_login` datetime default NULL,
  `created_at` datetime default NULL,
  `updated_at` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `sd_user_U_1` (`username`),
  UNIQUE KEY `sd_user_U_2` (`email`),
  KEY `sd_user_FI_1` (`current_sponsor_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sd_file`
--
ALTER TABLE `sd_file`
  ADD CONSTRAINT `sd_file_FK_1` FOREIGN KEY (`task_id`) REFERENCES `sd_task` (`id`);

--
-- Constraints for table `sd_invitation`
--
ALTER TABLE `sd_invitation`
  ADD CONSTRAINT `sd_invitation_FK_1` FOREIGN KEY (`inviter_user_id`) REFERENCES `sd_user` (`id`),
  ADD CONSTRAINT `sd_invitation_FK_2` FOREIGN KEY (`invitee_user_id`) REFERENCES `sd_user` (`id`);

--
-- Constraints for table `sd_product_user`
--
ALTER TABLE `sd_product_user`
  ADD CONSTRAINT `sd_product_user_FK_1` FOREIGN KEY (`product_id`) REFERENCES `sd_product` (`id`),
  ADD CONSTRAINT `sd_product_user_FK_2` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`);

--
-- Constraints for table `sd_question`
--
ALTER TABLE `sd_question`
  ADD CONSTRAINT `sd_question_FK_1` FOREIGN KEY (`sprint_id`) REFERENCES `sd_sprint` (`id`),
  ADD CONSTRAINT `sd_question_FK_2` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`);

--
-- Constraints for table `sd_question_task`
--
ALTER TABLE `sd_question_task`
  ADD CONSTRAINT `sd_question_task_FK_1` FOREIGN KEY (`question_id`) REFERENCES `sd_question` (`id`),
  ADD CONSTRAINT `sd_question_task_FK_2` FOREIGN KEY (`task_id`) REFERENCES `sd_task` (`id`);

--
-- Constraints for table `sd_sponsorship_history`
--
ALTER TABLE `sd_sponsorship_history`
  ADD CONSTRAINT `sd_sponsorship_history_FK_1` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`);

--
-- Constraints for table `sd_sponsorship_request`
--
ALTER TABLE `sd_sponsorship_request`
  ADD CONSTRAINT `sd_sponsorship_request_FK_1` FOREIGN KEY (`requester_user_id`) REFERENCES `sd_user` (`id`),
  ADD CONSTRAINT `sd_sponsorship_request_FK_2` FOREIGN KEY (`sponsor_user_id`) REFERENCES `sd_user` (`id`),
  ADD CONSTRAINT `sd_sponsorship_request_FK_3` FOREIGN KEY (`sponsored_user_id`) REFERENCES `sd_user` (`id`);

--
-- Constraints for table `sd_sponsorship_request_history`
--
ALTER TABLE `sd_sponsorship_request_history`
  ADD CONSTRAINT `sd_sponsorship_request_history_FK_1` FOREIGN KEY (`requester_user_id`) REFERENCES `sd_user` (`id`),
  ADD CONSTRAINT `sd_sponsorship_request_history_FK_2` FOREIGN KEY (`sponsor_user_id`) REFERENCES `sd_user` (`id`),
  ADD CONSTRAINT `sd_sponsorship_request_history_FK_3` FOREIGN KEY (`sponsored_user_id`) REFERENCES `sd_user` (`id`);

--
-- Constraints for table `sd_sprint`
--
ALTER TABLE `sd_sprint`
  ADD CONSTRAINT `sd_sprint_FK_1` FOREIGN KEY (`product_id`) REFERENCES `sd_product` (`id`);

--
-- Constraints for table `sd_task`
--
ALTER TABLE `sd_task`
  ADD CONSTRAINT `sd_task_FK_1` FOREIGN KEY (`product_id`) REFERENCES `sd_product` (`id`),
  ADD CONSTRAINT `sd_task_FK_2` FOREIGN KEY (`sprint_id`) REFERENCES `sd_sprint` (`id`),
  ADD CONSTRAINT `sd_task_FK_3` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`),
  ADD CONSTRAINT `sd_task_FK_4` FOREIGN KEY (`creator_user_id`) REFERENCES `sd_user` (`id`);

--
-- Constraints for table `sd_task_history`
--
ALTER TABLE `sd_task_history`
  ADD CONSTRAINT `sd_task_history_FK_1` FOREIGN KEY (`task_id`) REFERENCES `sd_task` (`id`),
  ADD CONSTRAINT `sd_task_history_FK_2` FOREIGN KEY (`user_id`) REFERENCES `sd_user` (`id`);

--
-- Constraints for table `sd_user`
--
ALTER TABLE `sd_user`
  ADD CONSTRAINT `sd_user_FK_1` FOREIGN KEY (`current_sponsor_user_id`) REFERENCES `sd_user` (`id`);
