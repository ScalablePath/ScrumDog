#executed on production on 9/12

CREATE TABLE IF NOT EXISTS `sd_session` (
  `id` varchar(255) NOT NULL default '',
  `time` int(10) unsigned NOT NULL,
  `data` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

 ALTER TABLE `sd_user` CHANGE `time_zone` `time_zone` VARCHAR( 50 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'America/Los_Angeles',
CHANGE `time_zone_offset` `time_zone_offset` INT( 11 ) NOT NULL DEFAULT '-7' ;

update `sd_user` set `time_zone`='America/Los_Angeles';

update `sd_user` set `time_zone_offset`='-7';