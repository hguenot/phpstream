<?php

/**
 * List collector.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\collectors;

/**
 * Collects all items resulting of the Stream Process discarding the key/value pair association.
 */
class ListCollector extends AbstractCollector {
	
	/** @var array Collected elements. */
	private $array;
	
	/**
	 * Collects all items resulting of the Stream Process discarding the key/value pair.
	 * 
	 * @param mixed $key Key value in the initial array (<em>array index</em>)
	 * @param mixed $value Value after processing
	 */
	public function collect($key, $value) {
		$this->array[] = $value;
	}

	/** 
	 * Returns the collected elements.
	 * 
	 * @return array The collected elements.
	 */
	public function get() {
		return $this->array;
	}

	/**
	 * Resetting the collected elements array.
	 */
	public function reset() {
		$this->array = [];
	}

}
