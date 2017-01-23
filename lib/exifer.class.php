<?

class ExiferAtom {

	// filepath to image file
	protected $filepath=null;

	// raw exif array data
	protected $exif_raw;

	public function __construct($filepath=null) {

		$this->SetFilepath($filepath);

		$exif_raw = array();

		if($filepath && file_exists($filepath)) {

			if(function_exists('exif_read_data')) {

				$exif_raw = @exif_read_data($filepath);

				if(!is_array($exif_raw)) {
					$exif_raw = array();
				}
			}
		}

		$this->SetExifRawData($exif_raw);
	}

	// ---

	public function SetFilepath($filepath) {
		$this->filepath = $filepath;
	}

	public function GetFilepath() {
		return $this->filepath;
	}

	// ---

	public function SetExifRawData(array $exif_raw) {

		// remove unnecessary exif fields
		//$fields2remove = array('COMPUTED', 'THUMBNAIL', 'MakerNote');
		$fields2remove = array('MakerNote', 'UserComment', 'CFAPattern', 'FileSource');
		foreach($fields2remove as $field) {
			if(isset($exif_raw[$field])) {
				unset($exif_raw[$field]);
			}
		}

		$this->exif_raw = $exif_raw;
	}

	public function GetExifRawData() {
		return $this->exif_raw;
	}

	// ---

	public function SetExifRawProperty($property, $value) {
		if(!empty($this->exif_raw)) {
			$this->exif_raw[$property] = $value;
			return true;
		}
		return false;
	}

	public function GetExifRawProperty($property) {
		return ifsetor($this->exif_raw[$property], null);
	}

}


/**
 *
 * http://www.nongnu.org/gcmd/tags.html
 * http://www.awaresystems.be/imaging/tiff/tifftags/baseline.html
 *
 * --= Calculated params =-- //
 * ShutterSpeedValue = -log2(ExposureTime)
 * ApertureValue = 2*log2(FNumber)
 * BrightnessValue = log2(B/NK), B:cd/cm2, N,K: constant
 */
class Exifer extends ExiferAtom {

	const EXIF_TYPE_PHP = 0;
	const EXIF_NAME_RU = 1;
	const EXIF_NAME_EN = 2;
	const EXIF_TYPE_SQL = 3;

	// container for stripped exif data
	private $exif_format=array();
	private $exif_human=array();

	//function __construct($filepath=null) {
	//	parent::__construct($filepath);
	//}

	public function SetExifRawData(array $exif_raw) {
		parent::SetExifRawData($exif_raw);
		$this->SetExifFormatData();
		$this->SetExifHumanData();
	}

	public function SetExifRawProperty($property, $value) {
		if(parent::SetExifRawProperty($property, $value)) {
			$this->SetExifFormatData(); // recalc format data
			$this->SetExifHumanData(); // recalc human data
		}
	}

	// ---

	public function GetExifFormatProperty($property) {
		return ifsetor($this->exif_format[$property], null);
	}

	private function SetExifFormatProperty($property, $value) {
		if($property && !is_null($value)) {
			$this->exif_format[$property] = $value;
		}
	}

	// ---

	public function GetExifHumanProperty($property) {
		return ifsetor($this->exif_human[$property], null);
	}

	private function SetExifHumanProperty($property, $value) {
		if($property && !is_null($value)) {
			$this->exif_human[$property] = $value;
		}
	}

	// ---

	public function GetExifFormatData() {
		return $this->exif_format;
	}

	public function GetExifHumanData() {
		return $this->exif_human;
	}

	// ---

	private function SetExifFormatData() {

		$e_list = self::GetExifPropertyList();

		$exif_raw = $this->GetExifRawData();

		foreach($e_list as $e_tag=>$e_data) {

			$e_value = ifsetor($exif_raw[$e_tag], null);

			if(is_null($e_value)) {

				if(insetcheck($e_tag, array('IsColor','ByteOrderMotorola','CCDWidth')) && isset($exif_raw['COMPUTED'])) {
					if(isset($exif_raw['COMPUTED'][$e_tag])) {
						$e_value = $exif_raw['COMPUTED'][$e_tag];
					}
				}
				elseif(isset($exif_raw['GPS']) && _strpos($e_tag, 'GPS')===0) {
					$e_tag_short = _substr($e_tag, 3-1);
					foreach(array($e_tag, $e_tag_short) as $tag) {
						if(isset($exif_raw['GPS'][$tag])) {
							$e_value = $exif_raw['GPS'][$tag];
							break;
						}
					}
				}
			}

			if($e_value && is_scalar($e_value)) {

				$e_value = _trim($e_value);

				$e_type = $e_list[$e_tag][self::EXIF_TYPE_PHP];

				if(insetcheck($e_type, array('ASCII','RATIONAL','CHAR'))) {
					$e_value = Cast::str($e_value);
				}
				elseif(insetcheck($e_type, array('SHORT','WORD','LONG'))) {
					$e_value = Cast::unsignint($e_value);
				}
				elseif($e_type=='ASCII_TIME') {
					$e_value = strtotime(Cast::str($e_value));
				}

				if($e_tag=='UserComment') {

					$e_value = _str_replace('ASCII', '', $e_value);
					if(empty($e_value)) {
						$e_value = null;
					}
				}

				SafeHtmlModel::input(&$e_value);

				$this->SetExifFormatProperty($e_tag, $e_value);
			}
		}
	}

