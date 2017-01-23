<?

class Url extends Rewrite {

	public static function encode($url) {
		return urlencode($url);
	}

	public static function decode($url) {
		return urldecode($url);
	}

	/**
	 * Return current requested url address
	 */
	public static function currurl($remove_get_params=array()) {
		$url = null;
		if(Predicate::isWebCall()) {
			$PHP_SELF = _trim(_str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['PHP_SELF']), '?');
			$url = 'http://'.$_SERVER['HTTP_HOST'].$PHP_SELF;
			if($_SERVER['QUERY_STRING']) {
				$QUERY_STRING = urldecode($_SERVER['QUERY_STRING']);
				_parse_str($QUERY_STRING, $p_str_arr);
				$remove_get_params = Cast::strarr($remove_get_params);
				foreach($remove_get_params as $get) {
					if(array_key_exists($get, $p_str_arr)) {
						unset($p_str_arr[$get]);
					}
				}
				$QUERY_STRING = http_build_query($p_str_arr);
				if($QUERY_STRING) {
					$url .= '?'.$QUERY_STRING;
				}
			}
		}
		return $url;
	}

	/**
	 * Redirecting and exit
	 * Use PHP header location or metaredirect if headers are sent
	 *
	 * @param string $url
	 * @param string $message to out
	 * @param int $timeout before redirect [secs]
	 * @param int $http_response_code
	 */
	public static function redirect($url, $message=null, $timeout=0, $http_code=302) {

		$url = Cast::str($url);
		$message = Cast::str($message);
		$timeout = Cast::unsignint($timeout);
		$http_code = Cast::unsignint($http_code);

		$__debug =& __debug();

		if($__debug->isMessagingEnabled() && $timeout==0) {
			$timeout = 5;
		}

		if(headers_sent($filename, $linenum) || $timeout>0) {
			self::metaredirect($url, $message, $timeout);
		}
		else {
			Http::location($url, $http_code);
		}

		exit();
	}

	/**
	 * Redirecting and exit
	 * Use JavaScript & META tag 'http-equiv="refresh"'
	 *
	 * @param string $url
	 * @param string $message to out
	 * @param int $timeout before redirect [secs]
	 */
	public static function metaredirect($url, $message=null, $timeout=0) {

		$url = Cast::str($url);
		$message = Cast::str($message);
		$timeout = Cast::unsignint($timeout);

		$html = '<html>';
		$html .= '<head><title>Redirecting...</title>';
		$html .= '<noscript><meta http-equiv="refresh" content="'.$timeout.'; url='.$url.'"/></noscript>';
		$html .= '<script type="text/javascript">function redirect(){setTimeout("window.location.replace(\'' . _addslashes($url) . '\')", ' . $timeout * 1000 . ');}</script>';
		$html .= '</head>';
		$html .= '<body onload="redirect()">'.($message ? ($message.'<br />') : '').'Redirecting... <a href="'.$url.'" title="">'._htmlspecialchars($url).'</a></body>';
		$html .= '</html>';

		print $html;

		exit();
	}

	/**
	 * Replace double url slashaes to 1 slash, excluding cases http://
	 * @param string $url
	 * @return string $url
	 */
	public static function fix($url) {

		$url = Cast::str($url);

		$replaceMap = array(
			'://' => uniqid(), // like http://
		);

		foreach($replaceMap as $what=>$with) {
			$url = _str_ireplace($what, $with, $url);
		}

		if(_strstr($url, '//')) {
			$url = _preg_replace('/(\/){2,}/', '/', $url);
		}

		foreach($replaceMap as $what=>$with) {
			$url = _str_ireplace($with, $what, $url);
		}

		$url = _str_replace('\\', '/', $url);

		return $url;
	}

	public static function i_web2local($path) {
		$local = _str_replace(I_URL, I_PATH, $path);
		// remove get-params
		if(strcmp($path, $local)!==0 && ($str_pos = _strpos($local, '?')) !== false) {
			$local = _substr($local, 0, $str_pos);
		}
		return $local;
	}

	public static function i_local2web($path) {
		$web = _str_replace(I_PATH, I_URL, $path);
		return $web;
	}

	public static function s_web2local($path) {
		$local = _str_replace(S_URL, S_PATH, $path);
		// remove get-params
		if(strcmp($path, $local)!==0 && ($str_pos = _strpos($local, '?')) !== false) {
			$local = _substr($local, 0, $str_pos);
		}
		return $local;
	}

	public static function s_local2web($path) {
		$web = _str_replace(S_PATH, S_URL, $path);
		return $web;
	}
}
