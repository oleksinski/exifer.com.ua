<?

require_once(dirname(__FILE__).'/../../config.php');
require_once(ROOT_PATH . 'init.inc.php');

/*

4 - Молдова ()
5 - Казахстан ()
6 - Эстония ()
7 - Израиль ()
8 - Латвия ()
9 - Литва ()
10 - Польша (Варшава)
11 - Узбекистан ()
12 - Чехия (Прага)
13 - Болгария (София)
14 - Армения (Ереван)
15 - Азербайджан (Баку)
16 - Грузия (Тбилиси)
17 - Турция (Анкара)

INSERT IGNORE INTO exifer.location_country(id, name_en, name_ru, name_ua, active) VALUES(10,"Poland","Польша","Польща",0);
INSERT IGNORE INTO exifer.location_country(id, name_en, name_ru, name_ua, active) VALUES(11,"Uzbekistan","Узбекистан","Узбекістан",0);
INSERT IGNORE INTO exifer.location_country(id, name_en, name_ru, name_ua, active) VALUES(12,"Czech Republic","Чехия","Чехія",0);
INSERT IGNORE INTO exifer.location_country(id, name_en, name_ru, name_ua, active) VALUES(13,"Bulgaria","Болгария","Болгарія",0);
INSERT IGNORE INTO exifer.location_country(id, name_en, name_ru, name_ua, active) VALUES(14,"Armenia","Армения","Вірменія",0);
INSERT IGNORE INTO exifer.location_country(id, name_en, name_ru, name_ua, active) VALUES(15,"Azerbaijan","Азербайджан","Азербайджан",0);
INSERT IGNORE INTO exifer.location_country(id, name_en, name_ru, name_ua, active) VALUES(16,"Georgia","Грузия","Грузія",0);
INSERT IGNORE INTO exifer.location_country(id, name_en, name_ru, name_ua, active) VALUES(17,"Turkey","Турция","Турція",0);

UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=4 AND name_ru="Кишинев"; # moldova
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=5 AND name_ru="Астана"; # kazakhstan
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=6 AND name_ru="Таллин"; # Estonia
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=7 AND name_ru="Иерусалим"; # Israel
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=8 AND name_ru="Рига"; # Latvia
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=9 AND name_ru="Вильнюс"; # Lithuania(Litva)
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=10 AND name_ru="Варшава"; # Poland
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=11 AND name_ru="Ташкент"; # Uzbekistan
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=12 AND name_ru="Прага"; # Czech
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=13 AND name_ru="София"; # Bulgaria
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=14 AND name_ru="Ереван"; # Armenia
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=15 AND name_ru="Баку"; # Azerbaijan
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=16 AND name_ru="Тбилиси"; # Georgia
UPDATE exifer.location_city SET is_main=1, is_capital=1 WHERE country_id=17 AND name_ru="Анкара"; # Turkey

UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=4 AND is_capital=1 LIMIT 1) WHERE id=4;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=5 AND is_capital=1 LIMIT 1) WHERE id=5;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=6 AND is_capital=1 LIMIT 1) WHERE id=6;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=7 AND is_capital=1 LIMIT 1) WHERE id=7;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=8 AND is_capital=1 LIMIT 1) WHERE id=8;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=9 AND is_capital=1 LIMIT 1) WHERE id=9;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=10 AND is_capital=1 LIMIT 1) WHERE id=10;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=11 AND is_capital=1 LIMIT 1) WHERE id=11;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=12 AND is_capital=1 LIMIT 1) WHERE id=12;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=13 AND is_capital=1 LIMIT 1) WHERE id=13;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=14 AND is_capital=1 LIMIT 1) WHERE id=14;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=15 AND is_capital=1 LIMIT 1) WHERE id=15;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=16 AND is_capital=1 LIMIT 1) WHERE id=16;
UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=17 AND is_capital=1 LIMIT 1) WHERE id=17;

UPDATE exifer.location_country SET active=1 AND capital_id IS NOT NULL;

*/

$fileList = array(
	4 => 'moldova.txt',
	5 => 'kazakhstan.txt',
	6 => 'estonia.txt',
	7 => 'israel.txt',
	8 => 'latvia.txt',
	9 => 'litva.txt',
	10 => 'poland.txt',
	11 => 'uzbekistan.txt',
	12 => 'czech.txt',
	13 => 'bulgaria.txt',
	14 => 'armenia.txt',
	15 => 'azerbaijan.txt',
	16 => 'georgia.txt',
	17 => 'turkey.txt',
);

$allContents = array();
$allFilePath = dirname(__FILE__).'/target/all.sql';

foreach($fileList as $countryId=>$fileName) {
	$sourceFilePath = dirname(__FILE__).'/source/'.$fileName;
	$targetFilePath = dirname(__FILE__).'/target/'.$fileName.'.sql';
	if(file_exists($sourceFilePath)) {
		$fileContents = file_get_contents($sourceFilePath);
		$cilyList = explode("\n", $fileContents);
		$cilyList = array_unique($cilyList);
		$cilyList = array_diff($cilyList, array(''));
		sort($cilyList);

		$sqlInsertList = array();

		foreach($cilyList as $cityName) {
			$sqlInsertList[] =  sprintf('(%d, %s, 1)', $countryId, MySQL::str($cityName));
		}

		$sql = null;
		if($sqlInsertList) {
			$tmpList = array();
			$tmpList[] = 'INSERT IGNORE INTO exifer.location_city(country_id, name_ru, active) VALUES';
			$tmpList[] = implode(",\n", $sqlInsertList).';';
			$sql = implode("\n", $tmpList);
		}
		FileFunc::saveFile($targetFilePath, $sql);
		_e($fileName);
		_e($sql);
		_e('----');

		$allContents[] = sprintf('# %d - %s', $countryId, $fileName);
		$allContents[] = $sql;
		$allContents[] = "";
	}
	else {
		//_e('File '.$sourceFilePath.' not exists');
	}
}

FileFunc::saveFile($allFilePath, implode("\n", $allContents));

foreach($fileList as $countryId=>$fileName) {
	$sql = sprintf('UPDATE exifer.location_country SET capital_id=(SELECT id FROM exifer.location_city WHERE country_id=%d AND is_capital=1 LIMIT 1) WHERE id=%d;', $countryId, $countryId);
	//_e($sql);
}
