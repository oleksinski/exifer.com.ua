<?

/**
 * HTML FileUpload Model
 */

class FileUploadModel {

	const ERROR_OK = 0;
	const ERROR_INI_SIZE = 1;
	const ERROR_FORM_SIZE = 2;
	const ERROR_PARTIAL = 3;
	const ERROR_NO_FILE = 4;
	//const ERROR_ = 5;
	const ERROR_NO_TMP_DIR = 6;
	const ERROR_CANT_WRITE = 7;
	const ERROR_EXTENSION = 8;

	const FORM_MAX_FILE_SIZE = 10485760; // 10 * 1024 * 1024

	protected $form_maxfilesize = 0;
	protected $upload_maxfilesize = 0;
	protected $post_maxfilesize = 0;

	protected $filelist = array();

	protected $filelist_index = array();

	protected $upload_index = array();

	protected $__destruct = true;

	/**
	 * @constructor
	 * @param array [optional] $files
	 *
	 * $files struct:
	 * Array(
	 *    html_filename => Array(
	 *       name =>
	 *       tmp_name =>
	 *       type =>
	 *       error =>
	 *       size =>
	 *    )
	 * )
	 */
	public function __construct($files=array()) {

		$this->filelist = array();

		$filelist = $files ? $files : $_FILES;

		foreach($filelist as $html_filename=>$filedata) {

			foreach($filedata as $phpname=>$phpvalue) {

				if(is_array($phpvalue)) {

					foreach($phpvalue as $index=>$item) {
						$this->filelist[$html_filename][$index][$phpname] = $item;
					}
				}
				else {
					$this->filelist[$html_filename][0][$phpname] = $phpvalue;
				}
			}
		}

		// remove empty filelist items
		foreach($this->filelist as $html_filename=>&$filedata) {
			foreach($filedata as $index=>$data) {
				if(!isset($data['name']) || !is_scalar($data['name']) || !$data['name']) {
					unset($filedata[$index]);
				}
			}
			if(!$this->filelist[$html_filename]) {
				unset($this->filelist[$html_filename]);
			}
		}

		$this->filelist_index = $this->filelist;

		$this->set_desired_form_maxfilesize(self::FORM_MAX_FILE_SIZE);
		$this->upload_maxfilesize = $this->get_upload_maxfilesize();
		$this->post_maxfilesize = $this->get_post_maxfilesize();

		//_e(array('$_FILES'=>$_FILES));
		//_e(array('$this->filelist'=>$this->filelist));
		//_e(array('$this'=>$this));
	}

	public function __destruct() {
		if($this->__destruct) {
			foreach($this->filelist as $html_filename=>$filedata) {
				$this->flush_upload_index($html_filename);
				while(!is_null($upload_index=$this->get_next_upload_index($html_filename))) {
					$this->rm($html_filename, $upload_index);
				}
			}
		}
	}

	/**
	 * @param int bytes
	 */
	public function set_desired_form_maxfilesize($form_maxfilesize) {
		$form_maxfilesize = Cast::unsignint($form_maxfilesize);
		$upload_maxfilesize = self::get_upload_maxfilesize();
		if($form_maxfilesize > $upload_maxfilesize) {
			$form_maxfilesize = $upload_maxfilesize;
		}
		$this->form_maxfilesize = $form_maxfilesize;
		return $this->form_maxfilesize;
	}

	public function enable_destruct() {
		$this->__destruct = true;
	}

	public function disable_destruct() {
		$this->__destruct = false;
	}

	public function get_upload_maxfilesize() {
		$upload_maxfilesize_bytes = self::get_ini_maxfilesize_bytes(ini_get('upload_max_filesize')); // 2M, 96M
		return $upload_maxfilesize_bytes;
	}

	public function get_post_maxfilesize() {
		$post_maxfilesize_bytes = self::get_ini_maxfilesize_bytes(ini_get('post_max_size')); // 8M
		return $post_maxfilesize_bytes;
	}

	public function get_form_maxfilesize() {
		return $this->form_maxfilesize;
	}

	public function get_ini_maxfilesize_bytes($value) {
		if(_strstr($value, 'M')) {
			$bytes = Cast::megabyte2byte($value);
		}
		elseif(_strstr($value, 'K')) {
			$bytes = Cast::kilobyte2byte($value);
		}
		else {
			$bytes = Cast::unsignint($value);
		}
		return $bytes;
	}

	// ---

	public function get_html_filelist() {
		return array_keys($this->filelist);
	}

	// ---

	public function rm($html_filename, $upload_index=0) {
		$fpath = $this->get_fpath($html_filename, $upload_index);
		$result = file_exists($fpath) ? unlink($fpath) : false;
		_e(array(sprintf('rm tmp uploaded file %s', $fpath)=>$result));
		return $result;
	}

	public function cp($html_filename, $upload_index=0, $fpath_new=null, $chmod=FileFunc::CHMOD_OWNER_NOEXEC_READ_WRITE_OTHER_NOEXEC_READ_NOWRITE) {
		$fpath = $this->get_fpath($html_filename, $upload_index);
		$mkdir_ok = FileFunc::mkdir($fpath_new);
		if($mkdir_ok) {
			$result = copy($fpath, $fpath_new);
		}
		_e(array(sprintf('cp tmp uploaded file %s => %s', $fpath, $fpath_new)=>$result));
		return $result;
	}

