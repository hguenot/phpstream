<?php

/**
 * Map collector.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\collectors;

/**
 * Collects all items resulting of the Stream Process conserving the key/value pair association.
 */
class MapCollector extends AbstractCollector {
	
	/** @var array Collected elements. */
	private $array;
	
	/** @var callable */
	private $keyMapper;
	
	/**
	 * MapCollector constructor.
	 * @param callable $keyMapper
	 */
	public function __construct(callable $keyMapper = null) {
		parent::__construct();
		$this->keyMapper = $keyMapper
			? $keyMapper
			: function($key, $value) {
				return $key;
			};
	}
	
	/**
	 * Collects all items resulting of the Stream Process conserving the key/value pair.
	 * 
	 * @param mixed $key Key value in the initial array (<em>array index</em>)
	 * @param mixed $value Value after processing
	 */
	public function collect($key, $value) {
		$this->array[call_user_func($this->keyMapper, $key, $value)] = $value;
	}

	/** 
	 * Returns the collected elements.
	 * 
	 * @return array The collected elements.
	 */
	public function get() {
		return $this->array;
	}

	/**
	 * Resetting the collected elements array.
	 */
	public function reset() {
		$this->array = [];
	}

}
