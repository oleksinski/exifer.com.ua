<?

class LocaleModel {

	public static function &LocaleNameModifyList(&$list) {

		if(!empty($list)) {

			$__locale =& __locale();

			$lang = $__locale->getLang();

			foreach($list as $id=>&$data) {

				if(!empty($data)) {

					//$name_ru = ifsetor($data['name_ru'], null);
					//$name_ua = ifsetor($data['name_ua'], null);
					$name_en = ifsetor($data['name_en'], null);
					$name_lo = ifsetor($data['name_'.$lang], null);

					if($name_lo) {
						$data['name'] = $name_lo;
					}
					elseif($name_en) {
						$data['name'] = $name_en;
					}
					else {
						$data['name'] = null;
					}
				}
			}

			uasort($list, Util::CreateFunction('$a, $b', 'return strcmp($a["name"], $b["name"]);'));
		}

		return $list;
	}

	public static function &LocaleFieldOrderList($list, $field) {

		if(!empty($list)) {

			$callback_func = Util::CreateFunction('$a, $b', '

				$c = ifsetor($a["'.$field.'"], null);
				$d = ifsetor($b["'.$field.'"], null);

				if(is_numeric($c) && is_numeric($d)) {
					if($c==$d) return 0;
					return ($c<$d) ? -1 : +1;
				}
				else {
					return strcmp($c, $d);
				}
			');

			uasort($list, $callback_func);
		}

		return $list;
	}
}