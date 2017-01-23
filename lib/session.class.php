<?

/**
 * Session is unique in scope of 1 browser.
 * Another browser will have another session for current user
 *
 * @author alex
 *
 */
class Session {

	const SESSION_COOKIE = 'USC';
	const REMEMBER_TIME = 1209600; // 14*24*3600

	/**
	 *
	 * @var string
	 */
	private $session_id;
	/**
	 *
	 * @var int
	 */
	private $user_id;
	/**
	 *
	 * @var bool
	 */
	private $remember;

	/**
	 *
	 * @var unknown_type
	 */
	private static $cache=array();

	/**
	 * magic method - called before serializing
	 * @return array
	 */
	public function __sleep() {
		return array('session_id', 'user_id', 'remember');
	}

	/**
	 * @return void
	 */
	public function __wakeup() {
		self::$cache[$this->getSessionId()] = $this;
	}

	/**
	 * @return string
	 */
	public function getSessionId() {
		return $this->session_id;
	}

	/**
	 * @param void
	 * @return string
	 */
	public function getCookieSessionId() {
		return ifsetor($_COOKIE[self::SESSION_COOKIE], null);
	}

	/**
	 *
	 * @param unknown_type $session_id
	 * @return string
	 */
	public function retrieveSessionId($session_id=null) {
		if(!$session_id) {
			$cookie_id = $this->getCookieSessionId();
			$session_id = $cookie_id ? $cookie_id : $this->getSessionId();
		}
		return $session_id;
	}

	/**
	 *
	 * @param unknown_type $session_id
	 * @return void
	 */
	public function setSessionId($session_id) {
		$this->session_id = $session_id;
	}

	/**
	 * @return int
	 */
	public function getUserId() {
		return (int)$this->user_id;
	}

	/**
	 *
	 * @param unknown_type $session_id
	 * @return int
	 */
	public function retrieveUserId($session_id=null) {
		$user_id = 0;
		$session = $this->retrieveSession($session_id);
		if($session && is_object($session)) {
			$user_id = $session->getUserId();
		}
		return $user_id;
	}

	/**
	 *
	 * @param unknown_type $user_id
	 * @return void
	 */
	public function setUserId($user_id) {
		$this->user_id = (int)$user_id;
	}

	/**
	 * @return bool
	 */
	public function getRemember() {
		return (bool)$this->remember;
	}

	/**
	 *
	 * @param unknown_type $session_id
	 * @return bool
	 */
	public function retrieveRemember($session_id=null) {
		$remember = false;
		$session = $this->retrieveSession($session_id);
		if($session && is_object($session)) {
			$remember = $session->getRemember();
		}
		return $remember;
	}

	/**
	 *
	 * @param unknown_type $remember
	 * @return void
	 */
	public function setRemember($remember) {
		$this->remember = (bool)$remember;
	}

	/**
	 *
	 * @param unknown_type $session_id
	 * @return Session
	 */
	public function getSession($session_id=null) {

		$session = null;

		if($session_id) {
			$this->setSessionId($session_id);
		}

		$session_id = $this->getSessionId();

		if($session_id) {
			$__storage =& __storage();
			$session = $__storage->get($session_id);
			if($session && !is_object($session)) {
				$session = null;
			}
			self::$cache[$session_id] = $session;
		}

		//if(!$session) {
		//	$session = clone $this;
		//}

		return $session;
	}

	/**
	 *
	 * @param unknown_type $session_id
	 * @return Session
	 */
	public function retrieveSession($session_id=null) {
		$session_id = $this->retrieveSessionId($session_id);
		$session = null;
		if(array_key_exists($session_id, self::$cache)) {
			$session = self::$cache[$session_id];
		}
		else {
			$session = $this->getSession($session_id);
		}
		return $session;
	}

	/**
	 *
	 * @param unknown_type $session_id
	 * @return bool
	 */
	public function saveSession($session_id=null) {

		$result = false;

		if($this->getUserId()) {

			$__storage =& __storage();

			if(!$session_id) {
				$session_id = $__storage->get_key();
			}

			$this->setSessionId($session_id);

			$session_id = $this->getSessionId();

			if($session_id) {

				$session = clone $this;

				$result = (bool)$__storage->set($session_id, $session, self::REMEMBER_TIME);

				self::$cache[$session_id] = $session;

				$this->setSessionCookie($session_id, $this->getRemember() ? (time()+self::REMEMBER_TIME) : 0);
			}
		}
		return $result;
	}

	/**
	 *
	 * @param unknown_type $session_id
	 * @return bool
	 */
	public function removeSession($session_id=null) {

		$result = null;

		$session = $this->retrieveSession($session_id);

		if($session) {

			$session_id = $this->getSessionId();

			$__storage =& __storage();
			$result = (bool)$__storage->del($session_id);

			// do not remove user profile session to allow multibrowser sessions
			//$user_profile_session = new UserProfileSession($this->getUserId());
			//$user_profile_session->remove();

			$user_online_collection = new UserOnlineCollection();
			$user_online_collection->removeByUserId($this->getUserId());

			$this->setUserId(0);
			$this->setRemember(false);

			if(array_key_exists($session_id, self::$cache)) {
				unset(self::$cache[$session_id]);
			}

			$this->setSessionCookie($session_id, time()-1);
		}

		return $result;
	}

	/**
	 *
	 * @param unknown_type $value
	 * @param unknown_type $expires
	 * @return bool
	 */
	private function setSessionCookie($value, $expires) {

		$cookie = array();
		$cookie['name'] = self::SESSION_COOKIE;
		$cookie['value'] = $value;
		$cookie['domain'] = Cookie::domain();
		$cookie['expires'] = $expires;
		$cookie['path'] = '/';

		return Cookie::set($cookie['name'], $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain']);
	}
}
