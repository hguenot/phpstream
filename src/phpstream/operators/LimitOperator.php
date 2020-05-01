<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\operators;

use InvalidArgumentException;

/**
 * LimitOperator restricts the number of returns elements by ignoring the last ones.
 */
class LimitOperator implements StreamOperator {

	/**
	 * Maximum number of results to collect.
	 * @var int $_limit
	 */
	private $_limit;

	/**
	 * Construct a new LimitOperator.
	 *
	 * @param int $limit Maximum number of results to collect.
	 *
	 * @throws InvalidArgumentException If `$limit` is a negative number
	 */
	public function __construct(int $limit) {
		if ($limit < 0) {
			throw new InvalidArgumentException('Limit must be a positive integer.');
		}
		$this->_limit = $limit;
	}

	/**
	 * The method returns an `iterable` of the limited number of elements.
	 *
	 * @param iterable $values Values to process.
	 *
	 * @return iterable The limited number of elements.
	 */
	public function execute(iterable $values): iterable {
		$current = 0;

		foreach ($values as $key => $value) {
			if ($current < $this->_limit) {
				$current++;
				yield $key => $value;
			}
		}
	}
}
