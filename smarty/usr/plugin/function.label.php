<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Draw html label element
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * @param string $for
 * @param string $data
 */

function smarty_function_label($params, &$smarty) {

	$result = null;

	$for = ifsetor($params['for'], null);
	$data = ifsetor($params['data'], null);
	$class = ifsetor($params['class'], null);

	$result = sprintf('<label for="%s" class="%s">%s</label>', $for, $class, $data);

	return $result;
}
