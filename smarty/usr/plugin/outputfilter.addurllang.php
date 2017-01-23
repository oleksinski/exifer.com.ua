<?
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Smarty addurllang outputfilter plugin
 *
 * File:     outputfilter.addurllang.php
 * Type:     outputfilter
 * Name:     addurlcountryalias
 * Date:     Jan 20, 2009
 * Purpose:  Add current country alias (eg 'ua', 'ru', 'by') to the very first url alias
 *           to all links found inside a template
 *           Examples: Lang Alias = by;
 *           Tpl url: http://site.net/show/1/ -> http://site.net/ru/show/1/
 * Install:  Drop into the plugin directory, call
 *           $smarty->load_filter('output','addurllang');
 *           from application.
 * @version  1.1
 * @param string
 * @param Smarty
 */
function smarty_outputfilter_addurllang($html, &$smarty) {

	//$stopwatch = new StopWatch();

	$__locale =& __locale();
	$html = $__locale->renderHtml($html);

	//_e(sprintf('Smarty Output Url Lang Filter: %s, %s', $__locale->getLang(), $stopwatch->getFormat(5)));

	return $html;
}
