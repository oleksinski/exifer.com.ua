<?

class RestControl extends ControlModel {

	public function captcha() {

		$CaptchaModel = new CaptchaModel();
		$CaptchaModel->createCaptchaTypeOne();
		exit();
	}

	public function location() {

		$result_json_arr = array();

		$target_arr = array('country_states', 'country_cities', 'state_cities');

		$target = ifsetor($_REQUEST['target'], null);

		$id = Cast::unsignint(ifsetor($_REQUEST['id'], null));

		if(in_array($target, $target_arr) && $id) {

			$status = Cast::int(ifsetor($_REQUEST['status'], 1));

			switch($target) {

				case 'country_states':

					$state_arr =& LocationModel::GetStateListByCountryId($id, $status, LocationModel::CO_MIX);

					foreach($state_arr as $s_id=>$s_data) {
						$result_json_arr[$s_id] = array(
							'n' => $s_data['name'],
						);
					}

					break;

				case 'country_cities':

					$city_arr =& LocationModel::GetCityListByCountryId($id, $status, LocationModel::CO_MIX);

					foreach($city_arr as $ci_id=>$ci_data) {
						$result_json_arr[$ci_id] = array(
							'n' => $ci_data['name'],
							'm' => $ci_data['is_main'],
							'c' => $ci_data['is_capital'],
						);
					}

					break;

				case 'state_cities':

					$city_arr =& LocationModel::GetCityListByStateId($id, $status, LocationModel::CO_MIX);

					foreach($city_arr as $ci_id=>$ci_data) {
						$result_json_arr[$ci_id] = array(
							'n' => $ci_data['name'],
							'm' => $ci_data['is_main'],
							'c' => $ci_data['is_capital'],
						);
					}

					break;
			}
		}

		$json_response = JsonModel::encode($result_json_arr);

		return $json_response;
	}

