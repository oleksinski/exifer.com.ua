<?

class Photo extends Object {

	const MYSQL_TABLE = 'photo';

	const STATUS_LOCK = 0;
	const STATUS_OKE = 1;

	const MODERATED_OFF = 0;
	const MODERATED_ON = 1;

	const BITMASK_NONE = 0;
	const BITMASK_HIDE_EXIF = 1;
	const BITMASK_ADULT = 2;
	const BITMASK_RECEIVE_COMMENTS = 4;

	const RGB_COLORS = 15;

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {
		$this->setExtraField('name', SafeHtmlModel::output($this->getField('name')));
		$this->setExtraField('orig_name', SafeHtmlModel::output($this->getField('orig_name')));
		$this->setExtraField('description', SafeHtmlModel::output($this->getField('description')));
		//$this->setExtraField('hide_exif', $this->isBitmaskSet(self::BITMASK_HIDE_EXIF));
		//$this->setExtraField('is_adult', $this->isBitmaskSet(self::BITMASK_ADULT));
		//$this->setExtraField('receive_comments', $this->isBitmaskSet(self::BITMASK_RECEIVE_COMMENTS));
		$this->setExtraField('thumb', ThumbModel::GetOnePreparedBlind($this->getId(), $this->getField('add_tstamp')));
		$this->setExtraField('genre', GenreModel::GetOneGenreListByGenreId($this->getField('genre_id')));
		//$this->setExtraField('exif', null);
	}

	/**
	 *
	 * @param unknown_type $_REQ
	 * @param unknown_type $_REQ_RAW
	 * @return bool
	 */
	protected function validateAddChange($_REQ, $_REQ_RAW) {

		$__validator = new ValidatorModel();

		$_REQ['name'] = ifsetor($_REQ['name'], null);
		if(!$__validator->photo_name($_REQ['name'])) {
			$this->pushError('PHOTO_NAME');
		}

		$_REQ['description'] = ifsetor($_REQ['description'], null);
		if(!$__validator->photo_desc($_REQ['description'])) {
			$this->pushError('PHOTO_DESCRIPTION');
		}

		$_REQ['genre_id'] = ifsetor($_REQ['genre_id'], null);
		$genre = GenreModel::GetOneGenreListByGenreId($_REQ['genre_id']);
		if(!$genre) {
			$this->pushError('PHOTO_GENRE');
		}

		return !$this->isError();
	}

