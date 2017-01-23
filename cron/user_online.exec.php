<?

require_once(dirname(__FILE__).'/header.exec.php');

$user_online_collection = new UserOnlineCollection();

$user_online_collection->getCollectionLive();
foreach($user_online_collection as $user_online_id=>$user_online) {
	$user_online->synchronize();
}

$user_online_collection->getCollectionExpired();
foreach($user_online_collection as $user_online_id=>$user_online) {
	$user_online->removeById();
}