	public function user_upload_date() {

		$result = array();

		$user_id = Cast::unsignint(ifsetor($_REQUEST['uid'], null));
		$date = ifsetor($_REQUEST['date'], null);

		$tstamp = strtotime($date);

		if($user_id && $tstamp) {

			$month = DateConst::getMonth($tstamp);
			$year = DateConst::getYear($tstamp);

			$tstamp_start = DateConst::mk_time(1, $month, $year);
			$tstamp_end = $tstamp_start + 31*24*60*60;

			$__db =& __db();

			$where_arr = array();
			$where_arr[] = sprintf('status=%u', Photo::STATUS_OKE);
			$where_arr[] = sprintf('user_id=%u', $user_id);
			$where_arr[] = sprintf('add_tstamp>=%u', $tstamp_start);
			$where_arr[] = sprintf('add_tstamp<unix_timestamp(date_add(from_unixtime(%u), interval 1 month))', $tstamp_start);

			$where_sql = implode(' AND ', $where_arr);

			$sql = sprintf('
				SELECT COUNT(*) AS cnt, DATE(from_unixtime(add_tstamp)) AS add_dstamp
				FROM %1$s
				WHERE %2$s
				GROUP BY add_dstamp
			', Photo::db_table(), $where_sql);

			$sql_r = $__db->q($sql);

			while($row=$sql_r->next()) {
				$jscal_date_format = date('Ymd', strtotime($row['add_dstamp']));
				$result[$jscal_date_format] = $row['cnt'];
			}
		}

		$json_response = JsonModel::encode($result);

		return $json_response;
	}

	public function search() {

		$_REQ = $_GET;

		$targetDefault = 'photo';
		$targetList = array('photo', 'profile', 'comment');

		$target = ifsetor($_REQ['q_what'], $targetDefault);
		$target = insetor($target, $targetList, $targetDefault);

		if(isset($_REQ['q_what'])) {
			unset($_REQ['q_what']);
		}
		if(isset($_REQ['q_submit'])) {
			unset($_REQ['q_submit']);
		}

		if(isset($_REQ['q']) && _strlen($_REQ['q'])>2) {

			$__db =& __db();

			$insert_arr = array(
				'pattern' => $_REQ['q'],
				'target' => array_search($target, $targetList)+1,
				'last_uid' => User::getOnlineUserId(),
				'last_ip' => Network::clientIp(),
				'last_fwrd' => Network::clientFwrd(),
				'last_tstamp' => time(),
			);
			$insert_raw_arr = array(
				'times' => 'times+1',
			);

			$update_arr = array(
				'last_uid' => User::getOnlineUserId(),
				'last_ip' => Network::clientIp(),
				'last_fwrd' => Network::clientFwrd(),
				'last_tstamp' => time(),
			);
			$update_raw_arr = array(
				'times' => 'times+1',
			);

			$insert_sql = MySQL::prepare_fields($insert_arr, $insert_raw_arr);
			$update_sql = MySQL::prepare_fields($update_arr, $update_raw_arr);

			$sql = sprintf('INSERT INTO exifer.search SET %s ON DUPLICATE KEY UPDATE %s', $insert_sql, $update_sql);

			$__db->u($sql);
		}

		switch($target) {
			case 'photo':
				$url = UrlModel::photo_lenta($_REQ);
				break;
			case 'profile':
				$url = UrlModel::user_lenta($_REQ);
				break;
			case 'comment':
				$url = UrlModel::comment_lenta($_REQ);
				break;
			default:
				$url = UrlModel::homepage();
		}

		Url::redirect($url);
	}

	public function sitemap() {

		$sitemapIndex = new SitemapIndexModel();

		$xml = $sitemapIndex->generate();

		if(!headers_sent()) {
			header('Content-type: text/xml');
		}

		return $xml;
	}

	public function share() {

		$share_url = null;
		$item_url = null;
		$item_title = null;
		$item_image = null;

		$serviceAlias = ifsetor($_GET['service'], null);
		$serviceId = SocialShare::getShareServiceIdByAlias($serviceAlias);

		$itemTypeAlias = ifsetor($_GET['item_type'], null);
		$itemTypeId = SocialShare::getShareItemIdByAlias($itemTypeAlias);

		$itemId = ifsetor($_GET['item_id'], 0);

		if($serviceId) {
			switch($itemTypeId) {
				case SocialShare::ITEM_PHOTO:
					$itemObject = new Photo($itemId);
					if($itemObject->exists()) {
						$item_url = UrlModel::photo($itemObject->getId(), $itemObject);
						if(!Predicate::server_pro()) {
							$item_url = _str_replace('.dev', '.com.ua', $item_url);
						}
						$item_title = SeoModel::photo($itemObject, SeoModel::TITLE);
						$item_title = SeoModel::htmlToRawText($item_title);
					}
					break;
			}
		}

		if($item_url) {

			switch($serviceId) {

				case SocialShare::SERVICE_FACEBOOK:

					$http_params = array();
					if($item_url) {
						$http_params['u'] = $item_url;
					}
					if($item_title) {
						//$http_params['t'] = $item_title;
					}
					$http_query = http_build_query($http_params);
					$share_url = sprintf('http://www.facebook.com/sharer.php?%s', $http_query);

					break;

				case SocialShare::SERVICE_VKONTAKTE:

					$http_params = array();
					if($item_url) {
						$http_params['url'] = $item_url;
					}
					if($item_title) {
						//$http_params['title'] = $item_title;
					}
					if($item_image) {
						$http_params['image'] = $item_image;
						$http_params['noparse'] = 'true';
					}
					$http_query = http_build_query($http_params);
					$share_url = sprintf('http://vkontakte.ru/share.php?%s', $http_query);

					break;

				case SocialShare::SERVICE_TWITTER:

					$status_buffer = array();
					if($item_title) {
						$status_buffer[] = $item_title;
					}
					if($item_url) {
						$status_buffer[] = $item_url;
					}

					$status = implode(' ', $status_buffer);

					$http_params['status'] = $status;

					$http_query = http_build_query($http_params);
					$share_url = sprintf('http://twitter.com/home?%s', $http_query);

					break;

				case SocialShare::SERVICE_BUZZ:

					$http_params = array();
					if($item_url) {
						$http_params['url'] = $item_url;
					}
					if($item_title) {
						//$http_params['message'] = $item_title;
					}
					if($item_image) {
						//$http_params['imageurl'] = $item_image;
					}
					$http_query = http_build_query($http_params);
					$share_url = sprintf('http://www.google.com/buzz/post?%s', $http_query);

					break;
			}
		}

		if($share_url) {

			$share = new SocialShare();
			$share->setUpdateOnDuplicate(true);
			$share->setField('service_id', $serviceId);
			$share->setField('item_type', $itemTypeId);
			$share->setField('item_id', $itemId);
			$share->setField('clicks', 'clicks+1', true);
			$share->setField('last_ip', Network::clientIp());
			$share->setField('last_uid', User::getOnlineUserId());
			$share->setField('last_fwrd', Network::clientFwrd());
			$share->setField('last_tstamp', time());
			$share->save();
		}
		else {
			$share_url = UrlModel::homepage();
		}

		Url::redirect($share_url);
	}

	/**
	 * Get base64encoded grayscaled image
	 */
	public function photo_grayscale() {

		$photo_id = ifsetor($_GET['id'], 0);
		$format = ifsetor($_GET['format'], ThumbModel::THUMBNAIL_ORIGINAL);

		$result = array('id'=>$photo_id, 'image'=>null, 'error'=>null);

		$thumb = ThumbModel::GetThumbLocalPath($photo_id, $format);

		if(file_exists($thumb)) {
			$im = @imagecreatefromjpeg($thumb);
			imagefilter($im, IMG_FILTER_GRAYSCALE);
			imagefilter($im, IMG_FILTER_CONTRAST, -3);
			ob_start();imagejpeg($im);$imageData = ob_get_clean();
			imagedestroy($im);
			$base64image = sprintf('data:image/jpeg;base64,%s', base64_encode($imageData));
			$result['image'] = $base64image;
		}

		$json_response = JsonModel::encode($result);

		return $json_response;
	}
}
