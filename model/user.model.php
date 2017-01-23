<?

class User extends Object {

	const MYSQL_TABLE = 'user';

	const STATUS_NEW = 0;
	const STATUS_OKE = 1;

	const ONLINE_ON = 1;
	const ONLINE_ALL = 0;
	const ONLINE_OFF = -1;

	const BITMASK_NONE = 0;
	const BITMASK_HIDE_BIRTHDAY = 1;
	const BITMASK_HIDE_ONLINE = 2;
	const BITMASK_HIDE_LOCATION = 4;
	//const BITMASK_3 = 8;
	//const BITMASK_4 = 16;
	//const BITMASK_5 = 32;
	//const BITMASK_6 = 64;
	//const BITMASK_7 = 128;
	//const BITMASK_8 = 256;
	//const BITMASK_9 = 512;
	//const BITMASK_10 = 1024;
	//const BITMASK_11 = 2048;
	//const BITMASK_12 = 4096;
	//const BITMASK_13 = 8192;
	//const BITMASK_14 = 16384;
	const BITMASK_ADMIN_MODER = 32768;
	const BITMASK_ADMIN_ROOT = 98304; // (32768 | 65536 = 98304)

	const GENDER_MALE = 'm';
	const GENDER_FEMALE = 'f';

	const IM_ICQ = 1;
	const IM_SKYPE = 2;
	const IM_GTALK = 3;
	const IM_YAHOO = 4;
	const IM_IRC = 5;
	const IM_MSN = 6;
	const IM_AIM = 7;
	const IM_JABBER = 8;

	const ADMIN_COMMENT_PHOTO = 1; // 2^0
	const ADMIN_VOTE_PHOTO = 2; // 2^1
	const ADMIN_PHOTO = 4; // 2^2
	const ADMIN_3 = 8; // 2^3
	const ADMIN_4 = 16; // 2^4
	const ADMIN_5 = 32; // 2^5
	const ADMIN_6 = 64; // 2^6
	const ADMIN_7 = 128; // 2^7
	const ADMIN_8 = 256; // 2^8
	const ADMIN_9 = 512; // 2^9
	const ADMIN_10 = 1024; // 2^10
	const ADMIN_11 = 2048; // 2^11
	const ADMIN_12 = 4096; // 2^12
	const ADMIN_13 = 8192; // 2^13
	const ADMIN_14 = 16384; // 2^14
	const ADMIN_15 = 32768; // 2^15
	const ADMIN_16 = 65536; // 2^16
	const ADMIN_17 = 131072; // 2^17
	const ADMIN_18 = 262144; // 2^18
	const ADMIN_19 = 524288; // 2^19
	const ADMIN_20 = 1048576; // 2^20
	const ADMIN_21 = 2097152; // 2^21
	const ADMIN_22 = 4194304; // 2^22
	const ADMIN_23 = 8388608; // 2^23
	const ADMIN_24 = 16777216; // 2^24
	const ADMIN_25 = 33554432; // 2^25
	const ADMIN_26 = 67108864; // 2^26
	const ADMIN_27 = 134217728; // 2^27
	const ADMIN_28 = 268435456; // 2^28
	const ADMIN_29 = 536870912; // 2^29
	const ADMIN_30 = 1073741824; // 2^30
	const ADMIN_31 = 2147483648; // 2^31
	const ADMIN_32 = 4294967296; // 2^32
	const ADMIN_33 = 8589934592; // 2^33
	const ADMIN_34 = 17179869184; // 2^34
	const ADMIN_35 = 34359738368; // 2^35
	const ADMIN_36 = 68719476736; // 2^36
	const ADMIN_37 = 137438953472; // 2^37
	const ADMIN_38 = 274877906944; // 2^38
	const ADMIN_39 = 549755813888; // 2^39
	const ADMIN_40 = 1099511627776; // 2^40
	const ADMIN_41 = 2199023255552; // 2^41
	const ADMIN_42 = 4398046511104; // 2^42
	const ADMIN_43 = 8796093022208; // 2^43
	const ADMIN_44 = 17592186044416; // 2^44
	const ADMIN_45 = 35184372088832; // 2^45
	const ADMIN_46 = 70368744177664; // 2^46
	const ADMIN_47 = 140737488355328; // 2^47
	const ADMIN_48 = 281474976710656; // 2^48
	const ADMIN_49 = 562949953421312; // 2^49
	const ADMIN_50 = 1125899906842624; // 2^50
	const ADMIN_51 = 2251799813685248; // 2^51
	const ADMIN_52 = 4503599627370496; // 2^52
	const ADMIN_53 = 9007199254740992; // 2^53
	const ADMIN_54 = 18014398509481984; // 2^54
	const ADMIN_55 = 36028797018963968; // 2^55
	const ADMIN_56 = 72057594037927936; // 2^56
	const ADMIN_57 = 144115188075855872; // 2^57
	const ADMIN_58 = 288230376151711744; // 2^58
	const ADMIN_59 = 576460752303423488; // 2^59
	const ADMIN_60 = 1152921504606846976; // 2^60
	const ADMIN_61 = 2305843009213693952; // 2^61
	const ADMIN_62 = 4611686018427387904; // 2^62
	const ADMIN_63 = 9223372036854775808; // 2^63

