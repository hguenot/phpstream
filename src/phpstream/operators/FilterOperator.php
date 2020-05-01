<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\operators;

use InvalidArgumentException;
use phpstream\functions\UnaryFunction;

/**
 * A filter operator is used during the Stream Processor to check if element could continue the process or not
 *
 * @internal Internal API process.
 */
class FilterOperator implements StreamOperator {
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
	 * @var UnaryFunction $_function.
	 * @internal
	 */
	private $_function;

	/**
	 * Construct a new operator with the filter function.
	 *
	 * If parameter is a callable, the function should take one argument (value in the collection) and returns a boolean
	 * (`function(mixed $value): bool`)
	 * If parameter is an UnaryFunction, the implemented UnaryFunction::apply should return a boolean value.
	 *
	 * The value will be kept only if the return value of the filter method is considered as `true`
	 *
	 * @param callable|UnaryFunction $fn The filter function.
	 *
	 * @throws InvalidArgumentException If parameter is not callable or a UnaryFunction instance.
	 */
	public function __construct($fn) {
		[$this->_function, $this->_callable] = self::getFn($fn);
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
	public static function getFn($fn) {
		if ($fn instanceof FilterOperator) {
			return [$fn->_function, $fn->_callable];
		} else if ($fn instanceof UnaryFunction) {
			return [$fn, null];
		} else if (is_callable($fn)) {
			return [null, $fn];
		} else {
			throw new InvalidArgumentException('Parameter must be callable or UnaryFunction.');
		}
	}
}
