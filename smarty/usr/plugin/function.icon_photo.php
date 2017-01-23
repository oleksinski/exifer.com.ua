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
 * {icon_photo p_data=$p_data}
 *
 */

function smarty_function_icon_photo($params, &$smarty) {

	$html_output = null;
	$icon_set = array();

	$icon_pattern = '<img src="'.S_URL.'img/clear.gif" width="15" height="15" class="%s" title="%s" />';

	$photo = ifsetor($params['p_data'], null);

	if(is_object($photo)) {

		if($photo->getField('status')==Photo::STATUS_OKE) {
			$icon_set[] = sprintf($icon_pattern, 'ico_p_status_oke', 'статус: активно');
		}
		elseif($photo->getField('status')==Photo::STATUS_LOCK) {
			$icon_set[] = sprintf($icon_pattern, 'ico_p_status_lock', 'статус: заблокировано');
		}

		if($photo->getField('moderated')==Photo::MODERATED_ON) {
			$icon_set[] = sprintf($icon_pattern, 'ico_p_mod_on', 'просмотрено модератором');
		}
		elseif($photo->getField('moderated')==Photo::MODERATED_OFF) {
			$icon_set[] = sprintf($icon_pattern, 'ico_p_mod_off', 'не просмотрено модератором');
		}

		$html_output = implode('<span class="">&nbsp;</span>', $icon_set);
	}

	return $html_output;
}
