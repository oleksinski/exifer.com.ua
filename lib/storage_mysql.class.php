<?

/**
 * Storage MySQL Engine
 */

class StorageMysql extends StorageAtomic {

	/**
	 * @override
	 * @param unknown_type $c
	 */
	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}

	protected function __construct() {
		//$this->compression_level = $this->normalize_compression_level($this->compression_level);
		//$this->compression_method = $this->normalize_compression_method($this->compression_method);
		//$this->compression_enabled = true;
		//parent::__construct();
	}

	public function db_storage_mysql() {
		return sprintf('%s.%s', MYSQL_DATABASE, 1 ? 'storage_mysql_myisam' : 'storage_mysql_memory');
	}

	/**
	 * @param scalar $storage_key
	 * @param mixed $storage_data
	 * @param int $lifetime [sec]
	 * @param bool $compress
	 * @return $
	 */
	public function set($storage_key, $storage_data, $lifetime=self::__DEF_LIFETIME) {

		$result = null;

		$lifetime = Cast::int($lifetime);

		if($lifetime > 0) {

			$__db =& __db();

			$storage_key = $this->get_key($storage_key);
			$storage_data = $this->compress($storage_data);
			$expiration_tstamp = time() + $lifetime;

			$insert_arr = array(
				'storage_key' => $storage_key,
				'storage_data' => $storage_data,
				'expiration_tstamp' => $expiration_tstamp,
			);

			$insert_sql = MySQL::prepare_fields($insert_arr);

			$sql = sprintf('INSERT INTO %s SET %s ON DUPLICATE KEY UPDATE %2$s', self::db_storage_mysql(), $insert_sql);

			$affected = $__db->u($sql);

			$result = $storage_key;
		}

		return $result;
	}


	public function get($storage_key) {

		$result = null;

		$storage_key = Cast::str($storage_key);

		if($storage_key) {

			$__db =& __db();

			$sql = sprintf('SELECT storage_data FROM %s WHERE storage_key=%s AND expiration_tstamp>=%u',
				self::db_storage_mysql(),
				MySQL::str($storage_key),
				time()
			);

			$sql_r = $__db->row($sql);

			$storage_data = $sql_r['storage_data'];

			if($storage_data) {

				$storage_data = $this->decompress($storage_data);

				$result =& $storage_data;
			}
		}

		return $result;
	}

	public function touch($storage_key, $lifetime=self::__DEF_LIFETIME) {

		$affected = 0;

		$storage_key = Cast::str($storage_key);

		if($storage_key) {

			$__db =& __db();

			$lifetime = Cast::int($lifetime);

			$expiration_tstamp = time() + $lifetime;

			$sql = sprintf('UPDATE %s SET expiration_tstamp=%u WHERE storage_key=%s',
				self::db_storage_mysql(),
				$expiration_tstamp,
				MySQL::str($storage_key)
			);

			$affected = $__db->u($sql);
		}

		return $affected;
	}

	public function del($storage_key) {

		$affected = 0;

		$storage_key = Cast::str($storage_key);

		if($storage_key) {

			$__db =& __db();

			$sql = sprintf('DELETE FROM %s WHERE storage_key=%s',
				self::db_storage_mysql(),
				MySQL::str($storage_key)
			);

			$affected = $__db->u($sql);
		}

		return $affected;
	}

	public function clear() {

		$affected = 0;

		$__db =& __db();

		$sql = sprintf('DELETE FROM %s', self::db_storage_mysql());

		$affected = $__db->u($sql);

		return $affected;
	}

	public function clear_expired_data($tstamp=null) {

		$affected = 0;

		$__db =& __db();

		$tstamp = DateConst::getTime($tstamp);

		$sql = sprintf('DELETE FROM %s WHERE expiration_tstamp<%u', self::db_storage_mysql(), $tstamp);

		$affected = $__db->u($sql);

		return $affected;
	}

}