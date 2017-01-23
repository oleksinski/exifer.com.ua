<?

class Json extends Services_JSON {

	private $utf8 = 'UTF-8';
	private $defaultCodeset;

	public function __construct($use=0) {
		parent::__construct($use);

		$__locale =& __locale();
		$this->defaultCodeset = $__locale->getCodeset();
	}

	public function json_iconv($var, $fromEncoding, $toEncoding) {

		if(is_array($var)) {
			$new = array();
			foreach ($var as $k=>$v) {
				$new[$this->json_iconv($k, $fromEncoding, $toEncoding)] = $this->json_iconv($v, $fromEncoding, $toEncoding);
			}
			$var = $new;
		}
		elseif(is_object($var)) {
			$vars = get_class_vars(get_class($var));
			foreach ($vars as $m => $v) {
				$var->$m = $this->json_iconv($v, $fromEncoding, $toEncoding);
			}
		}
		elseif(is_string($var)) {
			$isVarUtf = 0 ? Predicate::isUtfEncoded($var) : false;
			if(0 && $fromEncoding==$this->utf8 && !$isVarUtf) {
				// var is not in utf
			}
			elseif(0 && $fromEncoding==$this->defaultCodeset && $isVarUtf) {
				// var already in utf
			}
			elseif(1) {
				$var = MyIconv::encode($var, $fromEncoding, $toEncoding);
			}
		}

		return $var;
	}

	public function encode_cyr_php($php) {
		$fromEncoding = $this->defaultCodeset;
		$toEncoding = $this->utf8;
		$php = $this->json_iconv($php, $fromEncoding, $toEncoding);
		return $php;
	}

	public function decode_cyr_php($php) {
		$fromEncoding = $this->utf8;
		$toEncoding = $this->defaultCodeset;
		$php = $this->json_iconv($php, $fromEncoding, $toEncoding);
		return $php;
	}
}
