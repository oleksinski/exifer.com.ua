<?

/**
 *
 * @author alex
 *
 */
abstract class Object extends AtomicObject {

	/**
	 *
	 * @var ErrorModel
	 */
	protected $error;
	/**
	 *
	 * @var unknown_type
	 */
	protected $userId;
	/**
	 *
	 * @var AtomicObject
	 */
	protected $userObject;
	/**
	 *
	 * @var unknown_type
	 */
	protected $itemId;
	/**
	 *
	 * @var Object
	 */
	protected $itemObject;

	public function __construct($id=0, $db_table=null) {
		if(!$db_table) $db_table = self::db_table();
		parent::__construct($id, $db_table);
	}

	/**
	 * @return string
	 */
	public static function db_table() {
		$db_name_constant = sprintf('%s::%s', get_called_class(), 'MYSQL_DATABASE');
		$MYSQL_DB = defined($db_name_constant) ? constant($db_name_constant) : MYSQL_DATABASE;
		$MYSQL_TABLE = constant(sprintf('%s::%s', get_called_class(), 'MYSQL_TABLE'));
		return sprintf('%s.%s', $MYSQL_DB, $MYSQL_TABLE);
	}

	/**
	 * Custom functionality
	 * @param $field_name : db field name
	 * @param $field_extra_name : add_field name
	 * @param $method_selector : method name to select data to fill add_field
	 * @param $selector_params : array of params for method_selector
	 */
	protected final function extendExtraField($field_name, $field_extra_name, $method_selector, $selector_params=array()) {
		if(!array_key_exists($field_extra_name, $this->extra_fields)) {
			$this->setExtraField($field_extra_name, null);
		}
		$field_extra_value = $this->getExtraField($field_extra_name);
		$field_value = $this->getField($field_name);
		if(empty($field_extra_value) && !is_null($field_value)) {
			if(method_exists($this, $method_selector)) {
				$selector_params = (array)$selector_params;
				array_unshift($selector_params, $field_value);
				$this->setExtraField($field_extra_name, call_user_func_array(array(&$this, $method_selector), $selector_params));
			}
		}
		return $this;
	}

	/**
	 * @return int
	 */
	public final function getUserId() {
		return (int)$this->userId;
	}

	/**
	 *
	 * @param unknown_type $userId
	 */
	public final function setUserId($userId) {
		$this->userId = (int)$userId;
		if($this->userId && is_object($this->userObject) && $this->userObject->getId()!=$this->userId) {
			$this->getUserObject($this->userId);
		}
	}

	/**
	 *
	 * @param unknown_type $userId
	 * @return AtomicObject
	 */
	public function getUserObject($userId=null) {
		$userId = $userId ? (int)$userId : $this->userId;
		if(!is_object($this->userObject) || $this->userObject->getId()!=$userId) {
			$user = new User($userId);
			$this->setUserObject($user->load());
		}
		return $this->userObject;
	}

	/**
	 *
	 * @param AtomicObject $object
	 */
	public final function setUserObject(AtomicObject $object) {
		$this->userId = $object->getId();
		$this->userObject = $object;
	}

	/**
	 * Check if exists user object
	 * @return bool
	 */
	public function isUserObjectExists() {
		return is_object($this->userObject) && $this->userObject->exists();
	}

	/**
	 * @return int
	 */
	public final function getItemId() {
		return (int)$this->itemId;
	}

	/**
	 *
	 * @param unknown_type $itemId
	 */
	public final function setItemId($itemId) {
		$this->itemId = (int)$itemId;
		if($this->itemId && is_object($this->itemObject) && $this->itemObject->getId()!=$this->itemId) {
			$this->getItemObject($this->itemId);
		}
	}

	/**
	 *
	 * @param unknown_type $itemId
	 * @param unknown_type $classname
	 * @return AtomicObject
	 */
	public function getItemObject($itemId=null, $classname='Photo') {
		$itemId = $itemId ? (int)$itemId : $this->getField('item_id');
		if(!is_object($this->itemObject) || $this->itemObject->getId()!=$itemId) {
			$item = new $classname($itemId);
			$this->setItemObject($item->load());
		}
		return $this->itemObject;
	}

	/**
	 *
	 * @param AtomicObject $object
	 */
	public final function setItemObject(Object $object) {
		$this->itemId = $object->getId();
		$this->itemObject = $object;
	}

	/**
	 * Check if exists item object
	 * @return bool
	 */
	public function isItemObjectExists() {
		return is_object($this->itemObject) && $this->itemObject->exists();
	}

	/**
	 * @override
	 * @see AtomicObject::loadExtraData()
	 */
	protected function loadExtraData() {
		$this->setItemId($this->getField('item_id'));
		$this->setUserId($this->getField('user_id'));
	}

	/**
	 * @Should be overrided in derived class which use add()
	 * @param unknown_type $_REQ
	 */
	protected function collectAddFields($_REQ) {
		return array();
	}
	/**
	 * @Should be overrided in derived class which use add()
	 * @param unknown_type $_REQ
	 */
	protected function collectAddRawFields($_REQ) {
		return array();
	}

