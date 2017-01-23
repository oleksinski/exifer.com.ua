#
# Table structure for table `user`
#

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`(
  `id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(255) default NULL,
  `password` varbinary(255) default NULL,
  `status` tinyint(1) unsigned default "0",
  `bitmask` mediumint(11) unsigned default NULL,
  `name` varchar(255) default NULL,
  `urlname` varchar(255) default NULL,
  `gender` enum("f", "m") NOT NULL default "m",
  `country` int(11) unsigned default NULL,
  `city` int(11) unsigned default NULL,
  `birthday` bigint(20) default NULL,
  `about` text default NULL,
  `about_emails` text default NULL,
  `about_phones` text default NULL,
  `about_urls` text default NULL,
  `about_ims` text default NULL,
  `authcode` char(32) default NULL,
  `photos` int(11) unsigned NOT NULL default "0",
  `views_guest` int(11) unsigned NOT NULL default "0",
  `views_user` int(11) unsigned NOT NULL default "0",
  `views` int(11) unsigned NOT NULL default "0",
  `comments` int(11) unsigned NOT NULL default "0",
  `articles` int(11) unsigned NOT NULL default "0",
  `rating` int(11) NOT NULL default "0",
  `userpic_tstamp` bigint(20) default NULL,
  `upload_limit` mediumint(11) unsigned default "0",
  `upload_tstamp` bigint(20) default NULL,
  `upload_next_tstamp` bigint(20) default NULL,
  `ban_tstamp` bigint(20) default NULL,
  `hit_tstamp` bigint(20) default NULL,
  `hit_ip` varchar(50) default NULL,
  `hit_fwrd` varchar(50) default NULL,
  `login_tstamp` bigint(20) default NULL,
  `login_ip` varchar(50) default NULL,
  `login_fwrd` varchar(50) default NULL,
  `update_tstamp` bigint(20) default NULL,
  `update_ip` varchar(50) default NULL,
  `update_fwrd` varchar(50) default NULL,
  `reg_tstamp` bigint(20) default NULL,
  `reg_ip` varchar(50) default NULL,
  `reg_fwrd` varchar(50) default NULL,
  `view_tstamp` bigint(20) default NULL,
  `view_ip` varchar(50) default NULL,
  `view_fwrd` varchar(50) default NULL,
  PRIMARY KEY(`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `urlname` (`urlname`),
  KEY `authcode`(authcode(5)),
  KEY `location` (`status`, `country`, `city`),
  KEY `reg_tstamp` (`status`, `reg_tstamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

#
# Table structure for table `user_deleted`
#

DROP TABLE IF EXISTS `user_deleted`;

CREATE TABLE `user_deleted` LIKE `user`;

ALTER TABLE `photo_deleted` MODIFY `id` int(11) unsigned NOT NULL;

ALTER TABLE `user_deleted` DROP KEY `email`;
ALTER TABLE `user_deleted` DROP KEY `urlname`;
ALTER TABLE `user_deleted` DROP KEY `authcode`;
ALTER TABLE `user_deleted` DROP KEY `location`;
ALTER TABLE `user_deleted` DROP KEY `reg_tstamp`;

ALTER TABLE `user_deleted` ADD `admin_id` int(11) unsigned NOT NULL default "0";
ALTER TABLE `user_deleted` ADD `is_spamer` tinyint(1) unsigned NOT NULL default "0";
ALTER TABLE `user_deleted` ADD `del_tstamp` bigint(20) default NULL;
ALTER TABLE `user_deleted` ADD `del_ip` varchar(50) default NULL;
ALTER TABLE `user_deleted` ADD `del_fwrd` varchar(50) default NULL;

ALTER TABLE `user_deleted` ADD KEY `email` (`email`);
