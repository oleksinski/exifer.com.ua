#
# Table structure for table `photo_thumb`
#

DROP TABLE IF EXISTS `photo_thumb`;
CREATE TABLE `photo_thumb`(
  `photo_id` int(11) unsigned NOT NULL,
  `format` smallint(4) unsigned NOT NULL,
  `width` smallint(5) unsigned default NULL,
  `height` smallint(5) unsigned default NULL,
  `filesize` int(11) unsigned default NULL,
  `tstamp` bigint(20) default NULL,
  PRIMARY KEY (`photo_id`, `format`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
