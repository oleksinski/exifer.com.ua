<?

/***************************************************************************

FeedCreator class v1.8
originally (c) Kai Blankenhorn
www.bitfolge.de
kaib@bitfolge.de
v1.3 work by Scott Reynen (scott@randomchaos.com) and Kai Blankenhorn
v1.5 OPML support by Dirk Clemens

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

****************************************************************************


Changelog:

v1.8	01-06-2009
	Changes made under RSS2.0 within RSS2.0 specification:
	extended channel-image
	added channel-category
	added channel-skipHours
	added channel-skipDays
	added channel-cloud
	added channel-textInput
	added item-source
	added item-enclosure
	added item-category
	added comments (RSS specification)

v1.7.2	10-11-04
	license changed to LGPL

v1.7.1
	fixed a syntax bug
	fixed left over debug code

v1.7	07-18-04
	added HTML and JavaScript feeds (configurable via CSS) (thanks to Pascal Van Hecke)
	added HTML descriptions for all feed formats (thanks to Pascal Van Hecke)
	added a switch to select an external stylesheet (thanks to Pascal Van Hecke)
	changed default content-type to application/xml
	added character encoding setting
	fixed numerous smaller bugs (thanks to Sцren Fuhrmann of golem.de)
	improved changing ATOM versions handling (thanks to August Trometer)
	improved the UniversalFeedCreator's useCached method (thanks to Sцren Fuhrmann of golem.de)
	added charset output in HTTP headers (thanks to Sцren Fuhrmann of golem.de)
	added Slashdot namespace to RSS 1.0 (thanks to Sцren Fuhrmann of golem.de)

v1.6	05-10-04
	added stylesheet to RSS 1.0 feeds
	fixed generator comment (thanks Kevin L. Papendick and Tanguy Pruvot)
	fixed RFC822 date bug (thanks Tanguy Pruvot)
	added TimeZone customization for RFC8601 (thanks Tanguy Pruvot)
	fixed Content-type could be empty (thanks Tanguy Pruvot)
	fixed author/creator in RSS1.0 (thanks Tanguy Pruvot)

v1.6 beta	02-28-04
	added Atom 0.3 support (not all features, though)
	improved OPML 1.0 support (hopefully - added more elements)
	added support for arbitrary additional elements (use with caution)
	code beautification :-)
	considered beta due to some internal changes

v1.5.1	01-27-04
	fixed some RSS 1.0 glitches (thanks to Stйphane Vanpoperynghe)
	fixed some inconsistencies between documentation and code (thanks to Timothy Martin)

v1.5	01-06-04
	added support for OPML 1.0
	added more documentation

v1.4	11-11-03
	optional feed saving and caching
	improved documentation
	minor improvements

v1.3    10-02-03
	renamed to FeedCreator, as it not only creates RSS anymore
	added support for mbox
	tentative support for echo/necho/atom/pie/???

v1.2    07-20-03
	intelligent auto-truncating of RSS 0.91 attributes
	don't create some attributes when they're not set
	documentation improved
	fixed a real and a possible bug with date conversions
	code cleanup

v1.1    06-29-03
	added images to feeds
	now includes most RSS 0.91 attributes
	added RSS 2.0 feeds

v1.0    06-24-03
	initial release


***************************************************************************
*          A little setup                                                 *
**************************************************************************/


// your local timezone, set to "" to disable or for GMT
define("FEED_TIME_ZONE", ""); //"+01:00"


/**
 * Version string.
 **/
define("FEEDCREATOR_VERSION", "FeedCreator 1.8");



/**
 * FeedCreator is the abstract base implementation for concrete
 * implementations that implement a specific format of syndication.
 *
 * @abstract
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @since 1.4
 */
class FeedCreator extends HtmlDescribable {

	/**
	 * ======== Mandatory attributes of a feed. ========
	 */

	/**
	 * The name of the channel. It's how people refer to your service.
	 */
	var $title;

	/**
	 * Phrase or sentence describing the channel.
	 */
	var $link;

	/**
	 * The URL to the HTML website corresponding to the channel.
	 */
	var $description;


	/**
	 * ======== Optional attributes of a feed. ========
	 */

	/**
	 * Specifies a GIF, JPEG or PNG image that can be displayed with the channel.
	 * @type object FeedImage
	 */
	var $image;

	/**
	 * The language the channel is written in. Example: en-US.
	 * Possible language-values: http://cyber.law.harvard.edu/rss/languages.html
	 */
	var $language;

	/**
	 * Copyright notice for content in the channel.
	 */
	var $copyright;

	/**
	 * RSS2.0 - Name + Email address for person responsible for editorial content.
	 */
	var $managingEditor;

	/**
	 * Email address for person responsible for technical issues relating to channel.
	 */
	var $webMaster;

	/**
	 * The publication date for the content in the channel.
	 * All date-times in RSS conform to the Date and Time Specification of RFC 822.
	 */
	var $pubDate;

	/**
	 * The last time the content of the channel changed.
	 * Date and Time Specification of RFC 822
	 */
	var $lastBuildDate;

	/**
	 * Specify one or more categories that the channel belongs to.
	 * Follows the same rules as the <item>-level category element.
	 * @type object FeedCategory
	 */
	var $category;

	/**
	 * A string indicating the program used to generate the channel.
	 */
	var $generator;

	/**
	 * A URL that points to the documentation for the format used in the RSS file.
	 * [http://blogs.law.harvard.edu/tech/rss]
	 */
	var $docs;

	/**
	 * Allows processes to register with a cloud to be notified of updates to the channel,
	 * implementing a lightweight publish-subscribe protocol for RSS feeds [HTTP-POST, XML-RPC, SOAP]
	 */
	var $cloud;

	/**
	 * Time to live [minutes]. It's a number of minutes that indicates how long a channel can be cached before refreshing from the source.
	 */
	var $ttl;

	/**
	 * The PICS rating for the channel [Platform for Internet Content Selection]
	 */
	var $rating;

	/**
	 * Specifies a text input box that can be displayed with the channel.
	 * type FeedTextInput
	 * The purpose is something of a mystery.
	 * You can use it to specify a search engine box. Or to allow a reader to provide feedback.
	 * Most aggregators ignore it.
	 */
	var $textInput;

	/**
	 * ======= skipHours/skipDays came from scriptingNews format, designed in late 1997, and adopted by Netscape in RSS 0.91 in the 1999. =======
	 */

	/**
	 * A hint for aggregators telling them which hours they can skip.
	 * An XML element that contains up to 24 <hour> sub-elements whose value is a number between 0 and 23,
	 * representing a time in GMT,when aggregators, if they support the feature,
	 * may not read the channel on hours listed in the skipHours element.
	 * The hour beginning at midnight is hour zero.
	 * <hour></hour> ... <!-- ... up to 24 <hour> sub-elements ... -->
	 */
	var $skipHours=array();

	/**
	 * A hint for aggregators telling them which days they can skip.
	 * An XML element that contains up to seven <day> sub-elements whose value is:
	 * Monday, Tuesday, Wednesday, Thursday, Friday, Saturday or Sunday.
	 * Aggregators may not read the channel during days listed in the skipDays element.
	 * <day></day> <!-- ... up to 7 <day> sub-elements ... -->
	 */
	var $skipDays = array();

	/**
	 * ATOM/OPML editor email
	 */
	var $editorEmail;


	/**
	* The url of the external xsl/css stylesheet used to format the naked rss feed.
	* Ignored in the output when empty.
	*/
	var $xslStyleSheet;
	var $cssStyleSheet;

	/**
	 * @access private
	 */
	var $_items = Array();


	/**
	 * This feed's MIME content type.
	 * @since 1.4
	 * @access private
	 */
	var $contentType = "application/xml";


	/**
	 * This feed's character encoding.
	 * @since 1.6.1
	 **/
	var $encoding = "ISO-8859-1";


	/**
	 * Any additional elements to include as an assiciated array. All $key => $value pairs
	 * will be included unencoded in the feed in the form
	 *     <$key>$value</$key>
	 * Again: No encoding will be used! This means you can invalidate or enhance the feed
	 * if $value contains markup. This may be abused to embed tags not implemented by
	 * the FeedCreator class used.
	 */
	var $additionalElements = Array();


	/**
	 * Adds an FeedItem to the feed.
	 *
	 * @param object FeedItem $item The FeedItem to add to the feed.
	 * @access public
	 */
	function addItem($item) {
		$this->_items[] = $item;
	}


