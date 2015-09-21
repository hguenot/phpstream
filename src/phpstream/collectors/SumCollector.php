<?php

/**
 * Sum collector.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\collectors;

/**
 * Callect values and compute the sum.
 */
class SumCollector extends AbstractCollector {
	
	/** @var int Sum of the collected elements. */
	private $sum;
	
	/**
	 * Computes the sum of the collected items.
	 * 
	 * @param type $key Key value in the initial array (<em>array index</em>)
	 * @param type $value Value after processing
	 */
	public function collect($key, $value) {
		$this->sum += intval($value);
	}

	/**
	 * Reset the sum to zero.
	 */
	public function reset() {
		$this->sum = 0;
	}
	
	/**
	 * Returns the sum of the collected values.
	 * 
	 * @return int The sum of the collected values.
	 */
	public function get() {
		return $this->sum;
	}

}