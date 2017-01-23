<?

class OccupationModel {

	const OCCUPATION_FIELDS = 'o.id, o.name_ru, o.name_ua, o.name_en, o.name_url';
	const EXPERIENCE_FIELDS = 'e.id, e.name_ru, e.name_ua, e.name_en, e.name_url';
	const OCCUPATION_EXPERIENCE_FIELDS = 'oe.occupation_id, oe.experience_id, oe.active';
	const OCCUPATION_EXPERIENCE_DATA_FIELDS = 'user_id, occupation_id, experience_id';

	// ---

	const OCCUPATION_STATIC_PATH = '/occupation/occupation.static.php';
	const EXPERIENCE_STATIC_PATH = '/occupation/experience.static.php';
	const OCCUPATION_EXPERIENCE_STATIC_PATH = '/occupation/occupation_experience.static.php';

	// ---

	const OCCUPATION_EXPERIENCE_LIMIT = 10; // experience items limit per one occupation

	// ---

	private static $occupationList = null;
	private static $experienceList = null;
	private static $occupationExperienceList = null;

	// ---

	public static function db_occupation() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'user_occupation');
	}

	public static function db_experience() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'user_experience');
	}

	public static function db_occupation_experience() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'user_occupation_experience');
	}

	public static function db_occupation_experience_data() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'user_occupation_experience_data');
	}

	// ---

	public static function getPropertyOccupationList() {
		return self::$occupationList;
	}

	public static function getPropertyExperienceList() {
		return self::$experienceList;
	}

	public static function getPropertyOccupationExperienceList() {
		return self::$occupationExperienceList;
	}

	// ---

	public static function file_occupation() {
		return STATIC_PATH . self::OCCUPATION_STATIC_PATH;
	}

	public static function file_experience() {
		return STATIC_PATH . self::EXPERIENCE_STATIC_PATH;
	}

	public static function file_occupation_experience() {
		return STATIC_PATH . self::OCCUPATION_EXPERIENCE_STATIC_PATH;
	}

	// ---

	public static function &GetOccupationList($refreshCache=false) {

		if(is_null(self::$occupationList) || $refreshCache) {

			$__db =& __db();

			$sql = sprintf('SELECT %s FROM %s AS o', self::OCCUPATION_FIELDS, self::db_occupation());

			$sql_r = $__db->q($sql);

			while($row = $sql_r->next()) {
				if($row['id']) {
					self::$occupationList[$row['id']] = $row;
				}
			}
		}

		self::LocaleNameExperienceOccupation(self::$occupationList);

		return self::$occupationList;
	}

	public static function &GetStaticOccupationList($refreshStatic=false) {

		if(is_null(self::$occupationList) || $refreshStatic) {
			if($refreshStatic) {
				self::$occupationList =& self::GenerateOccupationStatic();
			}
			else {
				$file = self::file_occupation();
				@include($file);
				self::$occupationList = ifsetor($occupation, array());
				if(empty(self::$occupationList)) {
					$self_func = __FUNCTION__;
					self::$occupationList =& self::$self_func(true);
				}
			}
		}

		self::LocaleNameExperienceOccupation(self::$occupationList);

		return self::$occupationList;
	}

	public static function &GetOccupationListFilterOccupation($occupation_id) {

		$list = array();

		$occupation_id = Cast::unsignintarr($occupation_id);

		$occupationList =& self::GetStaticOccupationList();

		foreach($occupation_id as $o_id) {
			if(array_key_exists($o_id, $occupationList)) {
				$list[$o_id] = $occupationList[$o_id];
			}
		}

		return $list;
	}

	public static function GetOneOccupationListFilterOccupation($occupation_id) {
		$array = self::GetOccupationListFilterOccupation($occupation_id);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	public static function &GetOccupationListFilterOccupationAlias($occupation_alias) {

		$list = array();

		$occupation_alias = Cast::strarr($occupation_alias);

		$occupationList =& self::GetStaticOccupationList();

		foreach($occupation_alias as $alias) {
			foreach($occupationList as $o_id=>$o_data) {
				if($o_data['name_url']==$alias) {
					$list[$o_id] = $occupationList[$o_id];
					break;
				}
			}
		}

		return $list;
	}

	public static function GetOneOccupationListFilterOccupationAlias($occupation_alias) {
		$array = self::GetOccupationListFilterOccupationAlias($occupation_alias);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	public static function &GenerateOccupationStatic() {
		$list =& self::GetOccupationList(true);
		FileFunc::saveVarsToFile(self::file_occupation(), array('occupation' => &$list));
		return $list;
	}

	public static function CheckValidOccupation($occupation_id) {
		$occupation_id = Cast::unsignint($occupation_id);
		$occupation = self::GetOneOccupationListFilterOccupation($occupation_id);
		return !empty($occupation) ? $occupation_id : 0;
	}

	// ---

	public static function &GetExperienceList($refreshCache=false) {

		if(is_null(self::$experienceList) || $refreshCache) {

			$__db =& __db();

			$sql = sprintf('SELECT %s FROM %s AS e', self::EXPERIENCE_FIELDS, self::db_experience());

			$sql_r = $__db->q($sql);

			while($row = $sql_r->next()) {
				if($row['id']) {
					self::$experienceList[$row['id']] = $row;
				}
			}
		}

		self::LocaleNameExperienceOccupation(self::$experienceList);

		return self::$experienceList;
	}

	public static function &GetStaticExperienceList($refreshStatic=false) {

		if(is_null(self::$experienceList) || $refreshStatic) {
			if($refreshStatic) {
				self::$experienceList =& self::GenerateExperienceStatic();
			}
			else {
				$file = self::file_experience();
				@include($file);
				self::$experienceList = ifsetor($experience, array());
				if(empty(self::$experienceList)) {
					$self_func = __FUNCTION__;
					self::$experienceList =& self::$self_func(true);
				}
			}
		}

		self::LocaleNameExperienceOccupation(self::$experienceList);

		return self::$experienceList;
	}

	public static function &GetExperienceListFilterExperience($experience_id) {

		$list = array();

		$experience_id = Cast::unsignintarr($experience_id);

		$experienceList =& self::GetStaticExperienceList();

		foreach($experience_id as $e_id) {
			if(array_key_exists($e_id, $experienceList)) {
				$list[$e_id] = $experienceList[$e_id];
			}
		}

		return $list;
	}

	public static function GetOneExperienceListFilterExperience($experience_id) {
		$array = self::GetExperienceListFilterExperience($experience_id);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	public static function &GetExperienceListFilterExperienceAlias($experience_alias) {

		$list = array();

		$experience_alias = Cast::strarr($experience_alias);

		$experienceList =& self::GetStaticExperienceList();

		foreach($experience_alias as $alias) {
			foreach($experienceList as $e_id=>$e_data) {
				if($e_data['name_url']==$alias) {
					$list[$e_id] = $experienceList[$e_id];
					break;
				}
			}
		}

		return $list;
	}

	public static function GetOneExperienceListFilterExperienceAlias($experience_alias) {
		$array = self::GetExperienceListFilterExperienceAlias($experience_alias);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	public static function &GenerateExperienceStatic() {
		$list =& self::GetExperienceList(true);
		FileFunc::saveVarsToFile(self::file_experience(), array('experience' => &$list));
		return $list;
	}

	public static function CheckValidExperience($experience_id) {
		$experience_id = Cast::unsignint($experience_id);
		$experience = self::GetOneExperienceListFilterExperience($experience_id);
		return !empty($experience) ? $experience_id : 0;
	}

	// ---

	public static function &GetOccupationExperienceList($refreshCache=false) {

		if(is_null(self::$occupationExperienceList) || $refreshCache) {

			$__db =& __db();

			$sql = sprintf(
				'SELECT %s FROM %s AS oe WHERE oe.active=1 ORDER BY oe.occupation_id',
				self::OCCUPATION_EXPERIENCE_FIELDS, self::db_occupation_experience()
			);

			$sql_r = $__db->q($sql);

			while($row = $sql_r->next()) {
				if($row['occupation_id']) {
					if(!isset(self::$occupationExperienceList[$row['occupation_id']])) {
						self::$occupationExperienceList[$row['occupation_id']] = array();
					}
					if($row['experience_id']) {
						self::$occupationExperienceList[$row['occupation_id']][] = $row['experience_id'];
					}
				}
			}
		}

		return self::$occupationExperienceList;
	}

	public static function &GetStaticOccupationExperienceList($refreshStatic=false) {

		if(is_null(self::$occupationExperienceList) || $refreshStatic) {
			if($refreshStatic) {
				self::$occupationExperienceList =& self::GenerateOccupationExperienceStatic();
			}
			else {
				$file = self::file_occupation_experience();
				@include($file);
				self::$occupationExperienceList = ifsetor($occupation_experience, array());
				if(empty(self::$occupationExperienceList)) {
					$self_func = __FUNCTION__;
					self::$occupationExperienceList =& self::$self_func(true);
				}
			}
		}

		return self::$occupationExperienceList;
	}

	public static function &GetOccupationExperienceListFilterOccupation($occupation_id) {

		$list = array();

		$occupation_id = Cast::unsignintarr($occupation_id);

		$occupationExperienceList =& self::GetStaticOccupationExperienceList();

		foreach($occupation_id as $o_id) {

			if(array_key_exists($o_id, $occupationExperienceList)) {
				$list[$o_id] = $occupationExperienceList[$o_id];
			}
		}

		return $list;
	}

	public static function GetOneOccupationExperienceListFilterOccupation($occupation_id) {
		$array = self::GetOccupationExperienceListFilterOccupation($occupation_id);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	public static function &GenerateOccupationExperienceStatic() {
		$list =& self::GetOccupationExperienceList(true);
		FileFunc::saveVarsToFile(self::file_occupation_experience(), array('occupation_experience' => &$list));
		return $list;
	}

	public static function CheckValidOccupationExperience($occupation_id, $experience_id) {
		$occupationExperienceList = self::GetOneOccupationExperienceListFilterOccupation($occupation_id);
		return insetcheck($experience_id, $occupationExperienceList);
	}

	// ---

	public static function &LocaleNameExperienceOccupation(&$list) {
		$list =& LocaleModel::LocaleNameModifyList($list);
		return $list;
	}

	// ---

	public static function SelectUserData($user_id, $occupation_id=null, $experience_id=null) {

		$result = array();

		$user_id = Cast::unsignintarr($user_id);

		if(!empty($user_id)) {

			$__db =& __db();

			$occupation_id = Cast::unsignint($occupation_id);
			$experience_id = Cast::unsignint($experience_id);

			$valid_occupation_id = self::CheckValidOccupation($occupation_id);
			$valid_experience_id = self::CheckValidExperience($experience_id);

			$where_sql_arr = array();
			$where_sql_arr[] = MySQL::sqlInClause('user_id', $user_id);
			if($occupation_id && $occupation_id==$valid_occupation_id) {
				$where_sql_arr[] = sprintf('occupation_id=%u', $occupation_id);
			}
			if($occupation_id && $occupation_id==$valid_occupation_id) {
				$where_sql_arr[] = sprintf('experience_id=%u', $experience_id);
			}

			$where_sql_str = implode(' AND ', $where_sql_arr);

			$sql = sprintf('SELECT %s FROM %s WHERE %s',
				self::OCCUPATION_EXPERIENCE_DATA_FIELDS,
				self::db_occupation_experience_data(),
				$where_sql_str
			);

			$sql_r = $__db->q($sql);

			while($row = $sql_r->next()) {
				if(!isset($result[$row['user_id']])) {
					$result[$row['user_id']] = array();
				}
				if(!isset($result[$row['user_id']][$row['occupation_id']])) {
					$result[$row['user_id']][$row['occupation_id']] = array();
				}
				if($row['experience_id']) {
					$result[$row['user_id']][$row['occupation_id']][] = $row['experience_id'];
				}
			}
		}

		return $result;
	}

	public static function SelectOneUserData($user_id, $occupation_id=null, $experience_id=null) {
		$array = self::SelectUserData($user_id, $occupation_id, $experience_id);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	/**
	 * @param int $user_id
	 * @param array $occupation - look SelectUserData return format
	 * @param bool $replace - check whetherto remove previous data or not
	 */
	public static function InsertUserData($user_id, $occupation, $replace=true, $oe_limit=self::OCCUPATION_EXPERIENCE_LIMIT) {

		$affected = 0;

		$user_id = Cast::unsignint($user_id);
		$occupation = is_array($occupation) ? $occupation : array();
		$oe_limit = Cast::unsignint($oe_limit);

		$occupation_arr = array();

		foreach($occupation as $occupation_id=>$occupation_data) {
			if(self::CheckValidOccupation($occupation_id)) {
				if(!isset($occupation_arr[$occupation_id])) {
					$occupation_arr[$occupation_id] = array();
				}
				$oe_iter = 0;
				if(is_array($occupation_data)) {
					foreach($occupation_data as $index=>$experience_id) {
						if(self::CheckValidOccupationExperience($occupation_id, $experience_id)) {
							if($oe_iter < $oe_limit) {
								$occupation_arr[$occupation_id][] = $experience_id;
								$oe_iter++;
							}
						}
					}
				}
			}
		}

		if($occupation_arr) {

			if($replace) {
				self::RemoveUserData($user_id);
			}

			foreach($occupation_arr as $occupation_id=>$occupation_data) {
				if(is_array($occupation_data) && $occupation_data) {
					foreach($occupation_data as $index=>$experience_id) {
						$affected += self::InsertOneUserData($user_id, $occupation_id, $experience_id);
					}
				}
				else {
					$affected += self::InsertOneUserData($user_id, $occupation_id);
				}
			}
		}

		return $affected;
	}

	/**
	 * @param int $user_id
	 * @param int $occupation_id
	 * @param int $experience_id
	 */
	public static function InsertOneUserData($user_id, $occupation_id, $experience_id=0) {

		$affected = 0;

		$user_id = Cast::unsignint($user_id);
		$occupation_id = Cast::unsignint($occupation_id);
		$experience_id = Cast::unsignint($experience_id);

		$valid_occupation_id = self::CheckValidOccupation($occupation_id);
		$valid_experience_id = self::CheckValidExperience($experience_id);
		$experience_match_occupation = self::CheckValidOccupationExperience($occupation_id, $experience_id);

		$insert_bool = true;
		$insert_bool = $insert_bool && !empty($user_id);
		$insert_bool = $insert_bool && $occupation_id==$valid_occupation_id && $occupation_id;
		$insert_bool = $insert_bool && $experience_id==$valid_experience_id && (!$experience_id || $experience_match_occupation);

		if($insert_bool) {

			$__db =& __db();

			$insert_arr = array(
				'user_id' => $user_id,
				'occupation_id' => $occupation_id,
				'experience_id' => $experience_id,
			);

			$insert_sql = MySQL::prepare_fields($insert_arr);

			$sql = sprintf('INSERT INTO %s SET %s ON DUPLICATE KEY UPDATE %2$s',
				self::db_occupation_experience_data(),
				$insert_sql
			);

			$affected = $__db->u($sql);
		}

		return $affected;
	}

	public static function RemoveUserData($user_id, $occupation_id=0, $experience_id=0) {

		$affected = 0;

		$user_id = Cast::unsignintarr($user_id);

		if(!empty($user_id)) {

			$__db =& __db();

			$occupation_id = Cast::unsignint($occupation_id);
			$experience_id = Cast::unsignint($experience_id);

			$delete_arr = array();
			$delete_arr[] = MySQL::sqlInClause('user_id', $user_id);
			if($occupation_id) {
				$delete_arr[] = sprintf('occupation_id=%u', $occupation_id);
			}
			if($experience_id) {
				$delete_arr[] = sprintf('experience_id=%u', $experience_id);
			}

			$delete_sql = implode(' AND ', $delete_arr);

			$sql = sprintf('DELETE FROM %s WHERE %s',
				self::db_occupation_experience_data(),
				$delete_sql
			);

			$affected = $__db->u($sql);
		}

		return $affected;
	}

	/**
	 *
	 * @param array $occupation_experience
	 * @return array(id=>name)
	 */
	public static function getOccupationIdName(array $occupation_experience) {
		$result = array();
		$list = self::GetStaticOccupationList();
		foreach($occupation_experience as $o_id=>$e_id) {
			$data = ifsetor($list[$o_id], array());
			$name = ifsetor($data['name'], null);
			if($name) {
				$result[$o_id] = $name;
			}
		}
		return $result;
	}

	/**
	 *
	 * @param array $occupation_experience
	 * @return array(id=>name)
	 */
	public static function getExperienceIdName(array $occupation_experience) {
		$result = array();
		$list = self::GetStaticExperienceList();
		foreach($occupation_experience as $o_id=>$e_id) {
			if($e_id) {
				$e_id = (array)$e_id;
				foreach($e_id as $id) {
					$data = ifsetor($list[$id], array());
					$name = ifsetor($data['name'], null);
					if($name) {
						$result[$o_id] = $name;
					}
				}
			}
		}
		_e($result);
		return $result;
	}
}