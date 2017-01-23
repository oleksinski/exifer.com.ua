
#
# Table structure for table 'location_country'
#

DROP TABLE IF EXISTS `location_country`;
CREATE TABLE `location_country` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name_en` varchar(255) default NULL,
  `name_ru` varchar(255) default NULL,
  `name_ua` varchar(255) default NULL,
  `name_url` varchar(255) default NULL,
  `capital_id` int(11) unsigned default NULL,
  `active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

#
# Table structure for table 'location_state'
#

DROP TABLE IF EXISTS `location_state`;

CREATE TABLE `location_state` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `country_id` int(11) unsigned NOT NULL,
  `capital_id` int(11) unsigned NOT NULL,
  `name_ru` varchar(255) default NULL,
  `name_ua` varchar(255) default NULL,
  `name_en` varchar(255) default NULL,
  `name_url` varchar(255) default NULL,
  `active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE `u_state`(`country_id`, `name_ru`),
  KEY `cid_a` (`country_id`,`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

#
# Table structure for table 'location_city'
#

DROP TABLE IF EXISTS `location_city`;
CREATE TABLE `location_city` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `country_id` int(11) unsigned NOT NULL,
  `state_id` int(11) unsigned NOT NULL,
  `name_ru` varchar(255) default NULL,
  `name_ua` varchar(255) default NULL,
  `name_en` varchar(255) default NULL,
  `name_url` varchar(255) default NULL,
  `is_capital` tinyint(1) unsigned NOT NULL default '0',
  `is_main` tinyint(1) unsigned NOT NULL default '0',
  `active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE `u_city`(`country_id`, `name_ru`),
  KEY `is_capital` (`is_capital`),
  KEY `cid_a` (`country_id`,`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
