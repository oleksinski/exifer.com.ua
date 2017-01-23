<?

class SafeHtmlModel extends SafeHTML {

	private static $safehtml;

	public static function &getInstance() {
		if(!self::$safehtml) {
			self::$safehtml = new SafeHTML();
		}
		return self::$safehtml;
	}

	public static function input($doc) {
		$safehtml =& self::getInstance();
		$doc = $safehtml->parse($doc);
		$doc = strip_tags($doc);
		$doc = Text::removeExtraNL($doc);
		$safehtml->clear();
		return $doc;
	}

	public static function output($doc, $wraplength=45, $nl2br=true, $escapehtml=true, $cutLen=0) {
		$doc = Text::strPrepare($doc, $escapehtml, $cutLen, $wraplength, $nl2br);
		//$doc = Text::wrapStr($doc, $wraplength);
		//if($nl2br) $doc = nl2br($doc);
		return $doc;
	}

	/**
	 * @param string $string
	 * @param mixed $escape_callback_func (may be a string func name, or array(Class, Method))
	 * @return escaped string with urls
	*/
	public static function output_urlify($string, $cut_length=null, $escape_callback_func=array('SafeHtmlModel','output')) {

		$cut_length = ifsetor($cut_length, 55);

		// --= fetch all urls =--- //
		$re_pattern = Regexp::re_pattern(Regexp::re_url(), 'i');
		$match_arr = Regexp::match_all($re_pattern, $string);

		$url_arr = array_unique($match_arr);

		$url_hash_arr = array();
		foreach($url_arr as $url) {
			$hash = '# '.md5($url).' #';
			$url_hash_arr[$hash] = $url;
		}

		foreach($url_hash_arr as $hash=>$url) {
			$string = _str_replace($url, $hash, $string);
		}

		// --= fetch all emails =--- //
		$re_pattern = Regexp::re_pattern(Regexp::re_email(), 'i');
		$match_arr = Regexp::match_all($re_pattern, $string);

		$email_arr = array_unique($match_arr);

		$email_hash_arr = array();
		foreach($email_arr as $email) {
			$hash = '# '.md5($email).' #';
			$email_hash_arr[$hash] = $email;
		}

		foreach($email_hash_arr as $hash=>$email) {
			$string = _str_replace($email, $hash, $string);
		}

		// perform html escape
		$string = call_user_func_array($escape_callback_func, array($string));

		// paste urls
		foreach($url_hash_arr as $hash=>$url) {
			$href = Regexp::match('#^(https?|ftps?|gopher|telnet|nntp)://.*$#i', $url) ? $url : sprintf('http://%s', $url);
			$replacement = sprintf('<a href="%s" class="inlink" title="" rel="nofollow" target="_blank">%s</a>', $href, Text::cutStr($url, $cut_length));
			$string = _str_replace($hash, $replacement, $string);
		}

		// paste emails
		foreach($email_hash_arr as $hash=>$email) {
			$replacement = sprintf('<a href="mailto:%s" class="inlink" title="" rel="nofollow">%s</a>', Text::escapeEmail($email), Text::escapeEmail(Text::cutStr($email, $cut_length)));
			$string = _str_replace($hash, $replacement, $string);
		}

		return $string;
	}

}
