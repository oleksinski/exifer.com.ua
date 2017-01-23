<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Draw photo img
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

function smarty_function_photo_img($params, &$smarty) {

	require_once(dirname(__FILE__) . '/function.img.php');

	$img = null;

	if(function_exists('smarty_function_img')) {

		//$id = 0;
		$photo = ifsetor($params['var'], null);
		$format = ifsetor($params['p_format'], null);

		if(is_object($photo)) {
			//$id =  $photo->getId();
			$thumb = $photo->getExtraField('thumb');
			$params += ifsetor($thumb[$format], array());
		}

		//if($id && $format) {
		//	ThumbModel::CreateThumbIfNotExists($id, $format);
		//}

		$img = smarty_function_img(&$params, $smarty);
	}

	return $img;
}
