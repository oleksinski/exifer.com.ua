<?

/**
 * "Image Magic" Basic Image Processing
 */

class IM {

	const IM_RESIZE_MODE_NULL = ''; // ignore any modes
	const IM_RESIZE_MODE_SHRINK = '>'; // [800x600>] shrink images to fit into the size given
	const IM_RESIZE_MODE_ENLARGE = '<'; // [800x600<] enlarges images that are smaller than the given size
	const IM_RESIZE_MODE_EXACT_FIT = '!'; // [800x600!] ignore aspect ratio and resize to exact fit the size specified
	const IM_RESIZE_MODE_LESS_FILL = '^'; // [800x600^] resize the image based on the smallest fitting dimension.

	const IM_RESIZE_QUALITY = '90%';

	public static function IM_CONVERT() {
		return ShellCmdModel::GetCmdPath('IM_CONVERT', true);
	}

	public static function IM_IDENTIFY() {
		return ShellCmdModel::GetCmdPath('IM_IDENTIFY', true);
	}

	public static function GetCorrectResizeMode($mode) {
		$modes = ReflectionModel::getClassConstValueList(__CLASS__, 'IM_RESIZE_MODE_');
		$mode = insetor($mode, $modes, self::IM_RESIZE_MODE_NULL);
		return $mode;
	}

	public static function GetResizeModeCmd($mode) {

		$mode = self::GetCorrectResizeMode($mode);

		if(Predicate::windowsOS()) {

			$escape_mode = array(
				self::IM_RESIZE_MODE_SHRINK,
				self::IM_RESIZE_MODE_LESS_FILL,
				self::IM_RESIZE_MODE_ENLARGE,
			);

			if(in_array($mode, $escape_mode)) {
				$mode = '^'.$mode;
			}
		}
		else {
			if($mode) {
				$mode = '\\'.$mode;
			}
		}

		return $mode;
	}

	/**
	 * Do not use quality option => $quality=0
	 * Max quality => $quality=100
	 */
	public static function GetQualityCmd($quality) {

		$quality_cmd = null;

		$quality = Cast::unsignint($quality_orig=$quality);

		if($quality) {
			$quality = ($quality >= 100) ? 100 : $quality;
			$quality .= Predicate::windowsOS() ? '%%' : '%';
			$quality_cmd = sprintf('-quality %s', $quality);
			if($quality==100) {
				$quality_cmd .= ' -compress LossLess';
			}
		}

		return $quality_cmd;
	}

	public static function GetGeometryCmd($geometry) {

		$geometry = Cast::unsignint($geometry_orig=$geometry);

		if($geometry && _strstr($geometry_orig, '%')) {
			$geometry .= Predicate::windowsOS() ? '%%' : '%';
		}

		return $geometry;
	}

	public static function GetProfileStripCmd($bool) {

		// -strip  IS THE SAME AS  +profile "*"
		$profile_cmd = $bool ? '-strip' : null;
		return $profile_cmd;
	}

	public static function GetTransparencyFixCmd($source) {

		$transparency_fix_cmd = '';

		if(self::IsImage($source)) {
			$info = self::GetImageInfo($source);
			if(!empty($info) && isset($info['type'])) {
				$transparent_types = array(
					IMAGETYPE_GIF,
					IMAGETYPE_PNG,
					IMAGETYPE_PSD,
				);
				if(in_array($info['type'], $transparent_types)) {
					$transparency_fix_cmd = sprintf('-background white -flatten');
				}
			}
		}

		return $transparency_fix_cmd;
	}

	public static function GetGravityCmd($gravity) {
		$gravityList = self::GetGravityList();
		$gravity_cmd = insetcheck(_strtolower($gravity), $gravityList) ? sprintf('-gravity %s', $gravity) : '';
		return $gravity_cmd;
	}

	public static function IsImage($source) {

		$bool = false;

		if(1) {
			$img_info = self::GetImageInfo($source);
			$bool = Cast::bool($img_info);
		}
		else {
			$cmd = sprintf(
				'%s -verbose %s',
				self::IM_IDENTIFY(),
				ShellCmd::EscapePath($source)
			);
			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $source);
		}

