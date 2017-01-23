<?

require_once(dirname(__FILE__).'/header.exec.php');

$i=0;

while(true) {
	$photo_collection = new PhotoCollection();
	$photo_collection->getCollection(array(), array(), 'ASC', array($i++, 100));
	if(!$photo_collection->length()) {
		break;
	}
	foreach($photo_collection as $photo_id=>$photo) {
		$s_filepath = ThumbModel::GetThumbLocalPath($photo_id, ThumbModel::THUMBNAIL_ORIGINAL);
		ThumbModel::CreateThumb($photo_id, ThumbModel::THUMB_75, $s_filepath);
		//ThumbModel::Create($photo_id, $source_filepath);
	}
}
