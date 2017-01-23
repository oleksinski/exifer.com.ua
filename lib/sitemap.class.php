<?

abstract class Sitemap {

	const GZIP_COMPRESS = true;

	/**
	 *
	 */
	protected $maxUrlCount = 49500;
	/**
	 *
	 */
	protected $maxFileSize = 9961472; // 9.5 Mb
	/**
	 *
	 */
	protected $content;
	/**
	 *
	 */
	protected $actualUrlCount;
	/**
	 *
	 */
	protected $actualFileSize;
	/**
	 *
	 */
	protected $dirName;
	/**
	 *
	 */
	protected $urlPath;
	/**
	 *
	 */
	protected $filePrefix;
	/**
	 *
	 */
	protected $compress;

	/**
	 * @Constructor
	 */
	public function __construct($dirName='/var/tmp/sitemaps/', $urlPath='http://example.com/', $filePrefix='sitemap') {
		$this->setDirName($dirName);
		$this->setUrlPath($urlPath);
		$this->setFilePrefix($filePrefix);
		$this->clearContent();
		$this->setCompress();
	}

	/**
	 * @Destructor
	 */
	public function __destruct() {
		// write sitemap file
		$this->write();
	}

	/**
	 * Check sitemap protocol limits
	 */
	public function checkLimit() {
		$result = false;
		if($this->actualUrlCount>=$this->maxUrlCount || $this->actualFileSize>=$this->maxFileSize) {
			$this->write();
			$result = true;
		}
		return $result;
	}

	/**
	 * Write sitemap files to disk
	 */
	public function write() {
		if($this->content) {
			$fileData = '';
			foreach($this->content as $content) {
				$fileData .= $this->generateRow($content)."\n";
			}

			if($fileData) {
				$fileData = $this->getHeader() . $fileData . $this->getFooter();
				static $was = null;
				if(is_null($was)) {
					$was = true;
					$this->removeFilesByPrefix();
				}
				$filepath = $this->getNextFilePath();

				if($this->isCompress()) {
					$fileData = gzencode($fileData, 9, FORCE_GZIP);
				}

				FileFunc::saveFile($filepath, $fileData);
			}
		}
		$this->clearContent();
	}

	public function removeFilesByPrefix() {
		$files = FileFunc::readDirFiles($this->dirName);
		$files2remove = array();
		foreach($files as $file) {
			if($this->checkFilenamePattern($file)) {
				$files2remove[] = $file;
			}
		}
		foreach($files2remove as $file) {
			_e('delete '.$file);
			unlink($file);
		}
	}

	/**
	 *
	 */
	public function clearContent() {
		$this->content = array();
		$this->actualUrlCount=0;
		$this->actualFileSize=_strlen($this->getHeader())+_strlen($this->getFooter());
	}

	/**
	 *
	 * @param unknown_type $dirName
	 * @return Sitemap
	 */
	public function setDirName($dirName) {
		$this->dirName = $dirName;
		return $this;
	}

	/**
	 *
	 */
	public function getDirName() {
		return $this->dirName;
	}

	/**
	 *
	 * @param unknown_type $urlPath
	 * @return Sitemap
	 */
	public function setUrlPath($urlPath) {
		$this->urlPath = $urlPath;
		return $this;
	}

	/**
	 *
	 */
	public function getUrlPath() {
		return $this->urlPath;
	}

	/**
	 *
	 * @param unknown_type $filePrefix
	 * @return Sitemap
	 */
	public function setFilePrefix($filePrefix) {
		$this->filePrefix = $filePrefix;
		return $this;
	}

	/**
	 *
	 */
	public function getfilePrefix() {
		return $this->filePrefix;
	}

