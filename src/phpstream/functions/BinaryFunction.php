<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\functions;

/**
 * Represents a function that accepts one arguments and produces a result.
 */
interface BinaryFunction {

	/**
	 * Applies this function to the given arguments.
	 *
	 * @param mixed $first The first argument.
	 * @param mixed $second The second argument.
	 *
	 * @return mixed Result value of the function
	 */
	public function apply($first, $second);
}
