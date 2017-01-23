<?

abstract class Comment extends Object {

	const STATUS_OKE = 0;
	const STATUS_CLR = 1;
	const STATUS_DEL = 2;

	const TYPE_PHOTO = 'photo';
	const TYPE_ARTICLE = 'article';

	const TEXT_UNLIMITED_LENGTH = -1;

	const COORD_PATTERN = '/(\[\d+,\d+,\d+,\d+\])/s';

	/**
	 *
	 * @var unknown_type
	 */
	protected $maxLength = 1000;
	/**
	 *
	 * @var unknown_type
	 */
	protected $itemType;

	/**
	 * image crop sample parse/display support
	 * @var unknown_type
	 */
	protected $imageCoordEnabled = false;

	/**
	 * Time period while comment can be edited by it's author
	 * @var unknown_type
	 */
	protected $editableTime = 600; // secs, 0 disables editing

	/**
	 *
	 * @var unknown_type
	 */
	protected $minPostTimeDelay = 5; // secs, 0 disables checkup

	/**
	 *
	 * @var unknown_type
	 */
	protected $maxCommentsPerDay = 0; // 0 disables checkup

	/**
	 *
	 * @var unknown_type
	 */
	protected $maxAnswerDepth = 0; // 0 disables checkup

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {
		$commentText = $this->getField('text');
		if($this->isImageCoordEnabled()) {
			$coords = Regexp::match_all(self::COORD_PATTERN, $commentText);
			foreach($coords as $coord) {
				$commentText = _str_replace($coord, '', $commentText);
			}
			$this->setExtraField('coords',$coords);
		}
		$this->setExtraField('raw_text_cordless', $commentText);
		$this->setExtraField('text', SafeHtmlModel::output($commentText));
		$this->setExtraField('text_prev', SafeHtmlModel::output($this->getField('text_prev')));
		$this->setExtraField('anchor', sprintf('c%u', $this->getId()));
		$this->setExtraField('url', '#');
		$this->setExtraField('item_url', '#');
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {

		$result = array();

		$result['item_id'] = (int)ifsetor($_REQ['item_id'], 0);
		$result['text'] = SafeHtmlModel::input(ifsetor($_REQ['text'], null));

		//$result['root_id'] = (int)ifsetor($_REQ['root_id'], 0);
		//$result['parent_id'] = (int)ifsetor($_REQ['parent_id'], 0);
		//$result['depth'] = (int)ifsetor($_REQ['depth'], 0);

		$result['user_id'] = User::getOnlineUserId();
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

		$bool = true;

		// check user_id
		if($bool) {
			$_REQ['user_id'] = ifsetor($_REQ['user_id'], 0);
			$bool = Cast::bool($_REQ['user_id']);

			if(!$bool) {
				$this->pushError('COMMENT_NO_UID');
			}
		}

		if($bool) {
			$bool = $this->isAddable();
			if(!$bool) {
				$this->pushError('COMMENT_NO_ADD_PERMISSION');
			}
		}

		// check time between recent comment (antibot protection)
		if($bool && $this->minPostTimeDelay>0) {
			$collectionClass = $this->getCollectionClass();
			$comment_collection = new $collectionClass();
			$comment_cnt = $comment_collection->getCount(
				array('item_id'=>$_REQ['item_id'], 'user_id'=>$_REQ['user_id']),
				array(sprintf('add_tstamp>%d', time()-$this->minPostTimeDelay))
			);
			$bool = $comment_cnt==0;
			if(!$bool) {
				$this->pushError('COMMENT_POST_DELAY', array($this->minPostTimeDelay));
			}
		}

		// check comments count for account per day
		if($bool && $this->maxCommentsPerDay>0) {
			$collectionClass = $this->getCollectionClass();
			$comment_collection = new $collectionClass();
			$comment_cnt = $comment_collection->getCount(
				array('user_id'=>$_REQ['user_id']),
				array(sprintf('add_tstamp>%d', DateConst::mk_time(DateConst::getDay(), DateConst::getMonth(), DateConst::getYear())))
			);
			$bool = $comment_cnt<=$this->maxCommentsPerDay;
			if(!$bool) {
				$this->pushError('COMMENT_LIMIT_PER_DAY', array($this->maxCommentsPerDay, date('d.m.Y', time()+24*3600)));
			}
		}

		// common add/change validation
		if($bool) {
			$bool = $this->validateAddChange($_REQ, $_REQ_RAW);
		}

		return $bool && !$this->isError();
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectChangeFields($_REQ) {

		$result = array();

		$result['text'] = SafeHtmlModel::input(ifsetor($_REQ['text'], null));
		$result['text_prev'] = $this->getField('text');
		$result['change_uid'] = User::getOnlineUserId();
		$result['change_tstamp'] = time();
		$result['change_ip'] = Network::clientIp();
		$result['change_fwrd'] = Network::clientFwrd();

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateChange()
	 */
	protected function validateChange($_REQ, $_REQ_RAW) {

		$bool = $this->exists();

		if(!$bool) {
			$this->pushError('COMMENT_ERROR');
		}

		if($bool) {
			$bool = $this->isEditable();
			if(!$bool) {
				$this->pushError('COMMENT_NO_EDIT_PERMISSION');
			}
		}

		// common add/change validation
		if($bool) {
			$bool = $this->validateAddChange($_REQ, $_REQ_RAW);
		}

		return $bool && !$this->isError();
	}

	/**
	 *
	 * @param unknown_type $_REQ
	 * @param unknown_type $_REQ_RAW
	 */
	protected function validateAddChange($_REQ, $_REQ_RAW) {

		$bool = true;

		// check item_id
		$itemObject = null;
		if($bool) {
			$itemId = $this->exists() ? $this->getField('item_id') : ifsetor($_REQ['item_id'], 0);
			$itemObject = $this->getItemObject($itemId);
			$bool = $itemObject->exists();
			if(!$bool) {
				$this->pushError('COMMENT_NO_ITEM');
			}
		}

		// check text
		$visibleSymbolText = null;
		if($bool) {
			$_REQ['text'] = ifsetor($_REQ['text'], null);
			if($this->maxLength!=self::TEXT_UNLIMITED_LENGTH && $this->maxLength>0) {
				$_REQ['text'] = Text::cutStr($_REQ['text'], $this->maxLength);
			}
			// check not empty
			$visibleSymbolText = $_REQ['text'];
			$visibleSymbolText = Text::removeExtraNL($visibleSymbolText, 0, 1); // remove all NL
			$visibleSymbolText = _preg_replace("/(\s){2,}/", " ", $visibleSymbolText); // replace double spaces
			$bool = ($visibleSymbolText!=='');

			if(!$bool) {
				$this->pushError('COMMENT_NO_TEXT');
			}
		}

		if($bool && $this->isImageCoordEnabled()) {
			$coords = Regexp::match_all(self::COORD_PATTERN, $visibleSymbolText);
			if($coords) {
				$coordError = array();
				foreach($coords as $i=>$coord) {
					$coord = JsonModel::decode($coord);
					$coordValidateResult = $this->validateCoord($itemObject, $coord);
					if(!$coordValidateResult) {
						$coordError[] = $i+1;
					}
				}
				if($coordError) {
					if(count($coords)>1) {
						$this->pushError('COMMENT_COORD_ERROR_NUMBER', array(implode(',', $coordError)));
					}
					else {
						$this->pushError('COMMENT_COORD_ERROR');
					}
				}
				// check for empty text without coords
				foreach($coords as $i=>$coord) {
					$visibleSymbolText = _str_replace($coord, '', $visibleSymbolText);
				}
				$visibleSymbolText = _trim($visibleSymbolText);
				if($visibleSymbolText==='') {
					$this->pushError('COMMENT_COORD_NO_TEXT');
				}
				$bool = !$this->isError();
			}
		}

		// check for duplicate post
		if($bool) {
			$sql = sprintf(
				'SELECT id FROM %s WHERE item_id=%u AND user_id=%u AND text=%s AND add_tstamp>%u',
				$this->db_table, $_REQ['item_id'], $_REQ['user_id'], MySQL::str($_REQ['text']), time()-5*60
			);
			$sql_r = $this->db()->row($sql);
			$bool = !ifsetor($sql_r['id'], false);

			if(!$bool) {
				$this->pushError('COMMENT_TEXT_DUPLICATE');
			}
		}

		return $bool && !$this->isError();
	}

	/**
	 *
	 */
	public function clear() {

		$bool = true;

		if($bool) {
			$bool = $this->isClearable();
			if(!$bool) {
				$this->pushError('COMMENT_NO_CLEAR_PERMISSION');
			}
		}

		if($bool) {
			$this->setField('text_prev', $this->getField('text'));
			$this->setField('text', '');
			$this->setField('change_uid', User::getOnlineUserId());
			$this->setField('change_tstamp', time());
			$this->setField('change_ip', Network::clientIp());
			$this->setField('change_fwrd', Network::clientFwrd());
			$this->save();
		}

		return $bool && !$this->isError();
	}

	/**
	 *
	 * @param unknown_type $maxLength
	 */
	public function setMaxLength($maxLength) {
		$this->maxLength = Cast::unsignint($maxLength);
	}

	/**
	 * @return int
	 */
	public function getMaxLength() {
		return (int)$this->maxLength;
	}

	/**
	 *
	 * @param unknown_type $item_type
	 */
	public function setItemType($item_type) {
		$this->itemType = $item_type;
	}

	/**
	 *
	 */
	public function getItemType() {
		return $this->itemType;
	}

	/**
	 *
	 * @param unknown_type $coord
	 */
	protected function validateCoord(AtomicObject $itemObject, array $coords) {
		return true;
	}

	/**
	 *
	 * @param unknown_type $x
	 * @param unknown_type $y
	 * @param unknown_type $x2
	 * @param unknown_type $y2
	 * @param unknown_type $w
	 * @param unknown_type $h
	 */
	final protected function validateCoordRule($x,$y,$x2,$y2,$w,$h) {
		$result = true;
		$result = $result && $x>=0 && $x<$w;
		$result = $result && $y>=0 && $y<$h;
		$result = $result && $x2>0 && $x2<=$w;
		$result = $result && $y2>0 && $y2<=$h;
		return $result;
	}

	/**
	 *
	 */
	public function isImageCoordEnabled() {
		return $this->imageCoordEnabled==true;
	}

	/**
	 *
	 * @param string $coord
	 */
	public function getCoordUrl($coord) {
		return sprintf('%s#c%s', $this->getExtraField('item_url'), (string)$coord);
	}

	public function isAddable() {
		return User::isLoginned();
	}

	public function isEditable() {
		return false;
		$bool = true
			&& $this->exists()
			&& User::isLoginned()
			&& (
				User::isOnlineModerator()
				||
				$this->getField('user_id')==User::getOnlineUserId()
				&& (
					((int)$this->getField('change_uid'))==0
					||
					$this->getField('change_uid')==$this->getField('user_id')
				)
				&&
				$this->editableTime>0
				&& (
					$this->getField('add_tstamp')>(time()-$this->editableTime)
					||
					$this->getField('change_tstamp')>(time()-$this->editableTime)
				)
			);
		return $bool;
	}

	public function isAnswerable() {
		return $this->exists() && $this->getField('depth')<$this->maxAnswerDepth;
	}

	public function isClearable() {
		return $this->exists() && User::isOnlineModerator() && $this->getField('text')!=='';
	}

	public function isRemovable() {
		return $this->exists() && User::isOnlineModerator();
	}

}

abstract class CommentCollection extends ObjectCollection {

	protected $itemType;

	public function __construct($classname='Comment') {
		$object = new $classname();
		parent::__construct($object);
		$this->itemType = $object->getItemType();
	}

	/**
	 *
	 */
	public function getItemType() {
		return $this->itemType;
	}

	/**
	 *
	 */
	public function getCountTotal() {
		return $this->length();
	}

	/**
	 *
	 */
	public function getCountRoot() {
		$result = 0;
		$value_arr = $this->getFieldValues('root_id');
		foreach($value_arr as $id->$value) {
			if($value==0) {
				$result++;
			}
		}
		return $result;
	}

	/**
	 *
	 */
	public function getCountChild() {
		$result = 0;
		$value_arr = $this->getFieldValues('root_id');
		foreach($value_arr as $id->$value) {
			if($value!=0) {
				$result++;
			}
		}
		return $result;
	}
}

// ---

abstract class CommentRate extends Object {

	protected $db_table = 'exifer.comment_rate';

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {
		//@TODO
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {

		$result = array();

		//@TODO

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateAdd()
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {

		return !$this->isError();
	}
}

abstract class CommentRateCollection extends ObjectCollection {

	public function __construct($classname='CommentRate') {
		$object = new $classname();
		parent::__construct($object);
	}
}

// ---

abstract class CommentKarma extends Object {

	protected $db_table = 'exifer.comment_karma';

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {
		//@TODO
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {

		$result = array();

		//@TODO

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateAdd()
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {

		return !$this->isError();
	}
}

abstract class CommentKarmaCollection extends ObjectCollection {

	public function __construct($classname='CommentKarma') {
		$object = new $classname();
		parent::__construct($object);
	}
}

// ---

abstract class CommentSubscribe extends Object {

	protected $db_table = 'exifer.comment_subscribe';

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {
		//@TODO
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {

		$result = array();

		//@TODO

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateAdd()
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {

		return !$this->isError();
	}
}

abstract class CommentSubscribeCollection extends ObjectCollection {

	public function __construct($classname='CommentSubscribe') {
		$object = new $classname();
		parent::__construct($object);
	}
}
