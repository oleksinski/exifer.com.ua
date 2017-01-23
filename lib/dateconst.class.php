<?

class DateConst {

	public static function getTime($time=null) {
		$time = (int)(ifsetor($time, time()));
		return $time;
	}

	public static function getDay($time=null, $trailingZero=true) {
		return date($trailingZero ? 'd' : 'j', self::getTime($time));
	}

	public static function getMonth($time=null, $trailingZero=true) {
		return date($trailingZero ? 'm' : 'n', self::getTime($time));
	}

	public static function getYear($time=null) {
		return date('Y', self::getTime($time));
	}

	public static function getHour($time=null, $trailingZero=true) {
		return date($trailingZero ? 'H' : 'G', self::getTime($time));
	}

	public static function getMinutes($time=null) {
		return date('i', self::getTime($time));
	}

	public static function getSeconds($time=null) {
		return date('s', self::getTime($time));
	}

	public static function isVysokosnyYear($time=null) {
		return date('L', self::getTime($time));
	}

	public static function mk_time($day, $month, $year, $hour=0, $minutes=0, $seconds=0) {
		return mktime($hour, $minutes, $seconds, $month, $day, $year);
	}

	public static function check_date($day, $month, $year) {
		return checkdate($month , $day ,$year);
	}

	public static function getHumandDate($time) {
		$result = sprintf('%d %s %d',
			self::getDay($time, false),
			self::month_r(self::getMonth($time, false)),
			self::getYear($time)
		);
		return $result;
	}

	/**
	 * Return months in format 'январь', 'февраль', etc
	 *
	 * @return array
	 */
	public static function months($month=0) {

		$monthList = array(
			1 => 'январь',
				 'февраль',
				 'март',
				 'апрель',
				 'май',
				 'июнь',
				 'июль',
				 'август',
				 'сентябрь',
				 'октябрь',
				 'ноябрь',
				 'декабрь',
		);

		if(array_key_exists($month, $monthList)) {
			return $monthList[$month];
		}
		else {
			return $monthList;
		}
	}

	/**
	 * Return months in format 'января', 'февраля', etc
	 *
	 * @return array
	 */
	public static function month_r($month=0) {

		$monthList = array(
			1 => 'января',
				 'февраля',
				 'марта',
				 'апреля',
				 'мая',
				 'июня',
				 'июля',
				 'августа',
				 'сентября',
				 'октября',
				 'ноября',
				 'декабря',
		);

		if(array_key_exists($month, $monthList)) {
			return $monthList[$month];
		}
		else {
			return $monthList;
		}
	}

