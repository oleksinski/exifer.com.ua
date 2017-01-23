#
# Table structure for table `user_contact`
#

CREATE TABLE `user_contact`(
  `user_id` int(11) unsigned NOT NULL,
  `contact_id` int(11) unsigned NOT NULL,
  `add_tstamp` bigint(20) default NULL,
  PRIMARY KEY `user_contact`(`user_id`, `contact_id`),
  KEY add_tstamp(`add_tstamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
