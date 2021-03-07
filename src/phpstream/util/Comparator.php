<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\util;

use SebastianBergmann\Type\CallableType;

/**
 * Interface for comparison function.
 */
interface Comparator {

	/**
	 * Compares its two arguments for order.
	 * Returns a negative integer, zero, or a positive integer as the first argument is less than,
	 * equal to, or greater than the second.
	 *
	 * @param mixed $o1
	 * @param mixed $o2
	 *
	 * @return int
	 */
	public function compare(mixed $o1, mixed $o2): int;

}
