<?

/**
 * Main Url Mapper
 * Implements Rewrite class methods and properties
 */

class RewriteModel extends Rewrite {

	protected $regexp;

	protected $controlName;
	protected $controlAction;
	protected $controlParams;

	private $urls;

	public function __construct($URL=null) {

		parent::__construct($URL, $setLocale=true);

		include(ROOT_PATH . 'urls.inc.php');

		if(isset($URL_RULES) && is_array($URL_RULES)) {
			$this->urls =& $URL_RULES;
		}
		else {
			$this->urls = array();
		}

		$this->mapUrl();
	}

	public function mapUrl() {

		$result = array();
		$matches = array();

		foreach($this->urls as $pattern=>$handler) {

			$pattern = _str_replace('#', '\#', $pattern);

			$__path = _trim($this->__path, '/');

			if(_preg_match('#'.$pattern.'#', $__path, $matches)) {

				if(isset($matches[0])) {

					array_shift($matches);

					$this->regexp = $pattern;

					$name_action = explode('.', $handler);

					$this->controlName = @$name_action[0];
					$this->controlAction = @$name_action[1];

					$this->controlParams = $matches;

					$result = array($this->controlName, $this->controlAction, $this->controlParams);

					break;
				}
			}
		}
		return $result;
	}

	public function getRegexp() {
		return $this->regexp;
	}

	public function getControlName() {
		return $this->controlName;
	}

	public function getControlAction() {
		return $this->controlAction;
	}

	public function getControlParams() {
		return $this->controlParams;
	}
}