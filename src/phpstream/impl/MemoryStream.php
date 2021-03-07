<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\impl;

use InvalidArgumentException;
use phpstream\collectors\StreamCollector;
use phpstream\functions\BinaryFunction;
use phpstream\functions\UnaryFunction;
use phpstream\operators\FilterOperator;
use phpstream\operators\MapOperator;
use phpstream\operators\PeekOperator;
use phpstream\operators\StreamOperator;
use phpstream\Stream;
use phpstream\util\Comparator;
use phpstream\util\Optional;

/**
 * Stream processor implementation using internal php array function.
 * It offers better performance but requires more memory to perform operations.
 */
class MemoryStream extends Stream {
	/**
	 * MemoryStream constructor.
	 *
	 * @param array $array Iterable value to be processed.
	 */
	public function __construct(private array $array) {
	}

	public function filter(callable|UnaryFunction|FilterOperator $filter): Stream {
		$this->array = array_filter($this->array, new FilterOperator($filter));
		return $this;
	}

	public function map(callable|UnaryFunction|string|MapOperator $mapper): Stream {
		$this->array = array_map(new MapOperator($mapper), $this->array);
		return $this;
	}

	public function peek(callable|UnaryFunction|PeekOperator $peekingFunction): Stream {
		array_walk($this->array, new PeekOperator($peekingFunction));
		return $this;
	}

	public function limit(int $limit): Stream {
		if ($limit < 0) {
			throw new InvalidArgumentException('Limit must be a positive integer.');
		}
		$this->array = array_slice($this->array, 0, $limit);
		return $this;
	}

	public function skip(int $limit): Stream {
		if ($limit < 0) {
			throw new InvalidArgumentException('Limit must be a positive integer.');
		}
		$this->array = array_slice($this->array, $limit);
		return $this;
	}

	public function distinct(): Stream {
		$this->array = array_unique($this->array);
		return $this;
	}

	public function index(callable|string $indexer, $allowDuplicate = false): Stream {
		$f = is_string($indexer)
				? fn($e) => $e->$indexer
				: $indexer;
		$array = [];
		if ($allowDuplicate) {
			foreach ($this->array as $value) {
				$array[$f($value)] = $value;
			}
		} else {
			foreach ($this->array as $value) {
				$key = $f($value);
				if (array_key_exists($key, $array)) {
					throw new InvalidArgumentException("Multiple elements got same key.");
				}
				$array[$key] = $value;
			}
		}
		$this->array = $array;

		return $this;
	}

	public function sort(callable|Comparator|string $cmp = null): Stream {
		$fn = $this->_getComparator($cmp);
		uasort($this->array, $fn instanceof Comparator
				? fn ($o1, $o2) => $fn->compare($o1, $o2)
				: $fn);
		return $this;
	}

	public function execute(StreamOperator $operator): Stream {
		$this->array = iterable_to_array($operator->execute($this->array), true);
		return $this;
	}

	public function findAny(): Optional {
		reset($this->array);
		return count($this->array) > 0 ? Optional::ofNullable(next($this->array)) : Optional::absent();
	}

	public function findFirst(): Optional {
		reset($this->array);
		return count($this->array) > 0 ? Optional::ofNullable(current($this->array)) : Optional::absent();
	}

	public function findLast(): Optional {
		reset($this->array);
		$count = count($this->array);
		return $count > 0 ? Optional::ofNullable(array_values($this->array)[$count-1]) : Optional::absent();
	}

	public function count(): int {
		return count($this->array);
	}

	public function toArray(): array {
		return array_values($this->array);
	}

	public function toMap(): array {
		return $this->array;
	}

	public function toIterable(): iterable {
		foreach ($this->array as $key => $value) {
			yield $key => $value;
		}
	}

	public function min(callable|Comparator $cmp = null): Optional {
		if ($cmp === null) {
			$cmp = function($o1, $o2) {
				return $o1 <=> $o2;
			};
		}
		if (!($cmp instanceof Comparator) && !is_callable($cmp)) {
			throw new InvalidArgumentException("Comparator callback must be instance of Comparator or a callable function.");
		}
		uasort($this->array, $cmp instanceof Comparator
				? function ($o1, $o2) use ($cmp) {
					return $cmp->compare($o1, $o2);
				}
				: $cmp);
		return $this->findFirst();
	}

	public function max(callable|Comparator $cmp = null): Optional {
		if ($cmp === null) {
			$cmp = function($o1, $o2) {
				return $o1 <=> $o2;
			};
		}
		if (!($cmp instanceof Comparator) && !is_callable($cmp)) {
			throw new InvalidArgumentException("Comparator callback must be instance of Comparator or a callable function.");
		}
		uasort($this->array, $cmp instanceof Comparator
				? function ($o1, $o2) use ($cmp) {
					return $cmp->compare($o2, $o1);
				}
				: function ($o1, $o2) use ($cmp) {
					return $cmp($o2, $o1);
				});
		return $this->findFirst();
	}

	public function reduce(callable|BinaryFunction $reducer, mixed $initialValue = null): mixed {
		return array_reduce($this->array, $reducer instanceof BinaryFunction
				? function ($carry, $item) use ($reducer) {
					return $reducer->apply($carry, $item);
				}
				: $reducer,
				$initialValue);
	}

	public function collect(StreamCollector $collector): mixed {
		return $collector->collect($this->array);
	}

}
