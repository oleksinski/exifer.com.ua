<?

/**
 * {$user|online}
 * {$user|online:false}
 */

function smarty_modifier_online($user, $forceshow=true) {

	$online_status = null;

	if(is_object($user)) {

		$user_id = $user->getId();

		static $online_status_cache = array();

		static $user_collection_online = null;
		if(is_null($user_collection_online)) {
			$user_collection_online = new UserOnlineCollection();
			$user_collection_online->getCollectionLive();
		}

		if(array_key_exists($user_id, $online_status_cache)) {
			if($forceshow || _strstr($online_status_cache[$user_id], 'status_offline')===false) {
				$online_status = $online_status_cache[$user_id];
			}
		}
		elseif($user_id) {

			$tstamp_min = UserOnline::getMinLiveTimestamp();
			$tstamp_away = $tstamp_min+round(UserOnline::LIVE_ONLINE_TIME/2);
			$away_min = round((time()-$tstamp_away)/60);

			$gender_was = $user->isFemale() ? 'Была' : 'Был';

			$status_pattern = '<img src="'.S_URL.'img/clear.gif" width="8" height="8" class="%s" title="%s" />';
			$status_online = sprintf($status_pattern, 'status_online', 'Сейчас на сайте');
			$status_offline = sprintf($status_pattern, 'status_offline', 'Нет на сайте');
			$status_away = sprintf($status_pattern, 'status_away', ($gender_was.' на сайте менее '.$away_min.' минут назад'));

			foreach($user_collection_online as $user_online_id=>$user_online) {
				if($user_online->getField('user_id')==$user_id) {
					$tstamp_hit = $user_online->getField('hit_tstamp');
					if($tstamp_hit>$tstamp_min) {
						if($tstamp_hit<$tstamp_away) {
							$online_status = $status_away;
						}
						else {
							$online_status = $status_online;
						}
					}
					break;
				}
			}
			if(!$online_status && $forceshow) {
				$online_status = $status_offline;
			}

			if($user->isBirthday()) {
				$online_status .= '<img src="'.S_URL.'img/icon/birthday.gif" alt="Именинник" title="Именинник" width="14" height="14" border="0" />';
			}

			$online_status_cache[$user_id] = $online_status;
		}
	}

	return $online_status;
}
