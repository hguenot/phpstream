<?php

/**
 * BiFunction definition.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\functions;

/**
 * Represents a function that accepts two arguments and produces a result.
 */
interface BiFunction {
	
	/**
	 * Applies this function to the given arguments.
	 * 
	 * @param type $first The first argument.
	 * @param type $second The second argument.
	 * 
	 * @return mixed Result value of the function
	 */
	public function apply($first, $second);
	
}
