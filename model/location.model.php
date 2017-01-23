<?

class LocationModel {

	const CITY_STATIC_PATH = '/location/city.static.php';
	const COUNTRY_STATIC_PATH = '/location/country.static.php';
	const STATE_STATIC_PATH = '/location/state.static.php';

	const COUNTRY_FIELDS = 'co.id, co.name_en, co.name_ru, co.name_ua, co.name_url, co.capital_id, co.active';
	const CITY_FIELDS = 'ci.id, ci.country_id, ci.state_id, ci.name_en, ci.name_ru, ci.name_ua, ci.name_url, ci.is_capital, ci.is_main, ci.active';
	const STATE_FIELDS = 's.id, s.country_id, s.capital_id, s.name_en, s.name_ru, s.name_ua, s.name_url, s.active';

	const NULL_COUNTRY = 0;
	const NULL_CITY = 0;
	const NULL_STATE = 0;

	const DEF_COUNTRY = 1; // Ukraine
	const DEF_CITY = 167; // Kiev
	const DEF_STATE = 10; // Kiev obl

	const CO_INACTIVE = 0;
	const CO_ACTIVE = 1;
	const CO_MIX = 2;

	const CI_INACTIVE = 0;
	const CI_ACTIVE = 1;
	const CI_MIX = 2;

	const S_INACTIVE = 0;
	const S_ACTIVE = 1;
	const S_MIX = 2;

	private static $cityList = null;
	private static $countryList = null;
	private static $stateList = null;

	// ---

