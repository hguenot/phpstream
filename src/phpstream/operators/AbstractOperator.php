<?php

/**
 * AbstractOperator definition.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */
namespace phpstream\operators;

use phpstream\StreamOperator;

/**
 * Base implementation of a StreamOperator.
 * It calls reset method {@link \phpstream\StreamOperator::reset()} in it's constructor.
 *
 * @internal Internal API process.
 */
abstract class AbstractOperator implements StreamOperator {

	/**
	 *
	 * @ignore
	 */
	public function __construct() {
		$this->reset();
	}

	/**
	 * Empty <tt>reset</tt> method implementation.
	 */
	public function reset() {}
}
