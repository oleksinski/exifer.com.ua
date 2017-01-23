<?

class ShellCmd {

	// PHP: shell_exec()

	public static function Execute($cmd, $traceMode=true, &$exec_output=null) {

		$exec_result = false;

		$cmd_modif = $cmd;

		if($traceMode && Predicate::isWebCall()) {

			$stdError2Out = ' 2>&1';

			if(_strstr($cmd, $stdError2Out)===false) {
				$cmd_modif = $cmd . $stdError2Out;
			}
		}

		//$exec_output = null;
		$cmd_exit_code = null;

		if(0) { // exec
			exec($cmd_modif, $exec_output, $cmd_exit_code);
			$exec_result = $cmd_exit_code==0;
			$exec_output = @implode("\n", $exec_output_array);
		}
		else { // passthru
			ob_start();
			passthru($cmd_modif, $cmd_exit_code);
			$exec_output = ob_get_clean();
			$exec_result = $cmd_exit_code==0;
		}
		flush();

		if($traceMode) {
			if($exec_result) {
				_e($cmd);
			}
			else {
				_e('CMD Exec Failed: '.$cmd, E_USER_ERROR);
				_e($exec_output, E_USER_ERROR);
				_e('CMD Exit Code: '.$cmd_exit_code, E_USER_ERROR);
			}
		}

		return $exec_result;
	}

	public static function EscapePath($filepath) {

		if(Predicate::windowsOS()) {
			$filepath = _trim($filepath, ' \'"');
			$dirs = explode('/', $filepath);
			foreach($dirs as &$dir) {
				if(_strstr($dir, ' ')!==false) {
					$dir = _trim($dir, '\'"');
					$dir = '"'.$dir.'"';
				}
			}
			$filepath = implode('/', $dirs);
		}
		else {
			$filepath = self::UnEscapePath($filepath);
			$filepath = '\''._addslashes($filepath).'\'';
		}

		return $filepath;
	}

	public static function UnEscapePath($filepath) {

		$filepath = _trim($filepath, ' \'');
		$filepath = _stripslashes($filepath);
		return $filepath;
	}

}