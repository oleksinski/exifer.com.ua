<?

/**
 * CLI params:
 *
 * 1) php __FILE__ -p 10
 *    Recalc info for given photo
 *
 * 2) php __FILE__
 *    Recalc all photos info
 *
 */

require_once(dirname(__FILE__).'/header.exec.php');

$args = getopt('p:');

if(isset($args['p'])) {
	$photo = new Photo($args['p']);
	$photo->recalcInfo();
}
else {

	$i=0;

	while(true) {
		$photo_collection = new PhotoCollection();
		$photo_collection->getCollection(array(), array(), 'ASC', array($i++, 100));
		if(!$photo_collection->length()) {
			break;
		}
		foreach($photo_collection as $photo_id=>$photo) {
			$photo->recalcInfo();
		}
	}
}