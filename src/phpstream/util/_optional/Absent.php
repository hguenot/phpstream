<?php
namespace phpstream\util\_optional;

use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use phpstream\functions\UnaryFunction;
use phpstream\operators\MapOperator;
use phpstream\util\Optional;

/**
 * Optional implementation for empty Optional
 *
 * @ignore
 * @internal
 */
class Absent extends Optional {
	/** @var null|Absent Singleton instance. */
	private static ?Absent $instance = null;

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
	public function isEmpty(): bool {
		return true;
	}

	/**
	 * Always raise an exception.
	 *
	 * @throws BadMethodCallException Always.
	 */
	public function get(): mixed {
		throw new BadMethodCallException("Optional->get() cannot be called on an absent value");
	}

	/**
	 * Always returns current instance
	 *
	 * @param callable|UnaryFunction|MapOperator|string $mapper Mapping function. If function is a string, use object
	 *         property or method as mapper.
	 *
	 * @return Optional Optional instance containing mapped value or Absent.
	 */
	public function map(callable|UnaryFunction|MapOperator|string $mapper): Optional {
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
	public function orElse(mixed $defaultValue): mixed {
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
	public function orElseThrow(Exception $ex): mixed {
		throw $ex;
	}

	/**
	 * Always returns NULL.
	 *
	 * @return mixed `NULL`.
	 */
	public function orNull(): mixed {
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
	public function equals(mixed $object): bool {
		return $object === $this;
	}

	/**
	 * Returns the singleton instance.
	 *
	 * @return Absent The Singleton instance.
	 */
	public static function instance(): Optional {
		if (!static::$instance) {
			return static::$instance = new Absent();
		}
		return static::$instance;
	}
}