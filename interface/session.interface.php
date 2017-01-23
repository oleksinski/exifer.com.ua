<?

interface SessionInterface {

	public function set($key, $data, $lifetime=null);

	public function get($key);

	public function del($key);

	public function open();

	public function open($reopen=false);

	public function setlifetime($lifetime=0);
}