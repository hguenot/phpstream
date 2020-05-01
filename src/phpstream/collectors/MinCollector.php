<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\collectors;

use InvalidArgumentException;
use phpstream\util\Comparator;
use phpstream\util\Optional;

/**
 * Collects the min collected element using comparing function.
 */
class MinCollector implements StreamCollector {

	/**
	 * Comparator function used to find the max value during the collecting process.
	 * It should take 2 arguments and must return an integer less than, equal to, or greater than zero if the first argument
	 * is considered to be respectively less than, equal to, or greater than the second
	 * @var callable $_callable
	 * @internal
	 */
	private $_callable;

	/**
	 * Comparator object used to find the max value during the collecting process.
	 * @var Comparator $_comparator.
	 * @internal
	 */
	private $_comparator;

	/**
	 * Create a new MinCollector using specific comparator function.
	 *
	 * If comparator function is not set, use native comparison.
	 * If parameter is a callable function, it should take 2 arguments and must return an integer less than, equal to,
	 * or greater than zero if the first argument is considered to be respectively less than, equal to, or greater than the second
	 *
	 * @param callable|Comparator $cmp Comparator method / object.
	 *        	
	 * @throws InvalidArgumentException If parameter is not callable or instance of Comparator.
	 */
	public function __construct($cmp = null) {
		if ($cmp === null) {
			$this->_callable = function ($o1, $o2) {
				if ($o1 == $o2)
					return 0;
				return $o1 < $o2 ? -1 : 1;
			};
		} else if ($cmp instanceof Comparator) {
			$this->_comparator = $cmp;
		} else if (is_callable($cmp)) {
			$this->_callable = $cmp;
		} else {
			throw new InvalidArgumentException('Parameter must be callable or Comparator.');
		}
	}

	/**
	 * The method collects the min value of an `iterable` processed by the Stream API.
	 *
	 * @param iterable $values Values to collect.
	 *
	 * @return Optional The min collected value if exists.
	 */
	public function collect(iterable $values): Optional {
		/* @var Optional $current */
		$current = null;
		if ($this->_comparator !== null) {
			foreach ($values as $value) {
				if ($current === null || $this->_comparator->compare($value, $current->orNull()) < 0) {
					$current = Optional::ofNullable($value);
				}
			}
		} else {
			foreach ($values as $value) {
				if ($current === null || call_user_func($this->_callable, $value, $current->orNull()) < 0) {
					$current = Optional::ofNullable($value);
				}
			}
		}

		return $current == null ? Optional::absent() : $current;
	}
}
