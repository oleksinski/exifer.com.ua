<?

class AdminControl extends ControlModel {

	/**
	 * Declare admin page access levels
	 */
	private function getAccessPageMap() {
		$map = array(
			'admin_index' => User::BITMASK_ADMIN_MODER,
			'admin_dbg_ctrl' => User::BITMASK_ADMIN_ROOT,
			'admin_dbg_cron' => User::BITMASK_ADMIN_ROOT,
			'admin_user' => User::BITMASK_ADMIN_ROOT,
			'admin_photo' => User::BITMASK_ADMIN_ROOT,
			'admin_location' => User::BITMASK_ADMIN_ROOT,
			'admin_stat' => User::BITMASK_ADMIN_ROOT,
		);
		return $map;
	}

	/**
	 * @override
	 * @see ControlModel::prefix()
	 */
	protected function prefix() {

		require_auth();

		$this->HtmlRobotsDisallow = true;

		$this->assign('ACCESS_PAGE_MAP', $this->getAccessPageMap());
	}

	/**
	 * @override
	 * @see ControlModel::postfix()
	 */
	protected function postfix() {}

	/**
	 *
	 * @param unknown_type $bit
	 * @param unknown_type $basic_auth_credentials
	 */
	private function check_admin($bit=User::BITMASK_ADMIN_ROOT, $basic_auth_credentials=array()) {

		$grant_access = User::getOnlineUser()->isAdmin($bit);

		if(!$grant_access && $basic_auth_credentials) {
			$grant_access = Util::basic_auth($basic_auth_credentials);
		}

		if(!$grant_access) {
			UrlModel::redirect(UrlModel::homepage());
		}

		return $grant_access;
	}

	/**
	 *
	 * @param unknown_type $page
	 */
	private function check_access($page, $basic_auth_credentials=array()) {
		$accessPageMap = $this->getAccessPageMap();
		$bit = ifsetor($accessPageMap[$page], User::BITMASK_ADMIN_ROOT);
		$this->check_admin($bit, $basic_auth_credentials);
	}

	// ---

	/**
	 *
	 */
	public function index() {
		$this->check_access('admin_index');
		$assign = array();
		return $this->layout('admin/index.tpl', $assign);
	}

