<?

class ControlModel {

	protected static $smarty;

	public $CN; // controller name
	public $CA; // controller action

	public $tplFetchTimeFloat; // float(summary fetch time)
	public $tplFetchTimeArr; // array(tpl => fetch time)

	protected $htmlMetaContent=array(); // array (name=>content)
	protected $htmlHeaderCSS=array(); // array (file, file, file)
	protected $htmlHeaderJS=array(); // array (file, file, file)
	protected $htmlHeaderRSS=array(); // array (link=>name)

	protected $HtmlRobotsDisallow;

	protected $_prefix; // boolean flag: method _prefix() executed
	protected $_postfix; // boolean flag: method _postfix() executed

	protected $__rewrite; // url-rewrite object

	public function __construct() {
		$this->global_init();
	}

	public function __get($what) {
		return ifsetor($this->$what, null);
	}

	public static function &getSmarty() {

		if(is_null(self::$smarty)){

			$smarty = new Smarty();

			$smarty->caching			= 0; // 0-off, 1-on, 2-tmp_session_cache
			$smarty->cache_dir			= SMARTY_STATIC_PATH.'template_cache/';
			$smarty->cache_lifetime		= 3600; // 0, -1

			$smarty->compile_check		= true;
			$smarty->compile_id			= URL_PROJECT;
			$smarty->compile_dir		= SMARTY_STATIC_PATH.'template_compile/';
			$smarty->config_dir			= SMARTY_STATIC_PATH.'template_config/';
			$smarty->config_overwrite	= false;
			$smarty->debugging			= false;
			$smarty->debug_tpl			= SMARTY_LIB.'debug.tpl';
			$smarty->force_compile		= false;
			$smarty->plugins_dir[]		= SMARTY_USR_PLUGIN_PATH;
			$smarty->template_dir		= TPL_PATH;
			$smarty->auto_literal		= false;
			$smarty->error_reporting	= Predicate::server_pro() ? (E_ALL & ~E_NOTICE) : E_ALL;

			// --= register output smarty filter =-- //

			//[rewrite template urls]
			//$__locale =& __locale();
			//if($__locale->getLang()!=Locale::__DEF_LANG) {
			//	$smarty->loadFilter('output','addurllang');
			//}

			if(self::is_compress_html()) {
				$smarty->loadFilter('output','compresshtml');
			}

			self::$smarty =& $smarty;
		}

		return self::$smarty;
	}


	final public function setupSmarty($property, $value) {
		$smarty =& self::getSmarty();
		$smarty->$property = $value;
	}

	final public function assign($name, $value=null) {
		$smarty =& self::getSmarty();
		$smarty->assign($name, $value);
	}

	final public function render($tpl, $vars=array(), $cache_lifetime=null) {

		$__stopwatch = new StopWatch();

		$smarty =& self::getSmarty();

		$this->assign($vars);

		$this->global_assign();

		$cache_lifetime = Cast::int($cache_lifetime);

		$smarty_caching = $smarty->caching;
		$smarty_cache_lifetime = $smarty->cache_lifetime;

		if($cache_lifetime > 0) { // change smarty caching setup
			$smarty->caching = 2; // session template cache only
			$smarty->cache_lifetime = $cache_lifetime;
		}

		$fetched = $smarty->fetch($tpl);

		if($cache_lifetime > 0) { // restore smarty caching setup
			$smarty->caching = $smarty_caching;
			$smarty->cache_lifetime = $smarty_cache_lifetime;
		}

		$fetch_time = $__stopwatch->getFormat(4);
		_e(sprintf('# Template %s render time: %s', $tpl, $fetch_time));
		$this->tplFetchTimeFloat += Cast::float($fetch_time);
		$this->tplFetchTimeArr[$tpl] = $fetch_time;

		return $fetched;
	}

	final public function layout($tpl, $vars=array(), $cache_lifetime=null) {

		$tpl_fetched = $this->render($tpl, $vars, $cache_lifetime);

		$doctypes =& self::getDoctypeList();

		$vars = array(
			'HTML_DOCTYPE_LIST' => $doctypes,
			'HTML_META' => $this->htmlMetaContent,
			'CSS' => $this->htmlHeaderCSS,
			'JAVASCRIPT' => $this->htmlHeaderJS,
			'RSS' => $this->htmlHeaderRSS,
			'CONTROL_TPL' => $tpl_fetched,
		);

		$tpl_fetched = $this->render('layout/layout.tpl', $vars);

		return $tpl_fetched;
	}

