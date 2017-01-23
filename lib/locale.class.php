<?


class Locale extends Singleton {

	const __DEF_ID = 1;
	const __DEF_LANG = 'ru';
	const __DEF_LOCALE = 'ru_RU';
	const __DEF_CODESET = 'UTF-8';

	protected $id;
	protected $lang;
	protected $locale;
	protected $codeset;
	protected $locale_codeset;
	protected $localeStruct;


	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}

	protected function __construct() {

		$this->localeStruct = array(
			// id => array(lang, locale)
			1 => array('ru', 'ru_RU'),
			2 => array('ua', 'uk_UA'),
			3 => array('en', 'en_US'),
		);
		//$this->id = self::__DEF_ID;
		//$this->lang = self::__DEF_LANG;
		//$this->locale = self::__DEF_LOCALE;
		$this->codeset = self::__DEF_CODESET;

		$this->initById(self::__DEF_ID);
	}

	public function getId() {
		return $this->id;
	}

	public function getLang() {
		return $this->lang;
	}

	public function getLocale() {
		return $this->locale;
	}

	public function getCodeset() {
		return $this->codeset;
	}

	public function getLocaleCodeset() {
		return $this->locale_codeset;
	}

	public function getLocaleStruct() {
		return $this->localeStruct;
	}

	public function getIdList() {
		return array_keys($this->localeStruct);
	}

	public function getLangList() {
		$__langList = array();
		foreach($this->localeStruct as $__id=>$__info) {
			array_push($__langList, $__info[0]);
		}
		return $__langList;
	}

	public function getLocaleList() {
		$__localeList = array();
		foreach($this->localeStruct as $__id=>$__info) {
			array_push($__localeList, $__info[1]);
		}
		return $__localeList;
	}

	public function getIdByLang($lang) {
		$id = null;
		foreach($this->localeStruct as $__id=>$__info) {
			if($__info[0]==$lang) {
				$id = $__id;
				break;
			}
		}
		return $id;
	}

	public function getIdByLocale($locale) {
		$id = null;
		foreach($this->localeStruct as $__id=>$__info) {
			if($__info[1]==$locale) {
				$id = $__id;
				break;
			}
		}
		return $id;
	}

	public function getLangById($id) {
		$lang = null;
		if(array_key_exists($id, $this->localeStruct)) {
			$__info = $this->localeStruct[$id];
			$lang = $__info[0];
		}
		return $lang;
	}

	public function getLangByLocale($locale) {
		$lang = null;
		foreach($this->localeStruct as $__id=>$__info) {
			if($__info[1]==$locale) {
				$lang = $__info[0];
				break;
			}
		}
		return $lang;
	}

	public function getLocaleById($id) {
		$locale = null;
		if(array_key_exists($id, $this->localeStruct)) {
			$__info = $this->localeStruct[$id];
			$locale = $__info[1];
		}
		return $locale;
	}

	public function getLocaleByLang($lang) {
		$locale = null;
		foreach($this->localeStruct as $__id=>$__info) {
			if($__info[0]==$lang) {
				$locale = $__info[1];
				break;
			}
		}
		return $locale;
	}

	public function initById($id=self::__DEF_ID) {
		$this->setLocale($id);
	}

	public function initByLang($lang=self::__DEF_LANG) {
		$id = $this->getIdByLang($lang);
		$this->setLocale($id);
	}

	public function initByLocale($locale=self::__DEF_LOCALE) {
		$id = $this->getIdByLocale($locale);
		$this->setLocale($id);
	}

	private function setLocale($id) {

		$lang = $this->getLangById($id);
		$locale = $this->getLocaleById($id);

		// input data ok
		$input_ok = $id && $lang && $locale;

		// if not init yet
		$update_ifnull = $input_ok;
		$update_ifnull = $update_ifnull && !isset($this->id);
		$update_ifnull = $update_ifnull && !isset($this->lang);
		$update_ifnull = $update_ifnull && !isset($this->locale);

		// if change init
		$update_ifchange = $input_ok && !$update_ifnull;
		$update_ifchange = $update_ifchange && $this->id!==$id;
		$update_ifchange = $update_ifchange && $this->lang!==$lang;
		$update_ifchange = $update_ifchange && $this->locale!==$locale;

		if($input_ok && ($update_ifnull || $update_ifchange)) {

			$this->id = $id;
			$this->lang = $lang;
			$this->locale = $locale;
			$this->locale_codeset = sprintf('%s.%s', $this->locale, $this->codeset);

			// set text domain for gettext() || _()
			$setlocale_result = setlocale(LC_ALL, $this->locale_codeset);
			putenv('LC_MESSAGES='.$this->locale);
			if(function_exists('bindtextdomain')) {
				bindtextdomain($this->lang, LOCALE_PATH);
			}
			if(function_exists('bind_textdomain_codeset')) {
				bind_textdomain_codeset($this->lang, $this->codeset);
			}
			if(function_exists('textdomain')) {
				textdomain($this->lang);
			}

			//_e(sprintf('Locale: %s => [%s]', $this->locale_codeset, $setlocale_result));
		}
	}

	public function isUA() {
		return ($this->lang==='ua');
	}

	public function isRU() {
		return ($this->lang==='ru');
	}

	public function isEN() {
		return ($this->lang==='en');
	}


	/**
	 * Parse input html and extend url links with selected language
	 * @param string $html
	 * @return string
	 */
	public function renderHtml($html) {

		$matches = array();

		$uri_array = array(); // array with parsed results

		// services list which links should be parsed
		$url_allowed_base = array('/', URL_PROJECT);
		$url_restricted_base = array();
		foreach($url_allowed_base as $url) {
			$url_restricted_base[] = $url.'/robots.txt';
			$url_restricted_base[] = $url.'/crossdomain.xml';
		}

		$url_allowed_base = array_unique($url_allowed_base);
		//_e($url_allowed_base);
		$url_restricted_base = array_unique($url_restricted_base);
		//_e($url_restricted_base);

		// Данный шаблон выбирает всё, что находится между (href=),
		// и символами пробела или закрытия тега (>) внутри тега (<a).
		$patterns = array();
		$pattern = "/(href|action)=(.*?)[\s|>]/si";
		array_push($patterns, $pattern);

		//$match_pattern_all = array();
		//$match_pattern_entitity = array();
		$match_pattern_url = array();

		foreach($patterns as $pattern) {
			_preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);
			//_e($matches);
			//$match_pattern_all = array_merge($match_pattern_all, $matches[0]); // full pattern matches
			//$match_pattern_entitity = array_merge($match_pattern_entitity, $matches[1]); // html entitity attrib
			$match_pattern_url = array_merge($match_pattern_url, $matches[2]); // = pattern mask matches
		}

		$match_pattern_url = array_unique($match_pattern_url);

		foreach($match_pattern_url as &$url_tmp) {
			$url_tmp = _trim($url_tmp, ' \'"');
			if($url_tmp==='') {
				unset($url_tmp);
			}
		}

		$match_pattern_url = array_unique($match_pattern_url);

		//array_multisort($match_pattern_url, SORT_STRING, SORT_DESC);
		usort($match_pattern_url, Util::CreateFunction('$a, $b', 'return _strlen($a)<_strlen($b);'));
		$pm_count = count($match_pattern_url);

		//_e($match_pattern_all);
		//_e($match_pattern_url);

		$replace_map = array();

		for($i=0; $i<$pm_count; $i++) {

			$ok = false;

			$url_i = $match_pattern_url[$i];

			$__rewrite = new Rewrite($url_i);

			//_e($__rewrite);

			$url_o = $__rewrite->getUrl();

			foreach($url_allowed_base as $allowed_base) {

				if(0===_strpos($url_o, $allowed_base, 0)) {

					//_e($url_o . ' == ' . $allowed_base);

					$ok = true;

					foreach($url_restricted_base as $restricted_base) {

						if(0===_strpos($url_o, $restricted_base, 0)) {

							$ok = false;

							break;
						}
					}

					if($ok) {

						$__langFrom = $__rewrite->getParsedLang();
						$__langTo = $this->lang;

						//_e($url_o . ' == '  .$__langFrom . ' === ' . $__langTo);

						if($__langTo!=self::__DEF_LANG) {

							if(in_array($__langFrom, $this->getLangList()) && $__langFrom!=$__langTo) {
								$__langTo = $__langFrom;
							}

							$__pathArr = $__rewrite->getPathArr();

							array_unshift($__pathArr, $__langTo);

							$__rewrite->modifyPathArr($__pathArr);

							$url_o = $__rewrite->getUrl();
						}

						$uniqid = uniqid();

						$replace_map[$uniqid] = $url_o;

						$html = _str_replace($url_i, $uniqid, $html);

						break;
					}
				}
			}
		}

		if(!empty($replace_map)) {
			foreach($replace_map as $uniqid=>$url_o) {
				$html = _str_replace($uniqid, $url_o, $html);
			}
		}

		return $html;
	}

}