	/**
	 *
	 * @var Session
	 */
	private static $session;
	/**
	 *
	 * @var User
	 */
	private static $user;

	/**
	 * @constructor
	 * @param unknown_type $id
	 */
	//public function __construct($id=0) {
	//	parent::__construct($id, self::db_table());
	//}

	/**
	 * @return Session
	 */
	public static function getStaticSession() {
		return self::$session;
	}

	/**
	 * @return User
	 */
	public static function getStaticUser() {
		return self::$user;
	}

	/**
	 * @return Session
	 */
	public static function getOnlineSession($refresh=false) {
		if(!self::$session || $refresh) {
			$session = new Session();
			self::$session = $session->retrieveSession();
			if(!self::$session) {
				self::$session = new Session();
			}
		}
		return self::getStaticSession();
	}

	/**
	 * @return User
	 */
	public static function getOnlineUser($refresh=false) {
		if(!self::$user || $refresh) {
			$session_profile = new UserSession(self::getOnlineUserId());
			self::$user = $session_profile->get();
			if(!self::$user) {
				self::$user = new self();
				self::$session = new Session();
			}
		}
		return self::getStaticUser();
	}

	/**
	 * @return int
	 */
	public static function getOnlineUserId($refresh=false) {
		return self::getOnlineSession($refresh)->getUserId();
	}

	/**
	 * @return bool
	 */
	public static function isOnlineAdmin($refresh=false) {
		return self::getOnlineUser($refresh)->isAdmin();
	}

	/**
	 * @return bool
	 */
	public static function isOnlineModerator($refresh=false) {
		return self::getOnlineUser($refresh)->isModerator();
	}

	/**
	 * @return string
	 */
	public static function getOnlineSessionId($refresh=false) {
		return self::getOnlineSession($refresh)->getSessionId();
	}

	/**
	 * @return bool
	 */
	public static function getOnlineRemember($refresh=false) {
		return self::getOnlineSession($refresh)->getRemember();
	}

	/**
	 * @return bool
	 */
	public static function isLoginned() {
		return (bool)self::getOnlineUserId();
	}

	/**
	 * @return bool
	 */
	public static function checkLoginned() {
		if(!self::isLoginned()) {
			Url::redirect(UrlModel::auth_login());
		}
		return true;
	}

	public static function retrieveSession($refresh=false) {
		return self::getOnlineUser()->exists();
		//return self::getOnlineSessionId($refresh) && self::getOnlineUserId($refresh);
	}

	/**
	 *
	 * @param unknown_type $what
	 * @return bool
	 */
	public static function statusExists($what) {
		return $what===self::STATUS_NEW || $what===self::STATUS_OKE;
	}

	/**
	 *
	 * @param unknown_type $what
	 * @return bool
	 */
	public static function genderExists($what) {
		return $what===self::GENDER_MALE || $what===self::GENDER_FEMALE;
	}

	/**
	 *
	 * @param unknown_type $what
	 * @return bool
	 */
	public static function IMExists($what) {
		return insetcheck($what, self::getIMs());
	}

