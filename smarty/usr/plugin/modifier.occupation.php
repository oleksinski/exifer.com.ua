<?

function smarty_modifier_occupation($user, $ignore_experience=false) {

	$occupation_txt = null;

	if(is_object($user)) {

		$occupation = $user->getExtraField('occupation');

		$occupation_txt = '&#8230;'; // ...

		if($occupation) {

			$occupationList = OccupationModel::GetStaticOccupationList();
			$experienceList = OccupationModel::GetStaticExperienceList();

			$occ_arr = array();

			foreach($occupation as $o_id=>$e_id) {

				$o_data = ifsetor($occupationList[$o_id], array());

				$o_name = ifsetor($o_data['name'], null);

				if($o_name) {

					//$occupation_txt .= $o_name;

					$experience = $occupation[$o_id];

					$ex_arr = array();

					foreach($experience as $e_id) {

						$e_data = ifsetor($experienceList[$e_id], array());

						$e_name = ifsetor($e_data['name'], null);

						if($e_name) {
							$ex_arr[] = $e_name;
						}
					}

					$ex_txt = $ex_arr ? sprintf('<span class="gray small"> (%s)</span>', implode(', ', $ex_arr)) : null;

					if($ex_txt && !$ignore_experience) {
						$o_name .= $ex_txt;
					}

					$occ_arr[] = $o_name;
				}
			}

			$occupation_txt = implode(', ', $occ_arr);
		}
	}

	return $occupation_txt;
}