	public function mv($html_filename, $upload_index=0, $fpath_new=null, $chmod=FileFunc::CHMOD_OWNER_NOEXEC_READ_WRITE_OTHER_NOEXEC_READ_NOWRITE) {
		$fpath = $this->get_fpath($html_filename, $upload_index);
		$mkdir_ok = FileFunc::mkdir($fpath_new);
		if($mkdir_ok) {
			$result = rename($fpath, $fpath_new);
		}
		_e(array(sprintf('mv tmp uploaded file %s => %s', $fpath, $fpath_new)=>$result));
		return $result;
	}

	// ---

	public function get_finfo($html_filename, $upload_index=0, $field=null) {
		$return = null;
		$filelist = ifsetor($this->filelist[$html_filename], null);
		$return = ifsetor($filelist[$upload_index], null);
		if($field) {
			$return = ifsetor($return[$field], null);
		}
		return $return;
	}

	public function get_fname($html_filename, $upload_index=0) {
		return $this->get_finfo($html_filename, $upload_index, 'name');
	}

	public function get_fpath($html_filename, $upload_index=0) {
		return $this->get_finfo($html_filename, $upload_index, 'tmp_name');
	}

	public function get_ftype($html_filename, $upload_index=0) {
		return $this->get_finfo($html_filename, $upload_index, 'type');
	}

	public function get_ferror($html_filename, $upload_index=0) {
		return $this->get_finfo($html_filename, $upload_index, 'error');
	}

	public function get_fsize($html_filename, $upload_index=0) {
		return $this->get_finfo($html_filename, $upload_index, 'size');
	}

	// ---

	public function is_error($html_filename, $upload_index=0) {
		$ferror = $this->get_ferror($html_filename, $upload_index);
		return $ferror!==self::ERROR_OK;
	}

	public function get_ferror_param($html_filename, $upload_index=0) {

		$model_error_id = null;
		$model_error_params = array();

		$ferror = $this->get_ferror($html_filename, $upload_index);
		$fname = $this->get_fname($html_filename, $upload_index);

		$upload_maxfilesize_megabytes = sprintf('%.2f Mb', Cast::byte2megabyte($this->upload_maxfilesize));
		$form_maxfilesize_megabytes = sprintf('%.2f Mb', Cast::byte2megabyte($this->form_maxfilesize));
		$post_maxfilesize_megabytes = sprintf('%.2f Mb', Cast::byte2megabyte($this->post_maxfilesize));

		switch($ferror) {

			case self::ERROR_OK:
				// calm down. all is ok
				break;

			case self::ERROR_INI_SIZE:
				$model_error_id = 'UPLOAD_INI_SIZE';
				$model_error_params = array($fname, $upload_maxfilesize_megabytes);
				break;

			case self::ERROR_FORM_SIZE:
				$model_error_id = 'UPLOAD_FORM_SIZE';
				$model_error_params = array($fname, $form_maxfilesize_megabytes);
				break;

			case self::ERROR_PARTIAL:
				$model_error_id = 'UPLOAD_PARTIAL';
				$model_error_params = array($fname);
				break;

			case self::ERROR_NO_FILE:
				$model_error_id = 'UPLOAD_NO_FILE';
				$model_error_params = array($fname);
				break;

			case self::ERROR_NO_TMP_DIR:
				$model_error_id = 'UPLOAD_NO_TMP_DIR';
				$model_error_params = array($fname);
				break;

			case self::ERROR_CANT_WRITE:
				$model_error_id = 'UPLOAD_CANT_WRITE';
				$model_error_params = array($fname);
				break;

			case self::ERROR_EXTENSION:
				$model_error_id = 'UPLOAD_EXTENSION';
				$model_error_params = array($fname);
				break;

			default:
				$model_error_id = 'UPLOAD_UNKNOWN';
				$model_error_params = array($fname);
				break;
		}

		foreach($model_error_params as &$p) {
			$p = _htmlspecialchars($p);
		}

		$return = array(
			'id' => $model_error_id,
			'params' => $model_error_params,
		);

		return $return;
	}

	public function get_ferror_desc($html_filename, $upload_index=0) {

		$ferror_desc = null;

		$ferror_param = $this->get_ferror_param($html_filename, $upload_index);

		list($m_error_id, $m_error_params) = array($ferror_param['id'], $ferror_param['params']);

		if($m_error_id) {
			$__error = ErrorModel::getInstance();
			$ferror_desc = $__error->get($m_error_id, $m_error_params);
		}

		return $ferror_desc;
	}

	// ---
	/**
	 * @return mixed or false if failed
	 */
	public function get_next_upload_index($html_filename) {
		$index = null;
		if(isset($this->filelist_index[$html_filename])) {
			$each = each($this->filelist_index[$html_filename]);
			$index = ifsetor($each['key'], null);
		}
		return $index;
	}

	public function flush_upload_index($html_filename) {
		if(isset($this->filelist_index[$html_filename])) {
			reset($this->filelist_index[$html_filename]);
		}
	}

}
