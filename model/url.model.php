<?

class UrlModel {

	public static function redirect($url=null) {
		$url_default = '/';//URL_PROJECT;
		$url_referer = ifsetor($_SERVER['HTTP_REFERER'], $url_default, true);
		$url_self = ifsetor($_SERVER['PHP_SELF'], $url_default, true);
		if(_strstr($url_referer, $url_self)) {
			$url_referer = $url_default;
		}
		$url_request = ifsetor($_REQUEST['url'], $url_referer, true);
		$url_param = ifsetor($url, $url_request, true);
		Url::redirect($url_param);
	}

	public static function get_req_query($request_query=array()) {
		$request_params = null;
		if($request_query && is_array($request_query)) {
			$request_params = http_build_query($request_query);
			if($request_params) {
				$request_params = '?'.$request_params;
			}
		}
		return $request_params;
	}

	public static function homepage() {
		return URL_PROJECT;
	}

	public static function appendUrlName($url, $what) {
		$name = null;
		if(is_string($what)) {
			$name = $what;
		}
		elseif(is_object($what) && is_a($what, 'Object')) {
			$name = $what->getField('name');
		}
		if($name) {
			$name_url = Translit::urlify($name);
			if($name_url) {
				$url .= sprintf('%s%s', Translit::LITERAL_SEPARATOR, $name_url);
			}
		}
		return $url;
	}

	public static function auth_login() {
		$url_auth_login_clear = self::auth_login_clear();
		$url_auth_login = $url_auth_login_clear;
		$cururl = Url::currurl(array('url'));
		$bool_add_query = _strpos($cururl, $url_auth_login_clear)===false;
		if($bool_add_query) {
			$http_query = self::get_req_query(array('url'=>$cururl));
			$url_auth_login = sprintf($url_auth_login_clear.'%s', $http_query);
		}
		return $url_auth_login;
	}

	public static function auth_login_clear() {
		return self::homepage().sprintf('/login/');
	}

	public static function auth_logout() {
		return self::homepage().sprintf('/logout/');
	}

	public static function auth_remind() {
		return self::homepage().sprintf('/remind/');
	}

	/**
	 *
	 * @param int $user_id
	 * @param User $user
	 */
	public static function user($user_id, $user=null) {
		//$url=  self::userById($user_id, $user);
		$url = self::userByIdName($user_id, $user);
		//$url = self::userByUrlname($user_id, $user);
		return $url;
	}

	public static function userById($user_id, $user=null) {
		return self::homepage().sprintf('/profile/%u', $user_id);
	}

	public static function userByIdName($user_id, $user=null) {
		$url = self::userById($user_id, $user);
		if(1 && is_object($user)) {
			$url = self::appendUrlName($url, $user);
		}
		return $url;
	}

	public static function userByUrlname($user_id, $user=null) {
		$url = null;
		if(1 && is_object($user)) {
			$urlname = $user->getField('urlname');
			if($urlname) {
				$url = self::homepage().sprintf('/profile/%s', $urlname);
			}
		}
		if(!$url) {
			$url = self::userById($user_id, $user);
		}
		return $url;
	}

	public static function user_register() {
		return self::homepage().sprintf('/register/');
	}

	public static function user_lenta($request_query=array()) {
		return self::homepage().sprintf('/profiles/%s', self::get_req_query($request_query));
	}

	public static function user_edit() {
		return self::homepage().sprintf('/edit/');
	}

	/**
	 *
	 * @param int $photo_id
	 * @param Photo $photo
	 */
	public static function photo($photo_id, $photo=null) {
		//$url = self::photoById($photo_id, $photo);
		$url = self::photoByIdName($photo_id, $photo);
		return $url;
	}

	public static function photoById($photo_id, $photo) {
		return $url = self::homepage().sprintf('/photo/%u', $photo_id);
	}

	public static function photoByIdName($photo_id, $photo) {
		$url = self::photoById($photo_id, $photo);
		if(1 && is_object($photo)) {
			$url = self::appendUrlName($url, $photo);
		}
		return $url;
	}

	public static function photo_upload() {
		return self::homepage().sprintf('/upload/');
	}

	public static function photo_lenta($request_query=array()) {
		return self::homepage().sprintf('/photos/%s', self::get_req_query($request_query));
	}

	public static function photo_edit($photo_id) {
		return self::homepage().sprintf('/photo/edit/%s', self::get_req_query(array('id'=>$photo_id)));
	}

	public static function photo_remove($photo_id) {
		return self::homepage().sprintf('/photo/remove/%s', self::get_req_query(array('id'=>$photo_id)));
	}

	// ---

	public static function rss() {
		return self::homepage().sprintf('/rss/');
	}

