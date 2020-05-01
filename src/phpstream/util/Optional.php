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
	public static function absent() {
		return Absent::instance();
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
	public static function of($reference) {
		if ($reference instanceof Optional)
			return $reference;

		return new Present(static::checkNotNull($reference));
	}

	/**
	 * Returns an empty Optional instance if given reference is null, an Optional instance containing
	 * the given reference otherwise.
	 *
	 * @param mixed $reference Reference to store.
	 *        	
	 * @return Optional An empty Optional instance or an Optional instance containing the given reference.
	 */
	public static function ofNullable($reference) {
		if ($reference instanceof Optional)
			return $reference;

		return $reference === null ? static::absent() : new Present($reference);
	}

	/**
	 * Returns the Optional instance emptiness.
	 *
	 * @return boolean `TRUE` if the current Optional instance is empty, `FALSE` otherwise.
	 */
	public abstract function isEmpty();

	/**
	 * Returns the Optional instance completeness.
	 *
	 * @return boolean `TRUE` if the current Optional instance is not empty (contains any value), `FALSE` otherwise.
	 */
	public function isNotEmpty() {
		return !$this->isEmpty();
	}

	/**
	 * Returns the contained reference for a non empty Optional instance, an Exception otherwise.
	 *
	 * @return mixed The contained reference for a non empty Optional instance.
	 *        
	 * @throws BadMethodCallException If the current Optional instance is empty.
	 */
	public abstract function get();

	/**
	 * Applying mapper on contained reference and return a new Optional element.
	 *
	 * @param callable|UnaryFunction|MapOperator $mapper Mapping function. If function is a string, use object property or method as mapper.
	 *
	 * @return Optional Optional instance containing mapped value or Absent.
	 */
	public abstract function map($mapper);

	/**
	 * Returns the contained reference for a non empty Optional instance, the not-null default value otherwise.
	 *
	 * @param mixed $defaultValue The default value.
	 *        	
	 * @return mixed The contained reference for a non empty Optional instance or the default value.
	 *        
	 * @throws InvalidArgumentException if default value is null.
	 */
	public abstract function orElse($defaultValue);

	/**
	 * Returns the contained reference for a non empty Optional instance, throw given exception otherwise.
	 *
	 * @param Exception $ex The exception to throw if empty
	 *        	
	 * @return mixed The contained reference for a non empty Optional instance or the default value.
	 *        
	 * @throws Exception if default value is null.
	 */
	public abstract function orElseThrow(Exception $ex);

	/**
	 * Returns the contained reference for a non empty Optional instance, null otherwise.
	 *
	 * @return mixed|null The contained reference for a non empty Optional instance or `null`.
	 */
	public abstract function orNull();

	/**
	 * Checks if current instance and given Optional instance contains same object.
	 *
	 * @param mixed $object Other object
	 *
	 * @return boolean `TRUE` if current instance and given Optional instance contains same object, `FALSE` otherwise.
	 *        
	 * @ignore
	 */
	public abstract function equals($object);

	/**
	 * Make sure the passed reference is not null.
	 *
	 * @param mixed $reference Reference to test.
	 * @param string $message Error message if reference is null
	 *        	
	 * @return mixed
	 *
	 * @throws InvalidArgumentException If reference is null.
	 */
	protected static function checkNotNull($reference, $message = null) {
		if ($message === null) {
			$message = "Disallowed null in reference found.";
		}

		if ($reference === null) {
			throw new InvalidArgumentException($message);
		}
		return $reference;
	}
}

/**
 * Optional implementation for empty Optional
 *
 * @ignore
 */
class Absent extends Optional {

	/** @var Absent Singleton instance. */
	private static $instance;

	/**
	 * Private constructor (Singleton pattern)
	 */
	protected function __construct() {
		parent::__construct();
	}

	/**
	 * Always returns true.
	 *
	 * @return boolean `TRUE`.
	 */
	public function isEmpty() {
		return true;
	}

	/**
	 * Always raise an exception.
	 *
	 * @throws BadMethodCallException Always.
	 */
	public function get() {
		throw new BadMethodCallException("Optional->get() cannot be called on an absent value");
	}

