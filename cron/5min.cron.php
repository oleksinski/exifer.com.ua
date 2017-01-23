<?

require_once(dirname(__FILE__).'/header.cron.php');

// clear storage
require_once(CRON_PATH . 'storage_clear_expired.exec.php');

// clear online user list
require_once(CRON_PATH . 'user_online.exec.php');