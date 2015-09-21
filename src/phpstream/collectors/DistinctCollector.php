<?php

/**
 * Distinct collector.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\collectors;

/**
 * Collects elements removing duplicates maintainig key/value pair (the first one).
 */
class DistinctCollector extends AbstractCollector {
	
	/** @var array The collected elements array */
	private $array;
	
	/**
	 * Collects elements removing duplicates maintainig key/value pair (the first one).
	 * 
	 * @param type $key Key value in the initial array (<em>array index</em>)
	 * @param type $value Value after processing
	 */
	public function collect($key, $value) {
		if (!in_array($value, $this->array, true))
			$this->array[$key] = $value;
	}

	/**
	 * Returns the collected elements array (associative array).
	 * 
	 * @return type
	 */
	public function get() {
		return $this->array;
	}

	/**
	 * Resets the collected elements array.
	 */
	public function reset() {
		$this->array = [];
	}

}