	/**
	 * @Should be overrided in derived class which use change()
	 * @param unknown_type $_REQ
	 */
	protected function collectChangeFields($_REQ) {
		return array();
	}
	/**
	 * @Should be overrided in derived class which use change()
	 * @param unknown_type $_REQ
	 */
	protected function collectChangeRawFields($_REQ) {
		return array();
	}

	/**
	 * @Should be overrided in derived class which use add()
	 * @param unknown_type $_REQ
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {
		return is_array($_REQ) && !empty($_REQ) || is_array($_REQ_RAW) && !empty($_REQ_RAW);
	}
	/**
	 * @Should be overrided in derived class which use change()
	 * @param unknown_type $_REQ
	 */
	protected function validateChange($_REQ, $_REQ_RAW) {
		return is_array($_REQ) && !empty($_REQ) || is_array($_REQ_RAW) && !empty($_REQ_RAW);
	}

	/**
	 * @May be overrided in derived class which use add()
	 * @param unknown_type $_REQ
	 */
	protected function doAfterAdd($_REQ=null) {
		return true;
	}
	/**
	 * @May be overrided in derived class which use change()
	 * @param unknown_type $_REQ
	 */
	protected function doAfterChange($_REQ=null) {
		return true;
	}

	/**
	 *
	 * @param unknown_type $_REQ
	 * @return bool
	 */
	public final function add($_REQ=null) {

		$_REQ = ifsetor($_REQ, (Predicate::posted() ? $_POST : $_GET));

		$insert = $this->collectAddFields($_REQ);
		$insert_raw = $this->collectAddRawFields($_REQ);

		if($this->validateAdd($insert, $insert_raw) && !$this->isError()) {

			foreach($insert as $field=>$value) {
				$this->setField($field, $value);
			}
			foreach($insert_raw as $field=>$value) {
				$this->setField($field, $value, true);
			}

			$this->save();

			$this->clearError();

			$this->doAfterAdd($_REQ);

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Simulate add
	 * @param unknown_type $_REQ
	 */
	public final function addTest($_REQ=null) {

		$_REQ = ifsetor($_REQ, (Predicate::posted() ? $_POST : $_GET));

		$insert = $this->collectAddFields($_REQ);
		$insert_raw = $this->collectAddRawFields($_REQ);

		return $this->validateAdd($insert, $insert_raw) && !$this->isError();
	}

	/**
	 *
	 * @param unknown_type $_REQ
	 * @return bool
	 */
	public final function change($_REQ=null) {

		$_REQ = ifsetor($_REQ, (Predicate::posted() ? $_POST : $_GET));

		$update = $this->collectChangeFields($_REQ);
		$update_raw = $this->collectChangeRawFields($_REQ);

		if($this->validateChange($update, $update_raw) && !$this->isError()) {

			foreach($update as $field=>$value) {
				$this->setField($field, $value);
			}
			foreach($update_raw as $field=>$value) {
				$this->setField($field, $value, true);
			}

			$this->update();

			$this->clearError();

			$this->doAfterChange($_REQ);

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Simulate change
	 * @param unknown_type $_REQ
	 */
	public final function changeTest($_REQ=null) {

		$_REQ = ifsetor($_REQ, (Predicate::posted() ? $_POST : $_GET));

		$update = $this->collectChangeFields($_REQ);
		$update_raw = $this->collectChangeRawFields($_REQ);

		return $this->validateChange($update, $update_raw) && !$this->isError();
	}

	/**
	 * @return bool
	 */
	public function updateViews() {

		$result = false;

		$clientIp = Network::clientIp();
		$clientFwrd = Network::clientFwrd();

		if($clientIp!=$this->getField('view_ip') || $clientFwrd!=$this->getField('view_fwrd')) {
			$this->setField('view_ip', $clientIp);
			$this->setField('view_fwrd', $clientFwrd);
			$this->setField('view_tstamp', time());
			if(User::isLoginned()) {
				$this->setField('views_user', 'views_user+1', true);
			}
			else {
				$this->setField('views_guest', 'views_guest+1', true);
			}
			$this->setField('views', 'views_user+views_guest', true);

			$result = $this->update();
		}

		return $result;
	}

	/**
	 * @return ErrorModel
	 */
	public function getErrorObject() {
		if(!$this->error) {
			$this->error = new ErrorModel();
		}
		return $this->error;
	}

	/**
	 *
	 * @param ErrorModel $error
	 * @return Object
	 */
	public function setErrorObject(ErrorModel $error) {
		$this->error = $error;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getErrors() {
		return $this->getErrorObject()->getErrors();
	}

	/**
	 *
	 * @param array $errors
	 * @return Object
	 */
	public function setErrors(array $errors) {
		$this->setErrorObject($this->getErrorObject()->setErrors($errors));
		return $this;
	}

	/**
	 * @return array
	 */
	public function getErrorValues() {
		return $this->getErrorObject()->getErrorValues();
	}

	/**
	 *
	 * @param unknown_type $id
	 * @param unknown_type $params
	 * @return Object
	 */
	public function pushError($id, $params=null) {
		$this->setErrorObject($this->getErrorObject()->push($id, $params));
		return $this;
	}

	/**
	 * @return Object
	 */
	public function clearError() {
		$this->error = null;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isError() {
		$error = $this->getErrorObject();
		return $error->isError();
	}

}

/**
 *
 * @author alex
 *
 */
abstract class ObjectCollection extends AtomicObjectCollection {

	/**
	 *
	 * @var AtomicObjectCollection
	 */
	protected $userObjectCollection;
	/**
	 *
	 * @var AtomicObjectCollection
	 */
	protected $itemObjectCollection;

	public function __construct(Object $object) {
		parent::__construct($object);
	}

	/**
	 *
	 * @param unknown_type $itemId
	 * @param unknown_type $where
	 * @param unknown_type $where_raw
	 * @param unknown_type $order
	 * @param unknown_type $limit
	 * @return AtomicObjectCollection
	 */
	public function getCollectionByItemId($itemId, $where=array(), $where_raw=array(), $order=null, $limit=null) {
		$where['item_id'] = $itemId;
		return $this->getCollection($where, $where_raw, $order, $limit);
	}

	/**
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $where
	 * @param unknown_type $where_raw
	 * @param unknown_type $order
	 * @param unknown_type $limit
	 * @return AtomicObjectCollection
	 */
	public function getCollectionByUserId($userId, $where=array(), $where_raw=array(), $order=null, $limit=null) {
		$where['user_id'] = $userId;
		return $this->getCollection($where, $where_raw, $order, $limit);
	}

	/**
	 *
	 * @param unknown_type $itemId
	 * @return bool
	 */
	public function removeByItemId($itemId) {
		return $this->remove(array('item_id'=>$itemId));
	}

	/**
	 *
	 * @param unknown_type $userId
	 * @return bool
	 */
	public function removeByUserId($userId) {
		return $this->remove(array('user_id'=>$userId));
	}

	/**
	 * Custom functionality
	 * @param $field_name : db field name
	 * @param $field_extra_name : add_field name
	 * @param $method_selector : method name to select data to fill add_field
	 * @param $selector_params : array of params for method_selector
	 * @return mixed
	 */
	protected final function extendCollectionExtraField($field_name, $field_extra_name, $method_selector, $selector_params=array()) {

		$result = null;

		$field_id_arr = array();

		foreach($this as $o_id=>$object) {
			array_push($field_id_arr, $object->getField($field_name));
		}

		if($field_id_arr) {
			$field_id_arr = array_unique($field_id_arr);
		}

		if(method_exists($this, $method_selector)) {
			$selector_params = (array)$selector_params;
			array_unshift($selector_params, $field_id_arr);
			$result = call_user_func_array(array(&$this, $method_selector), $selector_params);
			if($result) {
				foreach($this as $o_id=>$object) {
					foreach($result as $f_id=>$f_val) {
						if($object->getField($field_name)==$f_id) {
							$object->setExtraField($field_extra_name, $f_val);
						}
					}
				}
			}
		}

		return $result;
	}

	/**
	 *
	 * @param unknown_type $user_id
	 * @param unknown_type $user_field
	 * @return AtomicObject
	 */
	public function getUserObjectByUserId($user_id, $user_field='user_id') {
		$user = new User($user_id);
		foreach($this as $id=>$object) {
			if($object->getField($user_field)==$user_id) {
				$user = $object->getUserObject($user_id);
				break;
			}
		}
		return $user;
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @param unknown_type $classname
	 * @return AtomicObject
	 */
	public function getItemObjectByItemId($item_id, $classname='Photo') {
		$item = new $classname($item_id);
		foreach($this as $id=>$object) {
			if($object->getField('item_id')==$item_id) {
				$item = $object->getItemObject($item_id, $classname);
				break;
			}
		}
		return $item;
	}

	/**
	 * @return AtomicObjectCollection
	 */
	public function getUserObjectCollection($userIds=array()) {
		if(!$userIds) {
			$userIds = $this->getFieldValues('user_id');
		}
		$user_collection = new UserCollection();
		$this->userObjectCollection = $user_collection->getCollectionById($userIds);
		foreach($this as $id=>$object) {
			foreach($this->userObjectCollection as $user_id=>$user) {
				if($user_id==$object->getField('user_id')) {
					$object->setUserObject($user);
					break;
				}
			}
		}
		return $this->userObjectCollection;
	}

	/**
	 * @return AtomicObjectCollection
	 */
	public function getItemObjectCollection($itemIds=array(), $classname='PhotoCollection') {
		if(!$itemIds) {
			$itemIds = $this->getFieldValues('item_id');
		}
		$item_collection = new $classname();
		$this->itemObjectCollection = $item_collection->getCollectionById($itemIds);
		foreach($this as $id=>$object) {
			foreach($this->itemObjectCollection as $item_id=>$item) {
				if($item_id==$object->getField('item_id')) {
					$object->setItemObject($item);
					break;
				}
			}
		}
		return $this->itemObjectCollection;
	}
}
