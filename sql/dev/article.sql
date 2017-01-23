#
# Table structure for table `article`
#

DROP TABLE IF EXISTS `article`;

CREATE TABLE `article` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `type` tinyint(1) unsigned NOT NULL,
  `user_id` int(11) unsigned default NULL,
  `title` varchar(255) default NULL,
  `subtitle` varchar(500) default NULL,
  `body` text default NULL,
  `views` int(11) unsigned NOT NULL,
  `views_guest` int(11) unsigned NOT NULL,
  `views_user` int(11) unsigned NOT NULL,
  `comments` int(11) unsigned NOT NULL,
  `rating` int(11) NOT NULL,
  `add_ip` varchar(50) default NULL,
  `add_fwrd` varchar(50) default NULL,
  `add_tstamp` bigint(20) default NULL,
  `update_ip` varchar(50) default NULL,
  `update_fwrd` varchar(50) default NULL,
  `update_tstamp` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  KEY `x_type_user` (`type`, `user_id`),
  KEY `x_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
