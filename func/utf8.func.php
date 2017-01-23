<?

define('__ENC__', mb_internal_encoding());

//define('__ENC__', 'UTF-8');
//setLocale(LC_ALL, 'ru_RU.UTF-8');
//header('Content-Type: text/html; charset=utf-8');
//mb_internal_encoding(__ENC__);
//mb_regex_encoding(__ENC__);
//mb_regex_set_options('pnz');

// ---

function _strlen($string) {
	return mb_strlen($string);
}

function _strtolower($string) {
	//return mb_convert_case($string, MB_CASE_LOWER);
	return mb_strtolower($string);
}

function _strtoupper($string) {
	//return mb_convert_case($string, MB_CASE_UPPER);
	return mb_strtoupper($string);
}

function _ucfirst($string) {
	$char = _strtoupper(_substr($string, 0, 1));
	return _substr_replace($string, $char, 0, 1);
}

function _lcfirst($string) {
	$char = _strtolower(_substr($string, 0, 1));
	return _substr_replace($string, $char, 0, 1);
}

function _ucwords($string) {
	$string = mb_convert_case($string, MB_CASE_TITLE);
	return $string;
}

function _substr($string, $start=0, $length=null) {
	$args = func_get_args();
	return call_user_func_array('mb_substr', $args);
	//return call_user_func_array('mb_strcut', $args);
}

function _str_replace($search, $replace, $subject, &$count=null) {
	//$args = func_get_args();
	//return call_user_func_array('str_replace', $args);
	return str_replace($search, $replace, $subject, $count);
}

function _str_ireplace($search, $replace, $subject, &$count=null) {
	//$args = func_get_args();
	//return call_user_func_array('str_ireplace', $args);
	return str_ireplace($search, $replace, $subject, $count);
}

function _substr_replace($string, $replacement, $start, $length=null) {

	$strlen = _strlen($string);
	$start_actual = $start;
	$length_actual = is_null($length) ? $strlen : $length;

	if($start<0) {
		$start_actual += $strlen;
		if($start_actual<0) {
			$start_actual = 0;
		}
	}

	$start_actual_1 = $start_actual;
	$start_actual_2 = $start_actual+$length_actual;

	if($length<0) {
		$start_actual_2 = $strlen+$length;
		if($start_actual_2<0) {
			$start_actual_2 = $start_actual_1;
		}
	}

	$s1 = _substr($string, 0, $start_actual_1);
	$s2 = $replacement;
	$s3 = _substr($string, $start_actual_2, $strlen);

	return $s1.$s2.$s3;
}

function _substr_count($string, $needle, $offset=0, $length=null) {
	return mb_substr_count($string, $needle);
}

function _substr_compare($main_str, $str, $offset, $length=null, $case_insensitivity=false) {
	$args = func_get_args();
	return call_user_func_array('substr_compare', $args);
}

function _str_repeat($input, $multiplier) {
	return str_repeat($input, $multiplier);
}

function _strstr($haystack, $needle, $before_needle=false) {
	return mb_strstr($haystack, $needle, $before_needle);
}

function _strchr($haystack, $needle, $before_needle=false) {
	return _strstr($haystack, $needle, $before_needle);
}

function _strrchr($string, $needle) {
	return mb_strrchr($string, $needle);
}

function _strrichr($string, $needle, $part=false) {
	return mb_strrichr($string, $needle, $part);
}

function _stristr($haystack, $needle, $before_needle=false) {
	return mb_stristr($haystack, $needle, $before_needle);
}

function _strtr($string, $from, $to=null) {
	if(is_null($to)) {
		return strtr($string, $from);
	}
	else {
		preg_match_all('/./u', $from, $keys);
		preg_match_all('/./u', $to, $values);
		return _strtr($string, array_combine($keys[0], $values[0]));
	}
}

function _strpos($haystack, $needle, $offset=0) {
	return mb_strpos($haystack, $needle, $offset);
}

function _stripos($haystack, $needle, $offset=0) {
	return mb_stripos($haystack, $needle, $offset);
}

function _strripos($string, $needle, $offset=0) {
	return mb_strripos($string, $needle, $offset);
}

function _strrpos($string, $needle, $offset=0) {
	return mb_strrpos($string, $needle, $offset);
}

