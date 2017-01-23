<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Table with photos
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * @param integer $user_per_row [1,2,3,4,5]
 * @param array $user_collection
 *
 */

function smarty_function_user_lenta($params, &$smarty) {

	$fetched = null;

	$user_per_row = ifsetor($params['user_per_row'], 1);
	$user_collection = ifsetor($params['collection'], array());
	$userpic_width = ifsetor($params['userpic_width'], null);
	$userpic_height = ifsetor($params['userpic_height'], null);
	$hr_separate = ifsetor($params['hr_separate'], 12);
	$highlight = ifsetor($params['highlight'], null);

	$assign = array(
		'__user_per_row__' => $user_per_row,
		'__user_collection__' => $user_collection,
		'__user_collection_length__' => $user_collection->length(),
		'__userpic_width__' => $userpic_width,
		'__userpic_height__' => $userpic_height,
		'__hr_separate__' => $hr_separate,
		'__highlight__' => $highlight,
	);

	foreach($assign as $var=>$value) {
		$smarty->assign($var, $value);
	}

	$fetched = $smarty->fetch(SMARTY_USR_TPL_PATH.'user_lenta.tpl');

	//foreach($assign as $var=>$value) {
	//	$smarty->clear_assign($var);
	//}

	return $fetched;
}