	/**
	 * Truncates a string to a certain length at the most sensible point.
	 * First, if there's a '.' character near the end of the string, the string is truncated after this character.
	 * If there is no '.', the string is truncated after the last ' ' character.
	 * If the string is truncated, " ..." is appended.
	 * If the string is already shorter than $length, it is returned unchanged.
	 *
	 * @static
	 * @param string    string A string to be truncated.
	 * @param int        length the maximum length the string should be truncated to
	 * @return string    the truncated string
	 */
	function iTrunc($string, $length) {

		if (_strlen($string)<=$length) {
			return $string;
		}

		$pos = _strrpos($string,".");
		if ($pos>=$length-4) {
			$string = _substr($string,0,$length-4);
			$pos = _strrpos($string,".");
		}

		if ($pos>=$length*0.4) {
			return _substr($string,0,$pos+1)." ...";
		}

		$pos = _strrpos($string," ");
		if ($pos>=$length-4) {
			$string = _substr($string,0,$length-4);
			$pos = _strrpos($string," ");
		}

		if ($pos>=$length*0.4) {
			return _substr($string,0,$pos)." ...";
		}

		return _substr($string,0,$length-4)." ...";
	}


	/**
	 * Creates a comment indicating the generator of this feed.
	 * The format of this comment seems to be recognized by
	 * Syndic8.com.
	 */
	function _createGeneratorComment() {
		return "<!-- generator=\"".FEEDCREATOR_VERSION."\" -->\n";
	}


	/**
	 * Creates a string containing all additional elements specified in
	 * $additionalElements.
	 * @param	elements	array	an associative array containing key => value pairs
	 * @param indentString	string	a string that will be inserted before every generated line
	 * @return    string    the XML tags corresponding to $additionalElements
	 */
	function _createAdditionalElements($elements, $indentString="") {
		$ae = "";
		if (is_array($elements)) {
			foreach($elements AS $key => $value) {
				$ae.= $indentString."<$key>$value</$key>\n";
			}
		}
		return $ae;
	}

	function _createStylesheetReferences() {
		$xml = "";
		if ($this->cssStyleSheet) $xml .= "<?xml-stylesheet href=\"".$this->cssStyleSheet."\" type=\"text/css\"?>\n";
		if ($this->xslStyleSheet) $xml .= "<?xml-stylesheet href=\"".$this->xslStyleSheet."\" type=\"text/xsl\"?>\n";
		return $xml;
	}


	/**
	 * Builds the feed's text.
	 * @abstract
	 * @return    string    the feed's complete text
	 */
	function createFeed() { }

	/**
	 * Generate a filename for the feed cache file. The result will be $_SERVER["PHP_SELF"] with the extension changed to .xml.
	 * For example:
	 *
	 * echo $_SERVER["PHP_SELF"]."\n";
	 * echo FeedCreator::_generateFilename();
	 *
	 * would produce:
	 *
	 * /rss/latestnews.php
	 * latestnews.xml
	 *
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
		return _substr($fileInfo["basename"],0,-(_strlen($fileInfo["extension"])+1)).".xml";
	}


	/**
	 * @since 1.4
	 * @access private
	 */
	function _redirect($filename) {

		// attention, heavily-commented-out-area

		// maybe use this in addition to file time checking
		//Header("Expires: ".date("r",time()+$this->_timeout));

		/* no caching at all, doesn't seem to work as good:
		Header("Cache-Control: no-cache");
		Header("Pragma: no-cache");
		*/

		// HTTP redirect, some feed readers' simple HTTP implementations don't follow it
		//Header("Location: ".$filename);

		Header("Content-Type: ".$this->contentType."; charset=".$this->encoding."; filename=".basename($filename));
		Header("Content-Disposition: inline; filename=".basename($filename));
		readfile($filename, "r");
		die();
	}

	/**
	 * Turns on caching and checks if there is a recent version of this feed in the cache.
	 * If there is, an HTTP redirect header is sent.
	 * To effectively use caching, you should create the FeedCreator object and call this method
	 * before anything else, especially before you do the time consuming task to build the feed
	 * (web fetching, for example).
	 * @since 1.4
	 * @param filename	string	optional	the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
	 * @param timeout	int		optional	the timeout in seconds before a cached version is refreshed (defaults to 3600 = 1 hour)
	 */
	function useCached($filename="", $timeout=3600) {
		$this->_timeout = $timeout;
		if ($filename=="") {
			$filename = $this->_generateFilename();
		}
		if (file_exists($filename) AND (time()-filemtime($filename) < $timeout)) {
			$this->_redirect($filename);
		}
	}


	/**
	 * Saves this feed as a file on the local disk. After the file is saved, a redirect
	 * header may be sent to redirect the user to the newly created file.
	 * @since 1.4
	 *
	 * @param filename	string	optional	the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
	 * @param redirect	boolean	optional	send an HTTP redirect header or not. If true, the user will be automatically redirected to the created file.
	 */
	function saveFeed($filename="", $displayContents=true) {
		if ($filename=="") {
			$filename = $this->_generateFilename();
		}
		$feedFile = fopen($filename, "w+");
		if ($feedFile) {
			fputs($feedFile,$this->createFeed());
			fclose($feedFile);
			if ($displayContents) {
				$this->_redirect($filename);
			}
		}
		else {
			echo "<br /><b>Error creating feed file, please check write permissions.</b><br />";
		}
	}

}



/**
 * UniversalFeedCreator lets you choose during runtime which format to build.
 * For general usage of a feed class, see the FeedCreator class below or the example above.
 *
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class UniversalFeedCreator extends FeedCreator {

	var $_feed;

	function _setFormat($format) {

		switch (_strtoupper($format)) {

			case "2.0":
				// fall through
			case "RSS2.0":
				$this->_feed = new RSSCreator20();
				break;

			case "1.0":
				// fall through
			case "RSS1.0":
				$this->_feed = new RSSCreator10();
				break;

			case "0.91":
				// fall through
			case "RSS0.91":
				$this->_feed = new RSSCreator091();
				break;

			case "PIE0.1":
				$this->_feed = new PIECreator01();
				break;

			case "MBOX":
				$this->_feed = new MBOXCreator();
				break;

			case "OPML":
				$this->_feed = new OPMLCreator();
				break;

			case "ATOM":
				// fall through: always the latest ATOM version
			case "ATOM0.3":
				$this->_feed = new AtomCreator03();
				break;

			case "HTML":
				$this->_feed = new HTMLCreator();
				break;

			case "JS":
				// fall through
			case "JAVASCRIPT":
				$this->_feed = new JSCreator();
				break;

			default:
				$this->_feed = new RSSCreator20();
				break;
		}

		$vars = get_object_vars($this);

		foreach ($vars as $key=>$value) {
			// prevent overwriting of properties "contentType"; do not copy "_feed" itself
			if (!in_array($key, array("_feed", "contentType"))) {
				$this->_feed->{$key} = $this->{$key};
			}
		}
	}


	/**
	 * Creates a syndication feed based on the items previously added.
	 *
	 * @see FeedCreator::addItem()
	 * @param string format format the feed should comply to. Valid values are:
	 *   "PIE0.1", "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM0.3", "HTML", "JS"
	 * @return string the contents of the feed.
	 */
	function createFeed($format = "RSS2.0") {
		$this->_setFormat($format);
		return $this->_feed->createFeed();
	}


	/**
	 * Saves this feed as a file on the local disk. After the file is saved, an HTTP redirect
	 * header may be sent to redirect the use to the newly created file.
	 * @since 1.4
	 *
	 * @param string format: format the feed should comply to. Valid values are:
	 *   "PIE0.1" (deprecated), "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM", "ATOM0.3", "HTML", "JS"
	 * @param string filename, optional: the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
	 * @param boolean displayContents, optional: send the content of the file or not. If true, the file will be sent in the body of the response.
	 */
	function saveFeed($format="RSS2.0", $filename="", $displayContents=true) {
		$this->_setFormat($format);
		$this->_feed->saveFeed($filename, $displayContents);
	}


	/**
	* Turns on caching and checks if there is a recent version of this feed in the cache.
	* If there is, an HTTP redirect header is sent.
	* To effectively use caching, you should create the FeedCreator object and call this method
	* before anything else, especially before you do the time consuming task to build the feed
	* (web fetching, for example).
	*
	* @param  string format format the feed should comply to. Valid values are:
	*   "PIE0.1" (deprecated), "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM0.3".
	* @param filename string optional the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
	* @param timeout int optional the timeout in seconds before a cached version is refreshed (defaults to 3600 = 1 hour)
	*/
	function useCached($format="RSS2.0", $filename="", $timeout=3600) {
		$this->_setFormat($format);
		$this->_feed->useCached($filename, $timeout);
	}

}


/**
 * A FeedItem is a part of a FeedCreator feed.
 *
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @since 1.3
 */
class FeedItem extends HtmlDescribable {

	/**
	 * ======== Mandatory attributes of an item. ========
	 */

	/**
	 * The title of the item.
	 */
	var $title;

