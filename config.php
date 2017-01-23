<?

// ======== ENVIRONMENT INIT ==========
$php_uname = php_uname('n');
define('SERVER_PRO', $php_uname=='pro');
define('SERVER_LAB', $php_uname=='lab');
define('SERVER_DEV', $php_uname=='dev');
// ======== /ENVIRONMENT INIT ==========

define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__).'/'));

switch(true) {

case SERVER_DEV:

define('URL_DOMAIN', 'exifer.dev');
define('URL_NAME', 'EXIFER.dev');

define('MYSQL_HOSTNAME', 'localhost');
define('MYSQL_USERNAME', 'exifer');
define('MYSQL_PASSWORD', 'MYSQL_PASSWORD_GOES_HERE');

break;

case SERVER_PRO:
case SERVER_LAB:
default:

define('URL_DOMAIN', 'exifer.com.ua');
define('URL_NAME', 'EXIFER.com.ua');

define('MYSQL_HOSTNAME', 'localhost');
define('MYSQL_USERNAME', 'exifer');
define('MYSQL_PASSWORD', 'MYSQL_PASSWORD_GOES_HERE');

break;

}

define('MYSQL_DATABASE', 'exifer');
define('MYSQL_DATABASE_ETC', 'etc');

define('MEMCACHE_HOST', 'localhost');
define('MEMCACHE_PORT', 11211);

define('URL_PROJECT', sprintf('http://%s', URL_DOMAIN));

define('PROJECT_NAME', 'Exifer');

define('URL_DOT_DOMAIN', '.'.URL_DOMAIN);
define('URL_DOT_PROJECT', '.'.parse_url(URL_PROJECT, PHP_URL_HOST));

define('LIB_PATH', ROOT_PATH.'lib/');

define('LIB_PATH_EXT', LIB_PATH.'external/');

define('FUNC_PATH', ROOT_PATH.'func/');

define('CONTROL_PATH', ROOT_PATH.'control/');

define('MODEL_PATH', ROOT_PATH.'model/');

define('TPL_PATH', ROOT_PATH.'tpl/');

define('STATIC_PATH', ROOT_PATH.'static/');

define('INTERFACE_PATH', ROOT_PATH.'interface/');

define('SMARTY_PATH', ROOT_PATH.'smarty/');
define('SMARTY_STATIC_PATH', STATIC_PATH.'smarty/');
define('SMARTY_LIB', SMARTY_PATH.'Smarty-3.0.7/');
define('SMARTY_USR_PATH', SMARTY_PATH.'usr/');
define('SMARTY_USR_PLUGIN_PATH', SMARTY_USR_PATH.'plugin/');
define('SMARTY_USR_TPL_PATH', SMARTY_USR_PATH.'template/');

define('LOCALE_PATH', ROOT_PATH.'locale/');

define('I_PATH', ROOT_PATH.'../i/');
define('I_URL',  sprintf('http://i.%s/', URL_DOMAIN));

define('S_PATH', ROOT_PATH.'../s/');
define('S_URL',  sprintf('http://s.%s/', URL_DOMAIN));

define('CRON_PATH', ROOT_PATH.'cron/');

define('SESSION_PHP_PATH', '/var/tmp/session_php/');

define('CONST_COMPRESS_OUTPUT', (bool)SERVER_PRO);

define('PHOTO_THUMB_SECRET', 'SECRET_HASH_KEY_GOES_HERE');

define('SUPPORT_EMAIL', 'support@email.com');
define('SUPPORT_NAME', sprintf('Photosite %s', URL_NAME));
define('SUPPORT_EMAIL_REAL', 'support-alias@email.com');
