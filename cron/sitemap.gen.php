<?

require_once(dirname(__FILE__).'/header.exec.php');

// === Regular Sitemaps === //

$photo_collection = new PhotoCollection();
$photo_collection->getCollection(array('status'=>Photo::STATUS_OKE), array(), 'DESC', 1);
$photo = $photo_collection->getFirst();

$user_collection = new UserCollection();
$user_collection->getCollection(array('status'=>User::STATUS_OKE), array(), 'DESC', 1);
$user = $user_collection->getFirst();

$comment_collection = new CommentPhotoCollection();
$comment_collection->getCollection(array(), array(), 'DESC', 1);
$comment = $comment_collection->getFirst();

$contents = array(
	array(
		'loc' => UrlModel::homepage(),
		'lastmod' => time(),
		'changefreq' => 'hourly',
		'priority' => '0.8',
	),
	array(
		'loc' => UrlModel::photo_lenta(),
		'lastmod' => ifsetor($photo->getField('add_tstamp'), time()),
		'changefreq' => 'daily',
		'priority' => '0.5',
	),
	array(
		'loc' => UrlModel::user_lenta(),
		'lastmod' => ifsetor($user->getField('reg_tstamp'), time()),
		'changefreq' => 'daily',
		'priority' => '0.5',
	),
	array(
		'loc' => UrlModel::comment_lenta(),
		'lastmod' => ifsetor($comment->getField('add_tstamp'), time()),
		'changefreq' => 'daily',
		'priority' => '0.5',
	),
	array(
		'loc' => UrlModel::user_register(),
		'lastmod' => @filemtime(TPL_PATH.'user/register_edit.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.5',
	),
	array(
		'loc' => UrlModel::auth_login(),
		'lastmod' => @filemtime(TPL_PATH.'auth/login.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.3',
	),
	array(
		'loc' => UrlModel::auth_remind(),
		'lastmod' => @filemtime(TPL_PATH.'auth/remind.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.3',
	),
	array(
		'loc' => UrlModel::rss(),
		'lastmod' => @filemtime(TPL_PATH.'rss/index.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.3',
	),
	array(
		'loc' => UrlModel::support_feedback(),
		'lastmod' => @filemtime(TPL_PATH.'support/feedback.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.3',
	),
	array(
		'loc' => UrlModel::support_eula(),
		'lastmod' => @filemtime(TPL_PATH.'support/eula.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.3',
	),
	array(
		'loc' => UrlModel::support_about(),
		'lastmod' => @filemtime(TPL_PATH.'support/about.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.3',
	),
	array(
		'loc' => UrlModel::support_rules(),
		'lastmod' => @filemtime(TPL_PATH.'support/rules.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.3',
	),
	/*
	array(
		'loc' => UrlModel::support_adult(),
		'lastmod' => @filemtime(TPL_PATH.'support/adult.tpl'),
		'changefreq' => 'monthly',
		'priority' => '0.3',
	),
	*/
);

$sitemap = new SitemapRegularModel();
foreach($contents as $data) {
	$sitemap->addContent($data);
}

// === Photo Sitemaps, Photo Image Sitemaps === //

$sitemap = new SitemapRegularPhotoModel();
$sitemapImage = new SitemapImagePhotoModel();

$i=0;

while(true) {
	$photo_collection = new PhotoCollection();
	$photo_collection->getCollection(array(), array(), 'ASC', array($i++, 100));
	$photo_collection->getUserObjectCollection()->extendOccupationCollection();
	//$photo_collection->extendThumbCollection();
	if(!$photo_collection->length()) {
		break;
	}
	foreach($photo_collection as $photo_id=>$photo) {
		//_e($photo);
		$lastmod = ifsetor($photo->getField('update_tstamp'), $photo->getField('add_tstamp'), true);
		$data = array(
			'loc' => UrlModel::photo($photo_id, $photo),
			'lastmod' => $lastmod,
			'changefreq' => 'weekly',
			'priority' => SitemapCron::getPriority($lastmod),
		);
		$sitemap->addContent($data);

		// Image Sitemaps
		$thumbList = ifsetor($photo->getExtraField('thumb'), array());
		if(is_array($thumbList)) {
			$dataImage = array(
				'loc' => $data['loc'],
				'image' => array(),
			);
			foreach($thumbList as $thumb_format=>$thumb) {
				$dataImage['image'][] = array(
					'loc' => $thumb['src'],
					'caption' => SeoModel::htmlToRawText(SeoModel::photo($photo, SeoModel::TITLE)),
					'title' => $photo->getField('name'),
				);
			}
			if($dataImage['image']) {
				$sitemapImage->addContent($dataImage);
			}
		}
	}
}

// === User Sitemaps, User Image Sitemaps === //

$sitemap = new SitemapRegularUserModel();
$sitemapImage = new SitemapImageUserModel();

$i=0;

$userFormatList = UserpicModel::GetFormatValueList();
$userBlankWebList = array();
foreach($userFormatList as $userFormat) {
	$userBlankWebList[] = UserpicModel::GetBlankWebPath($userFormat);
}

while(true) {
	$user_collection = new UserCollection();
	$user_collection->getCollection(array(), array(), 'ASC', array($i++, 100));
	$user_collection->extendOccupationCollection();
	$user_collection->extendThumbCollection();
	if(!$user_collection->length()) {
		break;
	}
	foreach($user_collection as $user_id=>$user) {
		$lastmod = ifsetor($user->getField('update_tstamp'), $user->getField('reg_tstamp'), true);
		$data = array(
			'loc' => UrlModel::user($user_id, $user),
			'lastmod' => $lastmod,
			'changefreq' => 'weekly',
			'priority' => SitemapCron::getPriority($lastmod),
		);
		$sitemap->addContent($data);

		// Image Sitemaps
		$userpicList = ifsetor($user->getExtraField('userpic'), array());
		if(is_array($userpicList)) {
			$dataImage = array(
				'loc' => $data['loc'],
				'image' => array(),
			);
			foreach($userpicList as $userpic_format=>$userpic) {
				if(!in_array($userpic['src'], $userBlankWebList)) {
					$dataImage['image'][] = array(
						'loc' => $userpic['src'],
						'caption' => SeoModel::htmlToRawText(SeoModel::user($user, SeoModel::TITLE)),
						'title' => $user->getField('name'),
					);
				}
			}
			if($dataImage['image']) {
				$sitemapImage->addContent($dataImage);
			}
		}
	}
}

// ======================================

class SitemapCron {

	function getPriority($lastmod=null) {
		$priority = 0.5;
		$day = 24*3600;
		$week = 7*$day;
		$time = time();
		$lastmod = is_numeric($lastmod) ? $lastmod : 0;
		if($lastmod>($time-$day)) {
			$priority = 0.9;
		}
		elseif($lastmod>($time-$week)) {
			$priority = 0.8;
		}
		elseif($lastmod>($time-$week*2)) {
			$priority = 0.7;
		}
		elseif($lastmod>($time-$week*4)) {
			$priority = 0.6;
		}
		else {
			$priority = 0.5;
		}
		return $priority;
	}
}
