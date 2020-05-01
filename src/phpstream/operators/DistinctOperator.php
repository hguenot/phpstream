<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\operators;

/**
 * Removes duplicate entries in the list.
 */
class DistinctOperator implements StreamOperator {

	public function execute(iterable $values): iterable {
		$s = [];

		foreach ($values as $key => $value) {
			if (!in_array($value,$s)) {
				$s[] = $value;
				yield $key => $value;
			}
		}
	}

}