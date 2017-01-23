<?

class SupportControl extends ControlModel {

	public function feedback() {

		$__error = new ErrorModel();
		$__validator = new ValidatorModel();

		$assign = array();

		$feedback = array(
			'username' => ifsetor($_POST['username'], null),
			'email' => ifsetor($_POST['email'], null),
			'subject' => ifsetor($_POST['subject'], null),
			'message' => ifsetor($_POST['message'], null),
			'captcha' => ifsetor($_POST['captcha'], null),
		);

		$user = User::getOnlineUser();

		$CaptchaModel = new CaptchaModel();

		$__storage =& __storage();
		$storage_key = md5('support_feedback' . Network::clientHttpSignature());

		if(Predicate::posted()) {

			if(!User::isLoginned()) {

				SafeHtmlModel::input(&$feedback['email']);
				if(!$__validator->user_email($feedback['email'])) {
					$__error->push('EMAIL_FORMAT');
				}

				SafeHtmlModel::input(&$feedback['username']);
				if(!$__validator->user_name($feedback['username'])) {
					$__error->push('NAME_FORMAT');
				}

				if(!$CaptchaModel->Validate($feedback['captcha'])) {
					$__error->push('CAPTCHA_ERROR');
				}
			}

			SafeHtmlModel::input(&$feedback['subject']);

			SafeHtmlModel::input(&$feedback['message']);
			if(!$__validator->feedback_message($feedback['message'])) {
				$__error->push('FEEDBACK_MESSAGE');
			}

			if($__error->isOk()) {

				$insert_arr = array(
					'subject' => $feedback['subject'],
					'message' => $feedback['message'],
				);

				if($user->exists()) {
					$insert_arr += array(
						'user_id' => $user->getId(),
					);
				}
				else {
					$insert_arr += array(
						'username' => $feedback['username'],
						'email' => $feedback['email'],
					);
				}

				$feedback_id = FeedbackModel::Insert($insert_arr);

				if($feedback_id) {

					$__storage->set($storage_key, $feedback_id, 10);

					// send email
					MessageModel::support_feedback($feedback_id);
				}

				UrlModel::redirect(UrlModel::support_feedback());
			}
		}

		$assign += array(
			'feedback' => $feedback,
			'DATA_SAVED_FLAG' => $__storage->get($storage_key),
			'Error' => $__error,
			'Validator' => $__validator,
		);

		if(!User::isLoginned()) {
			$assign += array(
				'captcha_ini' => $CaptchaModel->getCaptchaMainParams(),
			);
		}

		$this->setHtmlMetaTitle(SeoModel::support_feedback(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::support_feedback(SeoModel::DESCRIPTION));

		return $this->layout('support/feedback.tpl', $assign);
	}

	public function about() {
		$this->setHtmlMetaTitle(SeoModel::support_about(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::support_about(SeoModel::DESCRIPTION));
		return $this->layout('support/about.tpl');
	}

	public function eula() {
		$this->setHtmlMetaTitle(SeoModel::support_eula(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::support_eula(SeoModel::DESCRIPTION));
		return $this->layout('support/eula.tpl');
	}

	public function rules() {
		$this->setHtmlMetaTitle(SeoModel::support_rules(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::support_rules(SeoModel::DESCRIPTION));
		return $this->layout('support/rules.tpl');
	}

	public function adult() {
		$this->setHtmlMetaTitle(SeoModel::support_adult(SeoModel::TITLE));
		$this->setHtmlMetaDescription(SeoModel::support_adult(SeoModel::DESCRIPTION));
		return $this->layout('support/adult.tpl');
	}
}