	private function SetExifHumanData() {
		if(empty($this->exif_format)) {
			$this->SetExifFormatData();
		}
		$exif_format = $this->GetExifFormatData();
		foreach($exif_format as $e_tag=>$e_value) {
			$e_value = $this->CalcExifHumanValue($e_tag, $e_value);
			$this->SetExifHumanProperty($e_tag, $e_value);
		}
	}

	// ---

	private function CalcExifHumanValue($e_tag, $e_value) {

		$e_value = $e_value ? $e_value : $this->GetExifFormatProperty($e_tag);

		if(is_null($e_value)) {
			return $e_value;
		}

		switch($e_tag) {

			case 'Make':
			case 'Model':
			case 'ImageDescription':
			case 'Artist':
			case 'DeviceSettingDescription':
			case 'Copyright':
			case 'DocumentName':
				// Format certain kinds of strings nicely (Camera make etc.)
				$e_value = _ucwords(_strtolower($e_value));
				break;

			case 'ExposureTime':
				$e_value = Cast::CalcStrFraction($e_value);
				if((int)$e_value > 30) {
					$e_value= sprintf('%s sec [bulb]', $e_value);
				}
				else {
					$e_value= sprintf('%s sec', $e_value);
				}
				break;

			case 'ISOSpeedRatings':
				$e_value = sprintf('ISO %u', Cast::CalcIntFraction($e_value));
				break;

			case 'FocalLength':
				$e_value = sprintf('%s mm', Cast::CalcIntFraction($e_value));
				break;

			case 'FNumber':
			case 'MaxApertureValue':
				$e_value = sprintf('f/%s', Cast::CalcIntFraction($e_value));
				break;

			case 'DigitalZoomRatio':
				$e_value = sprintf('x%s', Cast::CalcIntFraction($e_value));
				break;

			case 'DateTime':
			case 'DateTimeOriginal':
			case 'DateTimeDigitized':
				$e_value = date('d-m-Y H:i:s', strtotime($e_value));
				break;

			case 'FileDateTime':
				$e_value = date('d-m-Y H:i:s', $e_value);
				break;

			case 'MeteringMode':
				$map = array(
					0 => 'Unknown',
					1 => 'Average',
					2 => 'Center weighted average',
					3 => 'Spot',
					4 => 'Multi-spot',
					5 => 'Multi-segment (pattern)',
					6 => 'Partial',
					255 => 'other',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'ExposureMode':
				$map = array(
					0 => 'Auto exposure',
					1 => 'Manual exposure',
					2 => 'Auto bracket',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'WhiteBalance':
				$map = array(
					0 => 'Auto',
					1 => 'Manual',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'ExposureProgram':
				$map = array(
					0 => 'Not defined',
					1 => 'Manual',
					2 => 'Normal (auto)',
					3 => 'Aperature priority',
					4 => 'Shutter priority',
					5 => 'Creative program (biased toward depth of field))',
					6 => 'Action program (biased toward fast shutter speed))',
					7 => 'Portrat mode (for closeup photos with the background out of focus)',
					8 => 'Landscape mode (for landscape photos with the background in focus)',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'Flash':
				$map = array(
					0  => 'Flash did not fire',
					1  => 'Flash fired',
					5  => 'Flash fired, strobe return light not detected',
					7  => 'Flash fired, strobe return light detected',
					9  => 'Flash fired, compulsory flash mode',
					13 => 'Flash fired, compulsory flash mode, return light not detected',
					15 => 'Flash fired, compulsory flash mode, return light detected',
					16 => 'Flash did not fire, compulsory flash mode',
					24 => 'Flash did not fire, auto mode',
					25 => 'Flash fired, auto mode',
					29 => 'Flash fired, auto mode, return light not detected',
					31 => 'Flash fired, auto mode, return light detected',
					32 => 'No flash function',
					65 => 'Flash fired, red-eye reduction mode',
					69 => 'Flash fired, red-eye reduction mode, return light not detected',
					71 => 'Flash fired, red-eye reduction mode, return light detected',
					73 => 'Flash fired, compulsory flash mode, red-eye reduction mode',
					77 => 'Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected',
					79 => 'Flash fired, compulsory flash mode, red-eye reduction mode, return light detected',
					89 => 'Flash fired, auto mode, red-eye reduction mode',
					93 => 'Flash fired, auto mode, return light not detected, red-eye reduction mode',
					95 => 'Flash fired, auto mode, return light detected, red-eye reduction mode',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'FlashEnergy':
				$e_value = sprintf('%s BCPS', Cast::CalcIntFraction($e_value)); // Beam Candle Power Seconds
				break;

			case 'ColorSpace':
				$map = array(
					1 => 'sRGB',
					65535 => 'Uncalibrated',
				);
				$e_value = Cast::CalcIntFraction($e_value);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'PhotometricInterpretation':
				$map = array(
					0 => 'WhiteIsZero',
					1 => 'BlackIsZero',
					2 => 'RGB',
					3 => 'Palette color',
					4 => 'Transparency Mask',
					5 => 'CMYK',
					6 => 'YCbCr',
					8 => 'CIE L*a*b*',
					9 => 'ICC L*a*b*',
					10 => 'ITU L*a*b*',
					32803 => 'CFA (Color Filter Array)',
					34892 => 'LinearRaw',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'SensingMethod':
				$map = array(
					1 => 'Not defined',
					2 => 'One-chip color area sensor',
					3 => 'Two-chip color area sensor',
					4 => 'Three-chip color area sensor',
					5 => 'Color sequential area sensor',
					7 => 'Trilinear sensor',
					8 => 'Color sequential linear sensor',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'FocalLengthIn35mmFilm':
				$e_value = sprintf('%u mm', Cast::CalcIntFraction($e_value));
				break;

			case 'ExifImageWidth':
			case 'ExifImageLength':
				$e_value = sprintf('%u px', Cast::CalcIntFraction($e_value));
				break;

			case 'Orientation':
				$map = array(
					1 => 'Normal (0 deg, TopLeft)',
					2 => 'Mirrored (Flip Horizontal - Flop, TopRight)',
					3 => 'Upsidedown (Rotate 180, BottomRight)',
					4 => 'Upsidedown Mirrored (Flip Vertical, BottomLeft)',
					5 => '90 deg CW Mirrored (Transpose, LeftTop)',
					6 => '90 deg CCW (Rotate 90, RightTop)',
					7 => '90 deg CCW Mirrored (Transverse, RightBottom)',
					8 => '90 deg CW (Rotate 270, LeftBottom)',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'Compression':
				$map = array(
					1 => 'No Compression',
					2 => 'CCITT modified Huffman RLE',
					3 => 'CCITT Group 3 fax encoding',
					4 => 'CCITT Group 4 fax encoding',
					5 => 'LZW',
					6 => 'JPEG(old-style)',
					7 => 'JPEG(new-style)',
					8 => 'Deflate(Adobe style)',
					9 => 'ITU-T Rec. T.82 - ITU-T Rec. T.85',
					10 => 'ITU-T Rec. T.82 - ITU-T Rec. T.43',
					32771 => 'CCITTRLEW',
					32773 => 'PackBits compression, aka Macintosh RLE ',
					32895 => 'IT8CTPAD',
					32908 => 'PIXARFILM',
					32909 => 'PIXARLOG',
					32946 => 'deflate',
					34661 => 'JBIG',
					34712 => 'JP2000',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'ResolutionUnit':
			case 'FocalPlaneResolutionUnit':
				$map = array(
					1 => 'Unknown',
					2 => 'Inch',
					3 => 'Cm',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'XResolution':
			case 'YResolution':
				$e_value = Cast::CalcIntFraction($e_value) . ' dpi';
				break;

			case 'FocalPlaneXResolution':
			case 'FocalPlaneYResolution':
				$e_value = Cast::CalcIntFraction($e_value) . ' dpi';
				break;

			case 'ExposureBiasValue':
				$e_value = sprintf('%s EV', Cast::CalcIntFraction($e_value));
				break;

			case 'LightSource':
				$map = array(
					0  => 'Auto',
					1  => 'Daylight',
					2  => 'Flourescent',
					3  => 'Tungsten (incandescent light))',
					4  => 'Flash',
					9  => 'Fine weather',
					10 => 'Cloudy weather',
					11 => 'Shade',
					12 => 'Daylight fluorescent (D 5700 – 7100K)',
					13 => 'Day white fluorescent (N 4600 – 5400K)',
					14 => 'Cool white fluorescent (W 3900 – 4500K)',
					15 => 'White fluorescent (WW 3200 – 3700K)',
					17 => 'Standard Light A',
					18 => 'Standard Light B',
					19 => 'Standard Light C',
					20 => 'D55',
					21 => 'D65',
					22 => 'D75',
					23 => 'D50',
					24 => 'ISO studio tungsten',
					255 => 'Other light source',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'ExifVersion':
			case 'FlashPixVersion':
			case 'InteroperabilityVersion':
				$e_value = sprintf('v.%.2f', $e_value/100);
				break;

			case 'FileSource':
				$map = array(
					3 => 'Recorded on DSC',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'FileType':
				$map = array(
					1  => 'GIF',  // IMAGETYPE_GIF
					2  => 'JPEG', // IMAGETYPE_JPEG
					3  => 'PNG',  // IMAGETYPE_PNG
					4  => 'SWF',  // IMAGETYPE_SWF
					5  => 'PSD',  // IMAGETYPE_PSD
					6  => 'BMP',  // IMAGETYPE_BMP
					7  => 'TIFF', // IMAGETYPE_TIFF_II
					8  => 'TIFF', // IMAGETYPE_TIFF_MM
					9  => 'JPC',  // IMAGETYPE_JPC
					10 => 'JP2',  // IMAGETYPE_JP2
					11 => 'JPX',  // IMAGETYPE_JPX
					12 => 'JB2',  // IMAGETYPE_JB2
					13 => 'SWC',  // IMAGETYPE_SWC
					14 => 'IFF',  // IMAGETYPE_IFF
					15 => 'WBMP', // IMAGETYPE_WBMP
					16 => 'XBM',  // IMAGETYPE_XBM
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'FileSize':
				$kb = 1024;
				$mb = $kb*1024;
				if($e_value > $mb) {
					$e_value = (float)($e_value/$mb);
					$e_value = sprintf('%.2f Mb', $e_value);
				}
				else {
					$e_value = (float)($e_value/$kb);
					$e_value = sprintf('%.2f Kb', $e_value);
				}
				break;

			case 'SceneType':
				$map = array(
					1 => 'A directly photographed image',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'CustomRendered':
				$map = array(
					0 => 'Normal process',
					1 => 'Custom process',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'SceneCaptureType':
				$map = array(
					0 => 'Standard',
					1 => 'Landscape',
					2 => 'Portrait',
					3 => 'Night scene',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'GainControl':
				$map = array(
					0 => 'None',
					1 => 'Low gain up',
					2 => 'High gain up',
					3 => 'Low gain down',
					4 => 'High gain down',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'Contrast':
			case 'Sharpness':
				$map = array(
					0 => 'Normal',
					1 => 'Soft',
					2 => 'Hard',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'Saturation':
				$map = array(
					0 => 'Normal',
					1 => 'Low',
					2 => 'High',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'SubjectDistanceRange':
				$map = array(
					0 => 'unknown',
					1 => 'Macro',
					2 => 'Close view',
					3 => 'Distant view',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'SubjectDistance':
				$e_value = Cast::unsignint($e_value) . ' meters';
				break;

			case 'InteroperabilityIndex':
				$map = array(
					'R98' => 'ExifR98 Rule',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'ComponentsConfiguration':
				$map = array(
					0 => 'does not exist',
					1 => 'Y',
					2 => 'Cb',
					3 => 'Cr',
					4 => 'R',
					5 => 'G',
					6 => 'B',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'PlanarConfiguration':
				$map = array(
					1 => 'Chunky format',
					2 => 'Planar format',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'FillOrder':
				$map = array(
					1 => 'pixels with lower column values are stored in the higher-order bits of the byte',
					2 => 'pixels with lower column values are stored in the lower-order bits of the byte',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'ExtraSamples':
				$map = array(
					0 => 'Unspecified data',
					1 => 'Associated alpha data (with pre-multiplied color)',
					2 => 'Unassociated alpha data ',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'IsColor':
				$map = array(
					0 => 'no',
					1 => 'yes',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'Predictor':
				$map = array(
					1 => 'No prediction scheme used before coding',
					2 => 'Horizontal differencing',
					3 => 'Floating point horizontal differencing',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'SampleFormat':
				$map = array(
					1 => 'unsigned integer data',
					2 => 'two\'s complement signed integer data',
					3 => 'IEEE floating point data',
					4 => 'undefined data format ',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'Threshholding':
				$map = array(
					1 => 'No dithering or halftoning has been applied to the image data',
					2 => 'An ordered dither or halftone technique has been applied to the image data',
					3 => 'A randomized process such as error diffusion has been applied to the image data',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'GPSVersionID':
				break;

			case 'GPSLatitudeRef':
			case 'GPSDestLatitudeRef':
				$map = array(
					'N' => 'North latitude',
					'S' => 'South latitude',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'GPSLongitudeRef':
			case 'GPSDestLongitudeRef':
				$map = array(
					'E' => 'East longitude',
					'W' => 'West longitude',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'GPSAltitudeRef':
				$map = array(
					'0' => 'Sea level',
					'1' => 'Sea level reference (negative value)',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'GPSStatus':
				$map = array(
					'A' => 'Measurement in progress',
					'V' => 'Measurement Interoperability',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'GPSMeasureMode':
				$map = array(
					'2' => '2-dimensional measurement',
					'3' => '3-dimensional measurement',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'GPSSpeedRef':
			case 'GPSDestDistanceRef':
				$map = array(
					'K' => 'Kilometers per hour',
					'M' => 'Miles per hour',
					'N' => 'Knots',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

			case 'GPSTrackRef':
			case 'GPSImgDirectionRef':
			case 'GPSDestBearingRef':
				$map = array(
					'T' => 'True direction',
					'M' => 'Magnetic direction',
				);
				$e_value = ifsetor($map[$e_value], $e_value);
				break;

		}

		return $e_value;
	}


	public function hasExifInfo() {

		$bool = false;

		$exifPropertyList = array('Make', 'Model', 'ExposureTime', 'FNumber', 'ISOSpeedRatings', 'FocalLength', 'Software', 'MaxApertureValue', 'ExposureMode', 'Flash', 'MeteringMode');

		foreach($exifPropertyList as $property) {
			if($this->GetExifRawProperty($property)) {
				$bool = true;
				break;
			}
		}

		return $bool;
	}

	// ---

	public static function GetExifPropertyList() {

		$ExifData = array(
			'Make' => array(
				'ASCII',
				'Производитель',
				'Manufacturer',
			),
			'Model' => array(
				'ASCII',
				'Модель фотокамеры',
				'Camera model',
			),
			'ImageDescription' => array(
				'ASCII',
				'Зоголовок фото',
				'Image title',
			),
			'ExposureTime' => array(
				'RATIONAL',
				'Время экспозиции',
				'Exposure time',
			),
			'FNumber' => array(
				'RATIONAL',
				'Диафрагма',
				'Aperture (F-number)',
			),
			'ISOSpeedRatings' => array(
				'WORD',
				'Значение ISO',
				'ISO speed rating',
			),
			'FocalLength' => array(
				'RATIONAL',
				'Фокусное расстояние',
				'Focal length',
			),
			'MaxApertureValue' => array(
				'RATIONAL',
				'Макс. диафрагма',
				'Max camera aperture',
			),
			'XResolution' => array(
				'RATIONAL',
				'Разрешение по ширине',
				'Image resolution in width direction',
			),
			'YResolution' => array(
				'RATIONAL',
				'Разрешение по высоте',
				'Image resolution in height direction',
			),
			'ResolutionUnit' => array(
				'SHORT',
				'Единица XY-разрешения',
				'Unit of X and Y resolution',
			),
			'Software' => array(
				'ASCII',
				'Программа-редактор',
				'Software',
			),
			'DateTime' => array(
				'ASCII', //ASCII_TIME
				'Дата/время изменения',
				'Date/time (Image Change)',
			),
			'DateTimeOriginal' => array(
				'ASCII', //ASCII_TIME
				'Дата/время съемки',
				'Date/time (Image Capture)',
			),
			'DateTimeDigitized' => array(
				'ASCII', //ASCII_TIME
				'Дата/время оцифровки',
				'Date/time (Image Digitize)',
			),
			'SceneType' => array(
				'SHORT',
				'Тип съемки',
				'Scene type',
			),
			'Orientation' => array(
				'SHORT',
				'Ориентация',
				'Image orientation',
			),
			'MeteringMode' => array(
				'SHORT',
				'Режим замера',
				'Metering mode',
			),
			'ExposureMode' => array(
				'SHORT',
				'Режим экспозиции',
				'Exposure mode',
			),
			'ExposureProgram' => array(
				'SHORT',
				'Программа экспозиции',
				'Exposure program',
			),
			'Flash' => array(
				'SHORT',
				'Вспышка',
				'Flash',
			),
			'FlashEnergy' => array(
				'RATIONAL',
				'Энергия вспышки',
				'Flash energy level',
			),
			'WhiteBalance' => array(
				'SHORT',
				'Баланс белого',
				'White balance',
			),
			'LightSource' => array(
				'SHORT',
				'Источник света',
				'Light source',
			),
			'ColorSpace' => array(
				'RATIONAL',
				'Цветовое пространство',
				'Color space',
			),
			'SensingMethod' => array(
				'SHORT',
				'Тип фото датчика',
				'Image sensor type',
			),
			'FocalLengthIn35mmFilm' => array(
				'RATIONAL',
				'35mm эквивалент',
				'Focal length in 35 mm film',
			),
			'Contrast' => array(
				'SHORT',
				'Контраст',
				'Contrast',
			),
			'Saturation' => array(
				'SHORT',
				'Насыщенность',
				'Saturation',
			),
			'Sharpness' => array(
				'SHORT',
				'Резкость',
				'Sharpness',
			),
			'Compression' => array(
				'SHORT',
				'Схема сжатия',
				'Compression scheme',
			),
			'ExposureBiasValue' => array(
				'RATIONAL',
				'Значение экспозиции (APEX)',
				'Exposure bias (APEX)',
			),
			'ExifImageWidth' => array(
				'RATIONAL',
				'Ширина изображения (оригинал)',
				'Orig image width',
			),
			'ExifImageLength' => array(
				'RATIONAL',
				'Высота изображения (оригинал)',
				'Orig image height',
			),
			'FlashPixVersion' => array(
				'ASCII',
				'Поддерживаемая версия Flashpix',
				'Supported Flashpix version',
			),
			'ExifVersion' => array(
				'ASCII',
				'Версия Exif',
				'Exif version',
			),
			'CustomRendered' => array(
				'SHORT',
				'Пользовательская обработка изображений',
				'Custom image processing',
			),
			'FileName' => array(
				'ASCII',
				'Имя файла',
				'File name (original)',
			),
			'FileDateTime' => array(
				'LONG',
				'Дата/время',
				'Date/time',
			),
			'FileSize' => array(
				'LONG',
				'Размер файла',
				'File size',
			),
			'FileType' => array(
				'SHORT',
				'Тип файла',
				'File type',
			),
			'MimeType' => array(
				'ASCII',
				'MIME-тип',
				'Mime type',
			),
			'IsColor' => array(
				'SHORT',
				'Цветное изображение',
				'Is color',
			),
			'ByteOrderMotorola' => array(
				'ASCII',
				'Byte order motorola',
				'Byte order motorola',
			),
			'CCDWidth' => array(
				'ASCII',
				'CCD width',
				'CCD width',
			),
			'SectionsFound' => array(
				'ASCII',
				'MIME-тип',
				'Mime type',
			),
			'FocalPlaneResolutionUnit' => array(
				'SHORT',
				'Focal plane resolution unit',
				'Focal plane resolution unit',
			),
			'FocalPlaneXResolution' => array(
				'RATIONAL',
				'Число пикселей по горизонтали',
				'Number of pixels in the image width',
			),
			'FocalPlaneYResolution' => array(
				'RATIONAL',
				'Число пикселей по вертикали',
				'Number of pixels in the image length',
			),
			'SubsecTime' => array(
				'RATIONAL',
				'Subsec time',
				'Subsec time',
			),
			'SubSecTimeDigitized' => array(
				'RATIONAL',
				'SubSec time digitized',
				'SubSec time digitized',
			),
			'SubSecTimeOriginal' => array(
				'RATIONAL',
				'SubSec time original',
				'SubSec time original',
			),
			'UserComment' => array(
				'ASCII',
				'Комментарий',
				'User comments',
			),
			'FileSource' => array(
				'SHORT',
				'Исходное изображение',
				'Image source',
			),
			'DigitalZoomRatio' => array(
				'RATIONAL',
				'Цифровое масштабирование',
				'Digital zoom ratio',
			),
			'SceneCaptureType' => array(
				'SHORT',
				'Программа съемки',
				'Scene capture type',
			),
			'GainControl' => array(
				'SHORT',
				'Регулировка усиления',
				'Gain control',
			),
			'LensType' => array(
				'ASCII',
				'Тип объектива',
				'Lens type',
			),
			'OwnerName' => array(
				'ASCII',
				'Имя владельца',
				'Camera owner',
			),
			'FirmWareVersion' => array(
				'ASCII',
				'Прошивка камеры',
				'Camera formware version',
			),
			'SubjectDistanceRange' => array(
				'SHORT',
				'Расстояние до объекта съемки',
				'Subject distance range',
			),
			'SubjectDistance' => array(
				'RATIONAL',
				'Расстояние до объекта',
				'Subject distance',
			),
			'PhotometricInterpretation' => array(
				'SHORT',
				'Фотометрика',
				'Pixel composition',
			),
			'Artist' => array(
				'ASCII',
				'Автор изображения',
				'Image creator',
			),
			'BatteryLevel' => array(
				'RATIONAL',
				'Уровень заряда батареи',
				'Battery level',
			),
			'BitsPerSample' => array(
				'RATIONAL',
				'Бит на образец',
				'Bits per sample',
			),
			'BrightnessValue' => array( // log2( B/NK ) Note that: B:cd/cm2, N,K: constant, [-99.99/+99.99]
				'RATIONAL',
				'Значение яркости',
				'Brightness value',
			),
			'CFAPattern' => array(
				'ASCII',
				'Шаблон CFA',
				'CFA Pattern',
			),
			'CFARepeatPatternDim' => array(
				'ASCII',
				'CFA repeat pattern dim',
				'CFA repeat pattern dim',
			),
			'ComponentsConfiguration' => array(
				'ASCII',
				'Конфигурация компонентов',
				'Components configuration',
			),
			'CompressedBitsPerPixel' => array(
				'RATIONAL',
				'Сжатых бит/пиксель',
				'Compressed bits per pixel',
			),
			'Copyright' => array(
				'ASCII',
				'Права(авторство)',
				'Copyright',
			),
			'DeviceSettingDescription' => array(
				'ASCII',
				'Описание установок устройства',
				'Device setting description',
			),
			'DocumentName' => array(
				'ASCII',
				'Название документа',
				'Document name',
			),
			'ExifIfdPointer' => array(
				'ASCII',
				'Указатель Ifd',
				'Ifd pointer',
			),
			'ExposureIndex' => array(
				'RATIONAL',
				'Индекс экспозиции',
				'Exposure index',
			),
			'FillOrder' => array(
				'SHORT',
				'The logical order of bits within a byte',
				'The logical order of bits within a byte',
			),
			'HalftoneHints' => array(
				'SHORT',
				'Halftone hints',
				'Halftone hints',
			),
			'ImageUniqueID' => array(
				'ASCII',
				'Image unique ID',
				'Image unique ID',
			),
			'InterColorProfile' => array(
				'ASCII',
				'Inter color profile',
				'Inter color profile',
			),
			'InteroperabilityIFDPointer' => array(
				'ASCII',
				'Interoperability IFD pointer',
				'Interoperability IFD pointer',
			),
			'InterOperabilityIndex' => array(
				'ASCII',
				'Inter operability index',
				'Inter operability index',
			),
			'InteroperabilityVersion' => array(
				'ASCII',
				'Interoperability version',
				'Interoperability version',
			),
			'NewSubfileType' => array(
				'ASCII',
				'New subfile type',
				'New subfile type',
			),
			'ExtraSamples' => array(
				'SHORT',
				'Extra samples',
				'Extra samples',
			),
			'OECF' => array(
				'ASCII',
				'OECF',
				'OECF',
			),
			'PixelXDimension' => array(
				'RATIONAL',
				'Pixel X dimension',
				'Pixel X dimension',
			),
			'PixelYDimension' => array(
				'RATIONAL',
				'Pixel Y dimension',
				'Pixel Y dimension',
			),
			'PlanarConfiguration' => array(
				'SHORT',
				'Planar configuration',
				'Planar configuration',
			),
			'Predictor' => array(
				'SHORT',
				'A mathematical operator that is applied to the image data before an encoding scheme is applied',
				'A mathematical operator that is applied to the image data before an encoding scheme is applied',
			),
			'PrimaryChromaticities' => array(
				'RATIONAL',
				'Primary chromaticities',
				'Primary chromaticities',
			),
			'ReferenceBlackWhite' => array(
				'ASCII',
				'Reference black white',
				'Reference black white',
			),
			'RelatedImageFileFormat' => array(
				'ASCII',
				'Related image file format',
				'Related image file format',
			),
			'RelatedImageLength' => array(
				'RATIONAL',
				'Related image length',
				'Related image length',
			),
			'RelatedSoundFile' => array(
				'ASCII',
				'Related sound file',
				'Related sound file',
			),
			'RowsPerStrip' => array(
				'ASCII',
				'Rows per strip',
				'Rows per strip',
			),
			'SampleFormat' => array(
				'SHORT',
				'Sample format',
				'Sample format',
			),
			'SamplesPerPixel' => array(
				'SHORT',
				'Samples rer rixel',
				'Samples rer rixel',
			),
			'SpatialFrequencyResponse' => array(
				'ASCII',
				'Spatial frequency response',
				'Spatial frequency response',
			),
			'SpectralSensitivity' => array(
				'ASCII',
				'Spectral sensitivity',
				'Spectral sensitivity',
			),
			'StripByteCounts' => array(
				'ASCII',
				'Strip byte counts',
				'Strip byte counts',
			),
			'StripOffsets' => array(
				'ASCII',
				'Strip offsets',
				'Strip offsets',
			),
			'SubIFDs' => array(
				'ASCII',
				'SubIFDs',
				'SubIFDs',
			),
			'SubjectArea' => array(
				'ASCII',
				'Subject area',
				'Subject area',
			),
			'SubjectLocation' => array(
				'SHORT',
				'Subject location',
				'Subject location',
			),
			'Threshholding' => array(
				'SHORT',
				'Threshholding',
				'Threshholding',
			),
			'TransferFunction' => array(
				'SHORT',
				'Transfer function fin tabular style',
				'Transfer function fin tabular style',
			),
			'TransferRange' => array(
				'ASCII',
				'Transfer range',
				'Transfer range',
			),
			'WhitePoint' => array(
				'RATIONAL',
				'White point',
				'White point',
			),
			'GPSVersionID' => array(
				'ASCII',
				'Версия GPS',
				'GPS version',
			),
			'InfoIFDPointer' => array(
				'ASCII',
				'Info IFD pointer',
				'Info IFD pointer',
			),
			'GPSLatitudeRef' => array(
				'CHAR',
				'Широта',
				'Latitude reference',
			),
			'GPSLatitude' => array(
				'RATIONAL',
				'Значение широты',
				'Latitude',
			),
			'GPSLongitudeRef' => array(
				'CHAR',
				'Долгота',
				'Longitude reference',
			),
			'GPSLongitude' => array(
				'RATIONAL',
				'Значение долготы',
				'Longitude',
			),
			'GPSAltitudeRef' => array(
				'SHORT',
				'Уровень моря (ниже/выше)',
				'Altitude reference',
			),
			'GPSAltitude' => array(
				'RATIONAL',
				'Высота над уровнем моря',
				'Altitude',
			),
			'GPSTimeStamp' => array(
				'RATIONAL',
				'Время GPS',
				'GPS UTC',
			),
			'GPSSatellites' => array(
				'ASCII',
				'Спутники GPS',
				'GPS satellites',
			),
			'GPSStatus' => array(
				'ASCII',
				'Статус GPS',
				'GPS status',
			),
			'GPSMeasureMode' => array(
				'ASCII',
				'Режим замера GPS',
				'GPS measure mode',
			),
			'GPSSpeedRef' => array(
				'CHAR',
				'Единица скорости GPS',
				'GPS speed reference',
			),
			'GPSSpeed' => array(
				'RATIONAL',
				'Скорость GPS',
				'GPS speed',
			),
			'GPSTrackRef' => array(
				'CHAR',
				'Направление маршрута GPS',
				'GPS track reference',
			),
			'GPSTrack' => array(
				'RATIONAL',
				'Маршрут GPS',
				'GPS track',
			),
			'GPSImgDirectionRef' => array(
				'CHAR',
				'Направление(общее) GPS во время съемки',
				'GPS direction while image capture',
			),
			'GPSImgDirection' => array(
				'RATIONAL',
				'Направление GPS',
				'GPS image direction',
			),
			'GPSDestLatitudeRef' => array(
				'CHAR',
				'Направление широты GPS-точки назначения',
				'GPS destination point latitude reference',
			),
			'GPSDestLatitude' => array(
				'RATIONAL',
				'Широта точки назначения',
				'GPS destination point latitude',
			),
			'GPSDestLongitudeRef' => array(
				'CHAR',
				'Направление долготы GPS-точки назначения',
				'GPS destination point longitude reference',
			),
			'GPSDestLongitude' => array(
				'RATIONAL',
				'Долгота точки назначения',
				'GPS destination point longitude',
			),
			'GPSDestBearingRef' => array(
				'CHAR',
				'GPS направление азимута точки назначения',
				'GPS destination point bearing reference',
			),
			'GPSDestBearing' => array(
				'RATIONAL',
				'Азимут точки назначения',
				'GPS destination point bearing',
			),
			'GPSDestDistanceRef' => array(
				'CHAR',
				'Единица GPS дистанции до точки назначения',
				'GPS destination point distance reference',
			),
			'GPSDestDistance' => array(
				'RATIONAL',
				'GPS дистанция до точки назначения',
				'GPS destination point distance',
			),
		);

		foreach($ExifData as $k=>&$v) {

			switch($v[self::EXIF_TYPE_PHP]) {

				case 'SHORT':
					$sql_type = 'tinyint(3) unsigned NOT NULL';
					break;

				case 'CHAR':
					$sql_type = 'char(1) default NULL';
					break;

				case 'LONG':
				case 'WORD':
					$sql_type = 'int(11) unsigned default NULL';
					break;

				case 'ASCII':
				case 'ASCII_TIME':
				case 'RATIONAL':
				default:
					$sql_type = 'varchar(255) default NULL';
			}
			$v[self::EXIF_TYPE_SQL] = $sql_type;

		}

		return $ExifData;
	}


	public static function GetExifListItemValue($e_tag, $t_index) {

		$e_list = self::GetExifPropertyList();
		$e_data = ifsetor($e_list[$e_tag], null);
		$e_value = ifsetor($e_data[$t_index], null);

		return $e_value;
	}

}
