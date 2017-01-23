<?

class MyIconv {

	public static function encode($what, $from, $to) {
		//return mb_convert_encoding($what, $to, $from);
		return @iconv($from, $to, $what);
	}

	public static function cp1251_to_utf8($what) {
		return self::encode($what, 'CP1251', 'UTF-8');
	}

	public static function utf8_to_cp1251($what) {
		return self::encode($what, 'UTF-8', 'CP1251');
	}
}