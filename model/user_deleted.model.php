<?

class UserDeleted extends User {

	const MYSQL_TABLE = 'user_deleted';

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see AtomicObject::beforeInsert()
	 */
	protected function beforeInsert() {
		$this->setField('admin_id', User::getOnlineUserId());
		$this->setField('del_tstamp', time());
		$this->setField('del_ip', Network::clientIp());
		$this->setField('del_fwrd', Network::clientFwrd());
	}

	/**
	 * @override
	 * @see User::afterUpdate()
	 */
	protected function afterUpdate() {
		//@TODO
	}

	/**
	 * @override
	 * @see User::removeById()
	 */
	public function removeById() {
		return parent::remove();
	}
}

class UserDeletedCollection extends UserCollection {

	public function __construct($classname='UserDeleted') {
		$object = new $classname();
		parent::__construct($object);
	}
}