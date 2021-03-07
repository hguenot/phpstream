<?php
/**
 * @copyright Copyright (c) 2015 Hervé Guenot
 * @license https://github.com/hguenot/phpstream/blob/master/LICENSE The MIT License (MIT)
 * @readme https://github.com/hguenot/phpstream#php-stream
 */
namespace phpstream\impl;

use InvalidArgumentException;
use phpstream\collectors\ArrayCollector;
use phpstream\collectors\CountCollector;
use phpstream\collectors\FirstCollector;
use phpstream\collectors\LastCollector;
use phpstream\collectors\MaxCollector;
use phpstream\collectors\MinCollector;
use phpstream\collectors\StreamCollector;
use phpstream\functions\UnaryFunction;
use phpstream\functions\BinaryFunction;
use phpstream\operators\DistinctOperator;
use phpstream\operators\FilterOperator;
use phpstream\operators\LimitOperator;
use phpstream\operators\MapOperator;
use phpstream\operators\PeekOperator;
use phpstream\operators\SkipOperator;
use phpstream\operators\StreamOperator;
use phpstream\Stream;
use phpstream\util\Comparator;
use phpstream\util\Optional;

/**
 * Stream processor implementation using Generators (`yield`).
 * It offers less performance but requires less memory to perform operations.
 */
class GeneratorStream extends Stream {
	/**
	 * GeneratorStream constructor.
	 *
	 * @param iterable $iterable Iterable value to be processed.
	 */
	public function __construct(private iterable $iterable) {
	}

	public function filter(callable|UnaryFunction|FilterOperator $filter): Stream {
		$operator = new FilterOperator($filter);
		$this->iterable = $operator->execute($this->iterable);
		return $this;
	}

	public function map(callable|UnaryFunction|string|MapOperator $mapper): Stream {
		$operator = new MapOperator($mapper);
		$this->iterable = $operator->execute($this->iterable);
		return $this;
	}

	public function peek(callable|UnaryFunction|PeekOperator $peekingFunction): Stream {
		$operator = new PeekOperator($peekingFunction);
		$this->iterable = $operator->execute($this->iterable);
		return $this;
	}

	public function limit(int $limit): Stream {
		$operator = new LimitOperator($limit);
		$this->iterable = $operator->execute($this->iterable);
		return $this;
	}

	public function skip(int $limit): Stream {
		$operator = new SkipOperator($limit);
		$this->iterable = $operator->execute($this->iterable);
		return $this;
	}

	public function distinct(): Stream {
		$operator = new DistinctOperator();
		$this->iterable = $operator->execute($this->iterable);
		return $this;
	}

	public function index(callable|string $indexer, $allowDuplicate = false): Stream {
		$f = is_string($indexer)
				? function ($e) use ($indexer) {
					return $e->$indexer;
				}
				: $indexer;
		$iterable = $this->iterable;
		if ($allowDuplicate) {
			$mapper = function () use ($iterable, $f): iterable {
				foreach ($iterable as $value) {
					yield $f($value) => $value;
				}
			};
		} else {
			$indices = [];
			$mapper = function () use ($iterable, $f, $indices): iterable {
				foreach ($iterable as $value) {
					$key = $f($value);
					if (array_key_exists($key, $indices)) {
						throw new InvalidArgumentException("Multiple elements got same key.");
					}
					$indices[$key] = true;
					yield $key => $value;
				}
			};
		}

		$this->iterable = $mapper();

		return $this;
	}

	public function sort(callable|Comparator|string $cmp = null): Stream {
		$fn = $this->_getComparator($cmp);
		$array = iterable_to_array($this->iterable, true);
		uasort($array, $fn instanceof Comparator
				? function ($o1, $o2) use ($fn) {
					return $fn->compare($o1, $o2);
				}
				: $fn);
		$this->iterable = $array;
		return $this;
	}

	public function execute(StreamOperator $operator): Stream {
		$this->iterable = $operator->execute($this->iterable);
		return $this;
	}

	public function findAny(): Optional {
		return $this->findFirst();
	}

	public function findFirst(): Optional {
		$collector = new FirstCollector();
		$this->limit(1);
		return $collector->collect($this->iterable);
	}

	public function findLast(): Optional {
		$collector = new LastCollector();
		return $collector->collect($this->iterable);
	}

	public function count(): int {
		$collector = new CountCollector();
		return $collector->collect($this->iterable);
	}

	public function toArray(): array {
		$collector = new ArrayCollector(false);
		return $collector->collect($this->iterable);
	}

	public function toMap(): array {
		$collector = new ArrayCollector(true);
		return $collector->collect($this->iterable);
	}

	public function toIterable(): iterable {
		return $this->iterable;
	}

	public function min(callable|Comparator $cmp = null): Optional {
		$collector = new MinCollector($cmp);
		return $collector->collect($this->iterable);
	}

	public function max(callable|Comparator $cmp = null): Optional {
		$collector = new MaxCollector($cmp);
		return $collector->collect($this->iterable);
	}

	public function reduce(callable|BinaryFunction $reducer, mixed $initialValue = null): mixed {
		$iterable = $this->iterable;
		$fn = $reducer instanceof BinaryFunction
				? function ($carry, $item) use ($reducer) {
					return $reducer->apply($carry, $item);
				}
				: $reducer;
		$result = $initialValue;
		foreach ($iterable as $value) {
			$result = $fn($result, $value);
		}

		return $result;
	}

	public function collect(StreamCollector $collector): mixed {
		return $collector->collect($this->iterable);
	}
}
