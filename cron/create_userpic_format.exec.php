<?

require_once(dirname(__FILE__).'/header.exec.php');

$i=0;

while(true) {
	$user_collection = new UserCollection();
	$user_collection->getCollection(array(), array(), 'ASC', array($i++, 100));
	if(!$user_collection->length()) {
		break;
	}
	foreach($user_collection as $user_id=>$user) {
		$source_filepath = UserpicModel::GetUserpicLocalPath($user_id, UserpicModel::FORMAT_300);
		if(1) { // create separate userpic format
			UserpicModel::CreateFormat($user_id, UserpicModel::FORMAT_75, $source_filepath);
		}
		else { // create all userpic formats
			UserpicModel::Create($user_id, $source_filepath);
		}
	}
}