	/**
	 *
	 * @param unknown_type $what
	 * @return bool
	 */
	public static function adminBitmaskExists($what) {
		return self::bitmaskExists($what) && ($what===self::BITMASK_ADMIN_ROOT || $what===self::BITMASK_ADMIN_MODER);
	}

	/**
	 *
	 * @param unknown_type $what
	 * @return bool
	 */
	public static function bitmaskExists($what) {
		return insetcheck($what, ReflectionModel::getClassConstValueList(__CLASS__, 'BITMASK_'));
	}

	public static function getIMs() {
		return ReflectionModel::getClassConstValueList(__CLASS__, 'IM_');
	}

	/**
	 * @override
	 * @see AtomicObject::afterUpdate()
	 */
	protected function afterUpdate() {
		if(!$this->isBanned()) {
			$user_session = new UserSession($this->getId());
			$user_session->save();
		}
	}

	/**
	 * @override
	 * @see AtomicObject::loadExtraFields()
	 */
	protected function loadExtraFields() {
		$this->setExtraField('name', SafeHtmlModel::output($this->getField('name')));
		$this->setExtraField('upload_periodical_limit', $this->getPeriodicalUploadLimit());
		$this->setExtraField('userpic', UserpicModel::GetOnePreparedBlind($this->getId(), $this->getField('userpic_tstamp')));
		if($this->getExtraField('occupation')===null) {
			$this->setExtraField('occupation', array());
		}
	}

	/**
	 * @database lookup
	 * @return AtomicObject
	 */
	public function extendThumb($thumb_format=null) {
		return $this->setExtraField('userpic', UserpicModel::GetOnePrepared($this->getId(), $thumb_format));
	}

	/**
	 * @return AtomicObject
	 */
	public function extendOccupation() {
		return $this->setExtraField('occupation', OccupationModel::SelectOneUserData($this->getId()));
	}

	/**
	 * @return AtomicObject
	 */
	public function extendAboutInfo() {
		$this->setExtraField('about', SafeHtmlModel::output($this->getField('about')));
		foreach(array('about_emails', 'about_phones', 'about_urls', 'about_ims') as $field) {
			$value = $this->getField($field) ? JsonModel::decode($this->getField($field)) : array();
			$this->setExtraField($field, $value);
			continue;
			if(is_array($value)) {
				foreach($value as $k=>&$v) {
					if(is_array($v)) {
						foreach($v as $kv=>&$vv) {
							$vv = SafeHtmlModel::output($vv);
						}
					}
					else {
						$v = SafeHtmlModel::output($v);
					}
				}
			}
			else {
				$value = SafeHtmlModel::output($value);
			}
			$this->setExtraField($field, $value);
		}
		return $this;
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
		if(!$__validator->user_name($_REQ['name'])) {
			$this->pushError('NAME_FORMAT');
		}

		//$_REQ['urlname'] = ifsetor($_REQ['urlname'], null);
		//if(!$__validator->user_urlname($_REQ['urlname'])) {
		//	$this->pushError('URLNAME_FORMAT');
		//}

		$_REQ['gender'] = ifsetor($_REQ['gender'], null);
		if(!$this->genderExists($_REQ['gender'])) {
			$this->pushError('GENDER_FORMAT');
		}

		$_REQ['about'] = ifsetor($_REQ['about'], null);
		if(!$__validator->user_about($_REQ['about'])) {
			$this->pushError('ABOUT_FORMAT');
		}

		$_REQ['birthday'] = ifsetor($_REQ['birthday'], null);
		if(!$__validator->user_birthday_tstamp($_REQ['birthday'])) {
			$this->pushError('BIRTHDAY_FORMAT');
		}

		$_REQ['country'] = ifsetor($_REQ['country'], null);
		$countryList = LocationModel::GetOneCountryListByCountryId($_REQ['country']);
		if(!$countryList) {
			$this->pushError('COUNTRY_FORMAT');
		}

		$_REQ['city'] = ifsetor($_REQ['city'], null);
		$cityList = LocationModel::GetOneCityListByCityId($_REQ['city']);
		if(!$cityList) {
			$this->pushError('CITY_FORMAT');
		}

		if(!$countryList || !$cityList || $countryList['id']!=$cityList['country_id']) {
			$this->pushError('COUNTRY_CITY_FORMAT');
		}

		return !$this->isError();
	}

