<?

class PhotoDeleted extends Photo {

	const MYSQL_TABLE = 'photo_deleted';

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
	 * @see Photo::removeById()
	 */
	public function removeById() {
		return parent::remove();
	}
}

class PhotoDeletedCollection extends PhotoCollection {

	public function __construct($classname='PhotoDeleted') {
		$object = new $classname();
		parent::__construct($object);
	}
}