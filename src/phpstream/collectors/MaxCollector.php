<?php

/**
 * Max collector.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\collectors;

use phpstream\util\Optional;
use phpstream\util\Comparator;

/**
 * Collects the max collected element.
 */
class MaxCollector extends AbstractCollector {
	
	/** @var Optional Current collected element (max of all at any time) */
	private $current;
	
	/** 
	 * @var callable Comparator function. 
	 * @ignore
	 */
	private $callable;
	
	/** 
	 * @var Comparator Comparator object. 
	 * @ignore
	 */
	private $comparator;
	
	/**
	 * Instanciate a new MaxCollector using specific comparator function.
	 * If comparator function is not set, use default comparator.
	 * 
	 * @param callable|Comparator $cmp Comparator method / object.
	 * 
	 * @throws \InvalidArgumentException If parameter is not callable or instance of Comparator.
	 */
	public function __construct($cmp = null) {
		parent::__construct();
		if ($cmp === null) {
			$this->callable = function($o1, $o2) {
				if ($o1 == $o2)
					return 0;
				return $o1 < $o2 ? -1 : 1;
			};
		} else if ($cmp instanceof Comparator) {
			$this->comparator = $cmp;
		} else if (is_callable($cmp)) {
			$this->callable = $cmp;
		} else {
			throw new \InvalidArgumentException('Parameter must be callable or Comparator.');
		}
	}
	
	/**
	 * Collects the max element at any time regarding the comparator method.
	 * 
	 * @param type $key Key value in the initial array (<em>array index</em>)
	 * @param type $value Value after processing
	 */
	public function collect($key, $value) {
		if ($this->current->isEmpty()) {
			$this->current = Optional::of($value);
		} else if ($this->comparator !== null) {
			if ($this->comparator->compare($value, $this->current->get()) > 0)
				$this->current = Optional::of($value);
		} else {
			if (call_user_func($this->callable, $value, $this->current->get()) > 0)
				$this->current = Optional::of($value);
		}
	}

	/**
	 * Removing the collected element.
	 */
	public function reset() {
		$this->current = Optional::absent();
	}
	
	/**
	 * Returns the max collected element regarding the comparator method, Optional::absent() if none.
	 * 
	 * @return Optional the max collected element regarding the comparator method, Optional::absent() if none.
	 */
	public function get() {
		return $this->current;
	}

}