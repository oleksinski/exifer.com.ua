<?

class Cookie {

	/**
	 * @param string $name
	 * @param string $value
	 * @param integer $expires
	 * @param string $path
	 * @param string $domain
	 * @param bool $secure
	 */
	public function set($name, $value, $expires=null, $path=null, $domain=null, $secure=null) {

		if(Predicate::isWebCall()) {
			if(headers_sent()) {
				$script = array();
				$cookie = array();
				$cookd = sprintf('cookd_%u', rand(1, 100000));
				$script[] = '<script type="text/javascript">'.($expires?'var '.$cookd.'=new Date('.$expires.'*1000);':'');
				$cookie[] = sprintf('document.cookie="%s=%s', $name, $value);
				if($expires) $cookie[] = sprintf('expires="+%s.toGMTString()+"', $cookd);
				if($domain) $cookie[] = sprintf('domain=%s', $domain);
				if($path) $cookie[] = sprintf('path=%s', $path);
				if($secure) $cookie[] = 'secure';
				$script[] = implode('; ', $cookie);
				$script[] = '";</script>';
				print implode('', $script);

				$info = array();
				if($name) $info['name']=$name;
				if($value) $info['value']=$value;
				if($expires) $info['expires']=$expires;
				if($domain) $info['domain']=$domain;
				if($path) $info['path']=$path;
				if($secure) $info['secure']=$secure;
				_e('cookie: '.urldecode(http_build_query($info)));
			}
			else {
				setcookie($name, $value, $expires, $path, $domain, $secure);
			}
			return true;
		}
		return false;
	}

	public function get($name) {
		return ifsetor($_COOKIE[$name], null);
	}

	public function issetcookie($name) {
		return isset($_COOKIE[$name]);
	}

	public function domain() {
		$domain = Predicate::isWebCall() ? ('.'.$_SERVER['HTTP_HOST']) : URL_DOT_DOMAIN;
		return $domain;
	}

}