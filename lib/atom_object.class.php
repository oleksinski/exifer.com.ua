<?

/**
 *
 * @author alex
 *
 */
abstract class AtomicObject {

	/**
	 *
	 * @var unknown_type
	 */
	protected $db_fields=array();
	/**
	 *
	 * @var unknown_type
	 */
	protected $db_fields_modified=array();
	/**
	 *
	 * @var unknown_type
	 */
	protected $db_fields_modified_raw=array();
	/**
	 *
	 * @var unknown_type
	 */
	protected $extra_fields=array();
	/**
	 *
	 * @var unknown_type
	 */
	protected $custom_fields=array();
	/**
	 *
	 * @var unknown_type
	 */
	protected $loaded;
	/**
	 *
	 * @var unknown_type
	 */
	protected $insert_ignore;
	/**
	 *
	 * @var unknown_type
	 */
	protected $insert_delayed;
	/**
	 *
	 * @var unknown_type
	 */
	protected $update_duplicate;
	/**
	 *
	 * @var unknown_type
	 */
	protected $db_choice;
	/**
	 *
	 * @var unknown_type
	 */
	protected $db_table;
	/**
	 *
	 * @var unknown_type
	 */
	protected $id;
	/**
	 *
	 * @var unknown_type
	 */
	protected $id_field='id';

	/**
	 * @return string
	 */
	public function getCollectionClass() {
		return get_class($this).'Collection';
	}

	/**
	 *
	 */
	protected function _init() {}

	public function __construct($id=0, $db_table=null) {
		$this->id = intval($id);
		if($db_table) {
			$this->db_table = $db_table;
		}
		$this->setDbChoice();
		$this->_init();
	}

	public function __toString() {
		return (string)($this->exists() ? $this->getId() : '');
	}

	/**
	 * magic method - called before serializing
	 * @return array
	 */
	public function __sleep() {
		return array(
			'db_fields', 'custom_fields',
			'insert_ignore', 'insert_delayed', 'update_duplicate',
			'db_choice', 'db_table', 'id_field'
		);
	}

	/**
	 * @return void
	 */
	public function __wakeup() {
		$this->populate($this->db_fields);
	}

	/**
	 *
	 */
	public final function getConstructId() {
		return (int)$this->id;
	}

	/**
	 * @return int
	 */
	public final function getId() {
		return (int)$this->getField($this->id_field);
	}

	/**
	 *
	 * @param unknown_type $id_field
	 */
	public function setIdField($id_field) {
		$this->id_field = $id_field;
	}

	/**
	 * @return string
	 */
	public function getIdField() {
		return $this->id_field;
	}

	/**
	 * Retrieve MySQL handler
	 * @param void
	 * @return MySQL $__db
	 */
	public function &db() {
		return __db($this->db_choice);
	}

	/**
	 *
	 * @param unknown_type $db_choice
	 */
	public function setDbChoice($db_choice=1) {
		$this->db_choice = (int)$db_choice;
	}

	/**
	 * @return int
	 */
	public function getDbChoice() {
		return $this->db_choice;
	}

	/**
	 *
	 * @param unknown_type $bool
	 */
	public function setInsertIgnore($bool=true) {
		$this->insert_ignore = (bool)$bool;
	}

	/**
	 *
	 * @param unknown_type $bool
	 */
	public function setInsertDelayed($bool=true) {
		$this->insert_delayed = (bool)$bool;
	}

	/**
	 *
	 * @param unknown_type $bool
	 */
	public function setUpdateOnDuplicate($bool=true) {
		$this->update_duplicate = (bool)$bool;
	}

	/**
	 * @return string
	 */
	public function getDbTable() {
		return $this->db_table;
	}

	/**
	 *
	 */
	protected function loadExtraFields() {
		$this->extra_fields = array();
	}

	/**
	 *
	 */
	protected function loadExtraData() {}

	/**
	 *
	 */
	private final function load_postfix() {
		$this->forceLoaded();
		$this->loadExtraFields();
		$this->loadExtraData();
	}

	/**
	 *
	 * @param unknown_type $where
	 * @param unknown_type $where_raw
	 * @return AtomicObject
	 */
	public final function load($where=array(), $where_raw=array()) {
		if(!$this->loaded()) {
			$this->reload($where, $where_raw);
		}
		return $this;
	}

