#
# Table structure for table `social_share`
#

DROP TABLE IF EXISTS `social_share`;
CREATE TABLE `social_share` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `service_id` tinyint(2) unsigned NOT NULL,
  `item_type` tinyint(2) unsigned NOT NULL,
  `item_id` int(11) unsigned NOT NULL,
  `clicks` int(11) unsigned NOT NULL default "0",
  `last_uid` int(11) unsigned default NULL,
  `last_ip` varchar(50) default NULL,
  `last_fwrd` varchar(50) default NULL,
  `last_tstamp` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `service_item_id` (`service_id`, `item_type`, `item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
