<?

class Translit {

	const LITERAL_SEPARATOR = '-';
	const URLNAME_SEPARATOR = '_';

	public static function getCommonLetterScheme() {

		$scheme = array(
			'А'=>'A', 'а'=>'a',
			'Б'=>'B', 'б'=>'b',
			'В'=>'V', 'в'=>'v',
			'Г'=>'G', 'г'=>'g',
			'Ґ'=>'G', 'ґ'=>'g',
			'Д'=>'D', 'д'=>'d',
			'Е'=>'E', 'е'=>'e',
			'Ё'=>'E', 'ё'=>'e',
			'Ж'=>'Zh', 'ж'=>'zh',
			'З'=>'Z', 'з'=>'z',
			'И'=>'I', 'и'=>'i',
			'Й'=>'J', 'й'=>'j',
			'К'=>'K', 'к'=>'k',
			'Л'=>'L', 'л'=>'l',
			'М'=>'M', 'м'=>'m',
			'Н'=>'N', 'н'=>'n',
			'О'=>'O', 'о'=>'o',
			'П'=>'P', 'п'=>'p',
			'Р'=>'R', 'р'=>'r',
			'С'=>'S', 'с'=>'s',
			'Т'=>'T', 'т'=>'t',
			'У'=>'U', 'у'=>'u',
			'Ф'=>'F', 'ф'=>'f',
			'Х'=>'H', 'х'=>'h',
			'Ц'=>'C', 'ц'=>'c',
			'Ч'=>'Ch', 'ч'=>'ch',
			'Ш'=>'Sh', 'ш'=>'sh',
			'Щ'=>'Shch', 'щ'=>'shch',
			'Ъ'=>'', 'ъ'=>'',
			'Ы'=>'Y', 'ы'=>'y',
			'Ь'=>'', 'ь'=>'',
			'Э'=>'E', 'э'=>'e',
			'Ю'=>'Yu', 'ю'=>'yu',
			'Я'=>'Ya', 'я'=>'ya',
			'І'=>'I', 'і'=>'i',
			'Є'=>'Ie', 'є'=>'ie',
			'Ї'=>'I', 'ї'=>'i',
		);

		return $scheme;
	}

	public static function getInitialLetterPositionScheme() {

		$scheme = array(
			'Ю'=>'Yu', 'ю'=>'yu',
			'Я'=>'Ya', 'я'=>'ya',
			'Є'=>'Ye', 'є'=>'ye',
			'Ї'=>'Yi', 'ї'=>'yi',
		);

		return $scheme;
	}

	public static function getUrlScheme() {

		$scheme = array(
			//self::LITERAL_SEPARATOR=>str_repeat(self::LITERAL_SEPARATOR, 2),
			' '=>self::LITERAL_SEPARATOR,
			'('=>self::LITERAL_SEPARATOR,
			')'=>self::LITERAL_SEPARATOR,
		);

		return $scheme;
	}

	public static function transliterate($string) {

		/*
		// Transliterate first letters
		$scheme1 = self::getInitialLetterPositionScheme();
		$re = array();
		foreach($scheme1 as $k=>$v) {
			$re[sprintf('|\b%s|si', $k)] = $v;
		}
		$search = array_keys($re);
		$replacement = array_values($re);
		$string = preg_replace($search, $replacement, $string);
		*/

		// first letters scheme
		$scheme1 = self::getInitialLetterPositionScheme();

		// main transliterate
		$scheme2 = self::getCommonLetterScheme();

		$scheme = array_merge($scheme2, $scheme1);

		foreach($scheme as $k=>$v) {
			$string = _str_replace($k, $v, $string);
		}

		return $string;
	}

	/**
	 * Transliterate string so as it can be used in url
	 * Output result includes all utf-8 compatible letters, digits and signs -_
	 * If $strict param is equal TRUE, then output result includes only roman letters, digits and signs -_
	 *
	 * @param unknown_type $string
	 * @param unknown_type $strict
	 * @return string
	 */
	public static function urlify($string, $strict=false) {

		$string = _strtolower($string);

		$string = self::transliterate($string);

		$replace_re = $strict ? '/[^a-z0-9_\-]/ui' : '/[^\p{L}\p{Nd}_\s\-]/ui';
		$string = _preg_replace($replace_re, self::LITERAL_SEPARATOR, $string);

		$scheme = self::getUrlScheme();
		foreach($scheme as $k=>$v) {
			$string = _str_replace($k, $v, $string);
		}

		if(_strpos($string, str_repeat(self::LITERAL_SEPARATOR, 2))) {
			$regexp = sprintf('/\%s{2,}/s', self::LITERAL_SEPARATOR);
			$string = _preg_replace($regexp, self::LITERAL_SEPARATOR, $string);
		}

		$string = _trim($string, self::LITERAL_SEPARATOR);

		$string = _htmlspecialchars($string);

		return $string;
	}

	/**
	 * Generates string by strict rules to use as user name, location value, genre value etc.
	 *
	 * @param unknown_type $input
	 * @return string
	 */
	public static function generateUrlName($input) {

		$result = self::urlify($input, true);

		// remove symbols from the start which are not particular roman letters
		$result = _preg_replace('/^[^a-z]*/ui', '', $result);

		$result = _preg_replace('/[^a-z0-9]/ui', self::URLNAME_SEPARATOR, $result);

		if(_strpos($result, str_repeat(self::URLNAME_SEPARATOR, 2))) {
			$regexp = sprintf('/\%s{2,}/s', self::URLNAME_SEPARATOR);
			$result = _preg_replace($regexp, self::URLNAME_SEPARATOR, $result);
		}

		$result = _trim($result, self::URLNAME_SEPARATOR);

		if(!Regexp::match('/^[a-z]/ui', $result)) {
			$result = null;
		}

		return $result;
	}

}
