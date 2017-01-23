<?

require_once(dirname(__FILE__).'/header.exec.php');

// Storage Current Engine
$__storage =& __storage();
$__storage->clear_expired_data();


// Storage Mysql Engine
//$__storage =& StorageMysql::getInstance();
//$__storage->clear_expired_data();


// Storage FileCache Engine
//$__storage =& StorageFilecache::getInstance();
//$__storage->clear_expired_data();
