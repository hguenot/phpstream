<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\operators;

use InvalidArgumentException;

/**
 * LimitOperator restricts the number of returns elements by skipping the first ones.
 */
class SkipOperator implements StreamOperator {

	/**
	 * Number of results to skip.
	 *
	 * @var int $_limit
	 */
	private $_limit;

	/**
	 * Construct a new LimitOperator.
	 *
	 * @param int $limit Number of results to skip.
	 */
	public function __construct(int $limit) {
		if ($limit < 0) {
			throw new InvalidArgumentException('Limit must be a positive integer.');
		}
		$this->_limit = $limit;
	}

	/**
	 * The method returns an `iterable` of the processed elements.
	 *
	 * @param iterable $values Values to process.
	 *
	 * @return iterable The processed values.
	 */
	public function execute(iterable $values): iterable {
		$current = 0;

		foreach ($values as $key => $value) {
			if ($current >= $this->_limit) {
				yield $key => $value;
			}
			$current++;
		}
	}
}
