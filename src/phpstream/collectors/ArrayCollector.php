<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\collectors;

/**
 * Collects all items resulting of the Stream Process discarding or not the key/value pair association.
 */
class ArrayCollector implements StreamCollector {
	/**
	 * Flag indicating if keys are kept when collecting data
	 * @var boolean $useKeys
	 */
	private $useKeys;

	/**
	 * ListCollector constructor.
	 *
	 * @param bool $useKeys Flag indicating if keys are kept when collecting data
	 */
	public function __construct(bool $useKeys = false) {
		$this->useKeys = $useKeys;
	}

	/**
	 * Collects all items resulting of the Stream Process discarding the key/value pair.
	 *
	 * @param iterable $iterable Values list
	 *
	 * @return array the array values of the iterable content
	 */
	public function collect(iterable $iterable): array {
		return iterable_to_array($iterable, $this->useKeys);
	}
}
