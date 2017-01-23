<?

abstract class Vote extends Object {

	const VOTE_CONS = -1;
	const VOTE_PROS = 1;
	const VOTE_ZERO = 0;

	const TYPE_PHOTO = 'photo';
	const TYPE_ARTICLE = 'article';

	/**
	 *
	 * @var unknown_type
	 */
	protected $db_table = 'exifer.vote';
	/**
	 *
	 * @var unknown_type
	 */
	protected $itemType;
	/**
	 *
	 * @var unknown_type
	 */
	protected $itemId;
	/**
	 *
	 * @var unknown_type
	 */
	protected $canVote;

	/**
	 *
	 * @var unknown_type
	 */
	protected $maxVotesPerDay = 0; // 0 disables checkup

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {
		$this->setExtraField('url', '#');
		$this->setExtraField('value', self::getSignedValue($this->getField('value')));
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {

		$result = array();

		$result['item_id'] = ifsetor($_REQ['item_id'], 0);
		$result['user_id'] = User::getOnlineUserId();
		$result['type'] = ifsetor($_REQ['vote_type'], null);
		switch($result['type']) {
			case self::VOTE_CONS:
				$result['value'] = Cast::float(-1);
				break;
			case self::VOTE_PROS:
				$result['value'] = Cast::float(1);
				break;
			default:
				$result['value'] = Cast::float(0);
				break;
		}
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

		if($bool) {
			$_REQ['item_id'] = ifsetor($_REQ['item_id'], 0);
			$bool = $this->getItemObject($_REQ['item_id'])->exists();
			if(!$bool) {
				$this->pushError('VOTE_NO_ITEM');
			}
		}

		if($bool) {
			$_REQ['user_id'] = ifsetor($_REQ['user_id'], 0);
			$bool = Cast::bool($_REQ['user_id']);
			if(!$bool) {
				$this->pushError('VOTE_NO_UID');
			}
		}

		if($bool) {
			$_REQ['type'] = Cast::int(ifsetor($_REQ['type'], null));
			$bool = insetcheck($_REQ['type'], array(self::VOTE_CONS, self::VOTE_PROS, self::VOTE_ZERO));
			if(!$bool) {
				$this->pushError('VOTE_NO_TYPE');
			}
		}

		if($bool) {
			$bool = !$this->isUserItem($_REQ['item_id'], $_REQ['user_id']);
			if(!$bool) {
				$this->pushError('VOTE_SELF');
			}
		}

		if($bool) {
			$bool = !$this->isVoted($_REQ['item_id'], $_REQ['user_id']);
			if(!$bool) {
				$this->pushError('VOTE_DUPLICATE');
			}
		}

		// check vote count for account per day
		if($bool && $this->maxVotesPerDay>0) {
			$collectionClass = $this->getCollectionClass();
			$comment_collection = new $collectionClass();
			$votes_cnt = $comment_collection->getCount(
				array('user_id'=>$_REQ['user_id']),
				array(sprintf('add_tstamp>%d', DateConst::mk_time(DateConst::getDay(), DateConst::getMonth(), DateConst::getYear())))
			);
			$bool = $votes_cnt<$this->maxVotesPerDay;
			if(!$bool) {
				$this->pushError('VOTE_LIMIT_PER_DAY', array($this->maxVotesPerDay, date('d.m.Y', time()+24*3600)));
			}
		}

		if($bool) {
			$bool = $this->customAddValidateRule($_REQ, $_REQ_RAW);
		}

		$bool = $bool && !$this->isError();

		$this->setCanVote($bool);

		return $bool;
	}

	/**
	 *
	 * @param unknown_type $_REQ
	 * @param unknown_type $_REQ_RAW
	 */
	protected function customAddValidateRule($_REQ, $_REQ_RAW) {
		return true;
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @param unknown_type $user_id
	 * @return int
	 */
	public function getCountByItemAndUserId($item_id, $user_id) {
		$collectionClass = $this->getCollectionClass();
		$collection = new $collectionClass();
		return $collection->getCount(array('item_id'=>$item_id, 'user_id'=>$user_id));
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @param unknown_type $user_id
	 * @return bool
	 */
	public function isUserItem($item_id, $user_id) {
		$bool = false;
		if($user_id) {
			$bool = $user_id==$this->getItemObject($item_id)->getField('user_id');
		}
		return $bool;
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @param unknown_type $user_id
	 * @return bool
	 */
	public function isVoted($item_id, $user_id) {
		$bool = true;
		if($user_id) {
			$bool = (bool)$this->getCountByItemAndUserId($item_id, $user_id)>0;
		}
		return $bool;
	}

	/**
	 *
	 * @param unknown_type $canVote
	 */
	public function setCanVote($canVote) {
		$this->canVote = (bool)$canVote;
	}

	/**
	 * @return bool
	 */
	public function canVote() {
		return (bool)$this->canVote;
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @param unknown_type $user_id
	 * @return bool
	 */
	public function checkCanVote($item_id, $user_id=null) {
		$user_id = $user_id ? $user_id : User::getOnlineUserId();
		$bool = $item_id && $user_id && !$this->isUserItem($item_id, $user_id) && !$this->isVoted($item_id, $user_id);
		$this->setCanVote($bool);
		return $this->canVote();
	}

	/**
	 *
	 * @param unknown_type $item_type
	 */
	public function setItemType($item_type) {
		$this->itemType = $item_type;
	}

	/**
	 * @return string
	 */
	public function getItemType() {
		return $this->itemType;
	}

	/**
	 *
	 * @param unknown_type $value
	 * @return string
	 */
	public static function getSignedValue($value) {
		//$value = sprintf('%.1f', $value);
		$value = number_format($value, 1);
		return Cast::int($value)>0 ? sprintf('+%s', $value) : $value;
	}

	/**
	 * @return int
	 */
	public function getTypeCons() {
		return self::VOTE_CONS;
	}
	/**
	 * @return int
	 */
	public function getTypeZero() {
		return self::VOTE_ZERO;
	}
	/**
	 * @return int
	 */
	public function getTypePros() {
		return self::VOTE_PROS;
	}

	/**
	 * @return boolean
	 */
	public function isAddable() {
		return User::isLoginned();
	}

	/**
	 * @return boolean
	 */
	public function isRemovable() {
		return $this->exists() && User::isOnlineModerator();
	}

	/**
	 *
	 */
	public function isTypeCons() {
		return $this->exists() && $this->getField('type')==self::VOTE_CONS;
	}

	/**
	 *
	 */
	public function isTypePros() {
		return $this->exists() && $this->getField('type')==self::VOTE_PROS;
	}

	/**
	 *
	 */
	public function isTypeZero() {
		return $this->exists() && $this->getField('type')==self::VOTE_ZERO;
	}

	/**
	 *
	 */
	public function isMyVote() {
		return $this->exists() && $this->getField('user_id')==User::getOnlineUserId();
	}
}

abstract class VoteCollection extends ObjectCollection {

	/**
	 *
	 * @var unknown_type
	 */
	protected $itemType;

	public function __construct($classname='Vote') {
		$object = new $classname();
		parent::__construct($object);
		$this->itemType = $object->getItemType();
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @return int
	 */
	public function getCountByItemIdTypeCons($item_id) {
		return $this->getCount(array('item_id'=>$item_id), array('value<0'));
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @return int
	 */
	public function getCountByItemIdTypePros($item_id) {
		return $this->getCount(array('item_id'=>$item_id), array('value>0'));
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @return int
	 */
	public function getCountByItemIdTypeZero($item_id) {
		return $this->getCount(array('item_id'=>$item_id), array('value=0'));
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @return float
	 */
	public function getValueSumByItemId($item_id) {
		$sql = sprintf('SELECT SUM(value) AS summa FROM %s WHERE item_id=%u', $this->db_table, $item_id);
		$sql_r = $this->db()->row($sql);
		$result = Cast::float($sql_r['summa']);
		return $result;
	}

	/**
	 * @return string
	 */
	public function getItemType() {
		return $this->itemType;
	}

	/**
	 * @return int
	 */
	public function getCountTotal() {
		return $this->length();
	}

	/**
	 * @return int
	 */
	public function getCountPros() {
		return $this->getCountCustom(Vote::VOTE_PROS);
	}

	/**
	 * @return int
	 */
	public function getCountZero() {
		return $this->getCountCustom(Vote::VOTE_ZERO);
	}

	/**
	 * @return int
	 */
	public function getCountCons() {
		return $this->getCountCustom(Vote::VOTE_CONS);
	}

	/**
	 * @return float
	 */
	public function getValueSum() {
		$result = 0.0;
		$value_arr = $this->getFieldValues('value');
		foreach($value_arr as $id=>$value) {
			$result += Cast::float($value);
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function getValueSumSigned() {
		return Vote::getSignedValue($this->getValueSum());
	}

	/**
	 *
	 * @param unknown_type $type
	 * @return int
	 */
	protected function getCountCustom($type) {
		$result = 0;
		$type_arr = $this->getFieldValues('type');
		foreach($type_arr as $id=>$value) {
			if($type==$value) {
				$result++;
			}
		}
		return $result;
	}
}
