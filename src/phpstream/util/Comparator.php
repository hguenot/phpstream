<?php

/**
 * Comparator interface.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\util;

/**
 * Interface for comparison function.
 */
interface Comparator {
	
	/**
	 * Compares its two arguments for order. 
	 * Returns a negative integer, zero, or a positive integer as the first argument is less than, 
	 * equal to, or greater than the second.
	 * 
	 * @param type $o1
	 * @param type $o2
	 * 
	 * @return int
	 */
	public function compare($o1, $o2);
	
}