	public static function db_city() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'location_city');
	}
	public static function db_country() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'location_country');
	}
	public static function db_state() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'location_state');
	}

	// ---

	public static function file_country() {
		return STATIC_PATH.self::COUNTRY_STATIC_PATH;
	}
	public static function file_city() {
		return STATIC_PATH.self::CITY_STATIC_PATH;
	}
	public static function file_state() {
		return STATIC_PATH.self::STATE_STATIC_PATH;
	}

	// ---

	public static function getPropertyCityList() {
		return self::$cityList;
	}

	public static function getPropertyCountryList() {
		return self::$countryList;
	}

	public static function getPropertyStateList() {
		return self::$stateList;
	}

	// ---

	/**
	 * Get Custom Status Country List From Database
	 * @param $co_status
	 * @return array
	 */
	public static function &GetCustomCountryList($co_status=self::CO_MIX) {

		$List = array();

		$__db =& __db();

		$where_arr = array();

		if($co_status==self::CO_MIX) {
			$where_arr[] = '1';
		}
		else {
			$where_arr[] = sprintf('co.active=%u', $co_status);
		}

		$where_sql = implode(' AND ', $where_arr);

		$sql = sprintf(
			'SELECT %1$s FROM %2$s AS co WHERE %3$s ORDER BY co.id ASC',
			self::COUNTRY_FIELDS, self::db_country(), $where_sql
		);

		$sql_r = $__db->q($sql);

		while($row=$sql_r->next()) {
			$List[$row['id']] = $row;
		}

		self::LocaleNameLocation($List);

		return $List;
	}

	// ---

	/**
	 * Get Custom Status City List From Database
	 * @param $ci_status
	 * @param $co_active
	 */
	public static function &GetCustomCityList($ci_status=self::CI_MIX, $co_status=self::CO_MIX) {

		$List = array();

		$__db =& __db();

		$where_arr = array();

		if($ci_status==self::CI_MIX) {
			$where_arr[] = '1';
		}
		else {
			$where_arr[] = sprintf('ci.active=%u', $ci_status);
		}

		if($co_status==self::CO_MIX) {
			$where_arr[] = '1';
		}
		else {
			$where_arr[] = sprintf('co.active=%u', $co_status);
		}

		$where_sql = implode(' AND ', $where_arr);

		$sql = sprintf('
			SELECT %1$s FROM %2$s AS ci JOIN %3$s AS co ON co.id=ci.country_id
			WHERE %4$s
			ORDER BY ci.id ASC
			',self::CITY_FIELDS, self::db_city(), self::db_country(), $where_sql
		);

		$sql_r = $__db->q($sql);

		while($row=$sql_r->next()) {
			$List[$row['id']] = $row;
		}

		self::LocaleNameLocation($List);

		return $List;
	}

	// ---

	/**
	 * Get Custom Status State List From Database
	 * @param $s_status
	 * @param $co_status
	 */
	public static function &GetCustomStateList($s_status=self::S_MIX, $co_status=self::CO_MIX) {

		$List = array();

		$__db =& __db();

		$where_arr = array();

		if($s_status==self::S_MIX) {
			$where_arr[] = '1';
		}
		else {
			$where_arr[] = sprintf('s.active=%u', $s_status);
		}

		if($co_status==self::CO_MIX) {
			$where_arr[] = '1';
		}
		else {
			$where_arr[] = sprintf('co.active=%u', $co_status);
		}

		$where_sql = implode(' AND ', $where_arr);

		$sql = sprintf('
			SELECT %1$s FROM %2$s AS s JOIN %3$s AS co ON co.id=s.country_id
			WHERE %4$s
			ORDER BY s.id ASC
			',self::STATE_FIELDS, self::db_state(), self::db_country(), $where_sql
		);

		$sql_r = $__db->q($sql);

		while($row=$sql_r->next()) {
			$List[$row['id']] = $row;
		}

		self::LocaleNameLocation($List);

		return $List;
	}

	// ---

	/**
	 * Get Country List From Database Or Internal Model Static Var
	 * @param boolean $refreshStatic
	 * @return array
	 */
	public static function &GetCountryList($refreshCache=false) {

		if(is_null(self::$countryList) || $refreshCache) {
			self::$countryList =& self::GetCustomCountryList(self::CO_ACTIVE);
		}

		self::LocaleNameLocation(self::$countryList);

		return self::$countryList;
	}

	// ---

	/**
	 * Get City List From Database Or Internal Model Static Var
	 * @param boolean $refreshStatic
	 * @return array
	 */
	public static function &GetCityList($refreshCache=false) {

		if(is_null(self::$cityList) || $refreshCache) {
			self::$cityList =& self::GetCustomCityList(self::CI_ACTIVE, self::CO_ACTIVE);
		}

		self::LocaleNameLocation(self::$cityList);

		return self::$cityList;
	}

	// ---

	/**
	 * Get State List From Database Or Internal Model Static Var
	 * @param boolean $refreshStatic
	 * @return array
	 */
	public static function &GetStateList($refreshCache=false) {

		if(is_null(self::$stateList) || $refreshCache) {
			self::$stateList =& self::GetCustomStateList(self::S_ACTIVE, self::CO_ACTIVE);
		}

		self::LocaleNameLocation(self::$stateList);

		return self::$stateList;
	}

	// ---

	/**
	 * Get Country List From Static File
	 * @param boolean $refreshStatic
	 * @return array
	 */
	public static function &GetStaticCountryList($refreshStatic=false) {

		//return self::GetCountryList();

		$file = self::file_country();

		if(is_null(self::$countryList) || $refreshStatic) {
			if($refreshStatic) {
				self::$countryList =& self::GenerateCountryStatic();
			}
			else {
				@include($file);
				self::$countryList = ifsetor($countries, array());
				if(empty(self::$countryList)) {
					self::$countryList =& self::GetStaticCountryList(true);
				}
			}
		}

		self::LocaleNameLocation(self::$countryList);

		return self::$countryList;
	}

	// ---

	/**
	 * Get City List From Static File
	 * @param boolean $refreshStatic
	 * @return array
	 */
	public static function &GetStaticCityList($refreshStatic=false) {

		//return self::GetCityList();

		$file = self::file_city();

		if(is_null(self::$cityList) || $refreshStatic) {
			if($refreshStatic) {
				self::$cityList =& self::GenerateCityStatic();
			}
			else {
				@include($file);
				self::$cityList = ifsetor($cities, array());
				if(empty(self::$cityList)) {
					self::$cityList =& self::GetStaticCityList(true);
				}
			}
		}

		self::LocaleNameLocation(self::$cityList);

		return self::$cityList;
	}

	// ---

	/**
	 * Get State List From Static File
	 * @param boolean $refreshStatic
	 * @return array
	 */
	public static function &GetStaticStateList($refreshStatic=false) {

		//return self::GetStateList();

		$file = self::file_state();

		if(is_null(self::$stateList) || $refreshStatic) {
			if($refreshStatic) {
				self::$stateList =& self::GenerateStateStatic();
			}
			else {
				@include($file);
				self::$stateList = ifsetor($states, array());
				if(empty(self::$stateList)) {
					self::$stateList =& self::GetStaticStateList(true);
				}
			}
		}

		self::LocaleNameLocation(self::$stateList);

		return self::$stateList;
	}

	// ---

	/**
	 * @param mixed(int|array) $co_id
	 * @return (hash) array
	 */
	public static function GetCountryListByCountryId($co_id, $co_status=self::CO_ACTIVE) {

		$List = array();

		$co_id = Cast::unsignintarr($co_id);

		if(!empty($co_id)) {

			if($co_status==self::CO_ACTIVE) {
				$countryList =& self::GetStaticCountryList();
			}
			else {
				$countryList =& self::GetCustomCountryList($co_status);
			}

			foreach($co_id as $id) {
				if(array_key_exists($id, $countryList)) {
					$List[$id] = $countryList[$id];
				}
			}
		}

		return $List;
	}

	public static function GetOneCountryListByCountryId($co_id, $co_status=self::CO_ACTIVE) {
		return reset(self::GetCountryListByCountryId($co_id, $co_status));
	}

	// ---

	/**
	 * @param mixed(int|array) $ci_id
	 * @return (hash) array
	 */
	public static function GetCityListByCityId($ci_id, $ci_status=self::CI_ACTIVE, $co_status=self::CO_ACTIVE) {

		$List = array();

		$ci_id = Cast::unsignintarr($ci_id);

		if(!empty($ci_id)) {

			if($ci_status==self::CI_ACTIVE && $co_status==self::CO_ACTIVE) {
				$cityList =& self::GetStaticCityList();
			}
			else {
				$cityList =& self::GetCustomCityList($ci_status, $co_status);
			}

			foreach($ci_id as $id) {
				if(array_key_exists($id, $cityList)) {
					$List[$id] = $cityList[$id];
				}
			}
		}

		return $List;
	}

	public static function GetOneCityListByCityId($ci_id, $ci_status=self::CI_ACTIVE, $co_status=self::CO_ACTIVE) {
		return reset(self::GetCityListByCityId($ci_id, $ci_status, $co_status));
	}

	// ---

	public static function &GetCityListByCountryId($co_id, $ci_status=self::CI_ACTIVE, $co_status=self::CO_ACTIVE) {

		$List = array();

		$co_id = Cast::unsignint($co_id);

		if($co_id) {

			if($ci_status==self::CI_ACTIVE && $co_status==self::CO_ACTIVE) {
				$cityList =& self::GetStaticCityList();
			}
			else {
				$cityList =& self::GetCustomCityList($ci_status, $co_status);
			}

			foreach($cityList as $ci_id=>$data) {
				if($co_id==$data['country_id']) {
					$List[$ci_id] = $data;
				}
			}
		}

		return $List;
	}

	// ---

	public static function &GetUserCityListByCountryId($co_id, $ci_status=self::CI_ACTIVE, $co_status=self::CO_ACTIVE) {

		$List = array();

		$co_id = $co_id = Cast::unsignint($co_id);

		if($co_id) {

			$__db =& __db();

			$sql = sprintf('
				SELECT %1$s FROM %2$s AS ci
				WHERE ci.id IN (
					SELECT DISTINCT(city) AS city FROM %3$s AS u WHERE u.status=%4$u AND country=%5$u AND NOT(u.bitmask&%7$u)
				) AND ci.country_id=%5$u AND ci.active=%6$u
				',
				self::CITY_FIELDS, self::db_city(),
				User::db_table(), User::STATUS_OKE,
				$co_id, $ci_status,
				User::BITMASK_HIDE_LOCATION
			);

			$sql_r = $__db->q($sql);
			while($row=$sql_r->next()) {
				$List[$row['id']] = $row;
			}

			self::LocaleNameLocation($List);
		}

		return $List;
	}

	// ---

	/**
	 * Get City List By State Id
	 * @param int $s_id
	 * @param int $ci_status
	 * @param int $co_status
	 */
	public static function &GetCityListByStateId($s_id, $ci_status=self::CI_ACTIVE, $co_status=self::CO_ACTIVE) {

		$List = array();

		$s_id = Cast::unsignint($s_id);

		if($s_id) {

			if($ci_status==self::CI_ACTIVE && $co_status==self::CO_ACTIVE) {
				$cityList =& self::GetStaticCityList();
			}
			else {
				$cityList =& self::GetCustomCityList($ci_status, $co_status);
			}

			foreach($cityList as $ci_id=>$data) {
				if($s_id==$data['state_id']) {
					$List[$ci_id] = $data;
				}
			}
		}

		return $List;
	}

	// ---

	public static function &GetStateListByStateId($s_id, $s_status=self::S_ACTIVE, $co_status=self::CO_ACTIVE) {

		$List = array();

		$s_id = Cast::unsignintarr($s_id);

		if(!empty($s_id)) {

			if($s_status==self::S_ACTIVE && $co_status==self::CO_ACTIVE) {
				$stateList =& self::GetStaticStateList();
			}
			else {
				$stateList =& self::GetCustomStateList($s_status, $co_status);
			}

			foreach($s_id as $id) {
				if(array_key_exists($id, $stateList)) {
					$List[$id] = $stateList[$id];
				}
			}
		}

		return $List;
	}

	public static function GetOneStateListByStateId($s_id, $s_status=self::S_ACTIVE, $co_status=self::CO_ACTIVE) {
		return reset(self::GetStateListByStateId($s_id, $s_status, $co_status));
	}


	// ---

	public static function &GetStateListByCountryId($co_id, $s_status=self::S_ACTIVE, $co_status=self::CO_ACTIVE) {

		$List = array();

		$co_id = Cast::unsignint($co_id);

		if($co_id) {

			if($s_status==self::S_ACTIVE && $co_status==self::CO_ACTIVE) {
				$stateList =& self::GetStaticStateList();
			}
			else {
				$stateList =& self::GetCustomStateList($s_status, $co_status);
			}

			foreach($stateList as $s_id=>$data) {
				if($co_id==$data['country_id']) {
					$List[$s_id] = $data;
				}
			}
		}

		return $List;
	}

	// ---

	public static function GetUrlifyName($what, $id) {
		$what = Translit::urlify($what);
		if($what) {
			$what .= '_'.$id;
		}
		$what = _strtolower($what);
		return $what;
	}

	// ---

	public static function FillOneCountryUrlName($co_id, $co_data=array()) {
		if($co_data) {
			if(!isset($co_data['id'])) $co_data['id'] = $co_id;
		}
		else {
			$co_data = self::GetOneCountryListByCountryId($co_id, self::CO_MIX);
		}
		$co_id = ifsetor($co_data['id'], self::NULL_COUNTRY);
		if($co_id) {
			$name = null;
			foreach(array('name_en', 'name_ru', 'name_ua') as $field) {
				$name = ifsetor($co_data[$field], null, true);
				if($name) break;
			}
			$name_url = self::GetUrlifyName($name, $co_id);
			if($name_url) {
				$update_arr = array('name_url'=>$name_url);
				self::UpdateCountry($co_id, $update_arr);
			}
		}
	}

	public static function FillCountryUrlName($co_status=self::CO_MIX) {
		$countryList =& self::GetCustomCountryList($co_status);
		if($countryList) {
			foreach($countryList as $co_id=>$co_data) {
				self::FillOneCountryUrlName($co_id, $co_data);
			}
			LocationModel::GenerateCountryStatic();
		}
	}

	// ---

	public static function FillOneCityUrlName($ci_id, $ci_data=array()) {
		if($ci_data) {
			if(!isset($ci_data['id'])) $ci_data['id'] = $ci_id;
		}
		else {
			$ci_data = self::GetOneCityListByCityId($ci_id, self::CI_MIX, self::CO_MIX);
		}
		$ci_id = ifsetor($ci_data['id'], self::NULL_CITY);
		if($ci_id) {
			$name = null;
			foreach(array('name_en', 'name_ru', 'name_ua') as $field) {
				$name = ifsetor($ci_data[$field], null, true);
				if($name) break;
			}
			$name_url = self::GetUrlifyName($name, $ci_id);
			if($name_url) {
				$update_arr = array('name_url'=>$name_url);
				self::UpdateCity($ci_id, $update_arr);
			}
		}
	}

	public static function FillCityUrlName($ci_status=self::CI_MIX, $co_status=self::CO_MIX) {
		$cityList =& self::GetCustomCityList($ci_status, $co_status);
		if($cityList) {
			foreach($cityList as $ci_id=>$ci_data) {
				self::FillOneCityUrlName($ci_id, $ci_data);
			}
			LocationModel::GenerateCityStatic();
		}
	}

	// ---

	public static function FillOneStateUrlName($s_id, $s_data=array()) {
		if($s_data) {
			if(!isset($s_data['id'])) $s_data['id'] = $s_id;
		}
		else {
			$s_data = self::GetOneStateListByStateId($s_id, self::S_MIX, self::CO_MIX);
		}
		$s_id = ifsetor($s_data['id'], self::NULL_STATE);
		if($s_id) {
			$name = null;
			foreach(array('name_en', 'name_ru', 'name_ua') as $field) {
				$name = ifsetor($s_data[$field], null, true);
				if($name) break;
			}
			$name_url = self::GetUrlifyName($name, $s_id);
			if($name_url) {
				$update_arr = array('name_url'=>$name_url);
				self::UpdateState($s_id, $update_arr);
			}
		}
	}

	public static function FillStateUrlName($s_status=self::S_MIX, $co_status=self::CO_MIX) {
		$stateList =& self::GetCustomStateList($s_status, $co_status);
		if($stateList) {
			foreach($stateList as $s_id=>$s_data) {
				self::FillOneStateUrlName($s_id, $s_data);
			}
			LocationModel::GenerateStateStatic();
		}
	}

	// ---

	public static function InsertCountry($insert_arr, $insert_clear_arr=array()) {
		return self::Insert(self::db_country(), $insert_arr, $insert_clear_arr);
	}
	public static function InsertCity($insert_arr, $insert_clear_arr=array()) {
		return self::Insert(self::db_city(), $insert_arr, $insert_clear_arr);
	}
	public static function InsertState($insert_arr, $insert_clear_arr=array()) {
		return self::Insert(self::db_state(), $insert_arr, $insert_clear_arr);
	}
	public static function Insert($db_table, $insert_arr, $insert_clear_arr=array()) {
		$id = 0;
		$insert_arr = Util::cast_dbtable_values($insert_arr, $db_table);
		$insert_clear_arr = Util::cast_dbtable_values($insert_clear_arr, $db_table, false);
		$insert_sql = MySQL::prepare_fields($insert_arr, $insert_clear_arr);
		if($insert_sql) {
			$__db =& __db();
			$sql = sprintf('INSERT IGNORE INTO %1$s SET %2$s', $db_table, $insert_sql);
			$__db->u($sql);
			$id = $__db->last_insert_id();
		}
		return $id;
	}
	// ---

	public static function UpdateCountry($co_id, $update_arr, $update_clear_arr=array()) {
		return self::Update(LocationModel::db_country(), $co_id, $update_arr, $update_clear_arr);
	}
	public static function UpdateCity($ci_id, $update_arr, $update_clear_arr=array()) {
		return self::Update(LocationModel::db_city(), $ci_id, $update_arr, $update_clear_arr);
	}

	public static function UpdateState($s_id, $update_arr, $update_clear_arr=array()) {
		return self::Update(LocationModel::db_state(), $s_id, $update_arr, $update_clear_arr);
	}
	public static function Update($db_table, $id, $update_arr, $update_clear_arr=array()) {
		$result = 0;
		$update_arr = Util::cast_dbtable_values($update_arr, $db_table);
		$update_clear_arr = Util::cast_dbtable_values($update_clear_arr, $db_table, false);
		$update_sql = MySQL::prepare_fields($update_arr, $update_clear_arr);
		if($update_sql) {
			$__db =& __db();
			$sql = sprintf('UPDATE %1$s SET %2$s WHERE id=%3$u', $db_table, $update_sql, $id);
			$result = $__db->u($sql);
		}
		return $result;
	}

	// ---

	public static function &GenerateCountryStatic() {

		$List =& self::GetCountryList(true);
		FileFunc::saveVarsToFile(self::file_country(), array('countries' => &$List));
		return $List;
	}

	public static function &GenerateCityStatic() {

		$List = self::GetCityList(true);
		FileFunc::saveVarsToFile(self::file_city(), array('cities' => &$List));
		return $List;
	}

	public static function &GenerateStateStatic() {

		$List = self::GetStateList(true);
		FileFunc::saveVarsToFile(self::file_state(), array('states' => &$List));
		return $List;
	}

	// ---

	public static function LocaleNameLocation(&$list) {

		$list =& LocaleModel::LocaleNameModifyList($list);
		return $list;
	}

	// ---

	public static function LocaleOrderField($list, $field) {
		$one = reset($list);
		$field_arr = array_keys($one);
		if(in_array($field, $field_arr)) {
			$list =& LocaleModel::LocaleFieldOrderList($list, $field);
		}
		return $list;
	}
}