	/**
	 * The item synopsis.
	 */
	var $description;

	/**
	 * The URL of the item.
	 */
	var $link;

	/**
	 * ======== Optional attributes of an item. ========
	 */

	/**
	 * Email address of the author of the item [author@email.net (Author Name)]
	 */
	var $author;

	/**
	 * PIE0.1 standards
	 */
	var $authorEmail;

	/**
	 * Includes the item in one or more categories.
	 * @type object FeedCategory
	 */
	var $category;

	/**
	 * URL of a page for comments relating to the item.
	 */
	var $comments;

	/**
	 * Describes a media object that is attached to the item.
	 * type object FeedItemEnclosure
	 * It has three required attributes:
	 * 1) url says where the enclosure is located
	 * 2) length says how big it is in bytes
	 * 3) and type says what its type is, a standard MIME type.
	 */
	var $enclosure;

	/**
	 * A string that uniquely identifies the item. isPermaLink is optional, its default value is true.
	 * If its value is false, the guid may not be assumed to be a url, or a url to anything in particular.
	 */
	var $guid;

	/**
	 * Publishing date of an item. May be in one of the following formats:
	 *
	 * RFC 822:
	 * "Mon, 20 Jan 03 18:05:41 +0400"
	 * "20 Jan 03 18:05:41 +0000"
	 *
	 * ISO 8601:
	 * "2003-01-20T18:05:41+04:00"
	 *
	 * Unix:
	 * 1043082341
	 */
	var $pubDate;

	/**
	 * The RSS channel that the item came from. <source url="http://www.site.net">Site Name</source>
	 * type object FeedItemSource
	 */
	var $source;

	/**
	 * Any additional elements to include as an assiciated array. All $key => $value pairs
	 * will be included unencoded in the feed item in the form
	 *     <$key>$value</$key>
	 * Again: No encoding will be used! This means you can invalidate or enhance the feed
	 * if $value contains markup. This may be abused to embed tags not implemented by
	 * the FeedCreator class used.
	 */
	var $additionalElements = Array();
}



/**
 * Specifies a RSS2.0 Channel text input box that can be displayed with the channel.
 * The purpose is something of a mystery. You can use it to specify a search engine box.
 * Or to allow a reader to provide feedback. Most aggregators ignore it.
 */
class FeedTextInput extends HtmlDescribable {

	/**
	 * The label of the Submit button in the text input area.
	 */
	var $title;

	/**
	 * Explains the text input area.
	 */
	var $description;

	/**
	 * The name of the text object in the text input area.
	 */
	var $name;

	/**
	 * The URL of the CGI script that processes text input requests.
	 */
	var $link;
}


/**
 * An HtmlDescribable is an item within a feed that can have a description that may
 * include HTML markup.
 */
class HtmlDescribable {

	/**
	 * Indicates whether the description field should be rendered in HTML within <![CDATA[]]
	 */
	var $descriptionHtmlSyndicated;

	/**
	 * Indicates whether and to how many characters a description should be truncated.
	 */
	var $descriptionTruncSize;

	/**
	 * Returns a formatted description field, depending on descriptionHtmlSyndicated and
	 * $descriptionTruncSize properties
	 * @return    string    the formatted description
	 */
	function getDescription() {
		$descriptionField = new FeedHtmlField($this->description);
		$descriptionField->syndicateHtml = $this->descriptionHtmlSyndicated;
		$descriptionField->truncSize = $this->descriptionTruncSize;
		return $descriptionField->output();
	}

}


/**
 * An FeedHtmlField describes and generates
 * a feed, item or image html field (probably a description). Output is
 * generated based on $truncSize, $syndicateHtml properties.
 * @author Pascal Van Hecke <feedcreator.class.php@vanhecke.info>
 * @version 1.6
 */
class FeedHtmlField {
	/**
	 * Mandatory attributes of a FeedHtmlField.
	 */
	var $rawFieldContent;

	/**
	 * Optional attributes of a FeedHtmlField.
	 *
	 */
	var $truncSize;
	var $syndicateHtml;

	/**
	 * Creates a new instance of FeedHtmlField.
	 * @param  $string: if given, sets the rawFieldContent property
	 */
	function FeedHtmlField($parFieldContent) {
		if ($parFieldContent) {
			$this->rawFieldContent = $parFieldContent;
		}
	}


	/**
	 * Creates the right output, depending on $truncSize, $syndicateHtml properties.
	 * @return string    the formatted field
	 */
	function output() {

		// when field available and syndicated in html we assume
		// - valid html in $rawFieldContent and we enclose in CDATA tags
		// - no truncation (truncating risks producing invalid html)
		if (!$this->rawFieldContent) {
			$result = "";
		}
		elseif ($this->syndicateHtml) {
			$result = "<![CDATA[".$this->rawFieldContent."]]>";
		}
		else {
			if ($this->truncSize and is_int($this->truncSize)) {
				$result = FeedCreator::iTrunc(_htmlspecialchars($this->rawFieldContent),$this->truncSize);
			}
			else {
				$result = _htmlspecialchars($this->rawFieldContent);
			}
		}
		return $result;
	}

}

/**
 * Item guid tag
 *
 */
class FeedItemGuid {

	/**
	 * guid attrib "isPermaLink" (true|false)
	 */
	var $isPermaLink=true;

	/**
	 * guid content
	 */
	var $value;
}


/**
 * An FeedImage may be added to a FeedCreator feed.
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @since 1.3
 */
class FeedImage extends HtmlDescribable {

	/**
	 * ======== Mandatory attributes of an image. ========
	 */

	/**
	 * Title of the image, it's used in the ALT attribute of the HTML.
	 */
	var $title;

	/**
	 * The URL of a GIF, JPEG or PNG image that represents the channel.
	 */
	var $url;

	/**
	 * The URL of the site, when the channel is rendered, the image is a link to the site.
	 */
	var $link;


	/**
	 * ======== Optional attributes of an image. ========
	 */

	/**
	 * Maximum value for width is 144, default value is 88.
	 */
	var $width;

	/**
	 * Maximum value for height is 400, default value is 31.
	 */
	var $height;

	/**
	 * Image description
	 */
	var $description;
}


/**
 * A FeedItemSource is a sub-item within a feed item that include HTML markup.
 * <item>-<source> is the RSS channel that the item came from
 */
class FeedItemSource extends HtmlDescribable {

	/**
	 * Source URL
	 */
	var $url;

	/**
	 * Source Name
	 */
	var $description;
}



class FeedCategory extends HtmlDescribable {

	/**
	 * category id - NOT a RSS2.0 standard
	 */
	var $id;

	/**
	 * category url - NOT a RSS2.0 standard
	 */
	var $url;

	/**
	 * a string that identifies a category name
	 */
	var $name;

	/**
	 * a string that identifies a categorization taxonomy.
	 */
	var $domain;

	/**
	 * Category name | description
	 */
	var $description;

}


/**
 * Describes a media object that is attached to the feed-item.
 */
class FeedItemEnclosure {

	/**
	 * Says where the enclosure is located
	 */
	var $url;

	/**
	 * Says how big it is in bytes
	 */
	var $length;

	/**
	 * says what its type is, a standard MIME type.
	 */
	var $type;
}


/**
 * FeedDate is an internal class that stores a date for a feed or feed item.
 * Usually, you won't need to use this.
 */
class FeedDate {

	var $unix;

	/**
	 * Creates a new instance of FeedDate representing a given date.
	 * Accepts RFC 822, ISO 8601 date formats as well as unix time stamps.
	 * @param mixed $dateString optional the date this FeedDate will represent. If not specified, the current date and time is used.
	 */
	function FeedDate($dateString="") {

		if (is_numeric($dateString)) {
			$this->unix = $dateString;
			return;
		}

		if ($dateString=="") $dateString = date("r");

		if (_preg_match("~(?:(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s+)?(\\d{1,2})\\s+([a-zA-Z]{3})\\s+(\\d{4})\\s+(\\d{2}):(\\d{2}):(\\d{2})\\s+(.*)~",$dateString,$matches)) {
			$months = Array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
			$this->unix = mktime($matches[4],$matches[5],$matches[6],$months[$matches[2]],$matches[1],$matches[3]);
			if (_substr($matches[7],0,1)=='+' OR _substr($matches[7],0,1)=='-') {
				$tzOffset = (_substr($matches[7],0,3) * 60 + _substr($matches[7],-2)) * 60;
			}
			else {
				if (_strlen($matches[7])==1) {
					$oneHour = 3600;
					$ord = ord($matches[7]);
					if ($ord < ord("M")) {
						$tzOffset = (ord("A") - $ord - 1) * $oneHour;
					}
					elseif ($ord >= ord("M") AND $matches[7]!="Z") {
						$tzOffset = ($ord - ord("M")) * $oneHour;
					}
					elseif ($matches[7]=="Z") {
						$tzOffset = 0;
					}
				}
				switch ($matches[7]) {
					case "UT":
					case "GMT":
						$tzOffset = 0;
				}
			}
			$this->unix += $tzOffset;
			return;
		}
		if (_preg_match("~(\\d{4})-(\\d{2})-(\\d{2})T(\\d{2}):(\\d{2}):(\\d{2})(.*)~",$dateString,$matches)) {
			$this->unix = mktime($matches[4],$matches[5],$matches[6],$matches[2],$matches[3],$matches[1]);
			if (_substr($matches[7],0,1)=='+' OR _substr($matches[7],0,1)=='-') {
				$tzOffset = (_substr($matches[7],0,3) * 60 + _substr($matches[7],-2)) * 60;
			}
			else {
				if ($matches[7]=="Z") {
					$tzOffset = 0;
				}
			}
			$this->unix += $tzOffset;
			return;
		}
		$this->unix = 0;
	}