function _str_split($string, $split_length=1) {
	$array = array();
	$string_length = _strlen($string);
	$split_length = (int)$split_length; if($split_length<1) $split_length=1;
	for($i=0; $i<$string_length; $i=$i+$split_length) {
		$array[] = _substr($string, $i, $split_length);
	}
	return $array;
}

function _strrev($string) {
	return implode('', array_reverse(_str_split($string)));
}

function _str_shuffle($string) {
	$array = _str_split($string);
	shuffle($array);
	$string = implode('', $array);
	return $string;
}

function _addcslashes($str, $charlist) {
	//$args = func_get_args();
	//return call_user_func_array('addcslashes', $args);
	return addcslashes($str, $charlist);
}

function _stripcslashes($str) {
	//$args = func_get_args();
	//return call_user_func_array('stripcslashes', $args);
	return stripcslashes($str);
}

function _parse_str($string, &$arr=null) {
	mb_parse_str($string, $arr);
}

function _addslashes($string) {
	return mysql_escape_string($string);
}

function _stripslashes($string) {
	$string = (string)$string;
	return stripslashes($string);
}

function _trim($string, $charlist=null, $border=3) {

	if($charlist) {
		$charlist_array = _str_split($charlist);
		$charlist_array = array_map('preg_quote', $charlist_array);
	}
	else {
		$charlist_array = array("\s","\t","\n","\r", "\0", "\x0B");
	}
	$charlist_string = implode('|', $charlist_array);
	if($border&1) {
		$string = mb_ereg_replace("^($charlist_string)*", '', $string);
	}
	if($border&2) {
		$string = mb_ereg_replace("($charlist_string)*$", '', $string);
	}
	return $string;
}

function _ltrim($string, $charlist=null) {
	return _trim($string, $charlist, 1);
}

function _rtrim($string, $charlist=null) {
	return _trim($string, $charlist, 2);
}

function _wordwrap($string, $width=75, $break="\n") {
	$n_exploded = explode("\n", $string);
	for($i=0; $i<count($n_exploded); $i++) {
		$s_exploded = explode(" ", $n_exploded[$i]);
		for($j=0; $j<count($s_exploded); $j++) {
			$s_exploded[$j] = implode($break, _str_split($s_exploded[$j], $width));
		}
		$n_exploded[$i] = implode(" ", $s_exploded);
	}
	$string = implode("\n", $n_exploded);
	return $string;
	//return implode($break, _str_split($string, $width));
}

function _htmlspecialchars($string, $quote_style=ENT_QUOTES, $charset=__ENC__, $double_encode=true) {
	return htmlspecialchars($string, $quote_style, $charset, $double_encode);
}

function _htmlspecialchars_decode($string, $quote_style=ENT_QUOTES) {
	return htmlspecialchars_decode($string, $quote_style);
}

function _htmlentities($string, $quote_style=ENT_QUOTES, $charset=__ENC__, $double_encode=true) {
	return htmlentities($string, $quote_style, $charset, $double_encode);
}

function _html_entity_decode($string, $quote_style=ENT_QUOTES, $charset=__ENC__) {
	return html_entity_decode($string, $quote_style, $charset);
}

function _xmlspecialchars($string) {
	$string = _htmlspecialchars($string, ENT_QUOTES);
	$string = _str_replace('&#039;', '&apos;', $string);
	return $string;
}

function _xmlspecialchars_decode() {
	$string = _str_replace('&apos;', '&#039;', $string);
	$string = _htmlspecialchars_decode($string, ENT_QUOTES);
	return $string;
}

function _crc32($string) {
	//return array_pop(unpack("l",pack("l",crc32($var))));
	return sprintf('%u', crc32($string));
}

function _preg_split($pattern, $subject, $limit=-1, $flags=0) {

	// pattern should be without backslash container
	$pattern = _trim($pattern, '/#');

	$result = mb_split($pattern, $subject, $limit);

	if($flags&PREG_SPLIT_NO_EMPTY) {
		foreach($result as $k=>&$v) {
			if($v==='') unset($result[$k]);
		}
	}
	if($flags&PREG_SPLIT_DELIM_CAPTURE) {
		// not implemented yet
	}
	if($flags&PREG_SPLIT_OFFSET_CAPTURE) {
		// not implemented yet
	}

	return $result;
}

