<?php

class Http {

	/*
	 * Get Http Code-Name map
	 * @param void
	 * @return hash array $list
	 */
	public static function getCodeList() {
		$list = array(
			// 1xx Informational
			100 => 'Continue',
			101 => 'Switching Protocols',
			102 => 'Processing',
			// 2xx Success
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			207 => 'Multi-Status',
			// 3xx Redirection
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => 'Switch Proxy',
			307 => 'Temporary Redirect',
			// 4xx Client Error
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'I\'m a teapot',
			422 => 'Unprocessable Entity',
			423 => 'Locked',
			424 => 'Failed Dependency',
			425 => 'Unordered Collection',
			426 => 'Upgrade Required',
			444 => 'No Response',
			449 => 'Retry With',
			450 => 'Blocked by Windows Parental Controls',
			499 => 'Client Closed Request',
			// 5xx Server Error
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported',
			506 => 'Variant Also Negotiates',
			507 => 'Insufficient Storage',
			509 => 'Bandwidth Limit Exceeded',
			510 => 'Not Extended',
		);
		return $list;
	}

	/**
	 * Get Http code name
	 * @param int $codeNumber
	 * @return string $codeName
	 */
	public static function getCodeName($codeNumber) {
		//$codeNumber = Cast::int($codeNumber);
		$codeList = self::getCodeList();
		$codeName = ifsetor($codeList[$codeNumber], null);
		return $codeName;
	}

	/**
	 * Print http header
	 * @param int $code
	 * @return void
	 */
	public static function header($codeNumber) {
		$codeName = self::getCodeName($codeNumber);
		if($codeName) {
			$header = sprintf('HTTP/1.0 %u %s', $codeNumber, $codeName);
			if(!headers_sent()) {
				header($header);
			}
			else {
				_e($header);
			}
		}
	}

	/*
	 * @param string $url
	 * @param int $code [optional]
	 * @return void
	 */
	public static function location($url, $code) {
		if(!headers_sent()) {
			//self::header($code);
			header('Location: ' . $url, true, $code);
		}
		else {
			_e('Location: '.$url);
		}
		exit();
	}
}