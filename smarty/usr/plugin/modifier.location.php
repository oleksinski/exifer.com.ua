<?

function smarty_modifier_location($user) {

	$location_txt = '<span>скрыто</span>';

	if(is_object($user)) {

		$USER = User::getOnlineUser();

		$is_owner = $USER->getId()===$user->getId();
		$is_hidden = $user->isBitmaskSet(User::BITMASK_HIDE_LOCATION);
		$is_moderator = $USER->isModerator();

		if($is_owner || !$is_hidden || $is_moderator) {

			$country = $user->getField('country');
			$city = $user->getField('city');

			$unknown = 'Неизвестно';

			$location_arr = array();

			$country_arr = LocationModel::GetOneCountryListByCountryId($country);
			$city_arr = LocationModel::GetOneCityListByCityId($city);

			$location_arr[] = $country_arr ? $country_arr['name'] : $unknown;
			$location_arr[] = $city_arr ? $city_arr['name'] : $unknown;

			if($location_arr[0]==$location_arr[1] && $location_arr[0]==$unknown) {
				$location_txt = $unknown;
			}
			else {
				$location_txt = implode(', ', $location_arr);
			}

			if(($is_owner||$is_moderator) && $is_hidden) {
				$location_txt .= '<span class="gray">&nbsp;(скрыто)</span>';
			}

		}
	}

	return $location_txt;
}