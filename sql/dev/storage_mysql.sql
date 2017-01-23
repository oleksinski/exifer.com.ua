#
# Table structure for table `storage_mysql_memory`
#

DROP TABLE IF EXISTS `storage_mysql_memory`;
CREATE TABLE `storage_mysql_memory`(
  `storage_key` varchar(255) NOT NULL,
  `storage_data` varbinary(8192),
  `expiration_tstamp` bigint(20) default NULL,
  PRIMARY KEY `storage_key` USING HASH (`storage_key`),
  KEY `expiration_tstamp` USING BTREE (`expiration_tstamp`)
) ENGINE = MEMORY DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

#
# Table structure for table `storage_mysql_myisam`
#

DROP TABLE IF EXISTS `storage_mysql_myisam`;
CREATE TABLE `storage_mysql_myisam`(
  `storage_key` varchar(255) NOT NULL,
  `storage_data` blob,
  `expiration_tstamp` bigint(20) default NULL,
  PRIMARY KEY `storage_key` (`storage_key`),
  KEY `expiration_tstamp` (`expiration_tstamp`)
) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

