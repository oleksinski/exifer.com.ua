<?
/**
 * Smarty User Gender
 *
 * Parameters:
 *
 *
 * Tag format:
 * {$user|gender}
 * {'m'|gender}
 * {'f'|gender}
 */

function smarty_modifier_gender($user) {

	$name = null;

	$gender = is_object($user) ? $user->getField('gender') : $user;
	if($gender==User::GENDER_MALE) {
		$name = 'Мужской';
	}
	elseif($gender==User::GENDER_FEMALE) {
		$name = 'Женский';
	}
	else {
		$name = 'Не указан';
	}

	return $name;
}
