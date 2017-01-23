<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Set checkbox|select option checked|selected
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * @param mixed arg1
 * @param mixed arg2
 * @param string type [option, checkbox] - optional
 * @param string use [array_keys, array_values] - optional
 *
 *
 * Usage examples:
 * {checked_selected arg1=5 arg2=5}
 * {checked_selected arg1=array(1,2,4,5) arg2=5}
 * {checked_selected use='array_keys' arg1=array(1=>0,6=>5) arg2=6}
 * {checked_selected type="option" arg1=5 arg2=5}
 *
 */

function smarty_function_checked_selected($params, &$smarty) {

	$result = null;

	$arg1 = ifsetor($params['arg1'], null);
	$arg2 = ifsetor($params['arg2'], null);
	$type = ifsetor($params['type'], null);
	$use = ifsetor($params['use'], null);

	switch($type) {
		case 'checkbox':
			$pattern = ' checked="true"';
			break;
		case 'option':
			$pattern = ' selected="true"';
			break;
		default:
			$pattern = ' checked="true" selected="true"';
			break;
	}

	$arr_func = insetor($use, array('array_keys', 'array_values'), 'array_values');

	if(is_scalar($arg1) && is_scalar($arg2)) {
		if($arg1 && $arg1==$arg2) {
			$result = $pattern;
		}
	}
	elseif(is_scalar($arg1) && is_array($arg2)) {
		if(in_array($arg1, $arr_func($arg2), false)) {
			$result = $pattern;
		}
	}
	elseif(is_array($arg1) && is_scalar($arg2)) {
		if(in_array($arg2, $arr_func($arg1), false)) {
			$result = $pattern;
		}
	}

	return $result;
}
