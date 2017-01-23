<?


class ValidatorModel {

	const AGE_MAX = 80;
	const AGE_MIN = 10;
	const AGE_AVG = 25;

	const __ERROR_REGEXP = 1;
	const __ERROR_LENGTH = 2;

	public $emailMinLength = 1;
	public $emailMaxLength = 100;

	public $nameMinLength = 2;
	public $nameMaxLength = 100;

	public $urlnameMinLength = 1;
	public $urlnameMaxLength = 100;

	public $aboutMinLength = 0;
	public $aboutMaxLength = 2000;

	public $passwordMinLength = 4;
	public $passwordMaxLength = 64;

	public $photoNameMinLength = 0;
	public $photoNameMaxLength = 255;

	public $photoDescMinLength = 0;
	public $photoDescMaxLength = 2000;

	public $feedbackMinLength = 1;
	public $feedbackMaxLength = 2000;

	public $urlMinLength = 4;
	public $urlMaxLength = 255;

	public $phoneMinLength = 10;
	public $phoneMaxLength = 15;

	public $maxEmailCount = 3;
	public $maxUrlCount = 7;
	public $maxPhoneCount = 3;
	public $maxIMCount = 5;

	public $birthYearMinValue = null; // init in constructor
	public $birthYearMaxValue = null; // init in constructor

	public $__error = null;

	public function __construct() {
		$year = date('Y', time());
		$this->birthYearMinValue = $year - self::AGE_MAX;
		$this->birthYearMaxValue = $year - self::AGE_MIN;
	}

	public function user_email($what) {
		$re_pattern = Regexp::re_email_exact();
		return $this->checkString($what, $re_pattern, $this->emailMinLength, $this->emailMaxLength);
	}

	public function user_name($what) {
		$re_pattern = Regexp::re_pattern_from_start_to_end('[\p{L}\p{Nd}_]+[\p{L}\p{Nd}_\s]*', 'i');
		return $this->checkString($what, $re_pattern, $this->nameMinLength, $this->nameMaxLength);
	}

	public function user_urlname($what) {
		$re_pattern = Regexp::re_pattern_from_start_to_end('[a-z][a-z0-9_\.]+', 'i');
		return $this->checkString($what, $re_pattern, $this->urlnameMinLength, $this->urlnameMaxLength);
	}

	public function user_about($what) {
		$re_pattern = Regexp::re_pattern_from_start_to_end('.*', array('i','s'));
		return $this->checkString($what, $re_pattern, $this->aboutMinLength, $this->aboutMaxLength);
	}

	public function user_password($what) {
		$re_pattern = Regexp::re_pattern_from_start_to_end('[^\s]+', 'i');
		return $this->checkString($what, $re_pattern, $this->passwordMinLength, $this->passwordMaxLength);
	}

	public function user_birthday($day, $month, $year) {
		$bool = DateConst::check_date($day, $month, $year);
		$bool = $bool && ($year >= $this->birthYearMinValue);
		$bool = $bool && ($year <= $this->birthYearMaxValue);
		return $bool;
	}

	public function user_birthday_tstamp($t_stamp) {
		$day = DateConst::getDay($t_stamp);
		$month = DateConst::getMonth($t_stamp);
		$year = DateConst::getYear($t_stamp);
		return $this->user_birthday($day, $month, $year);
	}

	public function photo_name($what) {
		$re_pattern = Regexp::re_pattern_from_start_to_end('.*', 'i');
		return $this->checkString($what, $re_pattern, $this->photoNameMinLength, $this->photoNameMaxLength);
	}

	public function photo_desc($what) {
		$re_pattern = Regexp::re_pattern_from_start_to_end('.*', array('i','s'));
		return $this->checkString($what, $re_pattern, $this->photoDescMinLength, $this->photoDescMaxLength);
	}

	public function feedback_message($what) {
		$re_pattern = Regexp::re_pattern_from_start_to_end('.+', array('i','s'));
		return $this->checkString($what, $re_pattern, $this->feedbackMinLength, $this->feedbackMaxLength);
	}

	public function isRegexpError() {
		return $this->__error == self::__ERROR_REGEXP;
	}

	public function isLengthError() {
		return $this->__error == self::__ERROR_LENGTH;
	}

	private function checkString($string, $regexp, $minLength, $maxLength) {

		$bool = false;

		$regexp_bool = Regexp::match($regexp, $string);

		if($regexp_bool!==false) {

			$length_bool = _strlen($string)>=$minLength && _strlen($string)<=$maxLength;

			if($length_bool) {
				$this->__error = null;
				$bool = true;
			}
			else {
				$this->__error = self::__ERROR_LENGTH;
			}
		}
		else {
			$this->__error = self::__ERROR_REGEXP;
		}

		return $bool;
	}

}
