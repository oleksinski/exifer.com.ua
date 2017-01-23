#
# Table structure for table `photo`
#

DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo`(
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `genre_id` int(11) unsigned default NULL,
  `name` varchar(255) default NULL,
  `description` text default NULL,
  `status` tinyint(1) unsigned NOT NULL default "0",
  `bitmask` mediumint(11) unsigned default NULL,
  `exif` text default NULL,
  `rgb` varchar(11) default "0,0,0",
  `views` int(11) unsigned NOT NULL default "0",
  `views_guest` int(11) unsigned NOT NULL default "0",
  `views_user` int(11) unsigned NOT NULL default "0",
  `comments` int(11) unsigned NOT NULL default "0",
  `votes` int(11) unsigned NOT NULL default "0",
  `votes_pros` int(11) unsigned NOT NULL default "0",
  `votes_cons` int(11) unsigned NOT NULL default "0",
  `votes_zero` int(11) unsigned NOT NULL default "0",
  `votes_value` decimal(11,1) NOT NULL default "0.0",
  `rating` int(11) NOT NULL default "0",
  `moderated` tinyint(1) unsigned NOT NULL default "0",
  `add_tstamp` bigint(20) default NULL,
  `add_ip` varchar(50) default NULL,
  `add_fwrd` varchar(50) default NULL,
  `update_tstamp` bigint(20) default NULL,
  `update_ip` varchar(50) default NULL,
  `update_fwrd` varchar(50) default NULL,
  `view_tstamp` bigint(20) default NULL,
  `view_ip` varchar(50) default NULL,
  `view_fwrd` varchar(50) default NULL,
  `orig_size` int(11) unsigned default NULL,
  `orig_width` int(11) unsigned default NULL,
  `orig_height` int(11) unsigned default NULL,
  `orig_name` varchar(255) default NULL,
  `orig_mimetype` varchar(20) default NULL,

  PRIMARY KEY(`id`),
  KEY `add_tstamp`(`status`, `add_tstamp`),
  KEY `uid_add`(`status`, `user_id`, `add_tstamp`),
  KEY `genre_uid`(`status`, `genre_id`, `user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


#
# Table structure for table `photo_deleted`
#

DROP TABLE IF EXISTS `photo_deleted`;

CREATE TABLE `photo_deleted` LIKE `photo`;

ALTER TABLE `photo_deleted` DROP KEY `add_tstamp`;
ALTER TABLE `photo_deleted` DROP KEY `uid_add`;
ALTER TABLE `photo_deleted` DROP KEY `genre_uid`;

ALTER TABLE `photo_deleted` MODIFY `id` int(11) unsigned NOT NULL;

ALTER TABLE `photo_deleted` ADD `admin_id` int(11) unsigned NOT NULL;
ALTER TABLE `photo_deleted` ADD `del_tstamp` bigint(20) default NULL;
ALTER TABLE `photo_deleted` ADD `del_ip` varchar(50) default NULL;
ALTER TABLE `photo_deleted` ADD `del_fwrd` varchar(50) default NULL;

ALTER TABLE `photo_deleted` ADD KEY `user_genre`(`user_id`, `admin_id`);
