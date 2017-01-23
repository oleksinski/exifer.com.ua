<?


class StorageAtomic extends Singleton {

	const METHOD_GZIP = 1;
	const METHOD_DEFLATE = 2;

	const __DEF_LIFETIME = 300; // 5 min

	protected $compression_enabled = true;
	protected $compression_level = 9; // [0-9]
	protected $compression_method = 2;

	/**
	 * @override
	 * @param unknown_type $c
	 */
	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}

	protected function __construct() {
		//$this->compression_level = $this->normalize_compression_level($this->compression_level);
		//$this->compression_method = $this->normalize_compression_method($this->compression_method);
		//$this->compression_enabled = true;
	}

	public function __destruct() {}

	public function normalize_compression_level($compression_level) {
		$compression_level = Cast::int($compression_level);
		return insetor($compression_level, range(0,9), 9);
	}

	public function normalize_compression_method($compressMethod) {
		return insetor($compressMethod, array(self::METHOD_GZIP, self::METHOD_DEFLATE), self::METHOD_DEFLATE);
	}

	public function enable_compression() {
		$this->compression_enabled = true;
	}

	public function disable_compression() {
		$this->compression_enabled = false;
	}

	/**
	 * Compressing objects with private or protected properties will cause problem
	 */
	public function compress($data) {
		$data = serialize($data);
		if($this->compression_enabled) {
			switch($this->compression_method) {
				case self::METHOD_GZIP:
					$data = gzencode($data, $this->compression_level, 1 ? FORCE_GZIP : FORCE_DEFLATE);
					break;
				case self::METHOD_DEFLATE:
				default:
					$data = gzdeflate($data, $this->compression_level);
					break;
			}
		}
		return $data;
	}

	/**
	 * Decompressing objects with private or protected properties will cause problem
	 */
	public function decompress($data) {
		if($this->compression_enabled) {
			switch($this->compression_method) {
				case self::METHOD_GZIP:
					$data = gzuncompress($data);
					break;
				case self::METHOD_DEFLATE:
				default:
					$data = gzinflate($data);
					break;
			}
		}
		$data = unserialize($data);
		return $data;
	}

	public function get_key($key=null) {
		$key = $key ? ($key) : md5(uniqid());
		return $key;
	}

}