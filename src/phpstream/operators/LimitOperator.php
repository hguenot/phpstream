<?php

/**
 * LimitOperator definition.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\operators;

/**
 * LimitOperator restricts the number of returns elements by ignoring the last ones.
 */
class LimitOperator extends AbstractOperator {
	
	/** @var int Maximum number of results to collect. */
	private $limit;
	
	/** @var int Number of elements already collected. */
	private $current;
	
	/**
	 * Construct a new LimitOperator.
	 * 
	 * @param int $limit Maximum number of results to collect.
	 */ 
	public function __construct($limit) {
		parent::__construct();
		$this->limit = $limit;
	}
	
	/**
	 * Accept value if limit is not reached.
	 * 
	 * When limit has been reached, `$stopPropagation` is set to `TRUE`. Returns the collected element instead.
	 * 
	 * @param mixed $value Collected element.
	 * @param boolean $stopPropagation Flag indicating if limit has been reached.
	 * 
	 * @return mixed Collected value if limit has not been reached.
	 */
	public function execute($value, bool &$stopPropagation = null) {
		if ($this->current < $this->limit){
			$this->current++;
			return $value;
		}
		$stopPropagation = true;
		return null;
	}

	/**
	 * Reset number of elements already collected to zero.
	 */
	public function reset() {
		$this->current = 0;
	}

}
