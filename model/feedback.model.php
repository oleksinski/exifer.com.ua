<?

class FeedbackModel {

	public static function db_feedback() {
		return sprintf('%s.%s', MYSQL_DATABASE, 'feedback');
	}

	/**
	 * @param mixed $feedback_id
	 */
	public static function Select($feedback_id) {

		$result = array();

		$feedback_id = Cast::unsignintarr($feedback_id);

		$__db =& __db();

		$sql = sprintf(
			'SELECT * FROM %s WHERE %s'
			, self::db_feedback(), MySQL::sqlInClause('id', $feedback_id)
		);

		$sql_r = $__db->q($sql);

		while($row = $sql_r->next()) {
			$result[$row['id']] = $row;
		}

		return $result;
	}

	/**
	 * @param mixed $feedback_id
	 */
	public static function SelectOne($feedback_id) {
		$one = reset(self::Select($feedback_id));
		return $one ? $one : array();
	}

	public static function Insert($insert_arr) {

		$feedback_id = 0;

		if(!empty($insert_arr)) {

			$__db =& __db();

			$insert_arr += array(
				'add_tstamp' => time(),
				'add_ip' => Network::clientIp(),
				'add_fwrd' => Network::clientFwrd(),
			);

			$insert_sql = MySQL::prepare_fields($insert_arr);

			$sql = sprintf('INSERT IGNORE INTO %s SET %s', self::db_feedback(), $insert_sql);

			$affected = $__db->u($sql);

			if($affected) {
				$feedback_id = $__db->last_insert_id();
			}
		}

		return $feedback_id;
	}

	public static function Remove($feedback_id) {

		$affected = 0;

		$feedback_id = Cast::unsignintarr($feedback_id);

		if(!empty($feedback_id)) {

			$__db =& __db();

			$sql = sprintf(
				'DELETE FROM %s WHERE %s'
				, self::db_feedback(), MySQL::sqlInClause('id', $feedback_id)
			);

			$affected = $__db->u($sql);
		}

		return $affected;
	}

	public static function RemoveUser($user_id) {

		$affected = 0;

		$user_id = Cast::unsignintarr($user_id);

		if(!empty($user_id)) {

			$__db =& __db();

			$sql = sprintf(
				'DELETE FROM %s WHERE %s'
				, self::db_feedback(), MySQL::sqlInClause('user_id', $user_id)
			);

			$affected = $__db->u($sql);
		}

		return $affected;
	}

}
