<?
/**
 * Smarty Instant Messanger Service Name
 *
 * Parameters:
 *
 *
 * Tag format:
 * {1|im}
 */

function smarty_modifier_im($im) {

	$map = array(
		User::IM_ICQ => 'ICQ',
		User::IM_SKYPE => 'Skype',
		User::IM_GTALK => 'Google Talk',
		User::IM_YAHOO => 'Yahoo',
		User::IM_IRC => 'IRC',
		User::IM_MSN => 'MSN',
		User::IM_AIM => 'AIM',
		User::IM_JABBER => 'Jabber',
	);

	$name = ifsetor($map[$im], 'Unknown');

	return $name;
}
