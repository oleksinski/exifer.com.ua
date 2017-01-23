<?

class Util {

	public static function CreateFunction($args, $code) {

		static $__functionList = array();
		$key = md5($args.'-'.$code);
		if($key && array_key_exists($key, $__functionList)) {
			$H = $__functionList[$key];
		}
		else {
			$H = create_function($args, $code);
			$__functionList[$key] = $H;
		}

		return $H;
	}

	// ---

	/**
	 * @param array $input [1,2]
	 * @param array $cache [array(1=>data, 2=>data)]
	 * @return array [array(1=>data, 2=>data)]
	 */
	public static function cache_intersect($input, $cache) {

		$result = array();

		$existant = array_keys($cache);
		//_e('---== existant ==---');
		//_e($existant);

		$intersect = array_intersect($input, $existant);
		//_e('---== intersect ==---');
		//_e($intersect);

		$intersect_count = count($intersect);
		$input_count = count($input);

		// get values from inner cache
		if($intersect_count==$input_count) {
			//_e($intersect_count . ' = ' . $input_count);
			foreach($input as $id) {
				if(isset($cache[$id])) {
					$result[$id] = $cache[$id];
				}
			}
		}

		return $result;
	}

	// ---

	/**
	 * Cast input datavase table values to proper php types
	 * Usually used before database insert/update procedures
	 * @param hash_array $values
	 * @param string $db_table [database.table name]
	 * @param boolean $normalize [flag for normalizing input values]
	 * @return hash_array (normalized input $values)
	 */
	public static function cast_dbtable_values($values, $db_table, $normalize=true) {

		$insert_update_arr = array();

		static $db_struct_map = array();
		$db_struct = array();
		if(!array_key_exists($db_table, $db_struct_map)) {
			$__db =& __db();
			$db_struct_map[$db_table] = $db_struct = $__db->describe($db_table);
		}
		else {
			$db_struct = $db_struct_map[$db_table];
		}

		$ascii_arr = array('char', 'varchar', 'enum', 'text', 'binary', 'blob');
		$int_arr = array('int', 'bigint', 'tinyint', 'smallint', 'mediumint');

		if(is_array($values) && is_array($db_struct)) {

			foreach($values as $key=>$value) {

				foreach($db_struct as $db_key=>$db_value) {

					if(_strtolower($key)==_strtolower($db_key)) {

						if($normalize) {

							$db_type = null;

							foreach($int_arr as $int_type) {
								if(_stripos($db_value, $int_type)===0) {
									$db_type = 'INT';
									break;
								}
							}

							//foreach($ascii_arr as $ascii_type) {
								if(is_null($db_type)) {
									$db_type = 'ASCII';
								}
							//}

							if($db_type=='INT') {
								if(_stristr($db_value, 'unsigned')) {
									$value = Cast::unsignint($value);
								}
								else {
									$value = Cast::int($value);
								}
							}
							else {
								$value = Cast::str($value);
							}
						}

						$insert_update_arr[$key] = $value;
						break;
					}
				}
			}
		}

		return $insert_update_arr;
	}

	/**
	 * Perform Basic Authentication
	 * @param array $credentials [login => password, login => password, ...]
	 * @param $http_login
	 * @param $http_password
	 * @return boolean or exit if credentials is not set
	 */
	public static function basic_auth($credentials, $http_login=null, $http_password=null) {

		$access = false;

		$http_login = ifsetor($_SERVER['PHP_AUTH_USER'], $http_login);
		$http_password = ifsetor($_SERVER['PHP_AUTH_PW'], $http_password);

		$login_exists = array_key_exists($http_login, $credentials);

		if($login_exists && $http_password==$credentials[$http_login]) {
			$access = true;
		}
		else {
			if(!headers_sent()) {
				header('WWW-Authenticate: Basic realm="My Realm"');
				header('HTTP/1.0 401 Unauthorized');
			}
			exit();
		}

		return $access;
	}

}
