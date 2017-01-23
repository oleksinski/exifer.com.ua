#
# Table structure for table `user_occupation`
#

DROP TABLE IF EXISTS `user_occupation`;
CREATE TABLE `user_occupation`(
  `id` int(11) unsigned NOT NULL,
  `name_ru` varchar(255) default NULL,
  `name_ua` varchar(255) default NULL,
  `name_en` varchar(255) default NULL,
  `name_url` varchar(255) default NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_ru`(`name_ru`),
  UNIQUE KEY `name_ua`(`name_ua`),
  UNIQUE KEY `name_en`(`name_en`),
  UNIQUE KEY `name_url`(`name_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

#
# Table structure for table `user_experience`
#

DROP TABLE IF EXISTS `user_experience`;
CREATE TABLE `user_experience`(
  `id` int(11) unsigned NOT NULL default "0",
  `name_ru` varchar(255) default NULL,
  `name_ua` varchar(255) default NULL,
  `name_en` varchar(255) default NULL,
  `name_url` varchar(255) default NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_ru`(`name_ru`),
  UNIQUE KEY `name_ua`(`name_ua`),
  UNIQUE KEY `name_en`(`name_en`),
  UNIQUE KEY `name_url`(`name_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

#
# Table structure for table `user_occupation_experience`
#

DROP TABLE IF EXISTS `user_occupation_experience`;
CREATE TABLE `user_occupation_experience`(
  `occupation_id` int(11) unsigned NOT NULL,
  `experience_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY `occupation_experience` (`occupation_id`, `experience_id`),
  KEY `active`(`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

#
# Table structure for table `user_occupation_experience_data`
#

DROP TABLE IF EXISTS `user_occupation_experience_data`;
CREATE TABLE `user_occupation_experience_data`(
  `user_id` int(11) unsigned NOT NULL,
  `occupation_id` int(11) unsigned NOT NULL,
  `experience_id` int(11) unsigned NOT NULL,
  PRIMARY KEY `user_occupation_experience` (`user_id`, `occupation_id`, `experience_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