function _preg_filter($pattern, $replacement, $subject, $limit=-1, &$count=null) {
	$result = _preg_split($pattern, $subject, $limit);
	//foreach($result as $k=>$v) {}
	return $result;
}

// http://habrahabr.ru/blogs/php/45910/
// http://test.dis.dj/utf/
// http://www.regular-expressions.info/unicode.html
// http://bolknote.ru/2010/09/08/~2704#22
// http://bolknote.ru/2010/09/08/~2706#00
// http://bolknote.ru/2010/09/16/~2727#13


function _preg_match($pattern, $subject, &$matches=null, $flags=0, $offset=0) {
	$pattern = cp1251_to_utf8_pattern($pattern);
	$result = preg_match($pattern, $subject, $matches, $flags, $offset);
	if($result) {
		if($flags&PREG_OFFSET_CAPTURE) {
			foreach($matches as &$match) {
				if(is_array($match)) {
					// do not use _substr here
					$match[1] = _strlen(substr($subject, 0, $match[1]));
				}
			}
		}
	}
	return $result;
}

function _preg_match_all($pattern, $subject, &$matches=null, $flags=0, $offset=0) {
	$pattern = cp1251_to_utf8_pattern($pattern);
	$result = preg_match_all($pattern, $subject, $matches, $flags, $offset);
	if($result) {
		if($flags&PREG_PATTERN_ORDER) {
			// not implemented yet
		}
		if($flags&PREG_SET_ORDER) {
			// not implemented yet
		}
		if($flags&PREG_OFFSET_CAPTURE) {
			foreach($matches as &$match) {
				foreach($match as &$match) {
					if(is_array($match)) {
						// do not use _substr here
						$match[1] = _strlen(substr($subject, 0, $match[1]));
					}
				}
			}
		}
	}
	return $result;
}

function _preg_grep($pattern, array $input, $flags=0) {
	$pattern = cp1251_to_utf8_pattern($pattern);
	$args = func_get_args();
	$args[0] = $pattern;
	return call_user_func_array('preg_grep', $args);
}

function _preg_replace($pattern, $replacement, $subject, $limit=-1, &$count=0) {
	$pattern = cp1251_to_utf8_pattern($pattern);
	//$args = func_get_args();
	//$args[0] = $pattern;
	//return call_user_func_array('preg_replace', $args);
	return preg_replace($pattern, $replacement, $subject, $limit, $count);
}

function _preg_replace_callback($pattern, $callback, $subject, $limit = -1, &$count=0) {
	//$replacement = call_user_func($callback);
	//return _preg_replace($pattern, $replacement, $subject, $limit, $count);
	return preg_replace_callback($pattern, $callback, $subject, $limit, $count);
}