	/**
	 * @override
	 * @see Object::collectAddFields()
	 */
	protected function collectAddFields($_REQ) {

		$result = array();

		$result['email'] = SafeHtmlModel::input(_strtolower(ifsetor($_REQ['email'], null)));

		$result['password'] = _trim(ifsetor($_REQ['password'], null));

		$result['name'] = SafeHtmlModel::input(ifsetor($_REQ['name'], null));

		$result['urlname'] = $this->getUniqUrlName($result['name']);

		$result['about'] = SafeHtmlModel::input(ifsetor($_REQ['about'], null));

		foreach(array('about_emails', 'about_phones', 'about_urls', 'about_ims') as $field) {
			$result[$field] = (is_array($_REQ[$field]) && $_REQ[$field]) ? JsonModel::encode($_REQ[$field]) : null;
		}

		$result['gender'] = ifsetor($_REQ['gender'], User::GENDER_MALE);

		$result['country'] = ifsetor($_REQ['country'], null);

		$result['city'] = ifsetor($_REQ['city'], null);

		$result['birthday'] = DateConst::mk_time(@$_REQ['day'], @$_REQ['month'], @$_REQ['year']);

		$result['bitmask'] = User::BITMASK_NONE;
		if(isset($_REQ['hide_location']) && $_REQ['hide_location']) {
			Cast::setbit(&$result['bitmask'], User::BITMASK_HIDE_LOCATION);
		}
		if(isset($_REQ['hide_birthday']) && $_REQ['hide_birthday']) {
			Cast::setbit(&$result['bitmask'], User::BITMASK_HIDE_BIRTHDAY);
		}

		$result['status'] = self::STATUS_OKE;
		$result['authcode'] = md5(uniqid());

		$result['reg_tstamp'] = time();
		$result['reg_ip'] = Network::clientIp();
		$result['reg_fwrd'] = Network::clientFwrd();

		$result['hit_tstamp'] = time();
		$result['hit_ip'] = Network::clientIp();
		$result['hit_fwrd'] = Network::clientFwrd();

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateAdd()
	 */
	protected function validateAdd($_REQ, $_REQ_RAW) {

		$__validator = new ValidatorModel();

		$_REQ['email'] = ifsetor($_REQ['email'], null);
		if(!$__validator->user_email($_REQ['email'])) {
			$this->pushError('EMAIL_FORMAT');
		}

		$_REQ['status'] = ifsetor($_REQ['status'], null);
		if(!$this->statusExists($_REQ['status'])) {
			$this->pushError('STATUS_FORMAT');
		}

		$_REQ['password'] = ifsetor($_REQ['password'], null);
		if(!$__validator->user_password($_REQ['password'])) {
			$this->pushError('PASSWORD_FORMAT');
		}

		$this->validateAddChange($_REQ, $_REQ_RAW);

		if(!$this->isError()) {
			// check existing user
			$user_collection = new UserCollection();
			$user_collection->getCollection(array('email'=>$_REQ['email']));
			if($user_collection->length()) {
				$this->pushError('EMAIL_EXISTS');
			}
			else {
				// check previously deleted user
				$user_deleted_collection = new UserDeletedCollection();
				$user_deleted_collection->getCollection(array('email'=>$_REQ['email'], 'is_spamer'=>1));
				if($user_deleted_collection->length()) {
					$this->pushError('EMAIL_SPAMER_EXISTS');
				}
			}
		}

		return !$this->isError();
	}

	/**
	 * @override
	 * @see Object::collectChangeFields()
	 */
	protected function collectChangeFields($_REQ) {

		$result = array();

		$password_new = ifsetor($_REQ['password_new'], null);
		$password_verify = ifsetor($_REQ['password_verify'], null);

		if($password_new) {
			if(strcmp($password_verify, $this->getField('password'))!==0) {
				$this->pushError('PASSWORD_NEW_CONF');
			}
			else {
				$result['password'] = $password_new;
			}
		}

		$result['name'] = SafeHtmlModel::input(ifsetor($_REQ['name'], $this->getField('name')));

		$result['urlname'] = $this->getUniqUrlName($result['name']);

		$result['about'] = SafeHtmlModel::input(ifsetor($_REQ['about'], $this->getField('about')));

		foreach(array('about_emails', 'about_phones', 'about_urls', 'about_ims') as $field) {
			$result[$field] = (is_array($_REQ[$field]) && $_REQ[$field]) ? JsonModel::encode($_REQ[$field]) : null;
		}

		$result['gender'] = ifsetor($_REQ['gender'], $this->getField('gender'));

		$result['country'] = ifsetor($_REQ['country'], $this->getField('country'));

		$result['city'] = ifsetor($_REQ['city'], $this->getField('city'));

		$result['birthday'] = DateConst::mk_time(@$_REQ['day'], @$_REQ['month'], @$_REQ['year']);

		$result['bitmask'] = $this->getField('bitmask');

		$func_bit = (isset($_REQ['hide_birthday']) && $_REQ['hide_birthday']) ? 'setbit' : 'unsetbit';
		Cast::$func_bit(&$result['bitmask'], User::BITMASK_HIDE_BIRTHDAY);

		$func_bit = (isset($_REQ['hide_location']) && $_REQ['hide_location']) ? 'setbit' : 'unsetbit';
		Cast::$func_bit(&$result['bitmask'], User::BITMASK_HIDE_LOCATION);

		$result['update_tstamp'] = time();
		$result['update_ip'] = Network::clientIp();
		$result['update_fwrd'] = Network::clientFwrd();

		$result['hit_tstamp'] = time();
		$result['hit_ip'] = Network::clientIp();
		$result['hit_fwrd'] = Network::clientFwrd();

		return $result;
	}

	/**
	 * @override
	 * @see Object::validateChange()
	 */
	protected function validateChange($_REQ, $_REQ_RAW) {

		$__validator = new ValidatorModel();

		if(isset($_REQ['password']) && !$__validator->user_password($_REQ['password'])) {
			$__error->push('PASSWORD_FORMAT');
		}

		$this->validateAddChange($_REQ, $_REQ_RAW);

		return !$this->isError();
	}

	/**
	 *
	 * @param bool $remember
	 * @return bool
	 */
	public function login($remember=false) {

		$result = false;

		$user_id = $this->getId();

		if($user_id && $this->getField('status')==self::STATUS_OKE && !$this->isBanned()) {

			$session = new Session();
			$session->setUserId($user_id);
			$session->setRemember($remember);
			$session->saveSession();
			//self::getOnlineSession(true); // will fail due to cookie is not set yet in current http session
			self::$session = $session;

			$user_session = new UserSession($user_id);
			$user_session->save();
			$user = self::getOnlineUser(true);

			if(!$user->isBitmaskSet(User::BITMASK_HIDE_ONLINE)) {
				$user->setField('hit_tstamp', time());
				$user->setField('hit_ip', Network::clientIp());
				$user->setField('hit_fwrd', Network::clientFwrd());
			}
			$user->setField('login_tstamp', time());
			$user->setField('login_ip', Network::clientIp());
			$user->setField('login_fwrd', Network::clientFwrd());
			$user->update();

			//$user_online = new UserOnline();
			//$user_online->add();
			//$user_online->synchronize();

			$result = self::getOnlineSessionId() && self::getOnlineUserId();
		}

		return $result;
	}

	/**
	 * @return bool
	 */
	public function logout() {

		$result = false;

		$user_id = $this->getConstructId();

		if($user_id && $user_id==self::getOnlineUserId()) {
			$session = self::getOnlineSession();
			$result = $session->removeSession();
			self::$session = self::$user = null;
			//$user_online_collection = new UserOnlineCollection();
			//$user_online_collection->removeByUserId($user_id);
		}

		return $result;
	}

	/**
	 * @return bool
	 */
	public function logoutGlobal($user_id=0) {

		$result = false;

		$user_id = $user_id ? $user_id : $this->getConstructId();

		if($user_id) {

			$user_session = new UserSession($user_id);
			$result = $user_session->remove();

			//$user_online_collection = new UserOnlineCollection();
			//$user_online_collection->removeByUserId($user_id);

			if($user_id==self::getOnlineUserId()) {
				$this->logout();
			}
		}

		return $result;
	}

	/**
	 *
	 * @param unknown_type $status
	 * @return bool
	 */
	public function changeStatus($status) {

		$result = false;

		if($this->exists() && self::statusExists($status)) {

			$this->setField('status', $status);
			$this->update();

			$user_session = new UserSession($this->getId());

			if($status==self::STATUS_OKE) {
				$user_session->update();
			}
			else {
				$user_session->remove();
			}

			$result = true;
		}

		return $result;
	}

	const RECALC_UPLOAD_TSTAMP = 1;
	const RECALC_PHOTO_COUNT = 2;
	const RECALC_COMMENT_COUNT = 4;
	const RECALC_VIEW_COUNT = 8;
	const RECALC_ARTICLE_COUNT = 16;
	/**
	 *
	 * @param unknown_type $recalcbit
	 */
	public function recalcInfo($recalcbit=15) {

		$result = false;

		if($this->exists()) {

			if($recalcbit&self::RECALC_UPLOAD_TSTAMP) {
				$sql = sprintf(
					'(SELECT add_tstamp FROM %1$s WHERE status=%2$s AND user_id=%3$s ORDER BY id DESC LIMIT 1)',
					Photo::db_table(), Photo::STATUS_OKE, $this->getId()
				);
				$this->setField('upload_tstamp', $sql, true);
			}
			if($recalcbit&self::RECALC_PHOTO_COUNT) {
				$sql = sprintf(
					'(SELECT count(*) AS cnt FROM %1$s WHERE status=%2$s AND user_id=%3$s)',
					Photo::db_table(), self::STATUS_OKE, $this->getId()
				);
				$this->setField('photos', $sql, true);
			}
			if($recalcbit&self::RECALC_COMMENT_COUNT) {
				$comment_photo_collection = new CommentPhotoCollection();
				$this->setField('comments', $comment_photo_collection->getCount(array('user_id'=>$this->getId())));
			}
			if($recalcbit&self::RECALC_VIEW_COUNT) {
				$this->setField('views', 'views_guest+views_user', true);
			}
			//if($recalcbit&self::RECALC_ARTICLE_COUNT) {
			//	$article_collection = new ArticleCollection();
			//	$this->setField('articles', $article_collection->getCount(array('user_id'=>$this->getId())));
			//}

			$result = $this->update();
		}

		return $result;
	}

	/**
	 * @override
	 * @see AtomicObject::removeById()
	 */
	public function removeById($is_spamer=false) {

		$result = false;

		if(!$this->exists()) return $result;

		$user_id = $this->getId();

		// remove photos
		$photo_collection = new PhotoCollection();
		$photo_collection->removeByUserId($user_id);

		// remove comments
		$comment_collection = new CommentPhotoCollection();
		$comment_collection->removeByUserId($user_id);

		// remove votes
		$vote_collection = new VotePhotoCollection();
		$vote_collection->removeByUserId($user_id);

		// remove online
		$user_online_collection = new UserOnlineCollection();
		$user_online_collection->removeByUserId($user_id);

		// Remove user_occupation_experience
		OccupationModel::RemoveUserData($user_id);

		// Remove userpics
		UserpicModel::Remove($user_id);

		// remove photo user view data
		$photo_user_view_collection = new PhotoUserViewCollection();
		$photo_user_view_collection->removeByUserId($user_id);

		// user deleted
		$user_deleted = new UserDeleted();
		foreach($this->getFields() as $field=>$value) {
			$user_deleted->setField($field, $value);
		}
		$user_deleted->setField('is_spamer', $is_spamer?1:0);
		$user_deleted->save();

		$this->logout();

		$result = parent::removeById();

		$this->logoutGlobal($user_id);

		// remove profile session
		//$user_session = new UserSession($user_id);
		//$user_session->remove();

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
	 * @param unknown_type $bit
	 * @return bool
	 */
	public function isBitmaskSet($bit) {
		return $bit && self::bitmaskExists($bit) && ($bit&$this->getField('bitmask'))==$bit;
	}

	/**
	 *
	 * @param unknown_type $bit
	 * @return bool
	 */
	public function isAdmin($bit=self::BITMASK_ADMIN_ROOT) {
		return self::adminBitmaskExists($bit) && $this->isBitmaskSet($bit);
	}

	/**
	 * @return bool
	 */
	public function isModerator() {
		return $this->isAdmin(self::BITMASK_ADMIN_MODER);
	}

	/**
	 * @return bool
	 */
	public function isMale() {
		return $this->getField('gender')==self::GENDER_MALE;
	}

	/**
	 * @return bool
	 */
	public function isFemale() {
		return $this->getField('gender')==self::GENDER_FEMALE;
	}

	/**
	 * @return bool
	 */
	public function isBanned() {
		return $this->getField('ban_tstamp')>time();
	}

	/**
	 *
	 * @param unknown_type $force_show
	 * @return bool
	 */
	public function isBirthday($force_show=false) {
		$hide_birthday = $this->isBitmaskSet(self::BITMASK_HIDE_BIRTHDAY);
		$is_birthday = date('d.m', $this->getField('birthday'))===date('d.m', time());
		return $is_birthday && (!$hide_birthday || $this->isModerator() || $force_show || ($this->getId()==self::getOnlineUserId()));
	}

	/**
	 *
	 * @return bool
	 */
	public function canUpload() {
		$upload_limit = $this->getField('upload_limit');
		//$upload_tstamp = $this->getField('upload_tstamp');
		//$upload_next_tstamp = $this->getField('upload_next_tstamp');
		//$bool = $upload_limit>0 || $upload_next_tstamp<time();
		$bool = $upload_limit>0;
		return $bool;
	}

	/**
	 * @return bool
	 */
	public function getPeriodicalUploadLimit() {
		return 3;
	}

	/**
	 * @return bool
	 */
	public function getPeriodicalUploadTime() {
		return 1*24*60*60;
	}

	public function getNextUploadTime() {
		$time = 0;
		if($this->canUpload() && $this->getField('upload_next_tstamp')>time()) {
			$time = $this->getField('upload_next_tstamp');
		}
		else {
			$time = time() + $this->getPeriodicalUploadTime();
		}
		return $time;
	}

	public function getUniqUrlName($name=null) {
		if(!$name) {
			$name = $this->getField('name');
		}
		$urlname = Translit::generateUrlName($name);
		if($urlname) {
			$user = new User();
			$user->load(array('urlname'=>$urlname), array('id!='.$this->getId()));
			if($user->exists()) {
				_preg_match('/[0-9]+$/ui', $urlname, $matches);
				$number = Cast::unsignint(ifsetor($matches[0], 0));
				$new_number = $number+1;
				$urlname_new = _str_replace($number, $new_number, $urlname);
				if($urlname==$urlname_new) {
					$urlname_new .= $new_number;
				}
				return $this->getUniqUrlName($urlname_new);
			}
		}
		else {
			$names = array('abc', 'xyz', 'user', 'unknown');
			list($i, $k) = array(array_rand($names), array_rand($names));
			$name = sprintf('%s_%s', $names[$i], $names[$k]);
			return $this->getUniqUrlName($name);
		}
		return $urlname;
	}
}

class UserCollection extends ObjectCollection {

	public function __construct($classname='User') {
		$object = new $classname();
		parent::__construct($object);
	}

	/**
	 * Extend thumbnail user info
	 * @param unknown_type $thumb_format
	 */
	public function extendThumbCollection($thumb_format=null) {
		$this->extendCollectionExtraField($this->id_field, 'userpic', 'getThumbObjectCollection', array($thumb_format));
	}

	/**
	 *
	 */
	protected function getThumbObjectCollection() {
		return call_user_func_array(array('UserpicModel','GetPrepared'), func_get_args());
	}

	/**
	 * Extend user occupation info
	 * @param unknown_type $occupation_id
	 * @param unknown_type $experience_id
	 */
	public function extendOccupationCollection($occupation_id=null, $experience_id=null) {
		$this->extendCollectionExtraField($this->id_field, 'occupation', 'getOccupationObjectCollection', func_get_args());
	}

	/**
	 *
	 */
	protected function getOccupationObjectCollection() {
		return call_user_func_array(array('OccupationModel','SelectUserData'), func_get_args());
	}
}
