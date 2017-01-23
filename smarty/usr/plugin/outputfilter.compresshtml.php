<?
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Smarty compresshtml outputfilter plugin
 *
 * File:     outputfilter.compresshtml.php
 * Type:     outputfilter
 * Name:     compresshtml
 * Date:     Jan 20, 2009
 *
 * Compress output html source by removind tabs, spaces, comments etc.
 *
 * @version  1.1
 * @param string
 * @param Smarty
 */
function smarty_outputfilter_compresshtml($html, &$smarty) {

	//$stopwatch = new StopWatch();

	$html = CodeCompressor::compressHtml($html, false);

	//_e(sprintf('CompressHtml Filter: %s', $stopwatch->getFormat(5)));

	return $html;
}
