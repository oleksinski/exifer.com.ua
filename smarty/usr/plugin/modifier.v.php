<?php
/**
 * Smarty Versioner
 *
 * @copyright Copyright 2009
 * @package Smarty
 * @subpackage plugins
 * @version 1.0
 * @filesource
 *
 * Parameters:
 * (string) filepath - the path to the file like '/js/hp/common.js' (only for static files!)
 *
 * Tag format:
 *
 * <link type="text/css" title="screen style" rel="stylesheet" href="{'http://site.net/file.css'|v}" />
 *
 * Add file modify-timestamp as get param to enable cache reloading at any css|js changes
 */

function smarty_modifier_v($filepath) {

	$versioner =& Versioner::getInstance();
	$filepath = $versioner->get($filepath);

	return $filepath;
}
