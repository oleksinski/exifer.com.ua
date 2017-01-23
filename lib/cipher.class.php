<?

class Cipher {

	//@private
	private $key;
	private $iv;

	public function __construct($textkey=null) {
		$textkey = ifsetor($textkey, Network::clientHttpSignature());
		$this->key = hash('sha256', $textkey, true);
		$this->iv = mcrypt_create_iv(32, MCRYPT_RAND);
	}

	public function getKey() {
		return $this->key;
	}

	public function encrypt($input) {
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $input, MCRYPT_MODE_ECB, $this->iv));
	}

	public function decrypt($input) {
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
	}
}