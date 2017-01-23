<?
/**
 * Smarty Pager
 *
 * @package Smarty
 * @subpackage plugins
 * @version 1.0
 * @filesource
 *
 * Parameters:
 * (object) var - pager object
 * (int) width - pager container width [px]
 * (string) onclick - function name to execute. function will recieve selected page number
 * (string) class - extra pager class
 * (string) style - manual pager styles
 *
 *
 * Tag format:
 *
 * {pager var=$__pager class="pager_cool" style="width:1000px"}
 *
 * default tpl - TPL_PATH.'smarty/pager.tpl'
 */


function smarty_function_pager($params, &$smarty) {

	$__pager = ifsetor($params['var'], null);

	if(!is_a($__pager, 'Pager')) {
		return false;
	}
	else {
		$__pager = clone $__pager;
	}

	$pager = array();

	$pager['class'] = ifsetor($params['class'], '');
	$pager['style'] = ifsetor($params['style'], '');
	$pager['onclick'] = ifsetor($params['onclick'], '');

	$pager['currentPage'] = $__pager->getCurrentPage();
	$pager['getParamName'] = $__pager->getGetParamName();
	$pager['urlParamPrefix'] = $__pager->getUrlParamPrefix();
	$pager['pageCount'] = $__pager->getPageCount();
	$pager['onPage'] = $__pager->getOnPage();
	$pager['totalCount'] = $__pager->getTotalCount();
	$pager['pageUrl'] = $__pager->getPageUrl();
	$pager['pageUri'] = $__pager->getPageUri();
	//$pager['pagePatternUrl'] = $__pager->buildPatternUrl();
	$pager['pagePatternUrl'] = $__pager->buildPatternQuery();
	$pager['pageUrlQuery'] = $__pager->getPageUrlQuery();
	$pager['containerId'] = $__pager->getContainerId();
	$pager['uniqId'] = $__pager->getUniqId();

	// for non javascript pager //
	$half = floor(($pager['onPage']-1)/2);
	$start = $pager['currentPage'] - $half;
	$finish = $pager['currentPage'] + $half;

	if($finish>$pager['pageCount']) {
		$start -= $half;
	}
	if($start<Pager::__PAGE_ONE) {
		$start = Pager::__PAGE_ONE;
	}
	$pager['noscript'] = array();
	for($i=0; $i<$pager['onPage']; $i++) {
		$page = $start+$i;
		if($page<=$pager['pageCount']) {
			$pager['noscript'][$page] = $__pager->buildLink($page);
		}
	}
	$pager['noscript_count'] = count($pager['noscript']);
	if($pager['noscript_count']) {
		$pager['td_width_percent'] = floor(100/$pager['noscript_count']);
	}
	//_e($pager);
	// END for non javascript pager //

	$smarty->assign('__pager__', $pager);

	$fetched = $smarty->fetch(SMARTY_USR_TPL_PATH.'pager.tpl');

	//$smarty->clear_assign('__pager__');

	return $fetched;
}
