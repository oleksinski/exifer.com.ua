<?

function smarty_modifier_comment_rich(Comment $comment, $url_cutlength=null) {
	$result = $comment->getField('text');
	if(!$result) {
		$result = '<span class="gray">Комментарий удален</span>';
	}
	else {

		if($comment->isImageCoordEnabled()) {
			$coords = $comment->getExtraField('coords');
			foreach($coords as $i=>$coord) {
				$result = _str_replace($coord, ' '.getEncodedCoordPaid($i, $coord).' ', $result);
			}
		}
		$result = SafeHtmlModel::output_urlify($result, $url_cutlength);
		if($comment->isImageCoordEnabled()) {
			$coords = $comment->getExtraField('coords');
			foreach($coords as $i=>$coord) {
				$html = sprintf(
					'<a href="%s" title="%s" rel="c_crop" class="mrl_tiny mrr_tiny"><img src="%simg/crop.gif" width="16" height="16" class="vmid" border="0" /></a>',
					$comment->getCoordUrl($coord),
					$coord,
					S_URL
				);
				$result = _str_replace(getEncodedCoordPaid($i, $coord), $html, $result);
			}
		}
	}
	return $result;
}

function getEncodedCoordPaid($i, $coord) {
	return sprintf('#coord:%s#', $i);
}