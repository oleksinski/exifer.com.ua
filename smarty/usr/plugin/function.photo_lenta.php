<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Table with photos
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * @param string $column [bigcol, bigcol_half]
 * @param integer $photo_per_row [1,2,3,4,5]
 * @param AtomicObjectCollection $collection
 * @param bool fit_width
 * @param bool fit_height
 * @param int $thumb_format [optional]
 */

function smarty_function_photo_lenta($params, &$smarty) {

	$fetched = null;

	$column = ifsetor($params['column'], 'bigcol_half');
	$photo_per_row = ifsetor($params['photo_per_row'], 2);
	$photo_collection = ifsetor($params['collection'], array());

	$photo_width = ifsetor($params['width'], '');
	$photo_height = ifsetor($params['height'], '');

	$fit_width = isset($params['fit_width']);
	$fit_height = isset($params['fit_height']);

	$td_class = ifsetor($params['td_class'], null);
	$td_style = ifsetor($params['td_style'], null);

	$photo_cur_id = ifsetor($params['photo_cur_id'], 0);

	$photo_info = ifsetor($params['p_info'], false);

	$highlight = ifsetor($params['highlight'], null);

	$thumb_format = null;

	if(isset($params['p_format'])) {
		$thumbFormatList = ThumbModel::GetFormatValueList();
		if(in_array($params['p_format'], $thumbFormatList)) {
			$thumb_format = $params['p_format'];
		}
	}

	if(!$thumb_format) {
		$thumb_format = ThumbModel::THUMB_150;
	}

	if(0 && !$thumb_format) {

		$page_map = array(
			1 => ThumbModel::THUMBNAIL_ORIGINAL,
			//3 => ThumbModel::THUMB_300,
			3 => ThumbModel::THUMB_301,
			4 => ThumbModel::THUMB_240,
			5 => ThumbModel::THUMB_150,
		);

		$bigcol_map = array(
			1 => ThumbModel::THUMB_300,
			2 => ThumbModel::THUMB_300,
			3 => ThumbModel::THUMB_150,
			4 => ThumbModel::THUMB_150,
		);

		$bigcol_half_map = $smallcol_map = array(
			1 => ThumbModel::THUMB_300,
			2 => ThumbModel::THUMB_150,
		);
		$current_map = array();

		switch($column) {
			case 'bigcol':
				$current_map =& $bigcol_map;
				break;
			case 'page':
				$current_map =& $page_map;
				break;
			case 'bigcol_half':
				$current_map =& $bigcol_half_map;
				break;
			case 'smallcol':
				$current_map =& $smallcol_map;
				break;
		}

		$thumb_format = ifsetor($current_map[$photo_per_row], ThumbModel::THUMB_150);
	}

	if($thumb_format==ThumbModel::THUMB_150 && $column=='bigcol_half') {
		//if(!$photo_width) $photo_width = '90%';
		//if(!$photo_height) $photo_height = '90%';
	}

	foreach($photo_collection as $photo_id=>$photo) {

		$photo_thumb = $photo->getExtraField('thumb');

		if(is_array($photo_thumb)) {

			foreach($photo_thumb as $t_format=>&$t_data) {

				$orig_width =& $t_data['width'];
				$orig_height =& $t_data['height'];

				if($orig_width && $orig_height) {
					if($photo_width) {
						$koef = $orig_width/$photo_width;
						$orig_width = $photo_width;
						$orig_height = round($orig_height/$koef);
					}
					if($photo_height) {
						$koef = $orig_height/$photo_height;
						$orig_height = $photo_height;
						$orig_width = round($orig_width/$koef);
					}
				}
				else {
					if($photo_width && _strstr($photo_width,'%')!==false) {
						$orig_width = $photo_width;
					}
					if($photo_height && _strstr($photo_height,'%')!==false) {
						$orig_height = $photo_height;
					}
				}

				if($fit_width || $fit_height) {
					$orig_width = $orig_height = '';
					if($fit_width) {
						$orig_width = '100%';
					}
					if($fit_height) {
						$orig_height = '100%';
					}
				}
			}
			$photo->setExtraField('thumb', $photo_thumb);
		}
	}

	$td_class = _rtrim(implode(' ', array_merge(array('vmid', 'hcenter'), explode(' ', $td_class))));

	$assign = array(
		'__column__' => $column,
		'__photo_per_row__' => $photo_per_row,
		'__photo_collection__' => $photo_collection,
		'__photo_collection_length__' => $photo_collection->length(),
		'__photo_cur_id__' => $photo_cur_id,
		'__thumb_format__' => $thumb_format,
		'__td_class__' => $td_class,
		'__td_style__' => $td_style,
		'__photo_info__' => $photo_info,
		'__highlight__' => $highlight,
	);

	$smarty->assign($assign);

	$fetched = $smarty->fetch(SMARTY_USR_TPL_PATH.'photo_lenta.tpl');

	//foreach($assign as $var=>$value) {
	//	$smarty->clear_assign($var);
	//}

	return $fetched;
}
