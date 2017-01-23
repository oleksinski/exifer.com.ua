<?

abstract class Article extends Object {

	const MYSQL_TABLE = 'article';

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected final function loadExtraFields() {
		$this->setExtraField('url', '#');
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {

		$result = array();

		$result['type'] = ifsetor($_REQ['type'], 0);
		$result['user_id'] = User::getOnlineUserId();
		$result['title'] = ifsetor($_REQ['title'], null);
		$result['subtitle'] = ifsetor($_REQ['subtitle'], null);
		$result['body'] = ifsetor($_REQ['body'], null);
		$result['add_tstamp'] = time();
		$result['add_ip'] = Network::clientIp();
		$result['add_fwrd'] = Network::clientFwrd();

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateAdd()
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {

		return !$this->isError();
	}

	/**
	 * @override
	 * @see Object::collectChangeFields()
	 */
	protected function collectChangeFields($_REQ) {

		$result = array();

		$result['title'] = ifsetor($_REQ['title'], null);
		$result['subtitle'] = ifsetor($_REQ['subtitle'], null);
		$result['body'] = ifsetor($_REQ['body'], null);
		$result['update_tstamp'] = time();
		$result['update_ip'] = Network::clientIp();
		$result['update_fwrd'] = Network::clientFwrd();

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateChange()
	 */
	protected function validateChange($_REQ, $_REQ_RAW) {

		return !$this->isError();
	}

	/**
	 * @override
	 * @see AtomicObject::removeById()
	 */
	public function removeById() {

		$user_id = $this->getField('user_id');

		parent::removeById();

		self::recalcUserArticleCount($user_id);
	}

	/**
	 *
	 * @param unknown_type $user_id
	 */
	protected static function recalcUserArticleCount($user_id) {
		$user = new User($user_id);
		return $user->recalcInfo(User::RECALC_ARTICLE_COUNT);
	}

}

abstract class ArticleCollection extends ObjectCollection {

	public function __construct($classname='Article') {
		$object = new $classname();
		parent::__construct($object);
	}
}
