<?

class FileFunc {

	const CHMOD_OWNER_NOEXEC_READ_WRITE_OTHER_NOEXEC_NOREAD_NOWRITE = 0600;
	const CHMOD_OWNER_NOEXEC_READ_WRITE_OTHER_NOEXEC_READ_NOWRITE = 0644;
	const CHMOD_OWNER_EXEC_READ_WRITE_OTHER_EXEC_READ_NOWRITE = 0755;
	const CHMOD_OWNER_EXEC_READ_WRITE_OWNERGROUP_EXEC_READ_NOWRITE= 0750;

	const FOPEN_MODE_READ_NOWRITE_NOCREATE_NOCAT = 'r';
	const FOPEN_MODE_READ_WRITE_NOCREATE_NOCAT = 'r+';
	const FOPEN_MODE_NOREAD_WRITE_CREATE_NOCAT = 'w';
	const FOPEN_MODE_READ_WRITE_CREATE_NOCAT = 'w+';
	const FOPEN_MODE_NOREAD_WRITE_CREATE_CAT = 'a';
	const FOPEN_MODE_READ_WRITE_CREATE_CAT = 'a+';
	const FOPEN_MODE_NOREAD_WRITE_ERRORCREATE_NOCAT= 'x';
	const FOPEN_MODE_READ_WRITE_ERRORCREATE_NOCAT= 'x+';


	/**
	 * Save given file contents to file
	 * @param string $filepath
	 * @param string $fileContents
	 * @param chmod access mode
	 * @param fopen access mode
	 * @return boolean
	 */
	public static function saveFile(

		$filepath,
		$fileContents,
		$chmod_mode=self::CHMOD_OWNER_NOEXEC_READ_WRITE_OTHER_NOEXEC_READ_NOWRITE,
		$fopen_mode=self::FOPEN_MODE_NOREAD_WRITE_CREATE_NOCAT

		) {

		$success = false;

		$timer = new StopWatch();

		$dirname = dirname($filepath);

		if(!$dirname || !self::mkdir($dirname)) {
			_e(
				sprintf('Cannot create dir: %s, %s::%s, line %s',
					$dirname,
					__CLASS__,
					__FUNCTION__,
					__LINE__
				),
				E_USER_ERROR
			);
		}
		else {
			if(!($fp = @fopen($filepath, $fopen_mode))) {
				_e(
					sprintf('Cannot open file: %s, %s::%s, line %s',
						$filepath,
						__CLASS__,
						__FUNCTION__,
						__LINE__
					),
					E_USER_ERROR
				);
			}
			else {

				$startTime = microtime();

				do{
					$canWrite = flock($fp, LOCK_EX); // Lock file

					// If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
					if(!$canWrite) usleep(round(rand(0, 100)*1000));
				}
				while((!$canWrite) && ((microtime()-$startTime) < 1000));

				//file was locked so now we can store information
				if($canWrite) {

					fwrite($fp, $fileContents);

					flock($fp, LOCK_UN); // unlock file

					$success = true;

					if($chmod_mode) $chmod_result = chmod($filepath, $chmod_mode);

					_e(
						sprintf('File %s saved; %s bytes; chmod 0%o; %s',
							$filepath,
							filesize($filepath),
							$chmod_mode,
							$timer->getFormat(5)
						),
						E_USER_NOTICE
					);

				}
				else {
					_e(
						sprintf('File is locked: %s, %s::%s, line %s',
							$filepath,
							__CLASS__,
							__FUNCTION__,
							__LINE__
						),
						E_USER_ERROR
					);
				}
				fclose($fp);
			}
		}

		return $success;
	}

	/**
	 * Serialize var and write to file
	 * @param string $filepath
	 * @param mixed $var
	 * @param chmod access mode
	 * @param fopen access mode
	 * @return boolean
	 */
	public static function saveVarsToFile(

		$filepath,
		$vars,
		$chmod_mode=self::CHMOD_OWNER_NOEXEC_READ_WRITE_OTHER_NOEXEC_READ_NOWRITE,
		$fopen_mode=self::FOPEN_MODE_NOREAD_WRITE_CREATE_NOCAT

		) {

		if (!$vars) {
			_e('No vars to save', E_USER_WARNING);
			return false;
		}

		$fileContentsArr = array();

		$fileContentsArr[] = sprintf("<?\n//This is auto-generated file by %s::%s()\n//DO NOT EDIT!\n", __CLASS__, __FUNCTION__);

		foreach($vars as $key=>$val) {
			$fileContentsArr[] = '$'.$key.'='.self::exportSerialize($val).';';
		}

		$fileContents = implode("\n", $fileContentsArr);

		return self::saveFile($filepath, $fileContents, $chmod_mode, $fopen_mode);
	}