	/**
	 * Gets the date stored in this FeedDate as an RFC 822 date. [Sat, 07 Sep 2002 00:00:01 GMT]
	 *
	 * @return a date in RFC 822 format
	 */
	function rfc822() {

		if(1) {
			$date = date("r", $this->unix);
		}
		else {
			//$date = gmdate("r",$this->unix);
			$date = gmdate("D, d M Y H:i:s", $this->unix);
			if (FEED_TIME_ZONE!="") $date .= " "._str_replace(":","",FEED_TIME_ZONE);
		}
		return $date;
	}

	/**
	 * Gets the date stored in this FeedDate as an ISO 8601 date.
	 *
	 * @return a date in ISO 8601 format
	 */
	function iso8601() {

		if(1) {
			$date = date("c", $this->unix);
		}
		else {
			$date = gmdate("Y-m-d\TH:i:sO",$this->unix);
			$date = _substr($date,0,22) . ':' . _substr($date,-2);
			if (FEED_TIME_ZONE!="") $date = _str_replace("+00:00",FEED_TIME_ZONE,$date);
		}
		return $date;
	}

	/**
	 * Gets the date stored in this FeedDate as unix time stamp.
	 *
	 * @return a date as a unix time stamp
	 */
	function unix() {
		return $this->unix;
	}
}


// ================================= [RSS 1.0] =========================================


/**
 * RSSCreator10 is a FeedCreator that implements RDF Site Summary (RSS) 1.0.
 *
 * @see http://www.purl.org/rss/1.0/
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class RSSCreator10 extends FeedCreator {

	/**
	 *
	 */
	var $syndicationURL;

	/**
	 * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
	 * The feed will contain all items previously added in the same order.
	 * @return    string    the feed's complete text
	 */
	function createFeed() {

		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		if ($this->cssStyleSheet=="") {
			$cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
		}
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<rdf:RDF\n";
		$feed.= "    xmlns=\"http://purl.org/rss/1.0/\"\n";
		$feed.= "    xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n";
		$feed.= "    xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\"\n";
		$feed.= "    xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
		$feed.= "    <channel rdf:about=\"".$this->syndicationURL."\">\n";
		$feed.= "        <title>"._htmlspecialchars($this->title)."</title>\n";
		$feed.= "        <description>"._htmlspecialchars($this->description)."</description>\n";
		$feed.= "        <link>".$this->link."</link>\n";
		if ($this->image!=null) {
			$feed.= "        <image rdf:resource=\"".$this->image->url."\" />\n";
		}
		$now = new FeedDate();
		$feed.= "       <dc:date>"._htmlspecialchars($now->iso8601())."</dc:date>\n";
		$feed.= "        <items>\n";
		$feed.= "            <rdf:Seq>\n";
		for ($i=0;$i<count($this->_items);$i++) {
			$feed.= "                <rdf:li rdf:resource=\""._htmlspecialchars($this->_items[$i]->link)."\"/>\n";
		}
		$feed.= "            </rdf:Seq>\n";
		$feed.= "        </items>\n";
		$feed.= "    </channel>\n";
		if ($this->image!=null) {
			$feed.= "    <image rdf:about=\"".$this->image->url."\">\n";
			$feed.= "        <title>".$this->image->title."</title>\n";
			$feed.= "        <link>".$this->image->link."</link>\n";
			$feed.= "        <url>".$this->image->url."</url>\n";
			$feed.= "    </image>\n";
		}
		$feed.= $this->_createAdditionalElements($this->additionalElements, "    ");

		for ($i=0;$i<count($this->_items);$i++) {
			$feed.= "    <item rdf:about=\""._htmlspecialchars($this->_items[$i]->link)."\">\n";
			//$feed.= "        <dc:type>Posting</dc:type>\n";
			$feed.= "        <dc:format>text/html</dc:format>\n";
			if ($this->_items[$i]->pubDate!=null) {
				$itemDate = new FeedDate($this->_items[$i]->pubDate);
				$feed.= "        <dc:date>"._htmlspecialchars($itemDate->iso8601())."</dc:date>\n";
			}
			if ($this->_items[$i]->source!="") {
				$feed.= "        <dc:source>"._htmlspecialchars($this->_items[$i]->source)."</dc:source>\n";
			}
			if ($this->_items[$i]->author!="") {
				$feed.= "        <dc:creator>"._htmlspecialchars($this->_items[$i]->author)."</dc:creator>\n";
			}
			$feed.= "        <title>"._htmlspecialchars(strip_tags(_strtr($this->_items[$i]->title,"\n\r","  ")))."</title>\n";
			$feed.= "        <link>"._htmlspecialchars($this->_items[$i]->link)."</link>\n";
			$feed.= "        <description>"._htmlspecialchars($this->_items[$i]->description)."</description>\n";
			$feed.= $this->_createAdditionalElements($this->_items[$i]->additionalElements, "        ");
			$feed.= "    </item>\n";
		}
		$feed.= "</rdf:RDF>\n";
		return $feed;
	}
}


// ================================= [RSS 0.91] =========================================


