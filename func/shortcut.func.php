<?

/**
 * Show Debug
 */
function _e($what, $error=E_USER_NOTICE) {
	$__debug =& __debug();
	$__debug->show($what, $error);
}

/**
 * Get MySQL Database Handler
 */
function &__db($choice=1) {
	$__db = null;
	if(!is_numeric($choice)) $choice = 1;
	if($choice==1) {
		$__db =& MySQL::getInstance(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
	}
	//_e($__db);
	return $__db;
}

/**
 * Get Storage Handler
 * @param string [storage classname: StorageMemcache, StorageMysql, StorageFilecache]
 */
function &__storage($choice=null) {
	switch($choice) {
		case StorageModel::STORAGE_MEMCACHE:
		case StorageModel::STORAGE_FILECACHE:
		case StorageModel::STORAGE_MYSQL:
			$__storage =& call_user_func(array($choice, 'singleton'));
			break;
		default:
			$__storage =& StorageModel::getInstance();
	}

	//_e($__storage);
	return $__storage;
}

/**
 * Get Locale&Codeset Handler
 */
function &__locale() {
	$__locale =& Locale::getInstance();
	//_e($__locale);
	return $__locale;
}

/**
 * Get Debug Handler
 */
function &__debug() {
	require_once(LIB_PATH . 'debug.class.php');
	static $__debug = null;
	if(is_null($__debug)) {
		$__debug =& Debug::getInstance();
		//$__debug->enableMessaging();
		//$__debug->realTimeOutput = true;
	}
	return $__debug;
}

/**
 * Quick Send Mail Func
 */
function __mailme($message_subject=null, $message_body=null) {
	return MessageModel::send_to_support($message_subject, $message_body, SUPPORT_EMAIL, URL_NAME);
}
/**
 * Check Mixed Var And Return Default Value If Not Set
 */
function ifsetor(&$param, $default, $notempty=false) {
	return (isset($param)&&($notempty?!empty($param):true))?$param:$default;
}

/**
 * Check Var In Array And Return Default Value If Not Set
 * @param scalar
 * @param array
 * @param scalar
 */
function insetor($what, $set, $default) {
	$result = $default;
	if(is_array($set)) {
		$values = array_values($set);
		$first = isset($values[0]) ? $values[0] : null;
		$what_cast = $what;
		if(is_int($first)) {
			$what_cast = (int)$what;
		}
		elseif(is_string($first)) {
			$what_cast = (string)$what;
		}
		elseif(is_float($first)) {
			$what_cast = (float)$what;
		}
		$result = (strcmp($what, $what_cast)===0 && in_array($what_cast, $set, true)) ? $what : $default;
	}
	return $result;
}

/**
 * Check if var exists in array
 * @param scalar
 * @param array
 * @return boolean
 */
function insetcheck($what, $set) {
	return insetor($what, $set, false)!==false;
}
/**
 * Check User Authorization. Redirect to login page if not auth
 */
function require_auth() {
	return User::checkLoginned();
}
