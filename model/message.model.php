<?

class MessageModel {

	public function user_register($user_id) {

		$result = false;

		$user = new User($user_id);

		if($user->exists()) {

			$message_subject = sprintf('Регистрация на %s', PROJECT_NAME);
			$message_body = implode("\n", array(
				$user->getField('name').',',
				sprintf('Вы зарегистрированы на сайте %s.', URL_NAME),
				'Регистрационные данные для входа на сайт:',
				sprintf('Email: %s', $user->getField('email')),
				sprintf('Пароль: %s', $user->getField('password')),
				self::footer(),
			));

			$result = self::send_from_support($message_subject, $message_body, $user->getField('email'), $user->getField('name'));
		}

		return $result;
	}

	public function comment_photo($id) {

		$result = false;

		$comment = new CommentPhoto($id);

		if($comment->exists()) {

			$sender = $comment->getUserObject();
			$sender_name = $sender->getField('name');
			$sender->setCustomField('gender_end', $sender->isMale() ? '' : 'а');
			$message_subject = sprintf('%s прокомментировал%s Вашу фотографию', $sender_name, $sender->getCustomField('gender_end'));

			$item = $comment->getItemObject();
			$receiver = $item->getUserObject();

			$excludeReceiverIdList = array(
				182, // vadlen
			);

			if($receiver->getId()!=$sender->getId() && !in_array($receiver->getId(), $excludeReceiverIdList)) {

				$message_body = implode("\n", array(
					$receiver->getField('name').',',
					'',
					sprintf('%s оставил%s комментарий к Вашей фотографии', $sender->getField('name'), $sender->getCustomField('gender_end')),
					UrlModel::photo($item->getId(), $item),
					'',
					$comment->getField('text'),
					self::footer(),
				));

				$result = self::send_from_support($message_subject, $message_body, $receiver->getField('email'), $receiver->getField('name'));
			}
		}

		return $result;
	}

	public function support_feedback($feedback_id) {

		$result = false;

		$feedback_arr = FeedbackModel::SelectOne($feedback_id);

		if($feedback_arr) {

			$name = $feedback_arr['username'];
			$email = $feedback_arr['email'];

			if($feedback_arr['user_id']) {
				$user = new User($feedback_arr['user_id']);
				$name = $user->getField('name');
				$email = $user->getField('email');
			}

			$message_subject = $feedback_arr['subject'] ? $feedback_arr['subject'] : sprintf('support_feedback');
			$message_body = $feedback_arr['message'];

			if($email) {
				$result = self::send_to_support($message_subject, $message_body, $email, $name);
			}
		}

		return $result;
	}

	public static function send_from_support($message_subject, $message_body, $email_to, $name_to=null) {

		$result = false;

		_e('# SEND EMAIL FROM SUPPORT #');

		if(Predicate::server_pro()) {
			$PhpMailer = new PhpMailerModel();
			$PhpMailer->Subject = $message_subject;
			$PhpMailer->Body = $message_body;
			$PhpMailer->AddAddress($email_to, $name_to);
			$result = $PhpMailer->Send();
			$PhpMailer->ClearAllRecipients();
		}
		else {
			_e(array(
				'$message_subject'=>$message_subject,
				'$message_body'=>$message_body,
				'$email_to'=>$email_to,
				'$name_to'=>$name_to,
			));
			$result = true;
		}

		return $result;
	}

	public static function send_to_support($message_subject, $message_body, $email_from, $name_from=null) {

		$result = false;

		_e('# SEND EMAIL TO SUPPORT #');

		if(Predicate::server_pro()) {
			$PhpMailer = new PhpMailerModel();
			$PhpMailer->Subject = $message_subject;
			$PhpMailer->Body = $message_body;
			$PhpMailer->SetFrom($email_from, $name_from);
			list($email_to, $name_to) = $PhpMailer->GetSupportEmailName();
			$PhpMailer->AddAddress($email_to, $name_to);
			$result = $PhpMailer->Send();
			$PhpMailer->ClearAllRecipients();
		}
		else {
			_e(array(
				'$message_subject'=>$message_subject,
				'$message_body'=>$message_body,
				'$email_from'=>$email_from,
				'$name_from'=>$name_from,
			));
			$result = true;
		}
		return $result;
	}

	public static function footer() {

		$sitename = URL_NAME;

		$tpl = <<<EOT

---
С уважением,
Команда $sitename
EOT;
		return $tpl;
	}

}
