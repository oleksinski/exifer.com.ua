#
# Table structure for table `vote_photo`
#

DROP TABLE IF EXISTS `vote_photo`;
CREATE TABLE `vote_photo` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `item_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned default NULL,
  `type` tinyint(1) NOT NULL default "1",
  `value` decimal(2,1) NOT NULL default "0.0",
  `add_ip` varchar(50) default NULL,
  `add_fwrd` varchar(50) default NULL,
  `add_tstamp` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_pair` (`item_id`, `user_id`),
  KEY `item_value` (`item_id`, `value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
