<?

class HtaccessControl extends ControlModel {

	public function pass($filepath) {
		$filepath = ROOT_PATH . $filepath;
		$this->output($filepath);
	}

	private function output($filepath, $print=true) {

		clearstatcache();

		if(is_file($filepath)) {
			$filecontents = @file_get_contents($filepath);
			if($filecontents) {
				$content_type = Mimetype::GetFileMimetype($filepath);
				if($content_type) {
					header(sprintf('Content-type: %s', $content_type));
				}
			}
			if($print) {
				print $filecontents;
			}
			else {
				return $filecontents;
			}
		}
	}

}