<?

/**
 * Storage Filecache Engine
 */

class StorageFilecache extends StorageAtomic {

	protected static $cache = array();

	/**
	 * @override
	 * @param unknown_type $c
	 */
	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}

	protected function __construct() {
		parent::__construct();
		$this->disable_compression();
	}

	public function get_fbase() {
		return sprintf('%s/filecache/', STATIC_PATH);
	}

	public function get_fdirname($f_name) {
		$f_dirname = sprintf('%u', _crc32($f_name));
		return $f_dirname%100;
	}

	public function get_fdirpath($f_name) {
		return sprintf('%s/%s/', self::get_fbase(), self::get_fdirname($f_name));
	}

	public function get_fname($f_name) {
		return md5($f_name).'.php';
	}

	public function get_fpath($f_name) {
		$fpath = sprintf('%s/%s', self::get_fdirpath($f_name), self::get_fname($f_name));
		$fpath = _str_replace('//', '/', $fpath);
		return $fpath;
	}

	public function set($storage_key, $storage_data, $lifetime=self::__DEF_LIFETIME) {

		$result = null;

		$lifetime = Cast::int($lifetime);

		if($lifetime > 0) {

			$storage_key = $this->get_key($storage_key);
			$storage_data = $this->compress($storage_data);
			$expiration_tstamp = time() + $lifetime;

			$data = array(
				'k' => &$storage_key,
				'd' => &$storage_data,
				'e' => &$expiration_tstamp,
			);

			$f_path = self::get_fpath($storage_key);

			$success = FileFunc::saveVarsToFile($f_path, array('data'=>&$data));

			if($success) {

				self::$cache[$f_path] = $data;

				$result = $storage_key;
			}
		}

		return $result;
	}

	public function get($storage_key) {

		$result = null;

		$storage_key = Cast::str($storage_key);

		if($storage_key) {

			$data = array();

			$f_path = self::get_fpath($storage_key);

			if(isset(self::$cache[$f_path])) {
				$data = self::$cache[$f_path];
			}
			else {
				@include($f_path);
				self::$cache[$f_path] = $data;
			}

			//$storage_key = ifsetor($data['k'], null);
			$storage_data = ifsetor($data['d'], null);
			$expiration_tstamp = ifsetor($data['e'], null);

			if($storage_data) {

				if($expiration_tstamp>time()) {
					$storage_data = $this->decompress($storage_data);
					$result =& $storage_data;
				}
				else {
					self::rm_fcache($f_path);
				}
			}
		}

		return $result;
	}

	public function touch($storage_key, $lifetime=self::__DEF_LIFETIME) {

		$affected = 0;

		$storage_key = Cast::str($storage_key);

		if($storage_key) {

			$data = array();

			$f_path = self::get_fpath($storage_key);

			if(isset(self::$cache[$f_path])) {
				$data = self::$cache[$f_path];
			}
			else {
				@include($f_path);
			}

			if($data) {
				$storage_data = ifsetor($data['d'], null);
				if($storage_data) {
					$storage_data = $this->decompress($storage_data);
				}
				$affected = $this->set($storage_key, $storage_data, $lifetime);
			}
		}

		return $affected;
	}

	public function del($storage_key) {

		$affected = 0;

		$storage_key = Cast::str($storage_key);

		if($storage_key) {

			$f_path = self::get_fpath($storage_key);

			$rm_result = self::rm_fcache($f_path);

			if($rm_result) $affected++;
		}

		return $affected;
	}

	public function clear() {

		$affected = 0;

		$f_dir = self::get_fbase();

		$dir_files = FileFunc::readDirFiles($f_dir);

		foreach($dir_files as $f_path) {
			$rm_result = self::rm_fcache($f_path);
			if($rm_result) $affected++;
		}

		return $affected;
	}

	public function clear_expired_data($tstamp=null) {

		$affected = 0;

		$tstamp = DateConst::getTime($tstamp);

		$f_dir = self::get_fbase();

		$dir_files = FileFunc::readDirFiles($f_dir);

		foreach($dir_files as $f_path) {

			$data = array();

			@include($f_path);

			if($data) {
				$storage_key = ifsetor($data['k'], null);
				$storage_data = ifsetor($data['d'], null);
				$expiration_tstamp = ifsetor($data['e'], null);

				if(!$storage_key || !$storage_data || $expiration_tstamp<$tstamp) {
					$rm_result = self::rm_fcache($f_path);
					if($rm_result) $affected++;
				}
			}
			else {
				$rm_result = self::rm_fcache($f_path);
				if($rm_result) $affected++;
			}
		}

		self::clear_expired_files();

		return $affected;
	}

	public function clear_expired_files() {

		$f_dir = self::get_fbase();

		$dir_files = FileFunc::readDirFiles($f_dir);

		$expire_tstamp = time() - 30*24*60*60; // 30 days

		foreach($dir_files as $f_path) {
			$filemtime = filemtime($f_path);
			if($filemtime && $filemtime<$expire_tstamp) {
				self::rm_fcache($f_path);
			}
		}
	}

	public function rm_fcache($f_path) {

		$result = false;

		if(is_file($f_path)) {

			$result = @unlink($f_path);
			unset(self::$cache[$f_path]);

			$f_dir = realpath(Url::fix(dirname($f_path)).'/');
			$f_base = realpath(Url::fix(self::get_fbase()).'/');

			if($f_base!==$f_dir && _strstr($f_dir, $f_base)) {
				$dir_files = FileFunc::readDirFiles($f_dir);
				if(empty($dir_files)) {
					@rmdir($f_dir);
				}
			}
		}
		return $result;
	}

}