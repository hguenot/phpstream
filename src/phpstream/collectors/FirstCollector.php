<?php

/**
 * First element collector.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */
namespace phpstream\collectors;

use phpstream\util\Optional;

/**
 * Collects the first element of the stream processing.
 */
class FirstCollector extends AbstractCollector {

	/** @var Optional Collected element ({@see Optional::absent()} if none). */
	private $optional;

	/**
	 * Collects the first element of the stream processing.
	 *
	 * @param mixed $key
	 *        	Key value in the initial array (<em>array index</em>)
	 * @param mixed $value
	 *        	Value after processing
	 */
	public function collect($key, $value) {
		if ($this->optional->isEmpty()) {
			$this->optional = Optional::of($value);
		}
	}

	/**
	 * Returns the collected element.
	 *
	 * @return Optional The collected element {@see Optional::absent()} if none.
	 */
	public function get() {
		return $this->optional;
	}

	/**
	 * Remove the collected element.
	 */
	public function reset() {
		$this->optional = Optional::absent();
	}
}
