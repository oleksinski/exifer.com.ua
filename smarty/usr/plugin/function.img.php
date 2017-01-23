<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Draw img
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * @param
 * @param
 */

function smarty_function_img($params, &$smarty) {

	$img_attribute = array();
	$img_attribute['i_src'] = ifsetor($params['src'], null);
	$img_attribute['i_width'] = ifsetor($params['width'], null);
	$img_attribute['i_height'] = ifsetor($params['height'], null);
	$img_attribute['i_border'] = ifsetor($params['border'], 0);
	$img_attribute['i_alt'] = ifsetor($params['alt'], null);
	$img_attribute['i_title'] = ifsetor($params['title'], $img_attribute['i_alt']);
	$img_attribute['i_id'] = ifsetor($params['id'], null);
	$img_attribute['i_class'] = ifsetor($params['class'], null);
	$img_attribute['i_style'] = ifsetor($params['style'], null);
	$img_attribute['i_rel'] = ifsetor($params['rel'], null);
	$img_attribute['i_align'] = ifsetor($params['align'], null);
	$img_attribute['i_hspace'] = ifsetor($params['hspace'], null);
	$img_attribute['i_vspace'] = ifsetor($params['vspace'], null);
	$img_attribute['i_onclick'] = ifsetor($params['onclick'], null);

	extract($img_attribute, EXTR_OVERWRITE);

	$attribute_arr = array();
	if(1 || $i_src) $attribute_arr[] = sprintf('src="%s"', $i_src);
	if($i_width) $attribute_arr[] = sprintf('width="%s"', $i_width);
	if($i_height) $attribute_arr[] = sprintf('height="%s"', $i_height);
	if($i_border) $attribute_arr[] = sprintf('border="%s"', $i_border);
	if($i_alt!=='') $attribute_arr[] = sprintf('alt="%s"', $i_alt);
	if($i_title!=='') $attribute_arr[] = sprintf('title="%s"', $i_title);
	if($i_id) $attribute_arr[] = sprintf('id=%s', $i_id);
	if($i_class) $attribute_arr[] = sprintf('class="%s"', $i_class);
	if($i_style) $attribute_arr[] = sprintf('style="%s"', $i_style);
	if($i_rel) $attribute_arr[] = sprintf('rel="%s"', $i_rel);
	if($i_align) $attribute_arr[] = sprintf('align="%s"', $i_align);
	if($i_hspace) $attribute_arr[] = sprintf('hspace="%s"', $i_hspace);
	if($i_vspace) $attribute_arr[] = sprintf('vspace="%s"', $i_vspace);
	if($i_onclick) $attribute_arr[] = sprintf('onclick="javascript:%s"', $i_onclick);

	$attribute_str = implode(' ', $attribute_arr);

	$img = '<img '.$attribute_str.' />';

	return $img;
}
