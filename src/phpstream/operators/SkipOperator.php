<?php

/**
 * SkipOperator definition.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */
namespace phpstream\operators;

/**
 * LimitOperator restricts the number of returns elements by skipping the first ones.
 */
class SkipOperator extends AbstractOperator {

	/** @var int Number of results to skip. */
	private $limit;

	/** @var int Number of elements already skipped. */
	private $current;

	/**
	 * Construct a new LimitOperator.
	 *
	 * @param int $limit
	 *        	Number of results to skip.
	 */
	public function __construct($limit) {
		parent::__construct();
		$this->limit = $limit;
	}

	/**
	 * Accept value if skip limit is reached.
	 *
	 * Until limit has been reached, `$stopPropagation` is set to `TRUE`. Returns the collected element instead.
	 *
	 * @param mixed $value
	 *        	Collected element.
	 * @param boolean $stopPropagation
	 *        	Flag indicating if limit has been reached.
	 *        	
	 * @return mixed Collected value if limit has not been reached.
	 */
	public function execute($value, bool &$stopPropagation = null) {
		if ($this->current < $this->limit && $stopPropagation !== true) {
			$this->current++;
			$stopPropagation = true;
		}
		return $value;
	}

	/**
	 * Reset number of elements already collected to zero.
	 */
	public function reset() {
		$this->current = 0;
	}
}