	final public function _prefix() {

		// not in _postfix
		$user_online = new UserOnline();
		$user_online->add();

		$this->_prefix = true;

		$this->prefix();
	}

	final public function _postfix() {

		$this->_postfix = true;

		$this->postfix();
	}

	protected function prefix() {} // you may overload it in derived class

	protected function postfix() {} // you may overload it in derived class

	public function page404() {
		if(!$this->_prefix) $this->_prefix();
		if(!$this->_postfix) $this->_postfix();
		Http::header(404);
		return $this->layout('layout/404.tpl');
	}

	protected function global_init($reinit=false) {

		static $inited = null;

		if(!$inited || $reinit) {

			$inited = true;

			global $__rewrite;
			// $this->__rewrite = new Rewrite();
			$this->__rewrite =& $__rewrite;

			$this->include_css('style.css');
			$this->include_js('jquery.js');
			$this->include_js('main.js');

			if(User::isOnlineAdmin()) {
				$this->HtmlRobotsDisallow = true;
			}

			$this->include_rss(array(UrlModel::rss_photo()=>SeoModel::rss_photo()));
			$this->include_rss(array(UrlModel::rss_user()=>SeoModel::rss_user()));
			$this->include_rss(array(UrlModel::rss_comment()=>SeoModel::rss_comment()));
		}
	}

	protected function global_assign($reassign=false) {

		static $assigned = null;

		if(!$assigned || $reassign) {

			$assigned = true;

			$this->assign('URL_DOMAIN', URL_DOMAIN);
			$this->assign('URL_PROJECT', URL_PROJECT);
			$this->assign('URL_NAME', URL_NAME);
			$this->assign('I_URL', I_URL);
			$this->assign('S_URL', S_URL);
			$this->assign('TPL_PATH', TPL_PATH);
			$this->assign('STATIC_PATH', STATIC_PATH);
			$this->assign('PROJECT_NAME', PROJECT_NAME);
			$this->assign('SUPPORT_EMAIL', SUPPORT_EMAIL);

			$this->assign('CN', $this->CN);
			$this->assign('CA', $this->CA);

			$ENV = array(
				'PRO' => (int)Predicate::server_pro(),
				//'LAB' => (int)Predicate::server_lab(),
				'DEV' => (int)Predicate::server_dev(),
			);
			$this->assign('ENV', $ENV);

			$this->setHtmlMetaHttpEquivContent('content-type', 'text/html; charset=utf-8');
			$this->setHtmlMetaHttpEquivContent('Pragma', 'no-cache');
			$this->setHtmlMetaHttpEquivContent('Cache-Control', 'no-cache');

			$htmlMetaTitle = $this->getHtmlMetaTitle();
			if(!$htmlMetaTitle) {
				$htmlMetaTitle = $this->setHtmlMetaTitle(SeoModel::index(SeoModel::TITLE), false);
			}
			$this->setHtmlMetaNameContent('title', $htmlMetaTitle);
			//$this->setHtmlMetaHttpEquivContent('title', $htmlMetaTitle);

			$htmlMetaDescription = $this->getHtmlMetaDescription();
			if(!$htmlMetaDescription) {
				$htmlMetaDescription = $this->setHtmlMetaDescription(SeoModel::index(SeoModel::DESCRIPTION), false);
			}
			$this->setHtmlMetaNameContent('description', $htmlMetaDescription);
			//$this->setHtmlMetaHttpEquivContent('description', $htmlMetaDescription);

			$htmlMetaKeywords = $this->getHtmlMetaKeywords();
			if(!$htmlMetaKeywords) {
				$htmlMetaKeywords = $this->setHtmlMetaKeywords(SeoModel::index(SeoModel::KEYWORDS), false);
			}
			$this->setHtmlMetaNameContent('keywords', $htmlMetaKeywords);
			//$this->setHtmlMetaHttpEquivContent('keywords', $htmlMetaKeywords);

			$this->setHtmlMetaNameContent('google-site-verification', 'AlY5uloLrtRA9i3qxkc3uDaoDO1RssYYPKJUhtOJRyU'); // alex.karpinskiy@gmail.com
			$this->setHtmlMetaNameContent('google-site-verification', 'eb9c4nSyH5s3BbuRNqvDTJATGex2Ho4Ls4zW7Xzw0ks'); // okarpynskyi@gmail.com
			$this->setHtmlMetaNameContent('alexaVerifyID', 'MfAAwso95Q76bLv81BkG3FcSuLE');
			$this->setHtmlMetaNameContent('yandex-verification', '7fb6139cac30fe8a');
			$this->setHtmlMetaNameContent('msvalidate.01', '9025F7C7908D9A6FFC841E0A7EC9057B');

			if(!$this->getHtmlMetaNameContent('robots')) {
				$this->setHtmlMetaNameContent('robots', 'all');
			}
			if(!$this->getHtmlMetaNameContent('revisit')) {
				$this->setHtmlMetaNameContent('revisit', '1');
			}
			$this->assign('HtmlRobotsDisallow', $this->HtmlRobotsDisallow);

			$this->assign('USER', User::getOnlineUser());
		}
	}

