<?php

/**
 * Stream collector interface.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream;

/**
 * StreamCollector is used during the Stream process for collecting valid computed value during the process.
 */
interface StreamCollector {
	
	/**
	 * Method called at the beginning of the process to reset collector at his initial state.
	 */
	public function reset();
	
	/**
	 * Method calls at end of the Stream process when a value could be collected.
	 * 
	 * @param mixed $key Key value in the initial array (<em>array index</em>)
	 * @param mixed $value Value after processing
	 */
	public function collect($key, $value);
	
	/**
	 * Returns he result of the collected values.
	 * 
	 * @return mixed The result of the collected values.
	 */
	public function get();
	
}