	/**
	 * VarExport Recursive Func [internal class usage]
	 * @param mixed $var
	 * @return string
	 */
	public static function exportSerialize(&$var) {
		if(is_array($var)) {
			$ret_str = 'array(';
			$i = 0;
			$number = 0;
			$arr_str = '';
			foreach($var as $id=>$val) {
				$arr_str .= '';
				if(!is_integer($id)) {
					$arr_str .= "\n" . var_export($id, true) . '=>';
				}
				else {
					if(($i++ != $id) || $number) {
						$arr_str .= "\n$id=>";
						$number = 1;
					}
				}
				$arr_str .= self::exportSerialize($val) . ',';
			}
			if($arr_str) {
				$arr_str = _substr_replace($arr_str, ')', -1, 1);
			}
			else {
				$arr_str = ')';
			}
			$ret_str .= $arr_str;
		}
		else {
			$ret_str = var_export($var, true);
		}
		return $ret_str;
	}

	/**
	 * Recursive mkdir
	 * @param string $path
	 * @param int mode
	 & @return boolean
	 */
	public static function mkdir($path, $chmod_mode=self::CHMOD_OWNER_EXEC_READ_WRITE_OTHER_EXEC_READ_NOWRITE) {

		$path = Cast::str($path);

		$path = Url::fix($path);

		if(0) {
			$dir = $path;
			if(!is_dir($path)) {
				$dir = dirname($path);
			}
			if(!file_exists($dir)) {
				$result = mkdir($path, $chmod_mode, true); #recursive mkdir
				if($result) {
					_e(sprintf('Create dir %s', $dir));
				}
			}
			else {
				chmod($dir, $chmod_mode);
			}
		}

		$input_path = $path;

		$DIRECTORY_SEPARATOR = '/';

		$dirs = explode($DIRECTORY_SEPARATOR , $path);

		$path = $path_created = _strpos($input_path, $DIRECTORY_SEPARATOR)===0 ? '/' : '';

		for($i=0; $i<count($dirs); ++$i) {
			$dirs[$i] = strval($dirs[$i]);
			if($dirs[$i]==='') { // warning: dir name can be "0"
				continue;
			}
			if($i==0 && _strpos($dirs[$i], $DIRECTORY_SEPARATOR, 0)===0) {
				$path = $path_created = $DIRECTORY_SEPARATOR;
			}
			$path .= $dirs[$i];
			if(($i+1)<count($dirs)) {
				$path.= $DIRECTORY_SEPARATOR;
			}
			if(file_exists($path) && is_dir($path)) {
				continue;
			}
			elseif(!mkdir($path, $chmod_mode)) {
				return false;
			}
			else {
				$path_created .= $dirs[$i];
				if(($i+1)<count($dirs)) {
					$path_created.= $DIRECTORY_SEPARATOR;
				}
			}
		}
		if($path_created!='' && $path_created!=$DIRECTORY_SEPARATOR) {
			_e(sprintf('Created path [%s] from [%s]', $path_created, $input_path));
		}
		return true;
	}

	public static function readDirContents($dir) {

		$result = array();

		$d = @dir($dir);

		if(is_object($d)) {
			while(false !== ($entry = $d->read())) {
				if($entry!='.' && $entry!='..') {
					$entry = $dir.'/'.$entry;
					if(is_dir($entry)) {
						array_push($result, $entry);
						$result = array_merge($result, self::readDirContents($entry));
					}
					else {
						array_push($result, $entry);
					}
				}
			}
			$d->close();
		}
		else {
			_e(sprintf('Failed to read dir %s', $dir), E_USER_WARNING);
		}

		foreach($result as &$path) {
			$path = Url::fix($path);
		}

		return $result;
	}

	public static function readDirFiles($dir) {
		$dirContents = self::readDirContents($dir);
		$fileList = array_values(array_filter($dirContents, Util::CreateFunction('$a', 'return !is_dir($a);')));
		return $fileList;
	}

	public static function readDirFolders($dir) {
		$dirContents = self::readDirContents($dir);
		$dirList = array_values(array_filter($dirContents, Util::CreateFunction('$a', 'return is_dir($a);')));
		return $dirList;
	}

}
