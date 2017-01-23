<?

define('SITEMAP_DIR', I_PATH.'sitemap/');
define('SITEMAP_URL', I_URL.'sitemap/');

class SitemapRegularModel extends SitemapRegular {

	public function __construct($prefix='main') {
		parent::__construct(SITEMAP_DIR, SITEMAP_URL, $prefix);
		$this->setCompress(false);
	}
}

class SitemapImageModel extends SitemapImage {

	public function __construct($prefix='image') {
		parent::__construct(SITEMAP_DIR, SITEMAP_URL, $prefix);
		$this->setCompress(true);
	}

}

// ---------------------------------------------------

class SitemapRegularPhotoModel extends SitemapRegularModel {
	public function __construct() {
		parent::__construct('photo');
	}
}

class SitemapRegularUserModel extends SitemapRegularModel {
	public function __construct() {
		parent::__construct('user');
	}
}

// ---------------------------------------------------

class SitemapImagePhotoModel extends SitemapImageModel {
	public function __construct() {
		parent::__construct('image_photo');
	}
}

class SitemapImageUserModel extends SitemapImageModel {
	public function __construct() {
		parent::__construct('image_user');
	}
}

// ---------------------------------------------------

class SitemapIndexModel extends SitemapIndex {

	public function __construct() {
		parent::__construct(
			new SitemapRegularModel(),
			new SitemapRegularPhotoModel(),
			new SitemapRegularUserModel(),
			new SitemapImagePhotoModel(),
			new SitemapImageUserModel()
		);
	}

}