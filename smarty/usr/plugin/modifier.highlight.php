<?

function smarty_modifier_highlight($string, $needle, $css_class='highlight') {
	return Text::highlighter(array($needle), $string, $css_class);
}