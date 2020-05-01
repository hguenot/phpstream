<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\collectors;

use phpstream\util\Optional;

/**
 * Returns the last element of the list if exists.
 */
class LastCollector implements StreamCollector {
	/**
	 * @param iterable $iterable The list of elements
	 *
	 * @return Optional The last element of the list if exists.
	 */
	public function collect(iterable $iterable): Optional {
		$optional = Optional::absent();

		foreach ($iterable as $value) {
			$optional = Optional::ofNullable($value);
		}

		return $optional;
	}


}
