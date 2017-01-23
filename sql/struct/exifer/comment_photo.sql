#
# Table structure for table `comment_photo`
#

DROP TABLE IF EXISTS `comment_photo`;

CREATE TABLE `comment_photo` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `item_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `root_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned NOT NULL,
  `depth` int(5) unsigned NOT NULL,
  `text` text default NULL,
  `text_prev` text default NULL,
  `add_ip` varchar(50) default NULL,
  `add_fwrd` varchar(50) default NULL,
  `add_tstamp` bigint(20) default NULL,
  `change_uid` int(11) unsigned default NULL,
  `change_ip` varchar(50) default NULL,
  `change_fwrd` varchar(50) default NULL,
  `change_tstamp` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  KEY `x_item` (`item_id`,`user_id`),
  KEY `x_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


#
# Table structure for table `comment_photo_rate`
#

DROP TABLE IF EXISTS `comment_photo_rate`;

CREATE TABLE `comment_photo_rate` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `comment_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pair` (`comment_id`, `user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


#
# Table structure for table `comment_photo_subscribe`
#

DROP TABLE IF EXISTS `comment_photo_subscribe`;

CREATE TABLE `comment_photo_subscribe` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `item_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pair` (`item_id`, `user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


#
# Table structure for table `comment_photo_karma`
#

DROP TABLE IF EXISTS `comment_photo_karma`;

CREATE TABLE `comment_photo_karma` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `value` int(11) NOT NULL default "0",
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


