<?

class VoteControl extends ControlModel {

	public function add() {

		$redirect_url = null;
		$json_response = array();

		$item_type = $this->parseItemType();
		$is_json = $this->isJson();

		switch($item_type) {

			case Vote::TYPE_PHOTO:
			default:

				$vote = new VotePhoto();
				$vote->add();

				$item_id = $this->getItemId($vote->getField('item_id'));

				$json_response = $this->get_array($item_id, $vote);

				if($vote->getId()) {
					$redirect_url = $vote->getExtraField('url');
				}
				else {
					$json_response['html'] = null;
				}

				break;
		}
		if($is_json) {
			return JsonModel::encode($json_response);
		}
		else {
			UrlModel::redirect($redirect_url);
		}

	}

	// ---

	public function del() {

		$json_response = array();

		$item_type = $this->parseItemType();
		$is_json = $this->isJson();

		$vote_id = ifsetor($_REQUEST['id'], 0);

		switch($item_type) {
			case Vote::TYPE_PHOTO:
			default:
				$vote = new VotePhoto($vote_id); $vote->load();
		}
		if(!$vote) $vote = new VoteBimbo();

		if($vote->isRemovable()) {
			$item_id = $this->getItemId($vote->getField('item_id'));
			$vote->removeById();
			if($is_json) {
				$json_response = $this->get_array($item_id, new VotePhoto());
			}
		}
		elseif($is_json) {
			$__error = new ErrorModel();
			$__error->push('VOTE_NO_MODER_PERMISSION');
			$json_response['error'] = $__error->getErrorValues();
		}

		if($is_json) {
			return JsonModel::encode($json_response);
		}
		else {
			UrlModel::redirect();
		}
	}

	// ---

	/**
	 * @param unknown_type $item_id
	 * @param unknown_type $item_type
	 * @param unknown_type $format [plain, array, json]
	 */
	public function get($item_id=null, $item_type=null, $format=null) {

		$result = null;

		$item_id = $this->getItemId($item_id);
		$item_type = $this->parseItemType($item_type);
		$format = ifsetor($format, null);

		$is_json = $this->isJson();

		if($is_json && is_null($format)) {
			$format = 'json';
		}

		$format = insetor($format, array('plain', 'array', 'json'), 'plain');

		switch($item_type) {
			case Vote::TYPE_PHOTO:
				$vote = new VotePhoto();
				break;
			default:
				$vote = new Vote();
				break;
		}

		switch($format) {
			case 'plain':
				$result = $this->get_plain($item_id, $vote);
				break;
			case 'array':
				$result = $this->get_array($item_id, $vote);
				break;
			case 'json':
				$result = $this->get_json($item_id, $vote);
				break;
		}

		return $result;
	}

	private function get_plain($item_id, Vote $vote) {
		$result = null;
		if($item_id) {
			$vote_collection = $this->getItemVoteCollection($item_id, $vote);
			$assign = array(
				'vote_collection' => $vote_collection,
				'item_id' => $item_id,
			);
			$result = $this->render('vote/item.tpl', $assign);
		}
		else {
			_e('# item_id is undefined', E_USER_WARNING);
		}
		return $result;
	}

	/**
	 *
	 * @param unknown_type $item_id
	 * @param Vote $vote
	 * @param array $result
	 */
	private function get_array($item_id, Vote $vote) {

		$result = array();

		$vote_collection = $this->getItemVoteCollection($item_id, $vote);

		$result['id'] = $vote->getId();
		$result['item_id'] = Cast::int($item_id);
		$result['votes'] = array(
			'total' => $vote_collection->getCountTotal(),
			'pros' => $vote_collection->getCountPros(),
			'cons' => $vote_collection->getCountCons(),
			'zero' => $vote_collection->getCountZero(),
			'value' => $vote_collection->getValueSumSigned(),
		);
		$result['url'] = $vote->getExtraField('url');
		$result['error'] = $vote->getErrorValues();
		$result['html'] = $this->get_plain($item_id, $vote);

		return $result;
	}

	private function get_json($item_id, Vote $vote) {
		return JsonModel::encode($this->get_array($item_id, $vote));
	}

	// ---

	/**
	 * Get Vote Object Collection
	 * @param unknown_type $item_id
	 * @param Vote $vote
	 * @param unknown_type $refresh
	 * @return VoteCollection $vote_collection
	 */
	private function getItemVoteCollection($item_id, Vote $vote, $refresh=false) {

		$vote_collection = null;

		static $collection = array();

		if($item_id) {

			$item_type = $vote->getItemType();

			if(array_key_exists($item_type, $collection) && array_key_exists($item_id, $collection[$item_type]) && !$refresh) {
				$vote_collection = $collection[$item_type][$item_id];
			}
			elseif($item_type==Vote::TYPE_PHOTO) {
				$vote_collection = new VotePhotoCollection();
			}

			if(!is_null($vote_collection)) {
				$vote_collection->getCollectionByItemId($item_id);
				$vote_collection->getUserObjectCollection();
				$collection[$item_type][$item_id] = $vote_collection;
			}
		}

		if(is_null($vote_collection)) {
			$vote_collection = new VoteBimboCollection();
		}

		return $vote_collection;
	}

	// ---

	private function getItemId($item_id=null) {
		return ifsetor($item_id, ifsetor($_REQUEST['item_id'], 0));
	}

	private function parseItemType($item_type=null) {
		$item_type = ifsetor($item_type, ifsetor($_REQUEST['item_type'], Vote::TYPE_PHOTO));
		$item_type = insetor($item_type, array(Vote::TYPE_PHOTO), Vote::TYPE_PHOTO);
		return $item_type;
	}

	private function isJson() {
		return ifsetor($_REQUEST['json'], false, true);
	}
}
