
#
# Table structure for table 'genre'
#

DROP TABLE IF EXISTS `genre`;
CREATE TABLE `genre` (
  `id` int(11) unsigned NOT NULL,
  `name_ru` varchar(255) default NULL,
  `name_ua` varchar(255) default NULL,
  `name_en` varchar(255) default NULL,
  `name_url` varchar(255) default NULL,
  `active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_url` (`name_url`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