/**
 * RSSCreator091 is a FeedCreator that implements RSS 0.91 Spec, revision 3.
 *
 * @see http://my.netscape.com/publish/formats/rss-spec-0.91.html
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class RSSCreator091 extends FeedCreator {

	/**
	 * Stores this RSS feed's version number.
	 * @access private
	 */
	var $RSSVersion;

	function RSSCreator091() {
		$this->_setRSSVersion("0.91");
		$this->contentType = "application/rss+xml";
	}

	/**
	 * Sets this RSS feed's version number.
	 * @access private
	 */
	function _setRSSVersion($version) {
		$this->RSSVersion = $version;
	}

	/**
	 * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
	 * The feed will contain all items previously added in the same order.
	 * @return    string    the feed's complete text
	 */
	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		//$feed.= $this->_createGeneratorComment();
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<rss version=\"".$this->RSSVersion."\">\n";
		$feed.= "    <channel>\n";
		$feed.= "        <title>".FeedCreator::iTrunc(_htmlspecialchars($this->title),256)."</title>\n";
		$feed.= "        <link>"._htmlspecialchars($this->link)."</link>\n";
		//$this->descriptionTruncSize = 500;
		$feed.= "        <description>".$this->getDescription()."</description>\n";
		if ($this->pubDate!="") {
			$pubDate = new FeedDate($this->pubDate);
			$feed.= "        <pubDate>"._htmlspecialchars($pubDate->rfc822())."</pubDate>\n";
		}
		if ($this->lastBuildDate!="") {
			$lastBuildDate = new FeedDate($this->lastBuildDate);
			$feed.= "        <lastBuildDate>"._htmlspecialchars($lastBuildDate->rfc822())."</lastBuildDate>\n";
		}
		$now = new FeedDate();

		$feed.= "        <generator>"._htmlspecialchars($this->generator ? $this->generator : FEEDCREATOR_VERSION)."</generator>\n";

		if (is_a($this->image, 'FeedImage')) {
			$feed.= "        <image>\n";
			$feed.= "            <url>"._htmlspecialchars($this->image->url)."</url>\n";
			$feed.= "            <title>".FeedCreator::iTrunc(_htmlspecialchars($this->image->title),100)."</title>\n";
			$feed.= "            <link>"._htmlspecialchars($this->image->link)."</link>\n";
			if ($this->image->width!="") {
				$feed.= "            <width>".(int)$this->image->width."</width>\n";
			}
			if ($this->image->height!="") {
				$feed.= "            <height>".(int)$this->image->height."</height>\n";
			}
			if ($this->image->description!="") {
				$feed.= "            <description>".$this->image->getDescription()."</description>\n";
			}
			$feed.= "        </image>\n";
		}
		if ($this->language!="") {
			$feed.= "        <language>"._htmlspecialchars($this->language)."</language>\n";
		}
		if ($this->copyright!="") {
			$feed.= "        <copyright>".FeedCreator::iTrunc(_htmlspecialchars($this->copyright),256)."</copyright>\n";
		}
		if ($this->managingEditor!="") {
			$feed.= "        <managingEditor>".FeedCreator::iTrunc(_htmlspecialchars($this->managingEditor),256)."</managingEditor>\n";
		}
		if ($this->webMaster!="") {
			$feed.= "        <webMaster>".FeedCreator::iTrunc(_htmlspecialchars($this->webMaster),256)."</webMaster>\n";
		}
		if (is_a($this->category, 'FeedCategory')) {
			$arrAttrib = array();
			foreach(array('domain', 'id', 'url') as $attrib) {
				if(isset($this->category->$attrib)) {
					$arrAttrib[] = $attrib.'="'._htmlspecialchars($this->category->$attrib).'"';
				}
			}
			$strAttrib = empty($arrAttrib) ? '' : (' '.implode(' ', $arrAttrib));
			$feed.= "        <category".$strAttrib.">".$this->category->getDescription()."</category>\n";
		}
		if ($this->docs!="") {
			$feed.= "        <docs>".FeedCreator::iTrunc(_htmlspecialchars($this->docs),500)."</docs>\n";
		}
		if ($this->ttl!="") {
			$feed.= "        <ttl>"._htmlspecialchars($this->ttl)."</ttl>\n";
		}
		if ($this->rating!="") {
			$feed.= "        <rating>".FeedCreator::iTrunc(_htmlspecialchars($this->rating),500)."</rating>\n";
		}
		if ($this->cloud!="") {
			$feed.= "        <cloud>"._htmlspecialchars($this->cloud)."</cloud>\n";
		}
		if (is_a($this->textInput, 'FeedTextInput')) {
			$feed.= "        <textInput>\n";
			$feed.= "            <title>".FeedCreator::iTrunc(_htmlspecialchars($this->textInput->title),100)."</title>\n";
			$feed.= "            <link>"._htmlspecialchars($this->textInput->link)."</link>\n";
			if ($this->textInput->name!="") {
				$feed.= "            <name>"._htmlspecialchars($this->textInput->name)."</name>\n";
			}
			if ($this->textInput->description!="") {
				$feed.= "            <description>".$this->textInput->getDescription()."</description>\n";
			}
			$feed.= "        </textInput>\n";
		}
		if (is_array($this->skipHours) && !empty($this->skipHours)) {
			$this->skipHours = array_unique($this->skipHours);
			$feed .= "        <skipHours>\n";
			foreach($this->skipHours as $hour) {
				$hour = (int)$hour;
				if($hour>=0 && $hour<=23) {
					$feed.= "            <hour>".$hour."</hour>\n";
				}
			}
			$feed .= "        </skipHours>\n";
		}
		if (is_array($this->skipDays) && !empty($this->skipDays)) {
			$this->skipDays = array_unique($this->skipDays);
			$feed .= "        <skipDays>\n";
			$days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
			foreach($this->skipDays as $day) {
				if(in_array($day, $days)) {
					$feed.= "            <day>".$day."</day>\n";
				}
			}
			$feed .= "        </skipDays>\n";
		}
		$feed.= $this->_createAdditionalElements($this->additionalElements, "    ");

		for ($i=0;$i<count($this->_items);$i++) {
			$feed.= "        <item>\n";
			$feed.= "            <title>".FeedCreator::iTrunc(_htmlspecialchars($this->_items[$i]->title),256)."</title>\n";
			$feed.= "            <link>"._htmlspecialchars($this->_items[$i]->link)."</link>\n";
			$feed.= "            <description>".$this->_items[$i]->getDescription()."</description>\n";

			if(is_a($this->_items[$i]->guid, 'FeedItemGuid')) {
				$arrAttrib = array();
				foreach(array('isPermaLink') as $attrib) {
					if(isset($this->_items[$i]->guid->$attrib)) {
						if($attrib=='isPermaLink') {
							$attribValue = $this->_items[$i]->guid->$attrib ? 'true' : 'false';
						}
						else {
							$attribValue = $this->_items[$i]->guid->$attrib;
						}
						$arrAttrib[] = $attrib.'="'._htmlspecialchars($attribValue).'"';
					}
				}
				$strAttrib = empty($arrAttrib) ? '' : (' '.implode(' ', $arrAttrib));
				$feed.= "            <guid".$strAttrib.">"._htmlspecialchars($this->_items[$i]->guid->value)."</guid>\n";
			}
			if($this->_items[$i]->author!="") {
				$feed.= "            <author>"._htmlspecialchars($this->_items[$i]->author)."</author>\n";
			}
			if(is_a($this->_items[$i]->source, 'FeedItemSource')) {
				$arrAttrib = array();
				foreach(array('url') as $attrib) {
					if(isset($this->_items[$i]->source->$attrib)) {
						$arrAttrib[] = $attrib.'="'._htmlspecialchars($this->_items[$i]->source->$attrib).'"';
					}
				}
				$strAttrib = empty($arrAttrib) ? '' : (' '.implode(' ', $arrAttrib));
				$feed.= "            <source".$strAttrib.">".$this->_items[$i]->source->getDescription()."</source>\n";
			}
			if(is_a($this->_items[$i]->category, 'FeedCategory')) {
				$arrAttrib = array();
				foreach(array('domain', 'id', 'url') as $attrib) {
					if(isset($this->_items[$i]->category->$attrib)) {
						$arrAttrib[] = $attrib.'="'._htmlspecialchars($this->_items[$i]->category->$attrib).'"';
					}
				}
				$strAttrib = empty($arrAttrib) ? '' : (' '.implode(' ', $arrAttrib));
				if($this->_items[$i]->category->name || $strAttrib) {
					$feed.= "            <category".$strAttrib.">".$this->_items[$i]->category->getDescription()."</category>\n";
				}
			}
			if($this->_items[$i]->comments!="") {
				$feed.= "            <comments>"._htmlspecialchars($this->_items[$i]->comments)."</comments>\n";
			}
			if($this->_items[$i]->pubDate!="") {
				$itemDate = new FeedDate($this->_items[$i]->pubDate);
				$feed.= "            <pubDate>"._htmlspecialchars($itemDate->rfc822())."</pubDate>\n";
			}
			if(is_a($this->_items[$i]->enclosure, 'FeedItemEnclosure')) {
				$arrAttrib = array();
				foreach(array('url', 'length', 'type') as $attrib) {
					if(isset($this->_items[$i]->enclosure->$attrib)) {
						$arrAttrib[] = $attrib.'="'._htmlspecialchars($this->_items[$i]->enclosure->$attrib).'"';
					}
				}
				$strAttrib = empty($arrAttrib) ? '' : (' '.implode(' ', $arrAttrib));
				if($strAttrib) {
					$feed.= "            <enclosure".$strAttrib."></enclosure>\n";
				}
			}
			$feed.= $this->_createAdditionalElements($this->_items[$i]->additionalElements, "        ");
			$feed.= "        </item>\n";
		}
		$feed.= "    </channel>\n";
		$feed.= "</rss>\n";
		return $feed;
	}
}


// ================================= [RSS 2.0] =========================================


/**
 * RSSCreator20 is a FeedCreator that implements RDF Site Summary (RSS) 2.0.
 *
 * @see http://backend.userland.com/rss
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class RSSCreator20 extends RSSCreator091 {

	function RSSCreator20() {
		parent::_setRSSVersion("2.0");
	}

}


// ================================= [PIE 01] =========================================


/**
 * PIECreator01 is a FeedCreator that implements the emerging PIE specification,
 * as in http://intertwingly.net/wiki/pie/Syntax.
 *
 * @deprecated
 * @since 1.3
 * @author Scott Reynen <scott@randomchaos.com> and Kai Blankenhorn <kaib@bitfolge.de>
 */
class PIECreator01 extends FeedCreator {

