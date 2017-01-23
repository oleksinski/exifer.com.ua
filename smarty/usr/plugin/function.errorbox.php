<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Draw html error box
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 */

function smarty_function_errorbox($params, &$smarty) {

	$result = null;

	$error_container = '<ul>%s</ul>';
	$error_iteration = '';

	$error = ifsetor($params['error'], null);
	if(is_object($error)) {
		$errors = $error->getErrors();
		foreach($errors as $key=>$val) {
			$error_iteration .= sprintf('<li class="red" id="%s"> - %s</li>', $key, $val);
		}
	}

	$result = sprintf($error_container, $error_iteration);

	require_once(dirname(__FILE__).'/function.boxinfo.php');

	$result = smarty_function_boxinfo(array('content'=>$result, 'type'=>3, 'width'=>ifsetor($params['width'], '100%')), $smarty);

	return $result;
}
