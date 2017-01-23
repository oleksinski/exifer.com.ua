<?

class SessionPhp extends Singleton {

	protected $id; // session id

	protected $ini_save_path = '';
	protected $ini_name = 'SID';
	protected $ini_save_handler = 'files';
	protected $ini_auto_start = 0;
	protected $ini_gc_probability = 1;
	protected $ini_gc_divisor = 100;
	protected $ini_gc_maxlifetime = 1440;
	protected $ini_serialize_handler = 'php';
	protected $ini_cookie_lifetime = 0;
	protected $ini_cookie_path = '/';
	protected $ini_cookie_domain = '';
	protected $ini_cookie_secure = '';
	protected $ini_cookie_httponly = '';
	protected $ini_use_cookies = 1;
	protected $ini_use_only_cookies = 1;
	protected $ini_referer_check = '';
	protected $ini_entropy_file = '';
	protected $ini_entropy_length = 0;
	protected $ini_cache_limiter = 'nocache';
	protected $ini_cache_expire = 180;
	protected $ini_use_trans_sid = 0;
	protected $ini_bug_compat_42 = 1;
	protected $ini_bug_compat_warn = 1;
	protected $ini_hash_function = 0;
	protected $ini_hash_bits_per_character = 4;

	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}

	protected function __construct() {

		$this->ini_save_path = SESSION_PHP_PATH;
		$this->ini_cookie_domain = Cookie::domain();

		$this->init();
	}

	public function __destruct() {
		$this->close();
	}

	public function init() {
		$vars = get_object_vars($this);
		foreach($vars as $name=>$value) {
			$this->__set($name, $value);
		}
	}

	public function __set($name, $value) {
		if(property_exists($this, $name)) {
			$ini = _str_replace('ini_', '', $name);
			if($ini && $ini!=$name) {
				//_e(sprintf('__set: %s = %s', $name, $value));
				$this->$name = $value;
				$ini = sprintf('session.%s', $ini);
				@ini_set($ini, $value);
				return true;
			}
		}
		return false;
	}

	public function setlifetime($lifetime=0) {
		$lifetime = Cast::unsignint($lifetime);
		if($this->ini_cookie_lifetime != $lifetime) {
			$this->__set('ini_cookie_lifetime', $lifetime);
			$this->__set('ini_gc_maxlifetime', $this->ini_gc_maxlifetime+$lifetime);
			if($this->check()) {
				$this->reopen();
			}
		}
	}

	public function id() {
		if(!$this->id) $this->id = session_id();
		return $this->id;
	}

	public function check() {
		return Cast::bool($this->id());
	}

	public function open($reopen=false) {
		if(!$this->check() || $reopen) {
			$this->close();
			@session_start();
		}
		return $this->id();
	}

	public function reopen() {
		return $this->open(true);
	}

	public function close() {
		if($this->check()) {
			@session_write_close();
			$this->id = null;
			return true;
		}
		return false;
	}

	public function clear() {
		if($this->check()) {
			$_SESSION = array();
			@session_destroy();
			Cookie::set($this->ini_name, $this->id, time()-1, $this->ini_cookie_path, $this->ini_cookie_domain);
			$this->id = null;
			return true;
		}
		return false;
	}

	public function setcookie() {
		if($this->check()) {
			$lifetime = $this->ini_cookie_lifetime ? (time()+$this->ini_cookie_lifetime) : null;
			Cookie::set($this->ini_name, $this->id, $lifetime, $this->ini_cookie_path, $this->ini_cookie_domain);
			return true;
		}
		return false;
	}

	public function set($key, $data, $lifetime=null) {
		if($this->check()) {
			if(!is_null($lifetime)) $this->setlifetime($lifetime);
			$_SESSION[$key] = $data;
			$this->setcookie();
			return true;
		}
		return false;
	}

	public function get($key) {
		if($this->check()) {
			return ifsetor($_SESSION[$key], null);
		}
	}

	public function del($key) {
		if($this->check()) {
			if(array_key_exists($key, $_SESSION)) {
				unset($_SESSION[$key]);
				if(!$this->getsession()) {
					$this->clear();
				}
				return true;
			}
		}
		return false;
	}

	public function getsession() {
		if($this->check()) {
			return $_SESSION;
		}
	}

}