<?

class JsonModel {

	private static $__json = null;

	public static function &getJsonInstance() {
		if(is_null(self::$__json)) {
			$__json = new Json();
		}
		return $__json;
	}

	public static function encode($php, $options=null) {
		//$__json =& self::getJsonInstance();
		//$php = $__json->encode_cyr_php($php);
		//$json = $__json->encode($php);
		$json = json_encode($php, $options);
		//if($print_header && !headers_sent()) {
		//	header('Content-Type: application/json');
		//}
		return $json;
	}

	public static function decode($json, $assoc=true) {
		//$__json =& self::getJsonInstance();
		//$php = $__json->decode($json);
		//$php = $__json->decode_cyr_php($php);
		$php = json_decode($json, $assoc);
		return $php;
	}
}