	/**
	 * Return days
	 *
	 * @param int $month
	 * @return array
	 */
	public static function days($month, $year=null) {

		static $months = array(
			1 => array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30),
				 array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31),
		);

		$month = Cast::unsignint($month);
		$year = Cast::unsignint($year);

		$days = ifsetor($months[$month], array());

		if($days && $year && date('L', mktime(0,0,0,0,0,$year)) && $month==2) { // vysokosny god
			unset($days[29]);
		}

		return $days;
	}

	/**
	 * Return week in format 'воскресенье', 'понедельник', etc or week day
	 *
	 * @param int $wDay
	 * @return mixed
	 */
	public static function week($wDay=0) {

		$weekdays = array(
			1 => 'воскресенье',
				 'понедельник',
				 'вторник',
				 'среда',
				 'четверг',
				 'пятница',
				 'суббота',
		);

		if(array_key_exists($wDay%7, $weekdays)) {
			return $weekdays[$wDay%7];
		}
		else {
			return $weekdays;
		}
	}

	/**
	 * Return week in format 'вс', 'пн', etc or week day
	 *
	 * @param int $wDay
	 * @return mixed
	 */
	public static function weekShort($wDay=0) {

		$weekdays = array(
			1 => 'вс',
				 'пн',
				 'вт',
				 'ср',
				 'чт',
				 'пт',
				 'сб',
		);

		if(array_key_exists($wDay%7, $weekdays)) {
			return $weekdays[$wDay%7];
		}
		else {
			return $weekdays;
		}
	}

	/**
	 * Return week where it uses with preposition "на"
	 * For example: на понедельник, на вторник, на среду
	 *
	 * @param int $wDay
	 * @return string or false
	 */

	public static function weekNA($wDay=0) {

		$weekdays = array(
			1 => 'воскресенье',
				 'понедельник',
				 'вторник',
				 'среду',
				 'четверг',
				 'пятницу',
				 'субботу',
		);

		if(array_key_exists($wDay%7, $weekdays)) {
			return $weekdays[$wDay%7];
		}
		else {
			return $weekdays;
		}
	}

	public static function zodiac($zodiac=0) {

		$result = array(
			1 => array(
				'name' => 'овен',
				'dateRange' => '21.03-20.04',
				'symbol' => 'aries',
				'fromMonth' => 3,
				'toMonth' => 4,
				'fromDay' => 21,
				'toDay' => 20,
			),
			2 => array(
				'name' => 'телец',
				'dateRange' => '21.04-20.05',
				'symbol' => 'taurus',
				'fromMonth' => 4,
				'toMonth' => 5,
				'fromDay' => 21,
				'toDay' => 20,
			),
			3 => array(
				'name' => 'близнецы',
				'dateRange' => '21.05-21.06',
				'symbol' => 'gemini',
				'fromMonth' => 5,
				'toMonth' => 6,
				'fromDay' => 21,
				'toDay' => 21,
			),
			4 => array(
				'name' => 'рак',
				'dateRange' => '22.06-22.07',
				'symbol' => 'cancer',
				'fromMonth' => 6,
				'toMonth' => 7,
				'fromDay' => 22,
				'toDay' => 22,
			),
			5 => array(
				'name' => 'лев',
				'dateRange' => '23.07-23.08',
				'symbol' => 'leo',
				'fromMonth' => 7,
				'toMonth' => 8,
				'fromDay' => 23,
				'toDay' => 23,
			),
			6 => array(
				'name' => 'дева',
				'dateRange' => '24.08-23.09',
				'symbol' => 'virgo',
				'fromMonth' => 8,
				'toMonth' => 9,
				'fromDay' => 24,
				'toDay' => 23,
			),
			7 => array(
				'name' => 'весы',
				'dateRange' => '24.09-23.10',
				'symbol' => 'libra',
				'fromMonth' => 9,
				'toMonth' => 10,
				'fromDay' => 24,
				'toDay' => 23,
			),
			8 => array(
				'name' => 'скорпион',
				'dateRange' => '24.10-22.11',
				'symbol' => 'scorpio',
				'fromMonth' => 10,
				'toMonth' => 11,
				'fromDay' => 24,
				'toDay' => 22,
			),
			9 => array(
				'name' => 'стрелец',
				'dateRange' => '23.11-21.12',
				'symbol' => 'sagittarius',
				'fromMonth' => 11,
				'toMonth' => 12,
				'fromDay' => 23,
				'toDay' => 21,
			),
			10 => array(
				'name' => 'козерог',
				'dateRange' => '22.12-20.01',
				'symbol' => 'capricorn',
				'fromMonth' => 12,
				'toMonth' => 1,
				'fromDay' => 22,
				'toDay' => 20,
			),
			11 => array(
				'name' => 'водолей',
				'dateRange' => '21.01-20.02',
				'symbol' => 'aquarius',
				'fromMonth' => 1,
				'toMonth' => 2,
				'fromDay' => 21,
				'toDay' => 20,
			),
			12 => array(
				'name' => 'рыбы',
				'dateRange' => '21.02-20.03',
				'symbol' => 'pisces',
				'fromMonth' => 2,
				'toMonth' => 3,
				'fromDay' => 21,
				'toDay' => 20,
			),
		);

		if(array_key_exists($zodiac, $result)) {
			return $result[$result];
		}
		else {
			return $result;
		}
	}

	public static function DaySpell($num) {

		$spell = '';

		switch ($num % 10) {
			case 1:
				if($num % 100 != 11) {
					$spell = 'день';
				}
				break;
			case 2:
			case 3:
			case 4:
				if($num % 100 < 11 || $num % 100 > 14) {
					$spell = 'дня';
				}
				break;
			default:
				$spell = 'дней';
		}

		return $spell;
	}
}
