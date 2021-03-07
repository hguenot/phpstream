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
 * A filter operator is used during the Stream Processor to check if element could continue the process or not
 *
 * @internal Internal API process.
 */
class FilterOperator implements StreamOperator {

	use OperatorInvokableTrait;

	/**
	 * Function used to filter each value during the streaming process.
	 * It should take one argument (value in the collection) and returns a boolean (`function(mixed $value): bool`)
	 * @var callable $_callable
	 * @internal
	 */
	private $_callable;

	/**
	 * Function object used to filter each value during the streaming process.
	 * The implemented UnaryFunction::apply should return a boolean value
	 * @var null|UnaryFunction $_function.
	 * @internal
	 */
	private ?UnaryFunction $_function;

	/**
	 * Construct a new operator with the filter function.
	 *
	 * If parameter is a callable, the function should take one argument (value in the collection) and returns a boolean
	 * (`function(mixed $value): bool`)
	 * If parameter is an UnaryFunction, the implemented UnaryFunction::apply should return a boolean value.
	 *
	 * The value will be kept only if the return value of the filter method is considered as `true`
	 *
	 * @param callable|UnaryFunction|FilterOperator $fn The filter function.
	 */
	public function __construct(callable|UnaryFunction|FilterOperator $fn) {
		[$this->_function, $this->_callable] = self::getFn($fn);
		$this->buildInvoker($this->_function, $this->_callable);
	}

	/**
	 * The method returns an `iterable` of the filtered elements using the filter function.
	 *
	 * @param iterable $values Values to filter.
	 *
	 * @return iterable The filtered values.
	 */
	public function execute(iterable $values): iterable {
		if ($this->_function !== null) {
			foreach ($values as $key => $value) {
				if ($this->_function->apply($value)) {
					yield $key => $value;
				}
			}
		} else {
			foreach ($values as $key => $value) {
				if (call_user_func($this->_callable, $value)) {
					yield $key => $value;
				}
			}
		}
	}

	/**
	 * @param callable|UnaryFunction|FilterOperator $fn The filter function.
	 *
	 * @return array [?UnaryFunction, ?callable]
	 *
	 * @ignore
	 * @internal
	 */
	private static function getFn(callable|UnaryFunction|FilterOperator $fn): array {
		if ($fn instanceof FilterOperator) {
			return [$fn->_function, $fn->_callable];
		} else if ($fn instanceof UnaryFunction) {
			return [$fn, null];
		} else {
			return [null, $fn];
		}
	}
}