/*
\p{L} or \p{Letter}: any kind of letter from any language.
	o \p{Ll} or \p{Lowercase_Letter}: a lowercase letter that has an uppercase variant.
	o \p{Lu} or \p{Uppercase_Letter}: an uppercase letter that has a lowercase variant.
	o \p{Lt} or \p{Titlecase_Letter}: a letter that appears at the start of a word when only the first letter of the word is capitalized.
	o \p{L&} or \p{Letter&}: a letter that exists in lowercase and uppercase variants (combination of Ll, Lu and Lt).
	o \p{Lm} or \p{Modifier_Letter}: a special character that is used like a letter.
	o \p{Lo} or \p{Other_Letter}: a letter or ideograph that does not have lowercase and uppercase variants.
\p{M} or \p{Mark}: a character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.).
	o \p{Mn} or \p{Non_Spacing_Mark}: a character intended to be combined with another character without taking up extra space (e.g. accents, umlauts, etc.).
	o \p{Mc} or \p{Spacing_Combining_Mark}: a character intended to be combined with another character that takes up extra space (vowel signs in many Eastern languages).
	o \p{Me} or \p{Enclosing_Mark}: a character that encloses the character is is combined with (circle, square, keycap, etc.).
\p{Z} or \p{Separator}: any kind of whitespace or invisible separator.
	o \p{Zs} or \p{Space_Separator}: a whitespace character that is invisible, but does take up space.
	o \p{Zl} or \p{Line_Separator}: line separator character U+2028.
	o \p{Zp} or \p{Paragraph_Separator}: paragraph separator character U+2029.
\p{S} or \p{Symbol}: math symbols, currency signs, dingbats, box-drawing characters, etc..
	o \p{Sm} or \p{Math_Symbol}: any mathematical symbol.
	o \p{Sc} or \p{Currency_Symbol}: any currency sign.
	o \p{Sk} or \p{Modifier_Symbol}: a combining character (mark) as a full character on its own.
	o \p{So} or \p{Other_Symbol}: various symbols that are not math symbols, currency signs, or combining characters.
\p{N} or \p{Number}: any kind of numeric character in any script.
	o \p{Nd} or \p{Decimal_Digit_Number}: a digit zero through nine in any script except ideographic scripts.
	o \p{Nl} or \p{Letter_Number}: a number that looks like a letter, such as a Roman numeral.
	o \p{No} or \p{Other_Number}: a superscript or subscript digit, or a number that is not a digit 0..9 (excluding numbers from ideographic scripts).
\p{P} or \p{Punctuation}: any kind of punctuation character.
	o \p{Pd} or \p{Dash_Punctuation}: any kind of hyphen or dash.
	o \p{Ps} or \p{Open_Punctuation}: any kind of opening bracket.
	o \p{Pe} or \p{Close_Punctuation}: any kind of closing bracket.
	o \p{Pi} or \p{Initial_Punctuation}: any kind of opening quote.
	o \p{Pf} or \p{Final_Punctuation}: any kind of closing quote.
	o \p{Pc} or \p{Connector_Punctuation}: a punctuation character such as an underscore that connects words.
	o \p{Po} or \p{Other_Punctuation}: any kind of punctuation character that is not a dash, bracket, quote or connector.
\p{C} or \p{Other}: invisible control characters and unused code points.
	o \p{Cc} or \p{Control}: an ASCII 0x00..0x1F or Latin-1 0x80..0x9F control character.
	o \p{Cf} or \p{Format}: invisible formatting indicator.
	o \p{Co} or \p{Private_Use}: any code point reserved for private use.
	o \p{Cs} or \p{Surrogate}: one half of a surrogate pair in UTF-16 encoding.
	o \p{Cn} or \p{Unassigned}: any code point to which no character has been assigned.
*/

/**
 * Should be rewritten when PCRE 8.10 will be available
 */
function cp1251_to_utf8_pattern($pattern) {

	if($pattern) $pattern .= 'u';

	//$alpha = '\p{L}';

	//$w = '\p{L}|\p{Nd}|_';
	//$W = '^'.$w;

	$w = '[\p{L}\p{Nd}_]';
	$W = '[^\p{L}\p{Nd}_]';

	//$w = '[A-Za-zА-Яа-яЁёЇїЄєІіҐґ0-9_]';
	//$W = '[^A-Za-zА-Яа-яЁёЇїЄєІіҐґ0-9_]';

	//$d = '\p{Nd}';
	//$D = '^'.$d;

	$d = '\p{Nd}';
	$D = '[^\p{Nd}]';

	//$d = '[0-9]';
	//$D = '[^0-9]';

	$map = array(
		'\w' => $w,
		'\d' => $d,
		//'\W' => $W,
		//'\D' => $D,
		//'^\W' => $w,
		//'^\D' => $d,
	);

	$pattern = _strtr($pattern, $map);

/*
	$pattern_new = null;

	// --- modify output regexp --- //
	preg_match_all('/./', $pattern, $pattern_arr);
	$pattern_arr = ifsetor($pattern_arr[0], array());
	$pattern_arr_count = count($pattern_arr);
	$set_opened = $set_closed = array();

	for($i=0; $i<$pattern_arr_count; $i++) {
		$chr_prev = ifsetor($pattern_arr[$i-1], null);
		$chr_curr = ifsetor($pattern_arr[$i], null);
		if($chr_prev!='\'') {
			if($chr_curr=='[') {
				if(count($set_opened)) {
					unset($pattern_arr[$i]);
				}
				array_push($set_opened, $i);
			}
			elseif($chr_curr==']') {
				array_pop($set_opened);
				if(count($set_opened)) {
					unset($pattern_arr[$i]);
				}
			}
		}
	}

	$pattern_new = implode('', $pattern_arr);

	if(strcmp($pattern, $pattern_new)) {
		_e($pattern . ' --- ' . $pattern_new);
	}
*/
	return $pattern;
}
