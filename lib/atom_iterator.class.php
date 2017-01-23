<?

/**
 *
 * @author alex
 *
 */
class AtomicObjectIteratorCollection implements IteratorAggregate {

	/**
	 *
	 * @var array
	 */
	private $items = array();
	/**
	 *
	 * @var unknown_type
	 */
	private $onLoad;
	/**
	 *
	 * @var unknown_type
	 */
	private $loaded = false;

	/**
	 *
	 * @param unknown_type $key
	 * @param unknown_type $item
	 */
	final public function addItem($key, $item) {
		//$this->checkCallback();
		$this->items[$key] = $item;
	}

	/**
	 *
	 * @param unknown_type $key
	 */
	final public function getItem($key) {
		//$this->checkCallback();
		if(isset($this->items[$key])) {
			return $this->items[$key];
		}
	}

	/**
	 *
	 */
	final public function getCurrent() {
		return current($this->items);
	}

	/**
	 *
	 */
	final public function getFirst() {
		return reset($this->items);
	}

	/**
	 *
	 */
	final public function getLast() {
		return end($this->items);
	}

	/**
	 *
	 */
	final public function getNext() {
		return next($this->items);
	}

	/**
	 *
	 */
	final public function getPrev() {
		return prev($this->items);
	}

	/**
	 *
	 * @param unknown_type $key
	 * @return AtomicObjectIteratorCollection
	 */
	final public function removeItem($key) {
		//$this->checkCallback();
		if(isset($this->items[$key])) {
			unset($this->items[$key]);
		}
		return $this;
	}

	/**
	 *
	 */
	final public function clear() {
		$this->loaded=false;
		$this->items=array();
		return $this;
	}

	/**
	 * @return array
	 */
	final public function keys() {
		//$this->checkCallback();
		return array_keys($this->items);
	}

	/**
	 * @return int
	 */
	final public function length() {
		//$this->checkCallback();
		return sizeof($this->items);
	}

	/**
	 *
	 * @param unknown_type $key
	 */
	final public function exists($key) {
		//$this->checkCallback();
		return isset($this->items[$key]);
	}

	/**
	 *
	 */
	final public function shuffle() {
		$keys = $this->keys();
		shuffle($keys);
		$items = array();
		foreach($keys as $key) {
			$items[$key] = $this->items[$key];
		}
		$this->items = $items;
		return $this;
	}

	/**
	 * Use this method to define a function to be invoked prior to accessing the collection.
	 * The function should take a collection as a its sole parameter.
	 */
	final public function setLoadCallback($functionName, $objOrClass=null) {
		if($objOrClass) {
			$callback = array($objOrClass, $functionName);
		}
		else {
			$callback = $functionName;
		}
		$this->onLoad = $callback;
	}

	/**
	 * Check to see if a callback has been defined and if so,
	 * whether or not it has already been called.
	 * If not, invoke the callback function.
	 */
	final private function checkCallback() {
		if(isset($this->onLoad) && !$this->loaded) {
			$this->loaded = true;
			call_user_func($this->onLoad, $this);
		}
	}

	/**
	 * @override
	 * @see IteratorAggregate::getIterator()
	 */
	final public function getIterator() {
		$this->checkCallback();
		return new AtomicObjectIterator($this);
	}
}

/**
 *
 * @author alex
 *
 */
class AtomicObjectIterator implements Iterator {

	/**
	 *
	 * @var unknown_type
	 */
	private $collection;
	/**
	 *
	 * @var unknown_type
	 */
	private $index = 0;
	/**
	 *
	 * @var unknown_type
	 */
	private $keys;

	/**
	 *
	 * @param unknown_type $Collection
	 */
	public function __construct(AtomicObjectIteratorCollection $Collection) {
		$this->collection = $Collection;
		$this->keys = $this->collection->keys();
	}

	/**
	 * @override
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->index = 0;
	}

	/**
	 * @return bool
	 */
	public function hasMore() {
		return $this->index < $this->collection->length();
	}

	/**
	 * @override
	 * @see Iterator::key()
	 * @return array
	 */
	public function key() {
		return $this->keys[$this->index];
	}

	/**
	 * @override
	 * @see Iterator::current()
	 */
	public function current() {
		return $this->collection->getItem($this->keys[$this->index]);
	}

	/**
	 * @override
	 * @see Iterator::next()
	 */
	public function next() {
		$this->index++;
	}

	/**
	 * @override
	 * @see Iterator::valid()
	 */
	public function valid() {
		return $this->hasMore();
	}
}
