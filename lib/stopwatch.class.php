<?

/**
 * Simple stopwatch
 *
 */
class StopWatch {

	private $__startTime;
	private $__stopTime;
	private $__nowTime;

	/**
	 * @Constructor
	 *
	 * @param bool $autoStart automatic start timer
	 * @return Stopwatch
	 */
	public function __construct($autoStart = true) {
		if ($autoStart == true) {
			$this->start();
		}
		else {
			$this->resetTime();
		}
	}

	public function start() {
		$this->__stopTime = 0;
		$this->__startTime = microtime();
	}

	public function resetTime() {
		$this->__startTime = $this->__stopTime = 0;
	}

	public function stop() {
		$this->__stopTime = microtime();
	}

	/**
	 * If need stop timer and return result with decimals with a dot
	 *
	 * @param int $decimals
	 * @return string
	 */
	public function get($decimals=false, $stop=true) {

		if(!$this->__startTime) {
			return false;
		}
		if(!$this->__stopTime) {
			$this->stop();
		}

		list($msec, $sec) = explode(' ', $this->__startTime);
		$start_time = (float)$msec + (float)$sec;

		list($msec, $sec) = explode(' ', $this->__stopTime);
		$end_time = (float)$msec + (float)$sec;

		if($stop == false) {
			$this->__stopTime = 0;
		}

		return !$decimals ? $end_time - $start_time : number_format($end_time - $start_time, $decimals);
	}

	public function getNow() {
		$this->__nowTime = microtime();
		list($msec, $sec) = explode(' ', $this->__nowTime);
		$msec = _substr(sprintf('%.4f', $msec), 1);
		$output = sprintf('%s%s', date('H:i:s', $sec), $msec);
		return $output;
	}

	public function getFormat($decimals=false, $stop=true) {
		return sprintf('%s sec [%s]', $this->get($decimals, $stop), $this->getNow());
	}

}

