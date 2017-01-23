<?

/**
 * Smarty block plugin
 * Realizes gettext translation based on current locale settings
 * Usage: {s}some_text{/s}
 */

function smarty_function_ajaxloader($params, &$smarty) {

	require_once(dirname(__FILE__) . '/function.img.php');

	$img = null;

	if(function_exists('smarty_function_img')) {
		$params['src'] = S_URL.'img/ajaxloader.gif';
		$params['width'] = 16;
		$params['height'] = 11;
		$params['alt'] = 'loading...';
		if(!isset($params['class'])) {
			$params['class'] = 'vmid';
		}
		$img = smarty_function_img(&$params, $smarty);
	}

	return $img;
}