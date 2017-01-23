<?

class SeoModel {

	const TITLE = 1;
	const DESCRIPTION = 2;
	const KEYWORDS = 3;

	/**
	 *
	 * @param unknown_type $target
	 */
	public static function index($target=self::TITLE) {
		$result = null;
		switch($target) {
			case self::TITLE:
				$result = sprintf('Проводник в мире художественной фотографии %s', URL_NAME);
				break;
			case self::DESCRIPTION:
				$result = sprintf('Фотосайт художественной фотографии %s', URL_NAME);
				break;
			case self::KEYWORDS:
				$keywordList = array(
					'фотография',
					'фотографии',
					'фото',
					'цифровая фотография',
					'художественная фотография',
					'красивые фотографии',
					'обработка фотографий',
					'фотограф',
					'фотоклуб',
					'фотосессия',
					'фотоарт',
					'фотокамера',
					'фотостудия',
					'фотосъемка',
					'фотогалерея',
					'портрет',
					'обнаженная натура',
					'ню',
					'жанр',
					'exif',
				);
				$result = implode(', ', $keywordList);
				break;
		}
		return $result;
	}

	/**
	 * @param unknown_type $photo
	 * @param unknown_type $target
	 */
	public static function photo(Photo $photo, $target=self::TITLE) {

		$result = null;

		$genre = GenreModel::GetOneGenreListByGenreId($photo->getField('genre_id'));
		$user = $photo->getUserObject();

		$photoName = ifsetor($photo->getField('name'), $photo->getField('orig_name'), true);
		$genreName = ifsetor($genre['name'], null);
		$userName = $user->getExtraField('name');
		$userOccupation = self::user_occupation($user);
		$userLocation = self::user_location($user);

		switch($target) {

			case self::TITLE:
			case self::DESCRIPTION:
				$parts = array();

				if($target==self::DESCRIPTION) {
					$description = Text::cutStr($photo->getField('description'), 300);
					if($description) {
						$parts[] = sprintf('%s / ', $description);
					}
				}

				$parts[] = 'Фотография';
				$parts[] = self::escapePhoto($photoName);
				if($genreName) {
					$tmp = array(
						self::TITLE => sprintf('(%s)', _ucfirst($genreName)),
						self::DESCRIPTION => sprintf('по жанру &laquo;%s&raquo;', _ucfirst($genreName)),
					);
					if(isset($tmp[$target])) {
						$parts[] = $tmp[$target];
					}
				}
				if($userName) {
					$tmp = array(
						self::TITLE => sprintf('/ %s', $userName),
						self::DESCRIPTION => sprintf('от автора %s', $userName),
					);
					if(isset($tmp[$target])) {
						$parts[] = $tmp[$target];
					}
				}
				if($userOccupation || $userLocation) {
					$partsTmp = array();
					if($userOccupation) {
						$partsTmp[] = $userOccupation;
					}
					if($userLocation) {
						if($userOccupation) {
							$partsTmp[] = '-';
						}
						$partsTmp[] = sprintf('%s', $userLocation);
					}
					if($partsTmp) {
						$parts[] = sprintf('(%s)', implode(' ', $partsTmp));
					}
				}

				$result = implode(' ', $parts);
				break;

			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	/**
	 *
	 * @param array $params
	 * @param unknown_type $target
	 */
	public static function photo_lenta(array $params, $target=self::TITLE) {

		$result = null;

		switch($target) {

			case self::TITLE:
			case self::DESCRIPTION:

				$parts = array();

				$genre = ifsetor($params['genre'], array());
				$genreName = ifsetor($genre['name'], null);

				$time = self::time_period($params);

				$user = ifsetor($params['user'], null);
				$userName = null;
				$userOccupation = null;
				$userLocation = null;
				if(is_a($user, 'User') && $user->exists()) {
					$userName = $user->getField('name');
					$userOccupation = self::user_occupation($user);
					$userLocation = self::user_location($user);
				}
				else {
					$user = null;
				}

				if($target==self::DESCRIPTION) {
					$parts[] = self::getDateFormat();
				}

				$parts[] = 'Галерея фотографий';

				// genre
				if($genreName) {
					$parts[] = sprintf('в жанре &laquo;%s&raquo;', $genreName);
				}

				if($time) {
					$parts[] = sprintf('за период %s', $time);
				}

				// user
				if($user) {
					if($userName) {
						if($target==self::TITLE) {
							$parts[] = '/';
							if($userOccupation) {
								$parts[] = $userOccupation;
							}
							$parts[] = sprintf('%s', $userName);
						}
						elseif($target==self::DESCRIPTION) {
							$parts[] = sprintf('от автора %s', $userName);
							if($userOccupation || $userLocation) {
								$partsTmp = array();
								if($userOccupation) {
									$partsTmp[] = $userOccupation;
								}
								if($userLocation) {
									if($userOccupation) {
										$partsTmp[] = '-';
									}
									$partsTmp[] = sprintf('%s', $userLocation);
								}
								if($partsTmp) {
									$parts[] = sprintf('(%s)', implode(' ', $partsTmp));
								}
							}
						}
					}
				}

				if($target==self::DESCRIPTION && !$genreName) {
					$genreList =& GenreModel::GetStaticGenreList();
					$genres = array();
					foreach($genreList as $genre_id=>$genre) {
						$genreName = ifsetor($genre['name'], null);
						if($genreName) {
							$genres[]= $genreName;
						}
					}
					if($genres) {
						$parts[] = sprintf('по жанрам %s', implode(', ', $genres));
					}
				}

				$result = implode(' ', $parts);
				break;

			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	/**
	 * @param unknown_type $target
	 */
	public static function photo_upload($target=self::TITLE) {
		return self::default_seo($target, 'Загрузка фотографий');
	}

	/**
	 *
	 * @param unknown_type $photo
	 * @param unknown_type $target
	 */
	public static function photo_edit(Photo $photo, $target=self::TITLE) {
		$result = null;
		switch($target) {
			case self::TITLE:
			case self::DESCRIPTION:
				$parts = array();
				if($target==self::DESCRIPTION) {
					$parts[] = self::getDateFormat();
				}
				$genre = GenreModel::GetOneGenreListByGenreId($photo->getField('genre_id'));
				$genreName = ifsetor($genre['name'], null);
				$parts[] = 'Редактирование фотографии';
				$photoName = $photo->getField('name');
				$parts[] = $photoName ? self::escapePhoto($photoName) : '*****';
				if($genreName) {
					$parts[] = sprintf('/ жанр &laquo;%s&raquo;', _ucfirst($genreName));
				}
				if($target==self::DESCRIPTION) {
					$description = Text::cutStr($photo->getField('description'), 300);
					if($description) {
						$parts[] = sprintf('/ Описание: %s', $description);
					}
				}
				$result = implode(' ', $parts);
				break;
			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	/**
	 *
	 * @param unknown_type $photo
	 * @param unknown_type $target
	 */
	public static function photo_remove(Photo $photo, $target=self::TITLE) {
		$result = null;
		switch($target) {
			case self::TITLE:
			case self::DESCRIPTION:
				$parts = array();
				if($target==self::DESCRIPTION) {
					$parts[] = self::getDateFormat();
				}
				$genre = GenreModel::GetOneGenreListByGenreId($photo->getField('genre_id'));
				$genreName = ifsetor($genre['name'], null);
				$parts[] = 'Удаление фотографии';
				$photoName = $photo->getField('name');
				$parts[] = $photoName ? self::escapePhoto($photoName) : '*****';
				if($genreName) {
					$parts[] = sprintf('/ жанр &laquo;%s&raquo;', _ucfirst($genreName));
				}
				if($target==self::DESCRIPTION) {
					$description = Text::cutStr($photo->getField('description'), 300);
					if($description) {
						$parts[] = sprintf('/ Описание: %s', $description);
					}
				}
				$result = implode(' ', $parts);
				break;
			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	/**
	 * @param User $user
	 * @param unknown_type $target
	 */
	public static function user(User $user, $target=self::TITLE) {

		$result = null;

		switch($target) {
			case self::TITLE:
			case self::DESCRIPTION:

				$parts = array();

				if($target==self::DESCRIPTION) {
					$description = Text::cutStr($user->getField('about'), 300);
					if($description) {
						$parts[] = sprintf('%s / ', $description);
					}
				}

				$occupation = self::user_occupation($user);
				if($occupation) {
					$parts[] = $occupation;
				}

				$userName = $user->getField('name');
				if($userName) {
					$parts[] = sprintf('%s', $userName);
				}

				$location = self::user_location($user);
				if($location) {
					$parts[] = sprintf('(%s)', $location);
				}

				$result = implode(' ', $parts);
				break;

			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	/**
	 *
	 * @param array $params
	 * @param unknown_type $target
	 */
	public static function user_lenta(array $params, $target=self::TITLE) {

		$result = null;

		switch($target) {
			case self::TITLE:
			case self::DESCRIPTION:
				$parts = array();

				if($target==self::DESCRIPTION) {
					$parts[] = self::getDateFormat();
				}

				$parts[] = 'Пользователи сайта';

				// occupation
				$occupation = ifsetor($params['occupation'], array());
				if($occupation) {
					$occupation = ifsetor($occupation['name'], null);
					if($occupation) {
						$parts[] = sprintf('по специализации &laquo;%s&raquo;', $occupation);
					}

					// experience
					$experience = ifsetor($params['experience'], array());
					if($experience) {
						$experience = ifsetor($experience['name'], null);
						if($experience) {
							$parts[] = sprintf('(%s)', $experience);
						}
					}
				}
				elseif($target==self::DESCRIPTION) {
					$list =& OccupationModel::GetStaticOccupationList();
					$occupations = array();
					foreach($list as $id=>$o) {
						$o_name = ifsetor($o['name'], null);
						if($o_name) {
							$occupations[]= $o_name;
						}
					}
					if($occupations) {
						$parts[] = sprintf('по специальностям %s', implode(', ', $occupations));
					}
				}

				$country = ifsetor($params['country'], array());
				if($country) {

					$locationParts = array();

					$city = ifsetor($params['city'], array());
					$city = ifsetor($city['name'], null);
					if($city) {
						$locationParts[] = $city;
					}
					$country = ifsetor($country['name'], null);
					if($country) {
						$locationParts[] = $country;
					}
					if($locationParts) {
						$parts[] = sprintf('/ %s', implode(', ', $locationParts));
					}
				}
				elseif($target==self::DESCRIPTION) {
					$list =& LocationModel::GetStaticCountryList();
					$countries = array();
					foreach($list as $id=>$c) {
						$c_name = ifsetor($c['name'], null);
						if($c_name) {
							$countries[]= $c_name;
						}
					}
					if($countries) {
						$parts[] = sprintf('из стран %s', implode(', ', $countries));
					}
				}

				$time = self::time_period($params);
				if($time) {
					$parts[count($parts)-1] .= sprintf(', зарегистрированные %s', $time);
				}

				$result = implode(' ', $parts);
				break;
			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	/**
	 * @param unknown_type $target
	 */
	public static function user_register($target=self::TITLE) {
		return self::default_seo($target, 'Регистрация нового пользователя');
	}

	/**
	 *
	 * @param unknown_type $user
	 * @param unknown_type $target
	 */
	public static function user_edit(User $user, $target=self::TITLE) {
		$result = null;
		switch($target) {
			case self::TITLE:
			case self::DESCRIPTION:
				$parts = array();
				if($target==self::DESCRIPTION) {
					$parts[] = self::getDateFormat();
				}
				$parts[] = 'Редактирования профайла';
				$parts[] = '/';
				$occupation = self::user_occupation($user);
				if($occupation) {
					$parts[] = $occupation;
				}
				$parts[] = $user->getField('name');
				$location = self::user_location($user);
				if($location) {
					$parts[] = sprintf('/ %s', $location);
				}
				$result = implode(' ', $parts);
				break;
			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	/**
	 * @param unknown_type $target
	 */
	public static function auth_login($target=self::TITLE) {
		return self::default_seo($target, 'Вход на сайт');
	}

	/**
	 * @param unknown_type $target
	 */
	public static function auth_remind($target=self::TITLE) {
		return self::default_seo($target, 'Напоминание пароля пользователя');
	}

	/**
	 *
	 * @param array $params
	 * @param unknown_type $target
	 */
	public static function comment_lenta(array $params, $target=self::TITLE) {

		$result = null;

		switch($target) {
			case self::TITLE:
			case self::DESCRIPTION:
				$parts = array();

				if($target==self::DESCRIPTION) {
					$parts[] = self::getDateFormat();
				}

				$parts[] = 'Комментарии к фотографиям';

				// user
				$user = ifsetor($params['user'], null);
				if(is_a($user, 'User') && $user->exists()) {

					$userName = $user->getField('name');
					if($userName) {
						$parts[] = sprintf('от пользователя %s', $userName);
						$userOccupation = self::user_occupation($user);
						if($userOccupation) {
							$parts[] = sprintf('/ %s', $userOccupation);
						}
						$userLocation = self::user_location($user);
						if($userLocation) {
							$parts[] = sprintf('(%s)', $userLocation);
						}
					}
				}

				// genre
				$genre = ifsetor($params['genre'], array());
				$genreName = ifsetor($genre['name'], null);
				if($genreName) {
					$parts[] = sprintf('в жанре &laquo;%s&raquo;', $genreName);
				}

				$time = self::time_period($params);
				if($time) {
					$parts[] = sprintf('за период %s', $time);
				}

				$result = implode(' ', $parts);
				break;
			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	public static function rss($target=self::TITLE) {
		return self::default_seo($target, 'RSS 2.0 - Подписка на RSS-ленту с фотографиями, пользователями, комментариями');
	}

	public static function support_feedback($target=self::TITLE) {
		return self::default_seo($target, 'Обратная связь');
	}

	public static function support_eula($target=self::TITLE) {
		return self::default_seo($target, 'Пользовательское соглашение');
	}

	public static function support_about($target=self::TITLE) {
		return self::default_seo($target, 'О сайте');
	}

	public static function support_rules($target=self::TITLE) {
			return self::default_seo($target, 'Правила сайта');
	}

	public static function support_adult($target=self::TITLE) {
		return self::default_seo($target, 'Просмотр страницы с содержанием &laquo;Для взрослых&raquo;');
	}

	/**
	 *
	 */
	public static function rss_photo() {
		return 'Лента фотографий';
	}

	/**
	 *
	 */
	public static function rss_user() {
		return 'Лента пользователей';
	}

	/**
	 *
	 */
	public static function rss_comment() {
		return 'Лента комментариев';
	}

	/**
	 *
	 */
	public static function rss_photo_comment(Photo $photo) {
		$result = null;
		$parts = array();
		$photoName = ifsetor($photo->getField('name'), $photo->getField('orig_name'), true);
		$parts[] = 'Последние комментарии к фотографии';
		if($photoName) {
			$parts[] = self::escapePhoto($photoName);
		}
		$result = implode(' ', $parts);
		return $result;
	}

	/**
	 *
	 */
	public static function rss_user_photo(User $user) {
		$result = null;
		$parts = array();
		$userName = $user->getField('name');
		$parts[] = 'Последние фотографии';
		if($userName) {
			$parts[] = sprintf('от %s', $userName);
		}
		$result = implode(' ', $parts);
		return $result;
	}

	/**
	 *
	 */
	public static function rss_user_comment(User $user) {
		$result = null;
		$parts = array();
		$userName = $user->getField('name');
		$parts[] = 'Последние комментарии';
		if($userName) {
			$parts[] = sprintf('от %s', $userName);
		}
		$result = implode(' ', $parts);
		return $result;
	}

	/**
	 * Replace special html symbols to simple text analogs
	 * @param unknown_type $string
	 */
	public static function htmlToRawText($string) {
		$search =  array('&laquo;', '&raquo;');
		$replace = array('"', '"');
		$string = _str_replace($search, $replace, $string);
		return $string;
	}

	/**
	 *
	 */
	private static function default_seo($target=self::TITLE, $string=null) {
		$result = null;
		switch($target) {
			case self::TITLE:
			case self::DESCRIPTION:
				if($target==self::DESCRIPTION) {
					$result .= self::getDateFormat().' ';
				}
				$result .= $string;
				break;
			case self::KEYWORDS:
				break;
		}
		return $result;
	}

	/**
	 *
	 * @param User $user
	 *
	 */
	private static function user_occupation(User $user) {
		$result = null;
		$occupation = $user->getExtraField('occupation');
		if($occupation) {
			$result = self::occupation($occupation);
		}
		return $result;
	}

	/**
	 *
	 * @param array $occupation
	 *
	 */
	private static function occupation(array $occupation) {
		$result = null;
		$idNameList = OccupationModel::getOccupationIdName($occupation);
		if($idNameList) {
			$parts = array_values($idNameList);
			$name = implode(', ', $parts);
			$result = _ucfirst(_strtolower($name));
		}
		return $result;
	}

	/**
	 *
	 * @param User $user
	 *
	 */
	private static function user_location(User $user) {

		$location = null;

		if(!$user->isBitmaskSet(User::BITMASK_HIDE_LOCATION)) {

			$location_arr = array();

			$country = $user->getField('country');
			$country_arr = LocationModel::GetOneCountryListByCountryId($country);

			$city = $user->getField('city');
			$city_arr = LocationModel::GetOneCityListByCityId($city);

			if(isset($country_arr['name'])) $location_arr[] = $country_arr['name'];
			if(isset($city_arr['name'])) $location_arr[] = $city_arr['name'];

			$location = implode(', ', $location_arr);
		}
		return $location;
	}

	/**
	 *
	 * @param array $params
	 */
	private static function time_period(array $params) {

		$time = null;

		$time_arr = array();

		$time_from = ifsetor($params['time_from'], 0);
		if($time_from) {
			$time_from = date('d.m.Y', $time_from);
			$time_arr[] = $time_from;
		}

		$time_to = ifsetor($params['time_to'], 0);
		if($time_to) {
			$time_to = date('d.m.Y', $time_to);
			$time_arr[] = $time_to;
		}

		$time = implode('-', $time_arr);

		return $time;
	}

	/**
	 *
	 * @param unknown_type $photo
	 *
	 */
	private static function escapePhoto($photo) {
		$result = null;
		$photoName = null;
		if(is_a($photo, 'Photo')) {
			$photoName = ifsetor($photo->getField('name'), $photo->getField('orig_name'), true);
		}
		elseif(is_string($photo)) {
			$photoName = $photo;
		}
		if($photoName) {
			$photoName = _trim($photoName, '"');
			$result = sprintf('&laquo;%s&raquo;', Text::cutStr($photoName, 100));
		}
		return $result;
	}

	private function getDateFormat($time=null) {
		return date('d.m.Y', is_null($time) ? time() : $time);
	}
}