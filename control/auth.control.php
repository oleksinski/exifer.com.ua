<?

class AuthControl extends ControlModel {

	public function login() {

		$url = Url::decode(ifsetor($_REQUEST['url'], null));

		if(User::getOnlineUserId()) {
			$url = ifsetor($url, UrlModel::user(User::getOnlineUserId(), User::getOnlineUser()));
			UrlModel::redirect($url);
		}

		$__error = new ErrorModel();
		$__validator = new ValidatorModel();

		$r_email = null;
		$postreg = ifsetor($_GET['postreg'], null);
		if($postreg) {
			$cipher = new Cipher();
			$r_email = $cipher->decrypt($postreg);
		}

		$email = _trim(ifsetor($_POST['email'], $r_email));
		$password = _trim(ifsetor($_POST['password'], null));
		$remember = ifsetor($_POST['remember'], User::getOnlineRemember());
		$captcha = ifsetor($_POST['captcha'], null);

		$__storage =& __storage();
		$storage_key = md5('auth_login' . Network::clientHttpSignature());
		$storage_lifetime = 5*60;
		$login_attempt = Cast::int($__storage->get($storage_key));
		$login_spamer = $login_attempt>=5;

		if($login_spamer) {
			$CaptchaModel = new CaptchaModel();
		}

		if(Predicate::posted()) {

			if($email!=='' && $password!=='') {

				if($login_spamer && !$CaptchaModel->Validate($captcha)) {
					$__error->push('CAPTCHA_ERROR');
				}

				if($__error->isOk()) {

					$user = new User();
					$user->load(array('email'=>$email, 'password'=>$password));

					if($user->exists()) {
						if($user->getField('status')==User::STATUS_OKE) {
							if($user->isBanned()) {
								$__error->push('AUTH_BANNED', array(date('d/m/Y H:i', $user->getField('ban_tstamp'))));
							}
							else {
								if($user->login($remember)) {
									$__storage->del($storage_key); // del spamer login counter
									UrlModel::redirect($url);
								}
							}
						}
						elseif($user->getField('status')==User::STATUS_NEW) {
							$__error->push('AUTH_NOT_ACTIVATED');
						}
					}
					else {
						$__error->push('AUTH_FAIL');
						$__storage->set($storage_key, $login_attempt+1, $storage_lifetime);
					}
				}
			}
			else {
				$__error->push('AUTH_FAIL');
			}
		}

		$assign = array(
			'auth' => array('email'=>$email, 'remember'=>(int)$remember, 'url' => Url::encode($url)),
			'Error' => $__error,
			'Validator' => $__validator,
		);

		if($login_spamer) {
			$assign += array(
				'captcha_ini' => $CaptchaModel->getCaptchaMainParams(),
			);
		}

		$this->setHtmlMetaTitle(SeoModel::auth_login(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::auth_login(SeoModel::DESCRIPTION));

		return $this->layout('auth/login.tpl', $assign);
	}

	public function logout($url=null) {
		User::getOnlineUser()->logout();
		UrlModel::redirect($url);
	}

	public function remind() {

		$__error = new ErrorModel();
		$__validator = new ValidatorModel();

		$user = new User();
		$user->setCustomField('email', ifsetor($_POST['email'], null));
		$user->setCustomField('url', Url::encode(Url::decode(ifsetor($_REQUEST['url'], null))));

		if(Predicate::posted()) {

			$email = $user->getCustomField('email');
			if($email) {

				$__storage =& __storage();
				$storage_key = md5('auth_remind' . Network::clientHttpSignature());
				$storage_lifetime = 2*60;
				$remind_attempt = Cast::int($__storage->get($storage_key));
				$remind_spamer = $remind_attempt>=3;

				if(!$remind_spamer) {

					$user->load(array('email'=>$email));

					if($user->exists()) {

						if(!$remind_spamer) {

							// send activation email
							$remind_ok = MessageModel::user_register($user->getId());

							$user->setCustomField('reminded', $remind_ok);
						}
					}
					else {
						$__error->push('EMAIL_NOT_EXISTS');
					}
					$__storage->set($storage_key, $remind_attempt+1, $storage_lifetime);
				}
				else {
					$__error->push('AUTH_REMIND_DDOS');
				}
			}
			else {
				$__error->push('EMAIL_FORMAT');
			}
		}
		$assign = array(
			'user' => $user,
			'Error' => $__error,
			'Validator' => $__validator,
		);

		$this->setHtmlMetaTitle(SeoModel::auth_remind(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::auth_remind(SeoModel::DESCRIPTION));

		return $this->layout('auth/remind.tpl', $assign);
	}

}
