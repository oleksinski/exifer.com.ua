<?

class UserpicModel {

	const FORMAT_300 = 300;
	const FORMAT_75 = 75;

	const DEFAULT_FORMAT = 300;

	protected static $cache = array();

	public static function db_userpic() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'user_picture');
	}

	public static function GetCorrectFormat($format) {
		return insetor($format, self::GetFormatValueList(), self::DEFAULT_FORMAT);
	}

	public static function GetFormatValueList() {
		return ReflectionModel::getClassConstValueList(__CLASS__, 'FORMAT_');
	}

	public static function GetFormatNameList() {
		return ReflectionModel::getClassConstNameList(__CLASS__, 'FORMAT_');
	}

	public static function GetFormatNameDimension($format) {
		$list_arr = ReflectionModel::getClassConstList(__CLASS__, 'FORMAT_');
		$list_arr = array_flip($list_arr);
		$name = ifsetor($list_arr[$format], $list_arr[self::DEFAULT_FORMAT]);
		$name = _str_replace('FORMAT_', '', $name);
		return $name;
	}

	public static function GetFormatDimension($format) {

		list($width, $height) = array(0,0);
		$format = self::GetCorrectFormat($format);
		$dimension = self::GetFormatNameDimension($format);

		switch($format) {
			case self::FORMAT_300:
			case self::FORMAT_75:
			default:
				$width = $height = $dimension;
				break;
		}

		return array($width, $height);
	}

	public static function GetUserpicLocalPath($user_id, $format) {
		$user_id = Cast::unsignint($user_id);
		$path = array();
		$path[] = _rtrim(I_PATH, '/');
		$path[] = 'userpic';
		$path[] = self::GetFormatNameDimension($format);
		$path[] = $user_id%10;
		$path[] = $user_id%100;
		$path[] = $user_id.'.jpg';
		$local = implode('/', $path);
		return $local;
	}

	/**
	 * @param int $user_id
	 * @param const $format
	 * @param int $param [anti web browser cache]
	 */
	public static function GetUserpicWebPath($user_id, $format, $param=0) {
		$local = self::GetUserpicLocalPath($user_id, $format);
		$web = Url::i_local2web($local);
		if($web && $param && ($param = Cast::unsignint($param))) {
			$web .= '?'.$param;
		}
		return $web;
	}

	public static function GetBlankLocalPath($format, $gender=User::GENDER_MALE) {
		$format = self::GetCorrectFormat($format);
		$local = sprintf('%s/img/userpic/2/blank_%s.jpg', _rtrim(S_PATH, '/'), $format);
		return $local;
	}

	public static function GetBlankWebPath($format, $gender=User::GENDER_MALE) {
		$local = self::GetBlankLocalPath($format);
		$web = Url::s_local2web($local);
		return $web;
	}

	// ---

	/**
	 * @param string $local
	 * @return const userpic format if success
	 */
	public static function GetBlankLocalPathFormat($local) {

		$format = false;

		$formats = self::GetFormatValueList();

		foreach($formats as $f) {
			if(_strstr($local, self::GetBlankLocalPath($f))) {
				$format = $f;
				break;
			}
		}
		return $format;
	}

	public static function GetBlankWebPathFormat($web) {

		$format = false;

		$formats = self::GetFormatValueList();

		foreach($formats as $f) {
			if(_strstr($web, self::GetBlankWebPath($f))) {
				$format = $f;
				break;
			}
		}
		return $format;
	}


	public static function Create($user_id, $source_filepath) {

		$affected = 0;

		$formats = self::GetFormatValueList();

		foreach($formats as $format) {
			$success = self::CreateFormat($user_id, $format, $source_filepath);
			if($success) {
				$affected++;
			}
		}

		return $affected;
	}


	public static function CreateFormat($user_id, $format, $source_filepath) {

		$affected = 0;

		$user_id = Cast::unsignint($user_id);
		$format = self::GetCorrectFormat($format);

		$destin_filepath = self::GetUserpicLocalPath($user_id, $format);

		$img_info = Im::GetImageInfo($source_filepath);

		if($img_info && strcmp($source_filepath, $destin_filepath)!==0) {

			list($max_width, $max_height) = self::GetFormatDimension($format);

			if($max_width && $max_height) {

				// img processing: crop & resize

				$resized = false;

				switch($format) {

					case self::FORMAT_75:
						$resized = Im::ResizeSmart(
							$source_filepath,
							$destin_filepath,
							$max_width,
							$max_height
						);
						break;

					default:
						$resized = Im::Resize(
							$source_filepath,
							$destin_filepath,
							$max_width,
							$max_height,
							Im::IM_RESIZE_MODE_SHRINK
						);
				}

				if($resized) {

					//$metadroppped = Im::DropMetaInfo($destin_filepath);
					//$resized = $metadroppped;

					$userpic_info = Im::GetImageInfo($destin_filepath);

					if($userpic_info) {

						$actual_width = ifsetor($userpic_info['width'], 0);
						$actual_height = ifsetor($userpic_info['height'], 0);
						$actual_filesize = ifsetor($userpic_info['filesize'], 0);

						$affected = self::Insert($user_id, $format, $actual_width, $actual_height, $actual_filesize);
					}
				}
			}
		}

		return $affected;
	}

	public static function CreateFormatIfNotExists($user_id, $t_format, $s_format=self::DEFAULT_FORMAT) {
		$affected = 0;
		$t_format = self::GetCorrectFormat($t_format);
		$s_format = self::GetCorrectFormat($s_format);
		if($t_format!=$s_format) {
			$t_filepath = self::GetUserpicLocalPath($user_id, $t_format);
			if(!file_exists($t_filepath)) {
				$s_filepath = self::GetUserpicLocalPath($user_id, $s_format);
				$affected = self::CreateFormat($user_id, $t_format, $s_filepath);
			}
		}
		return $affected;
	}

	public static function Insert($user_id, $format, $width, $height, $filesize) {

		$affected = 0;

		$user_id = Cast::unsignint($user_id);
		$format = self::GetCorrectFormat($format);
		$width = Cast::unsignint($width);
		$height = Cast::unsignint($height);
		$filesize = Cast::unsignint($filesize);

		if($user_id) {

			$__db =& __db();

			$insert_arr = array(
				'user_id' => $user_id,
				'format' => $format,
				'width' => $width,
				'height' => $height,
				'filesize' => $filesize,
				'tstamp' => time(),
			);

			$insert_sql = MySQL::prepare_fields($insert_arr);

			$sql = sprintf('INSERT INTO %1$s SET %2$s ON DUPLICATE KEY UPDATE %2$s', self::db_userpic(), $insert_sql);

			$affected = $__db->u($sql);

			if($affected) {

				$user = new User($user_id);
				$user->setField('userpic_tstamp', time());
				$user->update();

				self::$cache[$user_id][$format] = $insert_arr;
			}
		}

		return $affected;
	}


	public static function Get($user_id, $format=null) {

		$result = array();

		$user_id = Cast::unsignintarr($user_id);

		$format = is_null($format) ? null : self::GetCorrectFormat($format);

		if(!empty($user_id)) {

			$__db =& __db();

			$sql_where_arr = array();
			$sql_where_arr[] = MySQL::sqlInClause('user_id', $user_id);
			if($format) {
				$sql_where_arr[] = MySQL::prepare_fields(array('format'=>$format));
			}

			$sql_where_str = implode(' AND ', $sql_where_arr);

			$sql = sprintf('SELECT * FROM %1$s WHERE %2$s', self::db_userpic(), $sql_where_str);
			$sql_r = $__db->q($sql);

			while($row = $sql_r->next()) {
				$result[$row['user_id']][$row['format']] = $row;
			}

			self::$cache += $result;
		}

		return $result;

	}

	public static function GetOne($user_id, $format=null) {
		$array = self::Get($user_id, $format);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	public static function GetPrepared($user_id, $format=null) {

		$result = array();

		$user_id = Cast::unsignintarr($user_id);
		$format = is_null($format) ? null : self::GetCorrectFormat($format);
		$formats = self::GetFormatValueList();

		$data = self::Get($user_id, $format);

		foreach($user_id as $u_id) {

			$u_data = ifsetor($data[$u_id], array());

			foreach($formats as $f) {

				$u_format = ifsetor($u_data[$f], array());

				if(empty($u_format)) {

					list($width, $height) = self::GetFormatDimension($f);

					$u_format = array(
						'user_id' => $u_id,
						'format' => $f,
						'width' => $width,
						'height' => $height,
						'filesize' => 0,
						'tstamp' => 0,

						'src' => self::GetBlankWebPath($f),
						'local' => self::GetBlankLocalPath($f),
					);
				}
				else {
					$u_format['tstamp'] = $u_format['tstamp'];
					$u_format['src'] = self::GetUserpicWebPath($u_id, $f, $u_format['tstamp']);
					$u_format['local'] = self::GetUserpicLocalPath($u_id, $f, $u_format['tstamp']);
				}

				if(is_null($format) || $format==$f) {
					$result[$u_id][$f] = $u_format;
				}
			}
		}

		return $result;
	}

	public static function GetOnePrepared($user_id, $format=null) {
		$array = self::GetPrepared($user_id, $format);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	// ---

	public static function GetPreparedBlind($user_id, $tstamp=0) {

		$result = array();

		$user_id = Cast::unsignintarr($user_id);
		$formats = self::GetFormatValueList();

		$tstamp = Cast::unsignint($tstamp);

		foreach($user_id as $u_id) {

			foreach($formats as $format) {

				list($width, $height) = array('', '');

				if($tstamp) {
					list($width, $height) = self::GetFormatDimension($format);
				}

				$result[$u_id][$format] = array(
					'user_id' => $u_id,
					'format' => $format,
					'width' => '',//$width,
					'height' => '',//$height,
					'filesize' => 0,
					'tstamp' => $tstamp,

					'src' => $tstamp ? self::GetUserpicWebPath($u_id, $format, $tstamp) : self::GetBlankWebPath($format),
					'local' => $tstamp ? self::GetUserpicLocalPath($u_id, $format) : self::GetBlankLocalPath($format),
				);
			}
		}

		return $result;
	}

	public static function GetOnePreparedBlind($user_id, $tstamp=0) {
		$array = self::GetPreparedBlind($user_id, $tstamp);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	// ---

	public static function Remove($user_id, $format=null) {

		$affected = 0;

		$user_id = Cast::unsignintarr($user_id);

		$format = is_null($format) ? null : self::GetCorrectFormat($format);

		if(!empty($user_id)) {

			$__db =& __db();

			// remove db userpic records
			$sql_where_arr = array();
			$sql_where_arr[] = MySQL::sqlInClause('user_id', $user_id);
			if($format) {
				$sql_where_arr[] = MySQL::prepare_fields(array('format'=>$format));
			}

			$sql_where_str = implode(' AND ', $sql_where_arr);

			$sql = sprintf('DELETE FROM %1$s WHERE %2$s', self::db_userpic(), $sql_where_str);
			$affected += $__db->u($sql);

			$formats = self::GetFormatValueList();

			// remove files on disk
			foreach($user_id as $u_id) {

				if(is_null($format)) {
					foreach($formats as $f) {
						$local_path = self::GetUserpicLocalPath($u_id, $f);
						if(file_exists($local_path)) {
							_e('del '.$local_path);
							unlink($local_path);
						}
					}
				}
				else {
					$local_path = self::GetUserpicLocalPath($u_id, $format);
					if(file_exists($local_path)) {
						_e('del '.$local_path);
						unlink($local_path);
					}
				}
			}

			self::ClearCache($user_id, $format);

			// remove flag "user has userpic" from global use profile
			$userpic_user_data = self::Get($user_id);
			$userpic_uid_remain = array_keys($userpic_user_data);

			$uid_diff = array_diff($user_id, $userpic_uid_remain);

			foreach($uid_diff as $u_id) {
				$user = new User($u_id);
				$user->setField('userpic_tstamp', 0);
				$user->update();
			}
		}

		return $affected;
	}

	public static function ClearCache($user_id=null, $format=null) {

		if(self::$cache) {

			$user_id = Cast::unsignintarr($user_id);
			$format = is_null($format) ? null : self::GetCorrectFormat($format);

			foreach(self::$cache as $c_uid=>&$c_data) {
				if(!$user_id || in_array($c_uid, $user_id)) {
					if(isset($c_data[$format])) {
						unset($c_data[$format]);
					}
					if(is_null($format) || empty(self::$cache[$c_uid])) {
						unset(self::$cache[$c_uid]);
					}
				}
			}

		}

		return self::$cache;
	}

}
