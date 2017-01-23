<?

require_once(dirname(__FILE__).'/header.exec.php');

$versioner =& Versioner::getInstance();
$versioner->writeStatic();

_e($versioner->getStaticFileArr());