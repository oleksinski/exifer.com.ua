#
# Table structure for table `user_online`
#

DROP TABLE IF EXISTS `user_online`;
CREATE TABLE `user_online`(
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `hit_tstamp` bigint(20) default NULL,
  `hit_ip` varchar(50) default NULL,
  `hit_fwrd` varchar(50) default NULL,
   PRIMARY KEY  (`id`),
  UNIQUE KEY(`user_id`),
  KEY `hit_tstamp` USING BTREE (`hit_tstamp`)
) ENGINE = MEMORY DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
