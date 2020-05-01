<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\collectors;

/**
 * Returns the number of elements in the list.
 */
class CountCollector implements StreamCollector {
	/**
	 * @param iterable $iterable The list of elements
	 *
	 * @return int The number of elements
	 */
	public function collect(iterable $iterable): int {
		$i = 0;

		foreach ($iterable as $value) {
			$i++;
		}

		return $i;
	}


}
