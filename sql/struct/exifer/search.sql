#
# Table structure for table `search`
#

DROP TABLE IF EXISTS `search`;
CREATE TABLE `search` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `pattern` varchar(255) default NULL,
  `target` tinyint(11) unsigned NOT NULL,
  `times` int(11) unsigned default "0",
  `last_uid` int(11) unsigned default "0",
  `last_ip` varchar(50) default NULL,
  `last_fwrd` varchar(50) default NULL,
  `last_tstamp` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `target_pattern` (`pattern`, `target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
