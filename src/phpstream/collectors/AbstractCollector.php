<?php

/**
 * Abstract collector implementation.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */
namespace phpstream\collectors;

use phpstream\StreamCollector;

/**
 * Abstract collector implementation.
 * Constructor calls the reset function.
 */
abstract class AbstractCollector implements StreamCollector {

	/**
	 * Default constructor which calls the reset function.
	 *
	 * @ignore
	 */
	public function __construct() {
		$this->reset();
	}
}
