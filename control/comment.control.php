<?

class CommentControl extends ControlModel {

	public function lenta() {

		$__db =& __db();

		$request_query = array();

		// --= genre =-- //
		$filter_genre = ifsetor($_GET['genre'], null); // id | alias
		if($filter_genre=='__genre__') return $this->page404();
		$genre_data = array();
		$genre_id = 0;
		if($filter_genre) {
			$genre_data = GenreModel::GetOneGenreListByGenreId($filter_genre);
			if($genre_data) {
				$genre_id = $genre_data['id'];
				$request_query['genre'] = $filter_genre;
			}
		}

		// --= user =-- //
		$filter_uid = ifsetor($_GET['uid'], 0);
		if($filter_uid) {
			$request_query['uid'] = $filter_uid;
		}

		// --= date =-- //
		$filter_date = ifsetor($_GET['date'], null);
		if($filter_date=='__date__') return $this->page404();
		$filter_tstamp_from = 0;
		$filter_tstamp_to = 0;
		if($filter_date) {
			$filter_date_tstamp_arr = explode('/', $filter_date);
			$filter_tstamp_from = ifsetor($filter_date_tstamp_arr[0], null);
			$filter_tstamp_to = ifsetor($filter_date_tstamp_arr[1], null);
			$filter_tstamp_from = strtotime($filter_tstamp_from);
			$filter_tstamp_to = strtotime($filter_tstamp_to);
			if($filter_tstamp_from && !$filter_tstamp_to) {
				$filter_tstamp_to = $filter_tstamp_from + 24*3600;
			}
			if($filter_tstamp_from>0 && $filter_tstamp_from<$filter_tstamp_to) {
				$request_query['date'] = $filter_date;
			}
			else {
				$filter_tstamp_to = $filter_tstamp_from = 0;
			}
		}

		// --= search =-- //
		$filter_q = ifsetor($_GET['q'], null);
		if($filter_q) {
			$request_query['q'] = $_GET['q'];
		}

		// --= orderby =-- //
		$orderby = ifsetor($_GET['orderby'], null);
		$orderby = insetor($orderby, array('id'), 'id');
		if($orderby) {
			$request_query['orderby'] = $orderby;
		}

		// --= sort direction =-- //
		$ordermethod = ifsetor($_GET['ordermethod'], null);
		$ordermethod = insetor($ordermethod, array('asc', 'desc'), 'desc');
		if($ordermethod) {
			$request_query['ordermethod'] = $ordermethod;
		}

		// --= paginator =-- //
		$perpare = 20;
		$pagenum = ifsetor($_GET[Pager::GET_PARAM_NAME], Pager::__PAGE_ONE);
		$request_query['p'] = $pagenum;
		if(!is_numeric($pagenum)) return $this->page404();

		$where_arr = array();
		$order_arr = array();
		$group_arr = array();

		$where_arr[] = sprintf('p.status=%u', Photo::STATUS_OKE);

		if($genre_id) {
			$where_arr[] = sprintf('p.genre_id=%u', $genre_id);
		}
		if($filter_uid) {
			$where_arr[] = sprintf('c.user_id=%u', $filter_uid);
		}
		if($filter_tstamp_from) {
			$where_arr[] = sprintf('c.add_tstamp>=%u', $filter_tstamp_from);
		}
		if($filter_tstamp_to) {
			$where_arr[] = sprintf('c.add_tstamp<%u', $filter_tstamp_to);
		}
		if($filter_q) {
			$where_arr[] = sprintf('c.text LIKE \'%%%1$s%%\'', MySQL::escape($filter_q));
		}
		if($filter_q) {
			$filter_q = strip_tags($filter_q);
			if(_strlen($filter_q)>2) {
				$filter_q_sql = MySQL::escape($filter_q);
				$filter_q_sql = _str_replace('%', '\%', $filter_q_sql);
				$where_arr[] = sprintf('c.text LIKE \'%%%1$s%%\'', $filter_q_sql);
			}
			else {
				$where_arr[] = '0';
			}
		}
		if($orderby) {
			$order_arr[] = sprintf('%s %s', $orderby, _strtoupper($ordermethod));
		}

		$where_sql = implode(' AND ', $where_arr);
		$order_sql = implode(', ', $order_arr);
		$limit_sql = MySQL::sqlLimit(Pager::getCurrentPageSql($pagenum), $perpare);

		$sql = sprintf('
			SELECT c.* FROM %1$s AS c
			JOIN %2$s AS p on c.item_id=p.id
			WHERE %3$s
			ORDER BY %4$s
			LIMIT %5$s
		',
			CommentPhoto::db_table(),
			Photo::db_table(),
			$where_sql,
			$order_sql,
			$limit_sql
		);

		$comment_collection = new CommentPhotoCollection();

		$sql_r = $__db->q($sql, 0, $total_cnt);
		while($row = $sql_r->next()) {
			$comment_collection->populateObject($row);
		}
		$comment_collection->getUserObjectCollection();
		$comment_collection->getItemObjectCollection();

		_e('# GENRE INFO');
		$genre_collection =& GenreModel::GetStaticGenreList();

		_e('# USER');
		$user = new User();
		if($filter_uid) {
			$user = $comment_collection->getUserObjectByUserId($filter_uid);
			if($user->exists()) {
				$user->extendOccupation();
				$rss_url = UrlModel::rss_comment(array('uid'=>$user->getId()));
				$rss_name = sprintf('Последние комментарии от %s', $user->getField('name'));
				$this->include_rss(array($rss_url=>$rss_name));
			}
		}

		$__pager = new Pager($total_cnt, $perpare);

		$assign = array(
			'comment_collection' => $comment_collection,
			'genre_collection' => $genre_collection,
			'genre_data' => $genre_data,
			'user' => $user,
			'pager' => $__pager,
			'total_cnt' => $total_cnt,
			'request_query' => $request_query,
			'filter_date_from' => $filter_tstamp_from?$filter_tstamp_from:time(),
			'filter_date_to' => $filter_tstamp_to?$filter_tstamp_to:time(),
			'q_what' => 'comment',
		);

		//_e($assign);

		$this->include_jscal();
		$this->include_paginator();

		$this->setHtmlMetaCanonicalUrl(UrlModel::comment_lenta($request_query));
		$this->setHtmlMetaNameContent('robots', 'noarchive');

		$seo_params = array(
			'genre' => $genre_data,
			'user' => $user,
			'time_from' => $filter_tstamp_from,
			'time_to' => $filter_tstamp_to,
		);
		$this->setHtmlMetaTitle(SeoModel::comment_lenta($seo_params, SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::comment_lenta($seo_params, SeoModel::DESCRIPTION));

		return $this->layout('comment/lenta.tpl', $assign);
	}

	public function add() {

		$redirect_url = null;
		$json_response = array();

		$item_type = $this->parseItemType();
		$is_json = $this->isJson();

		$comment = null;
		switch($item_type) {
			case Comment::TYPE_PHOTO:
			default:
				$comment = new CommentPhoto();
		}
		if(!$comment) $comment = new CommentBimbo();

		if($comment->isAddable()) {
			$comment->add();
		}
		$item_id = $this->getItemId($comment->getField('item_id'));
		$json_response = $this->get_array($item_id, $comment);

		if($comment->getId()) {
			$redirect_url = $comment->getExtraField('url');
		}
		else {
			$json_response['html'] = null;
		}

		if($is_json) {
			return JsonModel::encode($json_response);
		}
		else {
			UrlModel::redirect($redirect_url);
		}
	}

	public function upd() {

		$redirect_url = null;
		$json_response = array();

		$item_type = $this->parseItemType();
		$is_json = $this->isJson();
		$comment_id = ifsetor($_REQUEST['id'], 0);

		$comment = null;
		switch($item_type) {
			case Comment::TYPE_PHOTO:
			default:
				$comment = new CommentPhoto($comment_id); $comment->load();
		}
		if(!$comment) $comment = new CommentBimbo();

		if($comment->isEditable()) {
			$comment->change();
		}
		if($is_json) {
			$item_id = $this->getItemId($comment->getField('item_id'));
			$json_response = $this->get_array($item_id, $comment);
		}
		else {
			$redirect_url = $comment->getExtraField('url');
		}

		if($is_json) {
			return JsonModel::encode($json_response);
		}
		else {
			UrlModel::redirect($redirect_url);
		}
	}

	public function clear() {

		$redirect_url = null;
		$json_response = array();

		$item_type = $this->parseItemType();
		$is_json = $this->isJson();
		$comment_id = ifsetor($_REQUEST['id'], 0);

		$comment = null;
		switch($item_type) {
			case Comment::TYPE_PHOTO:
			default:
				$comment = new CommentPhoto($comment_id); $comment->load();
		}
		if(!$comment) $comment = new CommentBimbo();

		if($comment->isClearable()) {
			$comment->clear();
		}
		if($is_json) {
			$item_id = $this->getItemId($comment->getField('item_id'));
			$json_response = $this->get_array($item_id, $comment);
			$json_response['url'] = $json_response['anchor'] = '#';
		}
		else {
			$redirect_url = $comment->getExtraField('url');
		}

		if($is_json) {
			return JsonModel::encode($json_response);
		}
		else {
			UrlModel::redirect($redirect_url);
		}
	}

	public function del() {

		$json_response = array();

		$item_type = $this->parseItemType();
		$is_json = $this->isJson();

		$comment_id = ifsetor($_REQUEST['id'], 0);

		$comment = null;
		switch($item_type) {
			case Comment::TYPE_PHOTO:
			default:
				$comment = new CommentPhoto($comment_id); $comment->load();
		}
		if(!$comment) $comment = new CommentBimbo();

		if($comment->isRemovable()) {
			$item_id = $this->getItemId($comment->getField('item_id'));
			$comment->removeById();
			if($is_json) {
				$json_response = $this->get_array($item_id, new CommentPhoto());
			}
		}
		elseif($is_json) {
			$__error = new ErrorModel();
			$__error->push('COMMENT_NO_DEL_PERMISSION');
			$json_response['error'] = $__error->getErrorValues();
		}

		if($is_json) {
			return JsonModel::encode($json_response);
		}
		else {
			UrlModel::redirect();
		}
	}

	// ---

	/**
	 * @param unknown_type $item_id
	 * @param unknown_type $item_type
	 * @param unknown_type $format [plain, array, json]
	 */
	public function get($item_id=null, $item_type=null, $format=null) {

		$result = null;

		$item_id = $this->getItemId($item_id);
		$item_type = $this->parseItemType($item_type);
		$format = ifsetor($format, null);

		$is_json = $this->isJson();

		if($is_json && is_null($format)) {
			$format = 'json';
		}

		$format = insetor($format, array('plain', 'array', 'json'), 'plain');

		switch($item_type) {
			case Comment::TYPE_PHOTO:
				$comment = new CommentPhoto();
				break;
			default:
				$comment = new Comment();
				break;
		}

		switch($format) {
			case 'plain':
				$result = $this->get_plain($item_id, $comment);
				break;
			case 'array':
				$result = $this->get_array($item_id, $comment);
				break;
			case 'json':
				$result = $this->get_json($item_id, $comment);
				break;
		}

		return $result;
	}

	private function get_plain($item_id, Comment $comment) {
		$result = null;
		if($item_id) {
			$comment_collection = $this->getItemCommentCollection($item_id, $comment);
			$assign = array(
				'comment_collection' => $comment_collection,
				'item_id' => $item_id,
			);
			$result = $this->render('comment/item.tpl', $assign);
		}
		else {
			_e('# item_id is undefined', E_USER_WARNING);
		}
		return $result;
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @param unknown_type $comment
	 * @return Array $result
	 */
	private function get_array($item_id, Comment $comment) {

		$result = array();

		$result['id'] = $comment->getId();
		$result['item_id'] = Cast::int($item_id);
		$result['count'] = $this->getItemCommentCollection($item_id, $comment)->length();
		$result['url'] = $comment->getExtraField('url');
		$result['error'] = $comment->getErrorValues();
		$result['html'] = $this->get_plain($item_id, $comment);

		return $result;
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @param unknown_type $comment
	 * @return string
	 */
	private function get_json($item_id, Comment $comment) {
		return JsonModel::encode($this->get_array($item_id, $comment));
	}

	// ---

	/**
	 *
	 * @param unknown_type $item_id
	 * @param Comment $comment
	 * @param unknown_type $refresh
	 * @return CommentCollection $comment_collection
	 */
	private function getItemCommentCollection($item_id, Comment $comment, $refresh=false) {

		$comment_collection = null;

		static $collection = array();

		if($item_id) {

			$item_type = $comment->getItemType();

			if(array_key_exists($item_type, $collection) && array_key_exists($item_id, $collection[$item_type]) && !$refresh) {
				$comment_collection = $collection[$item_type][$item_id];
			}
			elseif($item_type==Comment::TYPE_PHOTO) {
				$comment_collection = new CommentPhotoCollection();
			}

			if(!is_null($comment_collection)) {
				$comment_collection->getCollectionByItemId($item_id);
				$comment_collection->getUserObjectCollection();
				$collection[$item_type][$item_id] = $comment_collection;
			}
		}

		if(is_null($comment_collection)) {
			$comment_collection = new CommentBimboCollection();
		}

		return $comment_collection;
	}

	// ---

	private function getItemId($item_id=null) {
		return ifsetor($item_id, ifsetor($_REQUEST['item_id'], 0));
	}

	private function parseItemType($item_type=null) {
		$item_type = ifsetor($item_type, ifsetor($_REQUEST['item_type'], Comment::TYPE_PHOTO));
		$item_type = insetor($item_type, array(Comment::TYPE_PHOTO), Comment::TYPE_PHOTO);
		return $item_type;
	}

	private function isJson() {
		return ifsetor($_REQUEST['json'], false, true);
	}

}
