<?php

/**
 * MapOperator definition.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */
namespace phpstream\operators;

use phpstream\functions\UnaryFunction;

/**
 * Converts input value into another value
 */
class MapOperator extends AbstractOperator {

	/** @var callable Function to call. */
	private $callable;

	/** @var UnaryFunction Function to apply. */
	private $func;

	/**
	 * Creates a new MapOperator using the given function.
	 *
	 * Mapping function must take the vcollected value and return a new value. This new value will replace
	 * the current value for the rest of process.
	 *
	 * @param callable|UnaryFunction $fn
	 *        	Mapping function.
	 *        	
	 * @throws \InvalidArgumentException If parameter type is not valid.
	 */
	public function __construct($fn) {
		parent::__construct();
		if ($fn instanceof MapOperator) {
			$this->func = $fn->func;
			$this->callable = $fn->callable;
		} else if ($fn instanceof UnaryFunction) {
			$this->func = $fn;
		} else if (is_callable($fn)) {
			$this->callable = $fn;
		} else if (is_string($fn)) {
			$this->callable = function ($obj) use ($fn) {
				if (property_exists($obj, $fn))
					return $obj->{$fn};
				else if (method_exists($obj, $fn))
					return call_user_func([$obj, $fn]);
				else
					throw new \InvalidArgumentException($fn . ' is not a property or a method of ' . (is_object($obj) ? get_class($obj) : gettype($obj)));
			};
		} else {
			throw new \InvalidArgumentException('Parameter must be callable or UnaryFunction.');
		}
	}

	/**
	 * The method returns the element converter using the mapping function.
	 *
	 * @param mixed $value
	 *        	Element to convert.
	 * @param boolean $stopPropagation
	 *        	Boolean used to stop element processing.
	 *        	
	 * @return mixed The converted element.
	 *        
	 * @throws \LogicException If it was called but $stopPropagation already set to `FALSE`.
	 */
	public function execute($value, ?bool &$stopPropagation = null) {
		if ($stopPropagation !== true) {
			return $this->func !== null ? $this->func->apply($value) : call_user_func($this->callable, $value);
		}
		throw new \LogicException('Propagation has been stopped before this call.');
	}
}
