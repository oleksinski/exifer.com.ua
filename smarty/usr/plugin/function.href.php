<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * Get url address from UrlModel class
 *
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 *
 *
 * Params reference:
 * ^^^^^^^^^^^^^^^^^
 * {href target='user' p1=666293}
 * {href target='user' p1=666293 u_data=$user_arr}
 * {href target='photo' p1=1}
 * {href target='photo' p1=1 p_data=$photo_arr}
 *
 */

function smarty_function_href($params, &$smarty) {

	$result = '#';

	static $url_model = null;
	if(is_null($url_model)) {
		$url_model = new UrlModel();
	}

	$func = isset($params['target']) ? $params['target'] : null;
	$relative = isset($params['relative']) ? $params['relative'] : true;

	if(!method_exists($url_model, $func)) {
		return $result;
	}

	$query_dependent_func_arr = array(
		'photo_lenta',
		'user_lenta',
		'comment_lenta',
		'rss_photo',
		'rss_user',
		'rss_comment',
		'vote'
	);

	if(insetcheck($func, $query_dependent_func_arr)) {

		$query = isset($params['query']) ? $params['query'] : array();
		$ignore_query = isset($params['ignore_query']) ? $params['ignore_query'] : !empty($query);

		$__rewrite = new Rewrite(UrlModel::$func());

		if($ignore_query) {
			$query_new = $query;
		}
		else {
			$query_new = $__rewrite->getQueryArr() + $query;
		}

		$allowed_param_list = array();

		if($func=='photo_lenta') {
			$allowed_param_list = array(
				'uid', 'genre', 'date', 'viewmode', 'orderby', 'ordermethod', 'q', 'p',
				'status', 'moderated', // for admin
			);
			foreach($allowed_param_list as $p) {
				if(!array_key_exists($p, $params)) {
					unset($allowed_param_list[$p]);
				}
			}
		}
		elseif($func=='user_lenta') {
			$allowed_param_list = array(
				'date', 'viewmode', 'orderby', 'ordermethod', 'q', 'p',
				'occupation', 'experience',
				'country', 'city',
				'online',
				'status', // for admin
			);
			foreach($allowed_param_list as $p) {
				if(!array_key_exists($p, $params)) {
					unset($allowed_param_list[$p]);
				}
			}
		}
		elseif($func=='comment_lenta') {
			$allowed_param_list = array(
				'uid', 'genre', 'date', 'q', 'p',
			);
			foreach($allowed_param_list as $p) {
				if(!array_key_exists($p, $params)) {
					unset($allowed_param_list[$p]);
				}
			}
		}
		elseif($func=='rss_photo') {
			$allowed_param_list = array(
				'uid', 'genre',
			);
		}
		elseif($func=='rss_user') {
			$allowed_param_list = array(
				'occupation', 'experience',
			);
		}
		elseif($func=='rss_comment') {
			$allowed_param_list = array(
				'uid', 'pid',
			);
		}
		elseif($func=='vote') {
			$allowed_param_list = array(
				'item_id', 'item_target', 'action', 'json',
			);
		}

		foreach($allowed_param_list as $p) {
			if(array_key_exists($p, $params)) {
				$query_new[$p] = $params[$p];
				if(is_null($params[$p]) || $params[$p]==='') {
					unset($query_new[$p]);
				}
			}
		}

		$__rewrite->modifyQueryArr($query_new);

		$result = $__rewrite->getUrl();
	}
	else {

		$var = isset($params['var']) ? $params['var'] : null;
		$p1 = isset($params['p1']) ? $params['p1'] : null;
		$p2 = isset($params['p2']) ? $params['p2'] : null;
		$p3 = isset($params['p3']) ? $params['p3'] : null;

		if(in_array($func, array('photo', 'photoById', 'photoByIdName')) && !is_null($var)) {
			$photo_object = $var;
			if(is_object($photo_object) && $photo_object->getId() && is_null($p2)) {
				$p2 = $photo_object;
			}
		}
		elseif(in_array($func, array('user', 'userById', 'userByIdName', 'userByUrlname')) && !is_null($var)) {
			$user_object = $var;
			if(is_object($user_object) && $user_object->getId() && is_null($p2)) {
				$p2 = $user_object;
			}
		}

		$result = UrlModel::$func($p1, $p2, $p3);
	}

	if($relative) {
		$url_separator = '/';
		static $url_hp = null;
		if(is_null($url_hp)) {
			$url_hp = _rtrim(UrlModel::homepage(), $url_separator);
		}

		if(_stripos($result, $url_hp)===0) {
			if(strcmp($result, $url_hp)===0) {
				$result = $url_separator;
			}
			else {
				$result = _substr_replace($result, '', 0, _strlen($url_hp));
			}
		}
	}

	if(isset($params['jsify'])) {
		$result = Text::escapeJS($result);
	}

	return $result;
}
