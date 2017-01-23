#
# Table structure for table `feedback`
#

DROP TABLE IF EXISTS feedback;
CREATE TABLE feedback(
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned default NULL,
  `email` varchar(255) default NULL,
  `username` varchar(255) default NULL,
  `subject` varchar(255) default NULL,
  `message` text default NULL,
  `add_tstamp` bigint(20) default NULL,
  `add_ip` varchar(50) default NULL,
  `add_fwrd` varchar(50) default NULL,
  PRIMARY KEY(`id`),
  KEY `user_id`(`user_id`),
  KEY `email`(`email`),
  KEY `add_tstamp`(`add_tstamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
