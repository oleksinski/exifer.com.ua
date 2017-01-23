<?

//function exception_error_handler($exception) {
//	echo $exception->getMessage();
//}
//set_error_handler('exception_error_handler');

spl_autoload_register('my__autoload');
ini_set('mbstring.internal_encoding', 'utf-8');
@header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
mb_regex_set_options('pnz');

// --= Shortcut User Defined Function List =-- //
require_once(FUNC_PATH . 'shortcut.func.php');

// --= UTF-8 Function List =-- //
require_once(FUNC_PATH . 'utf8.func.php');

// --= Init Debug Message Handler =-- //
//global $__debug;
//$__debug =& Debug::getInstance();


// --= Init Locale =-- //
$__locale =& __locale();
//if(!$__locale->getId()) $__locale->initById();

// --= Init Online User Session =-- //
User::retrieveSession();

// --= Global Autoload Function =-- //
function my__autoload($classname) {

	if(!class_exists($classname)) {

		$modelname = _str_replace('Model', '', $classname);
		$controlname = _str_replace('Control', '', $classname);
		$interfacename = _str_replace('Interface', '', $classname);

		switch($classname) {

			case 'AtomicObjectIterator':
			case 'AtomicObjectIteratorCollection':
				$filepath = LIB_PATH . 'atom_iterator.class.php';
				break;

			case 'AtomicObject':
			case 'AtomicObjectCollection':
				$filepath = LIB_PATH . 'atom_object.class.php';
				break;

			case 'Object':
			case 'ObjectCollection':
				$filepath = MODEL_PATH . 'object.model.php';
				break;

			case 'Photo':
			case 'PhotoCollection':
				$filepath = MODEL_PATH . 'photo.model.php';
				break;

			case 'PhotoDeleted':
			case 'PhotoDeletedCollection':
				$filepath = MODEL_PATH . 'photo_deleted.model.php';
				break;

			case 'PhotoUserView':
			case 'PhotoUserViewCollection':
				$filepath = MODEL_PATH . 'photo_user_view.model.php';
				break;

			case 'User':
			case 'UserCollection':
				$filepath = MODEL_PATH . 'user.model.php';
				break;

			case 'UserDeleted':
			case 'UserDeletedCollection':
				$filepath = MODEL_PATH . 'user_deleted.model.php';
				break;

			case 'UserRegister':
			case 'UserRegisterCollection':
				$filepath = MODEL_PATH . 'user_register.model.php';
				break;

			case 'UserOnline':
			case 'UserOnlineCollection':
				$filepath = MODEL_PATH . 'user_online.model.php';
				break;

			case 'UserSession':
				$filepath = MODEL_PATH . 'user_session.model.php';
				break;

			//case 'Comment':
			case 'CommentCollection':
			case 'CommentRate':
			case 'CommentRateCollection':
			case 'CommentKarma':
			case 'CommentKarmaCollection':
			case 'CommentSubscribe':
			case 'CommentSubscribeCollection':
				$filepath = LIB_PATH . 'comment.class.php';
				break;

			//case 'Article':
			case 'ArticleCollection':
				$filepath = LIB_PATH . 'article.class.php';
				break;

			case 'CommentPhoto':
			case 'CommentPhotoCollection':
			case 'CommentPhotoRate':
			case 'CommentPhotoRateCollection':
			case 'CommentPhotoKarma':
			case 'CommentPhotoKarmaCollection':
			case 'CommentPhotoSubscribe':
			case 'CommentPhotoSubscribeCollection':
				$filepath = MODEL_PATH . 'comment_photo.model.php';
				break;

			case 'VotePhoto':
			case 'VotePhotoCollection':
				$filepath = MODEL_PATH . 'vote_photo.model.php';
				break;

			case 'SimpleHttp':
				$filepath = LIB_PATH_EXT.'simple_http/simple_http.class.php';
				break;

			case 'PhpCaptcha':
				$filepath = LIB_PATH_EXT.'php_captcha/php_captcha.class.php';
				break;

			case 'SimpleRss':
				$filepath = LIB_PATH_EXT.'simple_rss/simple_rss.class.php';
				break;

			case 'Services_JSON':
				$filepath = LIB_PATH_EXT.'json/services_json.class.php';
				break;

			case 'SafeHTML':
				$filepath = LIB_PATH_EXT . 'safehtml_drupal/safehtml.php';
				break;

			case 'StorageAtomic':
				$filepath = LIB_PATH . 'storage_atomic.class.php';
				break;

			case 'StorageMysql':
				$filepath = LIB_PATH . 'storage_mysql.class.php';
				break;

			case 'StorageFilecache':
				$filepath = LIB_PATH . 'storage_filecache.class.php';
				break;

			case 'StorageMemcache':
				$filepath = LIB_PATH . 'storage_memcache.class.php';
				break;

			case 'SessionPhp':
				$filepath = LIB_PATH . 'session_php.class.php';
				break;

			case 'Smarty':
				$filepath = SMARTY_LIB . 'Smarty.class.php';
				break;

			case 'CodeCompressor':
				$filepath = LIB_PATH_EXT . 'html_compressor/codecompressor.class.php';
				break;

			case 'Object_Freezer':
				$filepath = LIB_PATH_EXT . 'object_freezer/object_freezer.class.php';
				break;

			case 'UniversalFeedCreator':
			case 'FeedCreator':
			case 'FeedTextInput':
			case 'FeedImage':
			case 'FeedCategory':
			case 'FeedDate':
			case 'FeedItem':
			case 'FeedItemGuid':
			case 'FeedItemSource':
			case 'FeedItemEnclosure':
			case 'HtmlDescribable':
			case 'FeedHtmlField':
				$filepath = LIB_PATH_EXT.'www.bitfolge.de/feed_creator/feedcreator.class.php';
				break;

			case 'ExiferAtom':
				$filepath = LIB_PATH.'exifer.class.php';
				break;

			case 'PHPMailer':
				$filepath = LIB_PATH_EXT.'php_mailer/PHPMailer_v5.0.2/class.phpmailer.php';
				break;
			case 'SMTP':
				$filepath = LIB_PATH_EXT.'php_mailer/PHPMailer_v5.0.2/class.smtp.php';
				break;
			case 'POP3':
				$filepath = LIB_PATH_EXT.'php_mailer/PHPMailer_v5.0.2/class.pop3.php';
				break;

			case 'Sitemap':
			case 'SitemapIndex':
			case 'SitemapRegular':
			case 'SitemapImage':
			case 'SitemapUtil':
				$filepath = LIB_PATH.'sitemap.class.php';
				break;

			case 'SitemapRegularModel':
			case 'SitemapImageModel':
			case 'SitemapIndexModel':
			case 'SitemapRegularPhotoModel':
			case 'SitemapRegularUserModel':
			case 'SitemapImagePhotoModel':
			case 'SitemapImageUserModel':
				$filepath = MODEL_PATH.'sitemap.model.php';
				break;

			case 'ArticleBimbo':
			case 'ArticleBimboCollection':
			case 'CommentBimbo':
			case 'CommentBimboCollection':
			case 'CommentRateBimbo':
			case 'CommentRateBimboCollection':
			case 'CommentKarmaBimbo':
			case 'CommentKarmaBimboCollection':
			case 'CommentSubscribeBimbo':
			case 'CommentSubscribeBimboCollection':
			case 'VoteBimbo':
			case 'VoteBimboCollection':
				$filepath = LIB_PATH . 'bimbo.class.php';
				break;

			case 'SocialShare':
			case 'SocialShareCollection':
				$filepath = MODEL_PATH . 'social_share.model.php';
				break;

			default:
				if($classname!=$interfacename) {
					$filepath = INTERFACE_PATH . sprintf('%s.interface.php', _strtolower($interfacename));
				}
				elseif($classname!=$modelname) {
					$filepath = MODEL_PATH . sprintf('%s.model.php', _strtolower($modelname));
				}
				elseif($classname!=$controlname) {
					$filepath = CONTROL_PATH . sprintf('%s.control.php', _strtolower($controlname));
				}
				else {
					$filepath = LIB_PATH . sprintf('%s.class.php', _strtolower($classname));
				}
		}

		$filepath = _str_replace('\\', '/', $filepath);

		if(!_strstr($filepath, 'smarty_internal_')) {
			AutoloadList::put($classname, $filepath);
			require_once($filepath);
		}
	}
}


// --= Collection of Autoloaded Classes Class-Filepath =-- //
class AutoloadList {

	private static $list=array();

	public static function put($classname, $filepath) {
		self::$list[$classname] = $filepath;
	}

	public static function get($classname) {
		return ifsetor(self::$list[$classname], null);
	}

	public static function getList() {
		return self::$list;
	}
}
