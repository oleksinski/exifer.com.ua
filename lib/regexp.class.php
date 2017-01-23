<?

/**

REGEXP INFO:
----------------
http://www.owasp.org/index.php/OWASP_Validation_Regex_Repository
http://www.roscripts.com/PHP_regular_expressions_examples-136.html
http://www.regular-expressions.info/email.html
http://www.codinghorror.com/blog/archives/001181.html [The Problem With URLs]
http://ha.ckers.org/xss.html
http://www.blog.activa.be/2008/10/30/ExtractingURLsNotPerfectButQuotgoodEnoughquot.aspx

REGEXP VALIDATION ONLINE:
------------------------
http://erik.eae.net/playground/regexp/regexp.html

*/

class Regexp {

	public static function re_email($choice=1) {

		switch($choice) {
			case 1:
				$regexp = "([a-z0-9_\.\-]+)@(([a-z0-9-]+\.)+([a-z]{2,4})+)";
				break;
			case 2:
				// RFC 2822 implementation if we omit the syntax using double quotes and square brackets.
				// It will still match 99.99% of all email addresses in actual use today.
				$regexp = "[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?";
				break;
			case 3:
			default:
				// good email-regexp found on some forum
				$regexp = "([a-z0-9_\.\-\+]+)@(([a-z0-9-]+\.)+([a-z0-9]{2,4})+)";
				break;
		}
		return $regexp;
	}

	public static function re_email_exact() {
		return self::re_pattern_from_start_to_end(self::re_email(), 'i');
	}

	// ---

	public static function re_url($choice=4) {

		#$protocols = 'https?|ftps?|gopher|telnet|nntp';
		#$hex = '%[0-9A-Fa-f]';
		#$allowed = '-;:@&=?/a-zA-Z0-9$_.+!*#\',()';
		#$allowed_bracket_less = _str_replace('()', '', $allowed);

		switch($choice) {

			case 0: // not tested well
				$regexp = ""
					. "(https?|ftps?|gopher|telnet|nntp://)?"
					. "(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?" //user@
					. "(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP- 199.194.52.184
					. "|" // allows either IP or domain
					. "([0-9a-z_!~*'()-]+\.)*" // tertiary domain(s)- www.
					. "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." // second level domain
					. "[a-z]{2,6})" // first level domain- .com or .museum
					. "(:[0-9]{1,4})?" // port number- :80
					. "((/?)|" // a slash isn't required if there is no file name
					. "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)";

			case 1: // validate all urls
				$regexp = "((((https?|ftps?|gopher|telnet|nntp)://)|)((%[0-9A-Fa-f]?)|[-()_.!#~*';/?:@&=+\$,A-Za-z0-9])+)([).!';/?:,])?";
				break;

			case 2: // validate all urls
				$regexp = "(((https?|ftps?|gopher|telnet|nntp)://)|)([a-zA-Z-:@.0-9]+)(/*[-;:@&=?/a-zA-Z0-9\$_.+!*#',()]|%[A-Fa-f0-9])*";
				break;

			case 3: // validate urls with "()" only if open bracket ")" has corresponding close bracket ")"
				$regexp = "(((https?|ftps?|gopher|telnet|nntp)://)|)([a-zA-Z-:@.0-9]+)(/*((\([-;:@&=?/a-zA-Z0-9\$_.+!*#',]*?\))|[-;:@&=?/a-zA-Z0-9\$_.+!*#',]|%[a-fA-F0-9])+)*(?<![,.;])";
				break;
			case 4:
			default:
				$regexp = "((((https?|ftps?|gopher|telnet|nntp)://(www)?)|(www))[-;:@&%=?/a-zA-ZА-Яа-я0-9\$_.+!*#',()]+)";
				break;
		}
		return $regexp;
	}

	public static function re_url_exact() {
		return self::re_pattern_from_start_to_end(self::re_url(), 'i');
	}

	public static function re_phone() {
		return '\+(?:[0-9]?){6,14}[0-9]';
	}

	public static function re_phone_exact() {
		return self::re_pattern_from_start_to_end(self::re_phone(), 'i');
	}


	public static function re_from_start_to_end($regexp) {
		$regexp = self::re_from_start($regexp);
		$regexp = self::re_to_end($regexp);
		return $regexp;
	}

	public static function re_from_start($regexp) {
		$regexp = sprintf('^%s', Cast::str($regexp));
		return $regexp;
	}

	public static function re_to_end($regexp) {
		$regexp = sprintf('%s$', Cast::str($regexp));
		return $regexp;
	}

	/**
	 * Incapsulate regexp pattern and add modifiers [i, s, m, x]
	 * @param string regexp
	 * @param mixed (char|array) $modifier
	 * @return string
	*/
	public static function re_pattern($regexp, $modifier=null) {

		$regexp = Cast::str($regexp);

		$regexp = _trim($regexp, '#');

		$regexp = _str_replace('#', '\#', $regexp);

		if($regexp) {

			$modifier_str = null;
			$modifier_arr = self::parse_modifier($modifier);
			if(!empty($modifier_arr)) {
				$modifier_str = implode('', $modifier_arr);
			}

			$regexp = sprintf('#%s#%s', $regexp, $modifier_str);
		}
		return $regexp;
	}

	public static function re_pattern_from_start_to_end($regexp, $modifier=null) {
		return self::re_pattern(self::re_from_start_to_end($regexp), $modifier);
	}

	/**
	 * @param mixed str|array
	 * @return array [i, s, m, x]
	 */
	public static function parse_modifier($modifier=null) {
		$modifier = Cast::strarr($modifier);
		foreach($modifier as &$m) $m = _strtolower($m);
		$modifiers = array('i', 's', 'm', 'x');
		$modifier_arr = array_intersect($modifiers, $modifier);
		return $modifier_arr;
	}

	/**
	 * Check string for regex matches
	 * @return array of whole matches
	 */
	public static function match_all($re_pattern, $subject) {
		$matches = array();
		_preg_match_all($re_pattern, $subject, $matches, PREG_PATTERN_ORDER);
		$matches = ifsetor($matches[0], array());
		return $matches;
	}

	/**
	 * Check if string matches regex
	 * @return first matched
	 */
	public static function match($re_pattern, $subject) {
		$matches = self::match_all($re_pattern, $subject);
		$matches = ifsetor($matches[0], false);
		return $matches;
	}

	/**
	 * Shortcut for validating email address
	 */
	public static function match_email($subject) {
		return self::match(self::re_email_exact(), $subject);
	}

	/**
	 * Shortcut for validating url address
	 */
	public static function match_url($subject) {
		return self::match(self::re_url_exact(), $subject);
	}

	/**
	 * Shortcut for validating phone
	 */
	public static function match_phone($subject) {
		return self::match(self::re_phone_exact(), $subject);
	}
}
