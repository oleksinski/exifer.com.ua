<?

$URL_RULES = array(

	'^/?$' => 'main.index',

	'^login$' => 'auth.login',
	'^logout$' => 'auth.logout',
	'^activate$' => 'auth.activate',
	'^remind$' => 'auth.remind',

	'^profile$' => 'user.view',
	'^profile/(\d+)(-.*)?$' => 'user.view',
	'^profile/(.+)$' => 'user.view',

	'^photo/(\d+)(-.*)?$' => 'photo.view',
	'^photo/edit$' => 'photo.edit',
	'^photo/remove$' => 'photo.remove',

	'^photos$' => 'photo.lenta',
	'^profiles$' => 'user.lenta',
	'^comments$' => 'comment.lenta',
	'^upload$' => 'photo.upload',
	'^register$' => 'user.register',
	'^edit$' => 'user.edit',

	'^json/location$' => 'rest.location',
	'^json/user_upload_date$' => 'rest.user_upload_date',
	'^json/photo_grayscale$' => 'rest.photo_grayscale',

	'^rss$' => 'rss.index',
	'^rss/photo.xml$' => 'rss.photo',
	'^rss/profile.xml$' => 'rss.user',
	'^rss/comment.xml$' => 'rss.comment',

	'^feedback$' => 'support.feedback',
	'^about$' => 'support.about',
	'^rules$' => 'support.rules',
	'^eula$' => 'support.eula',

	'^captcha(/image.jpe?g)?$' => 'rest.captcha',

	'^comment/add$' => 'comment.add',
	'^comment/get$' => 'comment.get',
	'^comment/upd$' => 'comment.upd',
	'^comment/del$' => 'comment.del',
	'^comment/clr$' => 'comment.clear',

	'^vote/add$' => 'vote.add',
	'^vote/get$' => 'vote.get',
	'^vote/del$' => 'vote.del',

	'^admin$' => 'admin.index',
	'^admin/dbg_ctrl$' => 'admin.dbg_ctrl',
	'^admin/dbg_cron$' => 'admin.dbg_cron',
	'^admin/user$' => 'admin.user',
	'^admin/photo$' => 'admin.photo',
	'^admin/location$' => 'admin.location',

	'^search$' => 'rest.search',
	'^share$' => 'rest.share',

	'^sitemap.xml$' => 'rest.sitemap',
);


if(Predicate::server_dev()) {
	$URL_RULES_DEV = array(
		'^(i/.+)$' => 'htaccess.pass',
		'^(public/.+)$' => 'htaccess.pass',
		'^(wa/.+)$' => 'htaccess.pass',
		'^crossdomain.xml$' => 'htaccess.pass',
		'^robots.txt$' => 'htaccess.pass',
	);
	$URL_RULES = array_merge($URL_RULES, $URL_RULES_DEV);
}
