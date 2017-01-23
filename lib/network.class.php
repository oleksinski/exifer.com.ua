<?

class Network {

	/**
	 * Return HTTP clien IP address
	 *
	 * @param bool $toInt use ip2long()
	 * @return mixed
	 */
	public static function clientIp($toInt=false) {
		$ip = $toInt ? 0 : null;
		if(isset($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
			if($toInt) {
				$ip = Cast::iptolong($ip);
			}
		}
		return $ip;
	}


	/**
	 * Return HTTP clien forwarder IP address
	 *
	 * @param bool $toInt use ip2long()
	 * @return mixed
	 */
	public static function clientFwrd($toInt=false) {
		$ip = $toInt ? 0 : null;
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = _trim(end(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
			if($toInt) {
				$ip = Cast::iptolong($ip);
			}
		}
		return $ip;
	}

	/**
	 * http://en.wikipedia.org/wiki/Classless_Inter-Domain_Routing
	 * http://ru.wikipedia.org/wiki/Бесклассовая_адресация
	 * http://admin.vlady.ru/cidr.htm
	 *
	 * @param str $cidr (e.g. 193.239.68.0/24, max="/32")
	 * @param str $ip (e.g. 193.239.68.0)
	 * return bool
	 */
	public static function matchCIDR($cidr, $ip) {

		list($net, $cidr_mask) = explode('/', $cidr);

		$iplong_mask = 0xffffffff << (32 - $cidr_mask);
		$iplong_net = ip2long($net);
		$iplong_ip = ip2long($ip);

		//_e('$iplong_mask='.$iplong_mask);
		//_e('$iplong_net='.$iplong_net);
		//_e('$iplong_ip='.$iplong_ip);

		$net_mask = $iplong_net & $iplong_mask;
		$ip_mask = $iplong_ip & $iplong_mask;

		//_e('$net_mask='.$net_mask);
		//_e('$ip_mask='.$ip_mask);

		return ($net_mask == $ip_mask);
	}

	public static function clientHttpSignature() {
		$http_id_arr = array(
			@$_SERVER['REMOTE_ADDR'],
			@$_SERVER['HTTP_X_FORWARDED_FOR'],
			@$_SERVER['HTTP_USER_AGENT'],
		);
		$http_id_str = implode('_', $http_id_arr);
		return $http_id_str;
	}

}