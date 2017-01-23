<?

class MySQL {

	protected static $singleton; // MySQL instance holder

	protected $resource; // mysql instance

	protected $stopwatch; // timer

	protected $hostname;
	protected $username;
	protected $password;
	protected $database;


	public static function &getInstance($hostname, $username, $password, $database=null) {

		$instance = null;

		if(isset(self::$singleton) && is_array(self::$singleton)) {

			foreach(self::$singleton as &$mysql_o) {

				if(is_object($mysql_o) && is_a($mysql_o, __CLASS__)) {

					$found = true;
					$found = $found && $mysql_o->hostname==$hostname;
					$found = $found && $mysql_o->username==$username;
					$found = $found && $mysql_o->password==$password;

					if($found) {

						//$mysql_o->report(sprintf('MySQL: singleton retrain at %s@%s', $username, $hostname));

						$instance =& $mysql_o; // RETRAIN
						if($instance->database!=$database) {
							$instance->select_db($database);
						}
						break;
					}
				}
			}
		}
		else {
			self::$singleton = array();
		}

		if(is_null($instance)) { // CREATE

			$instance = new MySQL($hostname, $username, $password, $database);

			array_push(self::$singleton, $instance);

			//_e(self::$singleton);
		}

		return $instance;
	}

	protected function __construct($hostname, $username, $password, $database=null) {

		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		//$this->database = $database;

		$this->clearMysqlCache = defined('MYSQL_CACHE_REWRITE') && MYSQL_CACHE_REWRITE;

		$this->stopwatch = new StopWatch();

		$this->resource = @mysql_connect($hostname, $username, $password);

		$this->stopwatch->stop();

		if(is_resource($this->resource)) {

			//$this->report(sprintf('MySQL: connected to %s@%s; %s', $username, $hostname, $this->stopwatch->getFormat(3)));

			//$this->u("SET names utf8");
			//$this->u("SET character_set_server=utf8");
			//$this->u("SET character_set_client=utf8");
			//$this->u("SET character_set_results=utf8");
			//$this->u("SET collation_connection=utf8_general_ci");

			$this->select_db($database);

			//$this->report(sprintf('MySQL: stat[%s@%s]: %s', $username, $hostname, mysql_stat($this->resource)));
		}
		else {
			$this->report(sprintf("MySQL: connection failed to %s@%s \n%s", $username, $hostname, mysql_error()), E_USER_ERROR);
		}
	}


	public function __destruct() {

		$this->stopwatch->start();

		//$this->report(sprintf('MySQL: stat[%s@%s]: %s', $this->username, $this->hostname, mysql_stat($this->resource)));

		$bool = @mysql_close($this->resource);

		$this->stopwatch->stop();

		if($bool) {
			//$this->report(sprintf('MySQL: disconnect from %s@%s; %s', $this->username, $this->hostname, $this->stopwatch->getFormat(3)));
		}
		else {
			$this->report(sprintf("MySQL: disconnect from %s@%s failed \n%s", $this->username, $this->hostname, mysql_error()), E_USER_WARNING);
		}
	}


	public function select_db($database) {

		if($this->resource && $database) {

			$this->database = $database;

			$this->stopwatch->start();

			$select_db = mysql_select_db($database, $this->resource);

			$this->stopwatch->stop();

			if($select_db) {
				//$this->report(sprintf('MySQL: use database %s; %s; %s@%s', $database, $this->stopwatch->getFormat(3), $this->hostname, $this->username));
			}
			else {
				$this->report(sprintf("MySQL: failed use database %s  \n%s", $database, mysql_error()), E_USER_WARNING);
			}
		}
	}


	private function execute($sql) {

		$this->stopwatch->start();

		$result = @mysql_query($sql, $this->resource);

		$this->stopwatch->stop();

		if($result) {
			$this->report(sprintf('%s; ar: %d; %s; host: %s', $sql, mysql_affected_rows(), $this->stopwatch->getFormat(3), $this->hostname));

			//$mysql_info = @mysql_info($this->resource);
			//if($mysql_info) {
			//	$this->report(sprintf('SQL Info: %s', $mysql_info));
			//}
		}
		else {
			$this->report(sprintf("%s\nSQL Error:%s, %s", $sql, mysql_errno(), mysql_error()), E_USER_WARNING);
		}

		return $result;
	}


