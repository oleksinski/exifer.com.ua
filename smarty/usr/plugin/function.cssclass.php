<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Return CSS Class for given target
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * @param string target
 * @param array data
 *
 */

function smarty_function_cssclass($params, &$smarty) {

	$cssclass = null;

	$target = ifsetor($params['target'], null);

	switch($target) {

		case 'city':

			$city = ifsetor($params['data'], array());

			if($city) {

				$is_capital = ifsetor($city['is_capital'], 0);
				$is_main = ifsetor($city['is_main'], 0);

				if($is_capital) {
					$cssclass = 'bold underline';
				}
				elseif($is_main) {
					$cssclass = 'bold';
				}
			}
			break;

		default:
			break;
	}

	return $cssclass;
}
