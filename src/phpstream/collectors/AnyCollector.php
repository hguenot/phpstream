<?php

/**
 * Any collector.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\collectors;

use phpstream\util\Optional;

/**
 * The any collector is used to return oen of the resulting elements after stream processing.
 */
class AnyCollector extends AbstractCollector {
	
	/** @var array Collected elements array. */
	private $values;
	
	/**
	 * Method stores all valid values in resulting array.
	 * 
	 * @param mixed $key Key value in the initial array (<em>array index</em>)
	 * @param mixed $value Value after processing
	 * 
	 * @see \phpstream\StreamCollector::collect($key, $value);
	 */
	public function collect($key, $value) {
		$this->values[] = $value;
	}

	/**
	 * Return one of the collected element.
	 * 
	 * @return Optional Any collected element or {@see Optional::absent()} if no element has been collected.
	 */
	public function get() {
		if (empty($this->values))
			return Optional::absent ();
		
		return Optional::of(
			$this->values[intval(rand(0, count($this->values) -1))]
		);
	}

	/**
	 * Clear the collected elements array.
	 */
	public function reset() {
		$this->values = [];
		srand();
	}
}