	public function q($sql, $cache_lifetime_secs=0, &$total=0) {

		$result = null;

		$sql = $sql_calc = _trim($sql);

		if($total===null) {
			$sql_calc = _str_ireplace('SELECT ', 'SELECT SQL_CALC_FOUND_ROWS ', $sql);
		}

		// caching //
		$useCaching = is_int($cache_lifetime_secs) && $cache_lifetime_secs>0;

		if($useCaching) {

			$__storage =& __storage();

			if(!$this->clearMysqlCache) {

				$this->stopwatch->start();

				$cache = $__storage->get($sql_calc);

				if(isset($cache['result']) && is_object($cache['result']) && isset($cache['total'])) {
					$result = $cache['result'];
					$total = $cache['total'];
				}

				$this->stopwatch->stop();

				if(!is_null($result)) {
					$this->report(sprintf('cache: %s; ar: %d; %s;', $sql, $result->getFetchSize(), $this->stopwatch->getFormat(3)));
				}
			}
			else {
				$__storage->del($sql_calc);
			}
		}

		if(is_null($result)) {

			$query_result = $this->execute($sql_calc);

			if(strcmp($sql, $sql_calc)!=0) {
				$sqlResult = $this->row('SELECT FOUND_ROWS() AS total');
				$total = $sqlResult['total'];
			}

			if($useCaching) {

				$__storage =& __storage();

				$result = new ResultSetCache($query_result);

				$cache = array();
				$cache['result'] =& $result;
				$cache['total'] =& $total;

				$this->report(sprintf('cache: %s; lifetime: %s sec', $sql, $cache_lifetime_secs));

				$__storage->set($sql_calc, $cache, $cache_lifetime_secs);
			}
			else {
				$result = new ResultSet($query_result);
			}
		}

		return $result;
	}


	public function row($sql, $cache_lifetime_secs=0) {

		$result = $this->q($sql, $cache_lifetime_secs);
		$row = $result->next();
		$result->close();

		return $row;
	}


	public function u($sql) {

		$result = $this->execute($sql);

		$affected = $result ? mysql_affected_rows($this->resource) : 0;

		return $affected;
	}


	public function last_insert_id() {

		return @mysql_insert_id($this->resource);
	}

	public function describe($db_table) {

		$result = array();
		$sql_r = $this->q(sprintf('DESCRIBE %s', $db_table));

		while($row=$sql_r->next()) {

			$field = $row['Field'];
			$type = $row['Type'];
			$default = $row['Default'];
			$null = $row['Null'];

			$type_sql = $type;
			if($null=='YES') {
				$type_sql .= ' default NULL';
			}
			elseif($null=='NO') {
				$type_sql .= ' NOT NULL';
				if($default!==null && $default !=='') {
					$type_sql .= sprintf(' default %s', self::str($default));
				}
			}

			$result[$field] = $type_sql;
		}
		return $result;
	}

	public static function prepare_fields($fields=array(), $fields_raw=array()) {

		$result = '';

		$sql = array();

		if(is_array($fields) && !empty($fields)) {
			foreach($fields as $field=>$value) {
				$sql[] = $field.'='.self::sql_data_prepare($value);
			}
		}
		if(is_array($fields_raw) && !empty($fields_raw)) {
			foreach($fields_raw as $field=>$value) {
				$sql[] = $field.'='.$value;
			}
		}

		$result = implode(', ', $sql);

		return $result;
	}

	public static function sql_data_prepare($value) {
		if(is_numeric($value)) {
			//
		}
		elseif(is_null($value)) {
			$value = 'NULL';
		}
		else {
			$value = self::str($value);
		}
		return $value;
	}

	public static function escape($string) {
		if(0 && function_exists('mysql_real_escape_string')) {
			return mysql_real_escape_string($string);
		}
		else {
			return mysql_escape_string($string);
		}
	}


	public static function str($string) {
		return '\'' . self::escape($string) . '\'';
	}

	/**
	 *
	 * @param array $where=array('item_id'=>0)
	 * @param array $where_raw=array('item_id>0','user_id IS NULL', '(item_id=1 OR item_id=2)')
	 * @return array(item_id=0, 'item_id>0', ...)
	 */
	public static function getWhereSqlArr($where=array(), $where_raw=array()) {

		$where_arr = array();

		if(is_array($where)) {
			foreach($where as $field=>$value) {
				if(is_array($value) && !empty($value)) {
					$in_arr = array();
					foreach($value as $v) {
						$in_arr[] = ctype_digit((string)$v) ? (int)$v : self::str($v);
					}
					if(!empty($in_arr)) {
						$where_arr[] = self::sqlInClause($field, $in_arr);
					}
				}
				elseif(is_scalar($value) && $value) {
					$value = ctype_digit((string)$value) ? (int)$value : self::str($value);
					$where_arr[] = sprintf('%s=%s', $field, $value);
				}
				else {
					$where_arr[] = sprintf('%s=%u', $field, $value);
				}
			}
		}

		if(is_array($where_raw)) {
			$where_arr = array_merge($where_arr, $where_raw);
		}

		return $where_arr;
	}

