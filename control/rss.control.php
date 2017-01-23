<?

class RssControl extends ControlModel {

	public function index() {
		$assign = array(
			'genres' => GenreModel::GetStaticGenreList(),
			'occupation' => OccupationModel::GetStaticOccupationList(),
		);

		$this->setHtmlMetaTitle(SeoModel::rss(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::rss(SeoModel::DESCRIPTION));

		return $this->layout('rss/index.tpl', $assign);
	}

	public function photo() {

		$genre_id = (int)ifsetor($_GET['genre'], null);
		$user_id = (int)ifsetor($_GET['uid'], null);

		$genre_arr = array();

		$where = array();
		$where_raw = array();

		$where['status'] = Photo::STATUS_OKE;

		if($user_id) {
			$where['user_id'] = $user_id;
		}

		if($genre_id) {
			$where['genre_id'] = $genre_id;
			$genre_arr = GenreModel::GetOneGenreListByGenreId($genre_id);
		}

		$where['moderated'] = Photo::MODERATED_ON;

		$photo_collection = new PhotoCollection();
		$photo_collection->getCollection($where, $where_raw, 'DESC', array(0, 20));
		$photo_collection->getUserObjectCollection();
		$photo_collection->extendThumbCollection();

		$rss_title = 'Новые фотографии';
		$rss_link = UrlModel::homepage();

		if($user_id) {
			$user = $photo_collection->getUserObjectByUserId($user_id);
			if($user->exists()) {
				$rss_title .= sprintf(' пользователя %s', $user->getExtraField('name'));
				$rss_link = UrlModel::user($user->getId(), $user);
			}
		}

		if($genre_arr) {
			$rss_title .= sprintf(' в жанре "%s"', $genre_arr['name']);
		}

		$rss = $this->getFeedCreator(array('title'=>$rss_title, 'link'=>$rss_link));

		if($genre_arr) {
			// --= Channel category =-- //
			$feedCategory = new FeedCategory();
			$feedCategory->id = ifsetor($genre_arr['id'], 0);
			$feedCategory->domain = ifsetor($genre_arr['name_url'], null);
			$feedCategory->url = UrlModel::photo_lenta(array('genre'=>$genre_arr['id']));
			$feedCategory->description = ifsetor($genre_arr['name'], null);
			//$feedCategory->descriptionTruncSize = 500;
			$feedCategory->descriptionHtmlSyndicated = true;
			$rss->category = $feedCategory;
		}

		foreach($photo_collection as $photo_id=>$photo) {

			// --= Feed item =-- //
			$item = new FeedItem();

			// --= Item enclosure =-- //
			//ThumbModel::CreateThumbIfNotExists($photo->getId(), ThumbModel::THUMB_300);
			$p_thumb = $photo->getExtraField('thumb');
			$p_thumb = $p_thumb[ThumbModel::THUMB_300];
			//$p_thumb_lpath = $p_thumb['local'];
			$p_thumb_wpath = $p_thumb['src'];

			$enclosure_thumb = $photo->getExtraField('thumb');
			$enclosure_thumb = $enclosure_thumb[ThumbModel::THUMBNAIL_ORIGINAL];
			//$enclosure_thumb_lpath = $enclosure_thumb['local'];
			$enclosure_thumb_wpath = $enclosure_thumb['src'];

			$enclosure = new FeedItemEnclosure();
			$enclosure->url = $enclosure_thumb_wpath;
			$enclosure->length = $enclosure_thumb['filesize'];
			$enclosure->type = 'image/jpeg'; //Mimetype::GetFileMimetype($p_thumb_wpath);
			$item->enclosure = $enclosure;
			$item->title = ifsetor($photo->getField('name'), $photo->getField('orig_name'), true);
			$item->link = UrlModel::photo($photo_id, $photo);
			$item->pubDate = $photo->getField('add_tstamp');
			$item->author = $photo->getUserObject()->getExtraField('name');
			$item->comments = $item->link;

			$item_title = ifsetor($photo->getExtraField('name'), $photo->getExtraField('orig_name'), true);

			// --= Item guid =-- //
			$guid = new FeedItemGuid();
			$guid->isPermaLink = true;
			$guid->value = $photo_id;
			$item->guid = $guid;

			// --= Item category =-- //
			$p_genre = GenreModel::GetOneGenreListByGenreId($photo->getField('genre_id'));

			$p_genre_name = ifsetor($p_genre['name'], null);
			$p_genre_name_url = ifsetor($p_genre['name_url'], null);
			$p_genre_url = UrlModel::photo_lenta(array('genre'=>$photo->getField('genre_id')));

			if(1) {
				$itemCategory = new FeedCategory();
				$itemCategory->id = $photo->getField('genre_id');
				$itemCategory->url = $p_genre_url;
				$itemCategory->name = $p_genre_name;
				$itemCategory->domain = $p_genre_name_url;
				$itemCategory->description = $itemCategory->name;
				//$itemCategory->descriptionTruncSize = 500;
				$itemCategory->descriptionHtmlSyndicated = true;
				$item->category = $itemCategory;
			}

			// description
			$p_desc = array();
			$p_desc[] = sprintf('Автор: <a href="%1$s" title="%2$s">%2$s</a>', UrlModel::user($photo->getField('user_id'), $photo->getUserObject()), $item->author);
			$p_desc[] = sprintf('Жанр: <a href="%1$s" title="%2$s">%2$s</a>', $p_genre_url, $p_genre_name);
			//$p_desc[] = smarty_modifier_datetime($photo->getField('add_tstamp'));
			if($photo->getField('description')) {
				//$p_desc[] = $photo->getExtraField('description');
				$p_desc[] = SafeHtmlModel::output_urlify($photo->getField('description'), 200);
			}

			$item->description = _trim('
<table cellspacing="0" cellpadding="2" border="0"><tr>
<td valign="top"><a href="'.$item->link.'" title="'.$item_title.'"><img src="'.$p_thumb_wpath.'" width="'.$p_thumb['width'].'" height="'.$p_thumb['height'].'" alt="'.$item_title.'" border="0" /></a></td>
<td valign="top">'.implode('<br />', $p_desc).'</td>
</tr></table>');

			//$item->descriptionTruncSize = 500;
			$item->descriptionHtmlSyndicated = true;

			$rss->addItem($item);
		}

		$rss_feed = $rss->createFeed();

		return $this->feed_xml($rss_feed);
	}

	public function user() {

		$occupation_id = (int)ifsetor($_GET['occupation'], null);
		$experience_id = (int)ifsetor($_GET['experience'], null);

		$occupation_arr = array();
		$experience_arr = array();

		$where_arr = array();
		$join_arr = array();

		$where_arr[] = sprintf('u.status=%u', User::STATUS_OKE);

		if($occupation_id) {
			$join_arr[] = sprintf('JOIN %s AS oe ON u.id=oe.user_id', OccupationModel::db_occupation_experience_data());
			$where_arr[] = sprintf('oe.occupation_id=%u', $occupation_id);
			$occupation_arr = OccupationModel::GetOneOccupationListFilterOccupation($occupation_id);
		}
		$where_sql = implode(' AND ', $where_arr);
		$join_sql  = implode(' ', $join_arr);

		$sql = sprintf('
			SELECT * FROM %1$s AS u %2$s
			WHERE %3$s
			ORDER BY u.id DESC
			LIMIT 0, 20
		',
			User::db_table(),
			$join_sql,
			$where_sql
		);

		$user_collection = new UserCollection();
		$user_collection->getCollectionBySql($sql);
		$user_collection->extendOccupationCollection();
		$user_collection->extendThumbCollection();

		$rss_title = 'Новые пользователи';

		if($occupation_arr) {
			$rss_title .= sprintf(' (%s)', $occupation_arr['name']);
		}

		$rss = $this->getFeedCreator(array('title'=>$rss_title));

		if($occupation_arr) {
			$feedCategory = new FeedCategory();
			$feedCategory->id = ifsetor($occupation_arr['id'], 0);
			$feedCategory->domain = ifsetor($occupation_arr['name_url'], null);
			//$feedCategory->url = null;
			$feedCategory->description = ifsetor($occupation_arr['name'], null);
			//$feedCategory->descriptionTruncSize = 500;
			$feedCategory->descriptionHtmlSyndicated = true;
			$rss->category = $feedCategory;
		}

		require_once(SMARTY_USR_PLUGIN_PATH.'/modifier.occupation.php');

		foreach($user_collection as $user_id=>$user) {

			// --= Feed item =-- //
			$item = new FeedItem();

			// --= Item enclosure =-- //
			$userpic = $user->getExtraField('userpic');

			$userpic_small = $userpic[UserpicModel::FORMAT_75];
			$userpic_small_wpath = $userpic_small['src'];

			$enclosure = new FeedItemEnclosure();
			$userpic_big = $userpic[UserpicModel::FORMAT_300];
			$userpic_big_wpath = $userpic_big['src'];
			$enclosure->url = $userpic_big_wpath;
			$enclosure->length = $userpic_big['filesize'];
			$enclosure->type = 'image/jpeg'; // Mimetype::GetFileMimetype($userpic_wpath);
			$item->enclosure = $enclosure;

			// --= item name&description =-- //
			$item->title = $user->getField('name');
			$item->link = UrlModel::user($user_id, $user);
			$item->pubDate = $user->getField('reg_tstamp');
			$item->author = $item->title;
			//$item->comments = 'http://comments/url/';

			$item_title = $user->getExtraField('name');

			$item->description = _trim('
<table cellspacing="0" cellpadding="2" border="0"><tr>
	<td valign="top"><a href="'.$item->link.'" title="'.$item_title.'"><img src="'.$userpic_small_wpath.'" width="'.$userpic_small['width'].'" height="'.$userpic_small['height'].'" alt="'.$item_title.'" border="0" /></a></td>
	<td valign="top">'.smarty_modifier_occupation($user, true).'</td>
</tr></table>');

			// --= Item guid =-- //
			$guid = new FeedItemGuid();
			$guid->isPermaLink = true;
			$guid->value = $user_id;
			$item->guid = $guid;

			//$item->descriptionTruncSize = 500;
			$item->descriptionHtmlSyndicated = true;

			$rss->addItem($item);
		}

		$rss_feed = $rss->createFeed();

		return $this->feed_xml($rss_feed);
	}

	public function comment() {

		$comment_collection = new CommentPhotoCollection();

		$user_id = (int)ifsetor($_GET['uid'], null);
		$photo_id = (int)ifsetor($_GET['pid'], null);

		$where = array();
		$where_raw = array();

		if($user_id) {
			$where['user_id'] = $user_id;
		}

		if($photo_id) {
			$where['item_id'] = $photo_id;
		}

		$comment_collection->getCollection($where, $where_raw, 'DESC', array(0, 20));
		$comment_collection->getUserObjectCollection();
		$comment_collection->getItemObjectCollection();

		$rss_title = 'Новые комментарии';
		$rss_link = UrlModel::comment_lenta();

		if($user_id) {
			$user = $comment_collection->getUserObjectByUserId($user_id);
			if($user->exists()) {
				$rss_title .= sprintf(' от пользователя %s', $user->getExtraField('name'));
				$rss_link = UrlModel::comment_lenta(array('uid'=>$user->getId()));
			}
		}


		if($photo_id) {
			$photo = $comment_collection->getItemObjectByItemId($photo_id);
			if($photo->exists()) {
				$rss_title .= sprintf(' к фотографии %s', ifsetor($photo->getField('name'), $photo->getField('orig_name'), true));
				$rss_link = UrlModel::photo($photo->getId(), $photo);
			}
		}

		$rss = $this->getFeedCreator(array('title'=>$rss_title, 'link'=>$rss_link));

		foreach($comment_collection as $comment_id=>$comment) {

			// --= Feed item =-- //
			$item = new FeedItem();

			$user = $comment->getUserObject();
			$photo = $comment->getItemObject();

			// --= item name&description =-- //
			$item->title = sprintf('Комментарий от %s', $user->getExtraField('name'));
			$item->link = $comment->getExtraField('url');
			$item->pubDate = $comment->getField('add_tstamp');
			$item->author = $user->getExtraField('name');
			//$item->comments = 'http://comments/url/';

			// userpic
			$userpic = $user->getExtraField('userpic');
			$userpic = $userpic[UserpicModel::FORMAT_75];
			$userpic_wpath = $userpic['src'];

			// photo thumb
			$photo_thumb = $photo->getExtraField('thumb');
			$photo_thumb = $photo_thumb[ThumbModel::THUMB_150];
			$photo_thumb_wpath = $photo_thumb['src'];

			// description
			$p_desc = array();
			//$p_desc[] = $comment->getExtraField('text');
			$p_desc[] = SafeHtmlModel::output_urlify($comment->getExtraField('raw_text_cordless'), 200);
			$p_desc[] = '';
			$p_desc[] = '<a href="'.UrlModel::user($user->getId(), $user).'" title="'.$item->author.'"><img src="'.$userpic_wpath.'" width="35" alt="'.$item->author.'" title="" border="0" /></a>';

			$item->description = _trim('
<table cellspacing="0" cellpadding="2" border="0"><tr>
<td valign="top"><a href="'.$item->link.'" title="'.$photo->getExtraField('name').'"><img src="'.$photo_thumb_wpath.'" alt="'.$photo->getExtraField('name').'" border="0" /></a></td>
<td valign="top">'.implode('<br />', $p_desc).'</td>
</tr></table>');

			//$item->descriptionTruncSize = 500;
			$item->descriptionHtmlSyndicated = true;

			$rss->addItem($item);
		}

		$rss_feed = $rss->createFeed();

		return $this->feed_xml($rss_feed);
	}


	private function getFeedCreator($options=array()) {

		$__locale =& __locale();

		$options = array_merge(
			array(
				'encoding' => _strtolower($__locale->getCodeset()),
				'copyright' => URL_NAME,
				'title' => null,
				'description' => null,
				'link' => UrlModel::homepage(),
				'language' => $__locale->getLocale(),
				'pubDate' => time(),
				'generator' => null,
			),
			$options
		);

		$options['title'] = sprintf('%s - %s', $options['title'], URL_NAME);

		if(!isset($options['description'])) {
			$options['description'] = $options['title'];
		}
		if(!isset($options['generator'])) {
			$options['generator'] = $options['copyright'];
		}
		// Feed channel
		$rss = new UniversalFeedCreator();
		$rss->encoding = $options['encoding'];
		$rss->copyright = $options['copyright'];
		$rss->title = $options['title'];
		$rss->description = $options['description'];
		$rss->link = $options['link'];
		$rss->language = $options['language'];
		$rss->pubDate = $options['pubDate'];
		$rss->generator = $options['generator'];

		//$rss->managingEditor = 'managingEditor Name Surname email@email.com';
		//$rss->webMaster = 'webMaster Name Surname email@email.com';
		//$rss->lastBuildDate = time()-3600;
		//$rss->docs = 'http://blogs.law.harvard.edu/tech/rss';
		//$rss->ttl = 3548;
		//$rss->rating = 10.3;
		//$rss->cloud = 'Cloud channel content <script>alert(1);</script>';
		//$rss->skipHours = array(0,1,2,3,23,24);
		//$rss->skipDays = array('Monday', 'Friday');

		//$rss->descriptionTruncSize = 500;
		//$rss->descriptionHtmlSyndicated = true;
		//$rss->xslStyleSheet = "http://feedster.com/rss20.xsl";

		// --= Channel image =-- //
		$image = new FeedImage();
		$image->title = $rss->title;
		$image->url = S_URL . 'img/logo/logo.png';
		$image->link = $rss->link;
		$image->width = 103;
		$image->height = 26;
		$image->description = $rss->description;
		//$image->descriptionTruncSize = 500;
		$image->descriptionHtmlSyndicated = true;
		$rss->image = $image;

		return $rss;
	}

	private function feed_xml($what=null, $echo=false) {
		if(!headers_sent()) {
			header('Content-type: text/xml');
		}
		if($echo) {
			echo $what;
		}
		else {
			return $what;
		}
	}

}
