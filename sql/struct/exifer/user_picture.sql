#
# Table structure for table `user_picture`
#

DROP TABLE IF EXISTS `user_picture`;
CREATE TABLE `user_picture`(
  `user_id` int(11) unsigned NOT NULL,
  `format` smallint(4) unsigned NOT NULL,
  `width` smallint(5) unsigned NOT NULL,
  `height` smallint(5) unsigned NOT NULL,
  `filesize` int(11) unsigned NOT NULL,
  `tstamp` bigint(20) default NULL,
  PRIMARY KEY(`user_id`, `format`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