	/**
	 * Generate SQL "IN" clause:
	 *   if is_array($values) return SQL "IN" clause
	 *   else return SQL "=" clause
	 * Does not check for input params.
	 * Developer should take care by himself about inserting prepared escaped data into SQL.
	 *
	 * @param string $field
	 * @param array|int $values
	 * @return string [sql-clause]
	 */
	public static function sqlInClause($field, $values) {

		$sql = '0';

		$field = Cast::str($field);

		if($field) {

			if(is_array($values)) {

				foreach($values as &$value) {
					$value = self::sql_data_prepare($value);
				}

				$count = count($values);

				if($count==1) {
					$sql = $field.'='.reset($values);
				}
				elseif($count>1) {
					$sql = $field.' IN('.implode(', ', $values).')';
				}
			}
			elseif(is_scalar($values)) {
				$sql = $field.'='.$values;
			}
		}

		return $sql;
	}

	public static function sqlLimit($pageNum=0, $onPage=10) {

		$pageNum = Cast::int($pageNum); if($pageNum<0) $pageNum=0;
		$onPage = Cast::int($onPage); if($onPage<0) $onPage=0;

		$start = $pageNum * $onPage;
		$end = $onPage;

		return sprintf('%d, %d', $start, $end);
	}

	private function report($what, $error=E_USER_NOTICE) {

		static $func_exists = null;
		if(is_null($func_exists)) {
			$func_exists = function_exists('_e');
		}
		if($func_exists) {
			_e(self::sqlPrepare($what), $error);
		}
	}

	private static function sqlPrepare($sql) {
		//return $sql;
		if(_stristr(_ltrim($sql), 'SELECT') || _stristr(_ltrim($sql), 'DELETE')) {
			$space = ' ';
			$sql = _trim(_str_replace(array("\t", "\n"), $space, $sql));
			$sql = _preg_replace("/\s{2,}/", $space, $sql);
			//$sql = _str_replace(array(_str_repeat($space, 2), _str_repeat($space, 3)), $space, $sql);
		}
		return $sql;
	}
}


class ResultSet {

	private $resource;
	private $row;

	public function __construct($resource) {
		$this->resource = &$resource;
	}

	public function __destruct() {
		$this->close();
	}

	public function close() {
		if(is_resource($this->resource)) {
			mysql_free_result($this->resource);
		}
	}

	public function getFetchSize() {
		if(is_resource($this->resource)) {
			return mysql_num_rows($this->resource);
		}
		else {
			return 0;
		}
	}

	public function &next() {
		$result = null;
		if(is_resource($this->resource)) {
			$this->row = mysql_fetch_assoc($this->resource);
			$result =& $this->row;
		}
		return $result;
	}

	public function first() {
		if(is_resource($this->resource)) {
			$this->row = null;
			return mysql_data_seek($this->resource, 0);
		}
	}

	public function get($key) {
		$result = null;
		if(isset($this->row[$key])) {
			$result = $this->row[$key];
		}
		return $result;
	}
}

class ResultSetCache {

	public $mysql_data;
	public $row;
	public $cur_index;

	public function __construct($resource) {
		$this->mysql_data = array();
		$this->cur_index = 0;
		if(is_resource($resource)) {
			while($row = mysql_fetch_assoc($resource)) {
				$this->mysql_data[] = $row;
			}
		}
	}

	public function __destruct() {
		$this->close();
	}

	public function close() {
		$this->mysql_data = array();
	}

	public function getFetchSize() {
		return count($this->mysql_data);
	}

	public function &next() {
		$result = null;
		if(array_key_exists($this->cur_index, $this->mysql_data)) {
			$this->row = $this->mysql_data[$this->cur_index];
			$result =& $this->row;
			$this->cur_index++;
		}
		return $result;
	}


	public function first() {
		$cur_index = $this->cur_index;
		$this->cur_index = 0;
		$result =& $this->next();
		$this->cur_index = $cur_index;
		return $result;
	}

	public function get($key) {
		$result = null;
		if(isset($this->row[$key])) {
			$result = $this->row[$key];
		}
		return $result;
	}

}