	/**
	 *
	 * @param unknown_type $value
	 * @return Sitemap
	 */
	public function setMaxUrlCount($value) {
		$this->maxUrlCount = $value;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getMaxUrlCount() {
		return $this->maxUrlCount;
	}

	/**
	 *
	 * @param unknown_type $value
	 * @return Sitemap
	 */
	public function setMaxFileSize($value) {
		$this->maxFileSize = $value;
		return $this;
	}

	/**
	 * @return integer
	 */
	public function getMaxFileSize() {
		return $this->maxFileSize;
	}

	/**
	 *
	 * @param unknown_type $compress
	 */
	public function setCompress($compress=self::GZIP_COMPRESS) {
		$this->compress = (bool)$compress;
	}

	/**
	 *
	 */
	public function getCompress() {
		return $this->compress;
	}

	/**
	 *
	 */
	public function isCompress() {
		return (bool)$this->compress && function_exists('gzencode');
	}

	/**
	 *
	 */
	private function getNextFileName() {
		$filepath = $this->getNextFilePath();
		$filename = basename($filepath);
		return $filename;
	}

	/**
	 *
	 */
	private function getNextFilePath() {
		$filepath = null;
		$index = 0;
		while(true) {
			$index++;
			$filepath = Url::fix($this->dirName.'/'.$this->buildNextFileName($index));
			if(!file_exists($filepath)) {
				break;
			}
		}
		return $filepath;
	}

	/**
	 *
	 */
	private function buildNextFileName($index=0) {
		$filename = sprintf('%s%d.xml', $this->filePrefix, $index);
		if($this->isCompress()) {
			$filename .= '.gz';
		}
		return $filename;
	}

	/**
	 *
	 */
	public function getFilenamePattern() {
		return sprintf('/^%s\d+.*$/', $this->filePrefix);
	}

	/**
	 *
	 * @param unknown_type $filename
	 * @return bool
	 */
	public function checkFilenamePattern($filepath) {
		$filename = basename($filepath);
		$result = Regexp::match($this->getFilenamePattern(), $filename)!==false;
		return $result;
	}

	/**
	 * Get normalized value of changefreq field
	 * @param unknown_type $value
	 * @return string
	 */
	public function getChangefreq($value=null) {
		return insetor($value, array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'), null);
	}

	/**
	 * Normalize priority tag value. Possible values: [0; 1.0]
	 * @param unknown_type $value
	 * @return string
	 */
	public function getPriority($value=null) {
		$value = (float)$value;
		if($value<0 || $value>1) {
			$value = 0.5;
		}
		//$value = sprintf('%01.1f', $value); // locale dependent (used "," instead of ".")
		$value = number_format($value, 1, '.', '');
		return $value;
	}

	/**
	 * Generate Sitemap header
	 */
	abstract public function getHeader();

	/**
	 * Generate Sitemap footer
	 */
	abstract public function getFooter();

	/**
	 * Generate XML sitemap row
	 * @param array $content
	 */
	abstract public function generateRow(array $row);

	/**
	 *
	 * @param array $content
	 */
	final public function addContent(array $content) {
		$result = false;
		if($content) {
			$row = $this->generateRow($content);
			if($row) {
				$this->checkLimit();
				$this->actualUrlCount++;
				$this->actualFileSize += _strlen($row);
				$this->content[] = $content;
				$result = true;
			}
		}
		return $result;
	}

}

/**

http://www.sitemaps.org/protocol.php
http://www.google.com/support/webmasters/bin/answer.py?answer=156184
---

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>http://www.example.com/</loc>
		<lastmod>2005-01-01</lastmod>
		<changefreq>monthly</changefreq>
		<priority>0.8</priority>
	</url>
	<url>
		<loc>http://www.example.com/</loc>
		<lastmod>2005-01-01</lastmod>
		<changefreq>monthly</changefreq>
		<priority>0.8</priority>
	</url>
</urlset>

*/
class SitemapRegular extends Sitemap {

	/**
	 * @override
	 * @see Sitemap::getHeader()
	 */
	public function getHeader() {
		$headerList = array(
			'<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
			'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9',
			'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"',
			'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
		);
		$content = array(
			SitemapUtil::getXmlHeader(),
		);
		if(Predicate::server_pro()) {
			$content = array_merge($content, array(sprintf('<?xml-stylesheet type="text/xsl" href="%s/gss.xsl"?>', $this->getUrlPath())));
		}
		$content = array_merge($content, array(
			implode(' ', $headerList),
			sprintf('<!-- Last sitemap update %s -->', SitemapUtil::getDateFormat(time())),
		));
		$header = implode("\n", $content)."\n";
		return $header;
	}

	/**
	 * @override
	 * @see Sitemap::getFooter()
	 */
	public function getFooter() {
		return '</urlset>';
	}

	/**
	 * @override
	 * @see Sitemap::generateRow()
	 *
	 * $row = array(
	 *  'loc' => '', // required
	 *  'lastmod' => '', // optional
	 *  'changefreq' => '', // optional
	 *  'priority' => '', // optional
	 * );
	 */
	public function generateRow(array $row) {
		$result = null;
		$content = array();
		if(isset($row['loc'])) {
			$content[] = "\t<url>";
			$content[] = sprintf("\t\t<loc>%s</loc>", SitemapUtil::urlEscape($row['loc']));
			if(isset($row['lastmod']) && $row['lastmod']) {
				$content[] = sprintf("\t\t<lastmod>%s</lastmod>", SitemapUtil::getDateFormat($row['lastmod']));
			}
			if(isset($row['changefreq']) && $row['changefreq']) {
				$content[] = sprintf("\t\t<changefreq>%s</changefreq>", $this->getChangefreq($row['changefreq']));
			}
			if(isset($row['priority']) && $row['priority']) {
				$content[] = sprintf("\t\t<priority>%s</priority>", $this->getPriority($row['priority']));
			}
			$content[] = "\t</url>";
		}
		$result = implode("\n", $content);
		return $result;
	}
}

/**

http://www.google.com/support/webmasters/bin/answer.py?answer=178636
---

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
	<url>
		<loc>http://example.com/sample.html</loc>
		<image:image>
			<image:loc>http://example.com/image.jpg</image:loc>
			<image:caption>Text caption</image:caption>
			<image:geo_location>Limerick, Ireland</image:geo_location>
			<image:title>Image title</image:title>
			<image:license>A URL to the license of the image</image:license>
		</image:image>
		<image:image>
			<image:loc>http://example.com/image.jpg</image:loc>
		</image:image>
	</url>
</urlset>

*/
class SitemapImage extends Sitemap {

	/**
	 * @override
	 * @see Sitemap::getHeader()
	 */
	public function getHeader() {
		$headerList = array(
			'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
			'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'
		);
		$content = array(
			SitemapUtil::getXmlHeader(),
			sprintf('<!-- Last sitemap update %s -->', SitemapUtil::getDateFormat(time())),
			implode(' ', $headerList),
		);
		$header = implode("\n", $content);
		return $header;
	}

	/**
	 * @override
	 * @see Sitemap::getFooter()
	 */
	public function getFooter() {
		return '</urlset>';
	}

	/**
	 * @override
	 * @see Sitemap::generateRow()
	 *
	 * $row = array(
	 *  array(
	 *   'loc' => '', // required
	 *   'image' => array(
	 *     'loc' => '', // required
	 *     'caption' => '', // optional
	 *     'geo_location' => '', // optional
	 *     'title' => '', // optional
	 *     'license' => '', // optional
	 *   ),
	 *  ),
	 *  array(
	 *   'loc' => '', // required
	 *   'image' => '', // required
	 *   'caption' => '', // optional
	 *   'title' => '', // optional
	 *  ),
	 */
	public function generateRow(array $row) {
		$result = null;
		$content = array();
		if(isset($row['loc']) && isset($row['image'])) {
			$content[] = "<url>";
			$content[] = sprintf("\t<loc>%s</loc>", SitemapUtil::urlEscape($row['loc']));
			if(is_array($row['image'])) {
				foreach($row['image'] as $image) {
					if(isset($image['loc']) && $image['loc']) {
						$content[] = "\t<image:image>";
						$content[] = sprintf("\t\t<image:loc>%s</image:loc>", SitemapUtil::urlEscape($image['loc']));
						if(isset($image['caption']) && $image['caption']) {
							$content[] = sprintf("\t\t<image:caption>%s</image:caption>", SitemapUtil::xmlEscape($image['caption']));
						}
						if(isset($image['geo_location']) && $image['geo_location']) {
							$content[] = sprintf("\t\t<image:geo_location>%s</image:geo_location>", SitemapUtil::xmlEscape($image['geo_location']));
						}
						if(isset($image['title']) && $image['title']) {
							$content[] = sprintf("\t\t<image:title>%s</image:title>", SitemapUtil::xmlEscape($image['title']));
						}
						if(isset($image['license']) && $image['license']) {
							$content[] = sprintf("\t\t<image:license>%s</image:license>", SitemapUtil::urlEscape($image['license']));
						}
						$content[] = "\t</image:image>";
					}
				}
			}
			elseif(is_scalar($row['image']) && $row['image']) {
				$content[] = "\t<image:image>";
				$content[] = sprintf("\t\t<image:loc>%s</image:loc>", SitemapUtil::urlEscape($row['image']));
				if(isset($row['caption']) && $row['caption']) {
					$content[] = sprintf("\t\t<image:caption>%s</image:caption>", SitemapUtil::xmlEscape($row['caption']));
				}
				if(isset($row['title']) && $row['title']) {
					$content[] = sprintf("\t\t<image:title>%s</image:title>", SitemapUtil::xmlEscape($row['title']));
				}
				$content[] = "\t</image:image>";
			}
			$content[] = "</url>";
		}
		$result = implode("\n", $content);
		return $result;
	}
}

/**

http://www.sitemaps.org/protocol.php
---

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<sitemap>
		<loc>http://www.example.com/sitemap1.xml.gz</loc>
		<lastmod>2004-10-01T18:23:17+00:00</lastmod>
	</sitemap>
	<sitemap>
		<loc>http://www.example.com/sitemap1.xml.gz</loc>
		<lastmod>2004-10-01T18:23:17+00:00</lastmod>
	</sitemap>
</sitemapindex>

*/
abstract class SitemapIndex {

	protected $sitemaps = array();

	/**
	 * @Constructor
	 * @param Sitemap $sitemap
	 */
	public function __construct(/* Sitemap args */) {
		$sitemaps = func_get_args();
		foreach($sitemaps as $sitemap) {
			if(is_a($sitemap, 'Sitemap')) {
				$this->sitemaps[] = $sitemap;
			}
		}
	}

	/**
	 *
	 */
	public function getHeader() {
		$headerList = array(
			'<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
			'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"',
			'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
		);
		$content = array(
			SitemapUtil::getXmlHeader(),
			implode(' ', $headerList),
		);
		$header = implode("\n", $content)."\n";
		return $header;
	}

	/**
	 *
	 */
	public function getFooter() {
		return '</sitemapindex>';
	}

	/**
	 * @param array $row
	 */
	public function generateRow(array $row) {
		$result = null;
		$content = array();
		if(isset($row['loc']) && $row['loc']) {
			$content[] = "\t<sitemap>";
			$content[] = sprintf("\t\t<loc>%s</loc>", SitemapUtil::urlEscape($row['loc']));
			if(isset($row['lastmod']) && $row['lastmod']) {
				$content[] = sprintf("\t\t<lastmod>%s</lastmod>", SitemapUtil::getDateFormat($row['lastmod']));
			}
			$content[] = "\t</sitemap>";
		}
		$result = implode("\n", $content);
		return $result;
	}

	/**
	 *
	 */
	public function generate() {

		$result = '';

		$result .= $this->getHeader();

		foreach($this->sitemaps as $sitemap) {

			$fileList = FileFunc::readDirFiles($sitemap->getDirName());

			foreach($fileList as $filepath) {

				if($sitemap->checkFilenamePattern($filepath)) {

					$row = array(
						'loc' => $this->getUrlFilePath($sitemap, $filepath),
						'lastmod' => filemtime($filepath)
					);

					$result .= $this->generateRow($row)."\n";
				}
			}
		}

		$result .= $this->getFooter();

		return $result;
	}

	/**
	 *
	 * @param unknown_type $sitemap
	 * @param unknown_type $filepath
	 */
	protected function getLocalFilePath(Sitemap $sitemap, $filepath) {
		$filepath = _str_replace($sitemap->getDirName(), '', $filepath);
		$filepath = $sitemap->getDirName() . $filepath;
		return $filepath;
	}

	/**
	 *
	 * @param unknown_type $sitemap
	 * @param unknown_type $filepath
	 */
	protected function getUrlFilePath(Sitemap $sitemap, $filepath) {
		$filepath = $this->getLocalFilePath($sitemap, $filepath);
		$filepath = _str_replace($sitemap->getDirName(), $sitemap->getUrlPath(), $filepath);
		return $filepath;
	}
}

/**
 *
 * @author alex
 *
 */
class SitemapUtil {

	const DATE_FORMAT_ISO8601 = 'c';
	const DATE_FORMAT_DATESTAMP = 'Y-m-d';

	/**
	 * @param void
	 */
	public static function getXmlHeader() {
		return '<?xml version="1.0" encoding="UTF-8"?>';
	}

	/**
	 * Get Date in W3C DateTime format http://www.w3.org/TR/NOTE-datetime
	 * default format = c : ISO 8601 date [2004-02-12T15:19:21+00:00]
	 * @param int $timestamp
	 * @param unknown_type $format
	 * @return string W3C DateTime
	 */
	public static function getDateFormat($time, $format=self::DATE_FORMAT_ISO8601) {
		$result = null;
		$time = is_numeric($time) ? $time : strtotime($time);
		if($time) {
			$result = date($format, $time);
		}
		return $result;
	}

	/**
	 *
	 * @param unknown_type $value
	 */
	public static function xmlEscape($value) {
		if(1) {
			$search = array('&', '\'' , '"', '>', '<');
			$replace = array('&amp;', '&apos;' , '&quot;', '&gt;', '&lt;');
			$value = _str_replace($search, $replace, $value);
		}
		elseif(0) {
			$value = '<![CDATA[' . $value . ']]>';
		}
		elseif(0) {
			$value = Text::smartXmlSpecialChars($value);
		}
		elseif(0) {
			$value = _htmlspecialchars($value);
		}
		return $value;
	}

	/**
	 * Escape URLs follow the RFC-3986 standard
	 * @param unknown_type $value
	 * @return string
	 * @TODO: check xmlEscape after urlEscape
	 */
	public static function urlEscape($value) {
		if(1) {
			$value = '<![CDATA[' . $value . ']]>';
		}
		elseif(0) {
			$value = rawurlencode($value);
		}
		return $value;
	}

}
