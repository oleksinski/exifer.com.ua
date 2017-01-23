<?

/**
 * Debug message class
 *
 */

class Debug extends Singleton {

	const MSG_COOKIE = 'dbg_cookie';
	const MSG_REALTIME_COOKIE = 'dbg_realtime';
	const GET_DBG_PARAM = 'dbg';
	const MSG_SHOW = 1;
	const MSG_MAIL = 2;
	const MSG_LOG = 4;
	const MSG_EXIT = 8;
	const MSG_COMMAND = ' %cmd$';
	const MSG_COMMAND_AND = ' %cmd$&';
	const MSG_COMMAND_OR = ' %cmd$|';

	private $enableErrorMessaging = false;

	public $realTimeOutput = false;

	private $htmlHeaders = array();

	private $stdOutHeaders = array();

	private $logHeaders = array();

	private $mailHeaders = array();

	private $logFile = null;

	private $classSetings = array();

	private $mailBody = null;

	public static $messages = array();

	/**
	 * @override
	 * @param unknown_type $c
	 */
	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}

	protected function __construct() {

		$e_style = array();

		// 1
		$e_style[E_ERROR] = 'color:#cc0000;';

		// 2
		$e_style[E_WARNING] = 'color:#aa6600;';

		// 4
		$e_style[E_PARSE] = 'color:#BF970A;';

		// 8
		$e_style[E_NOTICE] = 'color:#BF0A8E;';

		// 16
		$e_style[E_CORE_ERROR] = 'color:#BB144B;';

		// 32
		$e_style[E_CORE_WARNING] = 'color:#8D16CB;';

		// 64
		$e_style[E_COMPILE_ERROR] = 'color:#8D16CB;';

		// 128
		$e_style[E_COMPILE_WARNING] = 'color:#8D16CB;';

		// 2047
		$e_style[E_ALL] = 'color:#3FBB14;';

		// 2048
		$e_style[E_STRICT] = 'color:#1BB2D2;';

		// 256
		$e_style[E_USER_ERROR] = 'color:#cc0000;';

		// 512
		$e_style[E_USER_WARNING] = 'color:#aa6600;';

		// 1024
		$e_style[E_USER_NOTICE] = 'color:#0000cc;';

		$style_str = ' style="font-family:verdana,sans-serif; padding-left:15px; font-size:10px; text-align:left; %s"';

		$this->htmlHeaders = array();
		foreach($e_style as $error=>$style) {
			$this->htmlHeaders[$error] = sprintf($style_str, $style);
		}

		$this->stdOutHeaders = array(
			E_ERROR				=> ' E_ERROR ',
			E_WARNING			=> ' E_WARNING ',
			E_PARSE				=> ' E_PARSE ',
			E_NOTICE			=> ' E_NOTICE ',
			E_CORE_ERROR		=> ' E_CORE_ERROR ',
			E_CORE_WARNING		=> ' E_CORE_WARNING ',
			E_COMPILE_ERROR		=> ' E_COMPILE_ERROR ',
			E_COMPILE_WARNING	=> ' E_COMPILE_WARNING ',
			E_ALL				=> ' E_ALL ',
			E_STRICT			=> ' E_STRICT ',
			E_USER_ERROR		=> ' E_USER_ERROR ',
			E_USER_WARNING		=> ' E_USER_WARNING ',
			E_USER_NOTICE		=> ' E_USER_NOTICE ',
		);

		foreach(array('E_RECOVERABLE_ERROR', 'E_DEPRECATED', 'E_USER_DEPRECATED') as $err_const) {
			if(defined($err_const)) {
				$this->stdOutHeaders[constant($err_const)] = ' '.$err_const.' ';
			}
		}

		$this->logHeaders = $this->stdOutHeaders;

		$this->mailHeaders = $this->stdOutHeaders;
		foreach(array('E_USER_ERROR', 'E_USER_WARNING', 'E_USER_NOTICE') as $e_us) {
			if(defined($e_us) && isset($this->mailHeaders[constant($e_us)])) {
				unset($this->mailHeaders[constant($e_us)]);
			}
		}

		$this->checkMessaging();

		$this->realTimeOutput = $this->isRealTimeOutput();

		ini_set('html_errors', false);

		error_reporting(E_ALL);
		//error_reporting(E_WARNING | E_NOTICE | E_USER_ERROR | E_USER_WARNING);

		set_error_handler(array(&$this, 'errorHandler'));

		if($this->isMessagingEnabled()) {
			register_shutdown_function(array(&$this, 'onExit'));
		}
		else {
			register_shutdown_function(array(&$this, 'sendMail'));
		}
	}


	/**
	 *
	 * @param unknown_type $errNo
	 * @param unknown_type $errStr
	 * @param unknown_type $errFile
	 * @param unknown_type $errLine
	 * @param unknown_type $errContext
	 */
	public function errorHandler($errNo, $errStr, $errFile, $errLine, $errContext) {

		$errNo &= error_reporting();

		if (!$errNo) {
			return false;
		}

		$className = false;
		if (isset($errContext['this'])) {
			$className = get_class($errContext['this']);
			$errStr = '[' . $className . '] ' . $errStr;
		}

		$errDesc = _trim(ifsetor($this->stdOutHeaders[$errNo], null));
		$errDesc = _substr_replace($errDesc, '', 0, 2);
		$errDesc = _ucwords(_strtolower($errDesc));
		$errDesc = sprintf("#%s - %s (%s:%s)", $errDesc, $errStr, $errFile, $errLine);

		$outEngine = self::MSG_SHOW;

		if(isset($this->classSetings[$className]) && isset($this->classSetings[$className][$errNo])) {
			$outEngine = $this->classSetings[$className][$errNo];
		}

		if(isset($this->mailHeaders[$errNo])) {
			$outEngine |= self::MSG_MAIL;
		}

		if($outEngine & self::MSG_SHOW) {
			$this->show($errDesc."\n".self::getDebugBacktrace(), $errNo);
		}
		if($outEngine & self::MSG_MAIL && !$this->isMessagingEnabled()) {
			$this->mailme($errDesc, $errNo);
		}
		if(0 && $outEngine & self::MSG_LOG) {
			$this->logme($errDesc, $errNo);
		}
		if(0 && $outEngine & self::MSG_EXIT) {
			exit();
		}
	}

	public function getDebugCookie() {
		require_once(LIB_PATH.'network.class.php');
		$signature = md5('dbg_global_debug_cookie'.Network::clientHttpSignature());
		return $signature;
	}

	public function enableMessaging() {
		$this->enableErrorMessaging=true;
	}

	public function disableMessaging() {
		$this->enableErrorMessaging=false;
	}

	public function isMessagingCookieSet() {
		return isset($_COOKIE[self::MSG_COOKIE]) && $_COOKIE[self::MSG_COOKIE]==$this->getDebugCookie();
	}

	public function isMessagingEnabled() {
		return $this->enableErrorMessaging===true;
	}

	public function getDebugRealtimeCookie() {
		require_once(LIB_PATH.'network.class.php');
		$signature = md5('dbg_realtime_debug_cookie'.Network::clientHttpSignature());
		return $signature;
	}

	public function isRealTimeOutput() {
		$bool = $this->realTimeOutput;
		$bool = $bool || isset($_COOKIE[self::MSG_REALTIME_COOKIE]) && $_COOKIE[self::MSG_REALTIME_COOKIE]==$this->getDebugRealtimeCookie();
		$bool = $bool || Predicate::isShellCall();
		return $bool;
	}

	public function checkMessaging() {
		$this->disableMessaging();
		if(Predicate::isShellCall()) {
			$this->enableMessaging();
			return true;
		}
		else {
			if($this->isMessagingCookieSet()) {
				if(!isset($_REQUEST[self::GET_DBG_PARAM]) || $_REQUEST[self::GET_DBG_PARAM]==1) {
					$this->enableMessaging();
					return true;
				}
			}
		}
	}
	/**
	 * Show formated message at the page
	 *
	 * @param mixed $what
	 * @param Int $type
	 */
	public function show($what, $type=E_USER_NOTICE) {

		if($this->enableErrorMessaging) {

			$what = $this->parse_var($what);

			if(Predicate::isWebCall()) {

				$header = array_key_exists($type, $this->htmlHeaders) ? $this->htmlHeaders[$type] : '';

				$debugMes = '<pre' . $header . ' class="_debug">' . _htmlspecialchars($what) . '</pre>';
			}
			else {
				$debugMes = $what."\n";
			}

			if($this->realTimeOutput) {
				echo $debugMes;
			}
			else {
				array_push(self::$messages, $debugMes);
			}

			//flush();
		}
	}

	public function parse_var($what) {
		if(is_numeric($what)) {
			$text = 'numeric:' . $what;
		}
		elseif($what===null) {
			$what = 'NULL';
		}
		elseif(is_bool($what)) {
			$what = 'bool:' . ($what?'TRUE':'FALSE');
		}
		elseif(is_string($what)) {
			$what = (_trim($what)==''?'empty string (' . _strlen($what) . '):\'' . $what . '\'':'') . $what;
		}
		elseif(is_array($what) or is_object($what)) {
			$what = print_r($what, true);
		}
		elseif(is_resource($what)) {
			$what = 'resource: #';
		}
		else {
			$what = 'unknown:' . $what;
		}
		return $what;
	}

	public function logme($what, $type=E_USER_NOTICE) {
		if($this->logFile && !error_log(date('H:i:s').$this->logHeaders[$type].$what."\n", 3, $this->logFile)) {
			$this->show('[message] Cannot write to log file ' . $this->logFile . '. Log is disabled.');
			$this->logFile = false;
		}
	}

	public function mailme($errDesc, $errNo=E_USER_NOTICE) {

		$dbg = self::getDebugBacktrace();

		if($this->mailBody) {
			$this->mailBody .= "\n\n";
		}

		if($errDesc) {
			$this->mailBody .= $errDesc;
		}

		if($dbg) {
			$this->mailBody .= "\n".$dbg;
		}
	}

	public function prepare_mailme_str($str, $desc=null) {
		$str = (string)($str);
		if(_strlen($str) > 3000) {
			$str = _substr($str, 0, 1500) . '...' . _substr($str, -1500);
		}
		if($desc) {
			$str = $desc.': '.$str;
		}
		return $str;
	}

	public function sendMail() {

		if($this->mailBody) {

			$vars = array();

			$server = 'PRO';
			if(Predicate::server_dev()) {
				$server = 'DEV';
			}
			elseif(Predicate::server_pro()) {
				$server = 'PRO';
			}

			$msg_subject = $server . ' - WARN,NOTICE,ERROR: ';

			if(Predicate::isWebCall()) {

				$vars[] =  $this->prepare_mailme_str(date("Y-m-d H:i:s", time()), 'Time');
				$vars[] =  $this->prepare_mailme_str(Url::currurl(), 'URL');
				$vars[] =  $this->prepare_mailme_str(php_uname(), 'php_uname');
				$vars[] =  $this->prepare_mailme_str(@$_SERVER['REMOTE_ADDR'], 'IP');
				$vars[] =  $this->prepare_mailme_str(@$_SERVER['HTTP_X_FORWARDED_FOR'], 'FWRD');
				$vars[] =  $this->prepare_mailme_str(gethostbyaddr(@$_SERVER['REMOTE_ADDR']), 'Host');
				$vars[] =  $this->prepare_mailme_str(@$_SERVER["HTTP_USER_AGENT"], 'UserAgent');
				$vars[] =  $this->prepare_mailme_str(@$_SERVER["HTTP_REFERER"], 'Referer');
				$vars[] =  $this->prepare_mailme_str(User::getOnlineUserId(), 'User');
				$vars[] =  $this->prepare_mailme_str($this->parse_var($_GET), 'GET');
				$vars[] =  $this->prepare_mailme_str($this->parse_var($_POST), 'POST');
				$vars[] =  $this->prepare_mailme_str($this->parse_var($_COOKIE), 'COOKIE');
				//$vars[] =  $this->prepare_mailme_str($this->parse_var(get_included_files()), 'Includes');

				$msg_subject .= @$_SERVER['PHP_SELF'];
			}
			else { // shell

				$script = @$_ENV['PWD'].'/'.$_SERVER['SCRIPT_NAME'];
				$vars[] =  $this->prepare_mailme_str($script, 'SCRIPT NAME');

				$msg_subject .= $script;
			}

			$vars[] =  $this->prepare_mailme_str($this->parse_var($_SERVER), '$_SERVER');

			$msg_body = implode("\n\n---\n\n", array($msg_subject, $this->mailBody, implode("\n", $vars)));

			// ---

			if(Predicate::server_pro()) {
				__mailme($msg_subject, $msg_body);
			}
			elseif(Predicate::server_dev()) {
				if(Predicate::isWebCall()) {
					$msg_body = sprintf('<pre style="font-family:verdana,sans-serif; padding-left:15px; font-size:10px; text-align:left; color:#cc0000;">%s</pre>', $msg_body);
				}
				echo $msg_body;
			}
		}
	}

	public function onExit() {
		if($this->realTimeOutput) {
			// nothing yet
		}
		else {
			foreach(self::$messages as $message) {
				echo $message;
			}
		}
	}

	public static function getDebugBacktrace() {

		$dbg = null;

		$backtrace = debug_backtrace();

		$backtrace_length = count($backtrace);

		for($i=$backtrace_length-1; $i>1; $i--) {

			if($dbg) $dbg .= "\n";

			$backtrace_i =& $backtrace[$i];

			$errFile = isset($backtrace_i['file']) ? $backtrace_i['file'] : null;
			$errLine = isset($backtrace_i['line']) ? $backtrace_i['line'] : null;
			$errFunc = isset($backtrace_i['function']) ? $backtrace_i['function'] : null;
			$errClass = isset($backtrace_i['class']) ? $backtrace_i['class'] : null;
			//$errObject = isset($backtrace_i['object']) ? $backtrace_i['object'] : null;
			$errType = isset($backtrace_i['type']) ? $backtrace_i['type'] : null;
			//$errArgs = isset($backtrace_i['args']) ? $backtrace_i['args'] : null;

			if($errClass) {
				$dbg .= $errClass . $errType;
			}

			if($errFunc!='unknown') {
				$errFunc.= '()';
			}

			$dbg .= $errFunc;

			if(is_scalar($errFile) && $errFile) {
				$errFileLine = $errFile;
				if(is_scalar($errLine) && $errLine) {
					$errFileLine .= ':'.$errLine;
				}
				$dbg .= ' ('.$errFileLine.')';
			}
		}

		return $dbg;
	}

	private function setFlag($class, $errNo, $flag) {
		@$this->classSetings[$class][$errNo] |= $flag;
	}

	private function clearFlag($class, $errNo, $flag) {
		@$this->classSetings[$class][$errNo] &= ~$flag;
	}

	private function setErrExitFlag($class) {
		$this->setFlag($class, E_USER_ERROR, self::MSG_EXIT);
	}

	private function clearErrExitFlag($class) {
		$this->clearFlag($class, E_USER_ERROR, self::MSG_EXIT);
	}
}