	/**
	 *
	 * @param unknown_type $where
	 * @param unknown_type $where_raw
	 * @return AtomicObject
	 */
	public final function reload($where=array(), $where_raw=array()) {
		if(empty($where) && empty($where_raw) && $this->id) {
			$where = array($this->id_field=>$this->id);
		}
		$where_arr = MySQL::getWhereSqlArr($where, $where_raw);
		if($where_arr) {
			$sql = sprintf('SELECT * FROM %s WHERE %s LIMIT 1', $this->db_table, implode(' AND ', $where_arr));
			$this->db_fields = $this->db()->row($sql);
			$this->id = (int)($this->db_fields[$this->id_field]);
			$this->load_postfix();
		}
		return $this;
	}

	/**
	 *
	 * @param array $row
	 * @return AtomicObject
	 */
	public final function populate(array $row=array()) {
		$this->db_fields = $row;
		$this->id = (int)($this->db_fields[$this->id_field]);
		$this->load_postfix();
		return $this;
	}

	/**
	 *
	 */
	public final function forceLoaded() {
		//foreach($this->db_fields_modified_raw as $field=>$value) {
		//	$this->db_fields[$field] = $value;
		//}
		foreach($this->db_fields_modified as $field=>$value) {
			$this->db_fields[$field] = $value;
		}
		$this->db_fields_modified = $this->db_fields_modified_raw = array();
		$this->loaded = true;
	}

	/**
	 * @return array
	 */
	public final function getFields() {
		$this->load();
		return $this->db_fields;
	}

	/**
	 *
	 * @param string $field
	 * @return mixed
	 */
	public final function getField($field) {
		$this->load();
		return isset($this->db_fields[$field]) ? $this->db_fields[$field] : null;
	}

