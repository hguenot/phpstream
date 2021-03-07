<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\collectors;

/**
 * Collects all items resulting of the Stream Process conserving the key/value pair association.
 */
class MapCollector implements StreamCollector {

	/**
	 * Callable function to be applied on each key when collecting data
	 * @var boolean $keyMapper
	 */
	private $keyMapper;

	/**
	 * Callable function to be applied on each value when collecting data
	 * @var boolean $valueMapper
	 */
	private $valueMapper;

	/**
	 * MapCollector constructor.
	 *
	 * @param callable|null $keyMapper Key mapper. If sets to null, use the original key.
	 * @param callable|null $valueMapper Value mapper. If sets to null, use the original value.
	 */
	public function __construct(callable $keyMapper = null, callable $valueMapper = null) {
		$this->keyMapper = $keyMapper ? $keyMapper : function ($key, $value) {
			return $key;
		};

		$this->valueMapper = $valueMapper ? $valueMapper : function ($key, $value) {
			return $value;
		};
	}

	public function collect(iterable $values): array {
		$keyMapper = $this->keyMapper;
		$valueMapper = $this->valueMapper;
		$res = [];
		foreach ($values as $key => $value) {
			$res[$keyMapper($key, $value)] = $valueMapper($key, $value);
		}

		return $res;
	}
}
