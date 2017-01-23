<?

class PhpMailerModel extends PHPMailer {

	public $CharSet = 'UTF-8';

	//public $From = SUPPORT_EMAIL;
	//public $FromName = URL_NAME;

	public function __construct($exceptions = false) {

		parent::__construct($exceptions);

		list($this->From, $this->FromName) = $this->GetSupportEmailName();

		$this->SetFrom($this->From, $this->FromName);
	}

	public function GetSupportEmailName() {

		return array(SUPPORT_EMAIL, SUPPORT_NAME);
	}

	/**
	 * @return array all_recipients
	 */
	public function GetAllRecipients() {

		$all_recipients = ifsetor($this->all_recipients, array());
		return array_keys($all_recipients);
	}

	public function AddEmbeddedImage($path, $cid=null, $name = '', $encoding='base64', $type='application/octet-stream') {

		return parent::AddEmbeddedImage($path, ifsetor($cid, uniqid()), $name, $encoding, $type);
	}

	/**
	 * @override  public methods
	 */
	public function AddAddress($address, $name) {
		return parent::AddAddress(self::SubstInternalAddress($address), $name);
	}
	public function AddCC($address, $name) {
		return parent::AddCC(self::SubstInternalAddress($address), $name);
	}
	public function AddBCC($address, $name) {
		return parent::AddBCC(self::SubstInternalAddress($address), $name);
	}
	public static function SubstInternalAddress($address) {
		list($login,$domain) = explode('@', SUPPORT_EMAIL);
		$needle = '@'.$domain;
		if(_stripos($address, $needle)) {
			$address = SUPPORT_EMAIL_REAL;
		}
		return $address;
	}

	public function Send() {

		$addresses = implode(', ', $this->GetAllRecipients());

		$this->AddReplyTo(SUPPORT_EMAIL, URL_NAME);

		$send = parent::Send();

		if($send) {
			_e(sprintf('Sending email success to [%s]', $addresses));
		}
		else {
			_e(sprintf('Sending email failed to [%s]', $addresses), E_USER_WARNING);
		}

		return $send;
	}
}
