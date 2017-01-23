<?

class ShellCmdModel /* extends ShellCmd */ {

	public function GetCmdPath($what, $escape=true) {

		$cmd = null;

		switch($what) {

			case 'PHP':

				$cmd = 'php';
				if(Predicate::server_dev() && Predicate::windowsOS()) {
					$cmd = 'C:/Denwer3/usr/bin/php5.exe';
				}
				break;

			case 'IM_CONVERT':

				$cmd = '/usr/bin/convert';
				if(Predicate::server_dev() && Predicate::windowsOS()) {
					$cmd = 'C:/Program Files/ImageMagick-6.5.1-Q16/convert.exe';
				}
				break;

			case 'IM_IDENTIFY':

				$cmd = '/usr/bin/identify';
				if(Predicate::server_dev() && Predicate::windowsOS()) {
					$cmd = 'C:/Program Files/ImageMagick-6.5.1-Q16/identify.exe';
				}
				break;

			default:

				$cmd = null;
				break;
		}

		if($cmd && $escape) {
			$cmd = ShellCmd::EscapePath($cmd);
		}

		return $cmd;
	}
}