	public static function rss_photo($request_query=array()) {
		return self::homepage().sprintf('/rss/photo.xml%s', self::get_req_query($request_query));
	}

	public static function rss_user($request_query=array()) {
		return self::homepage().sprintf('/rss/profile.xml%s', self::get_req_query($request_query));
	}

	public static function rss_comment($request_query=array()) {
		return self::homepage().sprintf('/rss/comment.xml%s', self::get_req_query($request_query));
	}

	// ---

	public static function support_feedback() {
		return self::homepage().sprintf('/feedback/');
	}

	public static function support_about() {
		return self::homepage().sprintf('/about/');
	}

	public static function support_rules() {
		return self::homepage().sprintf('/rules/');
	}

	public static function support_eula() {
		return self::homepage().sprintf('/eula/');
	}

	public static function support_adult() {
		return self::homepage().sprintf('/adult/');
	}

	// ---

	public static function comment_lenta($request_query=array()) {
		return self::homepage().sprintf('/comments/%s', self::get_req_query($request_query));
	}

	public static function comment_add() {
		return self::homepage().sprintf('/comment/add/');
	}

	public static function comment_get() {
		return self::homepage().sprintf('/comment/get/');
	}

	public static function comment_upd($id=0, $item_type=null) {
		$request_query = array();
		if($id || $item_type) {
			$request_query = array('id'=>$id, 'item_type'=>$item_type);
		}
		return self::homepage().sprintf('/comment/upd/%s', self::get_req_query($request_query));
	}

	public static function comment_clr($id=0, $item_type=null) {
		$request_query = array();
		if($id || $item_type) {
			$request_query = array('id'=>$id, 'item_type'=>$item_type);
		}
		return self::homepage().sprintf('/comment/clr/%s', self::get_req_query($request_query));
	}

	public static function comment_del($id=0, $item_type=null) {
		$request_query = array();
		if($id || $item_type) {
			$request_query = array('id'=>$id, 'item_type'=>$item_type);
		}
		return self::homepage().sprintf('/comment/del/%s', self::get_req_query($request_query));
	}

	// ---

	public static function vote_add() {
		return self::homepage().sprintf('/vote/add/');
	}

	public static function vote_get() {
		return self::homepage().sprintf('/vote/get/');
	}

	public static function vote_del($id=0, $item_type=null) {
		$request_query = array();
		if($id || $item_type) {
			$request_query = array('id'=>$id, 'item_type'=>$item_type);
		}
		return self::homepage().sprintf('/vote/del/%s', self::get_req_query($request_query));
	}

	// ---

	public static function search() {
		return self::homepage().'/search/';
	}

	// ---

	public static function social_share($service, $item_type, $item_id) {
		$request_query = array('service'=>$service,'item_type'=>$item_type,'item_id'=>$item_id);
		return self::homepage().sprintf('/share/%s', self::get_req_query($request_query));
	}

	// ---

	public static function admin_index() {
		return self::homepage().sprintf('/admin/');
	}

	public static function admin_dbg_ctrl() {
		return Url::fix(self::admin_index() . sprintf('/dbg_ctrl/'));
	}

	public static function admin_dbg_cron() {
		return Url::fix(self::admin_index() . sprintf('/dbg_cron/'));
	}

	public static function admin_user($uid=0) {
		$url = Url::fix(self::admin_index() . sprintf('/user/'));
		if($uid) {
			$request_query = http_build_query(array('id'=>$uid));
			$url .= '?'.$request_query;
		}
		return $url;
	}

	public static function admin_photo($pid=0) {
		$url = Url::fix(self::admin_index() . sprintf('/photo/'));
		if($pid) {
			$request_query = http_build_query(array('id'=>$pid));
			$url .= '?'.$request_query;
		}
		return $url;
	}

	public static function admin_location() {
		return Url::fix(self::admin_index() . sprintf('/location/'));
	}

	public static function admin_location_country($country_id=0) {
		return self::admin_location() . self::get_req_query(array('target'=>'country', 'country_id'=>$country_id));
	}

	public static function admin_location_city($city_id=0) {
		return self::admin_location() . self::get_req_query(array('target'=>'city', 'city_id'=>$city_id));
	}

	public static function admin_location_state($state_id=0) {
		return self::admin_location() . self::get_req_query(array('target'=>'state', 'state_id'=>$state_id));
	}

	public static function admin_location_name_url($target) {
		return self::admin_location() . self::get_req_query(array('target'=>$target, 'gen_url_name'=>'true'));
	}

	public static function admin_location_save_static($target) {
		return self::admin_location() . self::get_req_query(array('target'=>$target, 'save_static'=>'true'));
	}

	public static function reformal() {
		return 'http://exifer.reformal.ru/';
	}
}
