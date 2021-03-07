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
interface UnaryFunction {

	/**
	 * Applies this function to the given arguments.
	 *
	 * @param mixed $value
	 *        	The argument.
	 *        	
	 * @return mixed Result value of the function
	 */
	public function apply(mixed $value): mixed;
}
