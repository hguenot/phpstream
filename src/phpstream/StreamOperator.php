<?php

/**
 * StreamOperator interface.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */
namespace phpstream;

/**
 * The StreamOperator is used to perform operation over each element of the Stream.
 * It can stop propagation of the element in the process and compute a new value.
 */
interface StreamOperator {

	/**
	 * Execute operator using the element and return a (new) element.
	 *
	 * @param mixed $value
	 *        	Element to process
	 * @param boolean $stopPropagation
	 *        	If set to true, next operator will not be found.
	 *        	
	 * @return mixed The (new) elemnt computed by the operator.
	 */
	public function execute($value, ?bool &$stopPropagation = null);

	/**
	 * Reset the operator to his initial state
	 */
	public function reset();
}
