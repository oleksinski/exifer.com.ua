<?

interface StorageInterface {

	public function set($storage_key, $storage_data, $lifetime=0);

	public function get($storage_key);

	public function del($storage_key);

	public function get_key($key=null);

	public function clear();

	public function touch($session_key, $lifetime=0);

	public function clear_expired_data($tstamp=null);
}