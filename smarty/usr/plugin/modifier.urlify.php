<?

function smarty_modifier_urlify($string, $url_cutlength=null) {
	return SafeHtmlModel::output_urlify($string, $url_cutlength);
}

