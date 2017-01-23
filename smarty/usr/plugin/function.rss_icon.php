<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
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

function smarty_function_rss_icon($params, &$smarty) {

	$result = null;

	$attributes = array();

	$attributes[] = sprintf('src="%s"', S_URL . 'img/icon/rss_x16.gif');
	$attributes[] = sprintf('width="%s"', ifsetor($params['width'], 16));
	$attributes[] = sprintf('height="%s"', ifsetor($params['height'], 16));
	$attributes[] = sprintf('class="%s"', ifsetor($params['class'], 'vmid'));
	if(isset($params['alt'])) $attributes[] = sprintf('alt="%1$s" title="%1$s"', $params['alt']);
	if(isset($params['style'])) $attributes[] = sprintf('style="%s"', $params['style']);

	$result = sprintf('<img %s />', implode(' ', $attributes));

	return $result;
}
