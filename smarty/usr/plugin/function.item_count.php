<?

/**
 * {item_count item=views var=$photo type=photo icon=true icon_scheme=white detailed=true}
 * {item_count item=votes var=$photo type=photo icon=true icon_scheme=white detailed=true}
 * {item_count item=comments var=$photo type=photo icon=true icon_scheme=white detailed=true}
 * {item_count item=photos var=$photo type=photo icon=true icon_scheme=white detailed=true}
 * {item_count item=rate var=$photo type=photo icon=true icon_scheme=white detailed=true}
 *
 * @param unknown_type $params
 * @param unknown_type $smarty
 */
function smarty_function_item_count($params, &$smarty) {

	//@result
	$result = '';

	// @params
	$item = ifsetor($params['item'], null);
	$var = ifsetor($params['var'], null);
	$detailed = ifsetor($params['detailed'], true);
	$icon = ifsetor($params['icon'], true);
	$icon_scheme = insetor(ifsetor($params['icon_scheme'], null), array('white', 'black'), 'black');

	if(is_object($var)) {

		// @vars
		$item_icon_name = '%s_%s';
		$item_icon = '<img src="'.S_URL.'img/icon/%1$s.gif" width="11" height="11" border="0" alt="%2$s" title="%2$s" />&nbsp;';
		$item_name = null;

		switch($item) {

			case 'views':

				$views = $var->getField('views');
				$views_guest = $var->getField('views_guest');
				$views_user = $var->getField('views_user');

				if($detailed) {
					$result = sprintf('<span title="Просмотров: всего(гостями|пользователями)">%u <span class="gray">(%u|%u)</span></span>', $views, $views_guest, $views_user);
				}
				else {
					$result = $views;
				}

				$item_name = 'Просмотров';

				break;

			case 'votes':

				$item_id = $var->getId();
				$item_type = ifsetor($params['type'], 'photo');
				$votes = $var->getField('votes');
				$votes_pros = $var->getField('votes_pros');
				$votes_cons = $var->getField('votes_cons');
				$votes_zero = $var->getField('votes_zero');
				$votes_value = Vote::getSignedValue($var->getField('votes_value'));

				$result = $votes_value;

				if($detailed) {

					$html_cont = '&nbsp;<span class="gray">&raquo;</span>&nbsp;<span title="Оценок: всего(за|0|против)">&#8721;%s<span class="gray">(%s|%s|%s)</span></span>';

					// vc-photo-1-pros
					$html_value = sprintf('<span rel="vc-%s-%u-%s">%s</span>', $item_type, $item_id, 'value', $votes_value);
					$html_total = sprintf('<span rel="vc-%s-%u-%s">%u</span>', $item_type, $item_id, 'total', $votes);
					$html_pros = sprintf('<span rel="vc-%s-%u-%s">%u</span>', $item_type, $item_id, 'pros', $votes_pros);
					$html_cons = sprintf('<span rel="vc-%s-%u-%s">%u</span>', $item_type, $item_id, 'cons', $votes_cons);
					$html_zero = sprintf('<span rel="vc-%s-%u-%s">%u</span>', $item_type, $item_id, 'zero', $votes_zero);

					$result .= sprintf($html_cont, $html_total, $html_pros, $html_zero, $html_cons);
				}

				$item_name = 'Оценка';

				break;

			case 'comments':

				$item_id = $var->getId();
				$item_type = ifsetor($params['type'], 'photo');

				// cc-photo-1
				$comments = $var->getField('comments');
				$result = sprintf('<span rel="cc-%s-%u">%s</span>', $item_type, $item_id, $comments);

				$item_name = 'Комментариев';

				break;

			case 'photos':

				$result = $var->getField('photos');

				$item_name = 'Фотографий';

				break;

			case 'rate':

				$item_name = 'Рейтинг';

				break;

			case 'orient':

				$width = $var->getField('orig_width');
				$height = $var->getField('orig_height');

				if($width>0 && $height>0) {
					$orient = null;
					$koef = $width/$height;
					if($koef>1.1) {
						$orient = 'horizontal';
						$item_name = 'горизонтальное фото';
					}
					elseif($koef<0.9) {
						$orient = 'vertical';
						$item_name = 'вертикальное фото';
					}
					else {
						$orient = 'square';
						$item_name = 'квадратное фото';
					}
					$item_icon_name .= '_'.$orient;
				}

				break;
		}

		if($icon && $item_name) {
			$item_icon_name = sprintf($item_icon_name, $item, $icon_scheme);
			$result = sprintf($item_icon, $item_icon_name, $item_name) . $result;
		}
	}

	return $result;
}
