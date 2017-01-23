<?

/**
 * DataType Casting
 *
 */

class Cast {

	// to int
	public static function int($var) {
		return is_scalar($var) ? (int)$var : 0;
	}

	// To abs int
	/*
	public static function absint($var) {
		return abs(self::int($var));
	}
	*/

	// To unsigned int
	public static function unsignint($var) {
		$var = self::int($var);
		if($var<0) {
			$var = 0;
		}

		return $var;
	}

	/*
	public static function forceunsignint() {
		$var = self::int($var);
		if($var<0) {
			$var += 4294967296;
		}
		return $var;
	}
	*/

	// To array of int
	public static function intarr($var, $killNull=true) {
		$array = (array)$var;
		foreach($array as $key=>$value) {
			$array[$key] = self::int($value);
		}
		$array = array_unique(array_values($array));
		if($killNull) {
			$array = array_diff($array, array(0));
		}
		return $array;
	}

	/*
	// To array of abs int
	public static function absintarr($var) {
		$array = self::intarr($var);
		foreach($array as $key=>$value) {
			$array[$key] = self::absint($value);
		}
		$array = array_unique(array_values($array));
		return $array;
	}
	*/

	public static function unsignintarr($var) {
		$array = (array)$var;
		foreach($array as $key=>$value) {
			$array[$key] = self::unsignint($value);
		}
		$array = array_unique(array_values($array));
		return $array;
	}

	// To string
	public static function str($var) {
		return is_scalar($var) ? (string)$var : '';
	}

	// To array of string
	public static function strarr($var) {
		$array = (array)$var;
		foreach($array as $key=>$email) {
			if(isset($array[$key])) {
				if(empty($array[$key])) {
					unset($array[$key]);
				}
				else {
					$array[$key] = self::str($array[$key]);
				}
			}
		}
		$array = array_unique(array_values($array));
		return $array;
	}

	public static function float($var) {
		return floatval($var);
	}

	public static function floatarr($var) {
		$array = (array)$var;
		foreach($array as $key=>$value) {
			$array[$key] = self::float($value);
		}
		$array = array_unique(array_values($array));
		return $array;
	}

	public static function bool($var) {
		return (bool)$var;
	}

	public static function setbit($value, $bit) {
		$value = self::int($value);
		$bit = self::int($bit);
		$value |= $bit;
		return $value;
	}

	public static function unsetbit($value, $bit) {
		$value = self::int($value);
		$bit = self::int($bit);
		$value &= ~$bit;
		return $value;
	}

	public static function byte2megabyte($value) {

		$value = self::float($value);
		$value = $value/(1024*1024);
		return $value;
	}

	public static function megabyte2byte($value) {

		$value = self::float($value);
		$value = self::unsignint($value * 1024 * 1024);
		return $value;
	}

	public static function kilobyte2byte($value) {

		$value = self::float($value);
		$value = self::unsignint($value * 1024);
		return $value;
	}

	/**
	 * MySQL INET_ATON analogue
	 */
	public static function iptolong($ip) {
		return self::long32bit(ip2long($ip));
	}

	/**
	 * MySQL INET_NTOA analogue
	 */
	public static function longtoip($ip) {
		return long2ip($ip);
	}

	public static function long32bit($long) {
		return sprintf('%u', $long);
		//list(, $long) = unpack('l',pack('l', $long));
		//return $long;
	}

	/**
	 * Calc Human readable
	 * @param string  [1/20, 10/250, 374/10, 30]
	 * @return string [1/20, 1/250,  37.4,   30]
	 */
	public static function CalcStrFraction($StrFration) {

		$StrFration = self::str($StrFration);

		if($StrFration) {

			$exposure = explode('/', $StrFration);

			$top = self::int(@$exposure[0]);
			$bot = self::int(@$exposure[1]);

			if($top<$bot && $top>1 && $bot>0) { // 10/250
				$StrFration = sprintf('1/%d', round(self::CalcFraction($bot, $top), 2));
			}
			elseif($top>$bot && $bot>0) { // 200/35, 35/1
				$StrFration = sprintf('%s', round(self::CalcFraction($top, $bot), 2));
			}
			else { // 1/20
				$StrFration = sprintf('%u/%u', $top, $bot);
			}
		}
		return $StrFration;
	}

	/**
	 * Calc Human readable
	 * @param string  [1/20, 10/250, 374/10, 30]
	 * @return string [0.05, 0.04,   37.4,   30]
	 */
	public static function CalcIntFraction($StrFration) {

		$IntFration = $StrFration;

		$focallength = explode('/', $IntFration);

		$top = self::int(@$focallength[0]);
		$bot = self::int(@$focallength[1]);

		$IntFration = self::CalcFraction($top, $bot);

		$IntFration = (float)round($IntFration, 2);

		return $IntFration;
	}

	public static function CalcFraction($top, $bot) {

		$top = self::int($top); if(!$top) $top = 0;
		$bot = self::int($bot); if(!$bot) $bot = 1;

		$fraction = (float)($top/$bot);

		return $fraction;
	}

}