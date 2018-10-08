<?php

/**
 * PeekOperator definition.
 * 
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @link https://github.com/hguenot/phpstream#readme Readme
 */

namespace phpstream\operators;

use phpstream\functions\UnaryFunction;

/**
 * A peek operator is used to call a function over ech element of the collection without altering it (i.e. display
 * the acual value of the list.) 
 */
class PeekOperator extends AbstractOperator {
	
	/** @var callable Function to call. */
	private $callable;
	
	/** @var UnaryFunction Function to apply. */
	private $func;
	
	/**
	 * Creates a new peek operator enclosing the given function.
	 * 
	 * The function mtakes one argument (the value) and returns nothing (the return result will be ignore).
	 * 
	 * @param callable|UnaryFunction $fn The "do nothing" function.
	 * 
	 * @throws \InvalidArgumentException If function has a bad type.
	 */
	public function __construct($fn) {
		parent::__construct();
		if ($fn instanceof UnaryFunction) {
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
                    throw new \InvalidArgumentException($fn . ' is not a property or a method of '
                            . (is_object($obj) ? get_class($obj) : gettype($obj)));
		    };
		} else {
			throw new \InvalidArgumentException('Parameter must be callable or UnaryFunction.');
		}
	}
	
	/**
	 * Apply the function over each element of the function.
	 * 
	 * @param mixed $value The collected value.
	 * @param boolean $stopPropagation Boolean used to stop element processing.
	 * 
	 * @return mixed The collected value.
	 * 
	 * @throws \LogicException If it was called but $stopPropagation already set to `FALSE`.
	 */
	public function execute($value, bool &$stopPropagation = null) {
		if ($stopPropagation !== true) {
			$this->func !== null ?
				$this->func->apply($value) : 
				call_user_func($this->callable, $value);
			return $value;
		}
		throw new \LogicException('Propagation has been stopped before this call.');
	}

}
