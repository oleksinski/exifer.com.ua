<?

class ErrorModel {

	protected $errors = array();

	public static $desc = array(
		'EMAIL_FORMAT' => 'Неверный e-mail',
		'EMAIL_EXISTS' => 'Такой e-mail уже зарегистрирован. Воспользуйтесь службой восстановления пароля',
		'EMAIL_SPAMER_EXISTS' => 'Указанный email занесен в черный список',
		'EMAIL_NOT_EXISTS' => 'Такой e-mail не зарегистрирован',
		'PASSWORD_FORMAT' => 'Неверный формат пароля',
		'PASSWORD_NOT_MATCH' => 'Неверный пароль',
		'PASSWORD_NEW_CONF' => 'Для подтверждения смены пароля укажите текущий пароль',
		'NAME_FORMAT' => 'Неверный формат имени',
		'URLNAME_FORMAT' => 'Неверный формат URL-имени',
		'GENDER_FORMAT' => 'Неверный формат пола',
		'STATUS_FORMAT' => 'Неверный статус',
		'ABOUT_FORMAT' => 'Неверный формат описания про себя',
		'BIRTHDAY_FORMAT' => 'Неверная дата рождения',
		'EULA_CONFIRM' => 'Не отмечено соглашение с правилами пользования сайтом',
		'COUNTRY_FORMAT' => 'Не выбрана страна',
		'CITY_FORMAT' => 'Не выбран город',
		'COUNTRY_CITY_FORMAT' => 'Город не соответствует выбранной стране',
		'BIRTHDAY_FORMAT' => 'Неверно указана дата рождения',

		'AUTH_FAIL' => 'Неверный email и/или пароль',
		'AUTH_NOT_ACTIVATED' => 'Регистрация не активирована',
		'AUTH_LOCKED' => 'Ваш аккаунт заблокирован администратором',
		'AUTH_REMIND_SEND' => 'Письмо с регистрационными данными было отправлено на %s',
		'AUTH_REMIND_DDOS' => 'Количество попыток напоминания пароля подряд ограничено. Повторите попытку позже',
		'AUTH_BANNED' => 'Аккаунт заблокирован до %s',

		'DATA_SAVE_FAIL' => 'Ошибка сохранения данных',

		'UPLOAD_INI_SIZE' => 'Размер принятого файла %s превысил максимально допустимый размер %s',
		'UPLOAD_FORM_SIZE' => 'Размер принятогоо файла %s превысил максимально допустимый в HTML-форме размер %s',
		'UPLOAD_PARTIAL' => 'Загружаемый файл %s был получен только частично',
		'UPLOAD_NO_FILE' => 'Файл %s не был загружен',
		'UPLOAD_NO_TMP_DIR' => 'Временная директория загружаемого файла %s не найдена',
		'UPLOAD_CANT_WRITE' => 'Загружаемый файл %s не может быть принят',
		'UPLOAD_EXTENSION' => 'Ошибка при загрузке файла %s',
		'UPLOAD_UNKNOWN' => 'Файл %s не был загружен',

		'FILE_NOT_IMAGE' => 'Файл %s не является изображением',

		'CAPTCHA_ERROR' => 'Неверный защитный код',

		'PHOTO_NAME' => 'Неверный формат названия фото',
		'PHOTO_DESCRIPTION' => 'Неверный формат описания фото',
		'PHOTO_GENRE' => 'Не выбран жанр фото',
		'PHOTO_UPLOAD' => 'Не удалось загрузить фото. Попробуйте еще раз',
		'PHOTO_UPLOAD_EXCEEDED' => 'Добавление новых фотографий будет доступно с %s',
		'PHOTO_DIMENSION' => 'Ширина / высота загружаемого изображения менее %s px',
		'PHOTO_FORMAT' => 'Формат фото не поддерживается',

		'PHOTO_UPLOAD_INACTIVE' => 'Загрузка фото временно отключена',
		'USER_REGISTER_INACTIVE' => 'Регистрация временно отключена',

		'FEEDBACK_MESSAGE' => 'Неверный формат сообщения',

		'OBLIG_FIELDSET_EMPTY' => 'Не заполнены обязательные поля',
		'DEFAULT_COUNTRY_DEL' => 'Нельзя удалить страну %s',
		'DEFAULT_CITY_DEL' => 'Нельзя удалить город %s',
		'DEFAULT_STATE_DEL' => 'Нельзя удалить область/республику %s',

		'COMMENT_ERROR' => 'Ошибка',
		'COMMENT_NO_ITEM' => 'Неизвестно что комментируем',
		'COMMENT_NO_UID' => 'Комментарии могут оставлять только зарегистиррованные пользователи',
		'COMMENT_NO_TEXT' => 'Текст комментария пуст или содержит неразрешенные символы',
		'COMMENT_TEXT_DUPLICATE' => 'Не повторяйтесь',
		'COMMENT_NO_ADD_PERMISSION' => 'Вы не можете добавить комментарий',
		'COMMENT_NO_EDIT_PERMISSION' => 'Вы не можете редактировать комментарий',
		'COMMENT_NO_CLEAR_PERMISSION' => 'Вы не можете очистить текст комментарий',
		'COMMENT_NO_DEL_PERMISSION' => 'Вы не можете удалить комментарий',
		'COMMENT_COORD_ERROR' => 'Недопустимое значение координат изображения',
		'COMMENT_COORD_ERROR_NUMBER' => 'Недопустимое значение координат изображения (координата %s)',
		'COMMENT_COORD_NO_TEXT' => 'Кроме координат изображения комментарий должен содержать текст',
		'COMMENT_POST_DELAY' => 'Не так быстро. Не чаще 1 комментария в %d сек',
		'COMMENT_LIMIT_PER_DAY' => 'Лимит комментариев (%d) на сегодня исчерпан. Добавление комментариев будет доступно с %s',

		'VOTE_NO_ITEM' => 'Неизвестно что оцениваем',
		'VOTE_NO_UID' => 'Оценивать могут только зарегистиррованные пользователи',
		'VOTE_NO_TYPE' => 'Неправильный тип оценки',
		'VOTE_SELF' => 'Свои работы оценивать нельзя',
		'VOTE_DUPLICATE' => 'Ваш голос был ранее учтен',
		'VOTE_NO_MODER_PERMISSION' => 'У Вас нет прав удалять оценки',
		'VOTE_LIMIT_PER_DAY' => 'Лимит оценок (%d) на сегодня исчерпан. Новые оценки будут доступны с %s',
		'VOTE_PHOTO_NO_COMMENT' => 'Сначала прокомментируйте фотографию',
	);

