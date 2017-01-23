<?

class VotePhoto extends Vote {

	const MYSQL_TABLE = 'vote_photo';

	public function __construct($id=0) {
		parent::__construct($id, self::db_table());
		$this->setItemType(self::TYPE_PHOTO);
	}

	/**
	 * @override
	 * @see Vote::loadExtraFields()
	 */
	protected final function loadExtraFields() {
		parent::loadExtraFields();
		$this->setExtraField('url', sprintf('%s#%s', UrlModel::photo($this->getField('item_id')), 'v'));
	}

	/**
	 * @override
	 * @see Object::getItemObject()
	 */
	public function getItemObject($item_id=null) {
		return parent::getItemObject($item_id, 'Photo');
	}

	/**
	 * (non-PHPdoc)
	 * @see lib/Vote::customAddValidateRule()
	 */
	protected function customAddValidateRule($_REQ, $_REQ_RAW) {
		$bool = true;
		/*
		$item_id = ifsetor($_REQ['item_id'], 0);
		$user_id = ifsetor($_REQ['user_id'], 0);
		$comment_collection = new CommentPhotoCollection();
		$userCommentCount = $comment_collection->getCount(array('item_id'=>$item_id, 'user_id'=>$user_id));
		$bool = $userCommentCount>0;
		if(!$bool) {
			$this->pushError('VOTE_PHOTO_NO_COMMENT');
		}
		*/
		return $bool && !$this->isError();
	}

	/**
	 * @override
	 * @see Object::doAfterAdd()
	 */
	protected function doAfterAdd() {
		if($this->exists()) {
			self::recalcItemVoteCount($this->getField('item_id'));
		}
		return $this->exists();
	}

	/**
	 * @override
	 * @see AtomicObject::removeById()
	 */
	public function removeById() {

		$result = false;

		//$user_id = $this->getField('user_id');
		$item_id = $this->getField('item_id');

		$result = parent::removeById();

		self::recalcItemVoteCount($item_id);

		return $result;
	}

	/**
	 *
	 * @param unknown_type $item_id
	 */
	protected static function recalcItemVoteCount($item_id) {
		$photo = new Photo($item_id);
		return $photo->recalcInfo(Photo::RECALC_VOTE_COUNT);
	}

}

class VotePhotoCollection extends VoteCollection {

	public function __construct() {
		parent::__construct(new VotePhoto());
	}

	/**
	 * @override
	 * @see ObjectCollection::getItemObjectCollection()
	 */
	public function getItemObjectCollection($itemIds=array()) {
		return parent::getItemObjectCollection($itemIds, 'PhotoCollection');
	}
}