	private function getHtmlMetaCustomContent($type, $name) {
		return ifsetor($this->htmlMetaContent[$type][$name], null);
	}
	private function setHtmlMetaCustomContent($type, $name, $value=null) {
		if(!is_null($value)) {
			$this->htmlMetaContent[$type][$name] = $value;
		}
		else {
			unset($this->htmlMetaContent[$type][$name]);
		}
	}

	final public function getHtmlMetaNameContent($name) {
		return $this->getHtmlMetaCustomContent('META_NAME_CONTENT', $name);
	}
	final public function setHtmlMetaNameContent($name, $value=null) {
		$this->setHtmlMetaCustomContent('META_NAME_CONTENT', $name, $value);
	}

	final public function getHtmlMetaPropertyContent($name) {
		return $this->getHtmlMetaCustomContent('META_PROPERTY_CONTENT', $name);
	}
	final public function setHtmlMetaPropertyContent($name, $value=null) {
		$this->setHtmlMetaCustomContent('META_PROPERTY_CONTENT', $name, $value);
	}

	final public function getHtmlMetaHttpEquivContent($name) {
		return $this->getHtmlMetaCustomContent('META_HTTP_EQUIV_CONTENT', $name);
	}
	final public function setHtmlMetaHttpEquivContent($name, $value=null) {
		$this->setHtmlMetaCustomContent('META_HTTP_EQUIV_CONTENT', $name, $value);
	}

	final public function getHtmlMetaLinkRelHref($name) {
		return $this->getHtmlMetaCustomContent('META_LINK_REL_HREF', $name);
	}
	final public function setHtmlMetaLinkRelHref($name, $value=null) {
		$this->setHtmlMetaCustomContent('META_LINK_REL_HREF', $name, $value);
	}

	protected function getHtmlMetaPlainContent($name) {
		return ifsetor($this->htmlMetaContent[$name], null);
	}
	protected function setHtmlMetaPlainContent($name, $value=null, $escape=true) {
		$value = $escape ? Text::smartHtmlSpecialChars($value) : $value;
		$this->htmlMetaContent[$name] = $value;
		return $value;
	}

	final public function getHtmlMetaTitle() {
		return $this->getHtmlMetaPlainContent('TITLE');
	}
	final public function setHtmlMetaTitle($value=null, $escape=true) {
		return $this->setHtmlMetaPlainContent('TITLE', $value, $escape);
	}

	final public function getHtmlMetaDescription() {
		return $this->getHtmlMetaPlainContent('DESCRIPTION');
	}
	final public function setHtmlMetaDescription($value=null, $escape=true) {
		return $this->setHtmlMetaPlainContent('DESCRIPTION', $value, $escape);
	}

	final public function getHtmlMetaKeywords() {
		return $this->getHtmlMetaPlainContent('KEYWORDS');
	}
	final public function setHtmlMetaKeywords($value=null, $escape=true) {
		return $this->setHtmlMetaPlainContent('KEYWORDS', $value, $escape);
	}

	final public function getHtmlMetaImageSrc() {
		return $this->getHtmlMetaLinkRelHref('image_src');
	}
	final public function setHtmlMetaImageSrc($image_src) {
		$this->setHtmlMetaLinkRelHref('image_src', $image_src);
	}