	/**
	 * @return ErrorModel
	 */
	public function clearErrors() {
		$this->errors = array();
		return $this;
	}

	/**
	 * @return array
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 *
	 * @param array $errors
	 * @return ErrorModel
	 */
	public function setErrors(array $errors) {
		$this->errors = $errors;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getErrorKeys() {
		return array_keys($this->errors);
	}

	/**
	 * @return array
	 */
	public function getErrorValues() {
		return array_values($this->errors);
	}

	/**
	 * @return bool
	 */
	public function isError() {
		return count($this->errors)>0;
	}

	/**
	 * @return bool
	 */
	public function isOk() {
		return !$this->isError();
	}

	/**
	 *
	 * @param unknown_type $id
	 * @param unknown_type $params
	 * @return string
	 */
	public function get($id, $params=null) {

		$error = null;

		if(array_key_exists($id, self::$desc)) {

			$error = self::$desc[$id];

			$params = (array)$params;

			if(is_array($params) && !empty($params)) {

				array_unshift($params, $error);
				$error = call_user_func_array('sprintf', $params);
			}
		}

		return $error;
	}

	/**
	 *
	 * @param unknown_type $id
	 * @param unknown_type $params
	 * @return ErrorModel
	 */
	public function push($id, $params=null) {

		$error = $this->get($id, $params);
		if($error) {
			$this->errors[$id] = $error;
		}

		return $this;
	}

	/**
	 *
	 * @param unknown_type $error
	 * @param unknown_type $params
	 * @return ErrorModel
	 */
	public function pushRandom($error, $params=null) {

		$key = $this->extend($error);

		if($key) {
			$this->push($key, $params);
		}

		return $this;
	}

	/**
	 *
	 * @param unknown_type $error
	 * @return string
	 */
	private function extend($error) {

		$array_values = array_values(self::$desc);
		$array_keys = array_keys(self::$desc);

		$key = null;

		if(!in_array($error, $array_values)) {
			$key = max($array_keys)+1;
			self::$desc[$key] = $error;
		}
		else {
			foreach(self::$desc as $id=>$err) {
				if($err==$error) {
					$key = $id;
					break;
				}
			}
		}

		return $key;
	}

}