	function PIECreator01() {
		$this->encoding = "utf-8";
	}

	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<feed version=\"0.1\" xmlns=\"http://example.com/newformat#\">\n";
		$feed.= "    <title>".FeedCreator::iTrunc(_htmlspecialchars($this->title),100)."</title>\n";
		$this->truncSize = 500;
		$feed.= "    <subtitle>".$this->getDescription()."</subtitle>\n";
		$feed.= "    <link>".$this->link."</link>\n";
		for ($i=0;$i<count($this->_items);$i++) {
			$feed.= "    <entry>\n";
			$feed.= "        <title>".FeedCreator::iTrunc(_htmlspecialchars(strip_tags($this->_items[$i]->title)),100)."</title>\n";
			$feed.= "        <link>"._htmlspecialchars($this->_items[$i]->link)."</link>\n";
			$itemDate = new FeedDate($this->_items[$i]->pubDate);
			$feed.= "        <created>"._htmlspecialchars($itemDate->iso8601())."</created>\n";
			$feed.= "        <issued>"._htmlspecialchars($itemDate->iso8601())."</issued>\n";
			$feed.= "        <modified>"._htmlspecialchars($itemDate->iso8601())."</modified>\n";
			$feed.= "        <id>"._htmlspecialchars($this->_items[$i]->guid)."</id>\n";
			if ($this->_items[$i]->author!="") {
				$feed.= "        <author>\n";
				$feed.= "            <name>"._htmlspecialchars($this->_items[$i]->author)."</name>\n";
				if ($this->_items[$i]->authorEmail!="") {
					$feed.= "            <email>".$this->_items[$i]->authorEmail."</email>\n";
				}
				$feed.="        </author>\n";
			}
			$feed.= "        <content type=\"text/html\" xml:lang=\"en-us\">\n";
			$feed.= "            <div xmlns=\"http://www.w3.org/1999/xhtml\">".$this->_items[$i]->getDescription()."</div>\n";
			$feed.= "        </content>\n";
			$feed.= "    </entry>\n";
		}
		$feed.= "</feed>\n";
		return $feed;
	}
}


// ================================= [ATOM 03] =========================================


/**
 * AtomCreator03 is a FeedCreator that implements the atom specification,
 * as in http://www.intertwingly.net/wiki/pie/FrontPage.
 * Please note that just by using AtomCreator03 you won't automatically
 * produce valid atom files. For example, you have to specify either an managingEditor
 * for the feed or an author for every single feed item.
 *
 * Some elements have not been implemented yet. These are (incomplete list):
 * author URL, item author's email and URL, item contents, alternate links,
 * other link content types than text/html. Some of them may be created with
 * AtomCreator03::additionalElements.
 *
 * @see FeedCreator#additionalElements
 * @since 1.6
 * @author Kai Blankenhorn <kaib@bitfolge.de>, Scott Reynen <scott@randomchaos.com>
 */
class AtomCreator03 extends FeedCreator {

	function AtomCreator03() {
		$this->contentType = "application/atom+xml";
		$this->encoding = "utf-8";
	}

	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<feed version=\"0.3\" xmlns=\"http://purl.org/atom/ns#\"";
		if ($this->language!="") {
			$feed.= " xml:lang=\"".$this->language."\"";
		}
		$feed.= ">\n";
		$feed.= "    <title>"._htmlspecialchars($this->title)."</title>\n";
		$feed.= "    <tagline>"._htmlspecialchars($this->description)."</tagline>\n";
		$feed.= "    <link rel=\"alternate\" type=\"text/html\" href=\""._htmlspecialchars($this->link)."\"/>\n";
		$feed.= "    <id>"._htmlspecialchars($this->link)."</id>\n";
		$now = new FeedDate();
		$feed.= "    <modified>"._htmlspecialchars($now->iso8601())."</modified>\n";
		if ($this->managingEditor!="") {
			$feed.= "    <author>\n";
			$feed.= "        <name>".$this->managingEditor."</name>\n";
			if ($this->editorEmail!="") {
				$feed.= "        <email>".$this->editorEmail."</email>\n";
			}
			$feed.= "    </author>\n";
		}
		$feed.= "    <generator>".FEEDCREATOR_VERSION."</generator>\n";
		$feed.= $this->_createAdditionalElements($this->additionalElements, "    ");
		for ($i=0;$i<count($this->_items);$i++) {
			$feed.= "    <entry>\n";
			$feed.= "        <title>"._htmlspecialchars(strip_tags($this->_items[$i]->title))."</title>\n";
			$feed.= "        <link rel=\"alternate\" type=\"text/html\" href=\""._htmlspecialchars($this->_items[$i]->link)."\"/>\n";
			if ($this->_items[$i]->pubDate=="") {
				$this->_items[$i]->pubDate = time();
			}
			$itemDate = new FeedDate($this->_items[$i]->pubDate);
			$feed.= "        <created>"._htmlspecialchars($itemDate->iso8601())."</created>\n";
			$feed.= "        <issued>"._htmlspecialchars($itemDate->iso8601())."</issued>\n";
			$feed.= "        <modified>"._htmlspecialchars($itemDate->iso8601())."</modified>\n";
			$feed.= "        <id>"._htmlspecialchars($this->_items[$i]->link)."</id>\n";
			$feed.= $this->_createAdditionalElements($this->_items[$i]->additionalElements, "        ");
			if ($this->_items[$i]->author!="") {
				$feed.= "        <author>\n";
				$feed.= "            <name>"._htmlspecialchars($this->_items[$i]->author)."</name>\n";
				$feed.= "        </author>\n";
			}
			if ($this->_items[$i]->description!="") {
				$feed.= "        <summary>"._htmlspecialchars($this->_items[$i]->description)."</summary>\n";
			}
			$feed.= "    </entry>\n";
		}
		$feed.= "</feed>\n";
		return $feed;
	}
}


// ================================= [MBOX] =========================================


/**
 * MBOXCreator is a FeedCreator that implements the mbox format
 * as described in http://www.qmail.org/man/man5/mbox.html
 *
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class MBOXCreator extends FeedCreator {

	function MBOXCreator() {
		$this->contentType = "text/plain";
		$this->encoding = "ISO-8859-15";
	}

	function qp_enc($input = "", $line_max = 76) {
		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
		$lines = _preg_split("/(?:\r\n|\r|\n)/", $input);
		$eol = "\r\n";
		$escape = "=";
		$output = "";
		while( list(, $line) = each($lines) ) {
			//$line = _rtrim($line); // remove trailing white space -> no =20\r\n necessary
			$linlen = _strlen($line);
			$newline = "";
			for($i = 0; $i < $linlen; $i++) {
				$c = _substr($line, $i, 1);
				$dec = ord($c);
				if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only
					$c = "=20";
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required
					$h2 = floor($dec/16); $h1 = floor($dec%16);
					$c = $escape.$hex["$h2"].$hex["$h1"];
				}
				if ( (_strlen($newline) + _strlen($c)) >= $line_max ) { // CRLF is not counted
					$output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay
					$newline = "";
				}
				$newline .= $c;
			} // end of for
			$output .= $newline.$eol;
		}
		return _trim($output);
	}


	/**
	 * Builds the MBOX contents.
	 * @return    string    the feed's complete text
	 */
	function createFeed() {
		for ($i=0;$i<count($this->_items);$i++) {
			if ($this->_items[$i]->author!="") {
				$from = $this->_items[$i]->author;
			} else {
				$from = $this->title;
			}
			$itemDate = new FeedDate($this->_items[$i]->pubDate);
			$feed.= "From "._strtr(MBOXCreator::qp_enc($from)," ","_")." ".date("D M d H:i:s Y",$itemDate->unix())."\n";
			$feed.= "Content-Type: text/plain;\n";
			$feed.= "	charset=\"".$this->encoding."\"\n";
			$feed.= "Content-Transfer-Encoding: quoted-printable\n";
			$feed.= "Content-Type: text/plain\n";
			$feed.= "From: \"".MBOXCreator::qp_enc($from)."\"\n";
			$feed.= "Date: ".$itemDate->rfc822()."\n";
			$feed.= "Subject: ".MBOXCreator::qp_enc(FeedCreator::iTrunc($this->_items[$i]->title,100))."\n";
			$feed.= "\n";
			$body = chunk_split(MBOXCreator::qp_enc($this->_items[$i]->description));
			$feed.= _preg_replace("~\nFrom ([^\n]*)(\n?)~","\n>From $1$2\n",$body);
			$feed.= "\n";
			$feed.= "\n";
		}
		return $feed;
	}

	/**
	 * Generate a filename for the feed cache file. Overridden from FeedCreator to prevent XML data types.
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
		return _substr($fileInfo["basename"],0,-(_strlen($fileInfo["extension"])+1)).".mbox";
	}
}


// ================================= [OPML 1.0] =========================================


/**
 * OPMLCreator is a FeedCreator that implements OPML 1.0.
 *
 * @see http://opml.scripting.com/spec
 * @author Dirk Clemens, Kai Blankenhorn
 * @since 1.5
 */
class OPMLCreator extends FeedCreator {

