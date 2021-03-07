<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\util;

use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use phpstream\functions\UnaryFunction;
use phpstream\operators\MapOperator;

/**
 * A container object which may or may not contain a non-null value.
 *
 * If a value is present, isEmpty() will return `FALSE` and get() will return the value.
 */
abstract class Optional {

	/**
	 * Construct a new Optional instance
	 */
	protected function __construct() {}

	/**
	 * Returns an empty Optional instance.
	 *
	 * @return Optional An empty Optional instance.
	 */
	public static function absent(): Optional {
		return _optional\Absent::instance();
	}

	/**
	 * Returns an Optional instance containing the given non-null reference.
	 *
	 * @param mixed $reference Non-null reference to store.
	 *        	
	 * @return Optional An Optional instance containing the given reference.
	 *        
	 * @throws InvalidArgumentException if given reference is null.
	 */
	public static function of(mixed $reference): Optional {
		if ($reference instanceof Optional)
			return $reference;

		return new _optional\Present(static::checkNotNull($reference));
	}

	/**
	 * Returns an empty Optional instance if given reference is null, an Optional instance containing
	 * the given reference otherwise.
	 *
	 * @param mixed $reference Reference to store.
	 *        	
	 * @return Optional An empty Optional instance or an Optional instance containing the given reference.
	 */
	public static function ofNullable(mixed $reference): Optional {
		if ($reference instanceof Optional)
			return $reference;

		return $reference === null ? static::absent() : new _optional\Present($reference);
	}

	/**
	 * Returns the Optional instance emptiness.
	 *
	 * @return boolean `TRUE` if the current Optional instance is empty, `FALSE` otherwise.
	 */
	public abstract function isEmpty(): bool;

	/**
	 * Returns the Optional instance completeness.
	 *
	 * @return boolean `TRUE` if the current Optional instance is not empty (contains any value), `FALSE` otherwise.
	 */
	public function isNotEmpty(): bool {
		return !$this->isEmpty();
	}

	/**
	 * Returns the contained reference for a non empty Optional instance, an Exception otherwise.
	 *
	 * @return mixed The contained reference for a non empty Optional instance.
	 *        
	 * @throws BadMethodCallException If the current Optional instance is empty.
	 */
	public abstract function get(): mixed;

	/**
	 * Applying mapper on contained reference and return a new Optional element.
	 *
	 * @param callable|UnaryFunction|MapOperator|string $mapper Mapping function. If function is a string, use object property or method as mapper.
	 *
	 * @return Optional Optional instance containing mapped value or Absent.
	 */
	public abstract function map(callable|UnaryFunction|MapOperator|string $mapper): Optional;

	/**
	 * Returns the contained reference for a non empty Optional instance, the not-null default value otherwise.
	 *
	 * @param mixed $defaultValue The default value.
	 *        	
	 * @return mixed The contained reference for a non empty Optional instance or the default value.
	 *        
	 * @throws InvalidArgumentException if default value is null.
	 */
	public abstract function orElse(mixed $defaultValue): mixed;

	/**
	 * Returns the contained reference for a non empty Optional instance, throw given exception otherwise.
	 *
	 * @param Exception $ex The exception to throw if empty
	 *        	
	 * @return mixed The contained reference for a non empty Optional instance or the default value.
	 *        
	 * @throws Exception if default value is null.
	 */
	public abstract function orElseThrow(Exception $ex): mixed;

	/**
	 * Returns the contained reference for a non empty Optional instance, null otherwise.
	 *
	 * @return mixed The contained reference for a non empty Optional instance or `null`.
	 */
	public abstract function orNull(): mixed;

	/**
	 * Checks if current instance and given Optional instance contains same object.
	 *
	 * @param mixed $object Other object
	 *
	 * @return boolean `TRUE` if current instance and given Optional instance contains same object, `FALSE` otherwise.
	 *        
	 * @ignore
	 */
	public abstract function equals(mixed $object): bool;

	/**
	 * Make sure the passed reference is not null.
	 *
	 * @param mixed $reference Reference to test.
	 * @param string|null $message Error message if reference is null
	 *        	
	 * @return mixed
	 *
	 * @throws InvalidArgumentException If reference is null.
	 */
	protected static function checkNotNull(mixed $reference, ?string $message = null): mixed {
		if ($message === null) {
			$message = "Disallowed null in reference found.";
		}

		if ($reference === null) {
			throw new InvalidArgumentException($message);
		}
		return $reference;
	}
}


