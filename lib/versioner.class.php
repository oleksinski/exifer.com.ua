<?

/**
 * Http static automatic versioner.
 * Add file modification timestamp as get-param to keep static http contect (css, js..,etc) up-to-date at user-end
 */

class Versioner extends Singleton {

	protected $__staticFilePath;
	protected $__staticVarName;
	protected $__staticVarValue;

	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}

	protected function __construct() {

		$this->__staticFilePath = STATIC_PATH.'versioner/files.php';
		$this->__staticVarName = 'files';
		$this->__staticVarValue = array();

		@include($this->__staticFilePath);

		if(isset(${$this->__staticVarName}) && is_array(${$this->__staticVarName})) {
			$this->__staticVarValue = ${$this->__staticVarName};
		}
	}

	public function __destruct() {
		//
	}

	public function getStaticFileArr() {
		return $this->__staticVarValue;
	}

	public function getStaticFilePath() {
		return $this->__staticFilePath;
	}

	public function writeStatic() {

		$S_PATH = Url::fix(S_PATH);

		$fileList = FileFunc::readDirFiles($S_PATH);

		$this->__staticVarValue = array();

		$s_dirs = array('js/', 'css/', 'ext/', 'img/');

		foreach($fileList as $filepath) {

			$filepath_relative = _str_replace($S_PATH, '', $filepath);

			if(0!==strcmp($filepath, $filepath_relative)) {

				foreach($s_dirs as $dirname) {
					if(_strpos($filepath_relative, $dirname, 0)===0) {
						$filemtime = @filemtime($filepath);
						if($filemtime) {
							$this->__staticVarValue[$filepath_relative] = $filemtime;
						}
						break;
					}
				}
			}
		}

		FileFunc::saveVarsToFile(
			$this->__staticFilePath,
			array($this->__staticVarName => &$this->__staticVarValue)
		);
	}

	public function get($url) {

		Url::fix(&$url);

		$S_URL = Url::fix(S_URL);

		$url_relative = _str_replace($S_URL, '', $url);

		if(0!==strcmp($url, $url_relative) && array_key_exists($url_relative, $this->__staticVarValue)) {

			$__rewrite = new Rewrite($url_relative);

			$queryArr = $__rewrite->getQueryArr();
			$queryArr['v'] = $this->__staticVarValue[$url_relative];
			$__rewrite->modifyQueryArr($queryArr);

			$url = $S_URL . $__rewrite->getUrl();
		}

		return $url;
	}

}
