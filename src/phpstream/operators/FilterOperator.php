<?php

/**
 * FilterOperator definition.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\operators;

use phpstream\functions\UnaryFunction;

/**
 * A filter operator is used during the Stream Processor to chack if element could continue the process or not
 * 
 * @internal Internal API process.
 */
class FilterOperator extends AbstractOperator {
	
	/** @var callable Function to call. */
	private $callable;
	
	/** @var UnaryFunction Function to apply. */
	private $func;
	
	/**
	 * Construct a new operator with the filter function. 
	 * 
	 * The filter function must return `TRUE` (or something that PHP evaluates to `TRUE`) to lets the elements continue 
	 * the process.
	 * 
	 * @param callable|UnaryFunction $fn The filter function.
	 * 
	 * @throws \InvalidArgumentException If parameter is not callable or a UnaryFunction instance.
	 */
	public function __construct($fn) {
		parent::__construct();
		if ($fn instanceof UnaryFunction) {
			$this->func = $fn;
		} else if (is_callable($fn)) {
			$this->callable = $fn;
		} else {
			throw new \InvalidArgumentException('Parameter must be callable or UnaryFunction.');
		}
	}
	
	/**
	 * Check if the element match the filter process. 
	 * 
	 * The method returns the element and set `$stopProcessing` to `TRUE` if it match; it returns `null` and set 
	 * `$stopProcessing` to `FALSE` if it doesn't.
	 * 
	 * @param mixed $value Element to test.
	 * @param boolean $stopPropagation Boolean used to stop element processing.
	 * 
	 * @return mixed The given element or `null`.
	 * 
	 * @throws \LogicException If it was called but $stopPropagation already set to `FALSE`
	 */
	public function execute($value, ?bool &$stopPropagation = null) {
		if ($stopPropagation !== true) {
			$stopPropagation = $this->func !== null ?
				!$this->func->apply($value) : 
				!call_user_func($this->callable, $value);
		} else {
			throw new \LogicException('Propagation has been stopped before this call.');
		}
		
		return $stopPropagation === true ? null : $value;
	}

}
