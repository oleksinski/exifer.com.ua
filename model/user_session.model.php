<?

/**
 * One user session for all possible browser sessions.
 * UserSession is responsible for storing user profile in storage memory.
 *
 * @author alex
 *
 */
class UserSession {

	/**
	 *
	 * @var int
	 */
	private $user_id;

	/**
	 * Is WEB call (not via CLI)
	 * @var unknown_type
	 */
	private $is_web;

	/**
	 *
	 * @var array
	 */
	private static $cache=array();

	/**
	 *
	 * @param unknown_type $user_id
	 */
	public function __construct($user_id=0) {
		$this->setUserId($user_id);
		$this->is_web = Predicate::isWebCall();
	}

	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
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
	 * @return string
	 */
	public function getSessionId() {
		$user_id = $this->getUserId();
		return $user_id ? md5('uSx_23K-dJl'.$user_id) : null;
	}

	private function putCache($session_id, $value) {
		if($this->is_web) {
			self::$cache[$session_id] = $value;
			// refresh online user session
			$user = User::getStaticUser();
			if(is_object($user) && $user->getId()==$this->getUserId()) {
				User::getOnlineUser(true);
			}
		}
	}

	private function delCache($session_id) {
		if(array_key_exists($session_id, self::$cache)) {
			unset(self::$cache[$session_id]);
		}
		if($this->is_web) {
			if($this->getUserId() && $this->getUserId()==User::getOnlineUserId()) {
				User::getOnlineUser(true);
			}
		}
	}

	/**
	 * @return bool
	 */
	public function save() {
		$result = false;
		$user_id = $this->getUserId();
		if($user_id) {
			$user = new User($user_id);
			if($user->exists()) {
				$__storage =& __storage();
				$result = (bool)$__storage->set($this->getSessionId(), $user, Session::REMEMBER_TIME);
				$this->putCache($user_id, $user);
			}
		}
		return $result;
	}

	/**
	 * @return bool
	 */
	public function update() {
		$result = false;
		if($this->get()) {
			$result = $this->save();
		}
		return $result;
	}

	/**
	 * @return User
	 */
	public function get() {
		$user = null;
		$user_id = $this->getUserId();
		if($user_id) {
			if(array_key_exists($user_id, self::$cache)) {
				$user = self::$cache[$user_id];
			}
			else {
				$__storage =& __storage();
				$user = $__storage->get($this->getSessionId());
				if($user) {
					if(!is_object($user)) {
						$user = null;
					}
					$this->putCache($user_id, $user);
				}
			}
		}
		return $user;
	}

	/**
	 * @return bool
	 */
	public function remove() {
		$result = false;
		$user_id = $this->getUserId();
		if($user_id) {
			$__storage =& __storage();
			$result = (bool)$__storage->del($this->getSessionId());
			if($result) {
				$user_online_collection = new UserOnlineCollection();
				$user_online_collection->removeByUserId($user_id);
			}
			$this->delCache($user_id);
			$this->setUserId(0);
		}
		return $result;
	}

	/**
	 * @return void
	 */
	public static function removeAll() {
		$user_collection = new UserCollection();
		$user_collection->getCollection(array('status'=>User::STATUS_OKE), array(sprintf('login_tstamp>', time()-Session::REMEMBER_TIME)));
		foreach($user_collection as $user_id=>$user) {
			$self = new self($user_id);
			$self->remove();
		}
	}
}