		return $bool;
	}

	public static function GetImageInfo($source) {

		$info = array();

		if(file_exists($source)) {

			$imagesize = @getimagesize($source);

			if($imagesize) {
				$info['width'] = ifsetor($imagesize[0], 0);
				$info['height'] = ifsetor($imagesize[1], 0);
				$info['type'] = ifsetor($imagesize[2], 0);
				$info['htmlsize'] = ifsetor($imagesize[3], '');
				$info['bits'] = ifsetor($imagesize['bits'], 0);
				$info['channels'] = ifsetor($imagesize['channels'], 0);
				$info['mime'] = ifsetor($imagesize['mime'], '');
				if(!$info['mime'] && function_exists('image_type_to_mime_type')) {
					$info['mime'] = image_type_to_mime_type($info['type']);
				}
				$info['filesize'] = filesize($source);
			}
		}

		return $info;
	}

	public static function ImCopy($source, $target) {

		$result = false;

		if(self::IsImage($source)) {

			$cmd = sprintf('%s %s -strip -colorspace RGB %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				ShellCmd::EscapePath($target)
			);

			_e(sprintf('IM Copy %s => %s', $source, $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
		}

		return $result;
	}

	public static function SetPerms($source, $chmod=0664) {

		$result = @chmod($source, $chmod);
		return $result;
	}

	/**
	 * Specify orientation of a digital camera image. $orient=0 => exif auto-orient
	 *
	 * @param string $source - filepath
	 * @param string $target - filepath
	 * @param int $orient - EXIF['Orientation']
	 */
	public static function Orient($source, $target=null, $orient=0) {

		$result = false;

		if(self::IsImage($source)) {

			$target = ifsetor($target, $source);

			$orient_map = array(
				1 => 'top-left',//'TopLeft',
				2 => 'top-right',//'TopRight',
				3 => 'bottom-right',//'BottomRight',
				4 => 'bottom-left',//'BottomLeft',
				5 => 'left-top',//'LeftTop',
				6 => 'right-top',//'RightTop',
				7 => 'right-bottom',//'RightBottom',
				8 => 'left-bottom',//'LeftBottom',
			);

			if(array_key_exists($orient, $orient_map)) {
				$orient_cmd = sprintf('-orient %s', $orient_map[$orient]);
			}
			else {
				$orient_cmd = '-auto-orient';
			}

			$cmd = sprintf('%s %s %s -strip -colorspace RGB %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				$orient_cmd,
				ShellCmd::EscapePath($target)
			);

			_e(sprintf('ORIENT %s', $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);

			if($result && array_key_exists($orient, $orient_map)) {
				$result = self::Rotate($target, $target, $orient);
			}
		}

		return $result;
	}

	/**
	 * @param string $source - filepath
	 * @param string $target - filepath
	 * @param int $orient - EXIF['Orientation']
	 */
	public static function Rotate($source, $target=null, $orient=null) {

		$result = false;

		if(self::IsImage($source)) {

			$target = ifsetor($target, $source);

			$rotate_map = array(
				1 => '',
				2 => '-flop',
				3 => '-rotate 180',
				4 => '-flip',
				5 => '-transpose', // The same is: -flip -rotate 90
				6 => '-rotate 90',
				7 => '-transverse', // The same is: -flop -rotate 90
				8 => '-rotate 270',
			);

			$rotate_cmd = array_key_exists($orient, $rotate_map) ? $rotate_map[$orient] : '';

			$cmd = sprintf('%s %s %s -strip -colorspace RGB %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				$rotate_cmd,
				ShellCmd::EscapePath($target)
			);

			_e(sprintf('ROTATE %s', $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
		}

		return $result;
	}

	/**
	 * @param string $source
	 * @param string $target
	 * @param mixed $width [230, 70%, ...]
	 * @param mixed $height [230, 70%, ...]
	 * @param string $mode
	 * @param mixed $quality [75, 80%]
	 *
	 * Examples:
	 * ^^^^^^^^^
	 * Resize(source, target, 800, 600, '>', 75%)
	 * Resize(source, target, 70%, 60%)
	 * Resize(source, target, 0, 60%)
	 * Resize(source, target, 70%, 0)
	 */
	public static function Resize(
		$source,
		$target,
		$width,
		$height,
		$mode=self::IM_RESIZE_MODE_NULL,
		$quality=self::IM_RESIZE_QUALITY
	) {

		if(self::IsImage($source)) {

			$target = ifsetor($target, $source);

			$width_cmd = self::GetGeometryCmd($width);
			$width_cmd = $width_cmd ? $width_cmd : null;

			$height_cmd = self::GetGeometryCmd($height);
			$height_cmd = $height_cmd ? $height_cmd : null;

			$mode_cmd = self::GetResizeModeCmd($mode);

			$quality_cmd = self::GetQualityCmd($quality);

			$resize_cmd = sprintf('-resize %sx%s%s+0+0', $width_cmd, $height_cmd, $mode_cmd);

			$cmd = sprintf(
				'%s %s %s %s -strip +repage -colorspace RGB %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				$quality_cmd,
				$resize_cmd,
				ShellCmd::EscapePath($target)
			);

			_e(sprintf('RESIZE %s', $resize_cmd, $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
		}

		return $result;
	}

	public static function ResizeFill(
		$source,
		$target,
		$width,
		$height,
		$mode=self::IM_RESIZE_MODE_NULL,
		$quality=self::IM_RESIZE_QUALITY,
		$fill_color='white'
	) {

		$result = false;

		if(self::IsImage($source)) {

			$target = ifsetor($target, $source);

			$width_cmd = self::GetGeometryCmd($width);
			$width_cmd = $width_cmd ? $width_cmd : null;

			$height_cmd = self::GetGeometryCmd($height);
			$height_cmd = $height_cmd ? $height_cmd : null;

			$mode_cmd = self::GetResizeModeCmd($mode);

			$quality_cmd = self::GetQualityCmd($quality);

			$size_cmd = sprintf('-size %sx%s', $width, $height);

			$resize_cmd = sprintf('-resize %sx%s%s+0+0', $width_cmd, $height_cmd, $mode_cmd);

			$cmd = sprintf(
				'%s %s %s %s %s xc:%s +swap -gravity Center -composite -strip +repage -colorspace RGB %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				$resize_cmd,
				$quality_cmd,
				$size_cmd,
				$fill_color,
				ShellCmd::EscapePath($target)
			);

			_e(sprintf('RESIZE %s', $resize_cmd, $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
		}

		return $result;
	}


	/**
	 * Smart image resize
	 * ------------------
	 * 1. Keep an image side-by-side proportion
	 * 2. Enlarge smaller side to bigger one if possible && $enlarge_tolerance>0
	 * 3. Capture the maximum source image area befor crop & resize
	 *
	 * Result image dimension:
	 * -----------------------
	 * 1. rectangular $t_width x $t_height ($enlarge_tolerance=0)
	 * 2. rectangular bigger_side x (smaller_side+delta) (0<$enlarge_tolerance<1 && proportional resize is possible)
	 * 3. squared bigger_side x bigger_side ($enlarge_tolerance=1 && proportional resize is possible)
	 *
	 * Param description
	 * -----------------
	 * @param string $source
	 * @param string $target
	 * @param int $t_width
	 * @param int $t_height
	 * @param float $enlarge_tolerance [0, 1]
	 * @param mixed $quality [75, 80%]
	 * @param string $gravity [Center, SouthWest...]
	 *
	 * @return bool
	 */
	public static function ResizeSmart(
		$source,
		$target=null,
		$t_width=null,
		$t_height=null,
		$enlarge_tolerance=0,
		$quality=self::IM_RESIZE_QUALITY,
		$gravity='Center'
	) {

		$result = false;

		$s_info = self::GetImageInfo($source);

		$s_width = ifsetor($s_info['width'], 0, true);
		$s_height = ifsetor($s_info['height'], 0, true);

		if($s_info && $s_width && $s_height) {

			$target = ifsetor($target, $source, true);

			$enlarge_tolerance = Cast::float($enlarge_tolerance);
			if($enlarge_tolerance<0) $enlarge_tolerance = 0;
			if($enlarge_tolerance>1) $enlarge_tolerance = 1;

			$k_ss_wh = $s_width/$s_height;
			//_e(array('k_ss_wh'=>$k_ss_wh));

			$s_long_x = $s_width>=$s_height;
			$s_long_y = !$s_long_x;

			$t_width = Cast::unsignint($t_width);
			$t_height = Cast::unsignint($t_height);

			if(!$t_width && !$t_height) {
				list($t_width, $t_height) = array($s_width, $s_height);
			}
			elseif(!$t_width) {
				$t_width = $t_height*$k_ss_wh;
			}
			elseif(!$t_height) {
				$t_height = $t_width/$k_ss_wh;
			}

			$k_tt_wh = $t_width/$t_height;
			//_e(array('k_tt_wh'=>$k_tt_wh));

			$st_enlarge_w = $s_width<$t_width;
			$st_enlarge_h = $s_height<$t_height;

			$k_st_ww = $s_width/$t_width;
			$k_st_hh = $s_height/$t_height;

			$k_st_ww_hh_min = min($k_st_ww, $k_st_hh);

			$t_crop_w = $k_st_ww_hh_min*$t_width;
			$t_crop_w = $st_enlarge_w ? ceil($t_crop_w) : floor($t_crop_w);
			$t_crop_h = $k_st_ww_hh_min*$t_height;
			$t_crop_h = $st_enlarge_h ? ceil($t_crop_h) : floor($t_crop_h);

			//_e(array('t_crop_w before'=>$t_crop_w));
			//_e(array('t_crop_h before'=>$t_crop_h));

			// check which target image side more quickly reaches source image border
			if($k_st_ww_hh_min==$k_st_ww) { // width does
				$t_crop_max_h = min($t_crop_w, $s_height);
				$t_crop_max_h -= (1-$enlarge_tolerance)*($t_crop_max_h-$t_crop_h);
				while($t_crop_h<$t_crop_max_h) {
					$t_crop_h++;
				}
			}
			else { // height does
				$t_crop_max_w = min($t_crop_h, $s_width);
				$t_crop_max_w -= (1-$enlarge_tolerance)*($t_crop_max_w-$t_crop_w);
				while($t_crop_w<$t_crop_max_w) {
					$t_crop_w++;
				}
			}

			//_e(array('t_crop_w after'=>$t_crop_w));
			//_e(array('t_crop_h after'=>$t_crop_h));

			$k_crop_wh = $t_crop_w/$t_crop_h;
			//_e(array('k_crop_wh'=>$k_crop_wh));

			$t_resize_w = $t_width;
			$t_resize_h = $t_height;

			if($k_tt_wh!=$k_crop_wh) {
				if($s_long_x) {
					$t_resize_h = $t_resize_w/$k_crop_wh;
					$t_resize_h = $st_enlarge_h ? ceil($t_resize_h) : floor($t_resize_h);
				}
				else {
					$t_resize_w = $t_resize_h/$k_crop_wh;
					$t_resize_w = $st_enlarge_h ? ceil($t_resize_w) : floor($t_resize_w);
				}
			}

			//_e(array('t_resize_w'=>$t_resize_w));
			//_e(array('t_resize_h'=>$t_resize_h));

			if(1) {

				$crop_cmd = sprintf(
					'%s -crop %sx%s%s+0+0', self::GetGravityCmd($gravity),
					$t_crop_w, $t_crop_h, self::IM_RESIZE_MODE_NULL
				);

				$resize_cmd = sprintf(
					'-resize %sx%s%s+0+0',
					$t_resize_w, $t_resize_h, self::IM_RESIZE_MODE_NULL
				);

				$cmd = sprintf(
					'%s %s %s %s %s -strip +repage -colorspace RGB %s',
					self::IM_CONVERT(),
					ShellCmd::EscapePath($source),
					$crop_cmd,
					$resize_cmd,
					self::GetQualityCmd($quality),
					ShellCmd::EscapePath($target)
				);

				_e(sprintf('RESIZE-CROP SMART %s, %s', $crop_cmd, $resize_cmd, $target));

				$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
			}
			else {
				$result = 1
					&& self::Crop($source,$target,$t_crop_w,$t_crop_h,0,0,$gravity,self::IM_RESIZE_MODE_NULL,'100%')
					&& self::Resize($target, $target, $t_resize_w, $t_resize_h, self::IM_RESIZE_MODE_NULL, $quality)
				;
			}
		}

		return $result;
	}


	/**
	 * @param string $source
	 * @param string $target
	 * @param int $width_height
	 * @param mixed $quality [75, 80%]
	 * @param string $gravity [Center, SouthWest...]
	 *
	 * Examples:
	 * ^^^^^^^^^
	 * ResizeSquare(source, target, 800, 75%)
	 * ResizeSquare(source, target, 60)
	 */
	public static function ResizeSquare(
		$source,
		$target,
		$width_height,
		$quality=self::IM_RESIZE_QUALITY,
		$gravity='Center'
	) {

		$result = false;

		$info = self::GetImageInfo($source);

		if($info) {

			$width = $height = $width_height;

			$result = self::ResizeSmart($source, $target, $width, $height, $enlarge_tolerance=0, $quality, $gravity);
		}

		return $result;
	}

	/**
	 * @param string $source
	 * @param string $target
	 * @param mixed $width [230, 70%, ...]
	 * @param mixed $height [230, 70%, ...]
	 * @param mixed $offsetX [230, 70%, ...]
	 * @param mixed $offsetY [230, 70%, ...]
	 * @param string $gravity [Center, SouthWest...]
	 * @param string $mode
	 * @param mixed $quality [75, 80%]
	 *
	 * Examples:
	 * ^^^^^^^^^
	 * Crop(source, target, 600, 600, 0, 0, 'Center')
	 */
	public static function Crop(
		$source,
		$target,
		$width,
		$height,
		$offsetX=0,
		$offsetY=0,
		$gravity=null,
		$mode=self::IM_RESIZE_MODE_NULL,
		$quality=self::IM_RESIZE_QUALITY
	) {

		$result = false;

		if(self::IsImage($source)) {

			$target = ifsetor($target, $source);

			$width_cmd = self::GetGeometryCmd($width);
			$width_cmd = $width_cmd ? $width_cmd : null;

			$height_cmd = self::GetGeometryCmd($height);
			$height_cmd = $height_cmd ? $height_cmd : null;

			$offsetx_cmd = self::GetGeometryCmd($offsetX);

			$offsety_cmd = self::GetGeometryCmd($offsetY);

			$mode_cmd = self::GetResizeModeCmd($mode);

			$quality_cmd = self::GetQualityCmd($quality);

			$gravity_cmd = self::GetGravityCmd($gravity);

			$crop_cmd = sprintf('-crop %sx%s%s+%s+%s',
				$width_cmd,
				$height_cmd,
				$mode_cmd,
				$offsetx_cmd,
				$offsety_cmd
			);

			$cmd = sprintf(
				'%s %s %s %s %s -strip +repage -colorspace RGB %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				$quality_cmd,
				$gravity_cmd,
				$crop_cmd,
				ShellCmd::EscapePath($target)
			);

			_e(sprintf('CROP %s %s', $crop_cmd, $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
		}

		return $result;
	}

	/**
	 * @param string $source
	 * @param string $target
	 * @param string $filter [Mitchell, ... see self::GetFilterList()]
	 * @param string $interpolate [bicubic, ... see self::GetInterpolateList()]
	 * @param mixed $quality
	 */
	public static function ApplyFilters(
		$source,
		$target,
		$filter=null,
		$interpolate=null,
		$quality=0
	) {

		if(self::IsImage($source)) {

			$target = ifsetor($target, $source);

			$filter_list = self::GetFilterList();
			$filter = in_array(_strtolower($filter), $filter_list) ? _strtolower($filter) : null;
			$filter_cmd = $filter ? sprintf('-filter %s', $filter) : null;

			$interpolate_list = self::GetInterpolateList();
			$interpolate = in_array(_strtolower($interpolate), $interpolate_list) ? _strtolower($interpolate) : null;
			$interpolate_cmd = $interpolate ? sprintf('-interpolate %s', $interpolate) : null;

			$quality_cmd = self::GetQualityCmd($quality);

			$unsharp_cmd = 0 ? sprintf('-unsharp 1x2+1.0+0.05') : null;

			$blur_cmd = 0 ? sprintf('-blur 0.5') : null;

			$normalize_cmd = 0 ? sprintf('-normalize') : null;

			$modulate_cmd = 0 ? sprintf('-modulate 120,90,100') : null;

			$contrast_cmd = 0 ? sprintf('-contrast') : null;

			$vignette_cmd = 0 ? sprintf('-vignette 25') : null;

			$cmd = sprintf(
				'%s %s %s %s %s %s %s %s %s %s %s %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				$filter_cmd,
				$interpolate_cmd,
				$quality_cmd,
				$unsharp_cmd,
				$blur_cmd,
				$normalize_cmd,
				$modulate_cmd,
				$contrast_cmd,
				$vignette_cmd,
				ShellCmd::EscapePath($target)
			);

			_e(sprintf('APPLY FILTERS %s', $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
		}

		return $result;
	}

	public static function DropMetaInfo($source, $target=null, $profile='*') {

		$result = false;

		if(self::IsImage($source)) {

			$target = ifsetor($target, $source);

			$profile_cmd = self::GetProfileStripCmd($profile);

			$cmd = sprintf(
				'%s %s %s %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				$profile_cmd,
				ShellCmd::EscapePath($target)
			);

			_e(sprintf('DROP META INFO %s', $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
		}

		return $result;
	}

	/**
	 * http://www.fmwconcepts.com/imagemagick/histog/index.php
	 *
	 * @param $string $source
	 * @param $string $target
	 * @param string $channel
	 * @param int $width
	 * @param int $height
	 * @param mixed $quality
	 */
	public static function Histogram(
		$source,
		$target,
		$channel=null
	) {

		$result = false;

		if(self::IsImage($source)) {

			$histogram_cmd = sprintf('histogram:%s', ShellCmd::EscapePath($target));

			$ChannelList = self::GetChannelList();
			$channel = in_array(_strtolower($channel), $ChannelList) ? _strtolower($channel) : null;
			$histogram_cmd = $channel ? sprintf('-channel %s -separate %s', $channel, ShellCmd::EscapePath($target)) : $histogram_cmd;

			$cmd = sprintf(
				'%s %s histogram:- | %1$s - -fill none -opaque white -bordercolor white -background white -border 10x30 -font ArialB -pointsize 20 -draw "text 5,22 \'Red\'" %s',
				self::IM_CONVERT(),
				ShellCmd::EscapePath($source),
				ShellCmd::EscapePath($target)
			);

			//$cmd = sprintf(
			//	'%s %s %s',
			//	self::IM_CONVERT(),
			//	ShellCmd::EscapePath($source),
			//	$histogram_cmd
			//);

			_e(sprintf('HISTORGAM %s', $target));

			$result = self::ExecCmd(__FUNCTION__, $cmd, $source, $target);
		}

		return $result;
	}


	/**
	 * http://www.dylanbeattie.net/magick/filters/result.html
	 */
	private static function GetFilterList() {

		$list = array(
			'point',
			'box',
			'triangle',
			'hermite',
			'hanning',
			'blackman',
			'gaussian',
			'quadratic',
			'cubic',
			'catrom',
			'mitchell',
			'lanczos',
			'bessel',
			'sinc',
		);

		return $list;
	}

	private static function GetInterpolateList() {

		$list = array(
			'integer', // The color of the top-left pixel (floor function)
			'nearest-neighbor', // The nearest pixel to the lookup point (rounded function)
			'average', // The average color of the surrounding four pixels
			'bilinear', // A double linear interpolation of pixels (the default)
			'mesh', // Divide area into two flat triangular interpolations
			'bicubic', // Fitted bicubic-spines of surrounding 16 pixels
			'spline', // Direct spline curves (colors are blurred)
			'filter', // Use resize -filter settings
		);

		return $list;
	}

	private static function GetGravityList() {

		$list = array(
			'northwest',
			'north',
			'northeast',
			'west',
			'center',
			'east',
			'southwest',
			'south',
			'southeast'
		);

		return $list;
	}

	private static function GetChannelList() {

		$list = array(
			'red',
			'green',
			'blue',
			'alpha',
			'cyan',
			'magenta',
			'yellow',
			'black',
			'opacity',
			'index',
			'rgb',
			'rgba',
			'cmyk',
			'cmyka',
		);

		return $list;
	}

	/**
	 * @private
	 */
	private static function ExecCmd($func, $cmd, $source, $target) {
		$result = false;
		$dirname = @dirname($target);
		if(FileFunc::mkdir($dirname)) { // create destination unexisted dir schema
			//$transparency_fix_cmd = GetTransparencyFixCmd($source);
			$result = ShellCmd::Execute($cmd);
			//_e(sprintf('FUNC[%s]: ExecOk=%u', $func, $result));
			$result = $result && self::IsImage($target);
			//_e(sprintf('FUNC[%s]: isTargetImage=%u', $func, $result));
			$result = $result && self::SetPerms($target);
			//_e(sprintf('FUNC[%s]: SetTargetPerms=%u', $func, $result));
		}
		_e(_str_repeat('-', 20), $result ? E_USER_NOTICE : E_USER_WARNING);
		return $result;
	}
}