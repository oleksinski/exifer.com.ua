<?

class CommentPhoto extends Comment {

	const MYSQL_TABLE = 'comment_photo';

	public function __construct($id=0) {
		parent::__construct($id, self::db_table());
		$this->setItemType(self::TYPE_PHOTO);
		$this->imageCoordEnabled = true;
	}

	/**
	 * @override
	 * @see Comment::loadExtraFields()
	 */
	protected final function loadExtraFields() {
		parent::loadExtraFields();
		$this->setCommentUrl();
	}

	/**
	 *
	 */
	public function setCommentUrl() {
		$item_url = UrlModel::photo($this->getField('item_id'), ($this->isItemObjectExists() ? $this->getItemObject() : null));
		$this->setExtraField('item_url', _str_replace(UrlModel::homepage(), '', $item_url));
		$this->setExtraField('url', sprintf('%s#%s', $item_url, $this->getExtraField('anchor')));
	}

	/**
	 * @override
	 * @see Object::getItemObject()
	 */
	public function getItemObject($item_id=null) {
		return parent::getItemObject($item_id, 'Photo');
	}

	/**
	 * @override
	 * @see Object::doAfterAdd()
	 */
	protected function doAfterAdd() {

		if($this->exists()) {
			self::recalcUserCommentCount($this->getField('user_id'));
			self::recalcItemCommentCount($this->getField('item_id'));

			// send email notification
			MessageModel::comment_photo($this->getId());
		}

		return $this->exists();
	}

	/**
	 * @override
	 * @see AtomicObject::removeById()
	 */
	public function removeById() {

		$result = false;

		$user_id = $this->getField('user_id');
		$item_id = $this->getField('item_id');

		$result = parent::removeById();

		if($result) {
			self::recalcUserCommentCount($user_id);
			self::recalcItemCommentCount($item_id);
		}

		return $result;
	}

	/**
	 *
	 * @param unknown_type $user_id
	 * @return bool
	 */
	protected static function recalcUserCommentCount($user_id) {
		$user = new User($user_id);
		return $user->recalcInfo(User::RECALC_COMMENT_COUNT);
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @return bool
	 */
	protected static function recalcItemCommentCount($item_id) {
		$photo = new Photo($item_id);
		return $photo->recalcInfo(Photo::RECALC_COMMENT_COUNT);
	}

	/**
	 * @override
	 * @see Comment::validateCoord()
	 */
	protected function validateCoord(Photo $photo, array $coords) {
		// read init value fromparent class
		$result = parent::validateCoord($photo, $coords);

		if($result) {
			$photo->extendThumb(ThumbModel::THUMBNAIL_ORIGINAL);
			$width = $photo->getThumbWidth(ThumbModel::THUMBNAIL_ORIGINAL);
			$height = $photo->getThumbHeight(ThumbModel::THUMBNAIL_ORIGINAL);
			$result = $this->validateCoordRule($coords[0],$coords[1],$coords[2],$coords[3],$width,$height);
		}

		return $result;
	}

}

class CommentPhotoCollection extends CommentCollection {

	public function __construct() {
		parent::__construct(new CommentPhoto());
	}

	/**
	 * @override
	 * @see ObjectCollection::getItemObjectCollection()
	 */
	public function getItemObjectCollection($itemIds=array()) {
		$collection = parent::getItemObjectCollection($itemIds, 'PhotoCollection');
		foreach($this as $comment_id=>$comment) {
			$comment->setCommentUrl();
		}
		return $collection;
	}
}

// ---

class CommentPhotoRate extends CommentRate {

	const MYSQL_TABLE = 'comment_photo_rate';

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see CommentRate::loadExtraFields()
	 */
	protected final function loadExtraFields() {
		parent::loadExtraFields();
		//@TODO
	}

	/**
	 * @override
	 * @see Object::doAfterAdd()
	 */
	protected function doAfterAdd() {

		$id = $this->getId();

		if($id) {
			//@TODO
		}

		return $id;
	}
}

class CommentPhotoRateCollection extends CommentRateCollection {

	public function __construct() {
		parent::__construct(new CommentPhotoRate());
	}
}

// ---

class CommentPhotoKarma extends CommentKarma {

	const MYSQL_TABLE = 'comment_photo_karma';

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see CommentKarma::loadExtraFields()
	 */
	protected final function loadExtraFields() {
		parent::loadExtraFields();
		//@TODO
	}

	/**
	 * @override
	 * @see Object::doAfterAdd()
	 */
	protected function doAfterAdd() {

		$id = $this->getId();

		if($id) {
			//@TODO
		}

		return $id;
	}
}

class CommentPhotoKarmaCollection extends CommentKarmaCollection {

	public function __construct() {
		parent::__construct(new CommentPhotoKarma());
	}
}

// ---

class CommentPhotoSubscribe extends CommentSubscribe {

	const MYSQL_TABLE = 'comment_photo_subscribe';

	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @override
	 * @see CommentSubscribe::loadExtraFields()
	 */
	protected final function loadExtraFields() {
		parent::loadExtraFields();
		//@TODO
	}

	/**
	 * @override
	 * @see Object::doAfterAdd()
	 */
	protected function doAfterAdd() {

		$id = $this->getId();

		if($id) {
			//@TODO
		}

		return $id;
	}
}

class CommentPhotoSubscribeCollection extends CommentSubscribeCollection {

	public function __construct() {
		parent::__construct(new CommentPhotoSubscribe());
	}
}

