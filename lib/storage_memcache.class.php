<?

/**
 * Storage Memcache Engine
 */

class StorageMemcache extends Singleton {

	const __DEF_LIFETIME = 300; // 5 min

	/**
	 * memcache instance
	 */
	protected $memcache;

	protected $compression_method = MEMCACHE_COMPRESSED;

	protected $server_namespace = 'NS:';

	/**
	 * Point to the host where memcached is listening for connections.
	 * This parameter may also specify other transports like
	 * unix:///path/to/memcached.sock to use UNIX domain sockets,
	 * in this case port  must also be set to 0.
	 * @var unknown_type
	 */
	protected $server_host = null;

	/**
	 * Point to the port where memcached is listening for connections.
	 * This parameter is optional and its default value is 11211.
	 * Set this parameter to 0 when using UNIX domain sockets.
	 * @var unknown_type
	 */
	protected $server_port = null;

	// Controls the use of a persistent connection. Default to TRUE.
	protected $server_persistent = true;

	/**
	 * Number of buckets to create for this server which in turn control its probability of it being selected.
	 * The probability is relative to the total weight of all servers.
	 * @var unknown_type
	 */
	protected $server_weight = null;

	/**
	 * Value in seconds which will be used for connecting to the daemon.
	 * Think twice before changing the default value of 1 second - you can lose all the advantages of caching if your connection is too slow.
	 * @var unknown_type
	 */
	protected $server_timeout = null;

	/**
	 * Controls how often a failed server will be retried, the default value is 15 seconds.
	 * Setting this parameter to -1 disables automatic retry.
	 * Neither this nor the persistent  parameter has any effect when the extension is loaded dynamically via dl().
	 * @var unknown_type
	 */
	protected $server_retry_interval = 15;

	/**
	 * Controls if the server should be flagged as online.
	 * Setting this parameter to FALSE and retry_interval to -1 allows a failed server to be kept in the pool so as not to affect the key distribution algorithm.
	 * Requests for this server will then failover or fail immediately depending on the memcache.allow_failover setting.
	 * Default to TRUE, meaning the server should be considered online.
	 * @var unknown_type
	 */
	protected $server_status = true;

	/**
	 * Allows the user to specify a callback function to run upon encountering an error.
	 * The callback is run before failover is attempted.
	 * The function takes two parameters, the hostname and port of the failed server.
	 * @var unknown_type
	 */
	protected $server_failure_callback = null;

	//
	protected $server_timeoutms = null;

	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}

	protected function __construct() {

		$this->server_host = MEMCACHE_HOST;
		$this->server_port = MEMCACHE_PORT;

		$this->memcache = new Memcache();
		$this->memcache->addServer(
			$this->server_host
			,$this->server_port
			//,$this->server_persistent
			//,$this->server_weight
			//,$this->server_timeout
			//,$this->server_retry_interval
			//,$this->server_status
			//,$this->server_failure_callback
			//,$this->server_timeoutms
		);

		//_e($this->memcache->getStats());
		//_e($this->memcache->getServerStatus());
		//_e($this->memcache->getServerStatus($this->server_host));
	}

	public function __destruct() {
		$this->memcache->close();
	}

	public function connect() {
		$this->memcache->connect(
			$this->server_host
			,$this->server_port
			,$this->server_timeout
		);
	}

	public function enable_compression() {
		$this->compression_method = MEMCACHE_COMPRESSED;
	}

	public function disable_compression() {
		$this->compression_method = null;
	}

	public function get_key($key=null) {
		$key = $key ? ($key) : md5(uniqid());
		return $key;
	}

	protected function get_real_key($key) {
		return $this->server_namespace.$this->get_key($key);
	}

	public function set($storage_key, $storage_data, $lifetime=self::__DEF_LIFETIME) {

		$result = null;

		$lifetime = Cast::int($lifetime);

		if($lifetime>=0) {

			$storage_key = $this->get_key($storage_key);
			//$expiration_tstamp = time() + $lifetime;
			$expiration_tstamp = $lifetime;

			$storage_real_key = $this->get_real_key($storage_key);

			$bool = $this->memcache->set(
				$storage_real_key,
				$storage_data,
				$this->compression_method,
				$expiration_tstamp
			);

			if($bool) {
				_e(sprintf('Memcache::set, key[%s], real_key[%s], 1', $storage_key, $storage_real_key));
				$result = $storage_key;
			}
		}

		return $result;
	}

	public function get($storage_key) {
		$storage_real_key = $this->get_real_key($storage_key);
		$storage_data = $this->memcache->get($storage_real_key);
		_e(sprintf('Memcache::get, key[%s], real_key[%s], %u', $storage_key, $storage_real_key, (bool)$storage_data));
		return $storage_data;
	}

	public function del($storage_key) {
		$storage_real_key = $this->get_real_key($storage_key);
		$result = $this->memcache->delete($storage_real_key);
		_e(sprintf('Memcache::del, key[%s], real_key[%s], %u', $storage_key, $storage_real_key, $result));
		return $result;
	}

	public function touch($storage_key, $lifetime=self::__DEF_LIFETIME) {

		$lifetime = Cast::int($lifetime);

		$expiration_tstamp = $lifetime;

		$storage_data = $this->get($storage_key);
		$result = $this->memcache->replace(
			$this->get_real_key($storage_key),
			$storage_data,
			$this->compression_method,
			$expiration_tstamp
		);

		return $result;
	}

	public function clear() {
		return $this->memcache->flush();
	}

	public function clear_expired_data($tstamp=null) {
		return 0;
	}
}