<?

function smarty_modifier_share(AtomicObject $object) {

	$result = array();

	if(is_a($object, 'Photo')) {

		$item_id = $object->getId();
		$item_type = 'photo';

		$pattern = '<span rel="social_share"><a href="%1$s" title="%2$s" target="_blank" rel="nofollow"><img src="'.S_URL.'img/clear.gif" class="%3$s" alt="%2$s" width="24" height="24" /></a></span>';

		$result[] = sprintf($pattern,
			UrlModel::social_share('facebook', $item_type, $item_id),
			'Разместить в Facebook', 'share_facebook'
		);

		$result[] = sprintf($pattern,
			UrlModel::social_share('vkontakte', $item_type, $item_id),
			'Разместить в Вконтакте', 'share_vkontakte'
		);

		$result[] = sprintf($pattern,
			UrlModel::social_share('twitter', $item_type, $item_id),
			'Разместить в Twitter', 'share_twitter'
		);

		$result[] = sprintf($pattern,
			UrlModel::social_share('buzz', $item_type, $item_id),
			'Разместить в Google Buzz', 'share_buzz'
		);

		$result = implode("\n&nbsp;", $result);

		$result .= '
		<script type="text/javascript">
			__$("span[rel=social_share] > a").click(function(){
				try {
					var width = 900, height=600;
					var top = Math.abs($(window).height()+100 - height)/2;
					var left = Math.abs($(window).width() - width)/2;
					var specs = $.sprintf("width=%d,height=%d,top=%d,left=%d,menubar=no,resizable=yes,scrollbars=no,status=yes", width, height, top, left);
					window.open($(this).attr("href"), "_blank", specs);
					return false;
				} catch(e) {
					return true;
				}
			});
		</script>';
	}

	return $result;
}