	final public function getHtmlMetaCanonicalUrl() {
		return $this->getHtmlMetaLinkRelHref('canonical');
	}
	final public function setHtmlMetaCanonicalUrl($canonical_url) {
		$this->setHtmlMetaLinkRelHref('canonical', $canonical_url);
	}

	final public function include_js($js, $local=true) {
		$js = Cast::strarr($js);
		foreach($js as $file) {
			$file_js = $local ? (S_URL.'js/'.$file) : $file;
			if(!in_array($file_js, $this->htmlHeaderJS)) {
				$this->htmlHeaderJS[] = $file_js;
			}
		}
	}

	final public function include_css($css, $local=true) {
		$css = Cast::strarr($css);
		foreach($css as $file) {
			$file_css = $local ? (S_URL.'css/'.$file) : $file;
			if(!in_array($file_css, $this->htmlHeaderCSS)) {
				$this->htmlHeaderCSS[] = $file_css;
			}
		}
	}

	final public function include_rss($rss, $escapeTitle=true) {
		if(is_array($rss)) {
			foreach($rss as $href=>$title) {
				if(!array_key_exists($href, $this->htmlHeaderRSS)) {
					$this->htmlHeaderRSS[$href] = $escapeTitle ? Text::smartHtmlSpecialChars($title) : $title;
				}
			}
		}
	}

	/**
	 * include paginator js & css
	 * @param void
	 */
	final public function include_paginator() {
		$this->include_css('paginator.css');
		$this->include_js('paginator.js');
	}

	/**
	 * include jscalendar js & css
	 * @param void
	 */
	final public function include_jscal($scheme='steel') {

		$schemeList = array('gold', 'win2k', 'steel', 'matrix');
		$scheme = insetor($scheme, $schemeList, 'steel');

		$this->include_css(S_URL.'ext/jscal2-1.9/css/jscal2.css', false);
		$this->include_css(S_URL.'ext/jscal2-1.9/css/border-radius.css', false);
		//$this->include_css(S_URL.'ext/jscal2-1.9/css/reduce-spacing.css', false);

		$this->include_css(S_URL.'ext/jscal2-1.9/css/'.$scheme.'/'.$scheme.'.css', false);

		$this->include_js(S_URL.'ext/jscal2-1.9/js/jscal2.js', false);
		$this->include_js(S_URL.'ext/jscal2-1.9/js/lang/ru.js', false);
		//$this->include_js(S_URL.'ext/jscal2-1.9/js/lang/en.js', false);
		//$this->include_js(S_URL.'ext/jscal2-1.9/js/lang/de.js', false);
	}

	/**
	 *
	 */
	final public function include_jcrop() {
		$this->include_css(S_URL.'ext/jcrop-0.9.9/jquery.Jcrop.css', false);
		//$this->include_js(S_URL.'ext/jcrop-0.9.9/jquery.color.js', false);
		$this->include_js(S_URL.'ext/jcrop-0.9.9/jquery.Jcrop.min.js', false);
	}

	const COMPRESS_COOKIE_NAME = 1;
	const COMPRESS_COOKIE_VALUE = 2;
	final public static function get_compress_info($what) {
		$result = null;
		switch($what) {
			case self::COMPRESS_COOKIE_NAME:
				$result = 'dbg_not_compress';
				break;
			case self::COMPRESS_COOKIE_VALUE:
				$result = md5('dbg_not_compress'.Network::clientHttpSignature());
				break;
		}
		return $result;
	}

	final public static function is_compress_html() {
		$name = self::get_compress_info(self::COMPRESS_COOKIE_NAME);
		$cookie_value = ifsetor($_COOKIE[$name], null);
		$compress = CONST_COMPRESS_OUTPUT && $cookie_value!=self::get_compress_info(self::COMPRESS_COOKIE_VALUE);
		return $compress;
	}

	final public static function set_time_limit_user_abort($time=300, $ignore_user_abort=true) {
		set_time_limit($time);
		ignore_user_abort($ignore_user_abort);
	}

	final public function &getDoctypeList() {

		$doctypes = array(
			'xhtml11'		=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
			'xhtml1_strict'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
			'xhtml1_trans'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
			'xhtml1_trans_custom'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" xmlns:og="http://ogp.me/ns#">',
			'xhtml1_frame'	=> '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
			'html5'			=> '<!DOCTYPE html>',
			'html4_strict'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
			'html4_trans'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
			'html4_frame'	=> '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">'
		);
		return $doctypes;
	}
}
