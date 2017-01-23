<?


class Text {

	/**
	 * Composite method for string processing
	 * @param string $str
	 * @param bool $escapehtml
	 * @param int $cutLen
	 * @param int $wrap
	 * @param bool $nl2br
	 * @return string
	 */
	public static function strPrepare($str, $escapehtml=true, $cutLen=0, $wrapLen=45, $nl2br=false) {

		$str = Cast::str($str);
		$cutLen = Cast::unsignint($cutLen);
		$wrapLen = Cast::unsignint($wrapLen);

		$str = _trim($str);

		$str = self::removeExtraNL($str);

		if($wrapLen) {
			$str = self::wrapStr($str, $wrapLen);
		}

		if($cutLen) {
			$str = self::cutStr($str, $cutLen);
		}

		if($escapehtml) {
			$str = _htmlspecialchars($str);
		}

		if($nl2br) {
			$str = nl2br($str);
		}

		return $str;
	}


	/**
	 * Cut long string on word borders
	 * @param string $text
	 * @param int $len
	 * @param string $more
	 * @param int $addlen
	 * @return string
	 */
	public static function cutStr($text, $len, $more='...', $addlen=10) {
		$text = Cast::str($text);
		$len = Cast::unsignint($len);
		if(_strlen($text) > $len) {
			$more = Cast::str($more);
			$addlen = Cast::unsignint($addlen);
			$text = _substr($text, 0, $len + $addlen);
			$text = _preg_replace('/[^!?;.,>-\s]+$/', '', $text);
			$text = _trim($text).$more;
		}
		return $text;
	}


	/**
	 * wrap string by given separator
	 * @param string $str
	 * @param int $maxWordLength
	 * @param scalar $separator
	 * @return string
	 */
	public static function wrapStr($str, $maxWordLength=45, $separator=' ') {

		$str = Cast::str($str);
		$maxWordLength = Cast::unsignint($maxWordLength);
		$separator = Cast::str($separator);

		// wrapping long words
		$words = explode($separator, $str);

		foreach($words as $word) {

			if(_strlen($word) > $maxWordLength) {

				$newword = _wordwrap($word, $maxWordLength, $separator, true);

				$str = _str_replace($word, $newword, $str);
			}
		}

		return $str;
	}


	/**
	 * remove extra cariage return symbols
	 * @param string $str
	 * @param int $inputNLCount
	 * @return $string
	 */
	public static function removeExtraNL($str, $inputNLCount=2, $minNL2remove=2) {

		$str = Cast::str($str);
		$inputNLCount = Cast::unsignint($inputNLCount);
		$minNL2remove = Cast::unsignint($minNL2remove);
		if($minNL2remove<=0) $minNL2remove = 1;

		$n = "\n"; // carriage new line
		$r = "\r"; // carriage return
		$t = "\t"; // tab symbol
		$rn = "\r\n"; // carriage new line + return

		$rn_mult = _str_repeat($rn, $inputNLCount);
		$n_mult = _str_repeat($n, $inputNLCount);
		$r_mult = _str_repeat($r, $inputNLCount);

		$str = _preg_replace("/({$t})/", '', $str);
		$str = _preg_replace("/({$rn}){{$minNL2remove},}/", $rn_mult, $str);
		$str = _preg_replace("/{$n}{{$minNL2remove},}/", $n_mult, $str);
		$str = _preg_replace("/{$r}{{$minNL2remove},}/", $r_mult, $str);

		$str = _trim($str);

		return $str;
	}

	/**
	 *
	 * @param unknown_type $email
	 * @return string
	 */
	public static function escapeEmail($email) {
		$email_hex = '';
		for($i = 0; $i<_strlen($email); $i++) {
			$email_hex .= '&#x'.bin2hex($email[$i]).';';
			//$email_hex .= '&#' . ord($email[$i]) . ';';
		}
		return $email_hex;
	}

	/**
	 *
	 * @param unknown_type $string
	 * @param unknown_type $step
	 * @return string
	 */
	public static function escapeJS($string, $step=5) {
		$result = '';
		$array = _str_split($string, $step);
		foreach($array as $str) {
			if($result) $result .= '+';
			$result .= MySQL::str($str);
		}
		return $result;
	}

	/**
	 * HighLight search found patterns
	 * @param array of words
	 * @param string[html]
	 * @param html-css class
	 * @return string
	 */
	public static function highlighter($keywords, $str_data, $span_class='highlight') {

		//$keywords - array with words, that should be highlighted

		if(!is_array($keywords)) $keywords = array($keywords);

		if(!count($keywords)) return $str_data;

		// remove all tags and put them in array $tagList
		_preg_match_all('#<[^>]*>#', $str_data, $tags); array_unique($tags);
		$tagList=array(); $k = 0;
		foreach($tags[0] as $i) {
			$k++;
			$tagList[$k] = $i;
			$str_data = _str_replace($i, '<' . $k . '>', $str_data);
		}

		// wrap text in span's
		$re_meta_escape = array('/', '^', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '#');
		foreach($keywords as $i) {
			if(!is_numeric($i)) {
				// escape regexp meta symbols to use inside regular expression
				if(_strpos($i, '\\') !== false) { // fix with \ escape
					$i = _str_replace('\\', '\\\\', $i);
				}
				foreach($re_meta_escape as $re_meta) {
					$i = _preg_replace(('/\\' . $re_meta . '/'), ('\\'.$re_meta), $i);
				}
				$str_data = _preg_replace('#' . $i . '#i', '<span class="'.$span_class.'">$0</span>', $str_data);
			}
		}

		// put all tags on their old place
		foreach($tagList as $k=>$i) {
			$str_data = _str_replace('<' . $k . '>', $i, $str_data);
		}

		// remove all highlights inside tags <title> ... </title>
		$data = _preg_replace_callback(
			'#<title>(.*?)<\/title>#',
			Util::CreateFunction('$m', 'return _preg_replace("#<span([^>]*)>#", "", _str_replace("</span>", "", $m[0]));'),
			$str_data
		);

		return $str_data;
	}

	/**
	 * Html escape string preserving html entitities
	 *
	 * @param unknown_type $string
	 */
	public static function smartHtmlSpecialChars($string) {
		$preserveList = self::getHtmlEntitityList();
		foreach($preserveList as $i=>$s) {
			$string = _str_replace($s, sprintf('{%u}', $i), $string);
		}
		$string = _htmlspecialchars($string);
		foreach($preserveList as $i=>$s) {
			$string = _str_replace(sprintf('{%u}', $i), $s, $string);
		}
		return $string;
	}

	/**
	 * XML escape string preserving xml|html entitities
	 *
	 * @param unknown_type $string
	 */
	public static function smartXmlSpecialChars($string) {
		$preserveList = self::getHtmlEntitityList();
		foreach($preserveList as $i=>$s) {
			$string = _str_replace($s, sprintf('{%u}', $i), $string);
		}
		$string = _xmlspecialchars($string);
		foreach($preserveList as $i=>$s) {
			$string = _str_replace(sprintf('{%u}', $i), $s, $string);
		}
		return $string;
	}

	/**
	 *
	 */
	public static function getHtmlEntitityList() {
		return array('&ndash;', '&mdash;', '&quot;', '&apos;', '&#39;', '&nbsp;', '&amp;', '&laquo;', '&raquo;', '&lt;', '&gt;', '&lsaquo;', '&rsaquo;');
	}
}
