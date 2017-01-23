<?

/**
 * Pager class.
 *
 * Pager-param can be set as
 * 1) GET-param
 * 2) URL param between slash-separated url-symbols
 *
 * Usage:
 * ^^^^^^
 *
 * $__pager = new Pager($totalCount, $onPage=self::__PERPAGE);
 *
 * Full interface reference:
 * ^^^^^^^^^^^^^^^^^^^^^^^^
 *
 * $__pager->setParamType($paramType=self::__TYPE_URL);
 * $__pager->setUrlParamPrefix($pattern=self::URL_PARAM_PREFIX);
 * $__pager->setGetParamName($GetParamName=self::GET_PARAM_NAME);
 * $__pager->setId(uniqid());
 * $__pager->initCurrentPage();
 * $__pager->getOnPage();
 * $__pager->getCurrentPage();
 * $__pager->getGetParamName();
 * $__pager->getUrlParamPrefix();
 * $__pager->getPageCount();
 * $__pager->getPageUrl();
 * $__pager->getPageUri();
 * $__pager->getPageUrlQuery();
 * $__pager->buildLink($pageNum);
 *
 */

class Pager {

	// const list
	const __TYPE_GET = 1;
	const __TYPE_URL = 2;
	const __TYPE_DEF = 2;

	const GET_PARAM_NAME = 'p'; // http://site.net/?p=1
	const URL_PARAM_PREFIX = 'p'; // http://site.net/p1

	const __PERPAGE = 10;

	const __PAGE_ONE = 1; // 0

	// variable settings
	protected $GetParamName;
	protected $urlParamPrefix;
	protected $paramType; // GET, URL

	// Input params
	protected $onPage;
	protected $currentPage;
	protected $totalCount;

	// Calculated params
	protected $pageCount;
	protected $containerId;
	protected $uniqId;

	// bool flag indicating constructor call finished
	protected $constructed;

	protected $__rewrite;

	public function __construct($totalCount, $onPage=self::__PERPAGE) {

		$this->setParamType();
		$this->setUrlParamPrefix();
		$this->setGetParamName();
		$this->genUniqueId();

		$this->onPage = Cast::unsignint($onPage);
		if($this->onPage==0) {
			$this->onPage = self::__PERPAGE;
		}

		$totalCount = Cast::unsignint($totalCount);
		$this->pageCount = ceil($totalCount / $this->onPage);
		$this->totalCount = $totalCount;

		$this->initCurrentPage();

		$this->constructed = true;
	}

	public function __clone() {
		$this->genUniqueId();
	}

	public function genUniqueId() {
		$this->uniqId = uniqid();
		$this->setContainerId($this->uniqId);
	}

	public function setContainerId($id) {
		$this->containerId = $id;
	}

	public function setParamType($paramType=self::__TYPE_DEF) {

		//_e('called setParamType');

		$paramTypePrev = $this->paramType;

		$paramType = insetor($paramType, array(self::__TYPE_URL, self::__TYPE_GET), self::__TYPE_DEF);

		if($this->constructed && $paramTypePrev!=$this->paramType) {
			$this->initCurrentPage();
		}
	}

	public function setUrlParamPrefix($pattern=self::URL_PARAM_PREFIX) {

		$urlParamPrefixPrev = $this->urlParamPrefix;

		$this->urlParamPrefix = Cast::str($pattern);

		if($this->constructed && $this->paramType==self::__TYPE_URL && $urlParamPrefixPrev!=$this->urlParamPrefix) {
			$this->initCurrentPage();
		}
	}

	public function setGetParamName($GetParamName=self::GET_PARAM_NAME) {

		$GetParamNamePrev = $this->GetParamName;

		$this->GetParamName = Cast::str($GetParamName);

		if($this->constructed && $this->paramType==self::__TYPE_GET && $GetParamNamePrev!=$this->GetParamName) {
			$this->initCurrentPage();
		}
	}

	private function initCurrentPage() {

		$this->currentPage = self::__PAGE_ONE;

		$__rewrite = new Rewrite();
		$__pathArr = $__rewrite->getPathArr();
		$__queryArr = $__rewrite->getQueryArr();

		switch($this->paramType) {
			case self::__TYPE_URL:
				if($urlParam = array_pop($__pathArr)) {
					$pattern = '#^'.quotemeta($this->urlParamPrefix).'(\d+)$#';
					if(_preg_match($pattern, $urlParam, $matches)) {
						$foundPattern = ifsetor($matches[0], '');
						$this->currentPage = ifsetor($matches[1], 1);
						$__rewrite->modifyPathArr($__pathArr);
					}
				}
				break;
			case self::__TYPE_GET:
			default:
				if(array_key_exists($this->GetParamName, $__queryArr)) {
					$this->currentPage = Cast::int($__queryArr[$this->GetParamName]);
					unset($__queryArr[$this->GetParamName]);
					$__rewrite->modifyQueryArr($__queryArr);
				}
				break;
		}

		$this->__rewrite =& $__rewrite;

		if($this->currentPage<=0) {
			$this->currentPage = self::__PAGE_ONE;
		}
		elseif($this->currentPage>$this->pageCount) {
			$this->currentPage = $this->pageCount;
		}

		return $this->currentPage;
	}

	public function getRewritePattern() {

		$__rewrite = clone $this->__rewrite;

		switch($this->paramType) {
			case self::__TYPE_URL:
				$__pathArr = $__rewrite->getPathArr();
				$__pathArr[] = sprintf('%s%s', $this->urlParamPrefix, $this->uniqId);
				$__rewrite->modifyPathArr($__pathArr);
				break;
			case self::__TYPE_GET:
			default:
				$__queryArr = $__rewrite->getQueryArr();
				//$__queryArr[$this->GetParamName] = $pageNum;
				$__queryArr[$this->GetParamName] = $this->uniqId;
				$__rewrite->modifyQueryArr($__queryArr);
				break;
		}
		return $__rewrite;
	}

	public function buildPatternUrl() {
		return $this->getRewritePattern()->getUrl();
	}

	public function buildPatternQuery() {
		return $this->getRewritePattern()->getQuery();
	}

	public function buildLink($pageNum) {

		$pageNum = Cast::unsignint($pageNum);

		if($pageNum > $this->pageCount) {
			$pageNum = $this->pageCount;
		}

		$link = _str_replace($this->uniqId, $pageNum, $this->buildPatternUrl());

		return $link;
	}

	public function getContainerId() {
		return $this->containerId;
	}

	public function getUniqId() {
		return $this->uniqId;
	}

	public function getOnPage() {
		return $this->onPage;
	}

	public function getCurrentPage() {
		return $this->currentPage;
	}

	public static function getCurrentPageSql($page) {
		$page = Cast::int($page);
		$page -= self::__PAGE_ONE;
		if($page<0) $page=0;
		return $page;
	}

	public function getGetParamName() {
		return $this->GetParamName;
	}

	public function getUrlParamPrefix() {
		return $this->urlParamPrefix;
	}

	public function getTotalCount() {
		return $this->totalCount;
	}

	public function getPageCount() {
		return $this->pageCount;
	}

	public function getPageUrl() {
		return $this->__rewrite->getUrl();
	}

	public function getPageUri() {
		return $this->__rewrite->getUri();
	}

	public function getPageUrlQuery() {
		return $this->__rewrite->getQuery();
	}

}
