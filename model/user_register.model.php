<?

class UserRegister extends Object {

	const MYSQL_TABLE = 'user_register';

	const STATUS_NEW = 0;
	const STATUS_REGISTERED = 1;

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {
		//
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {
		$result = array();
		$result['email'] = SafeHtmlModel::input(ifsetor($_REQ['email'], null));
		$result['authcode'] = md5(uniqid());
		$result['reg_tstamp'] = time();
		$result['reg_ip'] = Network::clientIp();
		$result['reg_fwrd'] = Network::clientFwrd();
		return $result;
	}

	/**
	 * @override
	 * @see Object::validateAdd()
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {

		$__validator = new ValidatorModel();

		$_REQ['email'] = ifsetor($_REQ['email'], null);
		if(!$__validator->user_email($_REQ['email'])) {
			$this->pushError('EMAIL_FORMAT');
		}

		if(!$this->isError()) {
			// check existing user
			$user_collection = new UserCollection();
			$user_collection->getCollection(array('email'=>$_REQ['email']));
			if($user_collection->length()) {
				$this->pushError('EMAIL_EXISTS');
			}
			else {
				// check previously deleted user
				$user_deleted_collection = new UserDeletedCollection();
				$user_deleted_collection->getCollection(array('email'=>$_REQ['email'], 'is_spamer'=>1));
				if($user_deleted_collection->length()) {
					$this->pushError('EMAIL_SPAMER_EXISTS');
				}
			}
		}

		return !$this->isError();
	}

	/**
	 * @override
	 * @see User::removeById()
	 */
	public function removeById() {
		return parent::remove();
	}

	/**
	 *
	 * @param unknown_type $authcode
	 * @return UserRegister
	 */
	public function loadByAuthCode($authcode) {
		if($authcode) {
			$collectionClass = $this->getCollectionClass();
			$collection = new $collectionClass();
			$collection->getCollection(array('authcode'=>$authcode));
			if($collection->length()) {
				$this->populate($collection->getFirst()->getFields());
			}
		}
		return $this;
	}
}

class UserRegisterCollection extends ObjectCollection {

	public function __construct($classname='UserRegister') {
		$object = new $classname();
		parent::__construct($object);
	}
}