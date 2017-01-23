#
# Table structure for table `user_register`
#

DROP TABLE IF EXISTS `user_register`;
CREATE TABLE `user_register`(
  `id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(255) default NULL,
  `status` tinyint(1) unsigned default "0",
  `authcode` char(32) default NULL,
  `reg_tstamp` bigint(20) default NULL,
  `reg_ip` varchar(50) default NULL,
  `reg_fwrd` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `authcode`(authcode(5))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
