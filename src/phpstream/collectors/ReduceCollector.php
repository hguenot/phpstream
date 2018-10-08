<?php

/**
 * Reduce collector.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */
namespace phpstream\collectors;

use phpstream\functions\BiFunction;
use phpstream\util\Optional;

/**
 * Reduce collector must be used to reduce array to a single value.
 */
class ReduceCollector extends AbstractCollector {

	/** @var Optional Current collected value. */
	private $current;

	/** @var callable Reduce function to call. */
	private $callable;

	/** @var BiFunction Reduce function to apply. */
	private $func;

	/**
	 * Construct a new Reduce Collector giving the reduce function.
	 *
	 * @param callable|BiFunction $fn
	 *        	Reduce function.
	 *        	
	 * @throws \InvalidArgumentException If function is not callable or BiFunction
	 */
	public function __construct($fn) {
		parent::__construct();
		if ($fn instanceof BiFunction) {
			$this->func = $fn;
		} else if (is_callable($fn)) {
			$this->callable = $fn;
		} else {
			throw new \InvalidArgumentException('Parameter must be callable or BiFunction.');
		}
	}

	/**
	 * Collects item resulting of the Stream Process and call the reduce method with previous reduced value and
	 * current item.
	 *
	 * @param mixed $key
	 *        	Key value in the initial array (<em>array index</em>)
	 * @param mixed $value
	 *        	Value after processing
	 */
	public function collect($key, $value) {
		if ($this->current->isEmpty())
			$this->current = Optional::of($value);
		else
			$this->current = Optional::of($this->func !== null ? $this->func->apply($this->current->get(), $value) : call_user_func($this->callable, $this->current->get(), $value));
	}

	/**
	 * Removes collected value.
	 */
	public function reset() {
		$this->current = Optional::absent();
	}

	/**
	 * Returns the reduces value.
	 *
	 * @return Optional The reduce value.
	 */
	public function get() {
		return $this->current;
	}
}
