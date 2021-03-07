<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\operators;

use InvalidArgumentException;
use phpstream\functions\UnaryFunction;
use phpstream\traits\OperatorInvokableTrait;

/**
 * A peek operator is used to call a function over ech element of the collection without altering it (i.e.
 * display the actual value of the list.)
 */
class PeekOperator implements StreamOperator {

	use OperatorInvokableTrait;

	/**
	 * Function applied to each value during the streaming process.
	 * It should take one argument (value in the collection) and returns nothing (`function(mixed $value): void`)
	 * @var callable $_callable
	 * @internal
	 */
	private $_callable;

	/**
	 * Function object applied to each value during the streaming process.
	 * The implemented UnaryFunction::apply should return nothing
	 * @var UnaryFunction|null $_function.
	 * @internal
	 */
	private ?UnaryFunction $_function;

	/**
	 * Creates a new peek operator enclosing the given function.
	 *
	 * If parameter is a callable, the function should take one argument (value in the collection) and returns nothing
	 * (`function(mixed $value): void`).
	 * If parameter is an UnaryFunction, the implemented UnaryFunction::apply should return nothing.
	 *
	 * The potential returned value of the "peeking" function will be ignored.
	 *
	 * @param callable|UnaryFunction|PeekOperator $fn The "do nothing" function.
	 */
	public function __construct(callable|UnaryFunction|PeekOperator $fn) {
		[$this->_function, $this->_callable] = self::getFn($fn);
		$this->buildInvoker($this->_function, $this->_callable);
	}

	/**
	 * The method call the "picking" function and returns an `iterable` of the initial elements.
	 *
	 * @param iterable $values Values to process.
	 *
	 * @return iterable The processed values.
	 */
	public function execute(iterable $values): iterable {
		if ($this->_function !== null) {
			foreach ($values as $key => $value) {
				$this->_function->apply($value);
				yield $key => $value;
			}
		} else {
			foreach ($values as $key => $value) {
				call_user_func($this->_callable, $value);
				yield $key => $value;
			}
		}
	}

	/**
	 * @param callable|UnaryFunction|PeekOperator $fn The filter function.
	 *
	 * @return array [?UnaryFunction, ?callable]
	 *
	 * @ignore
	 * @internal
	 */
	private static function getFn(callable|UnaryFunction|PeekOperator $fn): array {
		if ($fn instanceof PeekOperator) {
			return [$fn->_function, $fn->_callable];
		} else if ($fn instanceof UnaryFunction) {
			return [$fn, null];
		} else {
			return [null, $fn];
		}
	}
}
