<?

class ThumbModel {

	const THUMB_1000 = 1000; //

	const THUMB_75 = 75;   // square
	const THUMB_150 = 150;

	const THUMB_240 = 240; // square

	const THUMB_300 = 300;

	const THUMB_301 = 301;

	const THUMBNAIL_DEFAULT = 150;
	const THUMBNAIL_ORIGINAL = 1000;

	/**
	 * Create fixed dimension landscape and portrait images
	 */
	const MODE_XY_FIX = 1;
	/**
	 * Create fixed dimension lanscape images.
	 * Portrait ones write into landscape format filling border with white color
	 */
	const MODE_X_FIX = 2;
	/**
	 * Create variable dimension landscape and portrait images. Can create a squared images.
	 */
	const MODE_XY_VAR = 3;
	/**
	 * Selected resize mode by defaults
	 */
	const RESIZE_MODE = 3;

	const MIN_WIDTH = 300;
	const MIN_HEIGHT = 200;

	protected static $cache = array();

	public static function db_thumb() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'photo_thumb');
	}

	public static function GetCorrectFormat($format) {
		return insetor($format, self::GetFormatValueList(), self::THUMBNAIL_DEFAULT);
	}

	public static function GetFormatValueList() {
		return ReflectionModel::getClassConstValueList(__CLASS__, 'THUMB_');
	}

	public static function GetFormatNameList() {
		return ReflectionModel::getClassConstNameList(__CLASS__, 'THUMB_');
	}

	public static function GetFormatNameDimension($format) {
		$list_arr = ReflectionModel::getClassConstList(__CLASS__, 'THUMB_');
		$list_arr = array_flip($list_arr);
		$name = ifsetor($list_arr[$format], $list_arr[self::THUMBNAIL_DEFAULT]);
		$name = _str_replace('THUMB_', '', $name);
		return $name;
	}

	public static function GetFormatDimension($format, $s_width=0, $s_height=0) {

		list($width, $height) = array(0, 0);

		$format = self::GetCorrectFormat($format);
		$dimension = self::GetFormatNameDimension($format);
		$fi = 1.618;

		switch($format) {
			case self::THUMB_1000:
				$width = 1100;
				$height = 900;
				break;
			case self::THUMB_75:
			case self::THUMB_240:
				$width = $height = $dimension;
				break;
			case self::THUMB_301:
				list($width, $height) = array($dimension, floor($dimension*2.5));
				break;
			default:
				list($width, $height) = array($dimension, ceil($dimension/$fi));
				if($s_width<$s_height) list($width, $height) = array($height, $width);
		}

		return array($width, $height);
	}

	public static function GetThumbLocalPath($photo_id, $format) {
		static $md5_cache = array();
		$photo_id = Cast::unsignint($photo_id);
		$hash_id = $hash_map[$photo_id] = ifsetor($hash_map[$photo_id], md5($photo_id.PHOTO_THUMB_SECRET));
		$path = array();
		$path[] = _rtrim(I_PATH, '/');
		$path[] = 'photo';
		$path[] = self::GetFormatNameDimension($format);
		$path[] = _substr($hash_id, 0, 1);
		$path[] = _substr($hash_id, 1, 1);
		$path[] = $photo_id.'x'._substr($hash_id, 2, 7).'.jpg';
		$local = implode('/', $path);
		return $local;
	}

	/**
	 * @param int $photo_id
	 * @param const $format
	 * @param int $param [anti web browser cache]
	 */
	public static function GetThumbWebPath($photo_id, $format, $param=0) {
		$local = self::GetThumbLocalPath($photo_id, $format);
		$web = Url::i_local2web($local);
		if(0 && $web && $param && ($param = Cast::unsignint($param))) {
			$web .= '?'.$param;
		}
		return $web;
	}

	public static function GetBlankLocalPath($format) {
		$format = self::GetCorrectFormat($format);
		$local = sprintf('%s/photo/blank/%u.jpg', _rtrim(I_PATH, '/'), $format);
		return $local;
	}

	public static function GetBlankWebPath($format) {
		$local = self::GetBlankLocalPath($format);
		$web = Url::i_local2web($local);
		return $web;
	}

	// ---

	public static function Create($photo_id, $s_filepath) {

		$affected = 0;

		$formats = self::GetFormatValueList();

		foreach($formats as $format) {
			$success = self::CreateThumb($photo_id, $format, $s_filepath);
			if($success) {
				$affected++;
			}
		}
		return $affected;
	}


	public static function CreateThumb($photo_id, $format, $s_filepath) {

		$affected = 0;

		$photo_id = Cast::unsignint($photo_id);
		$format = self::GetCorrectFormat($format);

		$t_filepath = self::GetThumbLocalPath($photo_id, $format);

		$img_info = Im::GetImageInfo($s_filepath);

		if($img_info && strcmp($s_filepath, $t_filepath)!==0) {

			$s_width = ifsetor($img_info['width'], 0);
			$s_height = ifsetor($img_info['height'], 0);

			list($t_width, $t_height) = self::GetFormatDimension($format, $s_width, $s_height);

			$resized = false;

			switch($format) {

				case self::THUMB_1000:
				case self::THUMB_301:

					$quality = $format==self::THUMB_301 ? '80%' : '90%';

					$resized = Im::Resize(
						$s_filepath,
						$t_filepath,
						$t_width,
						$t_height,
						Im::IM_RESIZE_MODE_SHRINK,
						$quality
					);
					break;

				case self::THUMB_75:
				case self::THUMB_150:
				case self::THUMB_240:
				case self::THUMB_300:

					$enlarge_tolerance = 1;
					$resized = Im::ResizeSmart(
						$s_filepath,
						$t_filepath,
						$t_width,
						$t_height,
						$enlarge_tolerance,
						'80%'
					);
					break;

			}

			if($resized) {

				$t_info = Im::GetImageInfo($t_filepath);

				if($t_info) {

					$t_width = ifsetor($t_info['width'], 0);
					$t_height = ifsetor($t_info['height'], 0);
					$t_filesize = ifsetor($t_info['filesize'], 0);

					$affected = self::Insert($photo_id, $format, $t_width, $t_height, $t_filesize);
				}
			}

		}

		return $affected;
	}

	public static function CreateThumbIfNotExists($photo_id, $t_format, $s_format=self::THUMBNAIL_ORIGINAL) {
		$affected = 0;
		$t_format = self::GetCorrectFormat($t_format);
		$s_format = self::GetCorrectFormat($s_format);
		if($t_format!=$s_format) {
			$t_filepath = self::GetThumbLocalPath($photo_id, $t_format);
			if(!file_exists($t_filepath)) {
				$s_filepath = self::GetThumbLocalPath($photo_id, $s_format);
				$affected = self::CreateThumb($photo_id, $t_format, $s_filepath);
			}
		}
		return $affected;
	}

	public static function Insert($photo_id, $format, $width, $height, $filesize) {

		$affected = 0;

		$photo_id = Cast::unsignint($photo_id);
		$format = self::GetCorrectFormat($format);
		$width = Cast::unsignint($width);
		$height = Cast::unsignint($height);
		$filesize = Cast::unsignint($filesize);

		if($photo_id) {

			$__db =& __db();

			$insert_arr = array(
				'photo_id' => $photo_id,
				'format' => $format,
				'width' => $width,
				'height' => $height,
				'filesize' => $filesize,
				'tstamp' => time(),
			);

			$insert_sql = MySQL::prepare_fields($insert_arr);

			$sql = sprintf('INSERT INTO %1$s SET %2$s ON DUPLICATE KEY UPDATE %2$s', self::db_thumb(), $insert_sql);

			$affected = $__db->u($sql);

			if($affected) {

				self::$cache[$photo_id][$format] = $insert_arr;
			}
		}

		return $affected;
	}

	public static function Get($photo_id, $format=null) {

		$result = array();

		$photo_id = Cast::unsignintarr($photo_id);

		$format = is_null($format) ? null : self::GetCorrectFormat($format);

		if(!empty($photo_id)) {

			$__db =& __db();

			$sql_where_arr = array();
			$sql_where_arr[] = MySQL::sqlInClause('photo_id', $photo_id);
			if($format) {
				$sql_where_arr[] = MySQL::prepare_fields(array('format'=>$format));
			}

			$sql_where_str = implode(' AND ', $sql_where_arr);

			$sql = sprintf('SELECT * FROM %1$s WHERE %2$s', self::db_thumb(), $sql_where_str);
			$sql_r = $__db->q($sql);

			while($row = $sql_r->next()) {
				$result[$row['photo_id']][$row['format']] = $row;
			}

			self::$cache += $result;
		}

		return $result;

	}

	public static function GetOne($photo_id, $format=null) {
		$one = reset(self::Get($photo_id, $format));
		return $one ? $one : array();
	}

	// ---

	public static function GetPrepared($photo_id, $format=null) {

		$result = array();

		$photo_id = Cast::unsignintarr($photo_id);
		$format = is_null($format) ? null : self::GetCorrectFormat($format);
		$formats = self::GetFormatValueList();

		$data = self::Get($photo_id, $format);

		foreach($photo_id as $p_id) {

			$p_data = ifsetor($data[$p_id], array());

			foreach($formats as $f) {

				$p_format = ifsetor($p_data[$f], array());

				if(empty($p_format)) {

					list($width, $height) = self::GetFormatDimension($f);

					$p_format = array(
						'photo_id' => $p_id,
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
					$p_format['tstamp'] = $p_format['tstamp'];
					$p_format['src'] = self::GetThumbWebPath($p_id, $f, $p_format['tstamp']);
					$p_format['local'] = self::GetThumbLocalPath($p_id, $f, $p_format['tstamp']);
				}

				if(is_null($format) || $format==$f) {
					$result[$p_id][$f] = $p_format;
				}
			}
		}

		return $result;
	}

	public static function GetOnePrepared($photo_id, $format=null) {
		$one = reset(self::GetPrepared($photo_id, $format));
		return $one ? $one : array();
	}

	// ---

	public static function GetPreparedBlind($photo_id, $tstamp=0) {

		$result = array();

		$photo_id = Cast::unsignintarr($photo_id);
		$formats = self::GetFormatValueList();

		$tstamp = Cast::unsignint($tstamp);

		foreach($photo_id as $p_id) {

			foreach($formats as $format) {

				list($width, $height) = array('', '');

				if($tstamp) {
					list($width, $height) = self::GetFormatDimension($format);
				}

				$result[$p_id][$format] = array(
					'photo_id' => $p_id,
					'format' => $format,
					'width' => '',//$width,
					'height' => '',//$height,
					'filesize' => 0,
					'tstamp' => $tstamp,

					'src' => $tstamp ? self::GetThumbWebPath($p_id, $format, $tstamp) : self::GetBlankWebPath($format),
					'local' => $tstamp ? self::GetThumbLocalPath($p_id, $format) : self::GetBlankLocalPath($format),
				);

			}
		}

		return $result;
	}

	public static function GetOnePreparedBlind($photo_id, $tstamp=0) {
		$array = self::GetPreparedBlind($photo_id, $tstamp);
		$one = $array ? reset($array) : array();
		return $one ? $one : array();
	}

	// ---

	public static function Remove($photo_id, $format=null) {

		$affected = 0;

		$photo_id = Cast::unsignintarr($photo_id);

		$format = is_null($format) ? null : self::GetCorrectFormat($format);

		if(!empty($photo_id)) {

			$__db =& __db();

			// remove db userpic records
			$sql_where_arr = array();
			$sql_where_arr[] = MySQL::sqlInClause('photo_id', $photo_id);
			if($format) {
				$sql_where_arr[] = MySQL::prepare_fields(array('format'=>$format));
			}

			$sql_where_str = implode(' AND ', $sql_where_arr);

			$sql = sprintf('DELETE FROM %1$s WHERE %2$s', self::db_thumb(), $sql_where_str);
			$affected += $__db->u($sql);

			$formats = self::GetFormatValueList();

			// remove files on disk
			foreach($photo_id as $p_id) {

				if(is_null($format)) {
					foreach($formats as $f) {
						$local_path = self::GetThumbLocalPath($p_id, $f);
						if(file_exists($local_path)) {
							_e('del '.$local_path);
							unlink($local_path);
						}
					}
				}
				else {
					$local_path = self::GetThumbLocalPath($p_id, $format);
					if(file_exists($local_path)) {
						_e('del '.$local_path);
						unlink($local_path);
					}
				}
			}

			self::ClearCache($photo_id, $format);
		}

		return $affected;
	}

	public static function ClearCache($photo_id=null, $format=null) {

		if(self::$cache) {

			$photo_id = Cast::unsignintarr($photo_id);
			$format = is_null($format) ? null : self::GetCorrectFormat($format);

			foreach(self::$cache as $c_pid=>&$c_data) {
				if(!$photo_id || in_array($c_pid, $photo_id)) {
					if(isset($c_data[$format])) {
						unset($c_data[$format]);
					}
					if(is_null($format) || empty(self::$cache[$c_pid])) {
						unset(self::$cache[$c_pid]);
					}
				}
			}
		}
		return self::$cache;
	}

}
