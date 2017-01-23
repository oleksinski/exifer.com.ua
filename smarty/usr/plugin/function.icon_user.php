<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * {icon_user u_data=$u_data}
 */

function smarty_function_icom_user($params, &$smarty) {

	$html_output = null;
	$icon_set = array();

	$icon_pattern = '<img src="'.S_URL.'img/clear.gif" width="15" height="15" class="%s" title="%s" />';

	$user = ifsetor($params['u_data'], array());

	if(is_object($user)) {

		if($user->getField('status')==User::STATUS_NEW) {
			$icon_set[] = sprintf($icon_pattern, 'ico_u_status_new', 'статус: неактивно');
		}
		elseif($user->getField('status')==User::STATUS_OKE) {
			$icon_set[] = sprintf($icon_pattern, 'ico_u_status_oke', 'статус: активно');
		}

		$html_output = implode('<span class="">&nbsp;</span>', $icon_set);
	}

	return $html_output;
}
