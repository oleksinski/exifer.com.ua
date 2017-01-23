<?

// Remove Stupid Denwer ModRewrite Behaviour
$_SERVER['PHP_SELF'] = $_SERVER['REQUEST_URI'];

require_once(dirname(__FILE__) . '/config.php');

require_once(ROOT_PATH . 'init.inc.php');

$__stopwatch = new StopWatch();

global $__rewrite;
$__rewrite = new RewriteModel();

//$__locale =& __locale();
//_e($__locale);

$__controllerRegexp = $__rewrite->getRegexp();
$__controlName = $__rewrite->getControlName();
$__controlAction = $__rewrite->getControlAction();
$__controlParams = $__rewrite->getControlParams();

if($__controlName && $__controlAction) {

	$__controlClass = _ucfirst($__controlName).'Control';

	$__control = new $__controlClass();

	$__control->CN = $__controlName;

	_e(sprintf('Url-Regexp: %s', $__controllerRegexp));

	_e(sprintf('Controller %s.control.php: %s->%s(%s);', $__controlName, $__controlClass, $__controlAction, implode(', ', $__controlParams)));

	if(method_exists($__control, $__controlAction)) {

		$__control->CA = $__controlAction;

		if(method_exists($__control, '_prefix')) {
			$__control->_prefix();
		}

		$tpl_fetched = call_user_func_array(array(&$__control, $__controlAction), $__controlParams);

		if(method_exists($__control, '_postfix')) {
			$__control->_postfix();
		}
	}
	else {
		_e(sprintf('Method %s->%s NOT implemented [%s]', $__controlName, $__controlAction, $__controlClass), E_USER_ERROR);
		$__controlModel = new ControlModel();
		$tpl_fetched = $__controlModel->page404();
	}

	//_e(array('TplFetchTime'=>$__control->tplFetchTimeArr));

	_e(sprintf('# Controller logic time: %s sec', $__stopwatch->getFormat(4)-$__control->tplFetchTimeFloat));

}
else {
	_e(sprintf('Page Controller handler is undefined, url-mapper regexp rule not found'), E_USER_ERROR);
	$__controlModel = new ControlModel();
	$tpl_fetched = $__controlModel->page404();
}

_e(sprintf('# Total page generating time: %s', $__stopwatch->getFormat(4)));

echo $tpl_fetched;

//echo implode('', Debug::$messages);

//_e(array('$_SERVER'=>$_SERVER));

//_e(array('$AutoloadedClasses$'=>AutoloadList::getList()));

_e(get_required_files());
