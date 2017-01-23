<?

class Singleton /*implements SingletonInterface*/ {

	protected static $singleton = array();

	public static function &getInstance($c=__CLASS__) {
		if(!isset(self::$singleton[$c])){
			self::$singleton[$c] = new $c();
		}
		return self::$singleton[$c];
	}

	protected function __construct() {}

	final public function __clone() {}
}