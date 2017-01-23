#
# Table structure for table `photo_user_view`
#

DROP TABLE IF EXISTS `photo_user_view`;
CREATE TABLE `photo_user_view`(
  `id` int(11) unsigned NOT NULL auto_increment,
  `item_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `views` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `item_user` (`item_id`, `user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
