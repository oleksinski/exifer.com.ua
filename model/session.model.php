<?

/* SessionPhp */

class SessionModel extends SessionPhp /* implements SessionInterface*/ {

	/**
	 * @override
	 * @param unknown_type $c
	 */
	public static function &getInstance($c=__CLASS__) {return parent::getInstance($c);}
}