	private function getAddChangeRgbColor($_REQ) {
		$listColorRgb = self::getColorsRGB();
		$rgb = ifsetor($_REQ['rgb'], $this->getField('rgb'));
		return insetor($rgb, $listColorRgb, end($listColorRgb));
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {

		$result = array();

		$result['name'] = SafeHtmlModel::input(ifsetor($_REQ['name'], null));

		$result['description'] = SafeHtmlModel::input(ifsetor($_REQ['description'], null));

		$result['user_id'] = User::getOnlineUserId();

		$result['genre_id'] = ifsetor($_REQ['genre'], null);

		$result['orig_size'] = ifsetor($_REQ['orig_size'], 0);
		$result['orig_width'] = ifsetor($_REQ['orig_width'], 0);
		$result['orig_height'] = ifsetor($_REQ['orig_height'], 0);
		$result['orig_mimetype'] = ifsetor($_REQ['orig_mimetype'], null);
		$result['orig_name'] = ifsetor($_REQ['orig_name'], null);

		//if($result['name']=='') {
		//	$result['name'] = $result['orig_name'];
		//}

		$result['exif'] = ifsetor($_REQ['exif'], null);

		$result['bitmask'] = Photo::BITMASK_NONE;
		//if(isset($_REQ['']) && $_REQ['']) {
		//	Cast::setbit(&$result['bitmask'], Photo::BITMASK_ADULT);
		//}
		//if(isset($_REQ['']) && $_REQ['']) {
		//	Cast::setbit(&$result['bitmask'], Photo::BITMASK_HIDE_EXIF);
		//}
		//if(isset($_REQ['']) && $_REQ['']) {
		//	Cast::setbit(&$result['bitmask'], Photo::BITMASK_RECEIVE_COMMENTS);
		//}

		$result['rgb'] = $this->getAddChangeRgbColor($_REQ);

		$result['status'] = ifsetor($_REQ['status'], self::STATUS_OKE);
		$result['moderated'] = ifsetor($_REQ['moderated'], self::MODERATED_ON);

		$result['add_tstamp'] = time();
		$result['add_ip'] = Network::clientIp();
		$result['add_fwrd'] = Network::clientFwrd();

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateAdd()
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {

		return $this->validateAddChange($_REQ, $_REQ_RAW);
	}

	/**
	 * @override
	 * @see Object::collectChangeFields()
	 */
	protected function collectChangeFields($_REQ) {

		$result = array();

		$result['name'] = SafeHtmlModel::input(ifsetor($_REQ['name'], $this->getField('name')));

		$result['description'] = SafeHtmlModel::input(ifsetor($_REQ['description'], $this->getField('description')));

		$result['rgb'] = $this->getAddChangeRgbColor($_REQ);

		$result['genre_id'] = ifsetor($_REQ['genre'], $this->getField('genre_id'));

		$result['update_tstamp'] = time();
		$result['update_ip'] = Network::clientIp();
		$result['update_fwrd'] = Network::clientFwrd();

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateChange()
	 */
	protected function validateChange($_REQ, $_REQ_RAW) {
		return $this->validateAddChange($_REQ, $_REQ_RAW);
	}

	/**
	 *
	 * @param unknown_type $thumb_format
	 */
	public function extendThumb($thumb_format=null) {
		return $this->setExtraField('thumb', ThumbModel::GetOnePrepared($this->getId(), $thumb_format));
	}

	/**
	 * @param void
	 */
	public function extendExif() {
		return $this->setExtraField('exif', $this->getExifInfo());
	}

	const RECALC_COMMENT_COUNT = 1;
	const RECALC_VOTE_COUNT = 2;
	const RECALC_VIEW_COUNT = 4;
	/**
	 *
	 * @param unknown_type $recalcbit
	 * @return bool
	 */
	public function recalcInfo($recalcbit=7) {

		$result = false;

		if($this->exists()) {

			if($recalcbit&self::RECALC_COMMENT_COUNT) {
				$comment_photo_collection = new CommentPhotoCollection();
				$this->setField('comments', $comment_photo_collection->getCount(array('item_id'=>$this->getId())));
			}
			if($recalcbit&self::RECALC_VOTE_COUNT) {
				$vote_collection = new VotePhotoCollection();
				$vote_collection->getCollectionByItemId($this->getId());
				$this->setField('votes_pros', $vote_collection->getCountPros());
				$this->setField('votes_cons', $vote_collection->getCountCons());
				$this->setField('votes_zero', $vote_collection->getCountZero());
				//$this->setField('votes', $vote_collection->getCountTotal());
				$this->setField('votes', 'votes_pros+votes_cons+votes_zero', true);
				$this->setField('votes_value', $vote_collection->getValueSum());
			}
			if($recalcbit&self::RECALC_VIEW_COUNT) {
				$this->setField('views', 'views_guest+views_user', true);
			}

			$result = $this->update();
		}

		return $result;
	}

	/**
	 * @override
	 * @see AtomicObject::removeById()
	 */
	public function removeById() {

		$result = false;

		if(!$this->exists()) return $result;

		$photo_id = $this->getId();
		$user_id = $this->getField('user_id');

		// remove comments
		$comment_collection = new CommentPhotoCollection();
		$comment_collection->removeByItemId($photo_id);

		// remove votes
		$vote_collection = new VotePhotoCollection();
		$vote_collection->removeByItemId($photo_id);

		// remove photo thumbnails
		ThumbModel::Remove($photo_id);

		// remove photo exif db record
		//$exifer = new ExiferModel();
		//$exifer->DeleleExif($photo_id);

		// remove photo user view data
		$photo_user_view_collection = new PhotoUserViewCollection();
		$photo_user_view_collection->removeByItemId($photo_id);

		// photo deleted
		$photo_deleted = new PhotoDeleted();
		foreach($this->getFields() as $field=>$value) {
			$photo_deleted->setField($field, $value);
		}
		$photo_deleted->save();

		$result = parent::removeById();

		$user = new User($user_id);
		$user->recalcInfo();

		return $result;
	}

	/**
	 *
	 * @param unknown_type $bit
	 */
	public function setBitmask($bit) {
		if(self::bitmaskExists($bit)) {
			$this->setField('bitmask', Cast::setbit($this->getField('bitmask'), $bit));
		}
	}

	/**
	 *
	 * @param unknown_type $bit
	 */
	public function unsetBitmask($bit) {
		if(self::bitmaskExists($bit)) {
			$this->setField('bitmask', Cast::unsetbit($this->getField('bitmask'), $bit));
		}
	}

	/**
	 *
	 * @param unknown_type $what
	 * @return bool
	 */
	public static function bitmaskExists($what) {
		return insetcheck($what, ReflectionModel::getClassConstValueList(__CLASS__, 'BITMASK_'));
	}

	/**
	 *
	 * @param unknown_type $bit
	 * @return bool
	 */
	public function isBitmaskSet($bit) {
		return $bit && self::bitmaskExists($bit) && ($bit&$this->getField('bitmask'))==$bit;
	}

	/**
	 * @return array
	 */
	public function getExifInfo($brief=false) {

		$result = array();

		$exif = @unserialize($this->getField('exif'));

		if($exif) {

			$exif_tag_list = array(
				'Make', 'Model', 'ExposureTime', 'FNumber',
				'ISOSpeedRatings', 'FocalLength', 'FocalLengthIn35mmFilm',
				'ExposureMode', 'ExposureProgram', 'Flash', 'WhiteBalance',
				'DateTimeOriginal', 'Orientation',
				'Compression', 'LightSource', 'FileSize', 'SceneCaptureType',
				'Contrast', 'Sharpness', 'Saturation',
				'MeteringMode', 'SceneCaptureType', 'Software', 'FileName',
			);

			if($brief) {
				$exif_tag_list = array(
					'Model', 'ExposureTime', 'FNumber', 'ISOSpeedRatings', 'FocalLength',
				);
			}

			$__exifer = new Exifer();
			$__exifer->SetExifRawData($exif);
			$exif_human = $__exifer->GetExifHumanData();

			foreach($exif_tag_list as $e_tag) {
				if(isset($exif_human[$e_tag])) {
					$e_name = Exifer::GetExifListItemValue($e_tag, Exifer::EXIF_NAME_RU);
					$e_value = SafeHtmlModel::output($exif_human[$e_tag]);
					$result[$e_name] = $e_value;
				}
			}
		}

		return $result;
	}

	/**
	 *
	 * @param unknown_type $what
	 * @return bool
	 */
	public static function statusExists($what) {
		return $what===self::STATUS_LOCK || $what===self::STATUS_OKE;
	}

	/**
	 *
	 * @param unknown_type $qty
	 * @param unknown_type $format
	 * @return array
	 */
	public static function getColorsRGB($qty=self::RGB_COLORS, $format=true) {

		$rgb_color = array();

		$qty = Cast::unsignint($qty);

		if($qty<=0) {
			$qty = 10;
		}

		$bg_color = array();
		$max_rgb = 255;
		$min_rgb = 0;
		$delta = ceil(255/($qty-0.1));

		for($i=0; $i<$qty; $i++) {

			$value = $max_rgb - $i*$delta;

			if($value<$delta || $value<0) {
				$value = $min_rgb;
			}

			if($format) {
				$rgb_color[] = sprintf('%1$u,%1$u,%1$u', $value);
			}
			else {
				$rgb_color[] = array('R'=>$value,'G'=>$value,'B'=>$value);
			}
		}

		return $rgb_color;
	}

	/**
	 *
	 * @param unknown_type $thumbFormat
	 * @return int
	 */
	public function getThumbWidth($thumbFormat=ThumbModel::THUMBNAIL_ORIGINAL) {
		$result = 0;
		$thumbField = $this->getExtraField('thumb');
		$thumb = ifsetor($thumbField[$thumbFormat], array());
		$result = ifsetor($thumb['width'], 0);
		return $result;
	}

	/**
	 *
	 * @param unknown_type $thumbFormat
	 * @return int
	 */
	public function getThumbHeight($thumbFormat=ThumbModel::THUMBNAIL_ORIGINAL) {
		$result = 0;
		$thumbField = $this->getExtraField('thumb');
		$thumb = ifsetor($thumbField[$thumbFormat], array());
		$result = ifsetor($thumb['height'], 0);
		return $result;
	}

	public function isEditable() {
		return true
			&& $this->exists()
			&& (
				$this->getField('user_id')==User::getOnlineUserId() && $this->getField('status')==Photo::STATUS_OKE
				|| User::isOnlineModerator()
			);
	}

	public function isRemovable() {
		return $this->isEditable();
	}
}

class PhotoCollection extends ObjectCollection {

	public function __construct($classname='Photo') {
		$object = new $classname();
		parent::__construct($object);
	}

	/**
	 *
	 * @param unknown_type $thumb_format
	 */
	public function extendThumbCollection($thumb_format=null) {
		return $this->extendCollectionExtraField($this->id_field, 'thumb', 'getThumbObjectCollection', array($thumb_format));
	}

	protected function getThumbObjectCollection() {
		return call_user_func_array(array('ThumbModel','GetPrepared'), func_get_args());
	}

}