	/**
	 * Always returns current instance
	 *
	 * @param callable|UnaryFunction|MapOperator $mapper Mapping function. If function is a string, use object property or method as mapper.
	 *
	 * @return Optional Optional instance containing mapped value or Absent.
	 */
	public function map($mapper) {
		return $this;
	}

	/**
	 * Always returns the non-null default value.
	 *
	 * @param mixed $defaultValue The default value.
	 *        	
	 * @return mixed The non-null default value
	 *        
	 * @throws InvalidArgumentException if default value is null.
	 */
	public function orElse($defaultValue) {
		$message = "use Optional->orNull() instead of Optional->or(null)";
		return static::checkNotNull($defaultValue, $message);
	}

	/**
	 * Always throws given exception.
	 *
	 * @param Exception $ex Exception to throw
	 *
	 * @return mixed Unused
	 *
	 * @throws Exception Given exception
	 */
	public function orElseThrow(Exception $ex) {
		throw $ex;
	}

	/**
	 * Always returns NULL.
	 *
	 * @return mixed `NULL`.
	 */
	public function orNull() {
		return null;
	}

	/**
	 * Checks the given object is the Singleton instance.
	 *
	 * @param Optional $object Any other optional instance.
	 *        	
	 * @return boolean `TRUE` if the given object is the Singleton instance, `FALSE` otherwise.
	 *        
	 * @ignore
	 */
	public function equals($object) {
		return $object === $this;
	}

	/**
	 * Returns the singleton instance.
	 *
	 * @return Absent The Singleton instance.
	 */
	public static function instance() {
		if (static::$instance == null) {
			return static::$instance = new Absent();
		}
		return static::$instance;
	}
}

/**
 * Optional implementation for storing any reference.
 *
 * @ignore
 */
class Present extends Optional {

	/**
	 * Stored reference.
	 *
	 * @var mixed $_reference
	 */
	private $_reference;

	/**
	 * Construct a new Optional with the given reference.
	 *
	 * @param mixed $reference Reference to store
	 */
	protected function __construct($reference) {
		parent::__construct();
		$this->_reference = $reference;
	}

	/**
	 * Always returns false.
	 *
	 * @return boolean `FALSE`.
	 */
	public function isEmpty() {
		return false;
	}

	/**
	 * Returns the stored reference.
	 *
	 * @return mixed The stored reference.
	 */
	public function get() {
		return $this->_reference;
	}

	/**
	 * Applying mapper on contained reference and return a new Optional element.
	 *
	 * @param callable|UnaryFunction|MapOperator|string $mapper Mapping function. If function is a string, use object property or method as mapper.
	 *
	 * @return Optional Optional instance containing mapped value or Absent.
	 */
	public function map($mapper): Optional {
		/* @var UnaryFunction $function */
		/* @var callable $callable */
		[$function, $callable] = MapOperator::getFn($mapper);

		return Optional::ofNullable($function
				? $function->apply($this->_reference)
				: $callable($this->_reference));
	}

	/**
	 * Always returns the stored reference.
	 *
	 * @param mixed $defaultValue The default value.
	 *        	
	 * @return mixed The stored reference.
	 *        
	 * @throws InvalidArgumentException if default value is null.
	 */
	public function orElse($defaultValue) {
		$message = "use Optional->orNull() instead of Optional->or(null)";
		static::checkNotNull($defaultValue, $message);
		return $this->_reference;
	}

	/**
	 * Always returns the stored reference.
	 *
	 * @param Exception $ex Unused
	 *        	
	 * @return mixed The stored reference.
	 *        
	 * @throws InvalidArgumentException if default value is null.
	 */
	public function orElseThrow(Exception $ex) {
		return $this->_reference;
	}

	/**
	 * Always returns the stored reference.
	 *
	 * @return mixed The stored reference.
	 */
	public function orNull() {
		return $this->_reference;
	}

	/**
	 * Checks if current instance and given Optional instance contains same object.
	 *
	 * @param mixed $object Other object to compare
	 *
	 * @return bool `TRUE` if current instance and given Optional instance contains same object, `FALSE` otherwise.
	 *        
	 * @ignore
	 */
	public function equals($object) {
		if ($object instanceof Present) {
			return $this->_reference === $object->get();
		}
		return false;
	}
}


