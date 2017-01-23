<?

function smarty_modifier_birthday($user) {

	$birthday_txt = '<span class="">скрыто</span>';

	if(is_object($user)) {

		$USER = User::getOnlineUser();

		$is_owner = User::getOnlineUserId()==$user->getId();
		$is_hidden = $user->isBitmaskSet(User::BITMASK_HIDE_BIRTHDAY);
		$is_moderator = $USER->isModerator();

		if($is_owner || !$is_hidden || $is_moderator) {
			$birthday = $user->getField('birthday');
			$birthday_txt = DateConst::getHumandDate($birthday);
			if(($is_owner||$is_moderator) && $is_hidden) {
				$birthday_txt .= '<span class="gray">&nbsp;(скрыто)</span>';
			}
		}
	}

	return $birthday_txt;
}