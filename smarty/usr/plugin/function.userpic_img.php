<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Draw userpic img
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * @param
 * @param
 */

function smarty_function_userpic_img($params, &$smarty) {

	require_once(dirname(__FILE__) . '/function.img.php');

	$img = null;

	if(function_exists('smarty_function_img')) {

		//$id = 0;
		$user = ifsetor($params['var'], null);
		$format = ifsetor($params['u_format'], null);

		if(is_object($user)) {
			//$id =  $user->getId();
			$thumb = $user->getExtraField('userpic');
			$params += ifsetor($thumb[$format], array());
		}

		//if($id && $format) {
		//	UserpicModel::CreateFormatIfNotExists($id, $format);
		//}

		$img = smarty_function_img(&$params, $smarty);
	}

	return $img;
}