	public function dbg_ctrl() {

		$this->check_access('admin_dbg_ctrl');

		$__debug =& __debug();

		$dbg_info = array(
			'on' => $__debug->isMessagingEnabled(),
			'off' => !$__debug->isMessagingEnabled(),
		);

		$dbg_realtime = array(
			'on' => $__debug->isRealTimeOutput(),
			'off' => !$__debug->isRealTimeOutput(),
		);

		$compress_info = array(
			'on' => self::is_compress_html(),
			'off' => !self::is_compress_html(),
		);

		if(Predicate::posted()) {

			$cookie = array(
				'domain' => Cookie::domain(),
				'path' => '/',
			);

			if(isset($_POST['submit_debug'])) {

				$choice = ifsetor($_REQUEST['debug'], 'off');

				$cookie['name'] = Debug::MSG_COOKIE;
				$cookie['value'] = $__debug->getDebugCookie();
				$cookie['expires'] = ($choice=='on') ? 0 : (time()-1);
			}
			elseif(isset($_POST['submit_debug_realtime'])) {

				$choice = ifsetor($_REQUEST['debug_realtime'], 'off');

				$cookie['name'] = Debug::MSG_REALTIME_COOKIE;
				$cookie['value'] = $__debug->getDebugRealtimeCookie();
				$cookie['expires'] = ($choice=='on') ? 0 : (time()-1);
			}
			elseif(isset($_POST['submit_compress'])) {

				$choice = ifsetor($_REQUEST['compress'], 'on');

				$cookie['name'] = self::get_compress_info(ControlModel::COMPRESS_COOKIE_NAME);
				$cookie['value'] = self::get_compress_info(ControlModel::COMPRESS_COOKIE_VALUE);
				$cookie['expires'] = ($choice=='off') ? 0 : (time()-1);
			}

			Cookie::set($cookie['name'], $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain']);

			Url::redirect(UrlModel::admin_dbg_ctrl());
		}
		$assign = array(
			'dbg_info' => $dbg_info,
			'dbg_realtime' => $dbg_realtime,
			'compress_info' => $compress_info,
		);

		return $this->layout('admin/dbg_ctrl.tpl', $assign);
	}

	public function dbg_cron() {

		$this->check_access('admin_dbg_cron');

		$cron_file_list = FileFunc::readDirFiles(CRON_PATH);

		$map_func = Util::CreateFunction('$a', 'return basename($a);');
		$cron_file_list = array_map($map_func, $cron_file_list);

		$cron_file = null;

		$cron_param = null;

		if(Predicate::posted()) {

			self::set_time_limit_user_abort(5*60);

			$cron_file = ifsetor($_POST['cron_file'], null);

			$cron_param = ifsetor($_POST['cron_param'], null);

			$result = false;

			if(in_array($cron_file, $cron_file_list)) {
				//$result = ShellCmd::Execute(
				//	sprintf('%s %s %s', ShellCmdModel::GetCmdPath('PHP', true), ShellCmd::EscapePath(CRON_PATH.$cron_file), $cron_param)
				//);
				include_once(CRON_PATH.$cron_file);
			}
		}

		$assign = array(
			'cron_file_list' => $cron_file_list,
			'cron_file' => $cron_file,
			'cron_param' => $cron_param,
		);

		return $this->layout('admin/dbg_cron.tpl', $assign);
	}

	public function user() {

		$this->check_access('admin_user');

		$user = new User();

		$id = ifsetor($_REQUEST['id'], null);
		$email = ifsetor($_REQUEST['email'], null);

		if(isset($_REQUEST['id'])) {
			$user->load(array('id'=>$_REQUEST['id']));
		}
		elseif(isset($_REQUEST['email'])) {
			$user->load(array('email'=>$_REQUEST['email']));
		}
		elseif(isset($_REQUEST['urlname'])) {
			$user->load(array('urlname'=>$_REQUEST['urlname']));
		}

		//_e($_POST);

		if($user->exists()) {

			if(isset($_POST['ban'])) {

				$ban_value = $_POST['ban'];

				$ban_map = array(
					'ban_hour' => time()+1*3600,
					'ban_day' => time()+1*24*3600,
					'ban_week' => time()+7*24*3600,
					'ban_month' => time()+30*24*3600,
					'unban' => time(),
				);

				if(array_key_exists($ban_value, $ban_map)) {
					$user->setField('ban_tstamp', $ban_map[$ban_value]);
					$user->update();
					// remove user session (for all browsers)
					if($ban_value!='unban') {
						$user->logoutGlobal();
					}
					Url::redirect(UrlModel::admin_user($user->getId()));
				}
			}
			elseif(isset($_POST['delete'])) {
				$id = $user->getId();
				$delete_result = $user->removeById(isset($_POST['spamer']));
				Url::redirect(UrlModel::admin_user($id));
			}
			elseif(isset($_POST['admin'])) {
				$user_bitmask = $user->getField('bitmask');
				switch($_POST['admin']) {
					case 'user':
						Cast::unsetbit(&$user_bitmask, User::BITMASK_HIDE_ONLINE);
						Cast::unsetbit(&$user_bitmask, User::BITMASK_ADMIN_MODER);
						Cast::unsetbit(&$user_bitmask, User::BITMASK_ADMIN_ROOT);
						break;
					case 'moderator':
						Cast::unsetbit(&$user_bitmask, User::BITMASK_HIDE_ONLINE);
						Cast::unsetbit(&$user_bitmask, User::BITMASK_ADMIN_ROOT);
						Cast::setbit(&$user_bitmask, User::BITMASK_ADMIN_MODER);
						break;
					case 'admin':
						Cast::setbit(&$user_bitmask, User::BITMASK_ADMIN_ROOT);
						Cast::setbit(&$user_bitmask, User::BITMASK_HIDE_ONLINE);
						break;
				}
				$user->setField('bitmask', $user_bitmask);
				$user->update();
				Url::redirect(UrlModel::admin_user($user->getId()));
			}
			elseif(isset($_POST['recalc'])) {
				$user->recalcInfo();
			}
		}

		$assign = array(
			'user' => $user,
		);

		return $this->layout('admin/user.tpl', $assign);
	}

	public function photo() {

		$this->check_access('admin_photo');

		$photo = new Photo();

		$id = ifsetor($_REQUEST['id'], null);

		if($id) {
			$photo->load(array('id'=>$id));
			$photo->extendThumb();
			//$photo->getUserObject();
		}

		//_e($_POST);

		if($photo->exists()) {
			if(isset($_POST['recalc'])) {
				$photo->recalcInfo();
			}
		}

		$assign = array(
			'photo' => $photo,
		);

		return $this->layout('admin/photo.tpl', $assign);
	}

	/**
	 * admin/location/
	 * admin/location/country/{ID}?id=0
	*  admin/location/city/{ID}?id=0
	 */
	public function location() {

		$this->check_access('admin_location');

		$__error = new ErrorModel();

		$__db =& __db();

		$country_id = ifsetor($_REQUEST['country_id'], LocationModel::NULL_COUNTRY);
		$city_id = ifsetor($_REQUEST['city_id'], LocationModel::NULL_CITY);
		$state_id = ifsetor($_REQUEST['state_id'], LocationModel::NULL_STATE);

		$country_arr = array();
		$country_active_arr = array();
		$country_inactive_arr = array();

		$city_arr = array();
		$city_active_arr = array();
		$city_inactive_arr = array();

		$state_arr = array();
		$state_active_arr = array();
		$state_inactive_arr = array();

		$country_data = array();
		$city_data = array();
		$state_data = array();

		$target = ifsetor($_GET['target'], null);

		$save_static = isset($_GET['save_static']);
		$gen_url_name = isset($_GET['gen_url_name']);

		switch($target) {

			case 'country':

				$country_id = ifsetor($_GET['country_id'], $country_id);

				if($gen_url_name) {
					LocationModel::FillCountryUrlName();
					LocationModel::GenerateCountryStatic();
					UrlModel::redirect(UrlModel::admin_location());
				}
				if($save_static) {
					LocationModel::GenerateCountryStatic();
					UrlModel::redirect(UrlModel::admin_location());
				}

				break;

			case 'city':

				$city_id = ifsetor($_GET['city_id'], $city_id);

				if($gen_url_name) {
					LocationModel::FillCityUrlName();
					LocationModel::GenerateCityStatic();
					UrlModel::redirect(UrlModel::admin_location());
				}
				if($save_static) {
					LocationModel::GenerateCityStatic();
					UrlModel::redirect(UrlModel::admin_location());
				}

				$city_data = LocationModel::GetOneCityListByCityId($city_id, LocationModel::CI_MIX, LocationModel::CO_MIX);

				$country_id = ifsetor($city_data['country_id'], $country_id);

				$state_id = ifsetor($city_data['state_id'], $state_id);

				$state_data = LocationModel::GetOneStateListByStateId($state_id, LocationModel::S_MIX, LocationModel::CO_MIX);

				break;

			case 'state':

				$state_id = ifsetor($_GET['state_id'], $state_id);

				if($gen_url_name) {
					LocationModel::FillStateUrlName();
					LocationModel::GenerateStateStatic();
					UrlModel::redirect(UrlModel::admin_location());
				}
				if($save_static) {
					LocationModel::GenerateStateStatic();
					UrlModel::redirect(UrlModel::admin_location());
				}

				$state_data = LocationModel::GetOneStateListByStateId($state_id, LocationModel::S_MIX, LocationModel::CO_MIX);

				$country_id = ifsetor($state_data['country_id'], $country_id);

				break;
		}

		// all countries
		$country_arr =& LocationModel::GetCustomCountryList();

		// all active countries
		$country_active_arr =& LocationModel::GetStaticCountryList();

		if($country_id) {

			// get country data
			$country_data = LocationModel::GetOneCountryListByCountryId($country_id, LocationModel::CO_MIX);

			if($country_data) {

				// all country cities
				$city_arr =& LocationModel::GetCityListByCountryId($country_id, LocationModel::CI_MIX, LocationModel::CO_MIX);

				// active country cities
				$city_active_arr =& LocationModel::GetCityListByCountryId($country_id, LocationModel::CI_ACTIVE, LocationModel::CO_MIX);

				// all country states
				$state_arr =& LocationModel::GetStateListByCountryId($country_id, LocationModel::S_MIX, LocationModel::CO_MIX);

				// active country states
				$state_active_arr =& LocationModel::GetStateListByCountryId($country_id, LocationModel::S_ACTIVE, LocationModel::CO_MIX);
			}
		}


		if(Predicate::posted()) {

			self::set_time_limit_user_abort(5*60);

			_e($_POST);

			if($target=='country') {

				#Array
				#(
				#    [id] => 135
				#    [name_ru] => Ukraine
				#    [name_ua] => Ukraine
				#    [name_en] => Ukraine
				#    [name_url] => ukraine
				#    [capital_id] => 1
				#    [active] => 1
				#    [country_del] => 1
				#)

				$insert_arr = array(
					'id' => Cast::unsignint(ifsetor($_POST['id'], LocationModel::NULL_COUNTRY)),
					'name_ru' => ifsetor($_POST['name_ru'], null),
					'name_ua' => ifsetor($_POST['name_ua'], null),
					'name_en' => ifsetor($_POST['name_en'], null),
					//'name_url' => ifsetor($_POST['name_url'], null),
					'capital_id' => ifsetor($_POST['capital_id'], null, true),
					'active' => isset($_POST['active']) ? LocationModel::CO_ACTIVE : LocationModel::CO_INACTIVE,
				);

				$is_country_del = isset($_POST['country_del']);

				if(!$is_country_del) {
					if(!$insert_arr['name_ru'] || !$insert_arr['name_ua'] || !$insert_arr['name_en']) {
						$__error->push('OBLIG_FIELDSET_EMPTY');
					}
				}

				if($__error->isOk()) {

					$is_new_insert = $insert_arr['id']==LocationModel::NULL_COUNTRY && !$is_country_del;

					if($is_new_insert) {

						unset($insert_arr['id']);

						$insert_arr = Util::cast_dbtable_values($insert_arr, LocationModel::db_country());

						if($insert_arr) {

							// insert new country
							$country_id = LocationModel::InsertCountry($insert_arr);

							// generate country name url
							LocationModel::FillOneCountryUrlName($country_id, $insert_arr);

							// refresh country static
							LocationModel::GenerateCountryStatic();
						}

						Url::redirect(UrlModel::admin_location_country($country_id));
					}
					elseif($is_country_del) {

						$country_id = $insert_arr['id'];

						if($country_id==LocationModel::DEF_COUNTRY) {
							$__error->push('DEFAULT_COUNTRY_DEL', array($insert_arr['name_ru']));
						}
						else {
							// delete country
							$sql = sprintf('DELETE FROM %1$s WHERE id=%2$u', LocationModel::db_country(), $country_id);
							$__db->u($sql);

							// delete country city list
							$sql = sprintf('DELETE FROM %1$s WHERE country_id=%2$u', LocationModel::db_city(), $country_id);
							$__db->u($sql);

							// delete country state list
							$sql = sprintf('DELETE FROM %1$s WHERE country_id=%2$u', LocationModel::db_state(), $country_id);
							$__db->u($sql);

							// recalc user country-city profile
							$sql = sprintf('
								SELECT id FROM %1$s
								WHERE status=%2$u AND country=%3$u AND login_tstamp>%4$u
								', User::db_table(),
								User::STATUS_OKE, $country_id, UserOnline::getMinLiveTimestamp()
							);
							$sql_r = $__db->q($sql);
							while($row=$sql_r->next()) {
								$user_session = new UserSession($row['id']);
								$user_session->remove();
							}

							$sql = sprintf('UPDATE %1$s SET country=%2$u, city=%3$u WHERE country=%4$u',
								User::db_table(), LocationModel::DEF_COUNTRY, LocationModel::DEF_CITY, $country_id
							);
							$__db->u($sql);

							// refresh country static
							LocationModel::GenerateCountryStatic();

							// refresh city static
							LocationModel::GenerateCityStatic();

							// refresh state static
							LocationModel::GenerateStateStatic();

							Url::redirect(UrlModel::admin_location());
						}
					}
					else {

						$update_arr = $insert_arr;

						$country_id = $update_arr['id'];

						unset($update_arr['id']);

						$update_arr = Util::cast_dbtable_values($update_arr, LocationModel::db_country());

						if($update_arr) {

							// update country record
							LocationModel::UpdateCountry($country_id, $update_arr);

							// generate country name url
							LocationModel::FillOneCountryUrlName($country_id, $update_arr);

							// refresh country static
							LocationModel::GenerateCountryStatic();
						}

						Url::redirect(UrlModel::admin_location_country($country_id));
					}

				}
			}
			elseif($target=='city') {

				#Array
				#(
				#    [id] => 8958
				#    [country_id] => 1
				#    [state_id] => 2
				#    [name_ru] => Kiev
				#    [name_ua] => Kiev
				#    [name_en] => Kiev
				#    [name_url] => kiev
				#    [is_capital] => 1
				#    [is_main] => 1
				#    [active] => 1
				#)

				$insert_arr = array(
					'id' => Cast::unsignint(ifsetor($_POST['id'], LocationModel::NULL_CITY)),
					'country_id' => Cast::unsignint(ifsetor($_POST['country_id'], LocationModel::NULL_COUNTRY)),
					'state_id' => Cast::unsignint(ifsetor($_POST['state_id'], LocationModel::NULL_STATE)),
					'name_ru' => ifsetor($_POST['name_ru'], null),
					'name_ua' => ifsetor($_POST['name_ua'], null),
					'name_en' => ifsetor($_POST['name_en'], null),
					//'name_url' => ifsetor($_POST['name_url'], null),
					'is_capital' => Cast::int(isset($_POST['is_capital'])),
					'is_main' => Cast::int(isset($_POST['is_main']) || isset($_POST['is_capital'])),
					'active' => isset($_POST['active']) ? LocationModel::CI_ACTIVE : LocationModel::CI_INACTIVE,
				);

				$is_city_del = isset($_POST['city_del']);

				if(!$is_city_del) {
					if(!$insert_arr['name_ru'] || !$insert_arr['name_ua'] || !$insert_arr['name_en']) {
						$__error->push('OBLIG_FIELDSET_EMPTY');
					}
					elseif(!array_key_exists($insert_arr['country_id'], $country_arr)) {
						$__error->push('OBLIG_FIELDSET_EMPTY');
					}
					elseif(!array_key_exists($insert_arr['state_id'], $state_arr)) {
						$__error->push('OBLIG_FIELDSET_EMPTY');
					}
				}

				if($__error->isOk()) {

					$is_new_insert = $insert_arr['id']==LocationModel::NULL_CITY && !$is_city_del;

					if($is_new_insert) {

						unset($insert_arr['id']);

						$insert_arr = Util::cast_dbtable_values($insert_arr, LocationModel::db_city());

						if($insert_arr) {

							// insert new city
							$city_id = LocationModel::InsertCity($insert_arr);

							// generate city name url
							LocationModel::FillOneCityUrlName($city_id, $insert_arr);

							// refresh city static
							LocationModel::GenerateCityStatic();
						}

						Url::redirect(UrlModel::admin_location_city($city_id));
					}
					elseif($is_city_del) {

						$city_id = $insert_arr['id'];

						if($city_id==LocationModel::DEF_CITY) {
							$__error->push('DEFAULT_CITY_DEL', array($insert_arr['name_ru']));
						}
						else {

							$city_arr = LocationModel::GetOneCityListByCityId($city_id, LocationModel::CI_MIX, LocationModel::CO_MIX);

							$country_id = ifsetor($city_arr['country_id'], LocationModel::DEF_COUNTRY);

							// delete city
							$sql = sprintf('DELETE FROM %1$s WHERE id=%2$u', LocationModel::db_city(), $city_id);
							$__db->u($sql);

							// recalc user country-city profile
							$sql = sprintf('
								SELECT id FROM %1$s
								WHERE status=%2$u AND city=%3$u AND login_tstamp>%4$u
								', User::db_table(),
								User::STATUS_OKE, $city_id, UserOnline::getMinLiveTimestamp()
							);
							$sql_r = $__db->q($sql);
							while($row=$sql_r->next()) {
								$user_session = new UserSession($row['id']);
								$user_session->remove();
							}

							$sql = sprintf('UPDATE %1$s SET country=%2$u, city=%3$u WHERE city=%4$u',
								User::db_table(), LocationModel::DEF_COUNTRY, LocationModel::DEF_CITY, $city_id
							);
							$__db->u($sql);

							// refresh city static
							LocationModel::GenerateCityStatic();

							Url::redirect(UrlModel::admin_location_country($country_id));
						}
					}
					else {

						$update_arr = $insert_arr;

						$city_id = $update_arr['id'];

						unset($update_arr['id']);

						$update_arr = Util::cast_dbtable_values($update_arr, LocationModel::db_city());

						if($update_arr) {

							// update city record
							LocationModel::UpdateCity($city_id, $update_arr);

							// generate city name url
							LocationModel::FillOneCityUrlName($city_id, $update_arr);

							// refresh city static
							LocationModel::GenerateCityStatic();
						}

						Url::redirect(UrlModel::admin_location_city($city_id));
					}
				}
			}
			elseif($target=='state') {

				#Array
				#(
				#    [id] => 8958
				#    [country_id] => 1
				#    [capital_id] => 2
				#    [name_ru] => Kiev obl
				#    [name_ua] => Kiev obl
				#    [name_en] => Kiev obl
				#    [name_url] => kiev_obl
				#    [active] => 1
				#)

				$insert_arr = array(
					'id' => Cast::unsignint(ifsetor($_POST['id'], LocationModel::NULL_CITY)),
					'country_id' => Cast::unsignint(ifsetor($_POST['country_id'], LocationModel::NULL_COUNTRY)),
					'capital_id' => Cast::unsignint(ifsetor($_POST['capital_id'], LocationModel::NULL_CITY)),
					'name_ru' => ifsetor($_POST['name_ru'], null),
					'name_ua' => ifsetor($_POST['name_ua'], null),
					'name_en' => ifsetor($_POST['name_en'], null),
					//'name_url' => ifsetor($_POST['name_url'], null),
					'active' => isset($_POST['active']) ? LocationModel::S_ACTIVE : LocationModel::S_INACTIVE,
				);

				$is_state_del = isset($_POST['state_del']);

				if(!$is_state_del) {
					if(!$insert_arr['name_ru'] || !$insert_arr['name_ua'] || !$insert_arr['name_en']) {
						$__error->push('OBLIG_FIELDSET_EMPTY');
					}
					elseif(!array_key_exists($insert_arr['country_id'], $country_arr)) {
						$__error->push('OBLIG_FIELDSET_EMPTY');
					}
				}

				if($__error->isOk()) {

					$is_new_insert = $insert_arr['id']==LocationModel::NULL_STATE && !$is_state_del;

					if($is_new_insert) {

						unset($insert_arr['id']);

						$insert_arr = Util::cast_dbtable_values($insert_arr, LocationModel::db_state());

						if($insert_arr) {

							// insert new state
							$state_id = LocationModel::InsertState($insert_arr);

							// generate state name url
							LocationModel::FillOneStateUrlName($state_id, $insert_arr);

							// refresh state static
							LocationModel::GenerateStateStatic();
						}

						Url::redirect(UrlModel::admin_location_state($state_id));
					}
					elseif($is_state_del) {

						$state_id = $insert_arr['id'];

						if($state_id==LocationModel::DEF_STATE) {
							$__error->push('DEFAULT_STATE_DEL', array($insert_arr['name_ru']));
						}
						else {

							$state_arr = LocationModel::GetOneStateListByStateId($state_id, LocationModel::S_MIX, LocationModel::CO_MIX);

							$country_id = ifsetor($state_arr['country_id'], LocationModel::DEF_COUNTRY);

							$city_arr =& LocationModel::GetCityListByStateId($state_id, LocationModel::CI_MIX, LocationModel::CO_MIX);
							$city_id_arr = array_keys($city_arr);

							// delete state cities
							$sql = sprintf('DELETE FROM %1$s WHERE state_id=%2$u', LocationModel::db_city(), $state_id);
							$__db->u($sql);

							// delete state
							$sql = sprintf('DELETE FROM %1$s WHERE id=%2$u', LocationModel::db_state(), $state_id);
							$__db->u($sql);

							// recalc user country-city profile
							if($city_id_arr) {

								$sql = sprintf('
									SELECT id FROM %1$s
									WHERE status=%2$u AND country=%3$u AND %4$u AND login_tstamp>%5$u
									', User::db_table(),
									User::STATUS_OKE, $country_id, MySQL::sqlInClause('city', $city_id_arr),
									UserOnline::getMinLiveTimestamp()
								);
								$sql_r = $__db->q($sql);
								while($row=$sql_r->next()) {
									$user_session = new UserSession($row['id']);
									$user_session->remove();
								}

								// get country cities
								$city_arr =& LocationModel::GetCityListByCountryId($country_id);

								// order by is_main
								$city_arr =& LocationModel::LocaleOrderField($city_arr, 'is_main');

								$city_data = reset($city_arr);
								$city_id = ifsetor($city_data['id'], Location::DEF_CITY);

								if(!$sity_id) {
									$country_id = LocationModel::DEF_COUNTRY;
									$city_id = LocationModel::DEF_CITY;
								}

								$sql = sprintf('UPDATE %1$s SET city=%2$u WHERE country_id=%3$u AND %4$s',
									User::db_table(), $city_id, $country_id, MySQL::sqlInClause('city', $city_id_arr)
								);
								$__db->u($sql);
							}

							// refresh state static
							LocationModel::GenerateStateStatic();

							// refresh city static
							LocationModel::GenerateCityStatic();

							Url::redirect(UrlModel::admin_location_country($country_id));
						}
					}
					else {

						$update_arr = $insert_arr;

						$state_id = $update_arr['id'];

						unset($update_arr['id']);

						$update_arr = Util::cast_dbtable_values($update_arr, LocationModel::db_state());

						if($update_arr) {

							// update state record
							LocationModel::UpdateState($state_id, $update_arr);

							// generate state name url
							LocationModel::FillOneStateUrlName($state_id, $update_arr);

							// refresh city static
							LocationModel::GenerateStateStatic();
						}

						Url::redirect(UrlModel::admin_location_state($state_id));
					}
				}
			}
		}

		$assign = array(
			'country_arr' => $country_arr,
			'city_arr' => $city_arr,
			'state_arr' => $state_arr,

			'country_active_arr' => $country_active_arr,
			'city_active_arr' => $city_active_arr,
			'state_active_arr' => $state_active_arr,

			'country_inactive_arr' => $country_inactive_arr,
			'city_inactive_arr' => $city_inactive_arr,
			'state_inactive_arr' => $state_inactive_arr,

			'country_data' => $country_data,
			'city_data' => $city_data,
			'state_data' => $state_data,

			'mode_add_country' => $target=='country' && $country_id==LocationModel::NULL_COUNTRY,
			'mode_add_city' => $target=='city' && $city_id==LocationModel::NULL_CITY,
			'mode_add_state' => $target=='state' && $state_id==LocationModel::NULL_STATE,

			'Error' => $__error,
		);

		return $this->layout('admin/location.tpl', $assign);
	}

}