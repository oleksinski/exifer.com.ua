<?

class PhotoUserView extends Object {

	const MYSQL_TABLE = 'photo_user_view';

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see AtomicObject::_init()
	 */
	protected function _init() {
		$this->setInsertDelayed(true);
		$this->setUpdateOnDuplicate(true);
	}

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {
		$result = array();
		$result['item_id'] = ifsetor($_REQ['item_id'], 0);
		$result['user_id'] = ifsetor($_REQ['user_id'], 0);
		return $result;
	}

	/**
	 * @override
	 * @see Object::collectAddRawFields()
	 */
	protected function collectAddRawFields($_REQ) {
		$result = array();
		$result['views'] = 'views+1';
		return $result;
	}

	/**
	 * @override
	 * @see Object::validateAdd()
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {
		return ifsetor($_REQ['item_id'], false, true) && ifsetor($_REQ['user_id'], false, true);
	}
}

class PhotoUserViewCollection extends ObjectCollection {

	public function __construct($classname='PhotoUserView') {
		$object = new $classname();
		parent::__construct($object);
	}
}
