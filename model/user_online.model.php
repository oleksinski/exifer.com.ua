<?

class UserOnline extends Object {

	const MYSQL_TABLE = 'user_online';

	const LIVE_ONLINE_TIME = 300; // 5*60 sec

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see AtomicObject::_init()
	 */
	protected function _init() {
		$this->setUpdateOnDuplicate(true);
		$this->setInsertDelayed(true);
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {
		$result = array();
		$user = User::getOnlineUser();
		if($user->exists() && !$user->isBitmaskSet(User::BITMASK_HIDE_ONLINE)) {
			$result['user_id'] = $user->getId();
			$result['hit_tstamp'] = time();
			$result['hit_ip'] = Network::clientIp();
			$result['hit_fwrd'] = Network::clientFwrd();
		}
		return $result;
	}

	/**
	 * @return int
	 */
	public static function getMinLiveTimestamp() {
		return Cast::unsignint(time() - self::LIVE_ONLINE_TIME);
	}

	/**
	 * @return bool
	 */
	public function isLive() {
		return $this->getField('hit_tstamp') > self::getMinLiveTimestamp();
	}

	/**
	 * @return bool
	 */
	public function isExpired() {
		return !$this->isLive();
	}

	/**
	 *
	 */
	public function synchronize() {
		if($this->getField('hit_tstamp')) {
			$user = new User($this->getField('user_id'));
			if(!$user->isBanned()) {
				$user->setField('hit_tstamp', $this->getField('hit_tstamp'));
				$user->setField('hit_ip', $this->getField('hit_ip'));
				$user->setField('hit_fwrd', $this->getField('hit_fwrd'));
				$user->update();
			}
		}
	}

	/**
	 * @override
	 * @see AtomicObject::beforeRemove()
	 */
	protected function beforeRemove(){
		$this->synchronize();
	}
}

class UserOnlineCollection extends ObjectCollection {

	public function __construct($classname='UserOnline') {
		$object = new $classname();
		parent::__construct($object);
	}

	/**
	 * @override
	 * @see AtomicObjectCollection::afterRemove()
	 */
	protected function afterRemove() {
		if($this->getCount()==0) {
			$this->db()->u(sprintf('TRUNCATE TABLE %s', $this->db_table));
		}
	}

	/**
	 * @return AtomicObjectCollection
	 */
	public function getCollectionLive() {
		return $this->getCollection(array(), array(sprintf('hit_tstamp>%u', UserOnline::getMinLiveTimestamp())), array('hit_tstamp'=>'DESC'));
	}

	/**
	 * @return AtomicObjectCollection
	 */
	public function getCollectionExpired() {
		return $this->getCollection(array(), array(sprintf('hit_tstamp<%u', UserOnline::getMinLiveTimestamp())), array('hit_tstamp'=>'ASC'));
	}
}