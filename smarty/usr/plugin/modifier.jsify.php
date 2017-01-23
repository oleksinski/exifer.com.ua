<?

function smarty_modifier_jsify($string, $step=5) {
	return Text::escapeJS($string, $step);
}

