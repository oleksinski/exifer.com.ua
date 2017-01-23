#
# Table structure for table `album`
#

CREATE TABLE `album`(
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL default '0',
  `privacy` tinyint(1) unsigned NOT NULL default '0',
  `title` varchar(255) default NULL,
  `description` text default NULL,
  `cover_id` int(11) unsigned default NULL,
  `create_tstamp` bigint(20) default NULL,
  `update_tstamp` bigint(20) default NULL,
  PRIMARY KEY(`id`),
  KEY `user_id`(`user_id`),
  KEY `access`(`user_id`, `type`, `privacy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

#
# Table structure for table `album_photo`
#

CREATE TABLE `album_photo` (
  `album_id` int(11) unsigned NOT NULL,
  `photo_id` int(11) unsigned NOT NULL,
  `order` int(11) unsigned NOT NULL default '0',
  `tstamp` bigint(20) default NULL,
  UNIQUE KEY `album_photo`(`album_id`, `photo_id`),
  KEY `tstamp_order`(`tstamp`, `order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
