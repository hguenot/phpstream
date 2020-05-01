<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\collectors;

use phpstream\util\Optional;

/**
 * Returns the first element of the list if exists.
 */
class FirstCollector implements StreamCollector {
	/**
	 * @param iterable $iterable The list of elements
	 *
	 * @return Optional The first element of the list if exists.
	 */
	public function collect(iterable $iterable): Optional {
		$optional = Optional::absent();
		$found = false;

		foreach ($iterable as $value) {
			if (!$found) {
				$optional = Optional::ofNullable($value);
				$found = true;
			}
		}

		return $optional;
	}


}
