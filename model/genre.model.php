<?

class GenreModel {

	const GENRE_STATIC_PATH = '/genre/genre.static.php';

	const GENRE_FIELDS = 'g.id, g.name_ru, g.name_ua, g.name_en, g.name_url, g.active';

	private static $genreList = null;

	public static function db_genre() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'genre');
	}

	public static function file_genre() {
		return STATIC_PATH . self::GENRE_STATIC_PATH;
	}

	public static function getPropertyGenreList() {
		return self::$cityList;
	}

	public static function &GetGenreList($refreshCache=false) {

		if(is_null(self::$genreList) || $refreshCache) {

			$__db =& __db();

			$sql = sprintf('SELECT %s FROM %s AS g WHERE active=1', self::GENRE_FIELDS, self::db_genre());

			$sql_r = $__db->q($sql);

			while($row = $sql_r->next()) {
				self::$genreList[$row['id']] = $row;
			}
		}

		self::$genreList =& self::LocaleNameGenre(self::$genreList);

		return self::$genreList;
	}

	public static function &GetStaticGenreList($refreshStatic=false) {

		if(is_null(self::$genreList) || $refreshStatic) {
			if($refreshStatic) {
				self::$genreList =& self::GenerateGenreStatic();
			}
			else {
				$file = self::file_genre();
				@include($file);
				self::$genreList = ifsetor($genre, array());
				if(empty(self::$genreList)) {
					$self_func = __FUNCTION__;
					self::$genreList =& self::$self_func(true);
				}
			}
		}

		self::$genreList =& self::LocaleNameGenre(self::$genreList);

		return self::$genreList;
	}

	/**
	 * @param mixed(int|array) $g_id
	 * @return (hash) array
	 */
	public static function GetGenreListByGenreId($g_id) {

		$List = array();

		$g_id = Cast::unsignintarr($g_id);

		if(!empty($g_id)) {

			$genreList =& self::GetStaticGenreList();

			foreach($g_id as $id) {
				if(array_key_exists($id, $genreList)) {
					$List[$id] = $genreList[$id];
				}
			}
		}

		return $List;
	}

	public static function GetOneGenreListByGenreId($g_id) {
		$array = self::GetGenreListByGenreId($g_id);
		return $array ? reset($array) : array();
	}

	/**
	 * @param mixed(str|array) $g_alias
	 * @return (hash) array
	 */
	public static function GetGenreListByGenreAlias($g_alias) {

		$List = array();

		$g_alias = Cast::strarr($g_alias);

		if(!empty($g_alias)) {

			$genreList =& self::GetStaticGenreList();

			foreach($g_alias as $alias) {
				foreach($genreList as $id=>$item) {
					if($item['name_url']==$alias) {
						$List[$id] = $genreList[$id];
						break;
					}
				}
			}
		}

		return $List;
	}

	public static function GetOneGenreListByGenreAlias($g_alias) {
		$array = self::GetGenreListByGenreAlias($g_alias);
		return $array ? reset($array) : array();
	}

	public static function &GenerateGenreStatic() {

		$List =& self::GetGenreList(true);
		FileFunc::saveVarsToFile(self::file_genre(), array('genre' => &$List));
		return $List;
	}

	public static function &LocaleNameGenre(&$list) {

		$list =& LocaleModel::LocaleNameModifyList($list);
		return $list;
	}
}
