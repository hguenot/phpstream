<?php
namespace phpstream\util\_optional;

use Exception;
use InvalidArgumentException;
use phpstream\functions\UnaryFunction;
use phpstream\operators\MapOperator;
use phpstream\util\Optional;

/**
 * Optional implementation for storing any reference.
 *
 * @ignore
 * @internal
 */
class Present extends Optional {
	/**
	 * Construct a new Optional with the given reference.
	 *
	 * @param mixed $reference Reference to store
	 */
	protected function __construct(private mixed $reference) {
		parent::__construct();
	}

	/**
	 * Always returns false.
	 *
	 * @return boolean `FALSE`.
	 */
	public function isEmpty(): bool {
		return false;
	}

	/**
	 * Returns the stored reference.
	 *
	 * @return mixed The stored reference.
	 */
	public function get(): mixed {
		return $this->reference;
	}

	/**
	 * Applying mapper on contained reference and return a new Optional element.
	 *
	 * @param callable|UnaryFunction|MapOperator|string $mapper Mapping function. If function is a string, use object
	 *         property or method as mapper.
	 *
	 * @return Optional Optional instance containing mapped value or Absent.
	 */
	public function map(callable|UnaryFunction|MapOperator|string $mapper): Optional {
		$callable = new MapOperator($mapper);
		return Optional::ofNullable($callable($this->reference));
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
	public function orElse(mixed $defaultValue): mixed {
		$message = "use Optional->orNull() instead of Optional->or(null)";
		static::checkNotNull($defaultValue, $message);
		return $this->reference;
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
	public function orElseThrow(Exception $ex): mixed {
		return $this->reference;
	}

	/**
	 * Always returns the stored reference.
	 *
	 * @return mixed The stored reference.
	 */
	public function orNull(): mixed {
		return $this->reference;
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
	public function equals(
			mixed $object): bool {
		if ($object instanceof Present) {
			return $this->reference === $object->get();
		}
		return false;
	}
}