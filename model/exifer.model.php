<?

class ExiferModel {

	protected $__exifer;

	protected $db;
	protected $tb;
	protected $db_tb;

	public function __construct($filepath=null) {
		$this->db = sprintf( '%s', MYSQL_DATABASE);
		$this->tb = sprintf( '%s', 'exif');
		$this->db_tb = sprintf('%s.%s', $this->db, $this->tb);
		$this->__exifer = new Exifer($filepath);
	}

	public function LoadRawExif(array $exif_raw) {
		$this->__exifer->SetExifRawData($exif_raw);
	}

	public function GetRawExif() {
		return $this->__exifer->GetExifRawData();
	}

	public function SetExiferProperty($property, $value) {
		$this->__exifer->SetExifRawProperty($property, $value);
	}

	public function StoreExif($PhotoId) {

		$PhotoId = Cast::unsignint($PhotoId);

		$sql_r = 0;

		$exif_format_arr = $this->__exifer->GetExifFormatData();

		if($PhotoId && $exif_format_arr && $this->__exifer->hasExifInfo()) {

			$__db =& __db();

			$insert_arr = $exif_format_arr;

			$insert_arr['PhotoId'] = $PhotoId;

			$insert_arr = Util::cast_dbtable_values($insert_arr, $this->db_tb);

			$insert_sql = MySQL::prepare_fields($insert_arr);

			$sql = sprintf('INSERT INTO %1$s SET %2$s ON DUPLICATE KEY UPDATE %2$s', $this->db_tb, $insert_sql);

			$sql_r = $__db->u($sql);
		}

		return $sql_r;
	}

	/**
	 * @param scalar|array $PhotoIdList
	 * @return hash_array PhotoId=>exif_data
	 */
	public function GetHumanExif($PhotoIdList) {

		$exif_human = array();

		$exif_raw = self::GetExif($PhotoIdList);

		$__exifer = new Exifer();

		foreach($exif_raw as $p_id=>$exif_arr) {

			$e_arr = array();
			$__exifer->SetExifRawData($exif_arr);
			$exif_format = $__exifer->GetExifFormatData();

			foreach($exif_format as $e_tag=>$e_value) {
				$e_arr[$e_tag] = array(
					'EXIF_VALUE_RAW' => $e_value,
					'EXIF_NAME_RU' => Exifer::GetExifListItem($e_tag, Exifer::EXIF_NAME_RU),
					'EXIF_NAME_EN' => Exifer::GetExifListItem($e_tag, Exifer::EXIF_NAME_EN),
					'EXIF_VALUE_RU' => $__exifer->CalcExifHumanValue($e_tag, $e_value),
					'EXIF_VALUE_EN' => $__exifer->CalcExifHumanValue($e_tag, $e_value),
				);
			}

			$exif_human[$p_id] = $e_arr;
		}

		return $exif_human;
	}

	public function GetOneHumanExif($PhotoIdList) {
		$one = reset(self::GetHumanExif($PhotoIdList));
		return $one ? $one : array();
	}

	/**
	 * @param scalar|array $PhotoIdList
	 * @return deleted rows count
	 */
	public function DeleleExif($PhotoIdList) {

		$sql_r = 0;

		$PhotoIdList = Cast::unsignintarr($PhotoIdList);

		if(!empty($PhotoIdList)) {

			$__db =& __db();

			$sql = sprintf('DELETE FROM %s WHERE %s', $this->db_tb, MySQL::sqlInClause('PhotoId', $PhotoIdList));

			$sql_r = $__db->u($sql);
		}

		return $sql_r;
	}

	public function GetExifer() {
		return $this->__exifer;
	}

	public function GetSqlCreateTable() {

		$e_list = Exifer::GetExifPropertyList();

		$sql = array();

		$sql[] = sprintf('DROP TABLE IF EXISTS `%s`;', $this->tb);

		$rows = array();
		$rows[] = '  `PhotoId` int(11) unsigned NOT NULL';
		foreach($e_list as $e_tag=>$e_data) {
			$rows[] = sprintf('  `%s` %s', $e_tag, Exifer::GetExifListItemValue($e_tag, Exifer::EXIF_TYPE_SQL));
		}
		$rows[] = '  PRIMARY KEY(`PhotoId`)';

		$sql[] = sprintf(
			"CREATE TABLE `%s`(\n%s\n) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;",
			$this->tb, implode(",\n", $rows)
		);

		return implode("\n", $sql);
	}

}
