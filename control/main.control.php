<?

class MainControl extends ControlModel {

	public function index() {

		$exist_photo_id_arr = array();

		_e('# NEW PHOTOS');
		$photo_collection_new = new PhotoCollection();
		$photo_collection_new->getCollection(array('status'=>Photo::STATUS_OKE), array(), 'DESC', array(0, 24));
		//_e($photo_collection_new);
		$exist_photo_id_arr = array_unique(array_merge($exist_photo_id_arr, $photo_collection_new->keys()));

		$photo_collection_new_1 = new PhotoCollection();
		$photo_collection_new_2 = new PhotoCollection();
		$i = 0;
		foreach($photo_collection_new as $photo_id=>$photo) {
			$photo_collection_new_1_2 =& $photo_collection_new_1;
			if($i>=24) {
				$photo_collection_new_1_2 =& $photo_collection_new_2;
			}
			$photo_collection_new_1_2->addItem($photo_id, $photo);
			$i = $photo_collection_new_1->length() + $photo_collection_new_2->length();
		}

		_e('# PHOTO OF THE DAY');
		$photo_collection_hit = new PhotoCollection();
		$photo_collection_hit->getCollection(
			array('status'=>Photo::STATUS_OKE),
			array(sprintf('add_tstamp>%u', time()-1*24*3600)),
			array('views'=>'DESC', 'votes_value'=>'DESC'),
			array(0, 1)
		);
		$photo_collection_hit->getUserObjectCollection();
		//_e($photo_collection_hit);
		$exist_photo_id_arr = array_unique(array_merge($exist_photo_id_arr, $photo_collection_hit->keys()));

		$photo_hit = new Photo();
		if($photo_collection_hit->length()) {
			$photo_hit = $photo_collection_hit->getFirst();
		}
		else {
			$photo_hit = $photo_collection_new->getFirst();
			foreach($photo_collection_new as $photo_id=>$photo) {
				if($photo_hit->getField('views')<$photo->getField('views')) {
					$photo_hit = $photo;
				}
			}
		}

		_e('# TOP PHOTOS');
		$photo_top_arr = array();
		$photo_top_time = time()-rand(10,60)*24*3600;
		$photo_top_date = date('d-m-Y', $photo_top_time);
		$photo_collection_top = new PhotoCollection();
		$photo_collection_top->getCollection(
			array('status'=>Photo::STATUS_OKE),
			array(sprintf('add_tstamp>%u', $photo_top_time), sprintf('NOT(%s)', MySQL::sqlInClause('id', $exist_photo_id_arr))),
			array('views'=>'DESC', 'votes_value'=>'DESC'),
			array(0, 6)
		);
		$photo_collection_top->shuffle();
		$exist_photo_id_arr = array_unique(array_merge($exist_photo_id_arr, $photo_collection_top->keys()));

		/*
		_e('# RAND PHOTOS');
		$photo_collection_rand = new PhotoCollection();
		$photo_collection_rand->getCollection(
			array('status'=>Photo::STATUS_OKE),
			array(
				sprintf('id>=(SELECT FLOOR(MAX(id) * RAND()) FROM %1$s)', Photo::db_table()),
				sprintf('NOT(%s)', MySQL::sqlInClause('id', $exist_photo_id_arr))
			),
			array('views'=>'DESC', 'votes_value'=>'DESC'),
			array(0, 6)
		);
		$photo_collection_rand->shuffle();
		$exist_photo_id_arr = array_unique(array_merge($exist_photo_id_arr, $photo_collection_rand->keys()));
		*/

		_e('# LAST COMMENTS');
		$comment_collection = new CommentPhotoCollection();
		$comment_collection->getCollection(array(), array(), 'DESC', array(0, 7));
		$comment_collection->getItemObjectCollection();
		$comment_collection->getUserObjectCollection();

		_e('# GENRE INFO');
		$genreList =& GenreModel::GetStaticGenreList();
		$d_stamp_now = strtotime(date('Y-m-d', time()));
		list($tstamp_from, $tstamp_to) = array($d_stamp_now, $d_stamp_now + 24*3600);
		$photo_collection_genre = new PhotoCollection();
		$photo_genre_count = $photo_collection_genre->getCountAggregated(
			'genre_id',
			array('status'=>Photo::STATUS_OKE),
			array(sprintf('add_tstamp>%u', $tstamp_from), sprintf('add_tstamp<%u', $tstamp_to))
		);
		foreach($genreList as $genre_id=>&$genre_data) {
			$genre_data['count'] = ifsetor($photo_genre_count[$genre_id], 0);
		}

		_e('# NEW USERS');
		$user_collection_new = new UserCollection();
		$user_collection_new->getCollection(array('status'=>User::STATUS_OKE), array(), 'DESC', array(0, 5));
		$user_collection_new->extendOccupationCollection();

		_e('# ONLINE USERS');
		$user_collection_online = new UserOnlineCollection();
		$user_collection_online->getCollectionLive();
		$user_collection_online->getUserObjectCollection();

		// define image_src for homepage
		$photo_image_src = new Photo();
		if($photo_collection_hit->length()) {
			$photo_image_src = $photo_collection_hit->getFirst();
		}
		elseif($photo_collection_top->length()) {
			$photo_image_src = $photo_collection_top->getFirst();
		}

		$thumb_image = $photo_image_src->getExtraField('thumb');
		$this->setHtmlMetaImageSrc(ifsetor($thumb_image[ThumbModel::THUMB_300]['src'], null));

		$assign = array(
			'photo_collection_new' => $photo_collection_new,
			'photo_collection_new_1' => $photo_collection_new_1,
			'photo_collection_new_2' => $photo_collection_new_2,
			'photo_collection_top' => $photo_collection_top,
			'photo_hit' => $photo_hit,
			'comment_collection' => $comment_collection,
			'user_collection_new' => $user_collection_new,
			'user_collection_online' => $user_collection_online,
			'genreUpdates' => $genreList,
			'photo_top_date' => $photo_top_date,
		);

		//_e($assign);

		return $this->layout('main/index.tpl', $assign);
	}

}
