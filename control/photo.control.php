<?

class PhotoControl extends ControlModel {

	const PHOTO_MAX_SIZE = 3145728; // 3*1024*1024

	const UPLOAD_INACTIVE = 0;

	public function upload() {

		require_auth();

		$photo = new Photo();
		$user = User::getOnlineUser();

		if(self::UPLOAD_INACTIVE) {
			$photo->pushError('PHOTO_UPLOAD_INACTIVE');
		}

		// update upload permissions
		if($user->getField('upload_next_tstamp')<time()) {
			$user->setField('upload_limit', $user->getPeriodicalUploadLimit());
			$user->setField('upload_next_tstamp', $user->getNextUploadTime());
			$user->update();
		}

		// Check user upload permissions
		if(!$user->canUpload()) {
			$photo->pushError('PHOTO_UPLOAD_EXCEEDED', date('d.m.Y G:i', $user->getNextUploadTime()));
		}

		$this->execUploadEditCommonPrefix($photo);

		$photo->setCustomField('status', Photo::STATUS_LOCK);

		$fileUploader = new FileUploadModel();
		$fileUploader->set_desired_form_maxfilesize(self::PHOTO_MAX_SIZE);

		if(Predicate::posted() && !$photo->isError()) {

			self::set_time_limit_user_abort(5*60);

			$photo->addTest($photo->getCustomFields());

			if(!$photo->isError()) {

				$photoUploadInfo = array();
				if(!$user->isError()) {
					$photoUploadInfo = $this->photo_upload_info($fileUploader, $photo);
				}

				if(!$photo->isError()) {

					// store exif data
					if($photoUploadInfo) {
						$__exifer = new Exifer($photoUploadInfo['fpath']);
						if($__exifer->hasExifInfo()) {
							$__exifer->SetExifRawProperty('FileName', $photoUploadInfo['fname']);
							$exif = $__exifer->GetExifRawData();
							$photo_exif = $exif ? @serialize($exif) : null;
							$photo->setCustomField('exif', $photo_exif);
						}

						$photoImageInfo = IM::GetImageInfo($photoUploadInfo['fpath']);
						$photo->setCustomField('orig_size', $photoImageInfo['filesize']);
						$photo->setCustomField('orig_width', $photoImageInfo['width']);
						$photo->setCustomField('orig_height', $photoImageInfo['height']);
						$photo->setCustomField('orig_mimetype', $photoImageInfo['mime']);
						$photo->setCustomField('orig_name', $photoUploadInfo['fname']);
					}

					$photo->add($photo->getCustomFields());

					if($photo->getId()) {

						// create thumbs
						$thumbResult = ThumbModel::Create($photo->getId(), $photoUploadInfo['fpath']);

						if($thumbResult) {

							// update photo status
							if($photo->getField('status')!=Photo::STATUS_OKE) {
								$photo->setField('status', Photo::STATUS_OKE);
								$photo->update();
							}

							// decrease user upload limit
							$user->setField('upload_limit', Cast::unsignint($user->getField('upload_limit')-1));
							$user->setField('upload_tstamp', time());
							$user->update();

							$user->recalcInfo();

							$photo_url = UrlModel::photo($photo->getId(), $photo);

							// send email to support when new photo is uploaded
							$message_subject = 'New photo';
							$message_body = sprintf('User %s uploaded photo %s %s', $user->getField('name'), $photo->getField('name'), $photo_url);
							__mailme($message_subject, $message_body);

							UrlModel::redirect($photo_url);
						}
						else {
							$photo->removeById();
							$photo = new Photo(); // create new object after calling remove method
							$fileName = _htmlspecialchars(ifsetor($photoUploadInfo['fname'], null));
							$photo->pushError('UPLOAD_UNKNOWN', array($fileName));
						}
					}
					else {
						$photo->pushError('DATA_SAVE_FAIL');
					}
				}
			}

		}

		$photo->setUserObject($user);

		$this->execUploadEditCommonPostfix($photo);

		$assign = $this->getUploadEditCommonAssign($photo);

		$allowedFormats = implode(', ', array_unique(array_values($this->getAllowedFormats())));

		$errors = $photo->getErrors();
		$uploadExceeded = array_key_exists('PHOTO_UPLOAD_EXCEEDED', $errors);

		$MAX_FILE_SIZE_BYTES = $fileUploader->get_form_maxfilesize();

		$assign += array(
			'UPLOAD_EXCEEDED' => $uploadExceeded,
			'UPLOAD_INACTIVE' => self::UPLOAD_INACTIVE,
			'ALLOWED_FORMATS' => $allowedFormats,
			'MAX_FILE_SIZE_BYTES' => $MAX_FILE_SIZE_BYTES,
			'MAX_FILE_SIZE_MBYTES' => Cast::byte2megabyte($MAX_FILE_SIZE_BYTES).' Mb',
		);

		$this->setHtmlMetaTitle(SeoModel::photo_upload(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::photo_upload(SeoModel::DESCRIPTION));

		return $this->layout('photo/upload_edit.tpl', $assign);
	}

	private function getAllowedFormats() {
		$formats = array(
			IMAGETYPE_JPEG => 'JPEG',
			IMAGETYPE_PNG => 'PNG',
			IMAGETYPE_TIFF_II => 'TIFF',
			IMAGETYPE_TIFF_MM => 'TIFF',
		);
		return $formats;
	}

	private function execUploadEditCommonPrefix(Photo &$photo) {

		$_REQ =& $_POST;

		$photo->setCustomFields(array_merge($photo->getFields(), $_REQ));

		if(!$photo->getCustomField('rgb')) {
			$photo->setCustomField('rgb', end(Photo::getColorsRGB()));
		}

		if(!$photo->getCustomField('genre')) {
			$photo->setCustomField('genre', $photo->getField('genre_id'));
		}

		_e($photo->getCustomFields());
	}

	private function execUploadEditCommonPostfix(Photo $photo) {
		//
	}

	private function getUploadEditCommonAssign(Photo $photo) {

		$assign = array(
			'photo' => $photo,
			'listGenre' => GenreModel::GetStaticGenreList(),
			'listColorRGB' => Photo::getColorsRGB(),
			'Validator' => new ValidatorModel(),
		);

		//_e($assign);

		return $assign;
	}

	public function edit($photo_id=0) {

		require_auth();

		$photo_id = ifsetor($_REQUEST['id'], $photo_id);

		$photo = new Photo($photo_id);
		$photo->extendThumb();
		$photo->getUserObject();

		if(!$photo->isEditable()) {
			Url::redirect(UrlModel::homepage());
		}

		$this->execUploadEditCommonPrefix($photo);

		if(Predicate::posted()) {

			$photo->changeTest();

			if(!$photo->isError()) {

				$updated = $photo->change($photo->getCustomFields());

				if(!$photo->isError()) {

					if($updated) {
						Url::redirect(UrlModel::photo($photo->getId(), $photo));
					}
					else {
						$photo->pushError('DATA_SAVE_FAIL');
					}
				}
			}
		}

		$this->execUploadEditCommonPostfix($photo);

		$assign = $this->getUploadEditCommonAssign($photo);

		$this->setHtmlMetaTitle(SeoModel::photo_edit($photo, SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::photo_edit($photo, SeoModel::DESCRIPTION));

		return $this->layout('photo/upload_edit.tpl', $assign);
	}

	public function view($photo_id) {

		$photo = new Photo($photo_id);

		if(!$photo->exists() || $photo->getField('status')!=Photo::STATUS_OKE && !User::isOnlineAdmin()) {
			Url::redirect(UrlModel::homepage());
		}

		$photo->getUserObject()->extendOccupation();
		$photo->extendThumb();
		$photo->extendExif();
		//$photo->setExtraField('exif_brief', $photo->getExifInfo(true));

		// user photo gallery
		$gallery_collection = $this->photo_user_gallery($photo_id, $photo->getField('user_id'));

		// update photo view statistics
		$photo->updateViews();

		// photo comments
		$comment_collection = new CommentPhotoCollection();
		$comment_collection->getCollectionByItemId($photo_id);
		$comment_collection->getUserObjectCollection();

		// photo comment object
		$comment = new CommentPhoto();
		$comment->setItemObject($photo);

		// photo vote
		$vote = new VotePhoto();
		$vote->checkCanVote($photo_id);
		$photo->setCustomField('vote', $vote);

		// Update photo user view
		if(User::getOnlineUserId()) {
			$photo_user_view = new PhotoUserView();
			$photo_user_view->add(array('item_id'=>$photo_id, 'user_id'=>User::getOnlineUserId()));
		}

		$thumb_image = $photo->getExtraField('thumb');
		$this->setHtmlMetaImageSrc(ifsetor($thumb_image[ThumbModel::THUMB_300]['src'], null));

		// @TODO
		$__db =& __db();
		$sql = sprintf(
			'SELECT width, height FROM %s where photo_id=%d and format=%d LIMIT 1',
			ThumbModel::db_thumb(),
			$photo->getId(),
			ThumbModel::THUMBNAIL_ORIGINAL
		);
		$sql_r = $__db->row($sql);

		//$photo->setCustomField('preview_width', $photo->getThumbWidth(ThumbModel::THUMBNAIL_ORIGINAL));
		//$photo->setCustomField('preview_height', $photo->getThumbHeight(ThumbModel::THUMBNAIL_ORIGINAL));
		$photo->setCustomField('preview_width', $sql_r['width'] ? $sql_r['width'] : 1000);
		$photo->setCustomField('preview_height', $sql_r['height']);

		$assign = array(
			'photo' => $photo,
			'comment' => $comment,
			'gallery_collection' => $gallery_collection,
			'comment_collection' => $comment_collection,
			'rgb_colors' => Photo::getColorsRGB(),
		);

		//_e($assign);

		$rss_url = UrlModel::rss_comment(array('pid'=>$photo_id));
		$rss_name = SeoModel::rss_photo_comment($photo);
		$this->include_rss(array($rss_url=>$rss_name));

		$this->include_jcrop();
		$this->include_js('photo.js');

		//$this->include_js('comment_atom.js');
		//$this->include_js('comment_form.js');
		//$this->include_js('comment_control.js');

		//$this->include_js('https://apis.google.com/js/plusone.js', false);

		$this->setHtmlMetaTitle(SeoModel::photo($photo, SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::photo($photo, SeoModel::DESCRIPTION));
		$this->setHtmlMetaCanonicalUrl(UrlModel::photo($photo->getId(), $photo));
		$this->setHtmlMetaPropertyContent('og:title', Text::smartHtmlSpecialChars(SeoModel::photo($photo, SeoModel::TITLE)));
		$this->setHtmlMetaPropertyContent('og:type', 'article');
		$this->setHtmlMetaPropertyContent('og:url', UrlModel::photo($photo->getId(), $photo));
		$this->setHtmlMetaPropertyContent('og:image', ifsetor($thumb_image[ThumbModel::THUMB_1000]['src'], null));
		$this->setHtmlMetaPropertyContent('og:description', Text::smartHtmlSpecialChars(SeoModel::photo($photo, SeoModel::DESCRIPTION)));
		$this->setHtmlMetaPropertyContent('og:site_name', URL_NAME);

		return $this->layout('photo/view.tpl', $assign);
	}

	public function remove($photo_id=0) {

		require_auth();

		$photo_id = ifsetor($_REQUEST['id'], $photo_id);

		$photo = new Photo($photo_id);
		$photo->extendThumb();

		if($photo->isRemovable()) {

			if(Predicate::posted()) {

				$photo->removeById();

				Url::redirect(UrlModel::user(User::getOnlineUserId(), User::getOnlineUser()));
			}

			$assign = array(
				'photo' => $photo,
			);

			$this->HtmlRobotsDisallow = true;

			$this->setHtmlMetaTitle(SeoModel::photo_remove($photo, SeoModel::TITLE));
			$this->setHtmlMetaDescription(SeoModel::photo_remove($photo, SeoModel::DESCRIPTION));

			return $this->layout('photo/remove.tpl', $assign);
		}

		Url::redirect(UrlModel::homepage());
	}

	public function lenta() {

		$request_query = array();

		// --= status =-- //
		$filter_status = Photo::STATUS_OKE;
		if(User::isOnlineAdmin()) {
			$filter_status = Cast::unsignint(ifsetor($_GET['status'], $filter_status));
			$filter_status = insetor(
				$filter_status, array(Photo::STATUS_OKE, Photo::STATUS_LOCK), Photo::STATUS_OKE
			);
			$request_query['status'] = $filter_status;
		}

		// --= moderated =-- //
		$filter_moderated = null;
		if(User::isOnlineAdmin()) {
			$filter_moderated = ifsetor($_GET['moderated'], $filter_moderated);
			if(!is_null($filter_moderated)) {
				$filter_moderated = insetor(
					Cast::unsignint($filter_moderated), array(Photo::MODERATED_ON, Photo::MODERATED_OFF), null
				);
				if(!is_null($filter_moderated)) {
					$request_query['moderated'] = $filter_moderated;
				}
			}
		}

		// --= genre =-- //
		$filter_genre = ifsetor($_GET['genre'], null); // id | alias
		if($filter_genre=='__genre__') return $this->page404();
		$genre_data = array();
		$genre_id = 0;
		if($filter_genre) {
			//$genre_data = GenreModel::GetOneGenreListByGenreAlias($filter_genre);
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
		$orderby = insetor($orderby, array('id', 'rating', 'views', 'comments', 'votes'), 'id');
		if($orderby) {
			$request_query['orderby'] = $orderby;
		}
		// extended orderby
		$orderby_extended = array(
			'votes' => array('votes_value', 'votes', 'views'),
			'views' => array('views', 'votes_value'),
			'comments' => array('comments', 'views'),
		);
		$orderby = ifsetor($orderby_extended[$orderby], $orderby);

		// --= sort direction =-- //
		$ordermethod = ifsetor($_GET['ordermethod'], null);
		$ordermethod = insetor($ordermethod, array('asc', 'desc'), 'desc');
		if($ordermethod) {
			$request_query['ordermethod'] = $ordermethod;
		}

		// --= view mode =-- //
		$viewmode_cookie = ifsetor($_COOKIE['photo_viewmode'], null);

		$viewmode = ifsetor($_GET['viewmode'], $viewmode_cookie);
		$viewmode = insetor($viewmode, array('square', 'asis'), 'square');

		if($viewmode) {

			$request_query['viewmode'] = $viewmode;

			if($viewmode_cookie!=$viewmode) {
				$cookie['name'] = 'photo_viewmode';
				$cookie['value'] = $viewmode;
				$cookie['domain'] = Cookie::domain();
				$cookie['expires'] = time()+31*24*60*60; // 1 month
				$cookie['path'] = '/';
				Cookie::set($cookie['name'], $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain']);
			}
		}

		$total_cnt = 0;
		$where = array();
		$where_raw = array();
		$order = array();
		$pagenum = ifsetor($_GET[Pager::GET_PARAM_NAME], Pager::__PAGE_ONE);
		$perpare = 3*10; if($viewmode=='square') $perpare = 4*9;
		$limit = array(Pager::getCurrentPageSql($pagenum), $perpare);
		$request_query['p'] = $pagenum;
		if(!is_numeric($pagenum)) return $this->page404();

		$where['status'] = Cast::unsignint($filter_status);

		if($genre_id) {
			$where['genre_id'] = Cast::unsignint($genre_id);
			$rss_url = UrlModel::rss_photo(array('genre'=>$genre_id));
			$rss_name = sprintf('Новые фотографии по жанру %s', $genre_data['name']);
			$this->include_rss(array($rss_url=>$rss_name));
		}
		if($filter_uid) {
			$where['user_id'] = Cast::unsignint($filter_uid);
		}
		if($filter_tstamp_from) {
			$where_raw[] = sprintf('add_tstamp>=%u', $filter_tstamp_from);
		}
		if($filter_tstamp_to) {
			$where_raw[] = sprintf('add_tstamp<%u', $filter_tstamp_to);
		}
		if($filter_q) {
			$filter_q = strip_tags($filter_q);
			if(_strlen($filter_q)>2) {
				$filter_q_sql = MySQL::escape($filter_q);
				$filter_q_sql = _str_replace('%', '\%', $filter_q_sql);
				$where_raw[] = sprintf('(name LIKE \'%%%1$s%%\' OR description LIKE \'%%%1$s%%\')', $filter_q_sql);
			}
			else {
				$where_raw[] = '0';
			}
		}
		if(!is_null($filter_moderated)) {
			$where['moderated'] = Cast::unsignint($filter_moderated);
		}

		if($orderby) {
			$orderby = Cast::strarr($orderby);
			foreach($orderby as $orderfield) {
				$order[$orderfield] = _strtoupper($ordermethod);
			}
		}
		else {
			$order = array($ordermethod);
		}

		_e('# PHOTO COLLECTION');
		$photo_collection = new PhotoCollection();
		$photo_collection->getCollection($where, $where_raw, $order, $limit);
		$photo_collection->getUserObjectCollection();

		_e('# GENRE INFO');
		$genre_collection =& GenreModel::GetStaticGenreList();
		$where_genre = $where; unset($where_genre['genre_id']);
		$where_raw_genre = $where_raw;
		$photo_collection_genre = new PhotoCollection();
		$genre_count = $photo_collection_genre->getCountAggregated('genre_id', $where_genre, $where_raw_genre);
		foreach($genre_count as $g=>$c) {
			if($genre_id && $genre_id==$g) {
				$total_cnt = $c;
			}
			elseif(!$genre_id) {
				$total_cnt += $c;
			}
		}
		foreach($genre_collection as $k=>&$v) {
			$v['count'] = ifsetor($genre_count[$k], 0);
		}

		$__pager = new Pager($total_cnt, $perpare);

		_e('# USER');
		$user = new User($filter_uid);
		if($filter_uid) {
			$user = $photo_collection->getUserObjectByUserId($filter_uid);
			if($user->exists()) {
				$user->extendOccupation();

				$rss_url = UrlModel::rss_photo(array('uid'=>$user->getId()));
				$rss_name = SeoModel::rss_user_photo($user);
				$this->include_rss(array($rss_url=>$rss_name));
			}
		}


		$assign = array(
			'pager' => $__pager,
			'photo_collection' => $photo_collection,
			'user' => $user,
			'genre_collection' => $genre_collection,
			'genre_data' => $genre_data,
			'total_cnt' => $total_cnt,
			'request_query' => $request_query,
			'filter_date_from' => $filter_tstamp_from?$filter_tstamp_from:time(),
			'filter_date_to' => $filter_tstamp_to?$filter_tstamp_to:time(),
			'q_what' => 'photo',
		);

		//_e($assign);
		//_e($request_query);

		$this->include_jscal();
		$this->include_paginator();

		$this->setHtmlMetaCanonicalUrl(UrlModel::photo_lenta($request_query));
		$this->setHtmlMetaNameContent('robots', 'noarchive');

		$seo_params = array(
			'genre' => $genre_data,
			'user' => $user,
			'time_from' => $filter_tstamp_from,
			'time_to' => $filter_tstamp_to,
		);
		$this->setHtmlMetaTitle(SeoModel::photo_lenta($seo_params, SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::photo_lenta($seo_params, SeoModel::DESCRIPTION));

		return $this->layout('photo/lenta.tpl', $assign);
	}

	// ---

	private function photo_user_gallery($i_cur, $user_id, $E_max=9) {

		$i_cur = Cast::unsignint($i_cur);
		$L_max = $R_max = $E_max-1;

		$where = array('status'=>Photo::STATUS_OKE, 'user_id'=>$user_id);

		$photo_collection_left = new PhotoCollection();
		$photo_collection_left->getCollection($where, array('id<'.$i_cur), 'DESC', array(0, $L_max));

		$photo_collection_right = new PhotoCollection();
		$photo_collection_right->getCollection($where, array('id>'.$i_cur), 'ASC', array(0, $R_max));

		$i_arr = array_merge($photo_collection_left->keys(), $photo_collection_right->keys(), array($i_cur));

		sort($i_arr);

		$l_arr = $c_arr = $r_arr = array();

		$i_index = array_search($i_cur, $i_arr);

		if($i_index!==false) {
			$c_arr = array_slice($i_arr, $i_index, 1);
			$l_arr = array_slice($i_arr, 0, $i_index);
			$r_arr = array_slice($i_arr, $i_index+1);
		}

		while(true) {

			$l_cnt = count($l_arr);
			$c_cnt = count($c_arr);
			$r_cnt = count($r_arr);

			$S = $l_cnt+$c_cnt+$r_cnt;

			if($S>$E_max) {

				if($l_cnt==$r_cnt) {
					//$cut_size = ceil(($E_max-1)/2);
					$cut_size = 1;
					$l_arr = array_slice($l_arr, $cut_size);
					$r_arr = array_slice($r_arr, 0, $r_cnt-$cut_size);
					continue;
				}
				else {
					$cut_size = ceil(($S-$E_max)/2);
					if($l_cnt>$cut_size && $r_cnt>$cut_size) {
						if($l_cnt>$r_cnt) {
							$l_arr = array_slice($l_arr, $cut_size);
							continue;
						}
						if($l_cnt<$r_cnt) {
							$r_arr = array_slice($r_arr, 0, $r_cnt-$cut_size);
							continue;
						}
						//$l_arr = array_slice($l_arr, $cut_size);
					}
					elseif($l_cnt<$r_cnt) {
						$r_arr = array_slice($r_arr, 0, $r_cnt-($S-$E_max));
					}
					elseif($l_cnt>$r_cnt) {
						$l_arr = array_slice($l_arr, $S-$E_max);
					}
				}
			}
			break;
		}

		$id_arr = array_merge($l_arr, $c_arr, $r_arr);

		$photo_collection = new PhotoCollection();
		$photo_collection->getCollectionById($id_arr);

		return $photo_collection;
	}

	private function photo_upload_info(&$fileUploader, Photo $photo) {

		$image = array();

		$html_filelist = 0 ? $fileUploader->get_html_filelist() : array('photo');

		foreach($html_filelist as $html_filename) {

			while(!is_null($upload_index=$fileUploader->get_next_upload_index($html_filename))) {

				$is_error = $fileUploader->is_error($html_filename, $upload_index);

				if(!$is_error) {

					$fname = $fileUploader->get_fname($html_filename, $upload_index);
					$fpath = $fileUploader->get_fpath($html_filename, $upload_index);
					$ftype = $fileUploader->get_ftype($html_filename, $upload_index);
					$ferror = $fileUploader->get_ferror($html_filename, $upload_index);
					$fsize = $fileUploader->get_fsize($html_filename, $upload_index);

					if(Im::IsImage($fpath)) {
						$image = array(
							'fname' => $fname,
							'fpath' => $fpath,
							'ftype' => $ftype,
							'ferror' => $ferror,
							'fsize' => $fsize,
						);
						_e($image);

						$info = Im::GetImageInfo($fpath);

						if($info['width']<ThumbModel::MIN_WIDTH || $info['height']<ThumbModel::MIN_HEIGHT) {
							$photo->pushError('PHOTO_DIMENSION', array(sprintf('%ux%u', ThumbModel::MIN_WIDTH, ThumbModel::MIN_HEIGHT)));
						}

						$formats = $this->getAllowedFormats();
						if(!array_key_exists($info['type'], $formats)) {
							$photo->pushError('PHOTO_FORMAT');
						}

					}
					else {
						$photo->pushError('FILE_NOT_IMAGE', $fname);
					}
				}
				else {
					$ferror_param = $fileUploader->get_ferror_param($html_filename, $upload_index);
					list($m_error_id, $m_error_params) = array($ferror_param['id'], $ferror_param['params']);

					$photo->pushError($m_error_id, $m_error_params);
				}
			}
		}

		if(!$image) {
			$photo->pushError('PHOTO_UPLOAD');
		}

		return $image;
	}

}
