<?

class ReflectionModel {

	public static function getClassConstValue($classname, $constname) {
		return constant(sprintf('%s::%s', $classname, $constname));
	}

	public static function getClassConstList($classname, $pattern=null) {

		static $classes = array();

		if(!isset($classes[$classname])) {
			$classes[$classname] = new ReflectionClass($classname);
		}

		$class =& $classes[$classname];
		$constants = $class->getConstants();

		if($pattern) {
			foreach($constants as $constant=>$value) {
				if(!_strstr($constant, $pattern)) {
					unset($constants[$constant]);
				}
			}
		}

		return $constants;
	}

	public static function getClassConstNameList($classname, $pattern=null) {
		$list = self::getClassConstList($classname, $pattern);
		$list = array_keys($list);
		return $list;
	}

	public static function getClassConstValueList($classname, $pattern=null) {
		$list = self::getClassConstList($classname, $pattern);
		$list = array_values($list);
		return $list;
	}
}