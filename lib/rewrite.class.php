<?

class Rewrite {

	protected $__url;
	protected $__uri;
	protected $__scheme;
	protected $__protocol;
	protected $__host;
	protected $__port;
	protected $__path;
	protected $__pathArr;
	protected $__query;
	protected $__queryArr;
	protected $__hash;

	protected $__pathArrLang;
	protected $__lang;

	/**
	 * @constructor
	 * @param string $URL [optional instead of default $_SERVER['REQUEST_URI'] | $_SERVER['PHP_SELF']
	 */
	public function __construct($URL=null, $setLocale=false) {

		if((bool)$URL===false) {
			if(isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI'])) {
				$URL = sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']);
			}
		}

		if(is_null($URL) || $URL==='') {
			$URL = '/';
		}
		$this->__url = $URL;

		$this->__url = Url::fix($this->__url);

		$parse_url = parse_url($this->__url);

		//_e($parse_url);

		// __scheme
		$this->__scheme = _strtolower(ifsetor($parse_url['scheme'], ''));

		// __protocol
		$this->__protocol = '';
		if(in_array($this->__scheme, array('http', 'https', 'ftp'))) {
			$this->__protocol = $this->__scheme . '://';
		}

		// __host
		$this->__host = ifsetor($parse_url['host'], '');

		// __port
		$this->__port = ifsetor($parse_url['port'], '');

		// __path
		$this->__path = ifsetor($parse_url['path'], '/');
		if(0!==strcmp($this->__path, '/')) {
			//$last_char = _substr($this->__path, -1);
			//$this->__path = _rtrim($this->__path, '/');
		}

		// __query
		$this->__query = ifsetor($parse_url['query'], '');

		// __queryArr
		_parse_str($this->__query, $__queryArr);
		$this->__queryArr = array();
		foreach($__queryArr as $key=>$value) {
			$key = _stripslashes($key);
			$value = _stripslashes($value);
			$this->__queryArr[$key] = $value;
		}

		// __hash
		$this->__hash = ifsetor($parse_url['fragment'], '');

		// __pathArr
		$this->__pathArr = explode('/', $this->__path);
		$this->__pathArr = $this->filterUrlArray($this->__pathArr);

		// __uri
		$this->__uri = $this->__protocol . $this->__host . ($this->__port ? ':'.$this->__port : '') . $this->__path;

		$this->__pathArrLang = array();

		$this->parseLang($setLocale);
	}

	public function parseLang($setLocale=false) {

		if(count($this->__pathArr) && 0===_strpos($this->__path, '/', 0)) {

			$__locale =& __locale();
			$__langList = $__locale->getLangList();

			while($__lang = array_shift($this->__pathArr)) {

				if(in_array($__lang, $__langList)) {

					array_push($this->__pathArrLang, $__lang);

					$this->__path = '/' . implode('/', $this->__pathArr);

					$this->rebuildUrl();
				}
				else {
					array_unshift($this->__pathArr, $__lang);
					break;
				}
			}
		}

		$this->__lang = reset($this->__pathArrLang);

		if($this->__lang && $setLocale===true) {
			$__locale->initByLang($this->__lang);
		}
	}

	public function modifyPath($__path) {
		$parse_url = parse_url($__path);
		$__path = ifsetor($parse_url['path'], '/');
		$__pathArr = explode('/', $__path);
		$this->modifyPathArr($__pathArr);
	}

	public function modifyPathArr($__pathArr) {

		$this->__pathArr = Cast::strarr($__pathArr);

		$this->__pathArr = $this->filterUrlArray($this->__pathArr);

		$__path = implode('/', $this->__pathArr);
		if(0===_strpos($this->__path, '/', 0)) {
			$__path = '/'.$__path;
		}
		$this->__path = $__path;

		$this->rebuildUrl();
	}

	public function modifyQuery($__query) {
		$__query = Cast::str($__query);
		_parse_str($__query, $__queryArr);
		$__queryArrNew = array();
		foreach($__queryArr as $key=>$value) {
			$key = _stripslashes($key);
			$value = _stripslashes($value);
			$__queryArrNew[$key] = $value;
		}
		$this->modifyQueryArr($__queryArrNew);
	}

	public function modifyQueryArr($__queryArr) {
		if(is_array($__queryArr)) {
			$this->__queryArr = $__queryArr;
			$this->__query = http_build_query($__queryArr);
			$this->rebuildUrl();
		}
	}

	public function rebuildUrl() {

		$this->__url = $this->__uri = $this->__protocol . $this->__host;

		if($this->__url && $this->__port) {
			$this->__url .= ':'.$this->__port;
		}

		$this->__url .= $this->__path;

		$this->__uri = $this->__url;

		if($this->__query) {
			$this->__url .= '?'.$this->__query;
		}

		if($this->__hash) {
			$this->__url .= '#'.$this->__hash;
		}

		return $this->__url;
	}

	public function getUrl() { // http://site.net/url/param?param1=value1&param2=value2#hash
		return $this->__url;
	}

	public function getUri() { // http://site.net/url/param
		return $this->__uri;
	}

	public function getHost() { // site.net
		return $this->__host;
	}

	public function getScheme() { // http
		return $this->__scheme;
	}

	public function getProtocol() { // http://
		return $this->__protocol;
	}

	public function getPort() { // 80
		return $this->__port;
	}

	public function getPath() { // /url/param
		return $this->__path;
	}

	public function getPathArr() { // Array('url', 'param')
		return $this->__pathArr;
	}

	public function getQuery() { // param1=value1&param2=value2
		return $this->__query;
	}

	public function getQueryArr() { // Array('param1'=>'value1', 'param2'=>'value2')
		return $this->__queryArr;
	}

	public function getHash() { // [#]hash
		return $this->__hash;
	}

	public function getPathLangArr() {
		return $this->__pathArrLang;
	}

	public function getParsedLang() {
		return $this->__lang;
	}

	public function isUrlLangModified() { // is Url modified by lang parser
		return is_array($this->__pathArrLang) && count($this->__pathArrLang);
	}

	public function get($index=0) { // url
		return ifsetor($this->__pathArr[$index], null);
	}

	public function getInt($index=0) { // (int)'url'
		$val = ifsetor($this->__pathArr[$index], null);
		if(!is_null($val)) {
			$val = Cast::int($val);
		}
		return $val;
	}

	public function getAbsInt($index=0) {
		return Cast::unsignint($this->getInt($index));
	}

	private function filterUrlArray($array) {

		$array = Cast::strarr($array);

		foreach($array as &$el) {
			$el = _trim($el, ' /');
			if($el==='') unset($el);
		}

		if(array_search("", $array)) {
			$array = array_filter($array, Util::CreateFunction('$a', 'return $a!=="";'));
		}

		return $array;
	}

}
