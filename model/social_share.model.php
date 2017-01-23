<?

class SocialShare extends Object {

	const MYSQL_TABLE = 'social_share';

	const SERVICE_FACEBOOK = 1;
	const SERVICE_VKONTAKTE = 2;
	const SERVICE_TWITTER = 3;
	const SERVICE_BUZZ = 4;

	const ITEM_PHOTO = 1;

	private static function getShareServiceIdAliasMap() {
		$map = array(
			self::SERVICE_FACEBOOK => 'facebook',
			self::SERVICE_VKONTAKTE => 'vkontakte',
			self::SERVICE_TWITTER => 'twitter',
			self::SERVICE_BUZZ => 'buzz',
		);
		return $map;
	}

	public static function getShareServiceAliasById($what) {
		$result = null;
		$map = self::getShareServiceIdAliasMap();
		$result = ifsetor($map[$what], $result);
		return $result;
	}

	public static function getShareServiceIdByAlias($what) {
		$result = 0;
		$map = self::getShareServiceIdAliasMap();
		$map = array_flip($map);
		if(is_array($map)) {
			$result = ifsetor($map[$what], $result);
		}
		return $result;
	}

	// ---

	private static function getShareItemIdAliasMap() {
		$map = array(
			self::ITEM_PHOTO => 'photo',
		);
		return $map;
	}

	public static function getShareItemAliasById($what) {
		$result = null;
		$map = self::getShareItemIdAliasMap();
		$result = ifsetor($map[$what], $result);
		return $result;
	}

	public static function getShareItemIdByAlias($what) {
		$result = 0;
		$map = self::getShareItemIdAliasMap();
		$map = array_flip($map);
		if(is_array($map)) {
			$result = ifsetor($map[$what], $result);
		}
		return $result;
	}

}

class SocialShareCollection extends ObjectCollection {

	public function __construct($classname='SocialShare') {
		$object = new $classname();
		parent::__construct($object);
	}
}