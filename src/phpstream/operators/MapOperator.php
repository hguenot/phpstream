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
 * Converts input value into another value
 */
class MapOperator implements StreamOperator {

	use OperatorInvokableTrait;

	/**
	 * Function used to map each value during the streaming process.
	 * It should take one argument (value in the collection) and returns any type of value (`function(mixed $value): mixed`)
	 * @var callable $_callable
	 * @internal
	 */
	private $_callable;

	/**
	 * Function object used to map each value during the streaming process.
	 * The implemented UnaryFunction::apply should return any type of value
	 * @var null|UnaryFunction $_function.
	 * @internal
	 */
	private ?UnaryFunction $_function;

	/**
	 * Creates a new MapOperator using the given function.
	 *
	 * If parameter is a callable, the function should take one argument (value in the collection) and returns another value
	 * (`function(mixed $value): mixed`).
	 * If parameter is an UnaryFunction, the implemented UnaryFunction::apply should return the mapped value.
	 * If parameter is a string, the process will use the corresponding property (or method if property does not exist) for getting
	 * the mapped value.
	 *
	 * The returned value will replace the current value for the rest of process.
	 *
	 * @param callable|UnaryFunction|string|MapOperator $fn Mapping function. If function is a string, use object property or method as mapper.
	 */
	public function __construct(callable|UnaryFunction|string|MapOperator $fn) {
		[$this->_function, $this->_callable] = self::getFn($fn);
		$this->buildInvoker($this->_function, $this->_callable);
	}

	/**
	 * The method returns an `iterable` of the converted elements using the mapping function.
	 *
	 * @param iterable $values Values to filter.
	 *
	 * @return iterable The mapped values.
	 */
	public function execute(iterable $values): iterable {
		if ($this->_function !== null) {
			foreach ($values as $key => $value) {
				yield $key => $this->_function->apply($value);
			}
		} else {
			foreach ($values as $key => $value) {
				yield $key => call_user_func($this->_callable, $value);
			}
		}
	}

	/**
	 * @param callable|UnaryFunction|MapOperator|string $fn Mapping function. If function is a string, use object property or method as mapper
	 *
	 * @return array [?UnaryFunction, ?callable]
	 *
	 * @ignore
	 * @internal
	 */
	private static function getFn(callable|UnaryFunction|MapOperator|string $fn): array {
		if ($fn instanceof MapOperator) {
			return [$fn->_function, $fn->_callable];
		} else if ($fn instanceof UnaryFunction) {
			return [$fn, null];
		} else if (is_callable($fn)) {
			return [null, $fn];
		} else {
			return [null, function ($obj) use ($fn) {
				if (!is_object($obj))
					throw new InvalidArgumentException(gettype($obj) . ' is not an object, ' . $fn . ' could not be applied.');
				else if (property_exists($obj, $fn))
					return $obj->{$fn};
				else if (method_exists($obj, $fn))
					return call_user_func([$obj, $fn]);
				else
					throw new InvalidArgumentException($fn . ' is not a property or a method of ' . get_class($obj));
			}];
		}
	}

}
