<?

class UserControl extends ControlModel {

	const USERPIC_MAX_SIZE = 2097152; // 2*1024*1024

	const REGISTER_INACTIVE = 0;

	/*
	public function preregister() {

		$user = $this->getUserRegisterByAuthCode(ifsetor($_GET['authcode'], null));

		if(self::REGISTER_INACTIVE) {
			$user->pushError('USER_REGISTER_INACTIVE');
		}

		$user->setCustomFields(array_merge($user->getFields(), $_GET, $_POST));

		$captcha = new CaptchaModel();

		if(Predicate::posted() && !$user->isError()) {

			$user->addTest($user->getCustomFields());

			if(!$captcha->Validate($user->getCustomField('captcha'))) {
				$user->pushError('CAPTCHA_ERROR');
			}

			if(!$user->isError()) {

				$user->add();

				if($user->getId()) {
					// send registration email
					MessageModel::user_preregister($user->getId());
				}
			}
		}

		$assign = array(
			'user' => $user,
			'Validator' => new ValidatorModel(),
			'captcha' => $captcha->getCaptchaMainParams(),
		);

		return $this->layout('user/register.tpl', $assign);
	}
	*/

	public function register() {

		if(User::isLoginned()) {
			Url::redirect(UrlModel::user_edit());
		}

		// get user register email by authcode
		//$user_register = $this->getUserRegisterByAuthCode(ifsetor($_REQUEST['authcode'], null));
		//if(!$user_register->exists() || $user_register->getField('status')!=UserRegister::STATUS_NEW) {
		//	return $this->preregister();
		//}

		$user = new User();

		if(self::REGISTER_INACTIVE) {
			$user->pushError('USER_REGISTER_INACTIVE');
		}

		$this->execRegisterEditCommonPrefix($user);

		//$user->setCustomField('email', $user_register->getField('email'));

		$fileUploader = new FileUploadModel();

		$captcha = new CaptchaModel();

		if(Predicate::posted() && !$user->isError()) {

			self::set_time_limit_user_abort(5*60);

			$user->addTest($user->getCustomFields());

			if(!$captcha->Validate($user->getCustomField('captcha'))) {
				$user->pushError('CAPTCHA_ERROR');
			}

			$userpic = array();
			if(!$user->isError()) {
				$userpic = $this->upload_userpic($fileUploader, $user);
			}

			if(!$user->isError()) {

				$user->add($user->getCustomFields());

				if($user->getId()) {

					// finish preregistration step
					//$user_register->setField('status', UserRegister::STATUS_REGISTERED);
					//$user_register->update();

					// processing user occupation data
					if($user->getCustomField('occupation')) {
						OccupationModel::InsertUserData($user->getId(), $user->getCustomField('occupation'), false);
					}

					// userpic processing
					if($userpic) {
						$filepath = ifsetor($userpic['fpath'], null);
						UserpicModel::Create($user->getId(), $filepath);
					}

					// send registration email
					MessageModel::user_register($user->getId());

					// send email to support when new user is registered
					$message_subject = 'New user';
					$message_body = sprintf('User %s %s registered', $user->getField('name'), UrlModel::user($user->getId(), $user));
					__mailme($message_subject, $message_body);

					$cipher = new Cipher();
					$r_email = $cipher->encrypt($user->getField('email'));
					$http_query = http_build_query(array('postreg'=>$r_email));
					Url::redirect(UrlModel::auth_login_clear().'?'.$http_query);
				}
				else {
					$user->pushError('DATA_SAVE_FAIL');
				}
			}
		}

		$this->execRegisterEditCommonPostfix($user);

		$assign = $this->getRegisterEditCommonAssign($user);
		$assign += array('captcha' => $captcha->getCaptchaMainParams());

		$this->setHtmlMetaTitle(SeoModel::user_register(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::user_register(SeoModel::DESCRIPTION));

		return $this->layout('user/register_edit.tpl', $assign);
	}

	public function edit() {

		require_auth();

		$user = new User(User::getOnlineUserId());
		$user->extendOccupation();
		$user->extendThumb();
		$user->extendAboutInfo();

		$this->execRegisterEditCommonPrefix($user);

		$fileUploader = new FileUploadModel();

		if(Predicate::posted()) {

			self::set_time_limit_user_abort(5*60);

			$user->changeTest($user->getCustomFields());

			$userpic = array();
			if(!$user->isError()) {
				$userpic = $this->upload_userpic($fileUploader, $user);
			}

			if(!$user->isError()) {

				$changed = $user->change($user->getCustomFields());

				if(!$user->isError()) {

					if($changed) {

						// processing user occupation data
						OccupationModel::InsertUserData($user->getId(), $user->getCustomField('occupation'));

						// remove userpic
						if($user->getCustomField('userpic_del')) {
							UserpicModel::Remove($user->getId());
						}

						// create userpic
						if($userpic) {
							$filepath = ifsetor($userpic['fpath'], null);
							UserpicModel::Create($user->getId(), $filepath);
						}

						Url::redirect(UrlModel::user($user->getId(), $user));
					}
					else {
						$user->pushError('DATA_SAVE_FAIL');
					}

				}
			}
		}

		$this->execRegisterEditCommonPostfix($user);

		$assign = $this->getRegisterEditCommonAssign($user);

		$this->setHtmlMetaTitle(SeoModel::user_edit($user, SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::user_edit($user, SeoModel::DESCRIPTION));

		return $this->layout('user/register_edit.tpl', $assign);
	}

	/**
	 *
	 * @param User $user
	 * @return void
	 */
	private function execRegisterEditCommonPrefix(User &$user) {

		$_REQ =& $_POST;

		$user->setCustomFields(array_merge($user->getFields(), $_REQ));

		if(!$user->getCustomField('day')) {
			$user->setCustomField('day', DateConst::getDay($user->getField('birthday')));
		}

		if(!$user->getCustomField('month')) {
			$user->setCustomField('month', DateConst::getMonth($user->getField('birthday')));
		}

		if(!$user->getCustomField('year')) {
			if($user->exists()) {
				$user->setCustomField('year', DateConst::getYear($user->getField('birthday')));
			} else {
				$user->setCustomField('year', DateConst::getYear()-ValidatorModel::AGE_AVG);
			}
		}

		if(!$user->getCustomField('gender')) {
			$user->setCustomField('gender', User::GENDER_MALE);
		}

		if(!$user->getCustomField('country')) {
			$user->setCustomField('country', LocationModel::DEF_COUNTRY);
			$user->setCustomField('city', LocationModel::DEF_CITY);
		}

		$__validator = new ValidatorModel();

		$user->setCustomField('about_emails', ifsetor($user->getExtraField('about_emails'), array()));
		if(isset($_REQ['about_emails']) && is_array($_REQ['about_emails'])) {
			$about_emails = array();
			foreach($_REQ['about_emails'] as $k=>$v) {
				if(Regexp::match_email($v) && count($about_emails)<$__validator->maxEmailCount) {
					$about_emails[] = $v;
				}
			}
			$user->setCustomField('about_emails', $about_emails);
		}
		$user->setCustomField('about_phones', ifsetor($user->getExtraField('about_phones'), array()));
		if(isset($_REQ['about_phones']) && is_array($_REQ['about_phones'])) {
			$about_phones = array();
			foreach($_REQ['about_phones'] as $k=>$v) {
				//$v = SafeHtmlModel::input($v);
				if(Regexp::match_phone($v) && count($about_phones)<$__validator->maxPhoneCount) {
					$about_phones[] = $v;
				}
			}
			$user->setCustomField('about_phones', $about_phones);
		}

		$user->setCustomField('about_urls', ifsetor($user->getExtraField('about_urls'), array()));
		if(isset($_REQ['about_urls']) && is_array($_REQ['about_urls'])) {
			$about_urls = array();
			foreach($_REQ['about_urls'] as $k=>$v) {
				if(Regexp::match_url($v) && count($about_urls)<$__validator->maxUrlCount) {
					$about_urls[] = $v;
				}
			}
			$user->setCustomField('about_urls', $about_urls);
		}

		$user->setCustomField('about_ims', ifsetor($user->getExtraField('about_ims'), array()));
		if(isset($_REQ['im_value_key']) && is_array($_REQ['im_value_key']) && isset($_REQ['im_value_item']) && is_array($_REQ['im_value_item'])) {
			$about_ims = array();
			foreach($_REQ['im_value_item'] as $i=>$v) {
				$v = SafeHtmlModel::input($v);
				if($v!='' && count($about_ims)<$__validator->maxIMCount) {
					foreach($_REQ['im_value_key'] as $j=>$k) {
						if($i==$j && User::IMExists($k)) {
							$about_ims[] = array($k=>$v);
						}
					}
				}
			}
			$user->setCustomField('about_ims', $about_ims);
		}

		if(!Predicate::posted()) {

			if(!isset($_REQ['occupation'])) {
				$user->setCustomField('occupation', $user->getExtraField('occupation'));
			}

			if(isset($_REQ['hide_location'])) {
				$user->setCustomField('hide_location', true);
			} else {
				$user->setCustomField('hide_location', (bool)($user->getField('bitmask') & User::BITMASK_HIDE_LOCATION));
			}

			if(isset($_REQ['hide_birthday'])) {
				$user->setCustomField('hide_birthday', true);
			} else {
				$user->setCustomField('hide_birthday', (bool)($user->getField('bitmask') & User::BITMASK_HIDE_BIRTHDAY));
			}

		}

		_e($user->getCustomFields());
	}

	/**
	 *
	 * @param User $user
	 * @return void
	 */
	private function execRegisterEditCommonPostfix(User $user) {

		$this->include_jscal();
	}

	/**
	 *
	 * @param User $user
	 * @return array
	 */
	private function getRegisterEditCommonAssign(User $user) {

		$assign = array(
			'user' => $user,
			'listDay' => range(1, 31),
			'listMonth' => DateConst::month_r(),
			'listYear' => range(DateConst::getYear()-ValidatorModel::AGE_MIN, DateConst::getYear()-ValidatorModel::AGE_MAX),
			'listCountry' => LocationModel::GetStaticCountryList(),
			'listCity' => LocationModel::GetCityListByCountryId($user->getCustomField('country')),
			'listOccupation' => OccupationModel::GetStaticOccupationList(),
			'listExperience' => OccupationModel::GetStaticExperienceList(),
			'listOccupationExperience' => OccupationModel::GetStaticOccupationExperienceList(),
			'imConstList' => User::getIMs(),
			'MAX_FILE_SIZE_BYTES' => self::USERPIC_MAX_SIZE,
			'MAX_FILE_SIZE_MBYTES' => Cast::byte2megabyte(self::USERPIC_MAX_SIZE).' Mb',
			'Validator' => new ValidatorModel(),
		);

		return $assign;
	}

	/**
	 *
	 * @param unknown_type $user_id
	 */
	public function view($user_id=null) {

		$user_id = ifsetor($user_id, User::getOnlineUserId());

		$user = new User();
		$user->load(array(is_numeric($user_id)?'id':'urlname'=>$user_id));

		if(!$user->exists() || $user->getField('status')!=User::STATUS_OKE && !User::isOnlineAdmin()) {
			Url::redirect(UrlModel::homepage());
		}

		$fullProfileUrl = UrlModel::user($user->getId(), $user);
		$domainlessProfileUrl = _str_replace(UrlModel::homepage(), '', $fullProfileUrl);
		$profileRequestUri = _strpos(rawurldecode($_SERVER['REQUEST_URI']), $domainlessProfileUrl);
		if($profileRequestUri===false) {
			if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) {
				$fullProfileUrl .= '?'.$_SERVER['QUERY_STRING'];
			}
			Url::redirect($fullProfileUrl, null, 0, 301);
		}
		//_e($fullProfileUrl);
		//_e($domainlessProfileUrl);
		//_e($_SERVER);

		//if(is_numeric($user_id) && $user->getField('urlname')) {
		//	Url::redirect(UrlModel::user($user->getId(), $user), null, 0, 301);
		//}

		$user->extendOccupation();
		$user->extendThumb();
		$user->extendAboutInfo();

		_e('# NEW USER PHOTOS');
		$photo_collection_new = new PhotoCollection();
		$photo_collection_new->getCollection(array('status'=>Photo::STATUS_OKE, 'user_id'=>$user->getId()), array(), 'DESC', array(0, 24));

		_e('# GENRE INFO');
		$genreList =& GenreModel::GetStaticGenreList();
		$photo_collection_genre = new PhotoCollection();
		$photo_genre_count = $photo_collection_genre->getCountAggregated(
			'genre_id',
			array('status'=>Photo::STATUS_OKE, 'user_id'=>$user->getId())
		);
		foreach($genreList as $genre_id=>&$genre_data) {
			$genre_data['count'] = ifsetor($photo_genre_count[$genre_id], 0);
		}

		// update photo view statistics
		$user->updateViews();

		$thumb_image = $user->getExtraField('userpic');
		$image_src = ifsetor($thumb_image[UserpicModel::FORMAT_300]['src'], null);
		$this->setHtmlMetaImageSrc($image_src);

		$assign = array(
			'user' => $user,
			'photo_collection_new' => $photo_collection_new,
			'genreList' => $genreList,
			'altProfileUrl' => array(
				'user' => UrlModel::user($user->getId(), $user),
				'userById' => UrlModel::userById($user->getId(), $user),
				'userByIdName' => UrlModel::userByIdName($user->getId(), $user),
				'userByUrlname' => UrlModel::userByUrlname($user->getId(), $user),
			),
		);

		//_e($assign);

		$rss_url = UrlModel::rss_photo(array('uid'=>$user->getId()));
		$rss_name = SeoModel::rss_user_photo($user);
		$this->include_rss(array($rss_url=>$rss_name));

		$rss_url = UrlModel::rss_comment(array('uid'=>$user->getId()));
		$rss_name = SeoModel::rss_user_comment($user);
		$this->include_rss(array($rss_url=>$rss_name));

		$this->include_jscal();

		$this->setHtmlMetaTitle(SeoModel::user($user, SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::user($user, SeoModel::DESCRIPTION));
		$this->setHtmlMetaCanonicalUrl(UrlModel::user($user->getId(), $user));
		$this->setHtmlMetaPropertyContent('og:title', Text::smartHtmlSpecialChars(SeoModel::user($user, SeoModel::TITLE)));
		$this->setHtmlMetaPropertyContent('og:type', 'profile');
		$this->setHtmlMetaPropertyContent('og:url', UrlModel::user($user->getId(), $user));
		$this->setHtmlMetaPropertyContent('og:image', $image_src);
		$this->setHtmlMetaPropertyContent('og:description', Text::smartHtmlSpecialChars(SeoModel::user($user, SeoModel::DESCRIPTION)));
		$this->setHtmlMetaPropertyContent('og:site_name', URL_NAME);

		return $this->layout('user/view.tpl', $assign);
	}

	public function lenta() {

		$__db =& __db();

		$request_query = array();

		// --= status =-- //
		$filter_status = User::STATUS_OKE;
		if(User::isOnlineAdmin() && isset($_GET['status'])) {
			$filter_status = insetor(
				$_GET['status'], array(User::STATUS_OKE, User::STATUS_NEW), User::STATUS_OKE
			);
			$request_query['status'] = $filter_status;
		}

		$filter_online = User::ONLINE_ALL;
		if(isset($_GET['online'])) {
			$filter_online = insetor($_GET['online'], array(User::ONLINE_OFF, User::ONLINE_ON), User::ONLINE_ALL);
			$request_query['online'] = $filter_online;
		}

		// --= country =-- //
		$filter_country = ifsetor($_GET['country'], null);
		$country_id = 0;
		$country_data = array();
		//$country_arr = array();
		$country_arr =& LocationModel::GetStaticCountryList();
		if($filter_country) {
			$country_data = LocationModel::GetOneCountryListByCountryId($filter_country);
			if($country_data) {
				$country_id = $country_data['id'];
				$request_query['country'] = $country_id;
			}
		}

		// --= city =-- //
		$filter_city = ifsetor($_GET['city'], null);
		$city_id = 0;
		$city_data = array();
		$city_arr = array();
		if($country_id) {
			//$city_arr =& LocationModel::GetCityListByCountryId($country_id);
			$city_arr =& LocationModel::GetUserCityListByCountryId($country_id);
			if($filter_city) {
				$city_data = LocationModel::GetOneCityListByCityId($filter_city);
				if($city_data) {
					if($city_data['country_id']==$country_id) {
						$city_id = $city_data['id'];
						$request_query['city'] = $city_id;
					}
					else {
						$city_data = array();
					}
				}
			}
		}

		// --= date =-- //
		$filter_date = ifsetor($_GET['date'], null);
		if($filter_date=='__date__') return $this->page404();
		$filter_tstamp_from = 0;
		$filter_tstamp_to = 0;
		if($filter_date) {
			$filter_date_tstamp_arr = explode('/', $filter_date);
			$filter_tstamp_from = ifsetor($filter_date_tstamp_arr[0], null);
			$filter_tstamp_to = ifsetor($filter_date_tstamp_arr[1], null);
			$filter_tstamp_from = strtotime($filter_tstamp_from);
			$filter_tstamp_to = strtotime($filter_tstamp_to);
			if($filter_tstamp_from && !$filter_tstamp_to) {
				$filter_tstamp_to = $filter_tstamp_from + 24*3600;
			}
			if($filter_tstamp_from>0 && $filter_tstamp_from<$filter_tstamp_to) {
				$request_query['date'] = $filter_date;
			}
			else {
				$filter_tstamp_to = $filter_tstamp_from = 0;
			}
		}

		// --= occupation =-- //
		$occupation_id = 0;
		$occupation_arr =& OccupationModel::GetStaticOccupationList();
		$occupation_data = array();
		$filter_occupation = ifsetor($_GET['occupation'], null);
		if($filter_occupation) {
			$occupation_data = OccupationModel::GetOneOccupationListFilterOccupation($filter_occupation);
			//$occupation_data = OccupationModel::GetOneOccupationListFilterOccupationAlias($filter_occupation);
			if($occupation_data) {
				$occupation_id = $occupation_data['id'];
				$occupation_alias = $occupation_data['name_url'];
				$request_query['occupation'] = $occupation_id;
				//$request_query['occupation'] = $occupation_alias;
			}
		}

		// --= experience =-- //
		$experience_id = 0;
		$experience_arr =& OccupationModel::GetStaticExperienceList();
		$experience_data = array();
		$filter_experience = ifsetor($_GET['experience'], null);
		if($occupation_id && $filter_experience) {
			$experience_data = OccupationModel::GetOneExperienceListFilterExperience($filter_experience);
			//$experience_data = OccupationModel::GetOneExperienceListFilterExperienceAlias($filter_experience);
			if($experience_data) {
				$experience_id = $experience_data['id'];
				$experience_alias = $experience_data['name_url'];
				if(OccupationModel::CheckValidOccupationExperience($occupation_id, $experience_data['id'])) {
					$request_query['experience'] = $experience_id;
					//$request_query['experience'] = $experience_alias;
				}
				else {
					$experience_id = 0;
				}
			}
		}

		// --= search =-- //
		$filter_q = ifsetor($_GET['q'], null);
		if($filter_q) {
			$request_query['q'] = $_GET['q'];
		}

		// --= orderby =-- //
		$orderby = ifsetor($_GET['orderby'], null);
		$orderby = insetor($orderby, array('id', 'rating', 'views', 'upload_tstamp', 'hit_tstamp'), 'id');
		if($orderby) {
			$request_query['orderby'] = $orderby;
		}

		// --= sort direction =-- //
		$ordermethod = ifsetor($_GET['ordermethod'], null);
		$ordermethod = insetor($ordermethod, array('asc', 'desc'), 'desc');
		if($ordermethod) {
			//$ordermethod = 'desc';
			$request_query['ordermethod'] = $ordermethod;
		}

		// --= view mode =-- //
		$viewmode_cookie = ifsetor($_COOKIE['user_viewmode'], null);

		$viewmode = ifsetor($_GET['viewmode'], $viewmode_cookie);
		$viewmode = insetor($viewmode, array('full', 'brief'), 'full');

		if($viewmode) {

			$request_query['viewmode'] = $viewmode;

			if($viewmode_cookie!=$viewmode) {
				$cookie['name'] = 'user_viewmode';
				$cookie['value'] = $viewmode;
				$cookie['domain'] = Cookie::domain();
				$cookie['expires'] = time()+31*24*60*60; // 1 month
				$cookie['path'] = '/';
				Cookie::set($cookie['name'], $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain']);
			}

		}

		// --= paginator =-- //
		$perpare = 20; if($viewmode=='brief') $perpare = $perpare*2;
		$pagenum = ifsetor($_GET[Pager::GET_PARAM_NAME], Pager::__PAGE_ONE);
		$request_query['p'] = $pagenum;
		if(!is_numeric($pagenum)) return $this->page404();

		$where_arr = array();
		$join_arr = array();
		$order_arr = array();
		$group_arr = array();
		$force_arr = array();

		$where_arr[] = sprintf('u.status=%u', $filter_status);

		if($country_id) {
			$where_arr[] = sprintf('u.country=%u', $country_id);
			if($city_id) {
				$where_arr[] = sprintf('u.city=%u', $city_id);
				$force_arr[] = 'location';
			}
			$where_arr[] = sprintf('NOT(u.bitmask&%u)', User::BITMASK_HIDE_LOCATION);
		}
		if($filter_tstamp_from) {
			$where_arr[] = sprintf('u.reg_tstamp>=%u', $filter_tstamp_from);
		}
		if($filter_tstamp_to) {
			$where_arr[] = sprintf('u.reg_tstamp<%u', $filter_tstamp_to);
		}
		if($occupation_id) {
			$join_arr[] = sprintf('JOIN %s AS oe ON u.id=oe.user_id', OccupationModel::db_occupation_experience_data());
			$where_arr[] = sprintf('oe.occupation_id=%u', $occupation_id);
			if($experience_id) {
				$where_arr[] = sprintf('oe.experience_id=%u', $experience_id);
			}
			$rss_url = UrlModel::rss_user(array('occupation'=>$occupation_id));
			$rss_name = sprintf('Новые пользователи по специализации %s', $occupation_data['name']);
			$this->include_rss(array($rss_url=>$rss_name));
		}
		if($filter_q) {
			$filter_q = strip_tags($filter_q);
			if(_strlen($filter_q)>2) {
				$filter_q_sql = MySQL::escape($filter_q);
				$filter_q_sql = _str_replace('%', '\%', $filter_q_sql);
				$where_arr[] = sprintf('u.name LIKE \'%%%1$s%%\'', $filter_q_sql);
			}
			else {
				$where_arr[] = '0';
			}
		}
		if($filter_online!=User::ONLINE_ALL) {
			$join_arr[] = sprintf('LEFT JOIN %s AS uo ON u.id=uo.user_id', UserOnline::db_table());
			switch($filter_online) {
				case User::ONLINE_OFF:
					$where_arr[] = sprintf('uo.id IS NULL OR uo.hit_tstamp<%d', UserOnline::getMinLiveTimestamp());
					break;
				case User::ONLINE_ON:
					$where_arr[] = sprintf('uo.hit_tstamp>%d', UserOnline::getMinLiveTimestamp());
					break;
			}
		}

		if($orderby) {
			$order_arr[] = sprintf('u.%s %s', $orderby, _strtoupper($ordermethod));
		}

		$join_sql  = implode(' ', $join_arr);
		$where_sql = implode(' AND ', $where_arr);
		$order_sql = implode(', ', $order_arr);
		$limit_sql = MySQL::sqlLimit(Pager::getCurrentPageSql($pagenum), $perpare);
		$force_sql = '';
		if($force_arr) {
			$force_arr = array_unique($force_arr);
			$force_sql = 'FORCE KEY('.implode(', ', $force_arr).')';
		}

		$sql = sprintf('
			SELECT u.*
			FROM %1$s AS u %2$s %3$s
			WHERE %4$s
			GROUP BY u.id
			ORDER BY %5$s
			LIMIT %6$s
		',
			User::db_table(),
			$force_sql,
			$join_sql,
			$where_sql,
			$order_sql,
			$limit_sql
		);

		$sql_r = $__db->q($sql, 0, $total_cnt);

		$user_collection = new UserCollection();

		while($row = $sql_r->next()) {
			$user_collection->populateObject($row);
		}
		$user_collection->extendOccupationCollection();

		_e('# GET USER PHOTOS');
		if($viewmode=='full') {
			foreach($user_collection as $user_id=>$user) {
				$photo_collection = new PhotoCollection();
				$photo_collection->getCollection(array('status'=>Photo::STATUS_OKE, 'user_id'=>$user_id), array(), 'DESC', array(0,6));
				$user->setCustomField('photo_collection', $photo_collection);
			}
		}

		$__pager = new Pager($total_cnt, $perpare);

		$occupation_experience_arr =& OccupationModel::GetStaticOccupationExperienceList();

		$assign = array(
			'pager' => $__pager,
			'user_collection' => $user_collection,
			'total_cnt' => $total_cnt,
			'request_query' => $request_query,
			'occupation_arr' => $occupation_arr,
			'occupation_data' => $occupation_data,
			'experience_arr' => $experience_arr,
			'experience_data' => $experience_data,
			'occupation_experience_arr' => $occupation_experience_arr,
			'country_arr' => $country_arr,
			'city_arr' => $city_arr,
			'country_data' => $country_data,
			'city_data' => $city_data,
			'filter_date_from' => $filter_tstamp_from?$filter_tstamp_from:time(),
			'filter_date_to' => $filter_tstamp_to?$filter_tstamp_to:time(),
			'q_what' => 'profile',
		);

		//_e($assign);

		$this->include_jscal();
		$this->include_paginator();

		$this->setHtmlMetaCanonicalUrl(UrlModel::user_lenta($request_query));
		$this->setHtmlMetaNameContent('robots', 'noarchive');

		$seo_params = array(
			'occupation' => $occupation_data,
			'experience' => $experience_data,
			'country' => $country_data,
			'city' => $city_data,
			'time_from' => $filter_tstamp_from,
			'time_to' => $filter_tstamp_to,
		);
		$this->setHtmlMetaTitle(SeoModel::user_lenta($seo_params, SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::user_lenta($seo_params, SeoModel::DESCRIPTION));

		return $this->layout('user/lenta.tpl', $assign);
	}

	/**
	 *
	 * @param unknown_type $authcode
	 * @return UserRegister
	 */
	/*
	private function getUserRegisterByAuthCode($authcode) {
		$user_register = new UserRegister();
		if($authcode) {
			$user_register->loadByAuthCode($authcode);
			$this->HtmlRobotsDisallow = true;
		}
		$user_register->setCustomField('authcode', $authcode);
		return $user_register;
	}
	*/

	/**
	 *
	 * @param FileUploadModel $FileUploadModel
	 * @param User $user
	 * @return array
	 */
	private function upload_userpic(FileUploadModel &$fileUploader, User &$user) {

		$userpic = array(); // userpic data container

		$fileUploader->set_desired_form_maxfilesize(self::USERPIC_MAX_SIZE);

		$html_filelist = 0 ? $fileUploader->get_html_filelist() : array('userpic');

		foreach($html_filelist as $html_filename) {

			while(!is_null($upload_index=$fileUploader->get_next_upload_index($html_filename))) {

				$is_error = $fileUploader->is_error($html_filename, $upload_index);

				if(!$is_error) {
					$fname  = $fileUploader->get_fname($html_filename, $upload_index);
					$fpath  = $fileUploader->get_fpath($html_filename, $upload_index);
					$ftype  = $fileUploader->get_ftype($html_filename, $upload_index);
					$ferror = $fileUploader->get_ferror($html_filename, $upload_index);
					$fsize  = $fileUploader->get_fsize($html_filename, $upload_index);

					if(Im::IsImage($fpath)) {
						$userpic = array(
							'fname' => $fname,
							'fpath' => $fpath,
							'ftype' => $ftype,
							'ferror' => $ferror,
							'fsize' => $fsize,
						);
					}
					else {
						$user->pushError('FILE_NOT_IMAGE', $fname);
					}
				}
				else {
					$ferror_param = $fileUploader->get_ferror_param($html_filename, $upload_index);
					list($m_error_id, $m_error_params) = array($ferror_param['id'], $ferror_param['params']);

					$user->pushError($m_error_id, $m_error_params);
				}
			}
		}

		return $userpic;
	}

}
