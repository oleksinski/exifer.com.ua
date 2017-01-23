<?
/**
 * Smarty User Gender
 *
 * Parameters:
 *
 *
 * Tag format:
 * {'CONST_BYTE'|constant}
 * {'User'|constant:'STATIC_CONST'}
 */

function smarty_modifier_constant($constant_name, $class_name=null) {

	$constant = null;

	if($constant_name && $class_name) {
		$constant = constant(sprintf('%s::%s', $class_name, $constant_name));
	}
	elseif($constant_name) {
		$constant = constant($constant_name);
	}

	return $constant;
}
