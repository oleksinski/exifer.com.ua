<?

class Predicate {

	// --= Check If HTTP Request Method Is POST =-- //
	public static function posted() {
		return isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	// --= Check If Script Is Executing On DEV =-- //
	public static function server_dev() {
		return SERVER_DEV;
	}

	public static function server_lab() {
		return SERVER_LAB;
	}

	public static function server_pro() {
		return SERVER_PRO;
	}

	public static function windowsOS() {
		$OS = getenv('SERVER_SOFTWARE');
		return _strstr($OS, 'Win32') || _strstr($OS, 'Win64');
	}

	public static function is64bit() {
		return PHP_INT_SIZE>=8;
	}

	public static function IsLandscapeSize($width, $height) {
		$width = Cast::int($width);
		$height = Cast::int($height);
		return $width>=$height;
	}

	public static function IsPortraitSize($width, $height) {
		return self::IsLandscapeSize($height, $width);
	}

	/**
	 * Check whether a request is made within web [not crontab or shell]
	 */
	public static  function isWebCall() {
		return !self::isShellCall();
	}

	public static  function isShellCall() {
		return php_sapi_name() == 'cli' && !isset($_SERVER['REMOTE_ADDR']);
	}

}
