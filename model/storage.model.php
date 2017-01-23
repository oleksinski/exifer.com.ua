<?

/* extends StorageMysql, StorageMemcache, StorageFilecache */

class StorageModel extends StorageFilecache /*implements StorageInterface*/ {

	const STORAGE_MEMCACHE = 'StorageMemcache';
	const STORAGE_FILECACHE = 'StorageFilecache';
	const STORAGE_MYSQL = 'StorageMysql';

	/**
	 * @override
	 * @param unknown_type $c
	 */
	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}
}
