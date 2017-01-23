<?
/*
1) <mm> мин  - если не более часа назад
2) Сегодня, <hh:mm> - если сегодня, но более часа назад
3) Вчера, <hh:mm> - если вчера
4) 15 июня, <hh:mm> - если ранее чем вчера, но в этом году
5) 15 июня'08, <hh:mm> - если в другом году
*/

function smarty_modifier_datetime($tstamp, $d_format=null) {

	$result = null;

	$time = time();

	if($d_format) {
		$result = date($d_format, $tstamp);
	}
	elseif($tstamp<=$time) {
		$months = DateConst::month_r();
		$hour = 60*60;
		$day = $hour*24;
		$diff = $time - $tstamp;
		if($diff < $hour) {// 1
			if($diff<15) {
				$result = sprintf('только&nbsp;что');
			}
			elseif($diff<60) {
				$result = sprintf('менее&nbsp;минуты&nbsp;назад');
			}
			else {
				$result = sprintf('%u&nbsp;мин&nbsp;назад', ceil($diff/60));
			}
		}
		elseif( date('Ymd', $tstamp) == date('Ymd') ) {// 2
			$result = sprintf('Сегодня'.',&nbsp;%s', date("G:i", $tstamp));
		}
		elseif( date('Ymd', $tstamp) == date('Ymd', time() - $day) ) {// 3
			$result = sprintf('Вчера'.',&nbsp;%s', date('G:i', $tstamp));
		}
		else {// 4
			$result = sprintf('%u&nbsp;%s&nbsp;%s,&nbsp;%s', date('j', $tstamp), $months[date('n', $tstamp)], date('Y', $tstamp), date('G:i', $tstamp));
		}
	}
	elseif($tstamp>$time) {
		$result = date('d/m/Y H:i', $tstamp);
	}

	return $result;
}