	/**
	 *
	 * @param string $field
	 * @param mix $value
	 * @param bool $is_raw
	 * @return AtomicObject
	 */
	public final function setField($field, $value, $is_raw=false) {
		if($is_raw==true) {
			$this->db_fields_modified_raw[$field] = $value;
		}
		else {
			$this->db_fields_modified[$field] = $value;
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public final function getExtraFields() {
		return $this->extra_fields;
	}

	/**
	 *
	 * @param bool $field
	 * @return mix
	 */
	public final function getExtraField($field) {
		$this->load();
		return isset($this->extra_fields[$field]) ? $this->extra_fields[$field] : null;
	}

	/**
	 *
	 * @param string $field
	 * @param mix $value
	 * @return AtomicObject
	 */
	public final function setExtraField($field, $value) {
		$this->extra_fields[$field] = $value;
		return $this;
	}

	/**
	 * @return array
	 */
	public final function getCustomFields() {
		return $this->custom_fields;
	}

	/**
	 *
	 * @param array $fields
	 * @return AtomicObject
	 */
	public final function setCustomFields(array $fields) {
		$this->custom_fields = $fields;
		return $this;
	}

	/**
	 *
	 * @param string $field
	 * @return mix
	 */
	public function getCustomField($field) {
		return ifsetor($this->custom_fields[$field], null);
	}

	/**
	 *
	 * @param string $field
	 * @param mix $value
	 * @return AtomicObject
	 */
	public function setCustomField($field, $value) {
		$this->custom_fields[$field] = $value;
		return $this;
	}

	protected function beforeInsert(){}
	protected function afterInsert(){}

	protected function beforeUpdate(){}
	protected function afterUpdate(){}

	protected function beforeReplace(){}
	protected function afterReplace(){}

	protected function beforeRemove(){}
	protected function afterRemove(){}

	public final function insert() {
		$id = 0;
		if($this->insert_prefix()) {
			$insert_sql = MySQL::prepare_fields($this->db_fields_modified, $this->db_fields_modified_raw);
			if($insert_sql) {
				$insert_statement = $this->insert_ignore ? 'INSERT IGNORE' : 'INSERT';
				$insert_statement = $this->insert_delayed ? ($insert_statement.' DELAYED') : $insert_statement;
				$on_duplicate = $this->update_duplicate ? sprintf(' ON DUPLICATE KEY UPDATE %s', $insert_sql) : '';
				$this->db()->u(sprintf('%s INTO %s SET %s%s', $insert_statement, $this->db_table, $insert_sql, $on_duplicate));
				$id = $this->id = $this->db_fields_modified[$this->id_field] = $this->db_fields_modified_raw[$this->id_field] = $this->db()->last_insert_id();
				$this->insert_postfix();
			}
		}
		return $id;
	}

	/**
	 * @return bool
	 */
	public final function update() {
		$result = false;
		if($this->update_prefix()) {
			$update_sql = MySQL::prepare_fields($this->db_fields_modified, $this->db_fields_modified_raw);
			if($update_sql) {
				//$is_raw_fields = !empty($this->db_fields_modified_raw);
				$result = (bool)$this->db()->u(sprintf('UPDATE IGNORE %s SET %s WHERE %s=%u', $this->db_table, $update_sql, $this->id_field, $this->id));
				$this->db_fields_modified[$this->id_field] = $this->db_fields_modified_raw[$this->id_field] = $this->id;
				$this->update_postfix();
				// reload object to have actual data of raw fields
				//if($is_raw_fields) {
				//	$this->reload();
				//}
			}
		}
		return $result;
	}

	/**
	 * @return bool
	 */
	public final function save() {
		return $this->id ? $this->update() : $this->insert();
	}

	/**
	 * @return bool
	 */
	public final function replace() {
		$result = false;
		if($this->replace_prefix()) {
			$where_sql = MySQL::prepare_fields($this->db_fields_modified, $this->db_fields_modified_raw);
			if($replace_sql) {
				//$is_raw_fields = !empty($this->db_fields_modified_raw);
				$result = (bool)$this->db()->u(sprintf('REPLACE INTO %s SET %s', $this->db_table, $replace_sql));
				$this->db_fields_modified[$this->id_field] = $this->db_fields_modified_raw[$this->id_field] = $this->id;
				$this->replace_postfix();
				// reload object to have actual data of raw fields
				//if($is_raw_fields) {
				//	$this->reload();
				//}
			}
		}
		return $result;
	}

	/**
	 * @return int
	 */
	public final function remove() {
		$result = 0;
		if($this->remove_prefix()) {
			$result = $this->db()->u(sprintf('DELETE FROM %s WHERE %s=%u', $this->db_table, $this->id_field, $this->id));
			$this->remove_postfix();
		}
		return $result;
	}

	/**
	 * @return int
	 */
	public function removeById() {
		return $this->remove();
	}

	/**
	 * @return bool
	 */
	private final function save_prefix() {
		$this->db_fields_modified = Util::cast_dbtable_values($this->db_fields_modified, $this->db_table);
		$this->db_fields_modified_raw = Util::cast_dbtable_values($this->db_fields_modified_raw, $this->db_table, false);
		return count($this->db_fields_modified+$this->db_fields_modified_raw);
	}

	private final function save_postfix() {
		$this->load_postfix();
	}

	private final function insert_prefix() {
		$this->beforeInsert();
		return $this->save_prefix();
	}

	private final function insert_postfix() {
		$this->save_postfix();
		$this->afterInsert();
	}

	private final function update_prefix() {
		$this->beforeUpdate();
		return $this->save_prefix();
	}

	private final function update_postfix() {
		$this->save_postfix();
		$this->afterUpdate();
	}

	private final function replace_prefix() {
		$this->beforeReplace();
		return $this->save_prefix();
	}

	private final function replace_postfix() {
		$this->save_postfix();
		$this->afterReplace();
	}

	private final function remove_prefix() {
		$bool = $this->exists();
		if($bool) {
			$this->beforeRemove();
		}
		return $bool;
	}

	private final function remove_postfix() {
		$this->db_fields = $this->db_fields_modified = $this->db_fields_modified_raw = array();
		$this->loaded = false;
		$this->afterRemove();
	}

	/**
	 * @return bool
	 */
	public final function loaded() {
		return $this->loaded;
	}

	/**
	 * @return bool
	 */
	public final function exists() {
		return $this->getId()>0;
	}
}

/**
 * AtomicObject Collection
 * Realizes Iterator, so it can be used as array
 */
abstract class AtomicObjectCollection extends AtomicObjectIteratorCollection {

	/**
	 *
	 * @var unknown_type
	 */
	private $object;
	/**
	 *
	 * @var unknown_type
	 */
	private $objectClass;
	/**
	 *
	 * @var unknown_type
	 */
	protected $db_choice;
	/**
	 *
	 * @var unknown_type
	 */
	protected $id_field;
	/**
	 *
	 * @var unknown_type
	 */
	protected $db_table;
	/**
	 *
	 * @var unknown_type
	 */
	protected $sql_calc_found_rows;
	/**
	 *
	 * @var unknown_type
	 */
	protected $found_rows_count;
	/**
	 *
	 */
	protected function init() {}

	/**
	 *
	 * @param AtomicObject $object
	 */
	public function __construct(AtomicObject $object) {
		//$this->object = clone $object;
		$this->objectClass = get_class($object);
		$this->db_choice = $object->getDbChoice();
		$this->db_table = $object->getDbTable();
		$this->id_field = $object->getIdField();
		//$this->init();
	}

	/**
	 * @return int length
	 */
	public function __toString() {
		return (string)($this->length() ? $this->length() : '');
	}

	/**
	 * Retrieve MySQL handler
	 * @param void
	 * @return MySQL $__db
	 */
	public function &db() {
		return __db($this->db_choice);
	}

	/**
	 *
	 * @param unknown_type $bool
	 */
	public function setSqlCalcFoundRows($bool=true) {
		$this->sql_calc_found_rows = (bool)$bool;
	}

	public function getSqlCalcFoundRows() {
		return (bool)$this->sql_calc_found_rows;
	}

	public function getFoundRowsCount() {
		return (int)$this->found_rows_count;
	}

	/**
	 *
	 * @param array $db_row
	 */
	public final function populateObject(array $db_row) {
		$object = new $this->objectClass();
		$object->populate($db_row);
		$this->addItem($db_row[$this->id_field], $object);
	}

	/**
	 *
	 * @param unknown_type $id_arr
	 */
	public final function getCollectionById($id_arr) {
		$id_arr = (array)$id_arr;
		foreach($id_arr as $k=>&$v) {
			$v = (int)$v;
		}
		$id_arr = array_unique($id_arr);
		if($id_arr) {
			$this->getCollectionBySql(
				sprintf('SELECT * FROM %s WHERE %s ORDER BY FIELD(%s, %s)',
					$this->db_table,
					MySQL::sqlInClause($this->id_field, $id_arr),
					$this->id_field,
					implode(', ', $id_arr)
				)
			);
		}
		return $this;
	}

	/**
	 *
	 * @param unknown_type $sql
	 * @return AtomicObjectCollection
	 */
	public final function getCollectionBySql($sql) {

		if($this->getSqlCalcFoundRows()) {
			$sql_r = $this->db()->q($sql, 0, $total);
			$this->found_rows_count = (int)$total;
		}
		else {
			$sql_r = $this->db()->q($sql);
			$this->found_rows_count = 0;
		}
		while($row = $sql_r->next()) {
			$this->populateObject($row);
		}
		return $this;
	}

	/**
	 *
	 * @param unknown_type $where
	 * @param unknown_type $where_raw array(field>0)
	 * @param unknown_type $order 'DESC', array('field'=>'DESC'), array(array('field_1'=>'DESC'), array('field_2'=>'DESC'))
	 * @param unknown_type $limit 7, array(0, 7)
	 * @return AtomicObjectCollection
	 */
	public final function getCollection($where=array(), $where_raw=array(), $order=null, $limit=null) {

		$this->clear();

		$where_sql = '';
		$order_sql = '';
		$limit_sql = '';

		$where_arr = MySQL::getWhereSqlArr($where, $where_raw);

		if($where_arr) {
			$where_sql = 'WHERE '.implode(' AND ', $where_arr);
		}

		if($order) {
			$order_arr = array();
			if(is_string($order)) {
				$order = array($this->id_field=>$order);
			}
			if(is_array($order)) {
				foreach($order as $k=>$v) {
					if(is_scalar($k) && is_scalar($v)) {
						if(is_numeric($k)) $k = $this->id_field;
						$order_arr[$k] = $v;
					}
					elseif(is_scalar($k) && is_array($v)) {
						foreach($v as $k1=>$v1) {
							if(is_scalar($k1) && is_scalar($v1)) {
								if(is_numeric($k1)) $k1 = $this->id_field;
								$order_arr[$k1] = $v1;
							}
						}
					}
				}
				$order_sql_arr = array();
				foreach($order_arr as $order_field=>$order_method) {
					$order_method = _strtoupper($order_method);
					if(!in_array($order_method, array('ASC', 'DESC'))) $order_method='DESC';
					$order_sql_arr[] = sprintf('%s %s', $order_field, $order_method);
				}
				if($order_sql_arr) {
					$order_sql = sprintf('ORDER BY %s', implode(', ', $order_sql_arr));
				}
			}
		}
		else {
			$order_sql = sprintf('ORDER BY %s ASC', $this->id_field);
		}

		if(is_scalar($limit)) {
			$limit = array(0, $limit);
		}
		if(isset($limit[0]) && isset($limit[1]) && $limit[1]) {
			$limit_sql = sprintf('LIMIT %s', MySQL::sqlLimit($limit[0], $limit[1]));
		}

		$sql = sprintf('SELECT * FROM %s %s %s %s', $this->db_table, $where_sql, $order_sql, $limit_sql);

		return $this->getCollectionBySql($sql);
	}

	/**
	 *
	 * @param unknown_type $where
	 * @param unknown_type $where_raw
	 * @return int
	 */
	public final function getCount($where=array(), $where_raw=array()) {
		$where_arr = MySQL::getWhereSqlArr($where, $where_raw);
		$where_sql = $where_arr ? sprintf('WHERE %s', implode(' AND ', $where_arr)) : '';
		$sql = sprintf('SELECT COUNT(*) AS cnt FROM %s %s', $this->db_table, $where_sql);
		$sql_r = $this->db()->row($sql);
		return $sql_r['cnt'];
	}

	/**
	 *
	 * @param unknown_type $field
	 * @param unknown_type $where
	 * @param unknown_type $where_raw
	 * @return array
	 */
	public final function getCountAggregated($field, $where=array(), $where_raw=array()) {
		$where_arr = MySQL::getWhereSqlArr($where, $where_raw);
		$where_sql = $where_arr ? sprintf('WHERE %s', implode(' AND ', $where_arr)) : '';
		$sql = sprintf('SELECT %1$s, COUNT(*) AS cnt FROM %2$s %3$s GROUP BY %1$s', $field, $this->db_table, $where_sql);
		$sql_r = $this->db()->q($sql);
		$result = array();
		while($row=$sql_r->next()) {
			$result[$row[$field]] = $row['cnt'];
		}
		return $result;
	}

	protected function beforeRemove(){}
	protected function afterRemove(){}

	/**
	 *
	 * @param unknown_type $where
	 * @param unknown_type $where_raw
	 * @return bool
	 */
	private final function remove_prefix($where=array(), $where_raw=array()) {
		$bool = is_array($where)&&!empty($where) || is_array($where_raw)&&!empty($where_raw);
		if($bool) {
			$this->clear();
			$this->beforeRemove();
		}
		return $bool;
	}

	/**
	 * @return void
	 */
	private final function remove_postfix() {
		$this->clear();
		$this->afterRemove();
	}

	/**
	 *
	 * @param unknown_type $where
	 * @param unknown_type $where_raw
	 * @return bool
	 */
	protected final function remove($where=array(), $where_raw=array()) {
		$result = 0;
		if($this->remove_prefix($where, $where_raw)) {
			$this->getCollection($where, $where_raw);
			$result = $this->length();
			foreach($this as $id=>$object) {
				$object->removeById();
			}
			$this->remove_postfix();
		}
		return $result;
	}

	/**
	 * @TODO
	 * @return bool
	 */
	public final function removeAll() {
		$result = 0;
		$this->beforeRemove();
		while(true) {
			$sql = sprintf('SELECT * FROM %1$s ORDER BY %2$s ASC LIMIT 100', $this->db_table, $this->id_field);
			$sql_r = $this->db()->q($sql);
			if($sql_r->getFetchSize()) {
				while($db_row=$sql_r->next()) {
					$this->populateObject($db_row);
					foreach($this as $id=>$object) {
						if($object->removeById()) {
							$result++;
						}
					}
				}
			}
			else {
				if($this->getCount()==0) {
					$this->db()->u(sprintf('TRUNCATE TABLE %s', $this->db_table));
				}
				break;
			}
		}
		$this->remove_postfix();
		return $result;
	}

	/**
	 *
	 * @param unknown_type $field
	 * @return array
	 */
	public final function getFieldValues($field) {
		$result = array();
		foreach($this as $id=>$object) {
			$result[$id] = $object->getField($field);
		}
		return $result;
	}

}