	function OPMLCreator() {
		$this->encoding = "utf-8";
	}

	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<opml xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">\n";
		$feed.= "    <head>\n";
		$feed.= "        <title>"._htmlspecialchars($this->title)."</title>\n";
		if ($this->pubDate!="") {
			$date = new FeedDate($this->pubDate);
			$feed.= "         <dateCreated>".$date->rfc822()."</dateCreated>\n";
		}
		if ($this->lastBuildDate!="") {
			$date = new FeedDate($this->lastBuildDate);
			$feed.= "         <dateModified>".$date->rfc822()."</dateModified>\n";
		}
		if ($this->managingEditor!="") {
			$feed.= "         <ownerName>".$this->managingEditor."</ownerName>\n";
		}
		if ($this->editorEmail!="") {
			$feed.= "         <ownerEmail>".$this->editorEmail."</ownerEmail>\n";
		}
		$feed.= "    </head>\n";
		$feed.= "    <body>\n";
		for ($i=0;$i<count($this->_items);$i++) {
			$feed.= "    <outline type=\"rss\" ";
			$title = _htmlspecialchars(strip_tags(_strtr($this->_items[$i]->title,"\n\r","  ")));
			$feed.= " title=\"".$title."\"";
			$feed.= " text=\"".$title."\"";
			//$feed.= " description=\""._htmlspecialchars($this->_items[$i]->description)."\"";
			$feed.= " url=\""._htmlspecialchars($this->_items[$i]->link)."\"";
			$feed.= "/>\n";
		}
		$feed.= "    </body>\n";
		$feed.= "</opml>\n";
		return $feed;
	}
}


// ================================= [HTML] =========================================


/**
 * HTMLCreator is a FeedCreator that writes an HTML feed file to a specific
 * location, overriding the createFeed method of the parent FeedCreator.
 * The HTML produced can be included over http by scripting languages, or serve
 * as the source for an IFrame.
 * All output by this class is embedded in <div></div> tags to enable formatting
 * using CSS.
 *
 * @author Pascal Van Hecke
 * @since 1.7
 */
class HTMLCreator extends FeedCreator {

	var $contentType = "text/html";

	/**
	 * Contains HTML to be output at the start of the feed's html representation.
	 */
	var $header;

	/**
	 * Contains HTML to be output at the end of the feed's html representation.
	 */
	var $footer ;

	/**
	 * Contains HTML to be output between entries. A separator is only used in
	 * case of multiple entries.
	 */
	var $separator;

	/**
	 * Used to prefix the stylenames to make sure they are unique
	 * and do not clash with stylenames on the users' page.
	 */
	var $stylePrefix;

	/**
	 * Determines whether the links open in a new window or not.
	 */
	var $openInNewWindow = true;

	var $imageAlign = "right";

	/**
	 * In case of very simple output you may want to get rid of the style tags,
	 * hence this variable.  There's no equivalent on item level, but of course you can
	 * add strings to it while iterating over the items ($this->stylelessOutput .= ...)
	 * and when it is non-empty, ONLY the styleless output is printed, the rest is ignored
	 * in the function createFeed().
	 */
	var $stylelessOutput = "";

	/**
	 * Writes the HTML.
	 * @return    string    the scripts's complete text
	 */
	function createFeed() {
		// if there is styleless output, use the content of this variable and ignore the rest
		if ($this->stylelessOutput!="") {
			return $this->stylelessOutput;
		}

		//if no stylePrefix is set, generate it yourself depending on the script name
		if ($this->stylePrefix=="") {
			$this->stylePrefix = _str_replace(".", "_", $this->_generateFilename())."_";
		}

		//set an openInNewWindow_token_to be inserted or not
		if ($this->openInNewWindow) {
			$targetInsert = " target=\"_blank\"";
		}

		// use this array to put the lines in and implode later with "document.write" javascript
		$feedArray = array();
		if ($this->image!=null) {
			$imageStr = "<a href=\"".$this->image->link."\"".$targetInsert.">".
							"<img src=\"".$this->image->url."\" border=\"0\" alt=\"".
							FeedCreator::iTrunc(_htmlspecialchars($this->image->title),100).
							"\" align=\"".$this->imageAlign."\" ";
			if ($this->image->width) {
				$imageStr .=" width=\"".$this->image->width. "\" ";
			}
			if ($this->image->height) {
				$imageStr .=" height=\"".$this->image->height."\" ";
			}
			$imageStr .="/></a>";
			$feedArray[] = $imageStr;
		}

		if ($this->title) {
			$feedArray[] = "<div class=\"".$this->stylePrefix."title\"><a href=\"".$this->link."\" ".$targetInsert." class=\"".$this->stylePrefix."title\">".
				FeedCreator::iTrunc(_htmlspecialchars($this->title),100)."</a></div>";
		}

		if ($this->getDescription()) {
			$feedArray[] = "<div class=\"".$this->stylePrefix."description\">".
				_str_replace("]]>", "", _str_replace("<![CDATA[", "", $this->getDescription())).
				"</div>";
		}

		if ($this->header) {
			$feedArray[] = "<div class=\"".$this->stylePrefix."header\">".$this->header."</div>";
		}

		for ($i=0;$i<count($this->_items);$i++) {
			if ($this->separator and $i > 0) {
				$feedArray[] = "<div class=\"".$this->stylePrefix."separator\">".$this->separator."</div>";
			}

			if ($this->_items[$i]->title) {
				if ($this->_items[$i]->link) {
					$feedArray[] =
						"<div class=\"".$this->stylePrefix."item_title\"><a href=\"".$this->_items[$i]->link."\" class=\"".$this->stylePrefix.
						"item_title\"".$targetInsert.">".FeedCreator::iTrunc(_htmlspecialchars(strip_tags($this->_items[$i]->title)),100).
						"</a></div>";
				}
				else {
					$feedArray[] =
						"<div class=\"".$this->stylePrefix."item_title\">".
						FeedCreator::iTrunc(_htmlspecialchars(strip_tags($this->_items[$i]->title)),100).
						"</div>";
				}
			}

			if ($this->_items[$i]->getDescription()) {
				$feedArray[] =
				"<div class=\"".$this->stylePrefix."item_description\">".
					_str_replace("]]>", "", _str_replace("<![CDATA[", "", $this->_items[$i]->getDescription())).
					"</div>";
			}
		}

		if ($this->footer) {
			$feedArray[] = "<div class=\"".$this->stylePrefix."footer\">".$this->footer."</div>";
		}

		$feed= "".implode("\r\n", $feedArray);
		return $feed;
	}

	/**
	 * Overrrides parent to produce .html extensions
	 *
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
		return _substr($fileInfo["basename"],0,-(_strlen($fileInfo["extension"])+1)).".html";
	}
}


// ================================= [JS] =========================================


/**
 * JSCreator is a class that writes a js file to a specific
 * location, overriding the createFeed method of the parent HTMLCreator.
 *
 * @author Pascal Van Hecke
 */
class JSCreator extends HTMLCreator {

	var $contentType = "text/javascript";

	/**
	 * writes the javascript
	 * @return    string    the scripts's complete text
	 */
	function createFeed() {
		$feed = parent::createFeed();
		$feedArray = explode("\n",$feed);

		$jsFeed = "";
		foreach ($feedArray as $value) {
			$jsFeed .= "document.write('"._trim(_addslashes($value))."');\n";
		}
		return $jsFeed;
	}

