<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Show template message - boxinfo
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * {boxinfo content='' type=1}
 * {boxinfo content='' type=3 width='50%' scheme='green'}
 */

function smarty_function_boxinfo($params, &$smarty) {

	$content = ifsetor($params['content'], null);
	$type = ifsetor($params['type'], 1);

	switch($type) {
		case 1:
		case 3:
			//$width_default = '400px';
			$width_default = '100%';
			break;
		case 2:
		default:
			$width_default = '100%';
			break;
	}

	$min_height_default = null;

	//$width = ifsetor($params['width'], $width_default);
	$width = array_key_exists('width', $params) ? $params['width'] : $width_default;
	if(is_numeric($width)) $width .= 'px';

	$min_height = array_key_exists('min_height', $params) ? $params['min_height'] : $min_height_default;
	if(is_numeric($min_height)) $min_height .= 'px';

	/**
	 * red
	 * green
	 * magenta
	 * yellow
	 */
	$scheme = 'b_scheme_'.ifsetor($params['scheme'], 'red');

	$class = array($scheme);

	if($c = ifsetor($params['class'], false)) {
		$class[] = $c;
	}

	$assign = array(
		'__content__' => $content,
		'__type__' => $type,
		'__width__' => $width,
		'__min_height__' => $min_height,
		'__class__' => implode(' ', $class),
	);

	foreach($assign as $var=>$value) {
		$smarty->assign($var, $value);
	}

	$fetched = $smarty->fetch(SMARTY_USR_TPL_PATH.'boxinfo.tpl');

	//foreach($assign as $var=>$value) {
	//	$smarty->clear_assign($var);
	//}

	return $fetched;
}
