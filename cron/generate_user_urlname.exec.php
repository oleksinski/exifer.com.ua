<?

/**
 * CLI params:
 *
 * 1) php __FILE__ -u 10
 *    Recalc info for given user
 *
 * 2) php __FILE__
 *    Recalc all users info
 *
 */

require_once(dirname(__FILE__).'/header.exec.php');

$args = getopt('u:');

if(isset($args['u'])) {
	$user = new User($args['u']);
	$user->setField('urlname', $user->getUniqUrlName($user->getField('urlname')));
	$user->update();
}
else {


	$i=0;

	while(true) {
		$user_collection = new UserCollection();
		$user_collection->getCollection(array(), array(), 'ASC', array($i++, 100));
		if(!$user_collection->length()) {
			break;
		}
		foreach($user_collection as $user_id=>$user) {
			$user->setField('urlname', $user->getUniqUrlName($user->getField('urlname')));
			$user->update();
		}
	}
}