<?

/**
 * Captcha Model
 *
 */

class CaptchaModel extends PhpCaptcha {

	const CUSTOM_WIDTH = 200;
	const CUSTOM_HEIGHT = 50;
	const CUSTOM_CHARS = 5;

	public function __construct() {

		$aFonts = array('VeraBd.ttf', 'VeraIt.ttf', 'Vera.ttf');
		foreach($aFonts as &$a) {
			$a = Url::fix(LIB_PATH_EXT.'/php_captcha/fonts/'.$a);
		}

		parent::__construct($aFonts);

		$this->sCaptchaSecretWord = 'Hack_mE_35#2-4H';
	}

	public function createCaptchaTypeOne() {

		$this->SetWidth(self::CUSTOM_WIDTH);
		$this->SetHeight(self::CUSTOM_HEIGHT);
		$this->SetNumChars(self::CUSTOM_CHARS);
		$this->SetNumLines(30);
		//$this->SetCharSet('a-z,A-Z,0-9');
		//$this->CaseInsensitive(true);
		$this->SetMinFontSize(16);
		$this->SetMaxFontSize(20);
		//$this->SetFileType('jpeg');//jpeg, gif or png

		//$this->SetOwnerText('Random copyright text');
		//$this->DisplayShadow(true);
		//$this->SetBackgroundImages($PHP_CAPTCHA_PATH.'bg.jpg');
		$this->UseColour(true);

		$this->Create();
	}

	/**
	 * @override
	 * @see PhpCaptcha::getCaptchaStorageKey()
	 */
	public function getCaptchaStorageKey() {
		$salt = $this->sCaptchaSecretWord;
		$http_signature = Network::clientHttpSignature();
		$captchaKeyRaw = $salt.$http_signature;
		$captchaKey = md5($captchaKeyRaw);
		_e(sprintf('Captcha raw = %s, key = %s', $captchaKeyRaw, $captchaKey));
		return $captchaKey;
	}

	public function getCaptchaMainParams() {
		$settings = array(
			'width' => $this->iWidth,
			'height' => $this->iHeight,
			'chars' => $this->iNumChars,
			'caseInsensitive' => $this->bCaseInsensitive,
			'rand' => rand(1, 10000),
			'imgsrc' => sprintf('%s/captcha/image.%s?rand=%u&dbg=0', URL_PROJECT, $this->sFileType, rand(1, 100000)),
		);

		return $settings;
	}
}