	/**
	 * Overrrides parent to produce .js extensions
	 *
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
		return _substr($fileInfo["basename"],0,-(_strlen($fileInfo["extension"])+1)).".js";
	}

}



/*** TEST SCRIPT *********************************************************

//include("feedcreator.class.php");

// Feed channel
$rss = new UniversalFeedCreator();
$rss->encoding = 'utf-8';
$rss->title = "Channel title <script>alert(1);</script>";
$rss->link = "http://www.dailyphp.net/news?alskj=&kfd";
$rss->description = "RSS) 2.0 <script>alert(1);</script>";
$rss->language = 'en_US';
$rss->copyright = 'Copyright <script>alert(1);</script>';
$rss->managingEditor = 'managingEditor Name Surname email@email.com';
$rss->webMaster = 'webMaster Name Surname email@email.com';
$rss->pubDate = time();
$rss->lastBuildDate = time()-3600;
//$rss->generator = 'Rss generator <script>alert(1);</script>';
$rss->docs = 'http://blogs.law.harvard.edu/tech/rss';
$rss->ttl = 3548;
$rss->rating = 10.3;
$rss->cloud = 'Cloud channel content <script>alert(1);</script>';
$rss->skipHours = array(0,1,2,3,23,24);
$rss->skipDays = array('Monday', 'Friday');

// --= Channel category =-- //
$feedCategory = new FeedCategory();
$feedCategory->id = 10;
$feedCategory->domain = 'sport';
$feedCategory->url = 'http://category/url/';
$feedCategory->description = 'Sport <script>alert(1);</script>';
//optional
//$feedCategory->descriptionTruncSize = 500;
$feedCategory->descriptionHtmlSyndicated = true;
$rss->category = $feedCategory;

// --= Channel textInput =-- //
$textInput = new FeedTextInput();
$textInput->title = 'Text Input title <script>alert(1);</script>';
$textInput->description = 'Text Input title <script>alert(1);</script>';
$textInput->name = 'Text Input name <script>alert(1);</script>';
$textInput->link = 'http://url/text.input/';
//$textInput->descriptionTruncSize = 500;
$textInput->descriptionHtmlSyndicated = true;
$rss->textInput = $textInput;

//optional
//$rss->descriptionTruncSize = 500;
//$rss->descriptionHtmlSyndicated = true;
//$rss->xslStyleSheet = "http://feedster.com/rss20.xsl";

// --= Channel image =-- //
$image = new FeedImage();
$image->title = 'Image title <script>alert(1);</script>';
$image->url = 'http://bm.img.com.ua/a/hp/img/search2/children.gif';
$image->link = 'http://www.dailyphp.net <script>alert(1);</script>';
$image->width = 236;
$image->height = 68;
$image->description = 'Image description <script>alert(1);</script>';
//optional
//$image->descriptionTruncSize = 500;
$image->descriptionHtmlSyndicated = true;
$rss->image = $image;

// get your news items from somewhere, e.g. your database:
//mysql_select_db($dbHost, $dbUser, $dbPass);
//$res = mysql_query("SELECT * FROM news ORDER BY newsdate DESC");
//while ($data = mysql_fetch_object($res)) {

	// --= Feed item =-- //
	$item = new FeedItem();
	$item->title = 'Item title <script>alert(1);</script>';
	$item->description = '<b>description in </b><br/>HTML <script>alert(1);</script>';
	$item->link = 'http://localhost/item/';
	$item->author = 'author@email.net (Author Name)';
	$item->pubDate = time();
	$item->author = 'John Doe';
	$item->comments = 'http://comments/url/';

	// --= Item guid =-- //
	$guid = new FeedItemGuid();
	$guid->isPermaLink = true;
	$guid->value = 1;
	$item->guid = $guid;

	// --= Item category =-- //
	$itemCategory = new FeedCategory();
	$itemCategory->id = 10;
	$itemCategory->domain = 'sport';
	$itemCategory->url = 'http://category/url/';
	$itemCategory->description = 'Item Sport <script>alert(1);</script>';
	//optional
	//$itemCategory->descriptionTruncSize = 500;
	$itemCategory->descriptionHtmlSyndicated = true;
	$item->category = $itemCategory;

	// --= Item enclosure =-- //
	$enclosure = new FeedItemEnclosure();
	$enclosure->url = 'http://i.i.ua/prikol/thumb/6/8/252986.jpg';
	$enclosure->length = 65297;
	$enclosure->type = 'image/jpeg';
	$item->enclosure = $enclosure;

	// --= Item source =-- //
	$source = new FeedItemSource();
	$source->url = 'http://www.site.net';
	$source->description = 'site name <script>alert(1);</script>';
	$source->descriptionHtmlSyndicated = true;
	$item->source = $source;

	//optional
	//$item->descriptionTruncSize = 500;
	$item->descriptionHtmlSyndicated = true;

	$rss->addItem($item);

//}

echo $rss->createFeed();



***************************************************************************/



/**
================================
|| RSS2.0 Feed Specification: ||
================================

<?xml version="1.0" encoding="utf-8"\?>

<rss version="2.0">

	<channel>
		<!-- Required channel elements -->
		<title></title> <!-- The name of the channel. It's how people refer to your service. --->
		<link></link> <!-- The URL to the HTML website corresponding to the channel. -->
		<description></description> <!-- Phrase or sentence describing the channel. -->

		<!-- Optional channel elements -->
		<image> <!-- Specifies a GIF, JPEG or PNG image that can be displayed with the channel -->
			<url></url> <!-- Is the URL of a GIF, JPEG or PNG image that represents the channel. -->
			<title></title> <!-- Describes the image, it's used in the ALT attribute of the HTML -->
			<link></link> <!-- Is the URL of the site, when the channel is rendered, the image is a link to the site -->
			<width></width> <!-- Maximum value for width is 144, default value is 88. -->
			<height></height> <!-- Maximum value for height is 400, default value is 31. -->
		</image>
		<language></language> <!-- The language the channel is written in. Example: en-US. Possible language-values: http://cyber.law.harvard.edu/rss/languages.html -->
		<copyright></copyright> <!-- Copyright notice for content in the channel. -->
		<managingEditor></managingEditor> <!-- Email address for person responsible for editorial content. -->
		<webMaster></webMaster> <!-- Email address for person responsible for technical issues relating to channel. -->
		<pubDate></pubDate> <!-- The publication date for the content in the channel. All date-times in RSS conform to the Date and Time Specification of RFC 822 [Sat, 07 Sep 2002 00:00:01 GMT] -->
		<lastBuildDate></lastBuildDate> <!-- The last time the content of the channel changed. Date and Time Specification of RFC 822 -->
		<category></category> <!-- Specify one or more categories that the channel belongs to. Follows the same rules as the <item>-level category element -->
		<generator></generator> <!-- A string indicating the program used to generate the channel. -->
		<docs><!--http://blogs.law.harvard.edu/tech/rss--></docs> <!-- A URL that points to the documentation for the format used in the RSS file. -->
		<cloud></cloud> <!-- Allows processes to register with a cloud to be notified of updates to the channel, implementing a lightweight publish-subscribe protocol for RSS feeds [HTTP-POST, XML-RPC, SOAP] -->
		<ttl></ttl> <!-- Time to live [minutes]. It's a number of minutes that indicates how long a channel can be cached before refreshing from the source. -->
		<rating></rating> <!-- The PICS rating for the channel [Platform for Internet Content Selection] -->
		<textInput> <!-- Specifies a text input box that can be displayed with the channel.The purpose is something of a mystery. You can use it to specify a search engine box. Or to allow a reader to provide feedback. Most aggregators ignore it. -->
			<title></title> <!-- The label of the Submit button in the text input area. -->
			<description></description> <!-- Explains the text input area. -->
			<name></name> <!-- The name of the text object in the text input area. -->
			<link></link> <!-- The URL of the CGI script that processes text input requests -->
		</textInput>
		<!-- These two sub-elements of <channel> came from scriptingNews format, designed in late 1997, and adopted by Netscape in RSS 0.91 in the spring of 1999. -->
		<skipHours> <!-- A hint for aggregators telling them which hours they can skip. An XML element that contains up to 24 <hour> sub-elements whose value is a number between 0 and 23, representing a time in GMT, when aggregators, if they support the feature, may not read the channel on hours listed in the skipHours element. The hour beginning at midnight is hour zero.-->
			<hour></hour>
			<hour></hour>
			<!-- ... up to 24 <hour> sub-elements ... -->
		</skipHours>
		<skipDays>
			<day></day>
			<day></day>
			<!-- ... up to 7 <day> sub-elements ... -->
		</skipDays> <!-- A hint for aggregators telling them which days they can skip. An XML element that contains up to seven <day> sub-elements whose value is Monday, Tuesday, Wednesday, Thursday, Friday, Saturday or Sunday. Aggregators may not read the channel during days listed in the skipDays element. -->

		<item> <!-- An item may represent a story -- much like a story in a newspaper or magazine. All elements of an item are optional, however at least one of title or description must be present. -->
			<guid isPermaLink="true"></guid> <!-- A string that uniquely identifies the item. isPermaLink is optional, its default value is true. If its value is false, the guid may not be assumed to be a url, or a url to anything in particular. -->
			<title></title> <!-- The title of the item. -->
			<link><![CDATA[]]></link> <!-- The URL of the item. -->
			<description> <!-- The item synopsis. -->
			<![CDATA[
			<table cellspacing="0" cellpadding="2" border="0">
				<tr>
					<td valign="top">
						<a href=""><img src="" width="" height="" alt="" title="" border="0" /></a>
					</td>
					<td valign="top">

					</td>
				</tr>
			</table>
			]]>
			</description>
			<category domain=""><![CDATA[]]></category> <!-- Includes the item in one or more categories. attribute, domain, a string that identifies a categorization taxonomy. -->
			<comments></comments> <!-- URL of a page for comments relating to the item. -->
			<enclosure url="" length="" type=""></enclosure> <!-- Describes a media object that is attached to the item. It has three required attributes. url says where the enclosure is located, length says how big it is in bytes, and type says what its type is, a standard MIME type. -->
			<pubDate></pubDate> <!-- Indicates when the item was published [Date and Time Specification of RFC 822]. -->
			<source url=""></source> <!-- The RSS channel that the item came from. <source url="http://www.site.net">Site Name</source> -->
			<author></author> <!-- Email address of the author of the item [author@email.net (Author Name)]. -->
		</item>

	</channel>

</rss>

*/
